<?php
// Get product ID from URL
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get product details
$product = getProductById($productId);

// If product not found, display error message
if (!$product) {
    echo '<div class="alert alert-danger">Product not found.</div>';
    exit;
}
?>

<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="my-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="index.php?page=home&category=<?php echo urlencode($product['category']); ?>"><?php echo htmlspecialchars($product['category']); ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($product['name']); ?></li>
        </ol>
    </nav>
    
    <div class="row">
        <!-- Product Image -->
        <div class="col-md-5 mb-4">
            <div class="card">
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" class="img-fluid p-4" alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
        </div>
        
        <!-- Product Details -->
        <div class="col-md-7">
            <h1 class="mb-3"><?php echo htmlspecialchars($product['name']); ?></h1>
            
            <!-- Ratings -->
            <?php if (!empty($product['rating'])): ?>
                <div class="d-flex align-items-center mb-3">
                    <?php
                    $rating = $product['rating'];
                    $fullStars = floor($rating);
                    $halfStar = $rating - $fullStars >= 0.5;
                    
                    for ($i = 0; $i < $fullStars; $i++) {
                        echo '<i class="fas fa-star text-warning"></i>';
                    }
                    
                    if ($halfStar) {
                        echo '<i class="fas fa-star-half-alt text-warning"></i>';
                        $fullStars++;
                    }
                    
                    for ($i = $fullStars + ($halfStar ? 0 : 0); $i < 5; $i++) {
                        echo '<i class="far fa-star text-warning"></i>';
                    }
                    
                    if (!empty($product['review_count'])) {
                        echo '<span class="ms-2 text-muted">' . $product['rating'] . ' (' . $product['review_count'] . ' reviews)</span>';
                    }
                    ?>
                </div>
            <?php endif; ?>
            
            <!-- Price -->
            <div class="mb-3">
                <?php if (!empty($product['sale_price'])): ?>
                    <span class="fs-3 text-danger fw-bold me-2"><?php echo formatPrice($product['sale_price']); ?></span>
                    <span class="text-muted text-decoration-line-through"><?php echo formatPrice($product['price']); ?></span>
                    <?php 
                    $discount = round(($product['price'] - $product['sale_price']) / $product['price'] * 100);
                    echo '<span class="badge bg-danger ms-2">Save ' . $discount . '%</span>';
                    ?>
                <?php else: ?>
                    <span class="fs-3 fw-bold"><?php echo formatPrice($product['price']); ?></span>
                <?php endif; ?>
                
                <?php if ($product['is_prime']): ?>
                    <span class="badge bg-warning text-dark ms-2">Prime</span>
                <?php endif; ?>
            </div>
            
            <!-- Description -->
            <div class="mb-4">
                <h5>Description</h5>
                <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            </div>
            
            <!-- Add to Cart Form -->
            <form action="actions/add_to_cart.php" method="post">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                
                <div class="row g-3 align-items-center mb-4">
                    <div class="col-auto">
                        <label for="quantity" class="col-form-label">Quantity:</label>
                    </div>
                    <div class="col-auto">
                        <div class="input-group quantity-control">
                            <button type="button" class="btn btn-outline-secondary decrement-btn">-</button>
                            <input type="number" id="quantity" name="quantity" class="form-control text-center quantity-input" value="1" min="1" max="99">
                            <button type="button" class="btn btn-outline-secondary increment-btn">+</button>
                        </div>
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-warning btn-lg">Add to Cart</button>
                    <a href="index.php?page=cart" class="btn btn-outline-primary">View Cart</a>
                </div>
            </form>
            
            <!-- Availability -->
            <div class="mt-4">
                <span class="<?php echo $product['in_stock'] ? 'text-success' : 'text-danger'; ?>">
                    <i class="fas <?php echo $product['in_stock'] ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i>
                    <?php echo $product['in_stock'] ? 'In Stock' : 'Out of Stock'; ?>
                </span>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    <div class="mt-5">
        <h3 class="mb-4">Related Products</h3>
        
        <div class="row">
            <?php
            // Get related products (same category)
            $relatedProducts = getProductsByCategory($product['category']);
            $count = 0;
            
            foreach ($relatedProducts as $relatedProduct) {
                // Skip current product
                if ($relatedProduct['id'] == $product['id']) {
                    continue;
                }
                
                // Display up to 4 related products
                if ($count >= 4) {
                    break;
                }
                
                ?>
                <div class="col-md-3 mb-4">
                    <div class="card product-card h-100">
                        <img src="<?php echo htmlspecialchars($relatedProduct['image_url']); ?>" class="card-img-top product-image p-2" alt="<?php echo htmlspecialchars($relatedProduct['name']); ?>">
                        
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($relatedProduct['name']); ?></h5>
                            
                            <div class="mt-auto">
                                <div class="d-flex align-items-center mb-2">
                                    <?php if (!empty($relatedProduct['sale_price'])): ?>
                                        <span class="product-price sale-price me-2"><?php echo formatPrice($relatedProduct['sale_price']); ?></span>
                                        <span class="original-price"><?php echo formatPrice($relatedProduct['price']); ?></span>
                                    <?php else: ?>
                                        <span class="product-price"><?php echo formatPrice($relatedProduct['price']); ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <a href="index.php?page=product&id=<?php echo $relatedProduct['id']; ?>" class="btn btn-primary btn-sm">View Details</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                $count++;
            }
            
            if ($count == 0) {
                echo '<div class="col-12"><div class="alert alert-info">No related products found.</div></div>';
            }
            ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quantity control
    const quantityInput = document.querySelector('.quantity-input');
    const decrementBtn = document.querySelector('.decrement-btn');
    const incrementBtn = document.querySelector('.increment-btn');
    
    decrementBtn.addEventListener('click', function() {
        let value = parseInt(quantityInput.value);
        if (value > 1) {
            quantityInput.value = value - 1;
        }
    });
    
    incrementBtn.addEventListener('click', function() {
        let value = parseInt(quantityInput.value);
        quantityInput.value = value + 1;
    });
});
</script>