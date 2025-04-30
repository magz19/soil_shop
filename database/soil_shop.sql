-- S-Oil Products Store Database

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
  username VARCHAR(50) NOT NULL,
  password VARCHAR(255) NOT NULL,
  email VARCHAR(100) NOT NULL,
  full_name VARCHAR(100) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create products table
CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  price DECIMAL(10, 2) NOT NULL,
  sale_price DECIMAL(10, 2) NULL,
  image_url VARCHAR(255) NOT NULL,
  category VARCHAR(100) NOT NULL,
  in_stock TINYINT(1) DEFAULT 1,
  rating DECIMAL(3, 1) NULL,
  review_count INT NULL,
  is_prime TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create carts table
CREATE TABLE carts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
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
  user_id INT NOT NULL,
  total DECIMAL(10, 2) NOT NULL,
  status ENUM('pending', 'processing', 'shipped', 'out_for_delivery', 'delivered', 'cancelled') DEFAULT 'pending',
  shipping_address VARCHAR(255) NOT NULL,
  shipping_city VARCHAR(100) NOT NULL,
  shipping_state VARCHAR(100) NOT NULL,
  shipping_zip VARCHAR(20) NOT NULL,
  shipping_method ENUM('pickup', 'delivery') NOT NULL,
  payment_method ENUM('gcash', 'counter') NOT NULL,
  payment_screenshot VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create order_items table
CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  quantity INT NOT NULL,
  price DECIMAL(10, 2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Insert test user
INSERT INTO users (username, password, email, full_name) VALUES
('user1', '$2y$10$8WjwU9OMRp6N3ocYQcsYEOrn/qMW7vZjOULKxBLz3H3WGkHOdJLQS', 'user@example.com', 'Test User');
-- Password is 'password' (hashed with bcrypt)

-- Insert S-Oil products
INSERT INTO products (name, description, price, sale_price, image_url, category, in_stock, rating, review_count, is_prime) VALUES
('S-Oil Ultra Synthetic 5W-30 Motor Oil', 'Fully synthetic premium motor oil that provides outstanding performance and protection. Ideal for modern engines.', 29.99, 24.99, 'https://images.unsplash.com/photo-1613177794106-be20802b11d3?auto=format&fit=crop&w=400&h=300', 'Motor Oil', 1, 4.8, 234, 1),

('S-Oil Seven Dragon 10W-40 Semi-Synthetic Oil', 'Semi-synthetic oil that offers excellent protection against engine wear and tear. Ideal for high-mileage vehicles.', 19.99, 17.99, 'https://images.unsplash.com/photo-1613177794106-be20802b11d3?auto=format&fit=crop&w=400&h=300', 'Motor Oil', 1, 4.5, 189, 1),

('S-Oil Automotive Filter', 'High-quality filter that efficiently removes contaminants from your engine oil. Extends engine life and improves performance.', 9.99, 7.50, 'https://images.unsplash.com/photo-1635784063504-1a0c869c8cf4?auto=format&fit=crop&w=400&h=300', 'Filters', 1, 4.3, 102, 1),

('S-Oil Premium Brake Fluid DOT4', 'High-performance brake fluid with superior thermal stability. Ensures reliable braking system performance under various conditions.', 12.99, NULL, 'https://images.unsplash.com/photo-1621939514649-280e2ee25f60?auto=format&fit=crop&w=400&h=300', 'Brake Fluids', 1, 4.7, 156, 1),

('S-Oil Coolant/Antifreeze', 'Advanced formula coolant that provides year-round protection against freezing and overheating. Compatible with all types of radiators.', 14.99, 12.99, 'https://images.unsplash.com/photo-1624984673480-e2ea2e588462?auto=format&fit=crop&w=400&h=300', 'Coolants', 1, 4.6, 87, 1),

('S-Oil Heavy Duty Diesel Engine Oil 15W-40', 'Specially formulated for diesel engines in heavy-duty applications. Provides excellent soot control and wear protection.', 39.99, 34.99, 'https://images.unsplash.com/photo-1613177794106-be20802b11d3?auto=format&fit=crop&w=400&h=300', 'Motor Oil', 1, 4.9, 210, 1),

('S-Oil Transmission Fluid ATF', 'High-quality automatic transmission fluid that ensures smooth shifting and optimal transmission performance.', 16.99, NULL, 'https://images.unsplash.com/photo-1613177794106-be20802b11d3?auto=format&fit=crop&w=400&h=300', 'Transmission Fluids', 1, 4.4, 76, 1),

('S-Oil Grease Lubricant', 'Multipurpose grease for automotive and industrial applications. Provides excellent lubrication and protection against wear.', 8.99, 6.99, 'https://images.unsplash.com/photo-1621939514649-280e2ee25f60?auto=format&fit=crop&w=400&h=300', 'Lubricants', 1, 4.2, 63, 0);

-- Create a cart for the test user
INSERT INTO carts (user_id) VALUES (1);