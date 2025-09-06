-- ========================
-- GoGrocery Full Seed (Development)
-- ========================

/* Users */
-- password_hash: bcrypt hash for development/testing
-- Plain passwords (for dev use only):
/*
1) Alice (user_id=1)
2) Bob (user_id=2)
*/
INSERT INTO users (name, email, phone, profile_image_url, password_hash) VALUES
('Alice', 'alice@example.com', '+60123456789', '/images/users/default.png', 
 '$2y$10$wH9N3P4hXnJ9Qz6M5tB.uOepJb5g6VhT8oPpZyTqW0aR8F6lT3Yy'),  -- Password123!
('Bob', 'bob@example.com', '+60198765432', '/images/users/default.png', 
 '$2y$10$7vH0Qw4XlR9yPq2D5tF3uOedKb6g9GfR7jPqZyTqW0bA5E6lU1Zy'),  -- Secret456!

/* Addresses */
INSERT INTO addresses (user_id, label, street, postcode, city, state_territory) VALUES
(1, 'Home', '123 Apple St', '43200', 'Kuala Lumpur', 'WP Kuala Lumpur'),
(1, 'Office', '456 Banana Ave', '50010', 'Kuala Lumpur', 'WP Kuala Lumpur'),
(2, 'Home', '789 Orange Rd', '79000', 'Johor Bahru', 'Johor');

/* Product Brands */
INSERT INTO brands (name) VALUES
('FarmFresh'), ('VeggieWorld'), ('GreenLeaf'), ('NatureBite'), ('FruitCo'),
('FreshFields'), ('FarmPride'), ('MeatMasters'), ('Butcher''s Choice'), ('BlueWave'),
('OceanCatch'), ('FreshCo'), ('MooFresh'), ('CreamyLand'), ('DairyPure'), 
('ChillCo'), ('PantryCo'), ('SnackCo'), ('CrunchyBite'), ('HappySnacks'), 
('DrinkUp'), ('SipCo'), ('CleanCo'), ('Sparkle'), ('HomeCare'),
('GlowWell'), ('CarePlus'), ('BeautyGen')

/* Product Categories */
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

