<?php
// Start the session
session_start();

// Include database connection
require_once 'includes/db_connection.php';
require_once 'includes/functions.php';

// Determine which page to load
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Include header
include 'includes/header.php';

// Load the appropriate page content
switch ($page) {
    case 'home':
        include 'pages/home.php';
        break;
    case 'product':
        include 'pages/product.php';
        break;
    case 'cart':
        include 'pages/cart.php';
        break;
    case 'checkout':
        include 'pages/checkout.php';
        break;
    case 'order-tracking':
        include 'pages/order-tracking.php';
        break;
    case 'admin-dashboard':
        include 'pages/admin/dashboard.php';
        break;
    case 'admin-order-details':
        include 'pages/admin/order-details.php';
        break;
    default:
        include 'pages/not-found.php';
        break;
}

// Include footer
include 'includes/footer.php';
?>