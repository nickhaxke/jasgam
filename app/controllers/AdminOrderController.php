<?php

namespace App\Controllers;

use Core\Controller;
use Core\View;
use Core\DataManager;
use Core\Security\CSRF;
use Core\Database;
use PDO;

class AdminOrderController extends Controller
{
    public function index()
    {
        $orders = [];
        $stats = [
            'total' => 0,
            'pending' => 0,
            'completed' => 0,
            'revenue' => 0
        ];
        
        try {
            $db = Database::getInstance()->getConnection();
            
            if (!$db) {
                throw new \Exception("Database connection failed");
            }
            
            // Get all orders
            $stmt = $db->query("SELECT * FROM orders ORDER BY created_at DESC");
            
            if (!$stmt) {
                throw new \Exception("Query failed");
            }
            
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (!is_array($orders)) {
                $orders = [];
            }
            
            // Calculate stats and add user names
            foreach ($orders as &$order) {
                $order['user_name'] = 'Guest';
                $order['item_count'] = 0;
                $order['total'] = $order['total_amount'] ?? 0;
                
                // Normalize status - handle NULL or empty as 'pending'
                if (empty($order['status'])) {
                    $order['status'] = 'pending';
                }
                
                // Update stats
                $stats['total']++;
                $currentStatus = strtolower(trim($order['status']));
                
                if ($currentStatus === 'pending') {
                    $stats['pending']++;
                } elseif ($currentStatus === 'completed') {
                    $stats['completed']++;
                }
                
                // Add to revenue if completed or any paid status
                if (in_array($currentStatus, ['completed', 'paid'])) {
                    $stats['revenue'] += floatval($order['total_amount'] ?? 0);
                }
                
                // Get user name
                if (!empty($order['user_id'])) {
                    try {
                        $userStmt = $db->prepare("SELECT name FROM users WHERE id = ?");
                        if ($userStmt && $userStmt->execute([$order['user_id']])) {
                            $user = $userStmt->fetch(PDO::FETCH_ASSOC);
                            if ($user) {
                                $order['user_name'] = $user['name'];
                            }
                        }
                    } catch (\Exception $e) {
                        error_log("User lookup error: " . $e->getMessage());
                    }
                }
                
                // Get item count
                try {
                    $itemStmt = $db->prepare("SELECT COUNT(*) as count FROM order_items WHERE order_id = ?");
                    if ($itemStmt && $itemStmt->execute([$order['id']])) {
                        $itemCount = $itemStmt->fetch(PDO::FETCH_ASSOC);
                        $order['item_count'] = $itemCount['count'] ?? 0;
                    }
                } catch (\Exception $e) {
                    error_log("Item count error: " . $e->getMessage());
                }
            }
            unset($order);
            
        } catch (\Exception $e) {
            error_log("AdminOrderController::index - " . $e->getMessage());
            $orders = [];
        }
        
        $config = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');
        $token = CSRF::getToken();
        
        return View::render('admin/orders', [
            'orders' => $orders,
            'stats' => $stats,
            'csrf_token' => $token,
            'basePath' => $basePath
        ]);
    }

