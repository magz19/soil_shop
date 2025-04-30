<?php
// Get order ID from URL or session
$orderId = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_SESSION['last_order_id']) ? $_SESSION['last_order_id'] : 0);

// Clear the session variable
if (isset($_SESSION['last_order_id'])) {
    unset($_SESSION['last_order_id']);
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
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                    </div>
                    <h1 class="mb-3">Order Confirmed!</h1>
                    <p class="lead mb-1">Thank you for your purchase.</p>
                    <p class="mb-4">Your order #<?php echo $order['id']; ?> has been placed successfully.</p>
                    
                    <div class="d-grid gap-2 d-md-block">
                        <a href="index.php?page=order-details&id=<?php echo $order['id']; ?>" class="btn btn-primary me-md-2">View Order Details</a>
                        <a href="index.php" class="btn btn-outline-secondary">Continue Shopping</a>
                    </div>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Order Information</h6>
                            <p><strong>Order Number:</strong> #<?php echo $order['id']; ?></p>
                            <p><strong>Order Date:</strong> <?php echo date('F d, Y', strtotime($order['created_at'])); ?></p>
                            <p><strong>Payment Method:</strong> <?php echo $order['payment_method'] == 'gcash' ? 'GCash' : 'Over-the-counter Payment'; ?></p>
                            <p><strong>Total:</strong> <?php echo formatPrice($order['total']); ?></p>
                        </div>
                        
                        <div class="col-md-6">
                            <h6>Shipping Information</h6>
                            <p><strong>Shipping Method:</strong> 
                                <?php echo $order['shipping_method'] == 'pickup' ? 'Personal Pick-up' : 'Grab/Lalamove Delivery'; ?>
                            </p>
                            <?php if ($order['shipping_method'] == 'pickup'): ?>
                                <p>Pick up your order at 123 Mendiola St. Manila City</p>
                                <p>Contact Person: Anjhela Geron 09454545</p>
                            <?php else: ?>
                                <p><strong>Address:</strong> <?php echo $order['shipping_address']; ?></p>
                                <p><strong>City:</strong> <?php echo $order['shipping_city']; ?>, <?php echo $order['shipping_state']; ?> <?php echo $order['shipping_zip']; ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Order Items -->
                    <h6>Order Items</h6>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th class="text-end">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order['items'] as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['product']['name']); ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td class="text-end"><?php echo formatPrice($item['price'] * $item['quantity']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="text-end"><strong>Total:</strong></td>
                                    <td class="text-end"><strong><?php echo formatPrice($order['total']); ?></strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <div class="text-center mt-4">
                        <p>A confirmation email has been sent to your email address.</p>
                        <p>If you have any questions, please contact our <a href="#">customer service</a>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>