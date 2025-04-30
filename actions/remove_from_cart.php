<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection and functions
require_once '../includes/db_connection.php';

// Get form data
$cartItemId = isset($_POST['cart_item_id']) ? (int)$_POST['cart_item_id'] : 0;

// Validate data
if ($cartItemId <= 0) {
    $_SESSION['error'] = 'Invalid cart item ID.';
    header('Location: ../index.php?page=cart');
    exit;
}

// Remove cart item
$result = removeCartItem($cartItemId);

if ($result) {
    $_SESSION['success'] = 'Item removed from cart.';
} else {
    $_SESSION['error'] = 'Failed to remove item from cart.';
}

// Redirect back to cart
header('Location: ../index.php?page=cart');
exit;
?>