/* Products */
INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FP-VX-AS-001',
      'Green Asparagus Bunch',
      (SELECT brand_id FROM brands WHERE name='FarmFresh'),
      (SELECT category_id FROM categories WHERE name='Asparagus & Shoots'),
      '500g',
      8.9,
      'The Green Asparagus Bunch from FarmFresh comes in a 500g pack and is part of our fresh produces – vegetables – asparagus & shoots. Freshly harvested and carefully selected, this green asparagus bunch delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 100g: Energy 50 kcal, Carbohydrates 12g, Sugars 9g, Fiber 2g, Vitamin C 10% RDI',
      25.0,
      'Limited Time Deal',
      '2025-08-30 10:15:10'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FP-VX-BP-002',
      'Sugar Snap Peas',
      (SELECT brand_id FROM brands WHERE name='VeggieWorld'),
      (SELECT category_id FROM categories WHERE name='Beans & Peas'),
      '500g',
      8.9,
      'The Sugar Snap Peas from VeggieWorld comes in a 500g pack and is part of our fresh produces – vegetables – beans & peas. Freshly harvested and carefully selected, this sugar snap peas delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 100g: Energy 50 kcal, Carbohydrates 12g, Sugars 9g, Fiber 2g, Vitamin C 10% RDI',
      NULL,
      NULL,
      '2025-08-17 11:12:14'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FP-VX-BC-003',
      'Broccoli Crown',
      (SELECT brand_id FROM brands WHERE name='VeggieWorld'),
      (SELECT category_id FROM categories WHERE name='Broccoli & Cabbages'),
      '500g',
      8.9,
      'The Broccoli Crown from VeggieWorld comes in a 500g pack and is part of our fresh produces – vegetables – broccoli & cabbages. Freshly harvested and carefully selected, this broccoli crown delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 100g: Energy 50 kcal, Carbohydrates 12g, Sugars 9g, Fiber 2g, Vitamin C 10% RDI',
      NULL,
      NULL,
      '2025-08-04 02:43:55'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FP-VX-CP-004',
      'Baby Carrots',
      (SELECT brand_id FROM brands WHERE name='VeggieWorld'),
      (SELECT category_id FROM categories WHERE name='Carrots & Potatoes'),
      '500g',
      8.9,
      'The Baby Carrots from VeggieWorld comes in a 500g pack and is part of our fresh produces – vegetables – carrots & potatoes. Freshly harvested and carefully selected, this baby carrots delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 100g: Energy 50 kcal, Carbohydrates 12g, Sugars 9g, Fiber 2g, Vitamin C 10% RDI',
      NULL,
      NULL,
      '2025-08-04 02:52:49'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FP-VX-CS-005',
      'Japanese Cucumber',
      (SELECT brand_id FROM brands WHERE name='GreenLeaf'),
      (SELECT category_id FROM categories WHERE name='Cucumbers & Squash'),
      '500g',
      8.9,
      'The Japanese Cucumber from GreenLeaf comes in a 500g pack and is part of our fresh produces – vegetables – cucumbers & squash. Freshly harvested and carefully selected, this japanese cucumber delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 100g: Energy 50 kcal, Carbohydrates 12g, Sugars 9g, Fiber 2g, Vitamin C 10% RDI',
      NULL,
      'Bundle Pack (2 for 1)',
      '2025-08-25 12:27:03'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FP-VX-CC-006',
      'Red Capsicum',
      (SELECT brand_id FROM brands WHERE name='VeggieWorld'),
      (SELECT category_id FROM categories WHERE name='Chili & Capsicum'),
      '500g',
      8.9,
      'The Red Capsicum from VeggieWorld comes in a 500g pack and is part of our fresh produces – vegetables – chili & capsicum. Freshly harvested and carefully selected, this red capsicum delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 100g: Energy 50 kcal, Carbohydrates 12g, Sugars 9g, Fiber 2g, Vitamin C 10% RDI',
      NULL,
      NULL,
      '2025-08-26 05:50:03'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FP-VX-HX-007',
      'Fresh Basil',
      (SELECT brand_id FROM brands WHERE name='VeggieWorld'),
      (SELECT category_id FROM categories WHERE name='Herbs'),
      '500g',
      8.9,
      'The Fresh Basil from VeggieWorld comes in a 500g pack and is part of our fresh produces – vegetables – herbs. Freshly harvested and carefully selected, this fresh basil delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 100g: Energy 50 kcal, Carbohydrates 12g, Sugars 9g, Fiber 2g, Vitamin C 10% RDI',
      NULL,
      'Free Gift with Purchase',
      '2025-08-22 07:41:45'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FP-VX-LV-008',
      'Romaine Lettuce',
      (SELECT brand_id FROM brands WHERE name='VeggieWorld'),
      (SELECT category_id FROM categories WHERE name='Leafy Vegetables'),
      '500g',
      8.9,
      'The Romaine Lettuce from VeggieWorld comes in a 500g pack and is part of our fresh produces – vegetables – leafy vegetables. Freshly harvested and carefully selected, this romaine lettuce delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 100g: Energy 50 kcal, Carbohydrates 12g, Sugars 9g, Fiber 2g, Vitamin C 10% RDI',
      NULL,
      NULL,
      '2025-08-03 21:38:00'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FP-VX-MF-009',
      'Brown Button Mushrooms',
      (SELECT brand_id FROM brands WHERE name='VeggieWorld'),
      (SELECT category_id FROM categories WHERE name='Mushrooms & Fungi'),
      '500g',
      8.9,
      'The Brown Button Mushrooms from VeggieWorld comes in a 500g pack and is part of our fresh produces – vegetables – mushrooms & fungi. Freshly harvested and carefully selected, this brown button mushrooms delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 100g: Energy 50 kcal, Carbohydrates 12g, Sugars 9g, Fiber 2g, Vitamin C 10% RDI',
      5.0,
      NULL,
      '2025-08-12 05:55:53'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FP-VX-OG-010',
      'Yellow Onions',
      (SELECT brand_id FROM brands WHERE name='VeggieWorld'),
      (SELECT category_id FROM categories WHERE name='Onions & Garlic'),
      '500g',
      8.9,
      'The Yellow Onions from VeggieWorld comes in a 500g pack and is part of our fresh produces – vegetables – onions & garlic. Freshly harvested and carefully selected, this yellow onions delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 100g: Energy 50 kcal, Carbohydrates 12g, Sugars 9g, Fiber 2g, Vitamin C 10% RDI',
      25.0,
      NULL,
      '2025-08-23 13:07:11'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FP-FX-AX-011',
      'Crisp Green Apples',
      (SELECT brand_id FROM brands WHERE name='NatureBite'),
      (SELECT category_id FROM categories WHERE name='Apples'),
      '1kg',
      8.9,
      'The Crisp Green Apples from NatureBite comes in a 1kg pack and is part of our fresh produces – fruits – apples. Freshly harvested and carefully selected, this crisp green apples delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 100g: Energy 50 kcal, Carbohydrates 12g, Sugars 9g, Fiber 2g, Vitamin C 10% RDI',
      NULL,
      NULL,
      '2025-08-27 04:57:30'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FP-FX-BG-012',
      'Seedless Red Grapes',
      (SELECT brand_id FROM brands WHERE name='FruitCo'),
      (SELECT category_id FROM categories WHERE name='Berries & Grapes'),
      '1kg',
      8.9,
      'The Seedless Red Grapes from FruitCo comes in a 1kg pack and is part of our fresh produces – fruits – berries & grapes. Freshly harvested and carefully selected, this seedless red grapes delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 100g: Energy 50 kcal, Carbohydrates 12g, Sugars 9g, Fiber 2g, Vitamin C 10% RDI',
      NULL,
      NULL,
      '2025-08-26 12:23:15'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FP-FX-CO-013',
      'Sweet Navel Oranges',
      (SELECT brand_id FROM brands WHERE name='NatureBite'),
      (SELECT category_id FROM categories WHERE name='Citrus & Oranges'),
      '1kg',
      8.9,
      'The Sweet Navel Oranges from NatureBite comes in a 1kg pack and is part of our fresh produces – fruits – citrus & oranges. Freshly harvested and carefully selected, this sweet navel oranges delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 100g: Energy 50 kcal, Carbohydrates 12g, Sugars 9g, Fiber 2g, Vitamin C 10% RDI',
      NULL,
      NULL,
      '2025-08-19 06:14:37'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FP-FX-MX-014',
      'Honeydew Melon',
      (SELECT brand_id FROM brands WHERE name='FreshFields'),
      (SELECT category_id FROM categories WHERE name='Melons'),
      '1kg',
      8.9,
      'The Honeydew Melon from FreshFields comes in a 1kg pack and is part of our fresh produces – fruits – melons. Freshly harvested and carefully selected, this honeydew melon delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 100g: Energy 50 kcal, Carbohydrates 12g, Sugars 9g, Fiber 2g, Vitamin C 10% RDI',
      5.0,
      NULL,
      '2025-08-15 10:57:07'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FP-FX-PX-015',
      'Juicy Packham Pears',
      (SELECT brand_id FROM brands WHERE name='NatureBite'),
      (SELECT category_id FROM categories WHERE name='Pears'),
      '1kg',
      8.9,
      'The Juicy Packham Pears from NatureBite comes in a 1kg pack and is part of our fresh produces – fruits – pears. Freshly harvested and carefully selected, this juicy packham pears delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 100g: Energy 50 kcal, Carbohydrates 12g, Sugars 9g, Fiber 2g, Vitamin C 10% RDI',
      NULL,
      NULL,
      '2025-08-29 03:58:30'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FP-FX-SF-016',
      'Yellow Peaches',
      (SELECT brand_id FROM brands WHERE name='FruitCo'),
      (SELECT category_id FROM categories WHERE name='Stone Fruits'),
      '1kg',
      8.9,
      'The Yellow Peaches from FruitCo comes in a 1kg pack and is part of our fresh produces – fruits – stone fruits. Freshly harvested and carefully selected, this yellow peaches delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 100g: Energy 50 kcal, Carbohydrates 12g, Sugars 9g, Fiber 2g, Vitamin C 10% RDI',
      NULL,
      NULL,
      '2025-08-29 13:02:21'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FP-FX-TF-017',
      'Ripe Mangos',
      (SELECT brand_id FROM brands WHERE name='FreshFields'),
      (SELECT category_id FROM categories WHERE name='Tropical Fruits'),
      '1kg',
      8.9,
      'The Ripe Mangos from FreshFields comes in a 1kg pack and is part of our fresh produces – fruits – tropical fruits. Freshly harvested and carefully selected, this ripe mangos delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 100g: Energy 50 kcal, Carbohydrates 12g, Sugars 9g, Fiber 2g, Vitamin C 10% RDI',
      NULL,
      NULL,
      '2025-08-21 02:40:14'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FP-MP-CX-018',
      'Chicken Breast Fillet',
      (SELECT brand_id FROM brands WHERE name='FarmPride'),
      (SELECT category_id FROM categories WHERE name='Chicken'),
      '1kg',
      14.9,
      'The Chicken Breast Fillet from FarmPride comes in a 1kg pack and is part of our fresh produces – meat & poultry – chicken. Freshly harvested and carefully selected, this chicken breast fillet delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 100g: Energy 165 kcal, Protein 20g, Fat 9g',
      NULL,
      NULL,
      '2025-08-27 01:55:05'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FP-MP-PX-019',
      'Pork Loin Slices',
      (SELECT brand_id FROM brands WHERE name='MeatMasters'),
      (SELECT category_id FROM categories WHERE name='Pork'),
      '1kg',
      14.9,
      'The Pork Loin Slices from MeatMasters comes in a 1kg pack and is part of our fresh produces – meat & poultry – pork. Freshly harvested and carefully selected, this pork loin slices delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 100g: Energy 165 kcal, Protein 20g, Fat 9g',
      NULL,
      NULL,
      '2025-08-31 00:07:33'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FP-MP-BX-020',
      'Beef Sirloin',
      (SELECT brand_id FROM brands WHERE name='MeatMasters'),
      (SELECT category_id FROM categories WHERE name='Beef'),
      '1kg',
      14.9,
      'The Beef Sirloin from MeatMasters comes in a 1kg pack and is part of our fresh produces – meat & poultry – beef. Freshly harvested and carefully selected, this beef sirloin delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 100g: Energy 165 kcal, Protein 20g, Fat 9g',
      NULL,
      NULL,
      '2025-08-31 14:31:23'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FP-MP-LX-021',
      'Lamb Shoulder Chops',
      (SELECT brand_id FROM brands WHERE name='Butcher''s Choice'),
      (SELECT category_id FROM categories WHERE name='Lamb'),
      '1kg',
      14.9,
      'The Lamb Shoulder Chops from Butcher''s Choice comes in a 1kg pack and is part of our fresh produces – meat & poultry – lamb. Freshly harvested and carefully selected, this lamb shoulder chops delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 100g: Energy 165 kcal, Protein 20g, Fat 9g',
      NULL,
      NULL,
      '2025-08-19 04:25:30'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FP-SX-FX-022',
      'Salmon Fillet',
      (SELECT brand_id FROM brands WHERE name='BlueWave'),
      (SELECT category_id FROM categories WHERE name='Fish'),
      '500g',
      28.9,
      'The Salmon Fillet from BlueWave comes in a 500g pack and is part of our fresh produces – seafood – fish. Freshly harvested and carefully selected, this salmon fillet delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 100g: Energy 120 kcal, Protein 22g, Fat 3g, Omega-3 0.8g',
      NULL,
      NULL,
      '2025-08-30 11:31:06'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FP-SX-PX-023',
      'Large Tiger Prawns',
      (SELECT brand_id FROM brands WHERE name='OceanCatch'),
      (SELECT category_id FROM categories WHERE name='Prawn'),
      '500g',
      19.9,
      'The Large Tiger Prawns from OceanCatch comes in a 500g pack and is part of our fresh produces – seafood – prawn. Freshly harvested and carefully selected, this large tiger prawns delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 100g: Energy 120 kcal, Protein 22g, Fat 3g, Omega-3 0.8g',
      NULL,
      NULL,
      '2025-08-10 03:07:20'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FP-SX-CX-024',
      'Cleaned Mud Crab',
      (SELECT brand_id FROM brands WHERE name='BlueWave'),
      (SELECT category_id FROM categories WHERE name='Crab'),
      '500g',
      19.9,
      'The Cleaned Mud Crab from BlueWave comes in a 500g pack and is part of our fresh produces – seafood – crab. Freshly harvested and carefully selected, this cleaned mud crab delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 100g: Energy 120 kcal, Protein 22g, Fat 3g, Omega-3 0.8g',
      NULL,
      'Bundle Pack (2 for 1)',
      '2025-08-18 00:25:50'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FP-SX-SX-025',
      'Cleaned Squid Rings',
      (SELECT brand_id FROM brands WHERE name='OceanCatch'),
      (SELECT category_id FROM categories WHERE name='Squid'),
      '500g',
      19.9,
      'The Cleaned Squid Rings from OceanCatch comes in a 500g pack and is part of our fresh produces – seafood – squid. Freshly harvested and carefully selected, this cleaned squid rings delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 100g: Energy 120 kcal, Protein 22g, Fat 3g, Omega-3 0.8g',
      NULL,
      NULL,
      '2025-08-10 01:23:12'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FP-SX-CX-026',
      'Live Clams',
      (SELECT brand_id FROM brands WHERE name='OceanCatch'),
      (SELECT category_id FROM categories WHERE name='Clam'),
      '500g',
      19.9,
      'The Live Clams from OceanCatch comes in a 500g pack and is part of our fresh produces – seafood – clam. Freshly harvested and carefully selected, this live clams delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 100g: Energy 120 kcal, Protein 22g, Fat 3g, Omega-3 0.8g',
      NULL,
      'Buy 1 Free 1',
      '2025-08-28 08:59:21'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FP-EX-EX-027',
      'Farm Eggs (Grade A)',
      (SELECT brand_id FROM brands WHERE name='FreshCo'),
      (SELECT category_id FROM categories WHERE name='Egg'),
      '10 eggs',
      8.5,
      'The Farm Eggs (Grade A) from FreshCo comes in a 10 eggs pack and is part of our fresh produces – egg – egg. Freshly harvested and carefully selected, this farm eggs (grade a) delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per egg (60g): Energy 68 kcal, Protein 6g, Fat 5g',
      NULL,
      NULL,
      '2025-08-04 20:42:38'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'CF-DP-MX-028',
      'Full Cream Milk',
      (SELECT brand_id FROM brands WHERE name='MooFresh'),
      (SELECT category_id FROM categories WHERE name='Milk'),
      '1L',
      6.5,
      'The Full Cream Milk from MooFresh comes in a 1L pack and is part of our chilled & frozen – dairy produces – milk. Freshly harvested and carefully selected, this full cream milk delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 100ml: Energy 64 kcal, Protein 3.4g, Fat 3.5g, Carbs 4.8g, Calcium 120mg',
      NULL,
      NULL,
      '2025-08-21 15:07:38'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'CF-DP-YX-029',
      'Greek-Style Yogurt',
      (SELECT brand_id FROM brands WHERE name='MooFresh'),
      (SELECT category_id FROM categories WHERE name='Yogurt'),
      '500g',
      9.9,
      'The Greek-Style Yogurt from MooFresh comes in a 500g pack and is part of our chilled & frozen – dairy produces – yogurt. Freshly harvested and carefully selected, this greek-style yogurt delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 100g: Energy 60 kcal, Protein 5g, Fat 3g, Carbs 4g',
      NULL,
      NULL,
      '2025-08-12 14:27:27'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'CF-DP-CX-030',
      'Mild Cheddar Cheese',
      (SELECT brand_id FROM brands WHERE name='CreamyLand'),
      (SELECT category_id FROM categories WHERE name='Cheese'),
      '200g',
      12.9,
      'The Mild Cheddar Cheese from CreamyLand comes in a 200g pack and is part of our chilled & frozen – dairy produces – cheese. Freshly harvested and carefully selected, this mild cheddar cheese delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 30g: Energy 120 kcal, Protein 7g, Fat 10g',
      NULL,
      NULL,
      '2025-08-03 23:26:31'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'CF-DP-BM-031',
      'Creamy Salted Butter',
      (SELECT brand_id FROM brands WHERE name='DairyPure'),
      (SELECT category_id FROM categories WHERE name='Butter & Margarine'),
      '250g',
      8.9,
      'The Creamy Salted Butter from DairyPure comes in a 250g pack and is part of our chilled & frozen – dairy produces – butter & margarine. Freshly harvested and carefully selected, this creamy salted butter delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 10g: Energy 72 kcal, Fat 8g',
      NULL,
      NULL,
      '2025-08-26 08:42:48'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'CF-DP-CX-032',
      'Cooking Cream',
      (SELECT brand_id FROM brands WHERE name='CreamyLand'),
      (SELECT category_id FROM categories WHERE name='Cream'),
      '250g',
      8.9,
      'The Cooking Cream from CreamyLand comes in a 250g pack and is part of our chilled & frozen – dairy produces – cream. Freshly harvested and carefully selected, this cooking cream delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 30ml: Energy 100 kcal, Fat 10g',
      NULL,
      NULL,
      '2025-08-22 20:15:31'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'CF-CD-JX-033',
      'Apple Juice',
      (SELECT brand_id FROM brands WHERE name='ChillCo'),
      (SELECT category_id FROM categories WHERE name='Juice'),
      '1L',
      7.9,
      'The Apple Juice from ChillCo comes in a 1L pack and is part of our chilled & frozen – chilled drinks – juice. Freshly harvested and carefully selected, this apple juice delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 250ml: Energy 110 kcal, Sugars 24g',
      NULL,
      NULL,
      '2025-09-02 07:02:28'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'CF-CD-YD-034',
      'Strawberry Yogurt Drink',
      (SELECT brand_id FROM brands WHERE name='ChillCo'),
      (SELECT category_id FROM categories WHERE name='Yogurt Drink'),
      '1L',
      7.9,
      'The Strawberry Yogurt Drink from ChillCo comes in a 1L pack and is part of our chilled & frozen – chilled drinks – yogurt drink. Freshly harvested and carefully selected, this strawberry yogurt drink delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 250ml: Energy 110 kcal, Sugars 24g',
      NULL,
      NULL,
      '2025-08-03 09:52:56'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'CF-CD-SM-035',
      'Original Soy Milk',
      (SELECT brand_id FROM brands WHERE name='ChillCo'),
      (SELECT category_id FROM categories WHERE name='Soy Milk'),
      '1L',
      7.9,
      'The Original Soy Milk from ChillCo comes in a 1L pack and is part of our chilled & frozen – chilled drinks – soy milk. Freshly harvested and carefully selected, this original soy milk delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 250ml: Energy 110 kcal, Sugars 24g',
      NULL,
      NULL,
      '2025-08-12 03:58:52'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'CF-FF-FM-036',
      'Frozen Chicken Lasagne',
      (SELECT brand_id FROM brands WHERE name='ChillCo'),
      (SELECT category_id FROM categories WHERE name='Frozen Meals'),
      '450g',
      11.9,
      'The Frozen Chicken Lasagne from ChillCo comes in a 450g pack and is part of our chilled & frozen – frozen food – frozen meals. Freshly harvested and carefully selected, this frozen chicken lasagne delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per serving (150g): Energy 290 kcal, Protein 12g, Fat 12g, Carbs 32g',
      NULL,
      NULL,
      '2025-08-08 05:02:46'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'CF-FF-FMP-037',
      'Frozen Beef Meatballs',
      (SELECT brand_id FROM brands WHERE name='ChillCo'),
      (SELECT category_id FROM categories WHERE name='Frozen Meat Poultry'),
      '450g',
      11.9,
      'The Frozen Beef Meatballs from ChillCo comes in a 450g pack and is part of our chilled & frozen – frozen food – frozen meat poultry. Freshly harvested and carefully selected, this frozen beef meatballs delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per serving (150g): Energy 290 kcal, Protein 12g, Fat 12g, Carbs 32g',
      NULL,
      NULL,
      '2025-08-14 14:11:39'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'CF-FF-FMP-038',
      'Frozen Breaded Chicken',
      (SELECT brand_id FROM brands WHERE name='ChillCo'),
      (SELECT category_id FROM categories WHERE name='Frozen Meat Poultry'),
      '450g',
      11.9,
      'The Frozen Breaded Chicken from ChillCo comes in a 450g pack and is part of our chilled & frozen – frozen food – frozen meat poultry. Freshly harvested and carefully selected, this frozen breaded chicken delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per serving (150g): Energy 290 kcal, Protein 12g, Fat 12g, Carbs 32g',
      NULL,
      NULL,
      '2025-08-25 11:02:45'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'CF-FF-FPA-039',
      'Frozen Puff Pastry Sheets',
      (SELECT brand_id FROM brands WHERE name='ChillCo'),
      (SELECT category_id FROM categories WHERE name='Frozen Puff and Pastry'),
      '450g',
      11.9,
      'The Frozen Puff Pastry Sheets from ChillCo comes in a 450g pack and is part of our chilled & frozen – frozen food – frozen puff and pastry. Freshly harvested and carefully selected, this frozen puff pastry sheets delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per serving (150g): Energy 290 kcal, Protein 12g, Fat 12g, Carbs 32g',
      20.0,
      NULL,
      '2025-08-31 22:30:56'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'CF-FF-FS-040',
      'Frozen Fish Fillets',
      (SELECT brand_id FROM brands WHERE name='BlueWave'),
      (SELECT category_id FROM categories WHERE name='Frozen Seafood'),
      '450g',
      11.9,
      'The Frozen Fish Fillets from BlueWave comes in a 450g pack and is part of our chilled & frozen – frozen food – frozen seafood. Freshly harvested and carefully selected, this frozen fish fillets delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per serving (150g): Energy 290 kcal, Protein 12g, Fat 12g, Carbs 32g',
      NULL,
      NULL,
      '2025-08-22 02:23:19'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'CF-FF-FV-041',
      'Frozen Mixed Vegetables',
      (SELECT brand_id FROM brands WHERE name='VeggieWorld'),
      (SELECT category_id FROM categories WHERE name='Frozen Vegetables'),
      '450g',
      11.9,
      'The Frozen Mixed Vegetables from VeggieWorld comes in a 450g pack and is part of our chilled & frozen – frozen food – frozen vegetables. Freshly harvested and carefully selected, this frozen mixed vegetables delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per serving (150g): Energy 290 kcal, Protein 12g, Fat 12g, Carbs 32g',
      NULL,
      NULL,
      '2025-08-19 08:03:26'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'CF-FF-FF-042',
      'Frozen Mixed Berries',
      (SELECT brand_id FROM brands WHERE name='FruitCo'),
      (SELECT category_id FROM categories WHERE name='Frozen Fruits'),
      '450g',
      11.9,
      'The Frozen Mixed Berries from FruitCo comes in a 450g pack and is part of our chilled & frozen – frozen food – frozen fruits. Freshly harvested and carefully selected, this frozen mixed berries delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per serving (150g): Energy 290 kcal, Protein 12g, Fat 12g, Carbs 32g',
      NULL,
      NULL,
      '2025-08-31 02:40:21'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FEC-CIS-SH-045',
      'Ground Turmeric Powder',
      (SELECT brand_id FROM brands WHERE name='PantryCo'),
      (SELECT category_id FROM categories WHERE name='Spices & Herbs'),
      '200g',
      7.5,
      'The Ground Turmeric Powder from PantryCo comes in a 200g pack and is part of our food essentials & commodities – cooking ingredients & seasoning – spices & herbs. Freshly harvested and carefully selected, this ground turmeric powder delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per serving: values vary by recipe',
      NULL,
      NULL,
      '2025-09-01 21:36:24'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FEC-CIS-SDT-046',
      'Classic Caesar Dressing',
      (SELECT brand_id FROM brands WHERE name='PantryCo'),
      (SELECT category_id FROM categories WHERE name='Salad Dressings & Toppings'),
      '200g',
      7.5,
      'The Classic Caesar Dressing from PantryCo comes in a 200g pack and is part of our food essentials & commodities – cooking ingredients & seasoning – salad dressings & toppings. Freshly harvested and carefully selected, this classic caesar dressing delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per serving: values vary by recipe',
      10.0,
      NULL,
      '2025-08-26 21:29:15'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FEC-CIS-CP-047',
      'Aromatic Curry Paste',
      (SELECT brand_id FROM brands WHERE name='PantryCo'),
      (SELECT category_id FROM categories WHERE name='Cooking Pastes'),
      '200g',
      7.5,
      'The Aromatic Curry Paste from PantryCo comes in a 200g pack and is part of our food essentials & commodities – cooking ingredients & seasoning – cooking pastes. Freshly harvested and carefully selected, this aromatic curry paste delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per serving: values vary by recipe',
      25.0,
      NULL,
      '2025-08-16 04:06:49'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FEC-CIS-SG-048',
      'Chicken Stock Cubes',
      (SELECT brand_id FROM brands WHERE name='PantryCo'),
      (SELECT category_id FROM categories WHERE name='Stocks & Gravies'),
      '200g',
      7.5,
      'The Chicken Stock Cubes from PantryCo comes in a 200g pack and is part of our food essentials & commodities – cooking ingredients & seasoning – stocks & gravies. Freshly harvested and carefully selected, this chicken stock cubes delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per serving: values vary by recipe',
      20.0,
      NULL,
      '2025-08-21 08:44:55'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FEC-CIS-SS-049',
      'Naturally Brewed Soy Sauce',
      (SELECT brand_id FROM brands WHERE name='PantryCo'),
      (SELECT category_id FROM categories WHERE name='Soy Sauces'),
      '500ml',
      10.9,
      'The Naturally Brewed Soy Sauce from PantryCo comes in a 500ml pack and is part of our food essentials & commodities – cooking ingredients & seasoning – soy sauces. Freshly harvested and carefully selected, this naturally brewed soy sauce delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per serving: values vary by recipe',
      NULL,
      NULL,
      '2025-08-19 01:00:28'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FEC-CIS-HS-050',
      'Hot Chili Sauce',
      (SELECT brand_id FROM brands WHERE name='PantryCo'),
      (SELECT category_id FROM categories WHERE name='Hot Sauces'),
      '500ml',
      10.9,
      'The Hot Chili Sauce from PantryCo comes in a 500ml pack and is part of our food essentials & commodities – cooking ingredients & seasoning – hot sauces. Freshly harvested and carefully selected, this hot chili sauce delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per serving: values vary by recipe',
      NULL,
      NULL,
      '2025-08-07 09:55:10'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FEC-CIS-DS-051',
      'Garlic Dipping Sauce',
      (SELECT brand_id FROM brands WHERE name='PantryCo'),
      (SELECT category_id FROM categories WHERE name='Dipping Sauces'),
      '500ml',
      10.9,
      'The Garlic Dipping Sauce from PantryCo comes in a 500ml pack and is part of our food essentials & commodities – cooking ingredients & seasoning – dipping sauces. Freshly harvested and carefully selected, this garlic dipping sauce delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per serving: values vary by recipe',
      NULL,
      NULL,
      '2025-08-26 09:44:25'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FEC-CIS-SB-052',
      'Miso Soup Base',
      (SELECT brand_id FROM brands WHERE name='PantryCo'),
      (SELECT category_id FROM categories WHERE name='Soup Bases'),
      '200g',
      7.5,
      'The Miso Soup Base from PantryCo comes in a 200g pack and is part of our food essentials & commodities – cooking ingredients & seasoning – soup bases. Freshly harvested and carefully selected, this miso soup base delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per serving: values vary by recipe',
      NULL,
      NULL,
      '2025-08-21 04:34:25'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FEC-CIS-CO-053',
      'Pure Sunflower Oil',
      (SELECT brand_id FROM brands WHERE name='PantryCo'),
      (SELECT category_id FROM categories WHERE name='Cooking Oils'),
      '500ml',
      10.9,
      'The Pure Sunflower Oil from PantryCo comes in a 500ml pack and is part of our food essentials & commodities – cooking ingredients & seasoning – cooking oils. Freshly harvested and carefully selected, this pure sunflower oil delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 15ml: Energy 120 kcal, Fat 14g',
      NULL,
      NULL,
      '2025-08-10 08:16:56'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FEC-CIS-CL-054',
      'Cooking Wine Substitute',
      (SELECT brand_id FROM brands WHERE name='PantryCo'),
      (SELECT category_id FROM categories WHERE name='Cooking Liquids'),
      '500ml',
      10.9,
      'The Cooking Wine Substitute from PantryCo comes in a 500ml pack and is part of our food essentials & commodities – cooking ingredients & seasoning – cooking liquids. Freshly harvested and carefully selected, this cooking wine substitute delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 15ml: Energy 120 kcal, Fat 14g',
      NULL,
      NULL,
      '2025-08-12 08:44:53'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FEC-CIS-SP-055',
      'Sea Salt Grinder',
      (SELECT brand_id FROM brands WHERE name='PantryCo'),
      (SELECT category_id FROM categories WHERE name='Salt & Pepper'),
      '200g',
      7.5,
      'The Sea Salt Grinder from PantryCo comes in a 200g pack and is part of our food essentials & commodities – cooking ingredients & seasoning – salt & pepper. Freshly harvested and carefully selected, this sea salt grinder delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 1g: Sodium 390mg',
      NULL,
      NULL,
      '2025-08-08 02:55:48'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FEC-CIS-VX-056',
      'Apple Cider Vinegar',
      (SELECT brand_id FROM brands WHERE name='PantryCo'),
      (SELECT category_id FROM categories WHERE name='Vinegar'),
      '500ml',
      10.9,
      'The Apple Cider Vinegar from PantryCo comes in a 500ml pack and is part of our food essentials & commodities – cooking ingredients & seasoning – vinegar. Freshly harvested and carefully selected, this apple cider vinegar delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per serving: values vary by recipe',
      15.0,
      NULL,
      '2025-08-06 03:12:43'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FEC-CIS-SS-057',
      'Fine White Sugar',
      (SELECT brand_id FROM brands WHERE name='PantryCo'),
      (SELECT category_id FROM categories WHERE name='Sugar & Sweeteners'),
      '500g',
      7.5,
      'The Fine White Sugar from PantryCo comes in a 500g pack and is part of our food essentials & commodities – cooking ingredients & seasoning – sugar & sweeteners. Freshly harvested and carefully selected, this fine white sugar delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 4g: Energy 16 kcal',
      NULL,
      NULL,
      '2025-08-26 23:04:28'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FEC-PIF-IN-059',
      'Chicken Flavour Instant Noodles',
      (SELECT brand_id FROM brands WHERE name='PantryCo'),
      (SELECT category_id FROM categories WHERE name='Instant Noodles'),
      '400g',
      7.9,
      'The Chicken Flavour Instant Noodles from PantryCo comes in a 400g pack and is part of our food essentials & commodities – pasta & instant food – instant noodles. Freshly harvested and carefully selected, this chicken flavour instant noodles delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 75g dry: Energy 270 kcal, Carbs 55g, Protein 9g',
      NULL,
      NULL,
      '2025-09-01 01:47:24'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FEC-PIF-PX-060',
      'Spaghetti Pasta',
      (SELECT brand_id FROM brands WHERE name='PantryCo'),
      (SELECT category_id FROM categories WHERE name='Pasta'),
      '400g',
      7.9,
      'The Spaghetti Pasta from PantryCo comes in a 400g pack and is part of our food essentials & commodities – pasta & instant food – pasta. Freshly harvested and carefully selected, this spaghetti pasta delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 75g dry: Energy 270 kcal, Carbs 55g, Protein 9g',
      NULL,
      NULL,
      '2025-08-25 13:24:17'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FEC-PIF-IS-061',
      'Cream of Mushroom Soup',
      (SELECT brand_id FROM brands WHERE name='PantryCo'),
      (SELECT category_id FROM categories WHERE name='Instant Soup'),
      '400g',
      7.9,
      'The Cream of Mushroom Soup from PantryCo comes in a 400g pack and is part of our food essentials & commodities – pasta & instant food – instant soup. Freshly harvested and carefully selected, this cream of mushroom soup delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 75g dry: Energy 270 kcal, Carbs 55g, Protein 9g',
      NULL,
      NULL,
      '2025-08-03 14:03:54'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FEC-PIF-IPR-062',
      'Instant Chicken Porridge',
      (SELECT brand_id FROM brands WHERE name='PantryCo'),
      (SELECT category_id FROM categories WHERE name='Instant Porridge & Rice'),
      '400g',
      7.9,
      'The Instant Chicken Porridge from PantryCo comes in a 400g pack and is part of our food essentials & commodities – pasta & instant food – instant porridge & rice. Freshly harvested and carefully selected, this instant chicken porridge delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 75g dry: Energy 270 kcal, Carbs 55g, Protein 9g',
      NULL,
      NULL,
      '2025-08-28 09:04:50'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FEC-CF-CV-063',
      'Canned Sweet Corn',
      (SELECT brand_id FROM brands WHERE name='VeggieWorld'),
      (SELECT category_id FROM categories WHERE name='Canned Vegetables'),
      '425g can',
      6.9,
      'The Canned Sweet Corn from VeggieWorld comes in a 425g can pack and is part of our food essentials & commodities – canned food – canned vegetables. Freshly harvested and carefully selected, this canned sweet corn delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 100g: Energy 120 kcal, Protein 10g, Fat 4g, Carbs 12g',
      NULL,
      NULL,
      '2025-08-04 05:26:29'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FEC-CF-CF-064',
      'Canned Pineapple Slices',
      (SELECT brand_id FROM brands WHERE name='NatureBite'),
      (SELECT category_id FROM categories WHERE name='Canned Fruits'),
      '425g can',
      6.9,
      'The Canned Pineapple Slices from NatureBite comes in a 425g can pack and is part of our food essentials & commodities – canned food – canned fruits. Freshly harvested and carefully selected, this canned pineapple slices delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 100g: Energy 120 kcal, Protein 10g, Fat 4g, Carbs 12g',
      NULL,
      NULL,
      '2025-08-22 02:14:57'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FEC-CF-CM-065',
      'Canned Luncheon Meat',
      (SELECT brand_id FROM brands WHERE name='PantryCo'),
      (SELECT category_id FROM categories WHERE name='Canned Meat'),
      '425g can',
      6.9,
      'The Canned Luncheon Meat from PantryCo comes in a 425g can pack and is part of our food essentials & commodities – canned food – canned meat. Freshly harvested and carefully selected, this canned luncheon meat delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 100g: Energy 120 kcal, Protein 10g, Fat 4g, Carbs 12g',
      NULL,
      NULL,
      '2025-08-04 12:45:59'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FEC-CF-CS-066',
      'Canned Tuna Chunks',
      (SELECT brand_id FROM brands WHERE name='BlueWave'),
      (SELECT category_id FROM categories WHERE name='Canned Seafood'),
      '425g can',
      6.9,
      'The Canned Tuna Chunks from BlueWave comes in a 425g can pack and is part of our food essentials & commodities – canned food – canned seafood. Freshly harvested and carefully selected, this canned tuna chunks delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 100g: Energy 120 kcal, Protein 10g, Fat 4g, Carbs 12g',
      NULL,
      NULL,
      '2025-08-05 08:45:47'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FEC-RX-BR-067',
      'Brown Rice',
      (SELECT brand_id FROM brands WHERE name='PantryCo'),
      (SELECT category_id FROM categories WHERE name='Brown Rice'),
      '5kg',
      29.9,
      'The Brown Rice from PantryCo comes in a 5kg pack and is part of our food essentials & commodities – rice – brown rice. Freshly harvested and carefully selected, this brown rice delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 100g uncooked: Energy 360 kcal, Carbs 80g, Protein 7g',
      NULL,
      NULL,
      '2025-08-26 14:36:16'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FEC-RX-GR-068',
      'Glutinous Rice',
      (SELECT brand_id FROM brands WHERE name='PantryCo'),
      (SELECT category_id FROM categories WHERE name='Glutinous Rice'),
      '5kg',
      29.9,
      'The Glutinous Rice from PantryCo comes in a 5kg pack and is part of our food essentials & commodities – rice – glutinous rice. Freshly harvested and carefully selected, this glutinous rice delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 100g uncooked: Energy 360 kcal, Carbs 80g, Protein 7g',
      NULL,
      NULL,
      '2025-09-01 11:45:45'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'FEC-RX-WR-069',
      'White Rice',
      (SELECT brand_id FROM brands WHERE name='PantryCo'),
      (SELECT category_id FROM categories WHERE name='White Rice'),
      '5kg',
      29.9,
      'The White Rice from PantryCo comes in a 5kg pack and is part of our food essentials & commodities – rice – white rice. Freshly harvested and carefully selected, this white rice delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 100g uncooked: Energy 360 kcal, Carbs 80g, Protein 7g',
      NULL,
      NULL,
      '2025-08-13 21:35:25'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'SX-BC-BCW-070',
      'Butter Cookies with Chocolate Chips',
      (SELECT brand_id FROM brands WHERE name='SnackCo'),
      (SELECT category_id FROM categories WHERE name='Biscuits & Cookies'),
      '120g',
      7.9,
      'The Butter Cookies with Chocolate Chips from SnackCo comes in a 120g pack and is part of our snacks – biscuits & cookies. Freshly harvested and carefully selected, this butter cookies with chocolate chips delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 30g serving: Energy 150 kcal, Fat 7g, Carbs 19g, Sugars 9g',
      NULL,
      NULL,
      '2025-08-05 14:06:16'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'SX-CX-CX-071',
      'Milk Chocolate Bar',
      (SELECT brand_id FROM brands WHERE name='CrunchyBite'),
      (SELECT category_id FROM categories WHERE name='Chocolates'),
      '20g',
      7.9,
      'The Milk Chocolate Bar from CrunchyBite comes in a 20g pack and is part of our snacks – chocolates. Freshly harvested and carefully selected, this milk chocolate bar delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 30g serving: Energy 150 kcal, Fat 7g, Carbs 19g, Sugars 9g',
      NULL,
      NULL,
      '2025-08-24 22:30:51'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'SX-CS-CS-072',
      'Fruit Gummies',
      (SELECT brand_id FROM brands WHERE name='SnackCo'),
      (SELECT category_id FROM categories WHERE name='Candies & Sweets'),
      '120g',
      7.9,
      'The Fruit Gummies from SnackCo comes in a 120g pack and is part of our snacks – candies & sweets. Freshly harvested and carefully selected, this fruit gummies delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 30g serving: Energy 150 kcal, Fat 7g, Carbs 19g, Sugars 9g',
      NULL,
      NULL,
      '2025-08-12 00:58:07'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'SX-CC-PAC-073',
      'Classic Potato Chips',
      (SELECT brand_id FROM brands WHERE name='HappySnacks'),
      (SELECT category_id FROM categories WHERE name='Potato and Corn Chips'),
      '150g',
      5.9,
      'The Classic Potato Chips from HappySnacks comes in a 150g pack and is part of our snacks – chips & crisps – potato and corn chips. Freshly harvested and carefully selected, this classic potato chips delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 30g serving: Energy 150 kcal, Fat 7g, Carbs 19g, Sugars 9g',
      NULL,
      NULL,
      '2025-08-25 09:32:10'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'SX-CC-CS-074',
      'Stacked Potato Crisps',
      (SELECT brand_id FROM brands WHERE name='SnackCo'),
      (SELECT category_id FROM categories WHERE name='Canister Snacks'),
      '150g',
      5.9,
      'The Stacked Potato Crisps from SnackCo comes in a 150g pack and is part of our snacks – chips & crisps – canister snacks. Freshly harvested and carefully selected, this stacked potato crisps delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 30g serving: Energy 150 kcal, Fat 7g, Carbs 19g, Sugars 9g',
      NULL,
      NULL,
      '2025-08-08 18:51:39'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'SX-CC-RTS-075',
      'Cheese Rings',
      (SELECT brand_id FROM brands WHERE name='HappySnacks'),
      (SELECT category_id FROM categories WHERE name='Ring & Twisted Snacks'),
      '150g',
      5.9,
      'The Cheese Rings from HappySnacks comes in a 150g pack and is part of our snacks – chips & crisps – ring & twisted snacks. Freshly harvested and carefully selected, this cheese rings delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 30g serving: Energy 150 kcal, Fat 7g, Carbs 19g, Sugars 9g',
      NULL,
      NULL,
      '2025-08-17 17:47:02'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'BX-CD-SD-077',
      'Cola Flavoured Soda',
      (SELECT brand_id FROM brands WHERE name='DrinkUp'),
      (SELECT category_id FROM categories WHERE name='Carbonated Drinks'),
      '1.5L',
      4.5,
      'The Cola Flavoured Soda from DrinkUp comes in a 1.5L pack and is part of our beverages – carbonated drinks. Freshly harvested and carefully selected, this cola flavoured soda delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 250ml: Energy 110 kcal, Sugars 27g',
      NULL,
      NULL,
      '2025-08-26 10:19:52'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'BX-CX-BAG-082',
      'Medium Roast Coffee Beans',
      (SELECT brand_id FROM brands WHERE name='SipCo'),
      (SELECT category_id FROM categories WHERE name='Beans and Ground'),
      '250g',
      24.9,
      'The Medium Roast Coffee Beans from SipCo comes in a 250g pack and is part of our beverages – coffee – beans and ground. Freshly harvested and carefully selected, this medium roast coffee beans delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per serving: Energy 2 kcal (black coffee)',
      NULL,
      NULL,
      '2025-08-24 09:02:28'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'BX-CX-IC-083',
      'Classic Instant Coffee',
      (SELECT brand_id FROM brands WHERE name='SipCo'),
      (SELECT category_id FROM categories WHERE name='Instant Coffee'),
      '200g',
      17.9,
      'The Classic Instant Coffee from SipCo comes in a 200g pack and is part of our beverages – coffee – instant coffee. Freshly harvested and carefully selected, this classic instant coffee delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per serving: Energy 2 kcal (black coffee)',
      NULL,
      NULL,
      '2025-08-29 07:30:51'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'BX-CX-RTD-085',
      'Iced Latte Bottle',
      (SELECT brand_id FROM brands WHERE name='SipCo'),
      (SELECT category_id FROM categories WHERE name='Drink Coffee'),
      '500ml',
      4.9,
      'The Iced Latte Bottle from SipCo comes in a 500ml pack and is part of our beverages – coffee – ready-to-drink coffee. Freshly harvested and carefully selected, this iced latte bottle delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 250ml: Energy 110 kcal, Sugars 18g',
      NULL,
      NULL,
      '2025-08-17 02:38:41'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'BX-WX-MW-090',
      'Natural Mineral Water',
      (SELECT brand_id FROM brands WHERE name='DrinkUp'),
      (SELECT category_id FROM categories WHERE name='Water'),
      '1.5L',
      2.5,
      'The Natural Mineral Water from DrinkUp comes in a 1.5L pack and is part of our beverages – water. Freshly harvested and carefully selected, this natural mineral water delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Per 250ml: Energy 0 kcal',
      NULL,
      NULL,
      '2025-09-02 02:34:02'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'HP-PP-BR-097',
      'Bathroom Tissue Rolls',
      (SELECT brand_id FROM brands WHERE name='CleanCo'),
      (SELECT category_id FROM categories WHERE name='Bathroom Roll'),
      '6 rolls',
      10.9,
      'The Bathroom Tissue Rolls from CleanCo comes in a 6 rolls pack and is part of our household products – paper products – bathroom roll. Freshly harvested and carefully selected, this bathroom tissue rolls delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Not applicable',
      NULL,
      NULL,
      '2025-08-05 05:20:20'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'HP-PP-FT-098',
      'Soft Facial Tissue',
      (SELECT brand_id FROM brands WHERE name='CleanCo'),
      (SELECT category_id FROM categories WHERE name='Facial Tissue'),
      '10 packs',
      10.9,
      'The Soft Facial Tissue from CleanCo comes in a 10 packs pack and is part of our household products – paper products – facial tissue. Freshly harvested and carefully selected, this soft facial tissue delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Not applicable',
      NULL,
      NULL,
      '2025-08-22 17:23:47'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'HP-PP-KR-099',
      'Kitchen Towels',
      (SELECT brand_id FROM brands WHERE name='Sparkle'),
      (SELECT category_id FROM categories WHERE name='Kitchen Roll'),
      '6 rolls',
      10.9,
      'The Kitchen Towels from Sparkle comes in a 6 rolls pack and is part of our household products – paper products – kitchen roll. Freshly harvested and carefully selected, this kitchen towels delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Not applicable',
      NULL,
      NULL,
      '2025-08-09 04:32:49'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'HP-PP-PT-100',
      'Pocket Tissues',
      (SELECT brand_id FROM brands WHERE name='CleanCo'),
      (SELECT category_id FROM categories WHERE name='Pocket Tissue'),
      '6 rolls',
      10.9,
      'The Pocket Tissues from CleanCo comes in a 6 rolls pack and is part of our household products – paper products – pocket tissue. Freshly harvested and carefully selected, this pocket tissues delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Not applicable',
      NULL,
      NULL,
      '2025-08-14 18:19:54'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'HP-PP-WX-101',
      'Antibacterial Wet Wipes',
      (SELECT brand_id FROM brands WHERE name='CleanCo'),
      (SELECT category_id FROM categories WHERE name='Wipes'),
      '10 wipes',
      5.0,
      'The Antibacterial Wet Wipes from CleanCo comes in a 10 wipes pack and is part of our household products – paper products – wipes. Freshly harvested and carefully selected, this antibacterial wet wipes delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Not applicable',
      NULL,
      NULL,
      '2025-08-27 06:13:58'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'HP-LX-BX-102',
      'Fabric-Safe Bleach',
      (SELECT brand_id FROM brands WHERE name='CleanCo'),
      (SELECT category_id FROM categories WHERE name='Bleach'),
      '1.5L',
      9.9,
      'The Fabric-Safe Bleach from CleanCo comes in a 1.5L pack and is part of our household products – laundry – bleach. Freshly harvested and carefully selected, this fabric-safe bleach delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Not applicable',
      NULL,
      NULL,
      '2025-08-19 15:34:26'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'HP-LX-SAD-103',
      'Clothing Softener',
      (SELECT brand_id FROM brands WHERE name='CleanCo'),
      (SELECT category_id FROM categories WHERE name='Softener and Delicate Care'),
      '1.5L',
      9.9,
      'The Clothing Softener from CleanCo comes in a 1.5L pack and is part of our household products – laundry – softener and delicate care. Freshly harvested and carefully selected, this clothing softener delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Not applicable',
      NULL,
      NULL,
      '2025-08-29 19:19:17'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'HP-LX-DX-104',
      'Laundry Detergent',
      (SELECT brand_id FROM brands WHERE name='HomeCare'),
      (SELECT category_id FROM categories WHERE name='Detergent'),
      '1.5L',
      9.9,
      'The Laundry Detergent from HomeCare comes in a 1.5L pack and is part of our household products – laundry – detergent. Freshly harvested and carefully selected, this laundry detergent delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Not applicable',
      NULL,
      NULL,
      '2025-08-10 01:29:15'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'HP-LX-LA-105',
      'Laundry Mesh Bag',
      (SELECT brand_id FROM brands WHERE name='CleanCo'),
      (SELECT category_id FROM categories WHERE name='Laundry Accessories'),
      '1.5L',
      9.9,
      'The Laundry Mesh Bag from CleanCo comes in a 1.5L pack and is part of our household products – laundry – laundry accessories. Freshly harvested and carefully selected, this laundry mesh bag delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Not applicable',
      NULL,
      NULL,
      '2025-08-12 08:12:49'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'HP-HCA-SS-106',
      'Non-Scratch Scouring Sponge',
      (SELECT brand_id FROM brands WHERE name='Sparkle'),
      (SELECT category_id FROM categories WHERE name='Sponge & Scourer'),
      '1 unit',
      6.9,
      'The Non-Scratch Scouring Sponge from Sparkle comes in a 1 unit pack and is part of our household products – home cleaning accessories – sponge & scourer. Freshly harvested and carefully selected, this non-scratch scouring sponge delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Not applicable',
      NULL,
      'Free Gift with Purchase',
      '2025-08-21 01:23:43'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'HP-HCA-MX-107',
      'Microfiber Mop',
      (SELECT brand_id FROM brands WHERE name='Sparkle'),
      (SELECT category_id FROM categories WHERE name='Mops'),
      '1 unit',
      6.9,
      'The Microfiber Mop from Sparkle comes in a 1 unit pack and is part of our household products – home cleaning accessories – mops. Freshly harvested and carefully selected, this microfiber mop delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Not applicable',
      NULL,
      'Buy 1 Free 1',
      '2025-08-09 21:23:10'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'HP-HCA-BDS-108',
      'Broom and Dustpan Set',
      (SELECT brand_id FROM brands WHERE name='Sparkle'),
      (SELECT category_id FROM categories WHERE name='Brooms & Dust Scoop'),
      '1 unit',
      6.9,
      'The Broom and Dustpan Set from Sparkle comes in a 1 unit pack and is part of our household products – home cleaning accessories – brooms & dust scoop. Freshly harvested and carefully selected, this broom and dustpan set delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Not applicable',
      NULL,
      NULL,
      '2025-08-22 18:58:24'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'HP-HCA-GX-110',
      'Household Cleaning Gloves',
      (SELECT brand_id FROM brands WHERE name='HomeCare'),
      (SELECT category_id FROM categories WHERE name='Gloves'),
      '1 unit',
      6.9,
      'The Household Cleaning Gloves from HomeCare comes in a 1 unit pack and is part of our household products – home cleaning accessories – gloves. Freshly harvested and carefully selected, this household cleaning gloves delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Not applicable',
      NULL,
      NULL,
      '2025-08-08 05:15:52'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'HP-HCA-PWD-111',
      'Cleaning Pail with Dipper',
      (SELECT brand_id FROM brands WHERE name='Sparkle'),
      (SELECT category_id FROM categories WHERE name='Pail & Water Dipper'),
      '1 unit',
      6.9,
      'The Cleaning Pail with Dipper from Sparkle comes in a 1 unit pack and is part of our household products – home cleaning accessories – pail & water dipper. Freshly harvested and carefully selected, this cleaning pail with dipper delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Not applicable',
      NULL,
      NULL,
      '2025-08-20 21:53:35'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'HP-HCA-DCC-112',
      'Microfiber Cleaning Cloth',
      (SELECT brand_id FROM brands WHERE name='Sparkle'),
      (SELECT category_id FROM categories WHERE name='Duster & Cleaning Cloth'),
      '1 unit',
      6.9,
      'The Microfiber Cleaning Cloth from Sparkle comes in a 1 unit pack and is part of our household products – home cleaning accessories – duster & cleaning cloth. Freshly harvested and carefully selected, this microfiber cleaning cloth delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Not applicable',
      NULL,
      NULL,
      '2025-08-04 06:06:15'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'BH-BC-SSA-113',
      'Moisturizing Body Wash',
      (SELECT brand_id FROM brands WHERE name='GlowWell'),
      (SELECT category_id FROM categories WHERE name='Soaps, Scrubs and Gels'),
      '250ml',
      12.9,
      'The Moisturizing Body Wash from GlowWell comes in a 250ml pack and is part of our beauty & health – body care – soaps, scrubs and gels. Freshly harvested and carefully selected, this moisturizing body wash delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Not applicable',
      NULL,
      NULL,
      '2025-08-04 07:30:54'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'BH-BC-BLA-114',
      'Hydrating Body Lotion',
      (SELECT brand_id FROM brands WHERE name='GlowWell'),
      (SELECT category_id FROM categories WHERE name='Body Lotion and Cream'),
      '250ml',
      12.9,
      'The Hydrating Body Lotion from GlowWell comes in a 250ml pack and is part of our beauty & health – body care – body lotion and cream. Freshly harvested and carefully selected, this hydrating body lotion delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Not applicable',
      NULL,
      NULL,
      '2025-08-18 14:30:19'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'BH-BC-DAF-115',
      'Long-Lasting Deodorant',
      (SELECT brand_id FROM brands WHERE name='GlowWell'),
      (SELECT category_id FROM categories WHERE name='Deo and Fragrances'),
      '250ml',
      12.9,
      'The Long-Lasting Deodorant from GlowWell comes in a 250ml pack and is part of our beauty & health – body care – deo and fragrances. Freshly harvested and carefully selected, this long-lasting deodorant delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Not applicable',
      NULL,
      NULL,
      '2025-08-18 10:34:28'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'BH-BC-HAF-116',
      'Nourishing Hand Cream',
      (SELECT brand_id FROM brands WHERE name='CarePlus'),
      (SELECT category_id FROM categories WHERE name='Hand and Foot Care'),
      '250ml',
      12.9,
      'The Nourishing Hand Cream from CarePlus comes in a 250ml pack and is part of our beauty & health – body care – hand and foot care. Freshly harvested and carefully selected, this nourishing hand cream delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Not applicable',
      NULL,
      NULL,
      '2025-08-11 02:33:23'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'BH-BC-SX-117',
      'Gentle Facial Cleanser',
      (SELECT brand_id FROM brands WHERE name='CarePlus'),
      (SELECT category_id FROM categories WHERE name='Skincare'),
      '250ml',
      12.9,
      'The Gentle Facial Cleanser from CarePlus comes in a 250ml pack and is part of our beauty & health – body care – skincare. Freshly harvested and carefully selected, this gentle facial cleanser delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Not applicable',
      NULL,
      NULL,
      '2025-08-06 07:05:04'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'BH-HC-CX-118',
      'Hair Repair Conditioner',
      (SELECT brand_id FROM brands WHERE name='GlowWell'),
      (SELECT category_id FROM categories WHERE name='Conditioner'),
      '250ml',
      12.9,
      'The Hair Repair Conditioner from GlowWell comes in a 250ml pack and is part of our beauty & health – hair care – conditioner. Freshly harvested and carefully selected, this hair repair conditioner delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Not applicable',
      NULL,
      NULL,
      '2025-08-09 11:23:05'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'BH-HC-SX-119',
      'Daily Care Shampoo',
      (SELECT brand_id FROM brands WHERE name='CarePlus'),
      (SELECT category_id FROM categories WHERE name='Shampoo'),
      '250ml',
      12.9,
      'The Daily Care Shampoo from CarePlus comes in a 250ml pack and is part of our beauty & health – hair care – shampoo. Freshly harvested and carefully selected, this daily care shampoo delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Not applicable',
      NULL,
      NULL,
      '2025-08-08 11:09:54'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'BH-HC-HS-120',
      'Strong Hold Hair Gel',
      (SELECT brand_id FROM brands WHERE name='GlowWell'),
      (SELECT category_id FROM categories WHERE name='Hair Styling'),
      '250ml',
      12.9,
      'The Strong Hold Hair Gel from GlowWell comes in a 250ml pack and is part of our beauty & health – hair care – hair styling. Freshly harvested and carefully selected, this strong hold hair gel delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Not applicable',
      NULL,
      NULL,
      '2025-08-25 11:53:06'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'BH-HC-HCT-121',
      'Natural Brown Hair Color',
      (SELECT brand_id FROM brands WHERE name='BeautyGen'),
      (SELECT category_id FROM categories WHERE name='Hair Colour & Treatments'),
      '250ml',
      12.9,
      'The Natural Brown Hair Color from BeautyGen comes in a 250ml pack and is part of our beauty & health – hair care – hair colour & treatments. Freshly harvested and carefully selected, this natural brown hair color delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Not applicable',
      NULL,
      NULL,
      '2025-08-05 18:34:04'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'BH-HC-HST-122',
      'Hair Scalp Tonic',
      (SELECT brand_id FROM brands WHERE name='GlowWell'),
      (SELECT category_id FROM categories WHERE name='Hair & Scalp Treatments'),
      '250ml',
      12.9,
      'The Hair Scalp Tonic from GlowWell comes in a 250ml pack and is part of our beauty & health – hair care – hair & scalp treatments. Freshly harvested and carefully selected, this hair scalp tonic delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Not applicable',
      NULL,
      NULL,
      '2025-08-23 17:03:26'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'BH-OC-MX-127',
      'Fresh Mint Mouthwash',
      (SELECT brand_id FROM brands WHERE name='CarePlus'),
      (SELECT category_id FROM categories WHERE name='Mouthwash'),
      '250ml',
      12.9,
      'The Fresh Mint Mouthwash from CarePlus comes in a 250ml pack and is part of our beauty & health – oral care – mouthwash. Freshly harvested and carefully selected, this fresh mint mouthwash delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Not applicable',
      NULL,
      NULL,
      '2025-08-25 19:13:17'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'BH-OC-TA-128',
      'Soft Bristle Toothbrush',
      (SELECT brand_id FROM brands WHERE name='CarePlus'),
      (SELECT category_id FROM categories WHERE name='Toothbrush & Accessories'),
      '250ml',
      12.9,
      'The Soft Bristle Toothbrush from CarePlus comes in a 250ml pack and is part of our beauty & health – oral care – toothbrush & accessories. Freshly harvested and carefully selected, this soft bristle toothbrush delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Not applicable',
      NULL,
      NULL,
      '2025-08-07 03:36:32'
    );

