<?php
// Include helper functions if not already included
if (!function_exists('getProduct')) {
    require_once __DIR__ . '/../includes/functions.php';
}

// Get product ID from URL
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get product details
$product = getProduct($productId);

// Redirect to products page if product not found
if (!$product) {
    echo '<script>window.location.href = "index.php?page=products";</script>';
    exit;
}

// Get related products (same category)
$relatedProducts = getProductsByCategory($product['category']);

// Remove current product from related products
$relatedProducts = array_filter($relatedProducts, function($item) use ($productId) {
    return $item['id'] != $productId;
});

// Limit to 4 products
$relatedProducts = array_slice($relatedProducts, 0, 4);

// Process add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    // Validate quantity
    if ($quantity < 1) {
        $quantity = 1;
    } elseif ($quantity > $product['stock_quantity']) {
        $quantity = $product['stock_quantity'];
    }
    
    // Add to cart (session-based for now)
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // Check if product already in cart
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['product_id'] == $productId) {
            $item['quantity'] += $quantity;
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        $_SESSION['cart'][] = [
            'product_id' => $productId,
            'quantity' => $quantity,
            'name' => $product['name'],
            'price' => $product['price'],
            'image_url' => $product['image_url']
        ];
    }
    
    // Update cart count
    $_SESSION['cart_count'] = 0;
    foreach ($_SESSION['cart'] as $item) {
        $_SESSION['cart_count'] += $item['quantity'];
    }
    
    // Show success message
    $addToCartSuccess = true;
}
?>

<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="index.php?page=products">Products</a></li>
            <li class="breadcrumb-item"><a href="index.php?page=products&category=<?php echo $product['category']; ?>"><?php echo ucfirst(str_replace('_', ' ', $product['category'])); ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($product['name']); ?></li>
        </ol>
    </nav>
    
    <?php if (isset($addToCartSuccess)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> Product added to cart successfully!
            <div class="mt-2">
                <a href="index.php?page=cart" class="btn btn-sm btn-primary">
                    <i class="fas fa-shopping-cart me-1"></i> View Cart
                </a>
                <a href="index.php?page=products" class="btn btn-sm btn-outline-secondary ms-2">
                    <i class="fas fa-shopping-bag me-1"></i> Continue Shopping
                </a>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <!-- Product Details -->
    <div class="row mb-5">
        <!-- Product Image -->
        <div class="col-md-5 mb-4">
            <div class="card border-0 rounded-4 shadow-sm p-3">
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" class="img-fluid product-img" alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
        </div>
        
        <!-- Product Info -->
        <div class="col-md-7">
            <h1 class="mb-3"><?php echo htmlspecialchars($product['name']); ?></h1>
            
            <div class="mb-3">
                <span class="badge bg-<?php echo $product['stock_quantity'] > 0 ? 'success' : 'danger'; ?>">
                    <?php echo $product['stock_quantity'] > 0 ? 'In Stock' : 'Out of Stock'; ?>
                </span>
                
                <?php if ($product['is_featured']): ?>
                    <span class="badge bg-warning ms-2">Featured</span>
                <?php endif; ?>
                
                <span class="badge bg-secondary ms-2"><?php echo ucfirst(str_replace('_', ' ', $product['category'])); ?></span>
            </div>
            
            <div class="mb-4">
                <h3 class="text-primary"><?php echo formatPrice($product['price']); ?></h3>
            </div>
            
            <div class="mb-4">
                <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            </div>
            
            <?php if ($product['stock_quantity'] > 0): ?>
                <form method="post" class="mb-4">
                    <div class="row g-3 align-items-center">
                        <div class="col-auto">
                            <label for="quantity" class="form-label">Quantity</label>
                        </div>
                        <div class="col-auto">
                            <div class="input-group" style="width: 140px;">
                                <button type="button" class="btn btn-outline-secondary" id="decreaseQuantity">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" class="form-control text-center" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>">
                                <button type="button" class="btn btn-outline-secondary" id="increaseQuantity">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <small class="text-muted"><?php echo $product['stock_quantity']; ?> available</small>
                        </div>
                        <div class="col-auto">
                            <button type="submit" name="add_to_cart" class="btn btn-primary px-4">
                                <i class="fas fa-cart-plus me-2"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                </form>
            <?php else: ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i> This product is currently out of stock.
                </div>
            <?php endif; ?>
            
            <!-- Additional Information -->
            <div class="mt-4">
                <div class="card border-0 rounded-4 shadow-sm">
                    <div class="card-body">
                        <h5 class="mb-3">Product Specifications</h5>
                        <table class="table table-borderless table-sm mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-muted" style="width: 30%;">Product ID</td>
                                    <td><?php echo $product['id']; ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Category</td>
                                    <td><?php echo ucfirst(str_replace('_', ' ', $product['category'])); ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Stock</td>
                                    <td><?php echo $product['stock_quantity']; ?> units</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    <?php if (count($relatedProducts) > 0): ?>
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="mb-4">Related Products</h2>
            </div>
            
            <?php foreach ($relatedProducts as $relatedProduct): ?>
                <div class="col-md-3 mb-4">
                    <div class="card border-0 rounded-4 shadow-sm product-card h-100">
                        <div class="position-relative">
                            <img src="<?php echo htmlspecialchars($relatedProduct['image_url']); ?>" alt="<?php echo htmlspecialchars($relatedProduct['name']); ?>" class="card-img-top rounded-top-4 p-3" style="height: 180px; object-fit: contain;">
                            <?php if ($relatedProduct['is_featured']): ?>
                                <span class="position-absolute top-0 end-0 badge bg-warning m-3">Featured</span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($relatedProduct['name']); ?></h5>
                            <p class="card-text text-muted small"><?php echo substr(htmlspecialchars($relatedProduct['description']), 0, 60); ?>...</p>
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <span class="fw-bold text-primary"><?php echo formatPrice($relatedProduct['price']); ?></span>
                                    <a href="index.php?page=product&id=<?php echo $relatedProduct['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        View
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.product-img {
    max-height: 400px;
    object-fit: contain;
}

.product-card:hover {
    transform: translateY(-5px);
    transition: transform 0.3s ease;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quantity controls
    const quantityInput = document.getElementById('quantity');
    const decreaseBtn = document.getElementById('decreaseQuantity');
    const increaseBtn = document.getElementById('increaseQuantity');
    
    if (quantityInput && decreaseBtn && increaseBtn) {
        const maxQuantity = parseInt(quantityInput.max);
        
        decreaseBtn.addEventListener('click', function() {
            let value = parseInt(quantityInput.value);
            if (value > 1) {
                quantityInput.value = value - 1;
            }
        });
        
        increaseBtn.addEventListener('click', function() {
            let value = parseInt(quantityInput.value);
            if (value < maxQuantity) {
                quantityInput.value = value + 1;
            }
        });
        
        quantityInput.addEventListener('change', function() {
            let value = parseInt(this.value);
            
            if (isNaN(value) || value < 1) {
                this.value = 1;
            } else if (value > maxQuantity) {
                this.value = maxQuantity;
            }
        });
    }
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});
</script>