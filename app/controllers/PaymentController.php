<?php

namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Database;

class PaymentController extends Controller
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function verifyPayment()
    {
        // Only logged-in users
        $user = Auth::check() ? Auth::user() : null;
        if (!$user) {
            return $this->json(['success' => false, 'error' => 'Please login first'], 401);
        }

        // Validate method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['success' => false, 'error' => 'Invalid request method'], 400);
        }

        $errors = [];

        // Validate inputs
        if (empty($_POST['product_id'])) {
            $errors[] = 'Product ID missing';
        }
        if (empty($_POST['payment_method'])) {
            $errors[] = 'Payment method required';
        }
        if (!isset($_FILES['payment_screenshot']) || $_FILES['payment_screenshot']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Payment screenshot required';
        }

        if (!empty($errors)) {
            return $this->json(['success' => false, 'errors' => $errors], 400);
        }

        $product_id = (int)$_POST['product_id'];
        $payment_method = trim($_POST['payment_method']);

        // Verify product exists
        try {
            $stmt = $this->db->prepare("SELECT id, name, price, product_type FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$product) {
                return $this->json(['success' => false, 'error' => 'Product not found'], 404);
            }
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'error' => 'Database error: ' . $e->getMessage()], 500);
        }

        // Create uploads directory
        $upload_dir = __DIR__ . '/../../uploads/payments/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Save screenshot
        $screenshot = $_FILES['payment_screenshot'];
        $file_extension = strtolower(pathinfo($screenshot['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (!in_array($file_extension, $allowed_extensions)) {
            return $this->json(['success' => false, 'error' => 'Invalid image format. Allowed: JPG, PNG, GIF, WEBP'], 400);
        }

        // Generate unique filename
        $screenshot_filename = 'payment_' . $user['id'] . '_' . $product_id . '_' . time() . '.' . $file_extension;
        $screenshot_path = $upload_dir . $screenshot_filename;

        if (!move_uploaded_file($screenshot['tmp_name'], $screenshot_path)) {
            return $this->json(['success' => false, 'error' => 'Failed to save screenshot'], 500);
        }

        try {
            $this->db->beginTransaction();

            $fullName = $user['name'] ?? '';
            $nameParts = preg_split('/\s+/', trim($fullName));
            $firstName = $nameParts[0] ?? 'User';
            $lastName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : $firstName;

            $orderStmt = $this->db->prepare("
                INSERT INTO orders (user_id, total_amount, status, payment_method, first_name, last_name, email, category, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $orderStmt->execute([
                $user['id'],
                $product['price'],
                'pending',
                $payment_method,
                $firstName,
                $lastName,
                $user['email'] ?? null,
                $product['product_type'] // Set category based on product type
            ]);

            $orderId = $this->db->lastInsertId();

            $itemStmt = $this->db->prepare("
                INSERT INTO order_items (order_id, product_id, quantity, unit_price, product_type, needs_verification)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $needsVerification = ($product['product_type'] === 'game') ? 1 : 0;
            $itemStmt->execute([
                $orderId, 
                $product_id, 
                1, 
                $product['price'], 
                $product['product_type'], // Track product type for revenue analytics
                $needsVerification
            ]);

            $orderItemId = $this->db->lastInsertId();

            if ($product['product_type'] === 'game') {
                $accessStmt = $this->db->prepare("
                    INSERT INTO game_access (user_id, product_id, order_item_id, is_unlocked)
                    VALUES (?, ?, ?, 0)
                ");
                $accessStmt->execute([$user['id'], $product_id, $orderItemId]);
            }

            $verifyStmt = $this->db->prepare("
                INSERT INTO payment_verifications 
                (order_id, user_id, payment_method, payment_amount, screenshot_path, status, created_at)
                VALUES (?, ?, ?, ?, ?, 'pending', NOW())
            ");
            $verifyStmt->execute([
                $orderId,
                $user['id'],
                $payment_method,
                $product['price'],
                '/uploads/payments/' . $screenshot_filename
            ]);

            $this->db->commit();

            return $this->json([
                'success' => true,
                'message' => '✅ Payment screenshot submitted! Admin will verify within minutes.',
                'redirect' => '/hasheem/order/dashboard'
            ]);

        } catch (\Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            // Delete uploaded file if database insert fails
            if (file_exists($screenshot_path)) {
                unlink($screenshot_path);
            }
            
            return $this->json(['success' => false, 'error' => 'Database error: ' . $e->getMessage()], 500);
        }
    }

    public function initiate()
    {
        // Placeholder for other payment methods
        return $this->json(['success' => false, 'error' => 'Not implemented'], 501);
    }

    public function callback()
    {
        // Placeholder for payment callbacks
        return $this->json(['success' => false, 'error' => 'Not implemented'], 501);
    }

    public function success()
    {
        return $this->view('payment/success');
    }

    public function fail()
    {
        return $this->view('payment/fail');
    }
}
