GoGrocery E-Commerce Website
============================

GoGrocery is a PHP + MySQL e-commerce web application.  
This repository includes everything needed to set up the project locally:
- Database schema (db.sql)
- User accounts (grocery_dev & grocery_customer)
- .env configuration
- Composer dependencies

------------------------------------------------------------
📋 Requirements
------------------------------------------------------------
- PHP 8.1+ (WAMP, XAMPP, or MAMP includes PHP)
- MySQL 8+ (or MariaDB)
- Composer (https://getcomposer.org/)
- Git
- Web server (Apache/Nginx) or PHP built-in server

------------------------------------------------------------
⚙️ 1. Clone Repository
------------------------------------------------------------
git clone https://github.com/yourusername/gogrocery.git
cd gogrocery

------------------------------------------------------------
📦 2. Install Composer
------------------------------------------------------------
Windows:
1. Download Composer installer → https://getcomposer.org/download/
2. Run the installer and select your PHP executable (from WAMP/XAMPP).
3. Verify installation:
   composer -V

Linux/macOS:
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
sudo mv composer.phar /usr/local/bin/composer
composer -V

------------------------------------------------------------
🗄️ 3. Setup MySQL Database
------------------------------------------------------------
Step 1: Create Database
Log in as root (via phpMyAdmin or CLI):
CREATE DATABASE gogrocery CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

Step 2: Create Users
Run the script provided in users.sql:
mysql -u root -p < users.sql

This creates:
- grocery_customer → frontend customer access
- grocery_dev → admin / development access

Step 3: Import Schema
The schema file db.sql is already prepared.  
Import it as root:
mysql -u root -p gogrocery < db.sql

------------------------------------------------------------
🔑 4. Setup Environment File
------------------------------------------------------------
Duplicate the example:
Linux/macOS:
cp .env.example .env

Windows PowerShell:
copy .env.example .env

Default .env:
DB_HOST=localhost
DB_NAME=gogrocery
DB_USER=grocery_customer
DB_PASS=StrongCustomerPassword123!

# Optional: admin creds (used only for migrations/scripts)
DB_ADMIN_USER=grocery_dev
DB_ADMIN_PASS=StrongDevPassword123!

⚠️ If you change usernames/passwords in users.sql, update them here.

------------------------------------------------------------
📥 5. Install PHP Dependencies
------------------------------------------------------------
composer install

This creates a vendor/ directory (ignored in .gitignore).

------------------------------------------------------------
▶️ 6. Run the Application
------------------------------------------------------------
Option 1: WAMP/XAMPP (Recommended)
- Place project inside C:\wamp64\www\ or C:\xampp\htdocs\
- Visit: http://localhost/gogrocery

Option 2: PHP Built-in Server
php -S localhost:8000 -t public
Then open http://localhost:8000

------------------------------------------------------------
🧪 7. Test Database Connection
------------------------------------------------------------
php config.php

Expected:
✅ Database connection successful!

If you see:
❌ Connection failed: SQLSTATE[HY000] [1045] Access denied
Check:
1. .env credentials
2. Users exist (users.sql executed)
3. db.sql imported into gogrocery

------------------------------------------------------------
📂 Project Structure
------------------------------------------------------------
gogrocery/
│── db.sql            # Database schema (already prepared)
│── users.sql         # Creates MySQL users + grants privileges
│── .env.example      # Template environment config
│── .env              # Your environment file (not committed to git)
│── config.php        # Database connection test
│── composer.json     # Composer dependencies
│── composer.lock     # Composer lockfile
│── public/           # Web root (index.php, assets, etc.)
│── src/              # PHP application code
│── vendor/           # Installed by Composer (ignored in git)

------------------------------------------------------------
🔒 Git Ignore Policy
------------------------------------------------------------
Do not commit:
- .env (secrets)
- vendor/
- composer.lock (optional: commit only if you want locked versions)

------------------------------------------------------------
👥 Developer Notes
------------------------------------------------------------
- Use grocery_dev for migrations/admin tasks
- Use grocery_customer for frontend testing
- Each developer must:
  1. Run users.sql as root
  2. Import db.sql
  3. Create .env
  4. Run composer install

------------------------------------------------------------
✅ Setup Complete
------------------------------------------------------------
You now have GoGrocery running locally 🎉

------------------------------------------------------------
💡 Best Practices
------------------------------------------------------------
- Never commit your .env file.
- Use strong passwords for MySQL users.
- Keep composer.json minimal (only necessary dependencies).
- Run "composer update" sparingly (prefer composer install for stability).
- Use grocery_customer for app usage to avoid damaging schema.

------------------------------------------------------------
🛠️ Troubleshooting
------------------------------------------------------------
Problem: "composer not recognized"
→ Add Composer to PATH or reinstall via installer.

Problem: Access denied for user
→ Check .env matches users.sql credentials.
→ Ensure you imported users.sql as root.

Problem: Database doesn't exist
→ Run CREATE DATABASE gogrocery; or re-import db.sql.

Problem: Website loads blank page
→ Check Apache/PHP error logs.
→ Ensure vendor/ installed (run composer install).
→ Verify .env file exists and is correct.

------------------------------------------------------------
📌 10) Minimal Commands Reference (Copy/Paste)
------------------------------------------------------------

Linux/macOS:
------------
# clone
git clone <repo-url> GoGrocery-Ecommerce
cd GoGrocery-Ecommerce

# install composer (if missing)
php -r "copy('https://getcomposer.org/installer','composer-setup.php');"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
php -r "unlink('composer-setup.php');"

# install packages
composer install

# import db & users
mysql -u root -p < db.sql
mysql -u root -p < users.sql

# secure env
chmod 600 .env

# test
php config.php

# run dev server (optional)
php -S localhost:8000 -t public


Windows (PowerShell):
---------------------
# clone
git clone <repo-url> GoGrocery-Ecommerce
cd .\GoGrocery-Ecommerce\

# install composer: download Composer-Setup.exe and run it,
# then in a new terminal:
composer -V

# install packages
composer install

# import db using mysql.exe from WAMP (adjust path if needed)
# Example using mysql client bundled with WAMP:
& 'C:\wamp64\bin\mysql\mysql8.0.XX\bin\mysql.exe' -u root -p < db.sql
& 'C:\wamp64\bin\mysql\mysql8.0.XX\bin\mysql.exe' -u root -p < users.sql

# create .env
notepad .env

# test
php config.php

# start WAMP and visit in browser:
http://localhost/GoGrocery-Ecommerce/


------------------------------------------------------------
📌 11) Extras & Best Practices
------------------------------------------------------------
- Keep composer.json and composer.lock in git; ignore vendor/.
- Put db.sql in repo only if it’s small; large dumps should be distributed externally.
- Use different passwords for grocery_dev and grocery_customer.
- In production, use server-managed secrets (Docker env / system env / secret manager), not .env files.
- Do not run the app as root or use root DB user in production.
