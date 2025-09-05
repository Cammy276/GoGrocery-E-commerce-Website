-- GoGrocery MySQL 8.0 schema
-- Engine: InnoDB, Charset: utf8mb4, Collation: utf8mb4_unicode_ci
-- Safe to run as a single script.

/* 0) One-time server/session recommendations (run once per server) */
-- SET GLOBAL time_zone = '+08:00';                                         -- time_zone: server timestamps match MYT (UTC+8).
-- SET PERSIST sql_mode = 'STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION';     -- sql_mode: stricter data checks (avoid silent truncation)
-- SET PERSIST innodb_strict_mode = 1;                                      -- innodb_strict_mode: tightens InnoDB consistency checks

/* 1) Create database */
-- Creates the DB with full Unicode support (emojis, multilingual)
CREATE DATABASE IF NOT EXISTS gogrocery
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- Selects the DB for all objects that follow
USE gogrocery;

/* 2) Users & addresses */
CREATE TABLE IF NOT EXISTS users (
  user_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  profile_image_url VARCHAR(500) NULL DEFAULT '/images/users/default.png'
    COMMENT 'Path or URL to user profile image',
  password_hash VARBINARY(255) NOT NULL COMMENT 'bcrypt/argon2id hash – NEVER plaintext',
  reset_token_hash VARCHAR(64),  
  reset_token_expires_at DATETIME, 
<<<<<<< HEAD
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_users_email (email),
  UNIQUE KEY uq_users_phone (phone)
=======
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
>>>>>>> d7c8e83c5d70af304d0d336edbdd4c62e8f22acc
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* 3) Product Brands */
CREATE TABLE IF NOT EXISTS brands (
  brand_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  slug VARCHAR(140) GENERATED ALWAYS AS (
  REGEXP_REPLACE(LOWER(name), '[^a-z0-9]+', '_')
  ) STORED,
  UNIQUE KEY uq_brand_name (name),
  UNIQUE KEY uq_brand_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*
REPLACE(LOWER(name), ' ', '-'):
1) LOWER(name) → converts all letters in the brand name to lowercase.
2) REPLACE(..., '[^a-z0-9]+', '_') → replaces any characters that is NOT a-z or 0-9 with underscore.

Slug: clean, human-readable, and SEO-friendly identifier derived from a name, often used in URLs instead of numeric IDs
Brand Name: Go Grocery
Slug: go-grocery
Product URL:
https://example.com/brands/go-grocery/products
*/

