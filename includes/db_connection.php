<?php
// Database configuration
$host = "localhost";    // MySQL server hostname (usually localhost)
$username = "root";     // MySQL username (default for XAMPP is root)
$password = "";         // MySQL password (default for XAMPP is blank)
$database = "soil_shop"; // Your database name

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to ensure proper handling of special characters
$conn->set_charset("utf8mb4");
?>