INSERT INTO products 
    (sku, product_name, brand_id, category_id, weight_volume, unit_price, 
     product_description, nutritional_info, discount_percent, special_offer_label, created_at)
    VALUES (
      'BH-OC-TX-129',
      'Whitening Toothpaste',
      (SELECT brand_id FROM brands WHERE name='CarePlus'),
      (SELECT category_id FROM categories WHERE name='Toothpaste'),
      '250ml',
      12.9,
      'The Whitening Toothpaste from CarePlus comes in a 250ml pack and is part of our beauty & health – oral care – toothpaste. Freshly harvested and carefully selected, this whitening toothpaste delivers excellent taste and nutrition. Perfect for a wide variety of recipes — whether steamed, roasted, stir-fried, or enjoyed raw — it brings convenience and health benefits to your meals. A wholesome choice for everyday cooking, it’s ideal for families and health-conscious individuals looking for both flavor and nourishment.',
      'Not applicable',
      NULL,
      'Buy 2 Get 10% Off',
      '2025-08-13 23:16:56'
    );

/* Product Images */
INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FP-VX-AS-001'),
      'image/products/fresh_produces/vegetables/asparagus_shoots/green_asparagus_bunch.jpg',
      'Green Asparagus Bunch',
      '2025-08-30 10:16:10'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FP-VX-BP-002'),
      'image/products/fresh_produces/vegetables/beans_peas/sugar_snap_peas.jpg',
      'Sugar Snap Peas',
      '2025-08-17 11:13:14'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FP-VX-BC-003'),
      'image/products/fresh_produces/vegetables/broccoli_cabbages/broccoli_crown.jpg',
      'Broccoli Crown',
      '2025-08-04 02:44:55'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FP-VX-CP-004'),
      'image/products/fresh_produces/vegetables/carrots_potatoes/baby_carrots.jpg',
      'Baby Carrots',
      '2025-08-04 02:53:49'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FP-VX-CS-005'),
      'image/products/fresh_produces/vegetables/cucumbers_squash/japanese_cucumber.jpg',
      'Japanese Cucumber',
      '2025-08-25 12:28:03'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FP-VX-CC-006'),
      'image/products/fresh_produces/vegetables/chili_capsicum/red_capsicum.jpg',
      'Red Capsicum',
      '2025-08-26 05:51:03'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FP-VX-HX-007'),
      'image/products/fresh_produces/vegetables/herbs/fresh_basil.jpg',
      'Fresh Basil',
      '2025-08-22 07:42:45'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FP-VX-LV-008'),
      'image/products/fresh_produces/vegetables/leafy_vegetables/romaine_lettuce.jpg',
      'Romaine Lettuce',
      '2025-08-03 21:39:00'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FP-VX-MF-009'),
      'image/products/fresh_produces/vegetables/mushrooms_fungi/brown_button_mushrooms.jpg',
      'Brown Button Mushrooms',
      '2025-08-12 05:56:53'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FP-VX-OG-010'),
      'image/products/fresh_produces/vegetables/onions_garlic/yellow_onions.jpg',
      'Yellow Onions',
      '2025-08-23 13:08:11'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FP-FX-AX-011'),
      'image/products/fresh_produces/fruits/apples/crisp_green_apples.jpg',
      'Crisp Green Apples',
      '2025-08-27 04:58:30'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FP-FX-BG-012'),
      'image/products/fresh_produces/fruits/berries_grapes/seedless_red_grapes.jpg',
      'Seedless Red Grapes',
      '2025-08-26 12:24:15'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FP-FX-CO-013'),
      'image/products/fresh_produces/fruits/citrus_oranges/sweet_navel_oranges.jpg',
      'Sweet Navel Oranges',
      '2025-08-19 06:15:37'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FP-FX-MX-014'),
      'image/products/fresh_produces/fruits/melons/honeydew_melon.jpg',
      'Honeydew Melon',
      '2025-08-15 10:58:07'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FP-FX-PX-015'),
      'image/products/fresh_produces/fruits/pears/juicy_packham_pears.jpg',
      'Juicy Packham Pears',
      '2025-08-29 03:59:30'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FP-FX-SF-016'),
      'image/products/fresh_produces/fruits/stone_fruits/yellow_peaches.jpg',
      'Yellow Peaches',
      '2025-08-29 13:03:21'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FP-FX-TF-017'),
      'image/products/fresh_produces/fruits/tropical_fruits/ripe_mangos.jpg',
      'Ripe Mangos',
      '2025-08-21 02:41:14'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FP-MP-CX-018'),
      'image/products/fresh_produces/meat_poultry/chicken/chicken_breast_fillet.jpg',
      'Chicken Breast Fillet',
      '2025-08-27 01:56:05'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FP-MP-PX-019'),
      'image/products/fresh_produces/meat_poultry/pork/pork_loin_slices.jpg',
      'Pork Loin Slices',
      '2025-08-31 00:08:33'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FP-MP-BX-020'),
      'image/products/fresh_produces/meat_poultry/beef/beef_sirloin.jpg',
      'Beef Sirloin',
      '2025-08-31 14:32:23'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FP-MP-LX-021'),
      'image/products/fresh_produces/meat_poultry/lamb/lamb_shoulder_chops.jpg',
      'Lamb Shoulder Chops',
      '2025-08-19 04:26:30'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FP-SX-FX-022'),
      'image/products/fresh_produces/seafood/fish/salmon_fillet.jpg',
      'Salmon Fillet',
      '2025-08-30 11:32:06'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FP-SX-PX-023'),
      'image/products/fresh_produces/seafood/prawn/large_tiger_prawns.jpg',
      'Large Tiger Prawns',
      '2025-08-10 03:08:20'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FP-SX-CX-024'),
      'image/products/fresh_produces/seafood/crab/cleaned_mud_crab.jpg',
      'Cleaned Mud Crab',
      '2025-08-18 00:26:50'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FP-SX-SX-025'),
      'image/products/fresh_produces/seafood/squid/cleaned_squid_rings.jpg',
      'Cleaned Squid Rings',
      '2025-08-10 01:24:12'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FP-SX-CX-026'),
      'image/products/fresh_produces/seafood/clam/live_clams.jpg',
      'Live Clams',
      '2025-08-28 09:00:21'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FP-EX-EX-027'),
      'image/products/fresh_produces/egg/farm_eggs_grade_a.jpg',
      'Farm Eggs (Grade A)',
      '2025-08-04 20:43:38'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='CF-DP-MX-028'),
      'image/products/chilled_frozen/dairy_produces/milk/full_cream_milk.jpg',
      'Full Cream Milk',
      '2025-08-21 15:08:38'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='CF-DP-YX-029'),
      'image/products/chilled_frozen/dairy_produces/yogurt/greek_style_yogurt.jpg',
      'Greek-Style Yogurt',
      '2025-08-12 14:28:27'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='CF-DP-CX-030'),
      'image/products/chilled_frozen/dairy_produces/cheese/mild_cheddar_cheese.jpg',
      'Mild Cheddar Cheese',
      '2025-08-03 23:27:31'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='CF-DP-BM-031'),
      'image/products/chilled_frozen/dairy_produces/butter_margarine/creamy_salted_butter.jpg',
      'Creamy Salted Butter',
      '2025-08-26 08:43:48'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='CF-DP-CX-032'),
      'image/products/chilled_frozen/dairy_produces/cream/cooking_cream.jpg',
      'Cooking Cream',
      '2025-08-22 20:16:31'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='CF-CD-JX-033'),
      'image/products/chilled_frozen/chilled_drinks/juice/apple_juice.jpg',
      'Apple Juice',
      '2025-09-02 07:03:28'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='CF-CD-YD-034'),
      'image/products/chilled_frozen/chilled_drinks/yogurt_drink/strawberry_yogurt_drink.jpg',
      'Strawberry Yogurt Drink',
      '2025-08-03 09:53:56'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='CF-CD-SM-035'),
      'image/products/chilled_frozen/chilled_drinks/soy_milk/original_soy_milk.jpg',
      'Original Soy Milk',
      '2025-08-12 03:59:52'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='CF-FF-FM-036'),
      'image/products/chilled_frozen/frozen_food/frozen_meals/frozen_chicken_lasagne.jpg',
      'Frozen Chicken Lasagne',
      '2025-08-08 05:03:46'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='CF-FF-FMP-037'),
      'image/products/chilled_frozen/frozen_food/frozen_meat_poultry/frozen_beef_meatballs.jpg',
      'Frozen Beef Meatballs',
      '2025-08-14 14:12:39'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='CF-FF-FMP-038'),
      'image/products/chilled_frozen/frozen_food/frozen_meat_poultry/frozen_breaded_chicken.jpg',
      'Frozen Breaded Chicken',
      '2025-08-25 11:03:45'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='CF-FF-FPA-039'),
      'image/products/chilled_frozen/frozen_food/frozen_puff_and_pastry/frozen_puff_pastry_sheets.jpg',
      'Frozen Puff Pastry Sheets',
      '2025-08-31 22:31:56'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='CF-FF-FS-040'),
      'image/products/chilled_frozen/frozen_food/frozen_seafood/frozen_fish_fillets.jpg',
      'Frozen Fish Fillets',
      '2025-08-22 02:24:19'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='CF-FF-FV-041'),
      'image/products/chilled_frozen/frozen_food/frozen_vegetables/frozen_mixed_vegetables.jpg',
      'Frozen Mixed Vegetables',
      '2025-08-19 08:04:26'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='CF-FF-FF-042'),
      'image/products/chilled_frozen/frozen_food/frozen_fruits/frozen_mixed_berries.jpg',
      'Frozen Mixed Berries',
      '2025-08-31 02:41:21'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FEC-CIS-SH-045'),
      'image/products/food_essentials_commodities/cooking_ingredients_seasoning/spices_herbs/ground_turmeric_powder.jpg',
      'Ground Turmeric Powder',
      '2025-09-01 21:37:24'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FEC-CIS-SDT-046'),
      'image/products/food_essentials_commodities/cooking_ingredients_seasoning/salad_dressings_toppings/classic_caesar_dressing.jpg',
      'Classic Caesar Dressing',
      '2025-08-26 21:30:15'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FEC-CIS-CP-047'),
      'image/products/food_essentials_commodities/cooking_ingredients_seasoning/cooking_pastes/aromatic_curry_paste.jpg',
      'Aromatic Curry Paste',
      '2025-08-16 04:07:49'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FEC-CIS-SG-048'),
      'image/products/food_essentials_commodities/cooking_ingredients_seasoning/stocks_gravies/chicken_stock_cubes.jpg',
      'Chicken Stock Cubes',
      '2025-08-21 08:45:55'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FEC-CIS-SS-049'),
      'image/products/food_essentials_commodities/cooking_ingredients_seasoning/soy_sauces/naturally_brewed_soy_sauce.jpg',
      'Naturally Brewed Soy Sauce',
      '2025-08-19 01:01:28'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FEC-CIS-HS-050'),
      'image/products/food_essentials_commodities/cooking_ingredients_seasoning/hot_sauces/hot_chili_sauce.jpg',
      'Hot Chili Sauce',
      '2025-08-07 09:56:10'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FEC-CIS-DS-051'),
      'image/products/food_essentials_commodities/cooking_ingredients_seasoning/dipping_sauces/garlic_dipping_sauce.jpg',
      'Garlic Dipping Sauce',
      '2025-08-26 09:45:25'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FEC-CIS-SB-052'),
      'image/products/food_essentials_commodities/cooking_ingredients_seasoning/soup_bases/miso_soup_base.jpg',
      'Miso Soup Base',
      '2025-08-21 04:35:25'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FEC-CIS-CO-053'),
      'image/products/food_essentials_commodities/cooking_ingredients_seasoning/cooking_oils/pure_sunflower_oil.jpg',
      'Pure Sunflower Oil',
      '2025-08-10 08:17:56'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FEC-CIS-CL-054'),
      'image/products/food_essentials_commodities/cooking_ingredients_seasoning/cooking_liquids/cooking_wine_substitute.jpg',
      'Cooking Wine Substitute',
      '2025-08-12 08:45:53'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FEC-CIS-SP-055'),
      'image/products/food_essentials_commodities/cooking_ingredients_seasoning/salt_pepper/sea_salt_grinder.jpg',
      'Sea Salt Grinder',
      '2025-08-08 02:56:48'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FEC-CIS-VX-056'),
      'image/products/food_essentials_commodities/cooking_ingredients_seasoning/vinegar/apple_cider_vinegar.jpg',
      'Apple Cider Vinegar',
      '2025-08-06 03:13:43'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FEC-CIS-SS-057'),
      'image/products/food_essentials_commodities/cooking_ingredients_seasoning/sugar_sweeteners/fine_white_sugar.jpg',
      'Fine White Sugar',
      '2025-08-26 23:05:28'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FEC-PIF-IN-059'),
      'image/products/food_essentials_commodities/pasta_instant_food/instant_noodles/chicken_flavour_instant_noodles.jpg',
      'Chicken Flavour Instant Noodles',
      '2025-09-01 01:48:24'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FEC-PIF-PX-060'),
      'image/products/food_essentials_commodities/pasta_instant_food/pasta/spaghetti_pasta.jpg',
      'Spaghetti Pasta',
      '2025-08-25 13:25:17'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FEC-PIF-IS-061'),
      'image/products/food_essentials_commodities/pasta_instant_food/instant_soup/cream_of_mushroom_soup.jpg',
      'Cream of Mushroom Soup',
      '2025-08-03 14:04:54'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FEC-PIF-IPR-062'),
      'image/products/food_essentials_commodities/pasta_instant_food/instant_porridge_rice/instant_chicken_porridge.jpg',
      'Instant Chicken Porridge',
      '2025-08-28 09:05:50'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FEC-CF-CV-063'),
      'image/products/food_essentials_commodities/canned_food/canned_vegetables/canned_sweet_corn.jpg',
      'Canned Sweet Corn',
      '2025-08-04 05:27:29'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FEC-CF-CF-064'),
      'image/products/food_essentials_commodities/canned_food/canned_fruits/canned_pineapple_slices.jpg',
      'Canned Pineapple Slices',
      '2025-08-22 02:15:57'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FEC-CF-CM-065'),
      'image/products/food_essentials_commodities/canned_food/canned_meat/canned_luncheon_meat.jpg',
      'Canned Luncheon Meat',
      '2025-08-04 12:46:59'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FEC-CF-CS-066'),
      'image/products/food_essentials_commodities/canned_food/canned_seafood/canned_tuna_chunks.jpg',
      'Canned Tuna Chunks',
      '2025-08-05 08:46:47'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FEC-RX-BR-067'),
      'image/products/food_essentials_commodities/rice/brown_rice/brown_rice.jpg',
      'Brown Rice',
      '2025-08-26 14:37:16'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FEC-RX-GR-068'),
      'image/products/food_essentials_commodities/rice/glutinous_rice/glutinous_rice.jpg',
      'Glutinous Rice',
      '2025-09-01 11:46:45'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='FEC-RX-WR-069'),
      'image/products/food_essentials_commodities/rice/white_rice/white_rice.jpg',
      'White Rice',
      '2025-08-13 21:36:25'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='SX-BC-BCW-070'),
      'image/products/snacks/biscuits_cookies/butter_cookies_with_chocolate_chips.jpg',
      'Butter Cookies with Chocolate Chips',
      '2025-08-05 14:07:16'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='SX-CX-CX-071'),
      'image/products/snacks/chocolates/milk_chocolate_bar.jpg',
      'Milk Chocolate Bar',
      '2025-08-24 22:31:51'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='SX-CS-CS-072'),
      'image/products/snacks/candies_sweets/fruit_gummies.jpg',
      'Fruit Gummies',
      '2025-08-12 00:59:07'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='SX-CC-PAC-073'),
      'image/products/snacks/chips_crisps/potato_and_corn_chips/classic_potato_chips.jpg',
      'Classic Potato Chips',
      '2025-08-25 09:33:10'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='SX-CC-CS-074'),
      'image/products/snacks/chips_crisps/canister_snacks/stacked_potato_crisps.jpg',
      'Stacked Potato Crisps',
      '2025-08-08 18:52:39'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='SX-CC-RTS-075'),
      'image/products/snacks/chips_crisps/ring_twisted_snacks/cheese_rings.jpg',
      'Cheese Rings',
      '2025-08-17 17:48:02'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='BX-CD-SD-077'),
      'image/products/beverages/carbonated_drinks/cola_flavoured_soda.jpg',
      'Cola Flavoured Soda',
      '2025-08-26 10:20:52'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='BX-CX-BAG-082'),
      'image/products/beverages/coffee/beans_and_ground/medium_roast_coffee_beans.jpg',
      'Medium Roast Coffee Beans',
      '2025-08-24 09:03:28'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='BX-CX-IC-083'),
      'image/products/beverages/coffee/instant_coffee/classic_instant_coffee.jpg',
      'Classic Instant Coffee',
      '2025-08-29 07:31:51'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='BX-CX-RTD-085'),
      'image/products/beverages/coffee/ready/to/drink_coffee/iced_latte_bottle.jpg',
      'Iced Latte Bottle',
      '2025-08-17 02:39:41'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='BX-WX-MW-090'),
      'image/products/beverages/water/natural_mineral_water.jpg',
      'Natural Mineral Water',
      '2025-09-02 02:35:02'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='HP-PP-BR-097'),
      'image/products/household_products/paper_products/bathroom_roll/bathroom_tissue_rolls.jpg',
      'Bathroom Tissue Rolls',
      '2025-08-05 05:21:20'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='HP-PP-FT-098'),
      'image/products/household_products/paper_products/facial_tissue/soft_facial_tissue.jpg',
      'Soft Facial Tissue',
      '2025-08-22 17:24:47'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='HP-PP-KR-099'),
      'image/products/household_products/paper_products/kitchen_roll/kitchen_towels.jpg',
      'Kitchen Towels',
      '2025-08-09 04:33:49'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='HP-PP-PT-100'),
      'image/products/household_products/paper_products/pocket_tissue/pocket_tissues.jpg',
      'Pocket Tissues',
      '2025-08-14 18:20:54'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='HP-PP-WX-101'),
      'image/products/household_products/paper_products/wipes/antibacterial_wet_wipes.jpg',
      'Antibacterial Wet Wipes',
      '2025-08-27 06:14:58'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='HP-LX-BX-102'),
      'image/products/household_products/laundry/bleach/fabric_safe_bleach.jpg',
      'Fabric-Safe Bleach',
      '2025-08-19 15:35:26'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='HP-LX-SAD-103'),
      'image/products/household_products/laundry/softener_and_delicate_care/clothing_softener.jpg',
      'Clothing Softener',
      '2025-08-29 19:20:17'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='HP-LX-DX-104'),
      'image/products/household_products/laundry/detergent/laundry_detergent.jpg',
      'Laundry Detergent',
      '2025-08-10 01:30:15'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='HP-LX-LA-105'),
      'image/products/household_products/laundry/laundry_accessories/laundry_mesh_bag.jpg',
      'Laundry Mesh Bag',
      '2025-08-12 08:13:49'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='HP-HCA-SS-106'),
      'image/products/household_products/home_cleaning_accessories/sponge_scourer/non_scratch_scouring_sponge.jpg',
      'Non-Scratch Scouring Sponge',
      '2025-08-21 01:24:43'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='HP-HCA-MX-107'),
      'image/products/household_products/home_cleaning_accessories/mops/microfiber_mop.jpg',
      'Microfiber Mop',
      '2025-08-09 21:24:10'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='HP-HCA-BDS-108'),
      'image/products/household_products/home_cleaning_accessories/brooms_dust_scoop/broom_and_dustpan_set.jpg',
      'Broom and Dustpan Set',
      '2025-08-22 18:59:24'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='HP-HCA-GX-110'),
      'image/products/household_products/home_cleaning_accessories/gloves/household_cleaning_gloves.jpg',
      'Household Cleaning Gloves',
      '2025-08-08 05:16:52'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='HP-HCA-PWD-111'),
      'image/products/household_products/home_cleaning_accessories/pail_water_dipper/cleaning_pail_with_dipper.jpg',
      'Cleaning Pail with Dipper',
      '2025-08-20 21:54:35'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='HP-HCA-DCC-112'),
      'image/products/household_products/home_cleaning_accessories/duster_cleaning_cloth/microfiber_cleaning_cloth.jpg',
      'Microfiber Cleaning Cloth',
      '2025-08-04 06:07:15'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='BH-BC-SSA-113'),
      'image/products/beauty_health/body_care/soaps_scrubs_and_gels/moisturizing_body_wash.jpg',
      'Moisturizing Body Wash',
      '2025-08-04 07:31:54'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='BH-BC-BLA-114'),
      'image/products/beauty_health/body_care/body_lotion_and_cream/hydrating_body_lotion.jpg',
      'Hydrating Body Lotion',
      '2025-08-18 14:31:19'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='BH-BC-DAF-115'),
      'image/products/beauty_health/body_care/deo_and_fragrances/long_lasting_deodorant.jpg',
      'Long-Lasting Deodorant',
      '2025-08-18 10:35:28'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='BH-BC-HAF-116'),
      'image/products/beauty_health/body_care/hand_and_foot_care/nourishing_hand_cream.jpg',
      'Nourishing Hand Cream',
      '2025-08-11 02:34:23'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='BH-BC-SX-117'),
      'image/products/beauty_health/body_care/skincare/gentle_facial_cleanser.jpg',
      'Gentle Facial Cleanser',
      '2025-08-06 07:06:04'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='BH-HC-CX-118'),
      'image/products/beauty_health/hair_care/conditioner/hair_repair_conditioner.jpg',
      'Hair Repair Conditioner',
      '2025-08-09 11:24:05'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='BH-HC-SX-119'),
      'image/products/beauty_health/hair_care/shampoo/daily_care_shampoo.jpg',
      'Daily Care Shampoo',
      '2025-08-08 11:10:54'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='BH-HC-HS-120'),
      'image/products/beauty_health/hair_care/hair_styling/strong_hold_hair_gel.jpg',
      'Strong Hold Hair Gel',
      '2025-08-25 11:54:06'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='BH-HC-HCT-121'),
      'image/products/beauty_health/hair_care/hair_colour_treatments/natural_brown_hair_color.jpg',
      'Natural Brown Hair Color',
      '2025-08-05 18:35:04'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='BH-HC-HST-122'),
      'image/products/beauty_health/hair_care/hair_scalp_treatments/hair_scalp_tonic.jpg',
      'Hair Scalp Tonic',
      '2025-08-23 17:04:26'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='BH-OC-MX-127'),
      'image/products/beauty_health/oral_care/mouthwash/fresh_mint_mouthwash.jpg',
      'Fresh Mint Mouthwash',
      '2025-08-25 19:14:17'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='BH-OC-TA-128'),
      'image/products/beauty_health/oral_care/toothbrush_accessories/soft_bristle_toothbrush.jpg',
      'Soft Bristle Toothbrush',
      '2025-08-07 03:37:32'
    );

