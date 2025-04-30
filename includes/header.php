<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the database connection and functions
require_once 'includes/db_connection.php';

// Get the page from URL
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Get cart item count for the current user
$userId = 1; // Default user ID for now (until we implement authentication)
$cart = getCartWithProducts($userId);
$cartItemCount = $cart ? count($cart['items']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S-Oil Products Store</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <!-- Brand -->
            <a class="navbar-brand" href="index.php">
                <span class="text-warning">S-Oil</span> Products
            </a>
            
            <!-- Navbar Toggler -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Navbar Links -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page === 'home' ? 'active' : ''; ?>" href="index.php">Home</a>
                    </li>
                    
                    <!-- Dynamic Categories -->
                    <?php
                    $categories = [];
                    $products = getAllProducts();
                    
                    // Extract unique categories
                    foreach ($products as $product) {
                        if (!in_array($product['category'], $categories)) {
                            $categories[] = $product['category'];
                        }
                    }
                    
                    // Display top categories (up to 4)
                    $topCategories = array_slice($categories, 0, 4);
                    foreach ($topCategories as $category) {
                        $isActive = isset($_GET['category']) && $_GET['category'] === $category;
                        echo '<li class="nav-item">';
                        echo '<a class="nav-link ' . ($isActive ? 'active' : '') . '" href="index.php?page=home&category=' . urlencode($category) . '">' . htmlspecialchars($category) . '</a>';
                        echo '</li>';
                    }
                    ?>
                    
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page === 'orders' ? 'active' : ''; ?>" href="index.php?page=orders">My Orders</a>
                    </li>
                </ul>
                
                <!-- Cart and User Links -->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page === 'cart' ? 'active' : ''; ?>" href="index.php?page=cart">
                            <i class="fas fa-shopping-cart"></i> Cart
                            <?php if ($cartItemCount > 0): ?>
                                <span class="badge rounded-pill bg-warning text-dark ms-1"><?php echo $cartItemCount; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    
                    <!-- Admin Link (visible to admins only) -->
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $page === 'admin' ? 'active' : ''; ?>" href="index.php?page=admin">Admin</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="py-4">
        <!-- Page content will be loaded here -->