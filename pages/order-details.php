<?php
// Include helper functions if not already included
if (!function_exists('formatPrice')) {
    require_once 'includes/functions.php';
}

// Get order ID from URL parameter
$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get order details with items
$order = getOrderWithItems($orderId);

// Redirect to orders page if order not found
if (!$order) {
    echo '<script>window.location.href = "index.php?page=orders";</script>';
    exit;
}

// Define statuses for timeline
$orderStatuses = [
    'pending' => [
        'label' => 'Order Placed',
        'icon' => 'fa-shopping-cart',
        'description' => 'Your order has been received and is being processed.'
    ],
    'processing' => [
        'label' => 'Processing',
        'icon' => 'fa-cogs',
        'description' => 'Your order is being prepared for shipping.'
    ],
    'shipped' => [
        'label' => 'Shipped',
        'icon' => 'fa-truck',
        'description' => 'Your order has been shipped and is on the way.'
    ],
    'delivered' => [
        'label' => 'Delivered',
        'icon' => 'fa-check-circle',
        'description' => 'Your order has been delivered successfully.'
    ],
    'cancelled' => [
        'label' => 'Cancelled',
        'icon' => 'fa-times-circle',
        'description' => 'Your order has been cancelled.'
    ]
];

// Helper function to check if a status is active in timeline
function isStatusActive($status, $currentStatus) {
    $statuses = ['pending', 'processing', 'shipped', 'delivered'];
    $currentIndex = array_search($currentStatus, $statuses);
    $statusIndex = array_search($status, $statuses);
    
    // Handle cancelled orders
    if ($currentStatus === 'cancelled') {
        return false; // No status is active for cancelled orders
    }
    
    return $statusIndex <= $currentIndex;
}
?>