INSERT INTO product_images (product_id, image_url, alt_text, created_at)
    VALUES (
      (SELECT product_id FROM products WHERE sku='BH-OC-TX-129'),
      'image/products/beauty_health/oral_care/toothpaste/whitening_toothpaste.jpg',
      'Whitening Toothpaste',
      '2025-08-13 23:17:56'
    );

/* Wishlist */
/* Sample Wishlist Data for Alice (user_id = 1) */

INSERT INTO wishlists (user_id, product_id, created_at) VALUES
(1, (SELECT product_id FROM products WHERE sku='FP-VX-AS-001'), '2025-08-30 11:00:00'),
(1, (SELECT product_id FROM products WHERE sku='FP-VX-BP-002'), '2025-08-17 12:00:00'),
(1, (SELECT product_id FROM products WHERE sku='FP-VX-BC-003'), '2025-08-04 03:10:00'),
(1, (SELECT product_id FROM products WHERE sku='FP-VX-CP-004'), '2025-08-04 03:20:00'),
(1, (SELECT product_id FROM products WHERE sku='FEC-RX-WR-069'), '2025-08-13 22:00:00'),
(1, (SELECT product_id FROM products WHERE sku='BX-WX-MW-090'), '2025-09-02 03:15:00'),
(1, (SELECT product_id FROM products WHERE sku='HP-LX-LA-105'), '2025-08-12 09:00:00');

