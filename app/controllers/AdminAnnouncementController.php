<?php

namespace App\Controllers;

use Core\Controller;
use Core\View;
use Core\Database;
use Core\Session;
use Core\Security\CSRF;
use PDO;

class AdminAnnouncementController extends Controller
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

        $config = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');

        // Get all announcements
        $announcements = [];
        $stats = [
            'total' => 0,
            'active' => 0,
            'total_views' => 0,
            'total_dismissals' => 0
        ];
        
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    a.*,
                    u.name as creator_name
                FROM announcements a
                LEFT JOIN users u ON a.created_by = u.id
                ORDER BY a.priority DESC, a.created_at DESC
            ");
            $stmt->execute();
            $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Calculate stats
            foreach ($announcements as $announcement) {
                $stats['total']++;
                if ($announcement['active']) {
                    $stats['active']++;
                }
                $stats['total_views'] += $announcement['view_count'] ?? 0;
                $stats['total_dismissals'] += $announcement['dismiss_count'] ?? 0;
            }
        } catch (\PDOException $e) {
            $_SESSION['error'] = 'Announcements table missing. Run system migration to create it.';
        }

        return View::render('admin/announcements', [
            'announcements' => $announcements,
            'stats' => $stats,
            'csrf_token' => CSRF::getToken(),
            'basePath' => $basePath
        ]);
    }

    public function create()
    {
        $user = Session::get('user');
        if (!$user || $user['role'] !== 'admin') {
            http_response_code(403);
            return View::render('errors/404', ['message' => 'Unauthorized']);
        }

        $config = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');

        return View::render('admin/announcement-form', [
            'csrf_token' => CSRF::getToken(),
            'basePath' => $basePath
        ]);
    }

    public function store()
    {
        $user = Session::get('user');
        if (!$user || $user['role'] !== 'admin') {
            http_response_code(403);
            die('Unauthorized');
        }

        if (!CSRF::validateToken($_POST['_token'] ?? '')) {
            die('CSRF token validation failed');
        }

        $title = $_POST['title'] ?? '';
        $message = $_POST['message'] ?? '';
        $active = isset($_POST['active']) ? 1 : 0;
        $autoCloseSeconds = (int)($_POST['auto_close_seconds'] ?? 30);
        $startTime = $_POST['start_time'] ?? null;
        $endTime = $_POST['end_time'] ?? null;
        $priority = (int)($_POST['priority'] ?? 0);
        $targetUsers = $_POST['target_users'] ?? 'all';

        if (empty($title) || empty($message)) {
            $_SESSION['error'] = 'Title and message are required';
            $config = require __DIR__ . '/../../config/app.php';
            $basePath = rtrim($config['base_path'] ?? '', '/');
            header('Location: ' . $basePath . '/admin/announcements/create');
            exit;
        }

        $stmt = $this->db->prepare("
            INSERT INTO announcements 
            (title, message, active, auto_close_seconds, start_time, end_time, priority, target_users, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $title,
            $message,
            $active,
            $autoCloseSeconds,
            $startTime ?: null,
            $endTime ?: null,
            $priority,
            $targetUsers,
            $user['id']
        ]);

        // Log action
        $this->logAudit($user['id'], 'create_announcement', 'announcement', $this->db->lastInsertId(), 'Created announcement: ' . $title);

        $_SESSION['success'] = 'Announcement created successfully';
        $config = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');
        header('Location: ' . $basePath . '/admin/announcements');
        exit;
    }

    public function edit($id)
    {
        $user = Session::get('user');
        if (!$user || $user['role'] !== 'admin') {
            http_response_code(403);
            return View::render('errors/404', ['message' => 'Unauthorized']);
        }

        $config = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');

        $stmt = $this->db->prepare("SELECT * FROM announcements WHERE id = ?");
        $stmt->execute([$id]);
        $announcement = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$announcement) {
            http_response_code(404);
            return View::render('errors/404', ['message' => 'Announcement not found']);
        }

        return View::render('admin/announcement-form', [
            'announcement' => $announcement,
            'csrf_token' => CSRF::getToken(),
            'basePath' => $basePath
        ]);
    }

    public function update($id)
    {
        $user = Session::get('user');
        if (!$user || $user['role'] !== 'admin') {
            http_response_code(403);
            die('Unauthorized');
        }

        if (!CSRF::validateToken($_POST['_token'] ?? '')) {
            die('CSRF token validation failed');
        }

        $title = $_POST['title'] ?? '';
        $message = $_POST['message'] ?? '';
        $active = isset($_POST['active']) ? 1 : 0;
        $autoCloseSeconds = (int)($_POST['auto_close_seconds'] ?? 30);
        $startTime = $_POST['start_time'] ?? null;
        $endTime = $_POST['end_time'] ?? null;
        $priority = (int)($_POST['priority'] ?? 0);
        $targetUsers = $_POST['target_users'] ?? 'all';

        $stmt = $this->db->prepare("
            UPDATE announcements 
            SET title = ?, message = ?, active = ?, auto_close_seconds = ?, 
                start_time = ?, end_time = ?, priority = ?, target_users = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $title,
            $message,
            $active,
            $autoCloseSeconds,
            $startTime ?: null,
            $endTime ?: null,
            $priority,
            $targetUsers,
            $id
        ]);

        // Log action
        $this->logAudit($user['id'], 'update_announcement', 'announcement', $id, 'Updated announcement: ' . $title);

        $_SESSION['success'] = 'Announcement updated successfully';
        $config = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');
        header('Location: ' . $basePath . '/admin/announcements');
        exit;
    }

    public function toggleActive($id)
    {
        $user = Session::get('user');
        if (!$user || $user['role'] !== 'admin') {
            http_response_code(403);
            die('Unauthorized');
        }

        if (!CSRF::validateToken($_POST['_token'] ?? '')) {
            die('CSRF token validation failed');
        }

        $stmt = $this->db->prepare("UPDATE announcements SET active = NOT active WHERE id = ?");
        $stmt->execute([$id]);

        // Log action
        $this->logAudit($user['id'], 'toggle_announcement', 'announcement', $id, 'Toggled announcement active status');

        $config = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');
        header('Location: ' . $basePath . '/admin/announcements');
        exit;
    }

    public function delete($id)
    {
        $user = Session::get('user');
        if (!$user || $user['role'] !== 'admin') {
            http_response_code(403);
            die('Unauthorized');
        }

        if (!CSRF::validateToken($_POST['_token'] ?? '')) {
            die('CSRF token validation failed');
        }

        $stmt = $this->db->prepare("DELETE FROM announcements WHERE id = ?");
        $stmt->execute([$id]);

        // Log action
        $this->logAudit($user['id'], 'delete_announcement', 'announcement', $id, 'Deleted announcement');

        $_SESSION['success'] = 'Announcement deleted successfully';
        $config = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');
        header('Location: ' . $basePath . '/admin/announcements');
        exit;
    }

    // API endpoint for frontend to fetch active announcements
    public function getActive()
    {
        $now = date('Y-m-d H:i:s');
        
        $stmt = $this->db->prepare("
            SELECT id, title, message, auto_close_seconds, priority, target_users
            FROM announcements
            WHERE active = 1
            AND (start_time IS NULL OR start_time <= ?)
            AND (end_time IS NULL OR end_time >= ?)
            ORDER BY priority DESC, created_at DESC
            LIMIT 1
        ");
        
        $stmt->execute([$now, $now]);
        $announcement = $stmt->fetch(PDO::FETCH_ASSOC);

        // Increment view count
        if ($announcement) {
            $updateStmt = $this->db->prepare("UPDATE announcements SET view_count = view_count + 1 WHERE id = ?");
            $updateStmt->execute([$announcement['id']]);
        }

        header('Content-Type: application/json');
        echo json_encode($announcement ?: null);
        exit;
    }

    public function dismiss($id)
    {
        // Track dismissal
        $stmt = $this->db->prepare("UPDATE announcements SET dismiss_count = dismiss_count + 1 WHERE id = ?");
        $stmt->execute([$id]);

        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
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
            // Silently fail audit logging to not disrupt main flow
            error_log('Audit log failed: ' . $e->getMessage());
        }
    }
}
