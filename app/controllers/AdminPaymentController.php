<?php

namespace App\Controllers;

use Core\Controller;
use Core\View;
use Core\Database;
use Core\Session;
use PDO;

class AdminPaymentController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function verifications()
    {
        $user = Session::get('user');
        if (!$user || $user['role'] !== 'admin') {
            http_response_code(403);
            return View::render('errors/404', ['message' => 'Unauthorized']);
        }

        $db = $this->db;

        // Get all pending payment verifications
        $stmt = $db->prepare("
            SELECT 
                pv.id,
                pv.order_id,
                pv.user_id,
                pv.payment_method,
                pv.payment_amount,
                pv.screenshot_path,
                pv.status,
                pv.created_at,
                u.name,
                u.email,
                o.total_amount,
                o.first_name,
                o.last_name,
                o.email as order_email,
                o.phone as order_phone,
                GROUP_CONCAT(DISTINCT p.name SEPARATOR ', ') AS product_names,
                MAX(CASE WHEN p.product_type = 'game' THEN 1 ELSE 0 END) AS has_game,
                MAX(CASE WHEN p.product_type = 'accessory' THEN 1 ELSE 0 END) AS has_accessory
            FROM payment_verifications pv
            LEFT JOIN users u ON pv.user_id = u.id
            LEFT JOIN orders o ON pv.order_id = o.id
            LEFT JOIN order_items oi ON oi.order_id = o.id
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE pv.status IN ('pending', 'pending_admin_review')
            GROUP BY pv.id
            ORDER BY pv.created_at DESC
        ");
        $stmt->execute();
        $pendingVerifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $pendingGameVerifications = [];
        $pendingProductVerifications = [];
        foreach ($pendingVerifications as $verification) {
            if (!empty($verification['has_game'])) {
                $pendingGameVerifications[] = $verification;
            } else {
                $pendingProductVerifications[] = $verification;
            }
        }

        // Get approved verifications
        $stmt = $db->prepare("
            SELECT 
                pv.id,
                pv.order_id,
                pv.user_id,
                pv.payment_method,
                pv.payment_amount,
                pv.status,
                pv.approved_at,
                u.name,
                u.email
            FROM payment_verifications pv
            LEFT JOIN users u ON pv.user_id = u.id
            WHERE pv.status = 'approved'
            ORDER BY pv.approved_at DESC
            LIMIT 10
        ");
        $stmt->execute();
        $approvedVerifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return View::render('admin/payment-verification', [
            'pendingVerifications' => $pendingVerifications,
            'pendingGameVerifications' => $pendingGameVerifications,
            'pendingProductVerifications' => $pendingProductVerifications,
            'approvedVerifications' => $approvedVerifications
        ]);
    }

    public function reviewPayment($id)
    {
        $user = Session::get('user');
        if (!$user || $user['role'] !== 'admin') {
            http_response_code(403);
            return View::render('errors/404', ['message' => 'Unauthorized']);
        }

        $db = $this->db;

        // Get payment verification details
        $stmt = $db->prepare("
            SELECT 
                pv.id,
                pv.order_id,
                pv.user_id,
                pv.payment_method,
                pv.payment_amount,
                pv.screenshot_path,
                pv.status,
                pv.created_at,
                u.id as uid,
                u.name,
                u.email,
                o.total_amount,
                o.first_name,
                o.last_name,
                o.email as order_email,
                o.street,
                o.city,
                o.state,
                o.zip,
                o.country,
                o.phone
            FROM payment_verifications pv
            LEFT JOIN users u ON pv.user_id = u.id
            LEFT JOIN orders o ON pv.order_id = o.id
            WHERE pv.id = ?
        ");
        $stmt->execute([(int)$id]);
        $verification = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$verification) {
            http_response_code(404);
            return View::render('errors/404', ['message' => 'Payment verification not found']);
        }

        // Get order items
        $stmt = $db->prepare("
            SELECT oi.*, p.name, p.price
            FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$verification['order_id']]);
        $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return View::render('admin/payment-review', [
            'verification' => $verification,
            'orderItems' => $orderItems
        ]);
    }

    public function getVerificationDetails($id)
    {
        // For AJAX requests
        header('Content-Type: application/json');
        
        $user = Session::get('user');
        if (!$user || $user['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $db = $this->db;

        // Get payment verification details
        $stmt = $db->prepare("
            SELECT 
                pv.id,
                pv.order_id,
                pv.user_id,
                pv.payment_method,
                pv.payment_amount,
                pv.screenshot_path,
                pv.status,
                pv.created_at,
                u.id as uid,
                u.name,
                u.email,
                o.total_amount,
                o.first_name,
                o.last_name,
                o.email as order_email,
                o.street,
                o.city,
                o.state,
                o.zip,
                o.country,
                o.phone
            FROM payment_verifications pv
            LEFT JOIN users u ON pv.user_id = u.id
            LEFT JOIN orders o ON pv.order_id = o.id
            WHERE pv.id = ?
        ");
        $stmt->execute([(int)$id]);
        $verification = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$verification) {
            http_response_code(404);
            echo json_encode(['error' => 'Not found']);
            exit;
        }

        // Get order items
        $stmt = $db->prepare("
            SELECT oi.*, p.name, p.price, p.product_type AS type
            FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$verification['order_id']]);
        $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'verification' => $verification,
            'orderItems' => $orderItems
        ]);
        exit;
    }

    public function approvePayment($id)
    {
        // Clear any output and set JSON header
        while (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Type: application/json');

        $user = Session::get('user');
        if (!$user || $user['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $db = $this->db;

        try {
            // Get verification details
            $stmt = $db->prepare("SELECT order_id, user_id FROM payment_verifications WHERE id = ?");
            $stmt->execute([(int)$id]);
            $verification = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$verification) {
                throw new \Exception('Payment verification not found');
            }

            $db->beginTransaction();

            // Update payment verification status
            $updateStmt = $db->prepare("
                UPDATE payment_verifications 
                SET status = 'approved', approved_at = NOW()
                WHERE id = ?
            ");
            $updateStmt->execute([(int)$id]);

            // Update order status to PAID (for revenue counting)
            $orderStmt = $db->prepare("UPDATE orders SET status = 'paid' WHERE id = ?");
            $orderStmt->execute([$verification['order_id']]);

            // Unlock game access
            $accessStmt = $db->prepare("
                UPDATE game_access 
                SET is_unlocked = 1, unlocked_at = NOW()
                WHERE user_id = ? AND order_item_id IN (
                    SELECT id FROM order_items WHERE order_id = ?
                )
            ");
            $accessStmt->execute([$verification['user_id'], $verification['order_id']]);

            $db->commit();

            echo json_encode([
                'success' => true,
                'message' => 'Payment approved successfully',
                'redirect' => '/hasheem/admin/payments'
            ]);

        } catch (\Exception $e) {
            if (isset($db)) {
                $db->rollBack();
            }
            error_log("Payment approval error: " . $e->getMessage() . " (Line: " . $e->getLine() . ")");
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'debug' => true
            ]);
        }
        exit;
    }

    public function rejectPayment($id)
    {
        // Clear any output and set JSON header
        while (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Type: application/json');

        $user = Session::get('user');
        if (!$user || $user['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $reason = $_POST['reason'] ?? 'No reason provided';
        $db = $this->db;

        try {
            $db->beginTransaction();

            // Update payment verification status
            $updateStmt = $db->prepare("
                UPDATE payment_verifications 
                SET status = 'rejected', rejected_at = NOW(), rejected_by = ?, rejection_reason = ?
                WHERE id = ?
            ");
            $updateStmt->execute([$user['id'], $reason, (int)$id]);

            // Get order details
            $stmt = $db->prepare("SELECT order_id FROM payment_verifications WHERE id = ?");
            $stmt->execute([(int)$id]);
            $verification = $stmt->fetch(PDO::FETCH_ASSOC);

            // Update order status to rejected
            $orderStmt = $db->prepare("UPDATE orders SET status = 'payment_rejected' WHERE id = ?");
            $orderStmt->execute([$verification['order_id']]);

            $db->commit();

            echo json_encode([
                'success' => true,
                'message' => 'Payment rejected successfully',
                'redirect' => '/hasheem/admin/payments'
            ]);

        } catch (\Exception $e) {
            if (isset($db)) {
                $db->rollBack();
            }
            error_log("Payment rejection error: " . $e->getMessage() . " (Line: " . $e->getLine() . ")");
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'debug' => true
            ]);
        }
        exit;
    }

    public function deleteVerification($id)
    {
        // Clear any output and set JSON header
        while (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Type: application/json');

        $user = Session::get('user');
        if (!$user || $user['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $db = $this->db;

        try {
            // Get verification details before deleting
            $stmt = $db->prepare("SELECT order_id, screenshot_path FROM payment_verifications WHERE id = ?");
            $stmt->execute([(int)$id]);
            $verification = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$verification) {
                throw new \Exception('Payment verification not found');
            }

            $db->beginTransaction();

            // Delete the verification
            $deleteStmt = $db->prepare("DELETE FROM payment_verifications WHERE id = ?");
            $deleteStmt->execute([(int)$id]);

            // Delete screenshot file if exists
            if (!empty($verification['screenshot_path'])) {
                $filePath = $_SERVER['DOCUMENT_ROOT'] . $verification['screenshot_path'];
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

            $db->commit();

            echo json_encode([
                'success' => true,
                'message' => 'Payment verification deleted successfully'
            ]);

        } catch (\Exception $e) {
            if (isset($db)) {
                $db->rollBack();
            }
            error_log("Payment deletion error: " . $e->getMessage());
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
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
