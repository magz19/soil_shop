<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userType = isset($_POST['user_type']) ? $_POST['user_type'] : '';
    
    if ($userType === 'admin') {
        // Set admin flag
        $_SESSION['is_admin'] = true;
        
        // Redirect to admin dashboard
        header('Location: index.php?page=admin');
        exit;
    } elseif ($userType === 'customer') {
        // Set customer session (in a real app, this would authenticate the user)
        $_SESSION['user_id'] = 1; // Default user
        
        // Redirect to home page
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S-Oil Products Store - Login</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        
        .login-container {
            max-width: 800px;
            width: 100%;
            padding: 20px;
        }
        
        .login-option {
            border: 2px solid #dee2e6;
            border-radius: 10px;
            padding: 30px;
            height: 100%;
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .login-option:hover {
            border-color: #007bff;
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .login-option.selected {
            border-color: #007bff;
            background-color: #f0f7ff;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .icon-container {
            font-size: 3rem;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1><span class="text-warning">S-Oil</span> Products Store</h1>
            <p class="text-muted">Choose your login type</p>
        </div>
        
        <form id="login-form" method="post" action="login.php">
            <input type="hidden" id="user_type" name="user_type" value="">
            
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="login-option" data-type="customer">
                        <div class="icon-container text-primary">
                            <i class="fas fa-user"></i>
                        </div>
                        <h3 class="text-center mb-3">Customer</h3>
                        <p class="text-center">Shop for S-Oil products, manage your cart, and track orders.</p>
                        <div class="d-grid mt-4">
                            <button type="button" class="btn btn-primary select-btn">Continue as Customer</button>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="login-option" data-type="admin">
                        <div class="icon-container text-warning">
                            <i class="fas fa-user-cog"></i>
                        </div>
                        <h3 class="text-center mb-3">Admin</h3>
                        <p class="text-center">Manage orders, products, and view store analytics.</p>
                        <div class="d-grid mt-4">
                            <button type="button" class="btn btn-warning select-btn">Continue as Admin</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        
        <div class="text-center mt-4">
            <p class="text-muted">© <?php echo date('Y'); ?> S-Oil Products Store</p>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginOptions = document.querySelectorAll('.login-option');
            const userTypeInput = document.getElementById('user_type');
            const loginForm = document.getElementById('login-form');
            
            loginOptions.forEach(option => {
                option.addEventListener('click', function() {
                    // Remove selected class from all options
                    loginOptions.forEach(opt => opt.classList.remove('selected'));
                    
                    // Add selected class to clicked option
                    this.classList.add('selected');
                    
                    // Set user type value
                    userTypeInput.value = this.getAttribute('data-type');
                });
                
                const selectBtn = option.querySelector('.select-btn');
                selectBtn.addEventListener('click', function() {
                    // Set user type and submit form
                    userTypeInput.value = option.getAttribute('data-type');
                    loginForm.submit();
                });
            });
        });
    </script>
</body>
</html>