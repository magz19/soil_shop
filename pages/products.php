<?php
// Include helper functions if not already included
if (!function_exists('getAllProducts')) {
    require_once 'includes/functions.php';
}

// Get category from URL parameter
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Get products based on category
if (!empty($category)) {
    $products = getProductsByCategory($category);
    $categoryName = ucfirst(str_replace('_', ' ', $category));
    $pageTitle = "$categoryName Products";
} else {
    $products = getAllProducts();
    $pageTitle = "All Products";
}

// Get all categories for sidebar
$categories = [
    'engine_oil' => 'Engine Oil',
    'transmission_fluid' => 'Transmission Fluid',
    'brake_fluid' => 'Brake Fluid',
    'coolant' => 'Coolant'
];
?>

<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo $pageTitle; ?></li>
                </ol>
            </nav>
            
            <h1 class="mb-4"><?php echo $pageTitle; ?></h1>
        </div>
    </div>
    
    <div class="row">
        <!-- Categories Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Categories</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush rounded-bottom">
                        <a href="index.php?page=products" class="list-group-item list-group-item-action <?php echo empty($category) ? 'active' : ''; ?>">
                            All Products
                        </a>
                        <?php foreach ($categories as $slug => $name): ?>
                            <a href="index.php?page=products&category=<?php echo $slug; ?>" class="list-group-item list-group-item-action <?php echo $category === $slug ? 'active' : ''; ?>">
                                <?php echo $name; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- Price Filter (For future implementation) -->
            <div class="card border-0 shadow-sm rounded-4 mt-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Price Range</h5>
                </div>
                <div class="card-body">
                    <div class="range-slider">
                        <input type="range" class="form-range" min="0" max="2000" step="100" id="priceRange">
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <span>₱0</span>
                        <span>₱2,000</span>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-sm btn-primary w-100" id="filterBtn">Apply Filter</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Products Grid -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <span class="text-muted">Showing <?php echo count($products); ?> products</span>
                </div>
                <div>
                    <select class="form-select form-select-sm" id="sortOrder">
                        <option value="default">Default Sorting</option>
                        <option value="price_low">Price: Low to High</option>
                        <option value="price_high">Price: High to Low</option>
                        <option value="name_asc">Name: A to Z</option>
                        <option value="name_desc">Name: Z to A</option>
                    </select>
                </div>
            </div>
            
            <?php if (count($products) > 0): ?>
                <div class="row">
                    <?php foreach ($products as $product): ?>
                        <div class="col-md-4 mb-4">
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
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> No products found in this category. Please check back later or explore other categories.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.product-card:hover {
    transform: translateY(-5px);
    transition: transform 0.3s ease;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
}

.list-group-item.active {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #000;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sort products functionality (for future implementation)
    const sortSelect = document.getElementById('sortOrder');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            // In a real implementation, this would reload the page with a sort parameter
            // For now, we'll just log the selected sort option
            console.log('Sort by:', this.value);
        });
    }
    
    // Price filter functionality (for future implementation)
    const filterBtn = document.getElementById('filterBtn');
    if (filterBtn) {
        filterBtn.addEventListener('click', function() {
            const priceRange = document.getElementById('priceRange').value;
            // In a real implementation, this would reload the page with a price range parameter
            // For now, we'll just log the selected price range
            console.log('Price range:', priceRange);
        });
    }
});
</script>