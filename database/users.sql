-- Drop user if exist (for fresh setup)
DROP USER IF EXISTS 'gogrocery_customer'@'localhost';

-- Create customer user (frontend app only)
CREATE USER 'gogrocery_customer'@'localhost' IDENTIFIED BY 'StrongCustomerPassword123!';

-- 🔹 Product browsing (read-only)
GRANT SELECT ON gogrocery.products TO 'gogrocery_customer'@'localhost';
GRANT SELECT ON gogrocery.categories TO 'gogrocery_customer'@'localhost';
GRANT SELECT ON gogrocery.product_images TO 'gogrocery_customer'@'localhost';
GRANT SELECT ON gogrocery.brands TO 'gogrocery_customer'@'localhost';

-- 🔹 Customer account (profile + addresses)
GRANT SELECT, INSERT, UPDATE,DELETE ON gogrocery.users TO 'gogrocery_customer'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON gogrocery.addresses TO 'gogrocery_customer'@'localhost';

-- 🔹 Orders & order items
GRANT SELECT, INSERT, UPDATE ON gogrocery.orders TO 'gogrocery_customer'@'localhost';
GRANT SELECT, INSERT ON gogrocery.order_items TO 'gogrocery_customer'@'localhost';

-- 🔹 Vouchers & Voucher Usages
GRANT SELECT, INSERT, UPDATE ON gogrocery.vouchers TO 'gogrocery_customer'@'localhost';
GRANT SELECT, INSERT, UPDATE ON gogrocery.user_vouchers TO 'gogrocery_customer'@'localhost';

-- 🔹 Wishlists & cart_items
GRANT SELECT, INSERT, DELETE, UPDATE ON gogrocery.wishlist TO 'gogrocery_customer'@'localhost';
GRANT SELECT, INSERT, DELETE, UPDATE ON gogrocery.cart_items TO 'gogrocery_customer'@'localhost';

-- 🔹 Contact form
GRANT SELECT, INSERT ON gogrocery.contact_messages TO 'gogrocery_customer'@'localhost';

/* Roles and usage:

1) gogrocery_customer
Purpose: Limited database user used by the website backend (PHP) when handling customer actions.
Never used for creating/modifying tables or schema changes.
Used by the backend app while developing or running the website; simulates what a real customer can do.

2) gogrocery_dev / root
Purpose: Full privileges for database development and administration.
Creating or modifying tables, indexes, foreign keys.
Running migrations, resetting or cleaning data.
Privileges: ALL PRIVILEGES on the database.
*/