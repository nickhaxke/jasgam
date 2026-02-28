-- Migration: Add payment system fields
-- Date: 2024

-- ALTER orders table to include delivery address fields
ALTER TABLE orders ADD COLUMN IF NOT EXISTS first_name VARCHAR(100) AFTER user_id;
ALTER TABLE orders ADD COLUMN IF NOT EXISTS last_name VARCHAR(100) AFTER first_name;
ALTER TABLE orders ADD COLUMN IF NOT EXISTS email VARCHAR(100) AFTER last_name;
ALTER TABLE orders ADD COLUMN IF NOT EXISTS phone VARCHAR(20) AFTER email;
ALTER TABLE orders ADD COLUMN IF NOT EXISTS street VARCHAR(255) AFTER phone;
ALTER TABLE orders ADD COLUMN IF NOT EXISTS city VARCHAR(100) AFTER street;
ALTER TABLE orders ADD COLUMN IF NOT EXISTS state VARCHAR(100) AFTER city;
ALTER TABLE orders ADD COLUMN IF NOT EXISTS zip VARCHAR(20) AFTER state;
ALTER TABLE orders ADD COLUMN IF NOT EXISTS country VARCHAR(100) AFTER zip;

-- Update orders table status field
ALTER TABLE orders MODIFY COLUMN status VARCHAR(50) DEFAULT 'pending_payment_verification' NOT NULL;

-- Update payment_verifications table - add new columns
ALTER TABLE payment_verifications ADD COLUMN IF NOT EXISTS order_item_id INT UNSIGNED NULL;
ALTER TABLE payment_verifications ADD COLUMN IF NOT EXISTS screenshot_path VARCHAR(255) NULL;
ALTER TABLE payment_verifications ADD COLUMN IF NOT EXISTS rejected_by INT UNSIGNED NULL;
ALTER TABLE payment_verifications ADD COLUMN IF NOT EXISTS rejected_at DATETIME NULL;
ALTER TABLE payment_verifications ADD COLUMN IF NOT EXISTS rejection_reason TEXT NULL;

-- Modify status column
ALTER TABLE payment_verifications MODIFY COLUMN status VARCHAR(50) DEFAULT 'pending_admin_review' NOT NULL;

-- Add indexes for faster queries
ALTER TABLE payment_verifications ADD INDEX IF NOT EXISTS idx_status (status);
ALTER TABLE payment_verifications ADD INDEX IF NOT EXISTS idx_user_id (user_id);
ALTER TABLE orders ADD INDEX IF NOT EXISTS idx_user_status (user_id, status);
