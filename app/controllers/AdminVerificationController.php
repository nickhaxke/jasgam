<?php

namespace App\Controllers;

use Core\Auth;
use Core\Controller;
use Core\Database;

class AdminVerificationController extends Controller
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        
        // Ensure tables exist
        $this->ensureTablesExist();
        
        // Check if user is authenticated and is admin
        if (!Auth::user() || Auth::user()['role'] !== 'admin') {
            header('Location: /login');
            exit;
        }
    }

    /**
     * Ensure required tables exist
     */
    private function ensureTablesExist()
    {
        try {
            $this->db->query("DESCRIBE payment_verifications LIMIT 1");
        } catch (\Exception $e) {
            $sql = <<<SQL
CREATE TABLE IF NOT EXISTS payment_verifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    amount INT NOT NULL,
    access_level VARCHAR(50) NOT NULL DEFAULT 'full',
    payment_method VARCHAR(100) NOT NULL,
    mpesa_phone VARCHAR(20),
    screenshot_path VARCHAR(500) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    approval_notes TEXT,
    approved_by INT,
    approved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_user_id (user_id),
    KEY idx_product_id (product_id),
    KEY idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
SQL;
            try {
                $this->db->exec($sql);
            } catch (\Exception $ex) {}
        }

        // Ensure access_level column exists on payment_verifications
        $this->ensureColumnExists(
            'payment_verifications',
            'access_level',
            "ALTER TABLE payment_verifications ADD COLUMN access_level VARCHAR(50) NOT NULL DEFAULT 'full'"
        );

        // Create game_access table if it doesn't exist
        try {
            $this->db->query("DESCRIBE game_access LIMIT 1");
        } catch (\Exception $e) {
            $sql = <<<SQL
CREATE TABLE IF NOT EXISTS game_access (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    access_level VARCHAR(50) NOT NULL DEFAULT 'full',
    granted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_product (user_id, product_id),
    KEY idx_user_id (user_id),
    KEY idx_product_id (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
SQL;
            try {
                $this->db->exec($sql);
            } catch (\Exception $ex) {}
        }

        // Ensure access_level column exists on game_access
        $this->ensureColumnExists(
            'game_access',
            'access_level',
            "ALTER TABLE game_access ADD COLUMN access_level VARCHAR(50) NOT NULL DEFAULT 'full'"
        );

        // Ensure granted_at column exists on game_access
        $this->ensureColumnExists(
            'game_access',
            'granted_at',
            "ALTER TABLE game_access ADD COLUMN granted_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP"
        );
    }

    /**
     * Ensure a column exists on a table
     */
    private function ensureColumnExists(string $table, string $column, string $alterSql)
    {
        try {
            $stmt = $this->db->query("DESCRIBE {$table}");
            $columns = $stmt->fetchAll(\PDO::FETCH_COLUMN, 0);

            if (!in_array($column, $columns, true)) {
                $this->db->exec($alterSql);
            }
        } catch (\Exception $e) {
            // Ignore column checks to avoid blocking requests
        }
    }

    /**
     * Display all pending payment verifications
     */
    public function index()
    {
        $status = $_GET['status'] ?? 'pending';

        try {
            // Build query based on status
            $query = "
                SELECT 
                    pv.id,
                    pv.user_id,
                    pv.product_id,
                    pv.amount,
                    pv.access_level,
                    pv.payment_method,
                    pv.mpesa_phone,
                    pv.screenshot_path,
                    pv.status,
                    pv.created_at,
                    p.name as product_name,
                    u.name as user_name,
                    u.email
                FROM payment_verifications pv
                JOIN products p ON pv.product_id = p.id
                JOIN users u ON pv.user_id = u.id
                WHERE pv.status = ?
                ORDER BY pv.created_at DESC
            ";

            $stmt = $this->db->prepare($query);
            $stmt->execute([$status]);
            $verifications = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Get count by status for tabs
            $statusQuery = "SELECT status, COUNT(*) as count FROM payment_verifications GROUP BY status";
            $statusStmt = $this->db->prepare($statusQuery);
            $statusStmt->execute();
            $statusCounts = [];
            while ($row = $statusStmt->fetch(\PDO::FETCH_ASSOC)) {
                $statusCounts[$row['status']] = $row['count'];
            }

            return $this->view('admin/game-payments', [
                'verifications' => $verifications,
                'status' => $status,
                'statusCounts' => $statusCounts
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Failed to load verifications: ' . $e->getMessage()], 500);
        }
    }

    /**
     * View single verification details
     */
    public function show($id)
    {
        try {
            $query = "
                SELECT 
                    pv.id,
                    pv.user_id,
                    pv.product_id,
                    pv.amount,
                    pv.access_level,
                    pv.payment_method,
                    pv.mpesa_phone,
                    pv.screenshot_path,
                    pv.status,
                    pv.created_at,
                    p.name as product_name,
                    p.description as product_description,
                    p.product_type,
                    u.name as user_name,
                    u.email as user_email,
                    u.phone
                FROM payment_verifications pv
                JOIN products p ON pv.product_id = p.id
                JOIN users u ON pv.user_id = u.id
                WHERE pv.id = ?
            ";

            $stmt = $this->db->prepare($query);
            $stmt->execute([$id]);
            $verification = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$verification) {
                header('HTTP/1.0 404 Not Found');
                return $this->view('errors/404');
            }

            return $this->view('admin/verification-detail', [
                'verification' => $verification
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Failed to load verification: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Approve a payment verification and grant access
     */
    public function approve($id)
    {
        try {
            // Validate CSRF token
            $token = $_POST['_token'] ?? $_REQUEST['_token'] ?? '';
            if (!\Core\Security\CSRF::validateToken($token)) {
                return $this->json(['error' => 'Invalid security token. Please try again.'], 419);
            }

            // Start transaction
            $this->db->beginTransaction();

            // Get verification details
            $stmt = $this->db->prepare("SELECT user_id, product_id, access_level FROM payment_verifications WHERE id = ?");
            $stmt->execute([$id]);
            $verification = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$verification) {
                $this->db->rollBack();
                return $this->json(['error' => 'Verification not found'], 404);
            }

            // Check if product exists and is a game
            $stmt = $this->db->prepare("SELECT id, product_type FROM products WHERE id = ? AND product_type = 'game'");
            $stmt->execute([$verification['product_id']]);
            $product = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$product) {
                $this->db->rollBack();
                return $this->json(['error' => 'Product not found or is not a game'], 404);
            }

            // Update verification status
            $stmt = $this->db->prepare("
                UPDATE payment_verifications 
                SET status = 'approved'
                WHERE id = ?
            ");
            $stmt->execute([$id]);

            // Create game_access record with verified access level
            $access_level = $verification['access_level'] ?: 'full';
            $stmt = $this->db->prepare("
                INSERT INTO game_access (user_id, product_id, access_level, granted_at)
                VALUES (?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE 
                    access_level = VALUES(access_level),
                    granted_at = NOW()
            ");
            $stmt->execute([
                $verification['user_id'],
                $verification['product_id'],
                $access_level
            ]);

            // Commit transaction
            $this->db->commit();

            // Log the approval
            error_log("[PAYMENT APPROVAL] User {$verification['user_id']} approved for product {$verification['product_id']} by admin " . Auth::user()['id']);

            return $this->json([
                'success' => true,
                'message' => '✅ Payment approved and access granted!'
            ]);
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("[PAYMENT APPROVAL ERROR] " . $e->getMessage());
            return $this->json([
                'error' => 'Failed to approve payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject a payment verification
     */
    public function reject($id)
    {
        try {
            // Validate CSRF token
            $token = $_POST['_token'] ?? $_REQUEST['_token'] ?? '';
            if (!\Core\Security\CSRF::validateToken($token)) {
                return $this->json(['error' => 'Invalid security token. Please try again.'], 419);
            }

            $reason = $_POST['reason'] ?? 'Invalid screenshot or payment method';

            // Get verification details first
            $stmt = $this->db->prepare("SELECT user_id, product_id FROM payment_verifications WHERE id = ?");
            $stmt->execute([$id]);
            $verification = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$verification) {
                return $this->json(['error' => 'Verification not found'], 404);
            }

            // Update status
            $stmt = $this->db->prepare("
                UPDATE payment_verifications 
                SET status = 'rejected', 
                    rejection_reason = ?,
                    rejected_at = NOW(),
                    rejected_by = ?
                WHERE id = ?
            ");
            $stmt->execute([$reason, Auth::user()['id'], $id]);

            // Log the rejection
            error_log("[PAYMENT REJECTION] User {$verification['user_id']} rejected for product {$verification['product_id']} - Reason: $reason");

            return $this->json([
                'success' => true,
                'message' => '❌ Payment rejected'
            ]);
        } catch (\Exception $e) {
            error_log("[PAYMENT REJECTION ERROR] " . $e->getMessage());
            return $this->json([
                'error' => 'Failed to reject payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Resubmit rejected verification (user uploads new screenshot)
     */
    public function resubmit($id)
    {
        try {
            // Check if user owns this verification
            $stmt = $this->db->prepare("SELECT user_id FROM payment_verifications WHERE id = ? AND status = 'rejected'");
            $stmt->execute([$id]);
            $verification = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$verification || $verification['user_id'] !== Auth::user()['id']) {
                return $this->json(['error' => 'Verification not found or already processed'], 404);
            }

            // Handle screenshot upload
            if (!isset($_FILES['screenshot']) || $_FILES['screenshot']['error'] !== UPLOAD_ERR_OK) {
                return $this->json(['error' => 'No screenshot provided'], 400);
            }

            $file = $_FILES['screenshot'];
            $allowed = ['image/jpeg', 'image/png', 'image/gif'];
            
            if (!in_array($file['type'], $allowed)) {
                return $this->json(['error' => 'Invalid file type. Use JPG, PNG, or GIF'], 400);
            }

            if ($file['size'] > 5 * 1024 * 1024) { // 5MB
                return $this->json(['error' => 'File too large. Max 5MB'], 400);
            }

            // Save new screenshot
            $dir = 'uploads/payments';
            if (!is_dir($dir)) mkdir($dir, 0755, true);

            $filename = uniqid() . '_' . basename($file['name']);
            $filepath = $dir . '/' . $filename;

            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                return $this->json(['error' => 'Failed to save screenshot'], 500);
            }

            // Update verification with new screenshot and reset to pending
            $stmt = $this->db->prepare("
                UPDATE payment_verifications 
                SET status = 'pending',
                    screenshot_path = ?,
                    rejection_reason = NULL,
                    rejected_at = NULL,
                    rejected_by = NULL,
                    updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$filepath, $id]);

            error_log("[PAYMENT RESUBMIT] User {$verification['user_id']} resubmitted verification $id");

            return $this->json([
                'success' => true,
                'message' => '✅ Screenshot resubmitted and moved to pending review'
            ]);
        } catch (\Exception $e) {
            error_log("[PAYMENT RESUBMIT ERROR] " . $e->getMessage());
            return $this->json([
                'error' => 'Failed to resubmit: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Grant game access to user for approved payment
     */
    public function grantAccess($id)
    {
        try {
            // Validate CSRF token
            $token = $_POST['_token'] ?? $_REQUEST['_token'] ?? '';
            if (!\Core\Security\CSRF::validateToken($token)) {
                return $this->json(['error' => 'Invalid security token. Please try again.'], 419);
            }

            // Get verification details
            $stmt = $this->db->prepare("SELECT user_id, product_id, access_level, status FROM payment_verifications WHERE id = ?");
            $stmt->execute([$id]);
            $verification = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$verification) {
                return $this->json(['error' => 'Verification not found'], 404);
            }

            // Check if product exists and is a game
            $stmt = $this->db->prepare("SELECT id, product_type FROM products WHERE id = ? AND product_type = 'game'");
            $stmt->execute([$verification['product_id']]);
            $product = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$product) {
                return $this->json(['error' => 'Product not found or is not a game'], 404);
            }

            // Create or update game_access record with verified access level
            $access_level = $verification['access_level'] ?: 'full';
            $stmt = $this->db->prepare("
                INSERT INTO game_access (user_id, product_id, access_level, granted_at)
                VALUES (?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE 
                    access_level = VALUES(access_level),
                    granted_at = NOW()
            ");
            $stmt->execute([
                $verification['user_id'],
                $verification['product_id'],
                $access_level
            ]);

            error_log("[GAME ACCESS] User {$verification['user_id']} granted access to product {$verification['product_id']}");

            return $this->json([
                'success' => true,
                'message' => '🔓 Game access granted to user!'
            ]);
        } catch (\Exception $e) {
            error_log("[GAME ACCESS ERROR] " . $e->getMessage());
            return $this->json([
                'error' => 'Failed to grant access: ' . $e->getMessage()
            ], 500);
        }
    }
}
