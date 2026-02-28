-- Create ad_unlocks table for ad-based game access
CREATE TABLE IF NOT EXISTS ad_unlocks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    token VARCHAR(80) NOT NULL UNIQUE,
    download_link TEXT NOT NULL,
    ip_address VARCHAR(64) NULL,
    user_agent VARCHAR(255) NULL,
    created_at DATETIME NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    INDEX idx_ad_unlocks_product (product_id),
    INDEX idx_ad_unlocks_expires (expires_at)
);
