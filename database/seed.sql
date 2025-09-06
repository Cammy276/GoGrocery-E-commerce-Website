-- ========================
-- GoGrocery Full Seed (Development)
-- ========================

/* 1) Users */
-- password_hash: bcrypt hash for development/testing
-- Plain passwords (for dev use only):

INSERT INTO users (name, email, phone, profile_image_url, password_hash) VALUES
('Alice', 'alice@example.com', '+60123456789', '/images/users/default.png', 
 '$2y$10$wH9N3P4hXnJ9Qz6M5tB.uOepJb5g6VhT8oPpZyTqW0aR8F6lT3Yy'),  -- Password123!
('Bob', 'bob@example.com', '+60198765432', '/images/users/default.png', 
 '$2y$10$7vH0Qw4XlR9yPq2D5tF3uOedKb6g9GfR7jPqZyTqW0bA5E6lU1Zy'),  -- Secret456!
('Charlie', 'charlie@example.com', '+60112233445', '/images/users/default.png', 
 '$2y$10$9fG4Vq8KxJ0bQs5M2nF8vOe9Lb3h7WjPqZyTqW1cX0bH6L3oT2By');  -- MyPass789!

/* 2) Product Brands */
INSERT INTO brands (name) VALUES
('FarmFresh'), ('VeggieWorld'), ('GreenLeaf'), ('NatureBite'), ('FruitCo'),
('FreshFields'), ('FarmPride'), ('MeatMasters'), ('Butcher''s Choice'), ('BlueWave'),
('OceanCatch'), ('FreshCo'), ('MooFresh'), ('CreamyLand'), ('DairyPure'), 
('ChillCo'), ('PantryCo'), ('SnackCo'), ('CrunchyBite'), ('HappySnacks'), 
('DrinkUp'), ('SipCo'), ('CleanCo'), ('Sparkle'), ('HomeCare'),
('GlowWell'), ('CarePlus'), ('BeautyGen')

/* 3) Product Categories */
-- ================================
-- Level 1 Categories
-- ================================
INSERT INTO categories (name, parent_id) VALUES
('Fresh Produces', NULL),
('Chilled & Frozen', NULL),
('Food Essentials & Commodities', NULL),
('Snacks', NULL),
('Beverages', NULL),
('Household Products', NULL),
('Beauty & Health', NULL);

-- ================================
-- Level 2 Categories
-- ================================
-- Fresh Produces
INSERT INTO categories (name, parent_id) VALUES
('Vegetables', (SELECT category_id FROM categories WHERE name='Fresh Produces')),
('Fruits', (SELECT category_id FROM categories WHERE name='Fresh Produces')),
('Meat & Poultry', (SELECT category_id FROM categories WHERE name='Fresh Produces')),
('Seafood', (SELECT category_id FROM categories WHERE name='Fresh Produces')),
('Egg', (SELECT category_id FROM categories WHERE name='Fresh Produces'));

-- Chilled & Frozen
INSERT INTO categories (name, parent_id) VALUES
('Dairy Produces', (SELECT category_id FROM categories WHERE name='Chilled & Frozen')),
('Chilled Drinks', (SELECT category_id FROM categories WHERE name='Chilled & Frozen')),
('Frozen Food', (SELECT category_id FROM categories WHERE name='Chilled & Frozen'));

-- Food Essentials & Commodities
INSERT INTO categories (name, parent_id) VALUES
('Pasta & Instant Food', (SELECT category_id FROM categories WHERE name='Food Essentials & Commodities')),
('Canned Food', (SELECT category_id FROM categories WHERE name='Food Essentials & Commodities')),
('Rice', (SELECT category_id FROM categories WHERE name='Food Essentials & Commodities'));

-- Snacks
INSERT INTO categories (name, parent_id) VALUES
('Biscuits & Cookies', (SELECT category_id FROM categories WHERE name='Snacks')),
('Chocolates', (SELECT category_id FROM categories WHERE name='Snacks')),
('Candies & Sweets', (SELECT category_id FROM categories WHERE name='Snacks')),
('Chips & Crisps', (SELECT category_id FROM categories WHERE name='Snacks'));

-- Beverages
INSERT INTO categories (name, parent_id) VALUES
('Carbonated Drinks', (SELECT category_id FROM categories WHERE name='Beverages')),
('Coffee', (SELECT category_id FROM categories WHERE name='Beverages')),
('Water', (SELECT category_id FROM categories WHERE name='Beverages'));

-- Household Products
INSERT INTO categories (name, parent_id) VALUES
('Paper Products', (SELECT category_id FROM categories WHERE name='Household Products')),
('Laundry', (SELECT category_id FROM categories WHERE name='Household Products')),
('Home Cleaning Accessories', (SELECT category_id FROM categories WHERE name='Household Products'));

