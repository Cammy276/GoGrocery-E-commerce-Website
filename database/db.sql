-- GoGrocery MySQL 8.0 schema
-- Engine: InnoDB, Charset: utf8mb4, Collation: utf8mb4_unicode_ci
-- Safe to run as a single script.

/* Create database */
-- Creates the DB with full Unicode support (emojis, multilingual)
CREATE DATABASE IF NOT EXISTS gogrocery
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- Selects the DB for all objects that follow
USE gogrocery;

/* Users */
CREATE TABLE IF NOT EXISTS users (
  user_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  profile_image_url VARCHAR(500) NULL DEFAULT '/images/users/default.png'
    COMMENT 'Path or URL to user profile image',
  password_hash VARBINARY(255) NOT NULL COMMENT 'bcrypt/argon2id hash – NEVER plaintext',
  reset_token_hash VARCHAR(64),  
  reset_token_expires_at DATETIME, 
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_users_email (email),
  UNIQUE KEY uq_users_phone (phone)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Addresses */
CREATE TABLE IF NOT EXISTS addresses (
  address_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  label VARCHAR(50) NOT NULL,                          -- e.g., "Home", "Office"
  street VARCHAR(255) NOT NULL,
  apartment VARCHAR(255) NULL,                          -- apartment/condo/garden
  postcode VARCHAR(10) NOT NULL,
  city VARCHAR(100) NOT NULL,
  state_territory ENUM(
    'Johor','Kedah','Kelantan','Malacca','Negeri Sembilan','Pahang','Penang',
    'Perak','Perlis','Sabah','Sarawak','Selangor','Terengganu',
    'WP Kuala Lumpur','WP Labuan','WP Putrajaya'
  ) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_addresses_user
    FOREIGN KEY (user_id) REFERENCES users(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Product Brands */
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

/* Product Categories */
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

/* Products */
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

/* Product images */
CREATE TABLE IF NOT EXISTS product_images (
  image_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id INT UNSIGNED NOT NULL,
  product_image_url VARCHAR(500) NOT NULL,        
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

/* Wishlist */
CREATE TABLE IF NOT EXISTS wishlist (
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

/* Vouchers */
CREATE TABLE IF NOT EXISTS vouchers (
  voucher_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(50) NOT NULL UNIQUE,          -- e.g., "WELCOME10"
  description VARCHAR(255) NOT NULL,
  voucher_image_url VARCHAR(500) NOT NULL, 
  terms_conditions VARCHAR(255) NOT NULL,
  discount_type ENUM('PERCENT', 'FIXED') NOT NULL,  -- % or RM discount
  discount_value DECIMAL(10,2) NOT NULL,     -- e.g., 10.00 = 10% if PERCENT, or RM10 if FIXED
  min_order_amount DECIMAL(10,2) DEFAULT 0.00,      -- optional min spend
  max_discount DECIMAL(10,2) NULL,           -- cap for % discount
  usage_limit INT UNSIGNED DEFAULT NULL,     -- total times the voucher can be used (across all users)
  per_user_limit INT UNSIGNED DEFAULT 1,     -- how many times one user can use it
  start_date DATETIME NOT NULL,
  end_date DATETIME NOT NULL,
  is_active BOOLEAN NOT NULL DEFAULT TRUE,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Orders */
CREATE TABLE IF NOT EXISTS orders (
  order_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  address_id INT UNSIGNED NOT NULL,
  status ENUM('paid','delivered') NOT NULL DEFAULT 'paid',  -- always paid because only paid orders are saved into this table
  payment_method ENUM('card','bank_transfer','e_wallet','grabpay','fpx') NOT NULL,
  voucher_id INT UNSIGNED NULL,                     
  subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  voucher_discount_value DECIMAL(10,2) NOT NULL DEFAULT 0.00, -- subtotal * discount_value (of voucher)
  shipping_fee DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  delivery_duration VARCHAR(50) NOT NULL,
  grand_total DECIMAL(10,2) AS (subtotal - voucher_discount_value + shipping_fee) STORED,
  placed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_orders_user (user_id),
  CONSTRAINT fk_orders_user
    FOREIGN KEY (user_id) REFERENCES users(user_id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_orders_address
    FOREIGN KEY (address_id) REFERENCES addresses(address_id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_orders_voucher
    FOREIGN KEY (voucher_id) REFERENCES vouchers(voucher_id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*
Why order_items exists?
- An order can contain multiple products (e.g., 2 shirts + 1 pair of shoes).
- The orders table holds only the overall order details (user, totals, status).
- The order_items table stores the list of products inside that order.
- 1-to-many relationship: 1 order → many order_items.
*/

/* Order Items */
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

/* Cart Items */ 
-- 1 user only can have 1 cart which stores all items pending to checkout
-- only those selected items will bring to the checkout (orders + orders_item)
-- display the total prices (line total) for each product (display the total for 3 units of Brocolli) 
-- display the total prices for all products selected (you need to retrieve the line total for each product 
-- & add them, I can't add the aggregated total for all cart items in database)
CREATE TABLE IF NOT EXISTS cart_items (
  cart_item_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  product_name VARCHAR(255) NOT NULL,     
  sku VARCHAR(64) NOT NULL,                
  unit_price DECIMAL(10,2) NOT NULL,       
  quantity INT UNSIGNED NOT NULL CHECK (quantity > 0),
  line_discount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  added_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_cart_user (user_id),
  KEY idx_cart_product (product_id),
  CONSTRAINT fk_cart_items_user
    FOREIGN KEY (user_id) REFERENCES users(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_cart_items_product
    FOREIGN KEY (product_id) REFERENCES products(product_id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  UNIQUE KEY uq_cart_user_product (user_id, product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Voucher Usages */
CREATE TABLE IF NOT EXISTS voucher_usages (
  usage_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  voucher_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  order_id INT UNSIGNED NOT NULL,
  used_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_usage_voucher FOREIGN KEY (voucher_id) REFERENCES vouchers(voucher_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_usage_user FOREIGN KEY (user_id) REFERENCES users(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_usage_order FOREIGN KEY (order_id) REFERENCES orders(order_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Contact Form */
CREATE TABLE IF NOT EXISTS contact_messages (
  message_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NULL,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(255) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  subject VARCHAR(255) NOT NULL,
  comment VARCHAR(500) NOT NULL,
  contact_image_url VARCHAR(500) NULL,   
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_contact_user (user_id),
  CONSTRAINT fk_contact_user
    FOREIGN KEY (user_id) REFERENCES users(user_id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;