<?php
// Get order ID from URL
$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get order with items
$order = getOrderWithItems($orderId);

// If order not found, display error message
if (!$order) {
    echo '<div class="alert alert-danger">Order not found.</div>';
    exit;
}

// Get the status timeline
$statusTimeline = [
    'pending' => 'Order received',
    'processing' => 'Processing order',
    'shipped' => 'Order shipped',
    'out_for_delivery' => 'Out for delivery',
    'delivered' => 'Order delivered'
];

// Current status index
$currentStatusIndex = array_search($order['status'], array_keys($statusTimeline));
?>

<div class="container">
    <div class="mb-4">
        <a href="index.php?page=orders" class="text-decoration-none">
            <i class="fas fa-arrow-left"></i> Back to Orders
        </a>
    </div>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Order #<?php echo $order['id']; ?></h5>
                    <span class="status-<?php echo $order['status']; ?>">
                        <?php
                        switch ($order['status']) {
                            case 'pending':
                                echo 'Pending';
                                break;
                            case 'processing':
                                echo 'Processing';
                                break;
                            case 'shipped':
                                echo 'Shipped';
                                break;
                            case 'out_for_delivery':
                                echo 'Out for Delivery';
                                break;
                            case 'delivered':
                                echo 'Delivered';
                                break;
                            case 'cancelled':
                                echo 'Cancelled';
                                break;
                            default:
                                echo ucfirst($order['status']);
                        }
                        ?>
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Order Information</h6>
                            <p><strong>Order Date:</strong> <?php echo date('F d, Y', strtotime($order['created_at'])); ?></p>
                            <p><strong>Status:</strong> 
                                <span class="status-<?php echo $order['status']; ?>">
                                    <?php
                                    switch ($order['status']) {
                                        case 'pending':
                                            echo 'Pending';
                                            break;
                                        case 'processing':
                                            echo 'Processing';
                                            break;
                                        case 'shipped':
                                            echo 'Shipped';
                                            break;
                                        case 'out_for_delivery':
                                            echo 'Out for Delivery';
                                            break;
                                        case 'delivered':
                                            echo 'Delivered';
                                            break;
                                        case 'cancelled':
                                            echo 'Cancelled';
                                            break;
                                        default:
                                            echo ucfirst($order['status']);
                                    }
                                    ?>
                                </span>
                            </p>
                            <p><strong>Total:</strong> <?php echo formatPrice($order['total']); ?></p>
                            <p><strong>Payment Method:</strong> <?php echo $order['payment_method'] == 'gcash' ? 'GCash' : 'Over-the-counter Payment'; ?></p>
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
                                <p><strong>City:</strong> <?php echo $order['shipping_city']; ?></p>
                                <p><strong>State/Province:</strong> <?php echo $order['shipping_state']; ?></p>
                                <p><strong>Zip Code:</strong> <?php echo $order['shipping_zip']; ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Order Status Timeline -->
                    <?php if ($order['status'] != 'cancelled' && $currentStatusIndex !== false): ?>
                        <div class="mb-4">
                            <h6>Order Status</h6>
                            <div class="order-timeline">
                                <?php 
                                $i = 0;
                                foreach ($statusTimeline as $status => $label): 
                                    $isCompleted = $i <= $currentStatusIndex;
                                    $isActive = $i == $currentStatusIndex;
                                ?>
                                    <div class="order-tracking-step <?php echo $isCompleted ? 'completed' : ''; ?> <?php echo $isActive ? 'active' : ''; ?>">
                                        <?php echo $label; ?>
                                    </div>
                                <?php 
                                    $i++;
                                endforeach; 
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
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
                                <?php 
                                $items = is_array($order['items']) ? $order['items'] : [];
                                foreach ($items as $item): 
                                ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if (isset($item['product']['image_url'])): ?>
                                                <img src="<?php echo htmlspecialchars($item['product']['image_url']); ?>" alt="<?php echo htmlspecialchars($item['product']['name']); ?>" class="order-item-image me-3">
                                                <?php endif; ?>
                                                <div>
                                                    <h6 class="mb-0"><?php echo htmlspecialchars($item['product']['name']); ?></h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td class="text-end"><?php echo formatPrice($item['price']); ?></td>
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
                    
                    <!-- Payment Screenshot (if applicable) -->
                    <?php if ($order['payment_method'] == 'gcash' && !empty($order['payment_screenshot'])): ?>
                        <div class="mt-4">
                            <h6>Payment Screenshot</h6>
                            <img src="<?php echo htmlspecialchars($order['payment_screenshot']); ?>" alt="Payment Screenshot" class="img-fluid img-thumbnail" style="max-width: 300px;">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Need Help Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Need Help?</h5>
                </div>
                <div class="card-body">
                    <p>If you have any questions or concerns about your order, please contact our customer service:</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-phone me-2"></i> 09454545</li>
                        <li><i class="fas fa-envelope me-2"></i> support@soil-products.com</li>
                    </ul>
                </div>
            </div>
            
            <!-- Reorder Section -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Reorder</h5>
                </div>
                <div class="card-body">
                    <p>Want to buy these items again?</p>
                    <form action="actions/reorder.php" method="post">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <button type="submit" class="btn btn-warning w-100">Add All to Cart</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.order-timeline {
    position: relative;
    display: flex;
    justify-content: space-between;
    margin: 30px 0;
    padding-top: 10px;
}

.order-timeline::before {
    content: '';
    position: absolute;
    top: 20px;
    left: 0;
    right: 0;
    height: 2px;
    background-color: #e9ecef;
    z-index: 0;
}

.order-tracking-step {
    position: relative;
    padding-top: 30px;
    z-index: 1;
    text-align: center;
    font-size: 0.85rem;
    flex: 1;
}

.order-tracking-step::before {
    content: '';
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background-color: #e9ecef;
    z-index: 1;
}

.order-tracking-step.completed::before {
    background-color: #28a745;
}

.order-tracking-step.active::before {
    background-color: #007bff;
}

.order-item-image {
    width: 60px;
    height: 60px;
    object-fit: contain;
}

.status-pending {
    color: #ffc107;
    font-weight: bold;
}

.status-processing {
    color: #17a2b8;
    font-weight: bold;
}

.status-shipped {
    color: #6610f2;
    font-weight: bold;
}

.status-out_for_delivery {
    color: #007bff;
    font-weight: bold;
}

.status-delivered {
    color: #28a745;
    font-weight: bold;
}

.status-cancelled {
    color: #dc3545;
    font-weight: bold;
}
</style>