-- Beauty & Health
INSERT INTO categories (name, parent_id) VALUES
('Body Care', (SELECT category_id FROM categories WHERE name='Beauty & Health')),
('Hair Care', (SELECT category_id FROM categories WHERE name='Beauty & Health')),
('Oral Care', (SELECT category_id FROM categories WHERE name='Beauty & Health'));

-- ================================
-- Level 3 Categories
-- ================================
-- Fresh Produces -> Vegetables
INSERT INTO categories (name, parent_id) VALUES
('Asparagus & Shoots', (SELECT category_id FROM categories WHERE name='Vegetables')),
('Beans & Peas', (SELECT category_id FROM categories WHERE name='Vegetables')),
('Broccoli & Cabbages', (SELECT category_id FROM categories WHERE name='Vegetables')),
('Carrots & Potatoes', (SELECT category_id FROM categories WHERE name='Vegetables')),
('Cucumbers & Squash', (SELECT category_id FROM categories WHERE name='Vegetables')),
('Chili & Capsicum', (SELECT category_id FROM categories WHERE name='Vegetables')),
('Herbs', (SELECT category_id FROM categories WHERE name='Vegetables')),
('Leafy Vegetables', (SELECT category_id FROM categories WHERE name='Vegetables')),
('Mushrooms & Fungi', (SELECT category_id FROM categories WHERE name='Vegetables')),
('Onions & Garlic', (SELECT category_id FROM categories WHERE name='Vegetables'));

-- Fresh Produces -> Fruits
INSERT INTO categories (name, parent_id) VALUES
('Apples', (SELECT category_id FROM categories WHERE name='Fruits')),
('Berries & Grapes', (SELECT category_id FROM categories WHERE name='Fruits')),
('Citrus & Oranges', (SELECT category_id FROM categories WHERE name='Fruits')),
('Melons', (SELECT category_id FROM categories WHERE name='Fruits')),
('Pears', (SELECT category_id FROM categories WHERE name='Fruits')),
('Stone Fruits', (SELECT category_id FROM categories WHERE name='Fruits')),
('Tropical Fruits', (SELECT category_id FROM categories WHERE name='Fruits'));

-- Fresh Produces -> Meat & Poultry
INSERT INTO categories (name, parent_id) VALUES
('Chicken', (SELECT category_id FROM categories WHERE name='Meat & Poultry')),
('Pork', (SELECT category_id FROM categories WHERE name='Meat & Poultry')),
('Beef', (SELECT category_id FROM categories WHERE name='Meat & Poultry')),
('Lamb', (SELECT category_id FROM categories WHERE name='Meat & Poultry'));

-- Fresh Produces -> Seafood
INSERT INTO categories (name, parent_id) VALUES
('Fish', (SELECT category_id FROM categories WHERE name='Seafood')),
('Prawn', (SELECT category_id FROM categories WHERE name='Seafood')),
('Crab', (SELECT category_id FROM categories WHERE name='Seafood')),
('Squid', (SELECT category_id FROM categories WHERE name='Seafood')),
('Clam', (SELECT category_id FROM categories WHERE name='Seafood'));

-- Chilled & Frozen -> Dairy Produces
INSERT INTO categories (name, parent_id) VALUES
('Milk', (SELECT category_id FROM categories WHERE name='Dairy Produces')),
('Yogurt', (SELECT category_id FROM categories WHERE name='Dairy Produces')),
('Cheese', (SELECT category_id FROM categories WHERE name='Dairy Produces')),
('Butter & Margarine', (SELECT category_id FROM categories WHERE name='Dairy Produces')),
('Cream', (SELECT category_id FROM categories WHERE name='Dairy Produces'));

-- Chilled & Frozen -> Chilled Drinks
INSERT INTO categories (name, parent_id) VALUES
('Juice', (SELECT category_id FROM categories WHERE name='Chilled Drinks')),
('Yogurt Drink', (SELECT category_id FROM categories WHERE name='Chilled Drinks')),
('Soy Milk', (SELECT category_id FROM categories WHERE name='Chilled Drinks'));

-- Chilled & Frozen -> Frozen Food
INSERT INTO categories (name, parent_id) VALUES
('Frozen Meals', (SELECT category_id FROM categories WHERE name='Frozen Food')),
('Frozen Meat & Poultry', (SELECT category_id FROM categories WHERE name='Frozen Food')),
('Frozen Puff & Pastry', (SELECT category_id FROM categories WHERE name='Frozen Food')),
('Frozen Seafood', (SELECT category_id FROM categories WHERE name='Frozen Food')),
('Frozen Vegetables', (SELECT category_id FROM categories WHERE name='Frozen Food')),
('Frozen Fruits', (SELECT category_id FROM categories WHERE name='Frozen Food'));

