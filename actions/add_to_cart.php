<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection and functions
require_once '../includes/db_connection.php';

// Get form data
$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

// Validate data
if ($productId <= 0 || $quantity <= 0) {
    $_SESSION['error'] = 'Invalid product ID or quantity.';
    header('Location: ../index.php?page=home');
    exit;
}

// Get user ID (in a real app, this would come from session)
$userId = 1;

// Add to cart
$result = addToCart($userId, $productId, $quantity);

if ($result) {
    $_SESSION['success'] = 'Product added to cart.';
    
    // Redirect back to the referring page or to the cart page
    $redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../index.php?page=cart';
    header('Location: ' . $redirect);
    exit;
} else {
    $_SESSION['error'] = 'Failed to add product to cart.';
    header('Location: ../index.php?page=home');
    exit;
}
?>