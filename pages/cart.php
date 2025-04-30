<?php
// Include helper functions if not already included
if (!function_exists('formatPrice')) {
    require_once __DIR__ . '/../includes/functions.php';
}

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Process cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update quantity
    if (isset($_POST['update_cart'])) {
        foreach ($_POST['quantity'] as $index => $quantity) {
            $quantity = (int)$quantity;
            if ($quantity <= 0) {
                // Remove item if quantity is 0 or negative
                unset($_SESSION['cart'][$index]);
            } else {
                $_SESSION['cart'][$index]['quantity'] = $quantity;
            }
        }
        
        // Reindex array
        $_SESSION['cart'] = array_values($_SESSION['cart']);
        
        // Show success message
        $updateCartSuccess = true;
    }
    
    // Remove item
    if (isset($_POST['remove_item'])) {
        $index = (int)$_POST['remove_item'];
        if (isset($_SESSION['cart'][$index])) {
            unset($_SESSION['cart'][$index]);
            // Reindex array
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            
            // Show success message
            $removeItemSuccess = true;
        }
    }
    
    // Clear cart
    if (isset($_POST['clear_cart'])) {
        $_SESSION['cart'] = [];
        
        // Show success message
        $clearCartSuccess = true;
    }
    
    // Update cart count
    $_SESSION['cart_count'] = 0;
    foreach ($_SESSION['cart'] as $item) {
        $_SESSION['cart_count'] += $item['quantity'];
    }
}

// Calculate cart totals
$subtotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

// Fixed shipping options
$shippingOptions = [
    'pickup' => ['name' => 'Personal Pick-up', 'price' => 0],
    'grab' => ['name' => 'Grab/Lalamove Delivery', 'price' => 0]
];
?>

<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="mb-4">Shopping Cart</h1>
        </div>
    </div>
    
    <?php if (isset($updateCartSuccess)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> Cart updated successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($removeItemSuccess)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> Item removed from cart!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($clearCartSuccess)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> Cart cleared successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if (empty($_SESSION['cart'])): ?>
        <div class="text-center py-5">
            <i class="fas fa-shopping-cart fa-5x text-muted mb-4"></i>
            <h3>Your cart is empty</h3>
            <p class="text-muted mb-4">Add some products to your cart and they will appear here</p>
            <a href="index.php?page=products" class="btn btn-primary px-4">
                <i class="fas fa-shopping-bag me-2"></i> Continue Shopping
            </a>
        </div>
    <?php else: ?>
        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8 mb-4">
                <div class="card border-0 rounded-4 shadow-sm">
                    <div class="card-body p-0">
                        <form method="post">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-center">Price</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-end">Total</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="me-3" style="width: 60px; height: 60px; object-fit: contain;">
                                                    <div>
                                                        <h6 class="mb-0"><?php echo htmlspecialchars($item['name']); ?></h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center"><?php echo formatPrice($item['price']); ?></td>
                                            <td class="text-center" style="width: 150px;">
                                                <div class="input-group input-group-sm">
                                                    <button type="button" class="btn btn-outline-secondary quantity-btn" data-action="decrease" data-index="<?php echo $index; ?>">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                    <input type="number" name="quantity[<?php echo $index; ?>]" class="form-control text-center quantity-input" value="<?php echo $item['quantity']; ?>" min="0" max="99">
                                                    <button type="button" class="btn btn-outline-secondary quantity-btn" data-action="increase" data-index="<?php echo $index; ?>">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="text-end"><?php echo formatPrice($item['price'] * $item['quantity']); ?></td>
                                            <td class="text-end">
                                                <button type="submit" name="remove_item" value="<?php echo $index; ?>" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-between p-3 border-top">
                                <div>
                                    <button type="submit" name="clear_cart" class="btn btn-outline-danger">
                                        <i class="fas fa-trash me-2"></i> Clear Cart
                                    </button>
                                </div>
                                <div>
                                    <a href="index.php?page=products" class="btn btn-outline-secondary me-2">
                                        <i class="fas fa-shopping-bag me-2"></i> Continue Shopping
                                    </a>
                                    <button type="submit" name="update_cart" class="btn btn-primary">
                                        <i class="fas fa-sync-alt me-2"></i> Update Cart
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Cart Summary -->
            <div class="col-lg-4">
                <div class="card border-0 rounded-4 shadow-sm mb-4">
                    <div class="card-header bg-light py-3">
                        <h5 class="mb-0">Cart Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span><?php echo formatPrice($subtotal); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping</span>
                            <span>Free</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <strong>Total</strong>
                            <strong class="text-primary h5"><?php echo formatPrice($subtotal); ?></strong>
                        </div>
                        <div class="d-grid gap-2">
                            <a href="index.php?page=checkout" class="btn btn-primary">
                                <i class="fas fa-shopping-cart me-2"></i> Proceed to Checkout
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Shipping Info -->
                <div class="card border-0 rounded-4 shadow-sm">
                    <div class="card-header bg-light py-3">
                        <h5 class="mb-0">Shipping Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6>Pickup Details:</h6>
                            <p class="mb-0">Address: 123 Mendiola St. Manila City</p>
                            <p class="mb-0">Contact Person: Anjhela Geron 09454545</p>
                        </div>
                        <div>
                            <h6>Delivery Details:</h6>
                            <p class="mb-0">Grab/Lalamove delivery arranged by the customer</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quantity buttons
    const quantityBtns = document.querySelectorAll('.quantity-btn');
    quantityBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const action = this.getAttribute('data-action');
            const index = this.getAttribute('data-index');
            const input = document.querySelector(`input[name="quantity[${index}]"]`);
            
            let value = parseInt(input.value);
            
            if (action === 'decrease') {
                if (value > 0) input.value = value - 1;
            } else if (action === 'increase') {
                input.value = value + 1;
            }
        });
    });
    
    // Auto-hide alerts after 3 seconds
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 3000);
    });
});
</script>