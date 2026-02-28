<?php

namespace App\Controllers;

use Core\Controller;
use Core\View;
use Core\Database;
use Core\Session;
use PDO;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $db = Database::getInstance()->getConnection();
        
        // === REVENUE ANALYTICS ===
        // Game Revenue (paid orders only)
        $stmt = $db->query("
            SELECT COALESCE(SUM(oi.unit_price * oi.quantity), 0) as total
            FROM order_items oi
            INNER JOIN orders o ON oi.order_id = o.id
            INNER JOIN products p ON oi.product_id = p.id
            WHERE p.product_type = 'game' AND o.status = 'paid'
        ");
        $gameRevenue = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        
        // Product Revenue (paid orders only)
        $stmt = $db->query("
            SELECT COALESCE(SUM(oi.unit_price * oi.quantity), 0) as total
            FROM order_items oi
            INNER JOIN orders o ON oi.order_id = o.id
            INNER JOIN products p ON oi.product_id = p.id
            WHERE p.product_type = 'accessory' AND o.status = 'paid'
        ");
        $productRevenue = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        
        // Total Revenue
        $totalRevenue = $gameRevenue + $productRevenue;
        
        // Today's Sales
        $stmt = $db->query("
            SELECT COALESCE(SUM(oi.unit_price * oi.quantity), 0) as total
            FROM order_items oi
            INNER JOIN orders o ON oi.order_id = o.id
            WHERE o.status = 'paid' AND DATE(o.created_at) = CURDATE()
        ");
        $todaySales = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        
        // === COUNTERS ===
        // Total Products
        $stmt = $db->query("SELECT COUNT(*) as count FROM products WHERE is_active = 1");
        $totalProducts = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
        
        // Total Users (active only)
        try {
            $stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE (blocked = 0 OR status = 'active')");
            $totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
        } catch (\PDOException $e) {
            $stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE blocked = 0");
            $totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
        }
        
        // Blocked Users
        try {
            $stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE blocked = 1 OR status = 'blocked'");
            $blockedUsers = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
        } catch (\PDOException $e) {
            $stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE blocked = 1");
            $blockedUsers = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
        }
        
        // Total Orders
        $stmt = $db->query("SELECT COUNT(*) as count FROM orders");
        $totalOrders = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
        
        // Pending Verifications
        $stmt = $db->query("SELECT COUNT(*) as count FROM payment_verifications WHERE status = 'pending'");
        $pendingVerifications = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
        
        // Approved Verifications
        $stmt = $db->query("SELECT COUNT(*) as count FROM payment_verifications WHERE status = 'approved'");
        $approvedVerifications = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
        
        // === CHARTS DATA ===
        // Last 7 days revenue
        $stmt = $db->query("
            SELECT 
                DATE(o.created_at) as date,
                COALESCE(SUM(oi.unit_price * oi.quantity), 0) as revenue
            FROM orders o
            INNER JOIN order_items oi ON o.id = oi.order_id
            WHERE o.status = 'paid' 
            AND o.created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            GROUP BY DATE(o.created_at)
            ORDER BY date ASC
        ");
        $revenueChart = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Top selling items
        $stmt = $db->query("
            SELECT 
                p.name,
                p.product_type,
                COUNT(oi.id) as sales_count,
                SUM(oi.unit_price * oi.quantity) as total_revenue
            FROM order_items oi
            INNER JOIN products p ON oi.product_id = p.id
            INNER JOIN orders o ON oi.order_id = o.id
            WHERE o.status = 'paid'
            GROUP BY p.id
            ORDER BY sales_count DESC
            LIMIT 5
        ");
        $topSellers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Recent Products (last 8)
        $stmt = $db->query("
            SELECT id, name, price, product_type, game_type, is_active 
            FROM products 
            ORDER BY created_at DESC 
            LIMIT 8
        ");
        $recentProducts = $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
        
        // Recent Transactions (last 10 orders with user info)
        $stmt = $db->query("
            SELECT 
                o.id,
                o.user_id,
                o.total_amount,
                o.status,
                o.created_at,
                u.name,
                u.email
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            ORDER BY o.created_at DESC
            LIMIT 10
        ");
        $recentTransactions = $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];

        // Get base path
        $config = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');

        $data = [
            // Revenue
            'gameRevenue' => $gameRevenue,
            'productRevenue' => $productRevenue,
            'totalRevenue' => $totalRevenue,
            'todaySales' => $todaySales,
            
            // Counters
            'totalProducts' => $totalProducts,
            'totalUsers' => $totalUsers,
            'blockedUsers' => $blockedUsers,
            'totalOrders' => $totalOrders,
            'pendingVerifications' => $pendingVerifications,
            'approvedVerifications' => $approvedVerifications,
            
            // Charts
            'revenueChart' => $revenueChart,
            'topSellers' => $topSellers,
            'recentProducts' => $recentProducts,
            'recentTransactions' => $recentTransactions,
            
            // Config
            'basePath' => $basePath
        ];

        return View::render('admin/dashboard', $data);
    }
}
