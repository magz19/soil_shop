<?php
// Include helper functions if not already included
if (!function_exists('formatPrice')) {
    require_once 'includes/functions.php';
}

// Get user ID (in a real application, this would be the logged-in user)
$userId = $_SESSION['user_id'] ?? 1;

// Initialize session for order tracking searches
if (!isset($_SESSION['order_searches'])) {
    $_SESSION['order_searches'] = [];
}

// Process form submission for order tracking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['track_order'])) {
    $orderId = isset($_POST['order_id']) ? trim($_POST['order_id']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    
    if (!empty($orderId) && !empty($email)) {
        // Verify order (in a real app, this would check the database)
        $order = getOrderWithItems($orderId);
        
        if ($order && strtolower($order['customer_email']) === strtolower($email)) {
            // Store in search history
            if (!in_array(['id' => $order['id'], 'email' => $email], $_SESSION['order_searches'])) {
                $_SESSION['order_searches'][] = [
                    'id' => $order['id'], 
                    'email' => $email,
                    'date' => date('Y-m-d H:i:s')
                ];
                
                // Limit to 5 most recent searches
                if (count($_SESSION['order_searches']) > 5) {
                    array_shift($_SESSION['order_searches']);
                }
            }
            
            // Redirect to order details page
            header('Location: index.php?page=order-details&id=' . $orderId);
            exit;
        } else {
            $trackingError = 'No order found with the provided ID and email.';
        }
    } else {
        $trackingError = 'Please provide both Order ID and Email to track your order.';
    }
}

// Get user's orders
$userOrders = getUserOrders($userId);
?>

<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="mb-4">Order Tracking</h1>
        </div>
    </div>
    
    <!-- Order Tracking Form -->
    <div class="row mb-5">
        <div class="col-lg-8 mx-auto">
            <div class="card border-0 rounded-4 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Track Your Order</h5>
                </div>
                <div class="card-body">
                    <?php if (isset($trackingError)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i> <?php echo $trackingError; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="order_id" name="order_id" placeholder="Order ID" required>
                                    <label for="order_id">Order ID</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                                    <label for="email">Email used for the order</label>
                                </div>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" name="track_order" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i> Track Order
                            </button>
                        </div>
                    </form>
                    
                    <?php if (!empty($_SESSION['order_searches'])): ?>
                        <div class="mt-4">
                            <h6>Recent Searches</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Email</th>
                                            <th>Date</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (array_reverse($_SESSION['order_searches']) as $search): ?>
                                            <tr>
                                                <td><?php echo $search['id']; ?></td>
                                                <td><?php echo htmlspecialchars(substr($search['email'], 0, 3) . '****' . strstr($search['email'], '@')); ?></td>
                                                <td><?php echo date('M d, Y H:i', strtotime($search['date'])); ?></td>
                                                <td class="text-end">
                                                    <a href="index.php?page=order-details&id=<?php echo $search['id']; ?>" class="btn btn-sm btn-outline-primary">View</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- User Orders -->
    <?php if (!empty($userOrders)): ?>
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="mb-4">Your Orders</h2>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="card border-0 rounded-4 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th>Items</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($userOrders as $order): ?>
                                        <tr>
                                            <td>#<?php echo $order['id']; ?></td>
                                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                            <td>
                                                <?php 
                                                    // In a real app, you'd get the actual count from the order_items table
                                                    $itemCount = rand(1, 5); // For demo only
                                                    echo $itemCount . ' ' . ($itemCount == 1 ? 'item' : 'items');
                                                ?>
                                            </td>
                                            <td><?php echo formatPrice($order['total']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo getStatusBadgeClass($order['status']); ?>">
                                                    <?php echo ucfirst(str_replace('_', ' ', $order['status'])); ?>
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <a href="index.php?page=order-details&id=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">
                                                    View Details
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-box-open fa-5x text-muted mb-4"></i>
            <h3>No orders found</h3>
            <p class="text-muted mb-4">You haven't placed any orders yet.</p>
            <a href="index.php?page=products" class="btn btn-primary px-4">
                <i class="fas fa-shopping-bag me-2"></i> Start Shopping
            </a>
        </div>
    <?php endif; ?>
</div>