<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection and functions
require_once '../includes/db_connection.php';

// Get form data
$cartItemId = isset($_POST['cart_item_id']) ? (int)$_POST['cart_item_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

// Validate data
if ($cartItemId <= 0 || $quantity <= 0) {
    $_SESSION['error'] = 'Invalid cart item ID or quantity.';
    header('Location: ../index.php?page=cart');
    exit;
}

// Update cart item
$result = updateCartItemQuantity($cartItemId, $quantity);

if ($result) {
    $_SESSION['success'] = 'Cart updated.';
} else {
    $_SESSION['error'] = 'Failed to update cart.';
}

// Redirect back to cart
header('Location: ../index.php?page=cart');
exit;
?>