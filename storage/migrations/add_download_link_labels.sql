-- Add download_link_labels column to products table
-- This column stores JSON array of labels for each download link
-- Format: ["Label 1", "Label 2", "Label 3"]

ALTER TABLE products 
ADD COLUMN download_link_labels JSON DEFAULT NULL AFTER download_links;
