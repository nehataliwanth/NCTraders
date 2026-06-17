-- Migration script for existing NC Traders database
-- Rename products table columns to use normalized product_* names.

ALTER TABLE products
  CHANGE COLUMN description product_description TEXT,
  CHANGE COLUMN price product_price DECIMAL(10, 2) NOT NULL,
  CHANGE COLUMN image_url product_image VARCHAR(255);
