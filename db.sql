-- CREATING THE DATABASE --
CREATE DATABASE `GoGrocery-E-commerce-Website` CHARACTER SET utf8 COLLATE utf8_general_ci;

USE `GoGrocery-E-commerce-Website`;

CREATE TABLE user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone_number VARCHAR(15) UNIQUE NOT NULL,  
    password_hash VARCHAR(255) NOT NULL,  
    reset_token_hash VARCHAR(64),  
    reset_token_expires_at DATETIME,  
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