    public function viewOrder($id)
    {
        $config = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');
        
        try {
            $db = Database::getInstance()->getConnection();
            
            if (!$db) {
                throw new \Exception("Database connection failed");
            }
            
            // Get order details
            $stmt = $db->prepare("SELECT * FROM orders WHERE id = ?");
            $stmt->execute([$id]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$order) {
                header('Location: ' . $basePath . '/admin/orders');
                exit;
            }
            
            // Normalize status
            if (empty($order['status'])) {
                $order['status'] = 'pending';
            }
            
            // Add user name
            $order['user_name'] = 'Guest';
            if (!empty($order['user_id'])) {
                $userStmt = $db->prepare("SELECT name FROM users WHERE id = ?");
                if ($userStmt && $userStmt->execute([$order['user_id']])) {
                    $user = $userStmt->fetch(PDO::FETCH_ASSOC);
                    if ($user) {
                        $order['user_name'] = $user['name'];
                    }
                }
            }
            
            // Get item count
            $itemStmt = $db->prepare("SELECT COUNT(*) as count FROM order_items WHERE order_id = ?");
            $itemStmt->execute([$id]);
            $itemCount = $itemStmt->fetch(PDO::FETCH_ASSOC);
            $order['item_count'] = $itemCount['count'] ?? 0;
            
            // Get order items with product details
            $itemsStmt = $db->prepare("
                SELECT oi.*, p.name as product_name, p.image_url 
                FROM order_items oi 
                LEFT JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = ?
            ");
            $itemsStmt->execute([$id]);
            $order['items'] = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
            
            $order['total'] = $order['total_amount'] ?? 0;
            
        } catch (\Exception $e) {
            error_log("AdminOrderController::viewOrder - " . $e->getMessage());
            header('Location: ' . $basePath . '/admin/orders');
            exit;
        }
        
        $token = CSRF::getToken();
        return View::render('admin/order-detail', [
            'order' => $order, 
            'csrf_token' => $token,
            'basePath' => $basePath
        ]);
    }

    public function updateStatus($id)
    {
        $config = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');
        
        try {
            $db = Database::getInstance()->getConnection();
            $status = $_POST['status'] ?? 'pending';
            
            $stmt = $db->prepare("UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$status, $id]);
            
        } catch (\Exception $e) {
            error_log("AdminOrderController::updateStatus - " . $e->getMessage());
        }
        
        header('Location: ' . $basePath . '/admin/orders');
        exit;
    }
    
    public function paymentDetails($id)
    {
        $db = Database::getInstance()->getConnection();
        
        // Get order details
        $stmt = $db->prepare("
            SELECT 
                o.*,
                u.name,
                u.email
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            WHERE o.id = ?
        ");
        $stmt->execute([$id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$order) {
            echo '<div style="text-align: center; padding: 40px; color: #ef4444;">Order not found.</div>';
            return;
        }
        
        // Get order items
        $stmt = $db->prepare("
            SELECT 
                oi.*,
                p.name as product_name,
                p.product_type
            FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$id]);
        $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get payment verifications
        $stmt = $db->prepare("
            SELECT * FROM payment_verifications 
            WHERE order_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->execute([$id]);
        $verifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $config = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');
        
        // Render popup content
        ?>
        <div style="display: grid; gap: 20px;">
            <!-- Order Summary -->
            <div class="glass" style="padding: 20px;">
                <h3 style="margin-top: 0;">Order #<?= $order['id'] ?></h3>
                <div style="display: grid; gap: 10px;">
                    <div><strong>Customer:</strong> <?= htmlspecialchars($order['name'] ?? $order['email']) ?></div>
                    <div><strong>Email:</strong> <?= htmlspecialchars($order['email'] ?? 'N/A') ?></div>
                    <div><strong>Total Amount:</strong> TZS <?= number_format($order['total_amount'], 0) ?></div>
                    <div>
                        <strong>Status:</strong> 
                        <?php
                        $statusClass = 'badge-warning';
                        if ($order['status'] === 'paid') $statusClass = 'badge-success';
                        if ($order['status'] === 'rejected') $statusClass = 'badge-danger';
                        ?>
                        <span class="badge <?= $statusClass ?>"><?= ucfirst($order['status']) ?></span>
                    </div>
                    <div><strong>Order Date:</strong> <?= date('d M, Y H:i', strtotime($order['created_at'])) ?></div>
                </div>
            </div>
            
            <!-- Order Items -->
            <div class="glass" style="padding: 20px;">
                <h4 style="margin-top: 0;">Items Ordered</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Type</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orderItems as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['product_name']) ?></td>
                                <td><?= htmlspecialchars($item['product_type']) ?></td>
                                <td><?= $item['quantity'] ?></td>
                                <td>TZS <?= number_format($item['unit_price'], 0) ?></td>
                                <td>TZS <?= number_format($item['unit_price'] * $item['quantity'], 0) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Payment Verifications -->
            <?php if (!empty($verifications)): ?>
                <div class="glass" style="padding: 20px;">
                    <h4 style="margin-top: 0;">Payment Verifications</h4>
                    <?php foreach ($verifications as $verification): ?>
                        <div style="padding: 15px; border: 1px solid var(--line); border-radius: 12px; margin-bottom: 15px;">
                            <div style="display: grid; gap: 10px;">
                                <div>
                                    <strong>Amount Paid:</strong> 
                                    TZS <?= number_format($verification['payment_amount'] ?? 0, 0) ?>
                                </div>
                                <div>
                                    <strong>Payment Method:</strong> 
                                    <?= htmlspecialchars($verification['payment_method'] ?? 'N/A') ?>
                                </div>
                                <?php if (!empty($verification['screenshot_path'])): ?>
                                    <div>
                                        <strong>Payment Proof:</strong><br>
                                        <img src="<?= $basePath . '/' . $verification['screenshot_path'] ?>" 
                                             alt="Payment Proof" 
                                             style="max-width: 100%; max-height: 300px; border-radius: 8px; margin-top: 10px;">
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <strong>Status:</strong> 
                                    <?php
                                    $vStatusClass = 'badge-warning';
                                    if ($verification['status'] === 'approved') $vStatusClass = 'badge-success';
                                    if ($verification['status'] === 'rejected') $vStatusClass = 'badge-danger';
                                    ?>
                                    <span class="badge <?= $vStatusClass ?>"><?= ucfirst($verification['status']) ?></span>
                                </div>
                                <div>
                                    <strong>Submitted:</strong> 
                                    <?= date('d M, Y H:i', strtotime($verification['created_at'])) ?>
                                </div>
                                
                                <!-- Action Buttons for Pending Payments -->
                                <?php if ($verification['status'] === 'pending'): ?>
                                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                                        <form method="POST" action="<?= $basePath ?>/admin/payment-approve/<?= $verification['id'] ?>" style="display: inline;">
                                            <input type="hidden" name="csrf_token" value="<?= \Core\Security\CSRF::getToken() ?>">
                                            <button type="submit" class="btn btn-success">
                                                ✓ Approve Payment
                                            </button>
                                        </form>
                                        <form method="POST" action="<?= $basePath ?>/admin/payment-reject/<?= $verification['id'] ?>" style="display: inline;">
                                            <input type="hidden" name="csrf_token" value="<?= \Core\Security\CSRF::getToken() ?>">
                                            <button type="submit" class="btn btn-danger">
                                                ✕ Reject Payment
                                            </button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="glass" style="padding: 20px; text-align: center; color: var(--muted);">
                    No payment verification submitted yet.
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
}
