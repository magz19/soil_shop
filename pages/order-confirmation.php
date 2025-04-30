<?php
// Include helper functions if not already included
if (!function_exists('formatPrice')) {
    require_once 'includes/functions.php';
}

// Get order ID from session
$orderId = $_SESSION['last_order_id'] ?? 0;

// If no order ID in session, redirect to home
if ($orderId === 0) {
    echo '<script>window.location.href = "index.php";</script>';
    exit;
}

// Get order details
$order = getOrderWithItems($orderId);

// If order not found, redirect to home
if (!$order) {
    echo '<script>window.location.href = "index.php";</script>';
    exit;
}
?>

<div class="container">
    <div class="row mb-5">
        <div class="col-lg-8 mx-auto text-center">
            <div class="mb-4">
                <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
            </div>
            <h1 class="mb-3">Order Placed Successfully!</h1>
            <p class="lead mb-4">Thank you for your purchase. Your order #<?php echo $order['id']; ?> has been received and is being processed.</p>
            <div class="alert alert-info mb-4" role="alert">
                <i class="fas fa-envelope me-2"></i> A confirmation email has been sent to <strong><?php echo htmlspecialchars($order['customer_email']); ?></strong>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card border-0 rounded-4 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Order Summary</h5>
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
                            <p class="mb-0"><strong>Method:</strong> <?php echo $order['payment_method'] === 'gcash' ? 'GCash' : 'Over-the-counter Payment'; ?></p>
                        </div>
                    </div>
                    
                    <h6>Order Items</h6>
                    <div class="table-responsive">
                        <table class="table">
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
                                                <img src="<?php echo htmlspecialchars($item['product']['image_url']); ?>" alt="<?php echo htmlspecialchars($item['product']['name']); ?>" class="me-3" style="width: 50px; height: 50px; object-fit: contain;">
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
                                    <td colspan="3" class="text-end"><strong>Total</strong></td>
                                    <td class="text-end"><strong class="text-primary"><?php echo formatPrice($order['total']); ?></strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="index.php?page=order-details&id=<?php echo $order['id']; ?>" class="btn btn-primary">
                    <i class="fas fa-file-alt me-2"></i> View Order Details
                </a>
                <a href="index.php?page=products" class="btn btn-outline-primary">
                    <i class="fas fa-shopping-bag me-2"></i> Continue Shopping
                </a>
            </div>
        </div>
    </div>
</div>