-- Food Essentials & Commodities -> Pasta & Instant Food
INSERT INTO categories (name, parent_id) VALUES
('Instant Noodles', (SELECT category_id FROM categories WHERE name='Pasta & Instant Food')),
('Pasta', (SELECT category_id FROM categories WHERE name='Pasta & Instant Food')),
('Instant Soups', (SELECT category_id FROM categories WHERE name='Pasta & Instant Food')),
('Instant Porridge & Rice', (SELECT category_id FROM categories WHERE name='Pasta & Instant Food'));

-- Food Essentials & Commodities -> Canned Food
INSERT INTO categories (name, parent_id) VALUES
('Canned Vegetables', (SELECT category_id FROM categories WHERE name='Canned Food')),
('Canned Fruits', (SELECT category_id FROM categories WHERE name='Canned Food')),
('Canned Meat', (SELECT category_id FROM categories WHERE name='Canned Food')),
('Canned Seafood', (SELECT category_id FROM categories WHERE name='Canned Food'));

-- Food Essentials & Commodities -> Rice
INSERT INTO categories (name, parent_id) VALUES
('Brown Rice', (SELECT category_id FROM categories WHERE name='Rice')),
('Glutinous Rice', (SELECT category_id FROM categories WHERE name='Rice')),
('White Rice', (SELECT category_id FROM categories WHERE name='Rice'));

-- Snacks -> Chips & Crisps
INSERT INTO categories (name, parent_id) VALUES
('Potato & Corn Chips', (SELECT category_id FROM categories WHERE name='Chips & Crisps')),
('Canister Snacks', (SELECT category_id FROM categories WHERE name='Chips & Crisps')),
('Ring & Twisted Snacks', (SELECT category_id FROM categories WHERE name='Chips & Crisps'));

-- Beverages -> Coffee
INSERT INTO categories (name, parent_id) VALUES
('Beans & Ground', (SELECT category_id FROM categories WHERE name='Coffee')),
('Instant Coffee', (SELECT category_id FROM categories WHERE name='Coffee')),
('Ready-to-Drink Coffee', (SELECT category_id FROM categories WHERE name='Coffee'));

-- Household Products -> Paper Products
INSERT INTO categories (name, parent_id) VALUES
('Bathroom Roll', (SELECT category_id FROM categories WHERE name='Paper Products')),
('Facial Tissue', (SELECT category_id FROM categories WHERE name='Paper Products')),
('Kitchen Roll', (SELECT category_id FROM categories WHERE name='Paper Products')),
('Pocket Tissue', (SELECT category_id FROM categories WHERE name='Paper Products')),
('Wipes', (SELECT category_id FROM categories WHERE name='Paper Products'));

-- Household Products -> Laundry
INSERT INTO categories (name, parent_id) VALUES
('Bleach', (SELECT category_id FROM categories WHERE name='Laundry')),
('Softener & Delicate Care', (SELECT category_id FROM categories WHERE name='Laundry')),
('Detergent', (SELECT category_id FROM categories WHERE name='Laundry')),
('Laundry Accessories', (SELECT category_id FROM categories WHERE name='Laundry'));

-- Household Products -> Home Cleaning Accessories
INSERT INTO categories (name, parent_id) VALUES
('Sponge & Scourer', (SELECT category_id FROM categories WHERE name='Home Cleaning Accessories')),
('Mops', (SELECT category_id FROM categories WHERE name='Home Cleaning Accessories')),
('Brooms & Dust Scoop', (SELECT category_id FROM categories WHERE name='Home Cleaning Accessories')),
('Gloves', (SELECT category_id FROM categories WHERE name='Home Cleaning Accessories')),
('Pail & Water Dipper', (SELECT category_id FROM categories WHERE name='Home Cleaning Accessories')),
('Duster & Cleaning Cloth', (SELECT category_id FROM categories WHERE name='Home Cleaning Accessories'));

-- Beauty & Health -> Body Care
INSERT INTO categories (name, parent_id) VALUES
('Soaps, Scrubs & Gels', (SELECT category_id FROM categories WHERE name='Body Care')),
('Body Lotion & Cream', (SELECT category_id FROM categories WHERE name='Body Care')),
('Deo & Fragrances', (SELECT category_id FROM categories WHERE name='Body Care')),
('Hand & Foot Care', (SELECT category_id FROM categories WHERE name='Body Care')),
('Skincare', (SELECT category_id FROM categories WHERE name='Body Care')); 

