<?php

namespace App\Controllers;

use Core\Controller;
use Core\View;
use Core\Cart;
use Core\Session;
use Core\Security\CSRF;
use Core\Database;
use PDO;

class OrderController extends Controller
{
    public function index()
    {
        $user = Session::get('user');
        if (!$user) {
            $config = require __DIR__ . '/../../config/app.php';
            $basePath = rtrim($config['base_path'] ?? '', '/');
            header('Location: ' . $basePath . '/login');
            exit;
        }
        
        // Get user's orders from database
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$user['id']]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return View::render('order/index', ['orders' => $orders]);
    }

    public function dashboard()
    {
        $user = Session::get('user');
        if (!$user) {
            $config = require __DIR__ . '/../../config/app.php';
            $basePath = rtrim($config['base_path'] ?? '', '/');
            header('Location: ' . $basePath . '/login');
            exit;
        }

        $db = Database::getInstance()->getConnection();
        
        // Get unlocked game access
        $stmt = $db->prepare("
            SELECT ga.*, p.name, p.product_type
            FROM game_access ga
            LEFT JOIN products p ON ga.product_id = p.id
            WHERE ga.user_id = ? AND ga.is_unlocked = 1
            ORDER BY ga.unlocked_at DESC
        ");
        $stmt->execute([$user['id']]);
        $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get pending game access
        $stmt = $db->prepare("
            SELECT ga.*, p.name, p.product_type
            FROM game_access ga
            LEFT JOIN products p ON ga.product_id = p.id
            WHERE ga.user_id = ? AND ga.is_unlocked = 0
            ORDER BY ga.created_at DESC
        ");
        $stmt->execute([$user['id']]);
        $pendingPackages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get pending orders with payment verification status
        $stmt = $db->prepare("
            SELECT 
                o.id,
                o.total_amount,
                o.status,
                o.created_at,
                pv.id as verification_id,
                pv.status as verification_status,
                pv.payment_method
            FROM orders o
            LEFT JOIN payment_verifications pv ON o.id = pv.order_id
            WHERE o.user_id = ? AND o.status IN ('pending', 'paid')
            ORDER BY o.created_at DESC
        ");
        $stmt->execute([$user['id']]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return View::render('order/dashboard', [
            'user' => $user,
            'packages' => $packages,
            'pendingPackages' => $pendingPackages,
            'orders' => $orders
        ]);
    }
    
    public function show($id)
    {
        $user = Session::get('user');
        if (!$user) {
            $config = require __DIR__ . '/../../config/app.php';
            $basePath = rtrim($config['base_path'] ?? '', '/');
            header('Location: ' . $basePath . '/login');
            exit;
        }
        
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
        $stmt->execute([(int)$id, $user['id']]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$order) {
            http_response_code(404);
            return View::render('errors/404');
        }
        
        return View::render('order/show', ['order' => $order]);
    }
    
    public function checkout()
    {
        $user = Session::get('user');
        if (!$user) {
            $config = require __DIR__ . '/../../config/app.php';
            $basePath = rtrim($config['base_path'] ?? '', '/');
            header('Location: ' . $basePath . '/login');
            exit;
        }

        $cartItems = Cart::items();
        if (empty($cartItems)) {
            $config = require __DIR__ . '/../../config/app.php';
            $basePath = rtrim($config['base_path'] ?? '', '/');
            header('Location: ' . $basePath . '/cart?error=Cart is empty');
            exit;
        }

        $token = CSRF::getToken();
        return View::render('order/checkout', [
            'items' => $cartItems,
            'summary' => Cart::summary(),
            'user' => $user,
            'csrf_token' => $token
        ]);
    }
    
    public function create()
    {
        $user = Session::get('user');
        if (!$user) {
            $config = require __DIR__ . '/../../config/app.php';
            $basePath = rtrim($config['base_path'] ?? '', '/');
            header('Location: ' . $basePath . '/login');
            exit;
        }
        
        if (Cart::isEmpty()) {
            $config = require __DIR__ . '/../../config/app.php';
            $basePath = rtrim($config['base_path'] ?? '', '/');
            header('Location: ' . $basePath . '/cart?error=Cart is empty');
            exit;
        }
        
        $db = Database::getInstance()->getConnection();
        
        try {
            // Start transaction
            $db->beginTransaction();
            
            $cartSummary = Cart::summary();
            
            // Create order
            $stmt = $db->prepare("
                INSERT INTO orders (user_id, total_amount, status, payment_method)
                VALUES (?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $user['id'],
                $cartSummary['total'],
                'pending',
                'payment_verification'
            ]);
            
            $order_id = $db->lastInsertId();
            $cartItems = Cart::items();
            
            // Add order items and check for games needing verification
            $hasGames = false;
            foreach ($cartItems as $item) {
                // Get product info
                $productStmt = $db->prepare("SELECT product_type, game_type FROM products WHERE id = ?");
                $productStmt->execute([$item['product_id']]);
                $product = $productStmt->fetch(PDO::FETCH_ASSOC);
                
                // Insert order item
                $itemStmt = $db->prepare("
                    INSERT INTO order_items (order_id, product_id, quantity, unit_price, needs_verification)
                    VALUES (?, ?, ?, ?, ?)
                ");
                
                $needsVerification = ($product && $product['product_type'] === 'game') ? 1 : 0;
                $itemStmt->execute([
                    $order_id,
                    $item['product_id'],
                    $item['quantity'] ?? 1,
                    $item['price'] ?? 0,
                    $needsVerification
                ]);
                
                $order_item_id = $db->lastInsertId();
                
                // If it's a game, create locked game access and create payment verification record
                if ($product && $product['product_type'] === 'game') {
                    $hasGames = true;
                    
                    // Create locked game access
                    $accessStmt = $db->prepare("
                        INSERT INTO game_access (user_id, product_id, order_item_id, is_unlocked)
                        VALUES (?, ?, ?, 0)
                    ");
                    $accessStmt->execute([$user['id'], $item['product_id'], $order_item_id]);
                    
                    // Create payment verification record (user will upload screenshot)
                    $verifyStmt = $db->prepare("
                        INSERT INTO payment_verifications (order_id, user_id, order_item_id, payment_amount, status)
                        VALUES (?, ?, ?, ?, 'pending')
                    ");
                    $verifyStmt->execute([
                        $order_id,
                        $user['id'],
                        $order_item_id,
                        $item['price'] ?? 0
                    ]);
                }
            }
            
            $db->commit();
            
            // Clear cart after successful order
            Cart::clear();
            
            $config = require __DIR__ . '/../../config/app.php';
            $basePath = rtrim($config['base_path'] ?? '', '/');
            
            // Redirect to payment upload if games, else success
            if ($hasGames) {
                header('Location: ' . $basePath . '/payment/initiate?order=' . $order_id);
            } else {
                header('Location: ' . $basePath . '/payment/success?order=' . $order_id);
            }
            exit;
            
        } catch (\Exception $e) {
            $db->rollBack();
            $config = require __DIR__ . '/../../config/app.php';
            $basePath = rtrim($config['base_path'] ?? '', '/');
            header('Location: ' . $basePath . '/order/checkout?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    public function createWithPayment()
    {
        // Accept JSON or form data
        header('Content-Type: application/json');
        
        $user = Session::get('user');
        if (!$user) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        // Get cart items
        $cartItems = Cart::items();
        if (empty($cartItems)) {
            echo json_encode(['success' => false, 'message' => 'Cart is empty']);
            exit;
        }

        $db = Database::getInstance()->getConnection();

        try {
            // Validate and process screenshot upload
            if (!isset($_FILES['screenshot']) || $_FILES['screenshot']['error'] !== UPLOAD_ERR_OK) {
                throw new \Exception('Screenshot upload failed');
            }

            $screenshot_file = $_FILES['screenshot'];
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 5 * 1024 * 1024; // 5MB

            if (!in_array($screenshot_file['type'], $allowed_types)) {
                throw new \Exception('Invalid file type. Only JPG, PNG, GIF allowed');
            }

            if ($screenshot_file['size'] > $max_size) {
                throw new \Exception('File too large. Max 5MB allowed');
            }

            // Create uploads directory if not exists
            $upload_dir = __DIR__ . '/../../uploads/payments/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            // Save screenshot with unique name
            $file_ext = pathinfo($screenshot_file['name'], PATHINFO_EXTENSION);
            $screenshot_name = 'payment_' . $user['id'] . '_' . time() . '.' . $file_ext;
            $screenshot_path = $upload_dir . $screenshot_name;

            if (!move_uploaded_file($screenshot_file['tmp_name'], $screenshot_path)) {
                throw new \Exception('Failed to save screenshot');
            }

            // Start transaction
            $db->beginTransaction();

            // Get form data
            $payment_method = $_POST['payment_method'] ?? 'unknown';
            $first_name = $_POST['first_name'] ?? '';
            $last_name = $_POST['last_name'] ?? '';
            $email = $_POST['email'] ?? '';
            $street = $_POST['street'] ?? '';
            $city = $_POST['city'] ?? '';
            $state = $_POST['state'] ?? '';
            $zip = $_POST['zip'] ?? '';
            $country = $_POST['country'] ?? '';

            // Validate required fields
            if (empty($first_name) || empty($last_name) || empty($email) || empty($street) || empty($city)) {
                throw new \Exception('Missing required address fields');
            }

            $cartSummary = Cart::summary();

            // Create order
            $stmt = $db->prepare("
                INSERT INTO orders (user_id, total_amount, status, payment_method, first_name, last_name, email, street, city, state, zip, country, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");

            $stmt->execute([
                $user['id'],
                $cartSummary['total'],
                'pending',
                $payment_method,
                $first_name,
                $last_name,
                $email,
                $street,
                $city,
                $state,
                $zip,
                $country
            ]);

            $order_id = $db->lastInsertId();

            // Add order items
            $hasGames = false;
            foreach ($cartItems as $item) {
                // Get product info
                $productStmt = $db->prepare("SELECT product_type, game_type FROM products WHERE id = ?");
                $productStmt->execute([$item['product_id']]);
                $product = $productStmt->fetch(PDO::FETCH_ASSOC);

                // Insert order item
                $itemStmt = $db->prepare("
                    INSERT INTO order_items (order_id, product_id, quantity, unit_price, needs_verification)
                    VALUES (?, ?, ?, ?, ?)
                ");

                $needsVerification = ($product && $product['product_type'] === 'game') ? 1 : 0;
                $itemStmt->execute([
                    $order_id,
                    $item['product_id'],
                    $item['quantity'] ?? 1,
                    $item['price'] ?? 0,
                    $needsVerification
                ]);

                $order_item_id = $db->lastInsertId();

                // If it's a game, create locked game access and payment verification record
                if ($product && $product['product_type'] === 'game') {
                    $hasGames = true;

                    // Create locked game access
                    $accessStmt = $db->prepare("
                        INSERT INTO game_access (user_id, product_id, order_item_id, is_unlocked)
                        VALUES (?, ?, ?, 0)
                    ");
                    $accessStmt->execute([$user['id'], $item['product_id'], $order_item_id]);
                }
            }

            // Create payment verification record with screenshot
            $verifyStmt = $db->prepare("
                INSERT INTO payment_verifications (
                    order_id, user_id, payment_method, payment_amount, 
                    screenshot_path, status, created_at
                )
                VALUES (?, ?, ?, ?, ?, 'pending', NOW())
            ");
            $verifyStmt->execute([
                $order_id,
                $user['id'],
                $payment_method,
                $cartSummary['total'],
                '/uploads/payments/' . $screenshot_name
            ]);

            $db->commit();

            // Clear cart after successful order
            Cart::clear();

            // Return success response
            echo json_encode([
                'success' => true,
                'message' => 'Payment verification submitted successfully',
                'order_id' => $order_id,
                'redirect' => '/hasheem/order/checkout-success?order=' . $order_id
            ]);
            exit;

        } catch (\Exception $e) {
            if (isset($db)) {
                $db->rollBack();
            }
            // Clean up uploaded file if order creation failed
            if (isset($screenshot_path) && file_exists($screenshot_path)) {
                unlink($screenshot_path);
            }

            http_response_code(400);
            
            // Log the error for debugging
            error_log("Payment creation error: " . $e->getMessage() . " (Line: " . $e->getLine() . ")");
            
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'debug' => true
            ]);
            exit;
        }
    }}