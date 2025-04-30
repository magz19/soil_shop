<?php
// Database setup script for S-Oil Products Store

// Database configuration
$host = "localhost";    // MySQL server hostname
$username = "root";     // MySQL username
$password = "";         // MySQL password
$database = "soil_shop"; // Your database name

// Create connection without selecting a database
$conn = new mysqli($host, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $database";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully or already exists\n";
} else {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db($database);

// Read and execute the SQL file
$sql = file_get_contents(__DIR__ . '/soil_shop.sql');

// Execute multi-query SQL script
if ($conn->multi_query($sql)) {
    echo "Database tables created and populated successfully\n";
    
    // Process all result sets to clear them
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->more_results() && $conn->next_result());
    
} else {
    echo "Error setting up database: " . $conn->error;
}

$conn->close();
echo "Database setup complete!\n";
?>