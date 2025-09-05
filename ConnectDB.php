<?php
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "gogrocery-e-commerce-website"; 

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

return $conn;
