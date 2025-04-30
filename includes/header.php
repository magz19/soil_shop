<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S-Oil Products Store</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <!-- Top Navigation Bar -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <!-- Logo -->
                <a class="navbar-brand" href="index.php">
                    <span class="text-warning">S-Oil</span>
                    <small>Products</small>
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <?php if (isset($_GET['page']) && strpos($_GET['page'], 'admin') === 0): ?>
                            <!-- Home Link (only visible on admin pages) -->
                            <li class="nav-item">
                                <a class="nav-link" href="index.php"><i class="fas fa-store"></i> Store Front</a>
                            </li>
                        <?php else: ?>
                            <!-- Admin Dashboard Link (only visible on customer pages) -->
                            <li class="nav-item">
                                <a class="nav-link" href="index.php?page=admin-dashboard"><i class="fas fa-user-shield"></i> Admin</a>
                            </li>
                            
                            <!-- Cart Link (only visible on customer pages) -->
                            <li class="nav-item">
                                <a class="nav-link" href="index.php?page=cart">
                                    <i class="fas fa-shopping-cart"></i> 
                                    Cart
                                    <?php
                                    // Display cart count if available
                                    if (isset($_SESSION['user_id'])) {
                                        $cartItems = getCartItems($_SESSION['user_id']);
                                        $itemCount = 0;
                                        foreach ($cartItems as $item) {
                                            $itemCount += $item['quantity'];
                                        }
                                        if ($itemCount > 0) {
                                            echo '<span class="badge bg-warning text-dark">' . $itemCount . '</span>';
                                        }
                                    }
                                    ?>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
        
        <!-- Secondary Nav with Credits -->
        <div class="bg-secondary py-1">
            <div class="container">
                <div class="text-white small">
                    <span class="text-light fst-italic">© <?php echo date('Y'); ?> S-Oil Products Store</span>
                </div>
            </div>
        </div>
    </header>

    <main class="container py-4">
        <!-- Page content will be inserted here -->