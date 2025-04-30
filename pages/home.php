<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="mb-4">S-Oil Products</h1>
            
            <!-- Categories -->
            <div class="d-flex flex-wrap mb-4">
                <?php
                $categories = [];
                $products = getAllProducts();
                
                // Extract unique categories
                foreach ($products as $product) {
                    if (!in_array($product['category'], $categories)) {
                        $categories[] = $product['category'];
                    }
                }
                
                // Display category buttons
                foreach ($categories as $category) {
                    echo '<a href="index.php?page=home&category=' . urlencode($category) . '" class="btn btn-outline-primary me-2 mb-2">' . htmlspecialchars($category) . '</a>';
                }
                ?>
                <a href="index.php?page=home" class="btn btn-outline-secondary me-2 mb-2">All Products</a>
            </div>
        </div>
    </div>
    
    <div class="row">
        <?php
        // Filter products by category if specified
        $filteredProducts = $products;
        if (isset($_GET['category'])) {
            $category = $_GET['category'];
            $filteredProducts = getProductsByCategory($category);
        }
        
        // Display products
        foreach ($filteredProducts as $product) {
            ?>
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="card product-card h-100">
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" class="card-img-top product-image p-3" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                        
                        <p class="card-text text-truncate-3"><?php echo htmlspecialchars($product['description']); ?></p>
                        
                        <div class="mt-auto">
                            <div class="d-flex align-items-center mb-2">
                                <?php
                                // Display rating stars
                                if (!empty($product['rating'])) {
                                    echo '<div class="me-2">';
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
                                    
                                    echo '</div>';
                                    
                                    if (!empty($product['review_count'])) {
                                        echo '<small class="text-muted">(' . $product['review_count'] . ')</small>';
                                    }
                                }
                                ?>
                            </div>
                            
                            <div class="d-flex align-items-center mb-3">
                                <?php if (!empty($product['sale_price'])): ?>
                                    <span class="product-price sale-price me-2"><?php echo formatPrice($product['sale_price']); ?></span>
                                    <span class="original-price"><?php echo formatPrice($product['price']); ?></span>
                                <?php else: ?>
                                    <span class="product-price"><?php echo formatPrice($product['price']); ?></span>
                                <?php endif; ?>
                                
                                <?php if ($product['is_prime']): ?>
                                    <span class="badge bg-warning text-dark ms-auto">Prime</span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <a href="index.php?page=product&id=<?php echo $product['id']; ?>" class="btn btn-primary">View Details</a>
                                
                                <form action="actions/add_to_cart.php" method="post">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn btn-warning w-100">Add to Cart</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        
        // Display message if no products found
        if (count($filteredProducts) === 0) {
            echo '<div class="col-12"><div class="alert alert-info">No products found.</div></div>';
        }
        ?>
    </div>
</div>

<style>
    .text-truncate-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>