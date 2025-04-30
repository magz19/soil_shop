# S-Oil Products Store

A PHP-based e-commerce platform for S-Oil products, featuring product browsing, cart management, checkout flow with payment screenshot uploads, and an admin dashboard for order management.

## Features

- Customer-facing storefront with product browsing by category
- Shopping cart functionality
- Checkout process with shipping options:
  - Personal Pick-up
  - Grab/Lalamove Delivery (arranged by client)
- Payment options:
  - GCash with payment screenshot upload
  - Over-the-counter payment
- Order tracking and history
- Admin dashboard for order management
- All prices displayed in Philippine Peso (₱)

## Installation

### Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache, Nginx, etc.)
- XAMPP/WAMP/MAMP (for local development)

### Installation Steps

1. Clone or download this repository to your web server's document root
2. Make sure your web server and MySQL server are running
3. Navigate to the installation wizard in your browser:
   ```
   http://localhost/s-oil-products/install.php
   ```
4. Follow the on-screen instructions to set up the database
5. After installation is complete, you can access your store:
   ```
   http://localhost/s-oil-products/
   ```

### Admin Access

To access the admin dashboard, you need to set up admin privileges:

1. Visit the utility page to make your current session an admin:
   ```
   http://localhost/s-oil-products/utility/make_admin.php
   ```
2. After being granted admin status, access the admin dashboard:
   ```
   http://localhost/s-oil-products/index.php?page=admin
   ```

## Configuration

Database configuration is managed through the `includes/config.php` file, which is created during installation. If you need to modify your database connection settings, edit this file with your updated credentials.

## Directory Structure

- `/assets` - CSS, JavaScript, and image files
- `/actions` - PHP scripts for handling form submissions
- `/includes` - Core PHP functions and includes
- `/pages` - PHP templates for each page
- `/uploads` - Directory for storing user-uploaded files (payment screenshots)
- `/database` - SQL files for database setup

## S-Oil Store Details

### Shipping Options
- Personal Pick-up at "123 Mendiola St. Manila City"
- Contact Person: Anjhela Geron 09454545
- Grab/Lalamove Delivery (arranged by client)

### Payment Options
- GCash (Martin Magno 091234567)
- Over-the-counter payment

## Security Considerations

- For production use, add proper authentication and authorization
- Implement proper validation for all form inputs
- Consider adding HTTPS for secure data transmission
- Remove or secure the installation and utility files after setup

## Troubleshooting

- If you encounter database connection issues, verify your database credentials in `includes/config.php`
- For permission issues with file uploads, ensure the `/uploads` directory is writable by the web server
- Check PHP error logs for detailed error messages