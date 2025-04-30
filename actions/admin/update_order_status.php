<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection and functions
require_once '../../includes/db_connection.php';

// Check if user is admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    $_SESSION['error'] = 'Access denied.';
    header('Location: ../../index.php');
    exit;
}

// Get form data
$orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
$status = isset($_POST['status']) ? trim($_POST['status']) : '';

// Validate data
if ($orderId <= 0) {
    $_SESSION['error'] = 'Invalid order ID.';
    header('Location: ../../index.php?page=admin&admin_page=orders');
    exit;
}

// Validate status
$validStatuses = ['pending', 'processing', 'shipped', 'out_for_delivery', 'delivered', 'cancelled'];
if (empty($status) || !in_array($status, $validStatuses)) {
    $_SESSION['error'] = 'Invalid status.';
    header('Location: ../../index.php?page=admin&admin_page=orders');
    exit;
}

// Update order status
$order = updateOrderStatus($orderId, $status);

if ($order) {
    $_SESSION['success'] = 'Order status updated successfully.';
} else {
    $_SESSION['error'] = 'Failed to update order status.';
}

// Redirect back to admin orders page or order details page
$redirect = '../../index.php?page=admin&admin_page=';
if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'admin_page=order-details') !== false) {
    $redirect .= 'order-details&id=' . $orderId;
} else {
    $redirect .= 'orders';
}

header('Location: ' . $redirect);
exit;
?>