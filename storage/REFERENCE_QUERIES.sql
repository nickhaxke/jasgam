-- ==========================================
-- SYSTEM HEALTH & ANALYSIS QUERIES
-- Quick reference for admins and developers
-- ==========================================

-- ==========================================
-- REVENUE QUERIES
-- ==========================================

-- Total Game Revenue (Approved Orders Only)
SELECT 
    COALESCE(SUM(oi.unit_price * oi.quantity), 0) as game_revenue
FROM order_items oi
INNER JOIN orders o ON oi.order_id = o.id
INNER JOIN products p ON oi.product_id = p.id
WHERE p.product_type = 'game' 
AND o.status = 'approved';

-- Total Product Revenue (Approved Orders Only)
SELECT 
    COALESCE(SUM(oi.unit_price * oi.quantity), 0) as product_revenue
FROM order_items oi
INNER JOIN orders o ON oi.order_id = o.id
INNER JOIN products p ON oi.product_id = p.id
WHERE p.product_type = 'accessory' 
AND o.status = 'approved';

-- Today's Sales
SELECT 
    COALESCE(SUM(oi.unit_price * oi.quantity), 0) as today_sales
FROM order_items oi
INNER JOIN orders o ON oi.order_id = o.id
WHERE o.status = 'approved' 
AND DATE(o.created_at) = CURDATE();

-- This Month's Revenue
SELECT 
    COALESCE(SUM(oi.unit_price * oi.quantity), 0) as month_revenue
FROM order_items oi
INNER JOIN orders o ON oi.order_id = o.id
WHERE o.status = 'approved' 
AND YEAR(o.created_at) = YEAR(CURDATE())
AND MONTH(o.created_at) = MONTH(CURDATE());

-- Revenue by Category (Game vs Product)
SELECT 
    p.product_type,
    COUNT(DISTINCT o.id) as total_orders,
    SUM(oi.unit_price * oi.quantity) as total_revenue
FROM order_items oi
INNER JOIN orders o ON oi.order_id = o.id
INNER JOIN products p ON oi.product_id = p.id
WHERE o.status = 'approved'
GROUP BY p.product_type;

-- Last 7 Days Revenue Trend
SELECT 
    DATE(o.created_at) as date,
    COALESCE(SUM(oi.unit_price * oi.quantity), 0) as revenue,
    COUNT(DISTINCT o.id) as orders
FROM order_items oi
INNER JOIN orders o ON oi.order_id = o.id
WHERE o.status = 'approved'
AND o.created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
GROUP BY DATE(o.created_at)
ORDER BY date ASC;

-- ==========================================
-- TOP SELLERS
-- ==========================================

-- Top 10 Best Selling Products (By Revenue)
SELECT 
    p.id,
    p.name,
    p.product_type,
    COUNT(oi.id) as sales_count,
    SUM(oi.quantity) as total_quantity,
    SUM(oi.unit_price * oi.quantity) as total_revenue
FROM order_items oi
INNER JOIN products p ON oi.product_id = p.id
INNER JOIN orders o ON oi.order_id = o.id
WHERE o.status = 'approved'
GROUP BY p.id
ORDER BY total_revenue DESC
LIMIT 10;

-- Top 10 Best Selling Games
SELECT 
    p.id,
    p.name,
    COUNT(oi.id) as sales_count,
    SUM(oi.unit_price * oi.quantity) as total_revenue
FROM order_items oi
INNER JOIN products p ON oi.product_id = p.id
INNER JOIN orders o ON oi.order_id = o.id
WHERE p.product_type = 'game' 
AND o.status = 'approved'
GROUP BY p.id
ORDER BY total_revenue DESC
LIMIT 10;

-- ==========================================
-- ORDER STATISTICS
-- ==========================================

-- Order Status Breakdown
SELECT 
    status,
    COUNT(*) as count,
    SUM(total_amount) as total_amount
FROM orders
GROUP BY status;

-- Orders by Category
SELECT 
    category,
    COUNT(*) as count,
    SUM(total_amount) as total_amount
FROM orders
WHERE status = 'approved'
GROUP BY category;

-- Pending Payment Verifications
SELECT COUNT(*) as pending_count
FROM payment_verifications
WHERE status = 'pending';

-- Average Order Value
SELECT 
    AVG(total_amount) as avg_order_value
FROM orders
WHERE status = 'approved';

-- ==========================================
-- USER STATISTICS
-- ==========================================

-- Total Users by Status
SELECT 
    CASE 
        WHEN blocked = 1 OR status = 'blocked' THEN 'blocked'
        ELSE 'active'
    END as user_status,
    COUNT(*) as count
FROM users
GROUP BY user_status;

-- Users by Role
SELECT 
    role,
    COUNT(*) as count
FROM users
GROUP BY role;

-- Top 10 Customers by Purchases
SELECT 
    u.id,
    u.name,
    u.email,
    COUNT(DISTINCT o.id) as total_orders,
    SUM(o.total_amount) as total_spent
FROM users u
INNER JOIN orders o ON u.id = o.user_id
WHERE o.status = 'approved'
GROUP BY u.id
ORDER BY total_spent DESC
LIMIT 10;

-- ==========================================
-- PRODUCT STATISTICS
-- ==========================================

-- Product Count by Type
SELECT 
    product_type,
    COUNT(*) as count,
    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_count
