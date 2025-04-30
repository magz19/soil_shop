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
    <!-- Header -->
    <header class="sticky-top">
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
            <div class="container">
                <a class="navbar-brand" href="index.php">
                    <span class="fw-bold text-warning">S-Oil</span> Products
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link <?php echo (!isset($_GET['page']) || $_GET['page'] == 'home') ? 'active' : ''; ?>" href="index.php">Home</a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'products') ? 'active' : ''; ?>" href="index.php?page=products">Products</a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'orders') ? 'active' : ''; ?>" href="index.php?page=orders">Track Orders</a>
                        </li>
                    </ul>
                    
                    <div class="d-flex align-items-center">
                        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                            <a href="index.php?page=admin" class="btn btn-warning me-2">
                                <i class="fas fa-user-cog me-1"></i> Admin Dashboard
                            </a>
                            <a href="logout.php" class="btn btn-outline-secondary me-2">
                                <i class="fas fa-sign-out-alt me-1"></i> Logout
                            </a>
                        <?php else: ?>
                            <a href="index.php?page=cart" class="btn btn-outline-primary position-relative me-2">
                                <i class="fas fa-shopping-cart"></i>
                                <span class="cart-count position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?php
                                    // Get cart count - in real app this would be from database
                                    $cartCount = isset($_SESSION['cart_count']) ? $_SESSION['cart_count'] : 0;
                                    echo $cartCount;
                                    ?>
                                </span>
                            </a>
                            <a href="login.php" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-user-cog me-1"></i> Admin
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>
        
        <?php if (isset($_GET['page']) && $_GET['page'] === 'admin'): ?>
            <!-- Admin Navigation Bar -->
            <div class="bg-light py-2 border-bottom">
                <div class="container">
                    <ul class="nav nav-pills">
                        <li class="nav-item">
                            <a class="nav-link <?php echo (!isset($_GET['admin_page']) || $_GET['admin_page'] == 'dashboard') ? 'active' : ''; ?>" href="index.php?page=admin&admin_page=dashboard">
                                <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (isset($_GET['admin_page']) && $_GET['admin_page'] == 'orders') ? 'active' : ''; ?>" href="index.php?page=admin&admin_page=orders">
                                <i class="fas fa-clipboard-list me-1"></i> Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (isset($_GET['admin_page']) && $_GET['admin_page'] == 'products') ? 'active' : ''; ?>" href="index.php?page=admin&admin_page=products">
                                <i class="fas fa-box me-1"></i> Products
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </header>
    
    <!-- Main Content -->
    <main class="py-4">
        <!-- Page content will be loaded here -->