/* Shipping Rates */
INSERT INTO shipping_rates (postcode, shipping_fee, delivery_duration) VALUES
-- All shipping fees are inclusive of SST charges, where applicable
-- Federal Territories
('50xxx', 5.00, '1-2 days'),  -- Kuala Lumpur
('62xxx', 5.00, '1-2 days'),  -- Putrajaya
('87xxx', 5.00, '1-2 days'),  -- Labuan

-- West Malaysia (Peninsular)
('10xxx', 7.00, '1-3 days'),  -- Penang
('30xxx', 7.00, '1-3 days'),  -- Perak
('79xxx', 8.00, '2-3 days'),  -- Johor
('75xxx', 8.00, '2-3 days'),  -- Melaka
('70xxx', 7.50, '2-3 days'),  -- Negeri Sembilan
('25xxx', 7.50, '2-3 days'),  -- Pahang
('20xxx', 8.00, '2-3 days'),  -- Terengganu
('15xxx', 8.00, '2-3 days'),  -- Kelantan
('05xxx', 8.50, '2-3 days'),  -- Kedah
('01xxx', 8.50, '2-3 days'),  -- Perlis

-- East Malaysia
('88xxx', 15.00, '3-5 days'), -- Sabah
('93xxx', 15.00, '3-5 days'); -- Sarawak

