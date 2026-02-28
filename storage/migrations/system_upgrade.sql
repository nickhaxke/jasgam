-- ============================================
-- SYSTEM UPGRADE MIGRATION
-- Adds missing columns and tables for production
-- ============================================

-- 1. Add category column to orders table
ALTER TABLE orders ADD COLUMN category VARCHAR(20) DEFAULT 'product' COMMENT 'game or product';

-- 2. Add product_type to order_items (critical for revenue tracking)
ALTER TABLE order_items ADD COLUMN product_type VARCHAR(20) DEFAULT 'product' COMMENT 'game or product';

-- 3. Add status column to users if not exists (for blocking)
ALTER TABLE users ADD COLUMN status VARCHAR(20) DEFAULT 'active' COMMENT 'active, blocked, suspended';

-- 3.1 Add game-specific fields to products
ALTER TABLE products ADD COLUMN download_links JSON NULL COMMENT 'JSON array of download links for the game';
ALTER TABLE products ADD COLUMN tutorial_video_link VARCHAR(500) NULL COMMENT 'URL to tutorial/how-to-play video';

-- 4. Update existing order_items with product_type from products table
UPDATE order_items oi
INNER JOIN products p ON oi.product_id = p.id
SET oi.product_type = p.product_type
WHERE oi.product_type IS NULL OR oi.product_type = 'product';

-- 5. Update orders category based on order_items
UPDATE orders o
SET o.category = (
    SELECT CASE 
        WHEN MAX(CASE WHEN oi.product_type = 'game' THEN 1 ELSE 0 END) = 1 THEN 'game'
        ELSE 'product'
    END
    FROM order_items oi
    WHERE oi.order_id = o.id
)
WHERE o.category IS NULL OR o.category = 'product';

-- 6. Create announcements table
CREATE TABLE IF NOT EXISTS announcements (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    active TINYINT(1) DEFAULT 0,
    auto_close_seconds INT DEFAULT 30,
    start_time DATETIME NULL,
    end_time DATETIME NULL,
    priority INT DEFAULT 0 COMMENT 'Higher priority shows first',
    target_users VARCHAR(20) DEFAULT 'all' COMMENT 'all, users, admins',
    created_by INT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_active (active, start_time, end_time),
    INDEX idx_priority (priority DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Update payment_verifications to include status column if missing
ALTER TABLE payment_verifications ADD COLUMN status VARCHAR(50) DEFAULT 'pending';

-- 8. Add indexes for performance
ALTER TABLE orders ADD INDEX idx_category (category);
ALTER TABLE orders ADD INDEX idx_status (status);
ALTER TABLE order_items ADD INDEX idx_product_type (product_type);
ALTER TABLE users ADD INDEX idx_status (status);
ALTER TABLE users ADD INDEX idx_blocked (blocked);
ALTER TABLE payment_verifications ADD INDEX idx_status (status);

-- 9. Ensure audit_logs has proper structure (already in schema.sql but adding for safety)
CREATE TABLE IF NOT EXISTS audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    action VARCHAR(64) NOT NULL,
    target_type VARCHAR(64) NULL,
    target_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    details TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_action (user_id, action),
    INDEX idx_created (created_at DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Add view_count to announcements for analytics
ALTER TABLE announcements ADD COLUMN view_count INT UNSIGNED DEFAULT 0;
ALTER TABLE announcements ADD COLUMN dismiss_count INT UNSIGNED DEFAULT 0;

-- ============================================
-- DATA INTEGRITY CHECKS
-- ============================================

-- Ensure all orders have proper status values
UPDATE orders SET status = 'pending' WHERE status IS NULL OR status = '';

-- Ensure all users have proper role values
UPDATE users SET role = 'user' WHERE role IS NULL OR role = '';

-- Convert legacy blocked field to status field
UPDATE users SET status = 'blocked' WHERE blocked = 1 AND (status = 'active' OR status IS NULL);
UPDATE users SET status = 'active' WHERE blocked = 0 AND (status IS NULL OR status = '');

-- ============================================
-- COMPLETED
-- ============================================
