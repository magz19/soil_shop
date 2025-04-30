<?php
// Include helper functions if not already included
if (!function_exists('getAllProducts')) {
    require_once __DIR__ . '/../includes/functions.php';
}

// Get featured products
$featuredProducts = getAllProducts();

// Limit to 8 products for featured section
$featuredProducts = array_slice($featuredProducts, 0, 8);

// Get categories for display
$categories = [
    'engine_oil' => 'Engine Oil',
    'transmission_fluid' => 'Transmission Fluid',
    'brake_fluid' => 'Brake Fluid',
    'coolant' => 'Coolant'
];
?>

<div class="container">
    <!-- Hero Section -->
    <div class="row mb-5">
        <div class="col-lg-12">
            <div class="card border-0 rounded-4 overflow-hidden shadow hero-card">
                <div class="card-body p-5 bg-light">
                    <div class="row align-items-center">
                        <div class="col-lg-7">
                            <h1 class="display-5 fw-bold mb-3">Premium S-Oil Products</h1>
                            <p class="lead mb-4">High-performance oils and lubricants for your vehicle, engineered for excellence.</p>
                            <div class="d-flex gap-3">
                                <a href="index.php?page=products" class="btn btn-warning btn-lg px-4">
                                    Shop Now <i class="fas fa-arrow-right ms-2"></i>
                                </a>
                                <a href="#featured-products" class="btn btn-outline-secondary btn-lg px-4">
                                    View Products
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-5 text-center d-none d-lg-block">
                            <img src="assets/images/hero-image.png" alt="S-Oil Products" class="img-fluid hero-image" style="max-height: 300px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Categories Section -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="fw-bold mb-4">Product Categories</h2>
        </div>
        
        <?php foreach ($categories as $slug => $name): ?>
            <div class="col-md-3 mb-4">
                <a href="index.php?page=products&category=<?php echo $slug; ?>" class="text-decoration-none">
                    <div class="card border-0 rounded-4 shadow-sm category-card h-100">
                        <div class="card-body text-center p-4">
                            <div class="category-icon mb-3">
                                <i class="fas fa-oil-can fa-3x text-warning"></i>
                            </div>
                            <h5 class="card-title"><?php echo $name; ?></h5>
                            <p class="card-text text-muted small">Quality <?php echo $name; ?> Products</p>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Featured Products Section -->
    <div id="featured-products" class="row mb-5">
        <div class="col-12">
            <h2 class="fw-bold mb-4">Featured Products</h2>
        </div>
        
        <?php if (count($featuredProducts) > 0): ?>
            <?php foreach ($featuredProducts as $product): ?>
                <div class="col-md-3 mb-4">
                    <div class="card border-0 rounded-4 shadow-sm product-card h-100">
                        <div class="position-relative">
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="card-img-top rounded-top-4 p-3" style="height: 200px; object-fit: contain;">
                            <?php if ($product['is_featured']): ?>
                                <span class="position-absolute top-0 end-0 badge bg-warning m-3">Featured</span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <p class="card-text text-muted small"><?php echo substr(htmlspecialchars($product['description']), 0, 80); ?>...</p>
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <span class="fw-bold text-primary"><?php echo formatPrice($product['price']); ?></span>
                                    <a href="index.php?page=product&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">
                    No products found. Please check back later.
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Why Choose Us Section -->
    <div class="row mb-5 pt-4">
        <div class="col-12 text-center mb-4">
            <h2 class="fw-bold">Why Choose S-Oil Products?</h2>
            <p class="text-muted">Trusted by mechanics and drivers for quality and performance</p>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card border-0 rounded-4 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-medal fa-3x text-warning"></i>
                    </div>
                    <h4>Premium Quality</h4>
                    <p class="text-muted">All our products are made with high-quality base oils and advanced additives.</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card border-0 rounded-4 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-shield-alt fa-3x text-warning"></i>
                    </div>
                    <h4>Engine Protection</h4>
                    <p class="text-muted">Our oils provide superior protection against wear, rust, and corrosion.</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card border-0 rounded-4 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-tachometer-alt fa-3x text-warning"></i>
                    </div>
                    <h4>Performance Boost</h4>
                    <p class="text-muted">Improve fuel efficiency and engine performance with our specially formulated products.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hero-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.category-card:hover, .product-card:hover {
    transform: translateY(-5px);
    transition: transform 0.3s ease;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
}

.category-icon, .feature-icon {
    height: 80px;
    width: 80px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background-color: rgba(255, 193, 7, 0.1);
}
</style>