/* Vouchers */
INSERT INTO vouchers 
(code, description, img_url, terms_conditions, discount_type, discount_value, min_order_amount, max_discount, valid_from, valid_until, active, used)
VALUES
('NEWUSER123', '10% off for first order', '/images/vouchers/newuser123.png', 'Valid for new users only. Minimum spend RM30.', 'percent', 10.00, 30.00, 25.00, '2025-08-01', '2025-12-31', TRUE, FALSE),
('RM5OFF', 'Flat RM5 off any order under Fresh Produces products above RM20', '/images/vouchers/rm5off.png', 'Minimum spend RM20.', 'fixed', 5.00, 20.00, NULL, '2025-08-01', '2025-09-30', TRUE, FALSE);

/* Voucher Usages */
-- Since only Alice applied the NEWUSER123 voucher, hence only 1 record here for the used voucher's details
/* 
1) Alice (user_id=1)
Used voucher NEWUSER123 (10% off first order).
Order #1 applied correctly with RM4.77 discount.

2) Bob (user_id=2)
Did not use any voucher.
Order #2 subtotal RM15, shipping RM8, no discounts applied.
*/
INSERT INTO voucher_usages (voucher_id, user_id, order_id, used_at) VALUES
((SELECT voucher_id FROM vouchers WHERE code='NEWUSER123'), 1, 1, '2025-09-01 11:00:00');