<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="index.php?page=orders">Orders</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Order #<?php echo $order['id']; ?></li>
                </ol>
            </nav>
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="mb-0">Order Details</h1>
                <a href="index.php?page=orders" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Back to Orders
                </a>
            </div>
        </div>
    </div>
    
    <!-- Order Status Timeline -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card border-0 rounded-4 shadow-sm">
                <div class="card-body">
                    <?php if ($order['status'] === 'cancelled'): ?>
                        <div class="alert alert-danger" role="alert">
                            <div class="d-flex">
                                <div class="me-3">
                                    <i class="fas fa-times-circle fa-3x"></i>
                                </div>
                                <div>
                                    <h5 class="alert-heading">Order Cancelled</h5>
                                    <p class="mb-0">This order has been cancelled.</p>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="timeline">
                            <?php 
                            $statuses = ['pending', 'processing', 'shipped', 'delivered'];
                            foreach ($statuses as $status):
                                $isActive = isStatusActive($status, $order['status']);
                                $statusInfo = $orderStatuses[$status];
                            ?>
                                <div class="timeline-item <?php echo $isActive ? 'active' : ''; ?>">
                                    <div class="timeline-icon">
                                        <i class="fas <?php echo $statusInfo['icon']; ?>"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1"><?php echo $statusInfo['label']; ?></h6>
                                        <p class="text-muted small mb-0"><?php echo $statusInfo['description']; ?></p>
                                        <?php if ($status === 'pending' && $isActive): ?>
                                            <small class="text-muted"><?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($order['shipping_method'] === 'delivery' && in_array($order['status'], ['processing', 'shipped'])): ?>
                        <div class="alert alert-info mt-3 mb-0" role="alert">
                            <i class="fas fa-info-circle me-2"></i> 
                            <?php if ($order['status'] === 'processing'): ?>
                                Your order is being prepared. Once ready, we will contact you to arrange delivery.
                            <?php else: ?>
                                Your order is ready for delivery. Please arrange your Grab/Lalamove pickup at your convenience.
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Order Details -->
        <div class="col-lg-8">
            <div class="card border-0 rounded-4 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Order #<?php echo $order['id']; ?></h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Order Information</h6>
                            <p class="mb-1"><strong>Order ID:</strong> #<?php echo $order['id']; ?></p>
                            <p class="mb-1"><strong>Date:</strong> <?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></p>
                            <p class="mb-1"><strong>Status:</strong> 
                                <span class="badge bg-<?php echo getStatusBadgeClass($order['status']); ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </p>
                            <p class="mb-0"><strong>Total:</strong> <?php echo formatPrice($order['total']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Customer Information</h6>
                            <p class="mb-1"><strong>Name:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                            <p class="mb-1"><strong>Email:</strong> <?php echo htmlspecialchars($order['customer_email']); ?></p>
                            <p class="mb-0"><strong>Phone:</strong> <?php echo htmlspecialchars($order['customer_phone']); ?></p>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Shipping Information</h6>
                            <?php if ($order['shipping_method'] === 'pickup'): ?>
                                <p class="mb-1"><strong>Method:</strong> Personal Pick-up</p>
                                <p class="mb-1">123 Mendiola St. Manila City</p>
                                <p class="mb-0">Contact Person: Anjhela Geron 09454545</p>
                            <?php else: ?>
                                <p class="mb-1"><strong>Method:</strong> Grab/Lalamove Delivery (arranged by customer)</p>
                                <p class="mb-1"><?php echo htmlspecialchars($order['shipping_address']); ?></p>
                                <p class="mb-0">
                                    <?php echo htmlspecialchars($order['shipping_city']); ?>, 
                                    <?php echo htmlspecialchars($order['shipping_state']); ?> 
                                    <?php echo htmlspecialchars($order['shipping_zip']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <h6>Payment Information</h6>
                            <p class="mb-1"><strong>Method:</strong> <?php echo $order['payment_method'] === 'gcash' ? 'GCash' : 'Over-the-counter Payment'; ?></p>
                            <?php if ($order['payment_method'] === 'gcash' && !empty($order['payment_screenshot'])): ?>
                                <p class="mb-1">Payment Screenshot:</p>
                                <a href="<?php echo htmlspecialchars($order['payment_screenshot']); ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                                    <i class="fas fa-image me-1"></i> View Screenshot
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if (!empty($order['notes'])): ?>
                        <div class="mb-4">
                            <h6>Order Notes</h6>
                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($order['notes'])); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <h6>Order Items</h6>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th class="text-center">Price</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order['items'] as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo htmlspecialchars($item['product']['image_url']); ?>" alt="<?php echo htmlspecialchars($item['product']['name']); ?>" class="me-3" style="width: 60px; height: 60px; object-fit: contain;">
                                                <div>
                                                    <h6 class="mb-0"><?php echo htmlspecialchars($item['product']['name']); ?></h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center"><?php echo formatPrice($item['price']); ?></td>
                                        <td class="text-center"><?php echo $item['quantity']; ?></td>
                                        <td class="text-end"><?php echo formatPrice($item['price'] * $item['quantity']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Subtotal</strong></td>
                                    <td class="text-end"><?php echo formatPrice($order['total']); ?></td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Shipping</strong></td>
                                    <td class="text-end">Free</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total</strong></td>
                                    <td class="text-end"><strong class="text-primary"><?php echo formatPrice($order['total']); ?></strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Help and Support -->
        <div class="col-lg-4">
            <div class="card border-0 rounded-4 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Help & Support</h5>
                </div>
                <div class="card-body">
                    <p>If you have any questions or concerns about your order, please contact our customer support team.</p>
                    <div class="d-grid">
                        <a href="mailto:support@soilshop.com" class="btn btn-outline-primary mb-2">
                            <i class="fas fa-envelope me-2"></i> Email Support
                        </a>
                        <a href="tel:+639123456789" class="btn btn-outline-primary">
                            <i class="fas fa-phone me-2"></i> Call Support
                        </a>
                    </div>
                    
                    <hr>
                    
                    <h6>Need to make changes?</h6>
                    <p class="text-muted small">If you need to cancel or modify your order, please contact us as soon as possible. Orders that have already been shipped cannot be cancelled.</p>
                </div>
            </div>
            
            <!-- Continue Shopping -->
            <div class="d-grid gap-2">
                <a href="index.php?page=products" class="btn btn-primary">
                    <i class="fas fa-shopping-bag me-2"></i> Continue Shopping
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 20px 0;
    position: relative;
}

.timeline::before {
    content: '';
    position: absolute;
    top: 50px;
    left: 10%;
    right: 10%;
    height: 2px;
    background-color: #e9ecef;
    z-index: 0;
}

.timeline-item {
    flex: 1;
    text-align: center;
    position: relative;
    z-index: 1;
}

.timeline-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background-color: #fff;
    border: 2px solid #e9ecef;
    color: #adb5bd;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
    font-size: 1.5rem;
}

.timeline-item.active .timeline-icon {
    background-color: #28a745;
    border-color: #28a745;
    color: #fff;
}

.timeline-content {
    padding: 0 10px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Print functionality
    const printOrderBtn = document.getElementById('print-order');
    if (printOrderBtn) {
        printOrderBtn.addEventListener('click', function() {
            window.print();
        });
    }
});
</script>