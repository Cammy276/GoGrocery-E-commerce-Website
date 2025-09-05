-- Drop users if they exist (for fresh setup)
DROP USER IF EXISTS 'grocery_dev'@'localhost';
DROP USER IF EXISTS 'grocery_customer'@'localhost';

-- Developer user: full privileges
CREATE USER 'grocery_dev'@'localhost' IDENTIFIED BY 'StrongDevPassword123!';
GRANT ALL PRIVILEGES ON gogrocery.* TO 'grocery_dev'@'localhost';

-- Customer user: restricted privileges
CREATE USER 'grocery_customer'@'localhost' IDENTIFIED BY 'StrongCustomerPassword123!';
GRANT SELECT, INSERT, UPDATE ON gogrocery.* TO 'grocery_customer'@'localhost';

-- Apply changes
FLUSH PRIVILEGES;


-- Customer User (limited access for frontend app)
CREATE USER 'gogrocery_customer'@'localhost' IDENTIFIED BY 'StrongCustomerPassword123!';
GRANT SELECT ON gogrocery.products TO 'gogrocery_customer'@'localhost';
GRANT SELECT, INSERT ON gogrocery.orders TO 'gogrocery_customer'@'localhost';
GRANT SELECT, INSERT, UPDATE ON gogrocery.customers TO 'gogrocery_customer'@'localhost';
-- (Optional: grant INSERT on payments if customers need to record payments)
-- GRANT INSERT ON gogrocery.payments TO 'gogrocery_customer'@'%';
-- Grants for browsing products & images
GRANT SELECT ON gogrocery.products TO 'gogrocery_customer'@'localhost';
GRANT SELECT ON gogrocery.categories TO 'gogrocery_customer'@'localhost';
GRANT SELECT ON gogrocery.product_images TO 'gogrocery_customer'@'localhost';

-- Grants for customer account management (profile updates)
GRANT SELECT, INSERT, UPDATE ON gogrocery.customers TO 'gogrocery_customer'@'localhost';
GRANT SELECT, INSERT, UPDATE ON gogrocery.addresses TO 'gogrocery_customer'@'localhost';

-- Grants for ordering & payments (app will INSERT orders, payments)
GRANT SELECT, INSERT ON gogrocery.orders TO 'gogrocery_customer'@'localhost';
GRANT SELECT, INSERT ON gogrocery.order_items TO 'gogrocery_customer'@'localhost';
GRANT INSERT ON gogrocery.payments TO 'gogrocery_customer'@'localhost';

-- Grants for wishlists & ratings (typical customer features)
GRANT SELECT, INSERT, DELETE ON gogrocery.wishlists TO 'gogrocery_customer'@'localhost';
-- GRANT SELECT, INSERT ON gogrocery.product_ratings TO 'gogrocery_customer'@'localhost';

-- Apply all changes
FLUSH PRIVILEGES;

-- ========================================================================
-- Database Users Management:
/* 'gogrocery_dev' 
-> developer/admin, safer than using root
-> Creating/modifying tables, Adding new columns, Updating products in bulk
-> Connect manually (phpMyAdmin)
-> Not used by your app in production
*/

/*'gogrocery_customer' 
-> for frontend/backend application code
-> Connection string in your app (PHP, Node.js, Python, etc.) will use this account.
->> All customer-facing operations (register, login, browse products, place order, checkout) go through this user.
-> Ensures the app can’t accidentally drop tables or mess with schema, because its privileges are limited.
*/

/*All queries is executed using the gogrocery_customer MySQL user (the one you created earlier), not Alice’s personal login.
Real customers sign up inside your website → stored in customers table → handled by your app logic (MySQL using gogrocery_customer)
Not letting every real customer be a MySQL user -> Managing permissions for each user is a nightmare. Security risks if one leaks credentials.
It’s the “bridge” between your frontend app and MySQL.
*/

-- - Keep root user only for system-level MySQL tasks (never for apps).
-- ========================================================================
