<?php

namespace App\Controllers;

use Core\Controller;
use Core\View;
use Core\Database;
use Core\Session;
use Core\Security\CSRF;
use PDO;

class AdminReportController extends Controller
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

        // Date range filters
        $startDate = $_GET['start_date'] ?? date('Y-m-01'); // First day of month
        $endDate = $_GET['end_date'] ?? date('Y-m-d'); // Today
        $category = $_GET['category'] ?? 'all'; // all, game, product

        // Total Revenue by Category
        $revenueQuery = "
            SELECT 
                COALESCE(SUM(o.total_amount), 0) as total_revenue,
                COUNT(DISTINCT o.id) as total_orders
            FROM orders o
            WHERE o.status IN ('completed', 'shipped')
            AND DATE(o.created_at) BETWEEN ? AND ?
        ";
        
        $stmt = $this->db->prepare($revenueQuery);
        $stmt->execute([$startDate, $endDate]);
        $revenueStats = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Get pending orders count
        $pendingStmt = $this->db->prepare("SELECT COUNT(*) as pending_count FROM orders WHERE status = 'pending' AND DATE(created_at) BETWEEN ? AND ?");
        $pendingStmt->execute([$startDate, $endDate]);
        $pendingData = $pendingStmt->fetch(PDO::FETCH_ASSOC);
        $revenueStats['pending_orders'] = $pendingData['pending_count'] ?? 0;
        
        // Get average order value
        $revenueStats['avg_order_value'] = $revenueStats['total_orders'] > 0 
            ? $revenueStats['total_revenue'] / $revenueStats['total_orders'] 
            : 0;

        // Daily Revenue Breakdown
        $dailyQuery = "
            SELECT 
                DATE(o.created_at) as date,
                COALESCE(SUM(o.total_amount), 0) as revenue,
                COUNT(DISTINCT o.id) as orders
            FROM orders o
            WHERE o.status IN ('completed', 'shipped')
            AND DATE(o.created_at) BETWEEN ? AND ?
            GROUP BY DATE(o.created_at)
            ORDER BY date DESC
            LIMIT 30
        ";
        
        $stmt = $this->db->prepare($dailyQuery);
        $stmt->execute([$startDate, $endDate]);
        $dailyRevenue = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Top Selling Items
        $topItemsQuery = "
            SELECT 
                p.id,
                p.name,
                p.category_id,
                COUNT(oi.id) as sales_count,
                SUM(oi.quantity) as total_quantity,
                SUM(oi.unit_price * oi.quantity) as total_revenue
            FROM order_items oi
            INNER JOIN products p ON oi.product_id = p.id
            INNER JOIN orders o ON oi.order_id = o.id
            WHERE o.status IN ('completed', 'shipped')
            AND DATE(o.created_at) BETWEEN ? AND ?
            GROUP BY p.id, p.name, p.category_id
            ORDER BY total_revenue DESC 
            LIMIT 10
        ";
        
        $stmt = $this->db->prepare($topItemsQuery);
        $stmt->execute([$startDate, $endDate]);
        $topItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Order Status Breakdown
        $statusQuery = "
            SELECT 
                status,
                COUNT(*) as count,
                SUM(total_amount) as revenue
            FROM orders
            WHERE DATE(created_at) BETWEEN ? AND ?
            GROUP BY status
        ";
        
        $stmt = $this->db->prepare($statusQuery);
        $stmt->execute([$startDate, $endDate]);
        $statusBreakdown = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Payment Method Breakdown
        $paymentQuery = "
            SELECT 
                payment_method,
                COUNT(*) as count,
                SUM(total_amount) as revenue
            FROM orders
            WHERE status IN ('completed', 'shipped')
            AND DATE(created_at) BETWEEN ? AND ?
            GROUP BY payment_method
        ";
        
        $stmt = $this->db->prepare($paymentQuery);
        $stmt->execute([$startDate, $endDate]);
        $paymentBreakdown = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [
            'revenueStats' => $revenueStats,
            'dailyRevenue' => $dailyRevenue,
            'topItems' => $topItems,
            'statusBreakdown' => $statusBreakdown,
            'paymentBreakdown' => $paymentBreakdown,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'category' => $category,
            'csrf_token' => CSRF::getToken()
        ];

        $config = require __DIR__ . '/../../config/app.php';
        $data['basePath'] = rtrim($config['base_path'] ?? '', '/');

        return View::render('admin/reports', $data);
    }

    public function export()
    {
        $user = Session::get('user');
        if (!$user || $user['role'] !== 'admin') {
            http_response_code(403);
            die('Unauthorized');
        }

        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');

        // Get detailed sales data
        $query = "
            SELECT 
                o.id as order_id,
                o.created_at as order_date,
                u.name as customer_name,
                u.email as customer_email,
                p.name as product_name,
                oi.quantity,
                oi.unit_price,
                (oi.quantity * oi.unit_price) as line_total,
                o.total_amount as order_total,
                o.status,
                o.payment_method
            FROM orders o
            INNER JOIN order_items oi ON o.id = oi.order_id
            INNER JOIN products p ON oi.product_id = p.id
            LEFT JOIN users u ON o.user_id = u.id
            WHERE DATE(o.created_at) BETWEEN ? AND ?
            ORDER BY o.created_at DESC
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$startDate, $endDate]);
        $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Generate CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="sales_report_' . $startDate . '_to_' . $endDate . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Add BOM for Excel UTF-8 recognition
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // CSV Headers
        fputcsv($output, [
            'Order ID', 
            'Date', 
            'Customer Name', 
            'Customer Email', 
            'Product', 
            'Quantity', 
            'Unit Price (TZS)', 
            'Line Total (TZS)', 
            'Order Total (TZS)',
            'Payment Method',
            'Status'
        ]);
        
        // CSV Data
        foreach ($sales as $sale) {
            fputcsv($output, [
                $sale['order_id'],
                $sale['order_date'],
                $sale['customer_name'] ?? 'Guest',
                $sale['customer_email'] ?? 'N/A',
                $sale['product_name'],
                $sale['quantity'],
                number_format($sale['unit_price'], 2),
                number_format($sale['line_total'], 2),
                number_format($sale['order_total'], 2),
                ucfirst($sale['payment_method'] ?? 'N/A'),
                ucfirst($sale['status'])
            ]);
        }
        
        fclose($output);
        exit;
    }
}