FROM products
GROUP BY product_type;

-- Low Stock Products
SELECT 
    id,
    name,
    product_type,
    stock
FROM products
WHERE stock < 10 
AND is_active = 1
ORDER BY stock ASC;

-- ==========================================
-- SECURITY & AUDIT
-- ==========================================

-- Recent Admin Actions
SELECT 
    al.action,
    u.name as admin_name,
    al.target_type,
    al.target_id,
    al.details,
    al.created_at
FROM audit_logs al
LEFT JOIN users u ON al.user_id = u.id
ORDER BY al.created_at DESC
LIMIT 50;

-- Actions by Admin User
SELECT 
    u.name,
    COUNT(*) as action_count
FROM audit_logs al
INNER JOIN users u ON al.user_id = u.id
GROUP BY u.id
ORDER BY action_count DESC;

-- Recent User Blocking/Unblocking
SELECT 
    al.*,
    u.name as admin_name
FROM audit_logs al
LEFT JOIN users u ON al.user_id = u.id
WHERE al.action IN ('block_user', 'unblock_user')
ORDER BY al.created_at DESC
LIMIT 20;

-- Payment Approvals Today
SELECT COUNT(*) as approvals_today
FROM audit_logs
WHERE action = 'approve_payment'
AND DATE(created_at) = CURDATE();

-- ==========================================
-- ANNOUNCEMENTS
-- ==========================================

-- Active Announcements
SELECT 
    id,
    title,
    active,
    priority,
    view_count,
    dismiss_count,
    created_at
FROM announcements
WHERE active = 1
AND (start_time IS NULL OR start_time <= NOW())
AND (end_time IS NULL OR end_time >= NOW())
ORDER BY priority DESC;

-- Announcement Performance
SELECT 
    title,
    view_count,
    dismiss_count,
    CASE 
        WHEN view_count > 0 THEN ROUND((dismiss_count / view_count) * 100, 2)
        ELSE 0
    END as dismiss_rate_percent
FROM announcements
ORDER BY view_count DESC;

-- ==========================================
-- SYSTEM INTEGRITY CHECKS
-- ==========================================

-- Orders Without Order Items
SELECT o.id, o.created_at
FROM orders o
LEFT JOIN order_items oi ON o.id = oi.order_id
WHERE oi.id IS NULL;

-- Order Items Without Product Type
SELECT COUNT(*) as count
FROM order_items
WHERE product_type IS NULL OR product_type = '';

-- Orders Without Category
SELECT COUNT(*) as count
FROM orders
WHERE category IS NULL OR category = '';

-- Payment Verifications Without Orders
SELECT pv.id
FROM payment_verifications pv
LEFT JOIN orders o ON pv.order_id = o.id
WHERE o.id IS NULL;

-- Verify Database Columns Exist
SELECT 
    COLUMN_NAME, 
    DATA_TYPE,
    IS_NULLABLE
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'orders'
AND COLUMN_NAME IN ('category', 'status', 'total_amount');

SELECT 
    COLUMN_NAME, 
    DATA_TYPE,
    IS_NULLABLE
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'order_items'
AND COLUMN_NAME IN ('product_type', 'unit_price', 'quantity');

-- Verify Tables Exist
SELECT TABLE_NAME
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME IN ('orders', 'order_items', 'announcements', 'audit_logs', 'payment_verifications');

-- ==========================================
-- PERFORMANCE QUERIES
-- ==========================================

-- Check Index Usage
SHOW INDEX FROM orders;
SHOW INDEX FROM order_items;
SHOW INDEX FROM users;
SHOW INDEX FROM products;

-- Table Sizes
SELECT 
    TABLE_NAME,
    ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) as size_mb,
    TABLE_ROWS
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE()
ORDER BY (DATA_LENGTH + INDEX_LENGTH) DESC;

-- Slow Queries Identification (requires slow query log enabled)
-- Enable: SET GLOBAL slow_query_log = 'ON';
-- Review: /var/log/mysql/slow-query.log

-- ==========================================
-- DATA CLEANUP (USE WITH CAUTION)
-- ==========================================

-- Clear old audit logs (older than 90 days)
-- DELETE FROM audit_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY);

-- Clear old announcements (ended more than 30 days ago)
-- DELETE FROM announcements 
-- WHERE active = 0 
-- AND end_time < DATE_SUB(NOW(), INTERVAL 30 DAY);

-- ==========================================
-- BACKUP & EXPORT
-- ==========================================

-- Export sales data for specific date range
-- Run from command line:
-- mysql -u root -p -D hasheem -e "SELECT * FROM orders WHERE created_at BETWEEN '2026-01-01' AND '2026-12-31'" > sales_2026.csv

-- Full database backup
-- mysqldump -u root -p hasheem > backup_$(date +%Y%m%d).sql

-- ==========================================
-- NOTES
-- ==========================================
-- 
-- 1. All revenue queries filter by o.status = 'approved'
-- 2. Game products: p.product_type = 'game'
-- 3. Physical products: p.product_type = 'accessory'
-- 4. Date functions use MySQL syntax (CURDATE(), NOW(), etc.)
-- 5. Performance: Use EXPLAIN before complex queries
-- 6. Always backup before DELETE/UPDATE operations
-- 
-- ==========================================
