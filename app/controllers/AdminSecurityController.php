<?php

namespace App\Controllers;

use Core\Controller;
use Core\View;
use Core\Database;
use Core\Session;
use Core\Security\CSRF;
use PDO;

class AdminSecurityController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function index()
    {
        $user = Session::get('user');
        if (!$user || $user['role'] !== 'admin') {
            http_response_code(403);
            return View::render('errors/404', ['message' => 'Unauthorized']);
        }

        // Filters
        $action = $_GET['action'] ?? 'all';
        $userId = $_GET['user_id'] ?? null;
        $limit = (int)($_GET['limit'] ?? 100);
        $page = (int)($_GET['page'] ?? 1);
        $offset = ($page - 1) * $limit;

        // Build query
        $query = "
            SELECT 
                al.*,
                u.name as user_name,
                u.email as user_email
            FROM audit_logs al
            LEFT JOIN users u ON al.user_id = u.id
            WHERE 1=1
        ";
        
        $params = [];
        
        if ($action !== 'all') {
            $query .= " AND al.action = ?";
            $params[] = $action;
        }
        
        if ($userId) {
            $query .= " AND al.user_id = ?";
            $params[] = $userId;
        }
        
        $query .= " ORDER BY al.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Count total logs for pagination
        $countQuery = "SELECT COUNT(*) as total FROM audit_logs WHERE 1=1";
        $countParams = [];
        
        if ($action !== 'all') {
            $countQuery .= " AND action = ?";
            $countParams[] = $action;
        }
        
        if ($userId) {
            $countQuery .= " AND user_id = ?";
            $countParams[] = $userId;
        }

        $stmt = $this->db->prepare($countQuery);
        $stmt->execute($countParams);
        $totalLogs = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Get action types for filter
        $stmt = $this->db->query("SELECT DISTINCT action FROM audit_logs ORDER BY action");
        $actionTypes = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Get recent security events
        $securityEvents = $this->getSecurityEvents();

        // Get blocked users count
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM users WHERE blocked = 1 OR status = 'blocked'");
        $blockedUsersCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        // Get failed login attempts (if tracked)
        $failedLoginCount = 0; // Placeholder - would need separate tracking

        $data = [
            'logs' => $logs,
            'actionTypes' => $actionTypes,
            'totalLogs' => $totalLogs,
            'currentPage' => $page,
            'totalPages' => ceil($totalLogs / $limit),
            'limit' => $limit,
            'selectedAction' => $action,
            'selectedUserId' => $userId,
            'securityEvents' => $securityEvents,
            'blockedUsersCount' => $blockedUsersCount,
            'failedLoginCount' => $failedLoginCount,
            'csrf_token' => CSRF::getToken()
        ];

        return View::render('admin/security', $data);
    }

    private function getSecurityEvents()
    {
        // Get critical security-related actions
        $stmt = $this->db->prepare("
            SELECT 
                al.*,
                u.name as user_name
            FROM audit_logs al
            LEFT JOIN users u ON al.user_id = u.id
            WHERE al.action IN (
                'login', 'logout', 'failed_login', 
                'block_user', 'unblock_user', 'delete_user',
                'approve_payment', 'reject_payment',
                'create_admin', 'delete_product'
            )
            ORDER BY al.created_at DESC
            LIMIT 50
        ");
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function clearLogs()
    {
        $user = Session::get('user');
        if (!$user || $user['role'] !== 'admin') {
            http_response_code(403);
            die('Unauthorized');
        }

        if (!CSRF::validate($_POST['_token'] ?? '')) {
            die('CSRF token validation failed');
        }

        $cutoffDays = (int)($_POST['days'] ?? 90);
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$cutoffDays} days"));

        $stmt = $this->db->prepare("DELETE FROM audit_logs WHERE created_at < ?");
        $stmt->execute([$cutoffDate]);
        $deletedCount = $stmt->rowCount();

        // Log this action
        $this->logAudit($user['id'], 'clear_old_logs', 'audit_logs', null, "Cleared {$deletedCount} logs older than {$cutoffDays} days");

        $_SESSION['success'] = "Cleared {$deletedCount} old audit logs";
        
        $config = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');
        header('Location: ' . $basePath . '/admin/security');
        exit;
    }

    public function exportLogs()
    {
        $user = Session::get('user');
        if (!$user || $user['role'] !== 'admin') {
            http_response_code(403);
            die('Unauthorized');
        }

        $action = $_GET['action'] ?? 'all';
        $userId = $_GET['user_id'] ?? null;

        $query = "
            SELECT 
                al.*,
                u.name as user_name,
                u.email as user_email
            FROM audit_logs al
            LEFT JOIN users u ON al.user_id = u.id
            WHERE 1=1
        ";
        
        $params = [];
        
        if ($action !== 'all') {
            $query .= " AND al.action = ?";
            $params[] = $action;
        }
        
        if ($userId) {
            $query .= " AND al.user_id = ?";
            $params[] = $userId;
        }
        
        $query .= " ORDER BY al.created_at DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Generate CSV
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="audit_logs_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // CSV Headers
        fputcsv($output, [
            'ID',
            'User',
            'Email',
            'Action',
            'Target Type',
            'Target ID',
            'IP Address',
            'User Agent',
            'Details',
            'Timestamp'
        ]);
        
        // CSV Data
        foreach ($logs as $log) {
            fputcsv($output, [
                $log['id'],
                $log['user_name'] ?? 'System',
                $log['user_email'] ?? 'N/A',
                $log['action'],
                $log['target_type'] ?? '',
                $log['target_id'] ?? '',
                $log['ip_address'] ?? '',
                $log['user_agent'] ?? '',
                $log['details'] ?? '',
                $log['created_at']
            ]);
        }
        
        fclose($output);
        exit;
    }

    private function logAudit($userId, $action, $targetType, $targetId, $details)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO audit_logs (user_id, action, target_type, target_id, ip_address, user_agent, details)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $userId,
                $action,
                $targetType,
                $targetId,
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null,
                $details
            ]);
        } catch (\Exception $e) {
            error_log('Audit log failed: ' . $e->getMessage());
        }
    }
}
