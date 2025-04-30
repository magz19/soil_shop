<?php
/**
 * Database Connection for S-Oil Products Store
 */

// Define development mode for error handling
define('DEVELOPMENT_MODE', true);

// Database connection details
$db_host = 'localhost';
$db_user = 'root'; // Default XAMPP MySQL username
$db_pass = '';     // Default XAMPP MySQL password
$db_name = 'soil_shop';

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    // If in development mode, show detailed error
    if (DEVELOPMENT_MODE) {
        die("Connection failed: " . $conn->connect_error);
    } else {
        // In production, show friendly message
        die("We're experiencing technical difficulties. Please try again later.");
    }
}

// Set charset
$conn->set_charset("utf8mb4");