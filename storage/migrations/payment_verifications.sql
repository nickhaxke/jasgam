-- Payment Verifications Table Migration
-- Created: 2026-02-14

CREATE TABLE IF NOT EXISTS payment_verifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    amount INT NOT NULL COMMENT 'Amount in TZS',
    access_level ENUM('mobile', 'pc', 'full') NOT NULL,
    payment_method VARCHAR(100) NOT NULL COMMENT 'mpesa, bank, vodacom',
    mpesa_phone VARCHAR(20) COMMENT 'M-Pesa phone number used',
    screenshot_path VARCHAR(500) NOT NULL COMMENT 'Path to payment screenshot',
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    approval_notes TEXT,
    approved_by INT COMMENT 'Admin user ID who approved',
    approved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    KEY idx_user_id (user_id),
    KEY idx_product_id (product_id),
    KEY idx_status (status),
    KEY idx_created_at (created_at),
    KEY idx_user_product (user_id, product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
