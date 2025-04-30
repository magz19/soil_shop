<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection and functions
require_once '../includes/db_connection.php';

// Check if shipping and payment info exist in session
if (!isset($_SESSION['checkout_shipping']) || !isset($_SESSION['checkout_payment'])) {
    $_SESSION['error'] = 'Missing shipping or payment information.';
    header('Location: ../index.php?page=checkout');
    exit;
}

// Get user ID (in a real app, this would come from session)
$userId = 1;

// Get user cart
$cart = getCartWithProducts($userId);

// Check if cart is empty
if (!$cart || empty($cart['items'])) {
    $_SESSION['error'] = 'Your cart is empty.';
    header('Location: ../index.php?page=cart');
    exit;
}

// Get shipping and payment info from session
$shippingInfo = $_SESSION['checkout_shipping'];
$paymentInfo = $_SESSION['checkout_payment'];

// Create order
$order = createOrder($userId, $shippingInfo, $paymentInfo, $cart['items']);

if ($order) {
    // Store order ID in session for confirmation page
    $_SESSION['last_order_id'] = $order['id'];
    
    // Clear checkout session data
    unset($_SESSION['checkout_shipping']);
    unset($_SESSION['checkout_payment']);
    
    // Set success message
    $_SESSION['success'] = 'Order placed successfully!';
    
    // Redirect to confirmation page
    header('Location: ../index.php?page=order-confirmation&id=' . $order['id']);
    exit;
} else {
    $_SESSION['error'] = 'Failed to place order.';
    header('Location: ../index.php?page=checkout&step=review');
    exit;
}
?>