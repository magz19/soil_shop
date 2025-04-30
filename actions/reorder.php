<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection and functions
require_once '../includes/db_connection.php';

// Get order ID from form
$orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;

// Validate order ID
if ($orderId <= 0) {
    $_SESSION['error'] = 'Invalid order ID.';
    header('Location: ../index.php?page=orders');
    exit;
}

// Get user ID (in a real app, this would come from session)
$userId = 1;

// Get order with items
$order = getOrderWithItems($orderId);

// Check if order exists and belongs to the user
if (!$order || $order['userId'] != $userId) {
    $_SESSION['error'] = 'Order not found.';
    header('Location: ../index.php?page=orders');
    exit;
}

// Add each item from the order to the cart
$success = true;
foreach ($order['items'] as $item) {
    $result = addToCart($userId, $item['productId'], $item['quantity']);
    if (!$result) {
        $success = false;
    }
}

if ($success) {
    $_SESSION['success'] = 'Items added to cart.';
    header('Location: ../index.php?page=cart');
} else {
    $_SESSION['error'] = 'Failed to add some items to cart.';
    header('Location: ../index.php?page=orders');
}
exit;
?>