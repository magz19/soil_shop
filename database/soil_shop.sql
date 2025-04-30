-- SQL Script for S-Oil Products Store
-- Run this script to create the necessary database tables

-- Create database (if it doesn't exist)
CREATE DATABASE IF NOT EXISTS soil_shop;

-- Use the database
USE soil_shop;

-- Drop tables if they exist
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS cart_items;
DROP TABLE IF EXISTS carts;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS users;

-- Create users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    zip VARCHAR(20),
    is_admin TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create products table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image_url VARCHAR(255),
    category VARCHAR(50) NOT NULL,
    stock_quantity INT DEFAULT 0,
    is_featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create carts table
CREATE TABLE carts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create cart_items table
CREATE TABLE cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cart_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Create orders table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total DECIMAL(10,2) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    payment_method VARCHAR(50) NOT NULL,
    payment_screenshot VARCHAR(255),
    shipping_method VARCHAR(50) NOT NULL,
    shipping_address TEXT,
    shipping_city VARCHAR(50),
    shipping_state VARCHAR(50),
    shipping_zip VARCHAR(20),
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Create order_items table
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Insert sample admin user (password is 'admin123')
INSERT INTO users (username, password, email, first_name, last_name, is_admin)
VALUES ('admin', '$2y$10$8SOOPxr6.f5s1tgQK38Sz.9eRNPfm0.UjWQvfJQ1VTS90QNkHLJQe', 'admin@example.com', 'Admin', 'User', 1);

-- Insert sample S-Oil products
INSERT INTO products (name, description, price, image_url, category, stock_quantity, is_featured) VALUES
('S-Oil Ultra Synthetic 5W-30 Motor Oil', 'Premium fully synthetic motor oil designed for high-performance engines. Provides superior wear protection and improved fuel efficiency.', 1999.00, 'assets/images/products/oil1.jpg', 'engine_oil', 100, 1),
('S-Oil Seven Gold 5W-40 Motor Oil', 'High-quality synthetic motor oil that protects engines from wear and tear, while ensuring smooth performance in all conditions.', 1799.00, 'assets/images/products/oil2.jpg', 'engine_oil', 85, 1),
('S-Oil ATF Multi Vehicle Transmission Fluid', 'Automatic transmission fluid suitable for most Asian and American vehicles. Ensures smooth shifting and helps extend transmission life.', 899.00, 'assets/images/products/transmission1.jpg', 'transmission_fluid', 65, 1),
('S-Oil Super Gear Oil 80W-90', 'Heavy-duty gear oil for manual transmissions and differentials. Excellent protection against wear, corrosion, and oxidation.', 799.00, 'assets/images/products/transmission2.jpg', 'transmission_fluid', 50, 0),
('S-Oil DOT 4 Brake Fluid', 'High-performance brake fluid with high boiling point for reliable braking in demanding conditions. Compatible with all vehicles requiring DOT 4 fluid.', 499.00, 'assets/images/products/brake1.jpg', 'brake_fluid', 120, 1),
('S-Oil DOT 3 Brake Fluid', 'Standard brake fluid suitable for most vehicles. Provides consistent braking performance and protects against corrosion.', 399.00, 'assets/images/products/brake2.jpg', 'brake_fluid', 90, 0),
('S-Oil Long Life Coolant', 'Extended-life coolant and antifreeze that protects against overheating and freezing. Compatible with all vehicle types.', 699.00, 'assets/images/products/coolant1.jpg', 'coolant', 75, 1),
('S-Oil Pink Antifreeze Coolant', 'Premium antifreeze and coolant with corrosion inhibitors. Helps maintain optimal engine temperature in all seasons.', 599.00, 'assets/images/products/coolant2.jpg', 'coolant', 60, 0);