/* Orders */
/* 
1) Alice:
Subtotal = asparagus (8.9 × 2) + white rice (29.9 × 1) = 47.70
Discount = 10% (NEWUSER123) = 4.77
Shipping = RM5.00 (KL)
Grand total = 47.70 - 4.77 + 5.00 = 47.93

2) Bob:
Subtotal = mineral water (2.50 × 6) = 15.00
No discount, shipping = RM8.00 (Johor)
Grand total = 15.00 + 8.00 = 23.00
*/
INSERT INTO orders (user_id, address_id, status, payment_method, voucher_id, subtotal, discount_total, shipping_fee, delivery_duration, placed_at)
VALUES
(1, 2, 'paid', 'card', (SELECT voucher_id FROM vouchers WHERE code='NEWUSER123'), 47.70, 4.77, 5.00, '1-2 days', '2025-09-01 11:00:00'),
(2, 3, 'delivered', 'grabpay', NULL, 15.00, 0.00, 8.00, '2-3 days', '2025-09-02 14:30:00');

/* Order Items */
INSERT INTO order_items (order_id, product_id, product_name, sku, unit_price, quantity, line_discount, line_total)
VALUES
-- Alice’s order
(1, (SELECT product_id FROM products WHERE sku='FP-VX-AS-001'), 'Green Asparagus Bunch', 'FP-VX-AS-001', 8.90, 2, 0.00, 17.80),
(1, (SELECT product_id FROM products WHERE sku='FEC-RX-WR-069'), 'White Rice', 'FEC-RX-WR-069', 29.90, 1, 0.00, 29.90),

