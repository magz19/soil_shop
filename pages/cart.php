<?php
// Get user cart
$userId = 1; // Default user ID for now (until we implement authentication)
$cart = getCartWithProducts($userId);

// Initialize variables
$cartItems = [];
$subtotal = 0;

if ($cart) {
    $cartItems = $cart['items'];
    
    // Calculate subtotal
    foreach ($cartItems as $item) {
        $price = !empty($item['product']['sale_price']) ? $item['product']['sale_price'] : $item['product']['price'];
        $subtotal += $price * $item['quantity'];
    }
}
?>

<div class="container">
    <h1 class="mb-4">Shopping Cart</h1>
    
    <?php if (empty($cartItems)): ?>
        <div class="alert alert-info">
            Your cart is empty. <a href="index.php" class="alert-link">Continue shopping</a>.
        </div>
    <?php else: ?>
        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Cart Items (<?php echo count($cartItems); ?>)</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($cartItems as $item): ?>
                            <div class="row cart-item py-3 border-bottom">
                                <!-- Product Image -->
                                <div class="col-md-2 mb-2 mb-md-0">
                                    <img src="<?php echo htmlspecialchars($item['product']['image_url']); ?>" alt="<?php echo htmlspecialchars($item['product']['name']); ?>" class="cart-image">
                                </div>
                                
                                <!-- Product Details -->
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <h5><a href="index.php?page=product&id=<?php echo $item['product']['id']; ?>" class="text-decoration-none"><?php echo htmlspecialchars($item['product']['name']); ?></a></h5>
                                    
                                    <?php if ($item['product']['in_stock']): ?>
                                        <span class="text-success"><i class="fas fa-check-circle"></i> In Stock</span>
                                    <?php else: ?>
                                        <span class="text-danger"><i class="fas fa-times-circle"></i> Out of Stock</span>
                                    <?php endif; ?>
                                    
                                    <div class="mt-2">
                                        <form action="actions/remove_from_cart.php" method="post" class="d-inline">
                                            <input type="hidden" name="cart_item_id" value="<?php echo $item['id']; ?>">
                                            <button type="submit" class="btn btn-link text-danger p-0">Remove</button>
                                        </form>
                                    </div>
                                </div>
                                
                                <!-- Quantity -->
                                <div class="col-md-2 mb-2 mb-md-0">
                                    <form action="actions/update_cart.php" method="post" class="update-quantity-form">
                                        <input type="hidden" name="cart_item_id" value="<?php echo $item['id']; ?>">
                                        <div class="input-group quantity-control">
                                            <button type="button" class="btn btn-outline-secondary decrement-btn">-</button>
                                            <input type="number" name="quantity" class="form-control text-center quantity-input" value="<?php echo $item['quantity']; ?>" min="1" max="99">
                                            <button type="button" class="btn btn-outline-secondary increment-btn">+</button>
                                        </div>
                                    </form>
                                </div>
                                
                                <!-- Price -->
                                <div class="col-md-2 text-end">
                                    <?php 
                                    $price = !empty($item['product']['sale_price']) ? $item['product']['sale_price'] : $item['product']['price'];
                                    $totalPrice = $price * $item['quantity'];
                                    ?>
                                    
                                    <?php if (!empty($item['product']['sale_price'])): ?>
                                        <div class="sale-price"><?php echo formatPrice($item['product']['sale_price']); ?></div>
                                        <div class="original-price"><?php echo formatPrice($item['product']['price']); ?></div>
                                    <?php else: ?>
                                        <div><?php echo formatPrice($item['product']['price']); ?></div>
                                    <?php endif; ?>
                                    
                                    <?php if ($item['quantity'] > 1): ?>
                                        <div class="text-muted">Total: <?php echo formatPrice($totalPrice); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span><?php echo formatPrice($subtotal); ?></span>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-bold">Total</span>
                            <span class="fw-bold"><?php echo formatPrice($subtotal); ?></span>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <a href="index.php?page=checkout" class="btn btn-warning">Proceed to Checkout</a>
                            <a href="index.php" class="btn btn-outline-secondary">Continue Shopping</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>