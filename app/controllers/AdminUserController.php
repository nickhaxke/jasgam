<?php

namespace App\Controllers;

use Core\Controller;
use Core\View;
use Core\DataManager;
use Core\Database;
use Core\Security\CSRF;
use Core\Session;
use PDO;

class AdminUserController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function index()
    {
        $users = DataManager::getData('users');
        $token = CSRF::getToken();
        return View::render('admin/users', ['users' => $users, 'csrf_token' => $token]);
    }

    public function block($id)
    {
        $adminUser = Session::get('user');
        if (!$adminUser || $adminUser['role'] !== 'admin') {
            http_response_code(403);
            die('Unauthorized');
        }

        if (!CSRF::validate($_POST['_token'] ?? '')) {
            die('CSRF token validation failed');
        }

        DataManager::updateUser($id, ['blocked' => true]);
        
        // Log audit trail
        $this->logAudit($adminUser['id'], 'block_user', 'user', $id, 'Blocked user ID: ' . $id);
        
        $config = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');
        header('Location: ' . $basePath . '/admin/users');
    }

    public function unblock($id)
    {
        $adminUser = Session::get('user');
        if (!$adminUser || $adminUser['role'] !== 'admin') {
            http_response_code(403);
            die('Unauthorized');
        }

        if (!CSRF::validate($_POST['_token'] ?? '')) {
            die('CSRF token validation failed');
        }

        DataManager::updateUser($id, ['blocked' => false]);
        
        // Log audit trail
        $this->logAudit($adminUser['id'], 'unblock_user', 'user', $id, 'Unblocked user ID: ' . $id);
        
        $config = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');
        header('Location: ' . $basePath . '/admin/users');
    }

    public function delete($id)
    {
        $adminUser = Session::get('user');
        if (!$adminUser || $adminUser['role'] !== 'admin') {
            http_response_code(403);
            die('Unauthorized');
        }

        if (!CSRF::validate($_POST['_token'] ?? '')) {
            die('CSRF token validation failed');
        }

        // Get user info before deletion for audit log
        $stmt = $this->db->prepare("SELECT name, email FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        DataManager::deleteUser($id);
        
        // Log audit trail
        $this->logAudit(
            $adminUser['id'], 
            'delete_user', 
            'user', 
            $id, 
            'Deleted user: ' . ($user['name'] ?? 'Unknown') . ' (' . ($user['email'] ?? 'N/A') . ')'
        );
        
        $config = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');
        header('Location: ' . $basePath . '/admin/users');
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