-- Bob’s order
(2, (SELECT product_id FROM products WHERE sku='BX-WX-MW-090'), 'Natural Mineral Water', 'BX-WX-MW-090', 2.50, 6, 0.00, 15.00);

/* Cart Items */
INSERT INTO cart_items (user_id, product_id, product_name, sku, unit_price, quantity, line_discount, voucher_id, added_at, updated_at)
VALUES
(1, (SELECT product_id FROM products WHERE sku='FP-VX-AS-001'), 'Green Asparagus Bunch', 'FP-VX-AS-001', 8.90, 2, 0.00, NULL, '2025-09-01 10:00:00', '2025-09-01 10:00:00'),
(1, (SELECT product_id FROM products WHERE sku='FEC-RX-WR-069'), 'White Rice', 'FEC-RX-WR-069', 29.90, 1, 0.00, (SELECT voucher_id FROM vouchers WHERE code='NEWUSER123'), '2025-09-01 10:05:00', '2025-09-01 10:05:00'),
(1, (SELECT product_id FROM products WHERE sku='BH-BC-SX-117'), 'Gentle Facial Cleanser', 'BH-BC-SX-117', 12.90, 3, 0.00, NULL, '2025-09-02 03:10:00', '2025-09-02 10:05:00'),
(2, (SELECT product_id FROM products WHERE sku='BX-WX-MW-090'), 'Natural Mineral Water', 'BX-WX-MW-090', 2.50, 6, 0.00, NULL, '2025-09-01 12:00:00', '2025-09-01 12:00:00');

/* Contact form */
INSERT INTO contact_messages (user_id, name, email, phone, subject, comment, image_url) VALUES
(1, 'Alice', 'alice@example.com', '+60123456789', 'Inquiry', 'I have a question about product SKU001', NULL),
(2, 'Bob', 'bob@example.com', '+60198765432', 'Feedback', 'Great website!', NULL)