-- Beauty & Health -> Hair Care
INSERT INTO categories (name, parent_id) VALUES
('Conditioner', (SELECT category_id FROM categories WHERE name='Hair Care')),
('Shampoo', (SELECT category_id FROM categories WHERE name='Hair Care')),
('Hair Styling', (SELECT category_id FROM categories WHERE name='Hair Care')),
('Hair Color & Treatments', (SELECT category_id FROM categories WHERE name='Hair Care')),
('Hair & Scalp Treatments', (SELECT category_id FROM categories WHERE name='Hair Care')); 

-- Beauty & Health -> Oral Care
INSERT INTO categories (name, parent_id) VALUES
('Mouth wash', (SELECT category_id FROM categories WHERE name='Oral Care')),
('Toothbrush & Accessories', (SELECT category_id FROM categories WHERE name='Oral Care')),
('Toothpaste', (SELECT category_id FROM categories WHERE name='Oral Care'));

/* 5) Products */
INSERT INTO products (sku, product_name, brand_id, category_id, unit_price, product_description, discount_percent, special_offer_label) VALUES
('SKU001', 'Apple', 1, 1, 2.50, 'Fresh red apples, 500g pack', 0, NULL),
('SKU002', 'Orange', 2, 1, 3.00, 'Juicy oranges, 1kg bag', 10, 'Buy 2 get 1 free'),
('SKU003', 'Carrot', 2, 2, 1.50, 'Fresh carrots, 500g', 0, NULL),
('SKU004', 'Orange Juice', 3, 3, 5.00, 'Freshly squeezed orange juice, 1L', 5, 'Special Offer'),
('SKU005', 'Potato Chips', 1, 4, 2.20, 'Crispy salted potato chips, 200g', 0, NULL);
-- ------------------------
-- 4) Product Images
-- ------------------------
INSERT INTO product_images (product_id, image_url, alt_text) VALUES
(1, '/images/products/apple.jpg', 'Fresh Red Apples'),
(2, '/images/products/orange.jpg', 'Juicy Oranges'),
(3, '/images/products/carrot.jpg', 'Fresh Carrots'),
(4, '/images/products/orange_juice.jpg', 'Orange Juice 1L'),
(5, '/images/products/potato_chips.jpg', 'Potato Chips 200g');


-- ------------------------
-- 6) Addresses
-- ------------------------
INSERT INTO addresses (user_id, label, street, postcode, city, state_territory) VALUES
(1, 'Home', '123 Apple St', '50000', 'Kuala Lumpur', 'WP Kuala Lumpur'),
(1, 'Office', '456 Banana Ave', '50010', 'Kuala Lumpur', 'WP Kuala Lumpur'),
(2, 'Home', '789 Orange Rd', '60000', 'Johor Bahru', 'Johor'),
(3, 'Home', '321 Carrot Lane', '70000', 'Penang', 'Penang');

-- ------------------------
-- 7) Orders
-- ------------------------
INSERT INTO orders (user_id, address_id, status, payment_method, subtotal, discount_total, shipping_fee, tax_total) VALUES
(1, 1, 'paid', 'card', 10.00, 1.00, 2.50, 0.75),
(2, 3, 'paid', 'e_wallet', 5.00, 0.50, 2.00, 0.25),
(3, 4, 'paid', 'bank_transfer', 15.00, 0.00, 3.00, 1.50);

-- ------------------------
-- 8) Order Items
-- ------------------------
INSERT INTO order_items (order_id, product_id, product_name, sku, unit_price, quantity, line_discount) VALUES
(1, 1, 'Apple', 'SKU001', 2.50, 2, 0.00),
(1, 2, 'Orange', 'SKU002', 3.00, 2, 1.00),
(2, 3, 'Carrot', 'SKU003', 1.50, 2, 0.50),
(3, 4, 'Orange Juice', 'SKU004', 5.00, 3, 0.00),
(3, 5, 'Potato Chips', 'SKU005', 2.20, 2, 0.00);

-- ------------------------
-- 9) Wishlists
-- ------------------------
INSERT INTO wishlists (user_id, product_id) VALUES
(1, 3),
(1, 4),
(2, 1),
(3, 2),
(3, 5);

-- ------------------------
-- 10) Contact Messages
-- ------------------------
INSERT INTO contact_messages (user_id, name, email, subject, comment, image_url) VALUES
(1, 'Alice', 'alice@example.com', 'Inquiry', 'I have a question about product SKU001', NULL),
(2, 'Bob', 'bob@example.com', 'Feedback', 'Great website!', NULL),
(3, 'Charlie', 'charlie@example.com', 'Complaint', 'Product arrived damaged', '/images/contact/damaged_item.jpg');
