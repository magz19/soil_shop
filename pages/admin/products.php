<?php
// Include helper functions if not already included
if (!function_exists('formatPrice')) {
    require_once 'includes/functions.php';
}

// Initialize variables
$products = getAllProducts();
$action = isset($_GET['action']) ? $_GET['action'] : '';
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$successMessage = '';
$errorMessage = '';

// Handle product actions (add, edit, delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_product']) || isset($_POST['update_product'])) {
        // Get form data
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        $price = isset($_POST['price']) ? (float)$_POST['price'] : 0;
        $category = isset($_POST['category']) ? trim($_POST['category']) : '';
        $stockQuantity = isset($_POST['stock_quantity']) ? (int)$_POST['stock_quantity'] : 0;
        $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
        
        // Validate form data
        if (empty($name)) {
            $errorMessage = 'Product name is required.';
        } elseif (empty($category)) {
            $errorMessage = 'Product category is required.';
        } elseif ($price <= 0) {
            $errorMessage = 'Product price must be greater than zero.';
        } elseif ($stockQuantity < 0) {
            $errorMessage = 'Stock quantity cannot be negative.';
        } else {
            // Handle image upload
            $imageUrl = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'assets/images/products/';
                
                // Create directory if it doesn't exist
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $fileName = time() . '_' . basename($_FILES['image']['name']);
                $uploadPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                    $imageUrl = $uploadPath;
                } else {
                    $errorMessage = 'Failed to upload product image.';
                }
            } else {
                // Use existing image for updates
                if (isset($_POST['update_product']) && isset($_POST['existing_image'])) {
                    $imageUrl = $_POST['existing_image'];
                } else {
                    // Default image for new products if no image uploaded
                    $imageUrl = 'assets/images/products/default-product.jpg';
                }
            }
            
            // If no errors, add or update product
            if (empty($errorMessage)) {
                $productData = [
                    'name' => $name,
                    'description' => $description,
                    'price' => $price,
                    'category' => $category,
                    'stock_quantity' => $stockQuantity,
                    'is_featured' => $isFeatured,
                    'image_url' => $imageUrl
                ];
                
                if (isset($_POST['add_product'])) {
                    // Add new product
                    $result = addProduct($productData);
                    if ($result) {
                        $successMessage = 'Product added successfully.';
                        // Refresh products list
                        $products = getAllProducts();
                    } else {
                        $errorMessage = 'Failed to add product.';
                    }
                } elseif (isset($_POST['update_product'])) {
                    // Update existing product
                    $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
                    $result = updateProduct($productId, $productData);
                    if ($result) {
                        $successMessage = 'Product updated successfully.';
                        // Refresh products list
                        $products = getAllProducts();
                    } else {
                        $errorMessage = 'Failed to update product.';
                    }
                }
            }
        }
    } elseif (isset($_POST['delete_product'])) {
        // Delete product
        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $result = deleteProduct($productId);
        if ($result) {
            $successMessage = 'Product deleted successfully.';
            // Refresh products list
            $products = getAllProducts();
        } else {
            $errorMessage = 'Failed to delete product.';
        }
    }
}

// Get product details for edit mode
$editProduct = null;
if ($action === 'edit' && $productId > 0) {
    $editProduct = getProduct($productId);
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Product Management</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
            <i class="fas fa-plus me-2"></i> Add New Product
        </button>
    </div>
    
    <?php if (!empty($successMessage)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> <?php echo $successMessage; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($errorMessage)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> <?php echo $errorMessage; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <!-- Products Table -->
    <div class="card border-0 rounded-4 shadow-sm mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Featured</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($products) > 0): ?>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?php echo $product['id']; ?></td>
                                    <td>
                                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: contain;">
                                    </td>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <?php echo ucfirst(str_replace('_', ' ', $product['category'])); ?>
                                        </span>
                                    </td>
                                    <td><?php echo formatPrice($product['price']); ?></td>
                                    <td>
                                        <?php if ($product['stock_quantity'] <= 5): ?>
                                            <span class="badge bg-danger"><?php echo $product['stock_quantity']; ?></span>
                                        <?php elseif ($product['stock_quantity'] <= 20): ?>
                                            <span class="badge bg-warning text-dark"><?php echo $product['stock_quantity']; ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-success"><?php echo $product['stock_quantity']; ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($product['is_featured']): ?>
                                            <span class="badge bg-warning text-dark"><i class="fas fa-star me-1"></i> Featured</span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <a href="index.php?page=admin&admin_page=products&action=edit&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $product['id']; ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        
                                        <!-- Delete Confirmation Modal -->
                                        <div class="modal fade" id="deleteModal<?php echo $product['id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo $product['id']; ?>" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteModalLabel<?php echo $product['id']; ?>">Confirm Delete</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Are you sure you want to delete the product: <strong><?php echo htmlspecialchars($product['name']); ?></strong>?</p>
                                                        <p class="text-danger mb-0"><small>This action cannot be undone.</small></p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <form method="post">
                                                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                            <button type="submit" name="delete_product" class="btn btn-danger">Delete</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                    <p class="mb-0">No products found.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="engine_oil">Engine Oil</option>
                                <option value="transmission_fluid">Transmission Fluid</option>
                                <option value="brake_fluid">Brake Fluid</option>
                                <option value="coolant">Coolant</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="price" class="form-label">Price (₱) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-6">
                            <label for="stock_quantity" class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" min="0" required>
                        </div>
                        <div class="col-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="image" class="form-label">Product Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured">
                                <label class="form-check-label" for="is_featured">
                                    Featured Product
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" name="add_product">Add Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Product Modal -->
<?php if ($editProduct): ?>
<div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="product_id" value="<?php echo $editProduct['id']; ?>">
                    <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($editProduct['image_url']); ?>">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit_name" class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_name" name="name" value="<?php echo htmlspecialchars($editProduct['name']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_category" class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="engine_oil" <?php echo $editProduct['category'] === 'engine_oil' ? 'selected' : ''; ?>>Engine Oil</option>
                                <option value="transmission_fluid" <?php echo $editProduct['category'] === 'transmission_fluid' ? 'selected' : ''; ?>>Transmission Fluid</option>
                                <option value="brake_fluid" <?php echo $editProduct['category'] === 'brake_fluid' ? 'selected' : ''; ?>>Brake Fluid</option>
                                <option value="coolant" <?php echo $editProduct['category'] === 'coolant' ? 'selected' : ''; ?>>Coolant</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_price" class="form-label">Price (₱) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="edit_price" name="price" step="0.01" min="0" value="<?php echo $editProduct['price']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_stock_quantity" class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="edit_stock_quantity" name="stock_quantity" min="0" value="<?php echo $editProduct['stock_quantity']; ?>" required>
                        </div>
                        <div class="col-12">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3"><?php echo htmlspecialchars($editProduct['description']); ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_image" class="form-label">Product Image</label>
                            <input type="file" class="form-control" id="edit_image" name="image" accept="image/*">
                            <small class="text-muted">Leave empty to keep current image</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Current Image</label>
                            <div>
                                <img src="<?php echo htmlspecialchars($editProduct['image_url']); ?>" alt="<?php echo htmlspecialchars($editProduct['name']); ?>" class="img-thumbnail" style="max-height: 100px;">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="edit_is_featured" name="is_featured" <?php echo $editProduct['is_featured'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="edit_is_featured">
                                    Featured Product
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" name="update_product">Update Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show edit modal
    new bootstrap.Modal(document.getElementById('editProductModal')).show();
});
</script>
<?php endif; ?>