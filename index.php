<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include header
include 'includes/header.php';

// Get the page from URL
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Determine which page to load
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
        
    case 'orders':
        include 'pages/orders.php';
        break;
        
    case 'admin':
        // Check if user is admin
        if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
            // Get admin page
            $adminPage = isset($_GET['admin_page']) ? $_GET['admin_page'] : 'dashboard';
            
            // Load the appropriate admin page
            switch ($adminPage) {
                case 'dashboard':
                    include 'pages/admin/dashboard.php';
                    break;
                
                case 'orders':
                    include 'pages/admin/orders.php';
                    break;
                
                case 'products':
                    include 'pages/admin/products.php';
                    break;
                
                default:
                    include 'pages/admin/dashboard.php';
                    break;
            }
        } else {
            // Redirect to home if not admin
            echo '<script>window.location.href = "index.php";</script>';
        }
        break;
        
    case 'order-confirmation':
        include 'pages/order-confirmation.php';
        break;
        
    default:
        // Load 404 page
        include 'pages/404.php';
        break;
}

// Include footer
include 'includes/footer.php';
?>