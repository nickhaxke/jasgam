-- Migration: Add game-specific fields to products table
-- This migration adds download_links and tutorial_video_link columns to support game management

ALTER TABLE products 
ADD COLUMN download_links JSON NULL COMMENT 'JSON array of download links for the game';

ALTER TABLE products 
ADD COLUMN tutorial_video_link VARCHAR(500) NULL COMMENT 'URL to tutorial/how-to-play video';

-- Add index for performance
ALTER TABLE products 
ADD INDEX idx_product_type (product_type);
ALTER TABLE products 
ADD INDEX idx_is_active (is_active);
