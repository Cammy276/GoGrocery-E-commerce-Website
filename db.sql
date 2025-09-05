-- GoGrocery MySQL 8.0 schema
-- Engine: InnoDB, Charset: utf8mb4, Collation: utf8mb4_unicode_ci
-- Safe to run as a single script.

/* 0) One-time server/session recommendations (run once per server) */
-- SET GLOBAL time_zone = '+08:00';                                         -- time_zone: server timestamps match MYT (UTC+8).
-- SET PERSIST sql_mode = 'STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION';     -- sql_mode: stricter data checks (avoid silent truncation)
-- SET PERSIST innodb_strict_mode = 1;                                      -- innodb_strict_mode: tightens InnoDB consistency checks

/* 1) Create database + app user (adjust passwords in production) */
-- Creates the DB with full Unicode support (emojis, multilingual)
CREATE DATABASE IF NOT EXISTS gogrocery
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- Selects the DB for all objects that follow
USE gogrocery;

/* 2) Reference tables */
CREATE TABLE userauthentication (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    email VARCHAR(200) UNIQUE NOT NULL,
    phone_number VARCHAR(15) UNIQUE NOT NULL,  
    password_hash VARCHAR(200) NOT NULL,  
    reset_token_hash VARCHAR(64),  
    reset_token_expires_at DATETIME,  
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS brands (
  brand_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  slug VARCHAR(140) GENERATED ALWAYS AS (REPLACE(LOWER(name), ' ', '-')) STORED,
  UNIQUE KEY uq_brand_name (name),
  UNIQUE KEY uq_brand_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS categories (
  category_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  parent_id INT UNSIGNED NULL,
  slug VARCHAR(140) GENERATED ALWAYS AS (REPLACE(LOWER(name), ' ', '-')) STORED,
  UNIQUE KEY uq_cat_name (name),
  UNIQUE KEY uq_cat_slug (slug),
  CONSTRAINT fk_categories_parent
    FOREIGN KEY (parent_id) REFERENCES categories(category_id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* 3) Users & addresses */
CREATE TABLE IF NOT EXISTS users (
  user_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(255) NOT NULL,
  password_hash VARBINARY(80) NOT NULL COMMENT 'bcrypt/argon2id hash – NEVER plaintext',
  phone VARCHAR(20),
  is_active BOOLEAN NOT NULL DEFAULT TRUE,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_users_email (email),
  UNIQUE KEY uq_users_phone (phone)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS addresses (
  address_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  label VARCHAR(50) NOT NULL,                          -- e.g., "Home", "Office"
  street VARCHAR(255) NOT NULL,
  apartment VARCHAR(255) NULL,                          -- apartment/condo/garden
  postcode VARCHAR(10) NOT NULL,
  city VARCHAR(100) NOT NULL,
  state_territory VARCHAR(100) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_addresses_user
    FOREIGN KEY (user_id) REFERENCES users(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* 4) Products */
CREATE TABLE IF NOT EXISTS products (
  product_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  sku VARCHAR(64) NOT NULL,
  product_name VARCHAR(255) NOT NULL,
  brand_id INT UNSIGNED NULL,
  category_id INT UNSIGNED NULL,
  weight_volume VARCHAR(50) NULL,                       -- e.g., "500 g" or "1 L"
  unit_price DECIMAL(10,2) NOT NULL,
  product_description TEXT NULL,
  nutritional_info JSON NULL,                           -- store as JSON (optional)
  discount_percent DECIMAL(5,2) NULL,                   -- 0-100
  is_new_arrival BOOLEAN NOT NULL DEFAULT FALSE,
  special_offer_label VARCHAR(120) NULL,
  total_person_bought INT UNSIGNED NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_products_sku (sku),
  KEY idx_products_category (category_id),
  KEY idx_products_brand (brand_id),
  CONSTRAINT fk_products_brand
    FOREIGN KEY (brand_id) REFERENCES brands(brand_id)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT fk_products_category
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Optional: Fulltext search */
CREATE FULLTEXT INDEX ft_products_name_desc
ON products (product_name, product_description);

/* 5) Product images (recommended: store URLs/paths, not BLOBs) */
CREATE TABLE IF NOT EXISTS product_images (
  image_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id BIGINT UNSIGNED NOT NULL,
  image_url VARCHAR(500) NOT NULL,                      -- e.g., CDN/S3 URL or relative path
  alt_text VARCHAR(255) NULL,
  sort_order INT UNSIGNED NOT NULL DEFAULT 1,
  is_primary BOOLEAN NOT NULL DEFAULT FALSE,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_prod_images_prod (product_id),
  KEY idx_prod_images_primary (product_id, is_primary),
  UNIQUE KEY uq_prod_images_sort (product_id, sort_order),
  CONSTRAINT fk_prod_images_product
    FOREIGN KEY (product_id) REFERENCES products(product_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CHECK (sort_order >= 1)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Alternative (less recommended): store image bytes in DB */
CREATE TABLE IF NOT EXISTS product_image_blobs (
  blob_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id BIGINT UNSIGNED NOT NULL,
  mime_type VARCHAR(50) NOT NULL,
  filename VARCHAR(255) NULL,
  file_bytes LONGBLOB NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_blob_product (product_id),
  CONSTRAINT fk_blob_product
    FOREIGN KEY (product_id) REFERENCES products(product_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* 6) Wishlists */
CREATE TABLE IF NOT EXISTS wishlists (
  user_id BIGINT UNSIGNED NOT NULL,
  product_id BIGINT UNSIGNED NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, product_id),
  CONSTRAINT fk_wishlist_user
    FOREIGN KEY (user_id) REFERENCES users(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_wishlist_product
    FOREIGN KEY (product_id) REFERENCES products(product_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* 7) Ratings & reviews */
CREATE TABLE IF NOT EXISTS product_ratings (
  rating_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  stars TINYINT UNSIGNED NOT NULL CHECK (stars BETWEEN 1 AND 5),
  comment TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_rating_user_product (product_id, user_id),
  KEY idx_ratings_product (product_id),
  KEY idx_ratings_user (user_id),
  CONSTRAINT fk_ratings_product
    FOREIGN KEY (product_id) REFERENCES products(product_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_ratings_user
    FOREIGN KEY (user_id) REFERENCES users(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Derived/aggregated view for product ratings
DROP VIEW IF EXISTS product_rating_stats;
CREATE VIEW product_rating_stats AS
SELECT
  p.product_id,
  COALESCE(ROUND(AVG(r.stars), 2), 0) AS avg_rating,
  COUNT(r.rating_id) AS rating_count
FROM products p
LEFT JOIN product_ratings r ON r.product_id = p.product_id
GROUP BY p.product_id;

/* 8) Delivery slots (optional but useful for groceries) */
CREATE TABLE IF NOT EXISTS delivery_slots (
  slot_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  window_start DATETIME NOT NULL,
  window_end DATETIME NOT NULL,
  capacity INT UNSIGNED NOT NULL DEFAULT 20,
  UNIQUE KEY uq_slot_window (window_start, window_end),
  CHECK (window_end > window_start),
  CHECK (capacity >= 1)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* 9) Orders */
CREATE TABLE IF NOT EXISTS orders (
  order_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  address_id BIGINT UNSIGNED NULL,                -- if you keep a reference to saved address
  slot_id INT UNSIGNED NULL,                      -- delivery slot reference
  status ENUM('pending','paid','packed','shipped','delivered','cancelled','refunded')
         NOT NULL DEFAULT 'pending',
  payment_method ENUM('card','bank_transfer','cod','e_wallet','fp_x','grabpay')
         NOT NULL,
  subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  discount_total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  shipping_fee DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  tax_total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  grand_total DECIMAL(10,2) AS (subtotal - discount_total + shipping_fee + tax_total) STORED,
  placed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_orders_user (user_id),
  KEY idx_orders_status (status),
  KEY idx_orders_slot (slot_id),
  CONSTRAINT fk_orders_user
    FOREIGN KEY (user_id) REFERENCES users(user_id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_orders_address
    FOREIGN KEY (address_id) REFERENCES addresses(address_id)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT fk_orders_slot
    FOREIGN KEY (slot_id) REFERENCES delivery_slots(slot_id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS order_items (
  order_item_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id BIGINT UNSIGNED NOT NULL,
  product_id BIGINT UNSIGNED NOT NULL,
  product_name VARCHAR(255) NOT NULL,       -- snapshot at time of order
  sku VARCHAR(64) NOT NULL,                 -- snapshot at time of order
  unit_price DECIMAL(10,2) NOT NULL,
  quantity INT UNSIGNED NOT NULL CHECK (quantity > 0),
  line_discount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  line_total DECIMAL(10,2) AS (unit_price * quantity - line_discount) STORED,
  KEY idx_order_items_order (order_id),
  KEY idx_order_items_product (product_id),
  CONSTRAINT fk_order_items_order
    FOREIGN KEY (order_id) REFERENCES orders(order_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_order_items_product
    FOREIGN KEY (product_id) REFERENCES products(product_id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Optional: inventory */
CREATE TABLE IF NOT EXISTS inventory (
  product_id BIGINT UNSIGNED PRIMARY KEY,
  qty_in_stock INT NOT NULL DEFAULT 0,
  restock_at DATETIME NULL,
  CONSTRAINT fk_inventory_product
    FOREIGN KEY (product_id) REFERENCES products(product_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* 10) Payments (basic) */
CREATE TABLE IF NOT EXISTS payments (
  payment_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id BIGINT UNSIGNED NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  provider VARCHAR(50) NULL,                 -- e.g., "Stripe", "ToyyibPay", etc.
  provider_ref VARCHAR(100) NULL,
  status ENUM('initiated','authorized','captured','failed','refunded') NOT NULL,
  paid_at DATETIME NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_payments_order (order_id),
  CONSTRAINT fk_payments_order
    FOREIGN KEY (order_id) REFERENCES orders(order_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* 11) Contact form (user optional) */
CREATE TABLE IF NOT EXISTS contact_messages (
  message_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NULL,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(255) NOT NULL,
  phone VARCHAR(20) NULL,
  subject VARCHAR(255) NOT NULL,
  comment TEXT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_contact_user (user_id),
  CONSTRAINT fk_contact_user
    FOREIGN KEY (user_id) REFERENCES users(user_id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* 12) Useful sample data */
INSERT INTO categories (name) VALUES
  ('Fresh Produce'), ('Chilled & Frozen'), ('Food Essentials & Commodities'),
  ('Snacks'), ('Beverages'), ('Household Products'), ('Beauty & Health')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO brands (name) VALUES ('Generic'), ('Local Farm'), ('GoGrocery')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO products (sku, product_name, brand_id, category_id, weight_volume, unit_price, product_description, is_new_arrival)
VALUES
  ('APL-RED-500G', 'Red Apples', 2, 1, '500 g', 5.90, 'Crisp and sweet red apples.', TRUE),
  ('MLK-FRESH-1L', 'Fresh Milk', 1, 2, '1 L', 6.50, 'Pasteurized whole milk.', FALSE)
ON DUPLICATE KEY UPDATE product_name = VALUES(product_name), unit_price = VALUES(unit_price);

INSERT INTO product_images (product_id, image_url, alt_text, sort_order, is_primary)
SELECT p.product_id, '/images/products/' || p.sku || '_1.jpg', CONCAT(p.product_name, ' image'), 1, TRUE
FROM products p
WHERE NOT EXISTS (
  SELECT 1 FROM product_images i WHERE i.product_id = p.product_id AND i.sort_order = 1
);

/* 13) Example queries */

-- Get product with images + rating stats
-- SELECT p.*, s.avg_rating, s.rating_count, i.image_url
-- FROM products p
-- LEFT JOIN product_rating_stats s ON s.product_id = p.product_id
-- LEFT JOIN product_images i ON i.product_id = p.product_id AND i.is_primary = TRUE
-- WHERE p.category_id = 1
-- ORDER BY p.created_at DESC;

-- Add to wishlist
-- INSERT INTO wishlists (user_id, product_id) VALUES (123, 456);

-- Place an order (transactional example)
-- START TRANSACTION;
-- INSERT INTO orders (user_id, address_id, slot_id, payment_method, subtotal, discount_total, shipping_fee, tax_total)
-- VALUES (123, 10, 1, 'card', 12.40, 0.00, 5.00, 0.00);
-- SET @order_id = LAST_INSERT_ID();
-- INSERT INTO order_items (order_id, product_id, product_name, sku, unit_price, quantity, line_discount)
-- SELECT @order_id, p.product_id, p.product_name, p.sku, p.unit_price, 2, 0.00
-- FROM products p WHERE p.product_id = 456;
-- UPDATE inventory SET qty_in_stock = qty_in_stock - 2 WHERE product_id = 456;
-- COMMIT;
