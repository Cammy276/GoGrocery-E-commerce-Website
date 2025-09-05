<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Choose which DB user to use
// For normal app usage (customer-facing operations)
$dbUser = $_ENV['DB_USER'] ?? 'gogrocery_customer';
$dbPass = $_ENV['DB_PASS'] ?? 'StrongCustomerPassword123!';

// Uncomment below to use admin/root for migrations or schema changes
// $dbUser = $_ENV['DB_ADMIN_USER'] ?? 'root';
// $dbPass = $_ENV['DB_ADMIN_PASS'] ?? '';

// Database connection parameters
$servername = $_ENV['DB_HOST'] ?? 'localhost';
$dbname     = $_ENV['DB_NAME'] ?? 'gogrocery';
$port       = $_ENV['DB_PORT'] ?? 3306;

// Create connection
$conn = new mysqli($servername, $dbUser, $dbPass, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

return $conn;
