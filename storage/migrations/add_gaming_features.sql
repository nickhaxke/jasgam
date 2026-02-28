-- Migration: Add dynamic gaming features
-- Run via /admin/migrations

-- New columns on products table
ALTER TABLE products ADD COLUMN cover_image VARCHAR(500) NULL AFTER image_url;
ALTER TABLE products ADD COLUMN preview_video_url VARCHAR(500) NULL AFTER cover_image;
ALTER TABLE products ADD COLUMN trailer_video_url VARCHAR(500) NULL AFTER preview_video_url;
ALTER TABLE products ADD COLUMN file_size VARCHAR(100) NULL AFTER trailer_video_url;
ALTER TABLE products ADD COLUMN is_featured TINYINT(1) NOT NULL DEFAULT 0 AFTER file_size;
ALTER TABLE products ADD COLUMN is_trending TINYINT(1) NOT NULL DEFAULT 0 AFTER is_featured;
ALTER TABLE products ADD COLUMN download_count INT UNSIGNED NOT NULL DEFAULT 0 AFTER is_trending;

-- Performance indexes
ALTER TABLE products ADD INDEX idx_is_featured (is_featured);
ALTER TABLE products ADD INDEX idx_is_trending (is_trending);
ALTER TABLE products ADD INDEX idx_download_count (download_count);

-- Screenshots / additional images table
CREATE TABLE IF NOT EXISTS product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    image_url VARCHAR(500) NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_product_images_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