/* 4) Product Categories */
CREATE TABLE IF NOT EXISTS categories (
  category_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  parent_id INT UNSIGNED NULL,
  slug VARCHAR(140) GENERATED ALWAYS AS (
  REGEXP_REPLACE(LOWER(name), '[^a-z0-9]+', '_')
  ) STORED,
  UNIQUE KEY uq_cat_name (name),
  UNIQUE KEY uq_cat_slug (slug),
  CONSTRAINT fk_categories_parent
    FOREIGN KEY (parent_id) REFERENCES categories(category_id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*
ON DELETE SET NULL
→ If a parent category is deleted, all its subcategories’ parent_id will be set to NULL.

ON UPDATE CASCADE
→ If a parent category’s category_id changes, the parent_id of its subcategories updates automatically.

UNIQUE KEY uq_cat_name (name)
Ensures that no two rows in the categories table can have the same name -> throw a duplicate entry error

UNIQUE KEY uq_cat_slug (slug)
Ensures that no two rows can have the same slug (the URL-friendly version of the name).
*/

/* 5) Products */
CREATE TABLE IF NOT EXISTS products (
  product_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  sku VARCHAR(64) NOT NULL,
  product_name VARCHAR(255) NOT NULL,
  brand_id INT UNSIGNED NULL,
  category_id INT UNSIGNED NULL,
  weight_volume VARCHAR(50) NULL,                       -- e.g., "500 g" or "1 L"
  unit_price DECIMAL(10,2) NOT NULL,
  product_description TEXT NULL,
  nutritional_info TEXT NULL,                           
  discount_percent DECIMAL(5,2) NULL,                   
  special_offer_label VARCHAR(120) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
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

/* 6) Product images (recommended: store URLs/paths, not BLOBs) */
CREATE TABLE IF NOT EXISTS product_images (
  image_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id INT UNSIGNED NOT NULL,
  image_url VARCHAR(500) NOT NULL,        
  alt_text VARCHAR(255) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  -- Each product can have only 1 image
  UNIQUE KEY uq_prod_images_product (product_id),
  KEY idx_prod_images_prod (product_id),
  -- Link image to product
  CONSTRAINT fk_prod_images_product
    FOREIGN KEY (product_id) REFERENCES products(product_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* 7) Addresses */
CREATE TABLE IF NOT EXISTS addresses (
  address_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
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

/* 8) Shipping Rates */
CREATE TABLE IF NOT EXISTS shipping_rates (
  rate_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  postcode VARCHAR(10) NOT NULL,
  shipping_fee DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  delivery_duration VARCHAR(50) NOT NULL,  -- e.g., "2-3 days"
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_shipping_postcode (postcode)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* 9) Wishlists */
CREATE TABLE IF NOT EXISTS wishlists (
  user_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  -- Show "Recently added to your wishlist" items (optional)
  -- Allow users to sort their wishlist by date (optional)
  PRIMARY KEY (user_id, product_id),
  CONSTRAINT fk_wishlist_user
    FOREIGN KEY (user_id) REFERENCES users(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_wishlist_product
    FOREIGN KEY (product_id) REFERENCES products(product_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* 10) Orders */
/* 10) Orders */
CREATE TABLE IF NOT EXISTS orders (
<<<<<<< HEAD
  order_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  address_id INT UNSIGNED NOT NULL,                          -- required for delivery
  status ENUM('paid','delivered') NOT NULL DEFAULT 'paid'
    /*
    paid: Order has been placed and paid by the customer, but not yet confirmed received.
    delivered: Customer has clicked “Received,” confirming they got the order.
    */
  ,
  payment_method ENUM('card','bank_transfer','e_wallet','grabpay','fpx') NOT NULL,
=======
  order_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  address_id BIGINT UNSIGNED NULL,
  status ENUM('paid','delivered') NOT NULL DEFAULT 'paid',
  -- user places (and pays) the order → status = paid
  -- user later clicks “Received” → status = delivered
  payment_method ENUM('card','bank_transfer', 'e_wallet','grabpay','fpx') NOT NULL,
>>>>>>> d7c8e83c5d70af304d0d336edbdd4c62e8f22acc
  subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  discount_total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  shipping_fee DECIMAL(10,2) NOT NULL DEFAULT 0.00,           -- fetched from shipping_rates
  delivery_duration VARCHAR(50) NOT NULL,                           -- fetched from shipping_rates
  tax_total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  grand_total DECIMAL(10,2) AS (subtotal - discount_total + shipping_fee + tax_total) STORED,
  placed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_orders_user (user_id),
  CONSTRAINT fk_orders_user
    FOREIGN KEY (user_id) REFERENCES users(user_id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_orders_address
    FOREIGN KEY (address_id) REFERENCES addresses(address_id)
    ON DELETE RESTRICT ON UPDATE CASCADE        -- prevent deletion of used addresses
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*
Why order_items exists?
- An order can contain multiple products (e.g., 2 shirts + 1 pair of shoes).
- The orders table holds only the overall order details (user, totals, status).
- The order_items table stores the list of products inside that order.
- 1-to-many relationship: 1 order → many order_items.
*/

/* 11) Order Items */
CREATE TABLE IF NOT EXISTS order_items (
  order_item_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  product_name VARCHAR(255) NOT NULL,       
  sku VARCHAR(64) NOT NULL,                 
  unit_price DECIMAL(10,2) NOT NULL,
  quantity INT UNSIGNED NOT NULL CHECK (quantity > 0),
  line_discount DECIMAL(10,2) NOT NULL DEFAULT 0.00,    -- per-product promotions
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

/* 12) Contact form */
CREATE TABLE IF NOT EXISTS contact_messages (
  message_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NULL,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(255) NOT NULL,
  phone VARCHAR(20) NULL,
  subject VARCHAR(255) NOT NULL,
  comment TEXT NOT NULL,
  image_url VARCHAR(500) NULL,   
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_contact_user (user_id),
  CONSTRAINT fk_contact_user
    FOREIGN KEY (user_id) REFERENCES users(user_id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;