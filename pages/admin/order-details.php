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
?>

<div class="container-fluid">
    <div class="mb-4">
        <a href="index.php?page=admin&admin_page=orders" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Orders
        </a>
    </div>
    
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Order #<?php echo $order['id']; ?> Details</h6>
                    
                    <form action="actions/admin/update_order_status.php" method="post">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <div class="d-flex align-items-center">
                            <label for="status" class="me-2">Status:</label>
                            <select name="status" id="status" class="form-select form-select-sm status-select me-2" style="width: 150px;">
                                <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                <option value="out_for_delivery" <?php echo $order['status'] === 'out_for_delivery' ? 'selected' : ''; ?>>Out for Delivery</option>
                                <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary">Update Status</button>
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Order Information</h6>
                            <p><strong>Order ID:</strong> #<?php echo $order['id']; ?></p>
                            <p><strong>Order Date:</strong> <?php echo date('F d, Y h:i A', strtotime($order['created_at'])); ?></p>
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
                            
                            <?php if ($order['payment_method'] == 'gcash' && !empty($order['payment_screenshot'])): ?>
                                <div class="mt-3">
                                    <h6 class="font-weight-bold">Payment Screenshot</h6>
                                    <a href="<?php echo htmlspecialchars($order['payment_screenshot']); ?>" target="_blank">
                                        <img src="<?php echo htmlspecialchars($order['payment_screenshot']); ?>" alt="Payment Screenshot" class="img-fluid img-thumbnail" style="max-width: 200px;">
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">Customer Information</h6>
                            <?php
                            // In a real application, you would get customer information from the database
                            // For now, let's display the shipping information
                            ?>
                            <p><strong>Customer ID:</strong> #<?php echo $order['userId']; ?></p>
                            <p><strong>Shipping Method:</strong> 
                                <?php echo $order['shipping_method'] == 'pickup' ? 'Personal Pick-up' : 'Grab/Lalamove Delivery'; ?>
                            </p>
                            
                            <?php if ($order['shipping_method'] == 'pickup'): ?>
                                <p>Pick up location: 123 Mendiola St. Manila City</p>
                                <p>Contact Person: Anjhela Geron 09454545</p>
                            <?php else: ?>
                                <p><strong>Shipping Address:</strong> <?php echo $order['shipping_address']; ?></p>
                                <p><strong>City:</strong> <?php echo $order['shipping_city']; ?></p>
                                <p><strong>State/Province:</strong> <?php echo $order['shipping_state']; ?></p>
                                <p><strong>Zip Code:</strong> <?php echo $order['shipping_zip']; ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <h6 class="font-weight-bold">Order Items</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th>Product ID</th>
                                            <th>Unit Price</th>
                                            <th>Quantity</th>
                                            <th>Total</th>
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
                                                            <small class="text-muted"><?php echo htmlspecialchars($item['product']['category']); ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?php echo $item['productId']; ?></td>
                                                <td><?php echo formatPrice($item['price']); ?></td>
                                                <td><?php echo $item['quantity']; ?></td>
                                                <td><?php echo formatPrice($item['price'] * $item['quantity']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                            <td><strong><?php echo formatPrice($order['total']); ?></strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border-left-info">
                                <div class="card-body">
                                    <h6 class="font-weight-bold">Order Timeline</h6>
                                    <?php
                                    // In a real application, you would get the order timeline from the database
                                    // For now, let's display a simple timeline based on the status
                                    $timelineItems = [];
                                    
                                    // Always add created
                                    $timelineItems[] = [
                                        'status' => 'created',
                                        'label' => 'Order Created',
                                        'date' => $order['created_at'],
                                        'completed' => true
                                    ];
                                    
                                    // Add status based on current order status
                                    $statusOrder = ['pending', 'processing', 'shipped', 'out_for_delivery', 'delivered'];
                                    $currentStatusIndex = array_search($order['status'], $statusOrder);
                                    
                                    if ($currentStatusIndex !== false) {
                                        for ($i = 0; $i <= $currentStatusIndex; $i++) {
                                            $status = $statusOrder[$i];
                                            switch ($status) {
                                                case 'pending':
                                                    $timelineItems[] = [
                                                        'status' => 'pending',
                                                        'label' => 'Order Pending',
                                                        'date' => $order['created_at'],
                                                        'completed' => true
                                                    ];
                                                    break;
                                                case 'processing':
                                                    $timelineItems[] = [
                                                        'status' => 'processing',
                                                        'label' => 'Order Processing',
                                                        'date' => $order['updated_at'] ?: $order['created_at'],
                                                        'completed' => true
                                                    ];
                                                    break;
                                                case 'shipped':
                                                    $timelineItems[] = [
                                                        'status' => 'shipped',
                                                        'label' => 'Order Shipped',
                                                        'date' => $order['updated_at'] ?: $order['created_at'],
                                                        'completed' => true
                                                    ];
                                                    break;
                                                case 'out_for_delivery':
                                                    $timelineItems[] = [
                                                        'status' => 'out_for_delivery',
                                                        'label' => 'Out for Delivery',
                                                        'date' => $order['updated_at'] ?: $order['created_at'],
                                                        'completed' => true
                                                    ];
                                                    break;
                                                case 'delivered':
                                                    $timelineItems[] = [
                                                        'status' => 'delivered',
                                                        'label' => 'Order Delivered',
                                                        'date' => $order['updated_at'] ?: $order['created_at'],
                                                        'completed' => true
                                                    ];
                                                    break;
                                            }
                                        }
                                        
                                        // Add future statuses
                                        for ($i = $currentStatusIndex + 1; $i < count($statusOrder); $i++) {
                                            $status = $statusOrder[$i];
                                            switch ($status) {
                                                case 'pending':
                                                    $timelineItems[] = [
                                                        'status' => 'pending',
                                                        'label' => 'Order Pending',
                                                        'date' => null,
                                                        'completed' => false
                                                    ];
                                                    break;
                                                case 'processing':
                                                    $timelineItems[] = [
                                                        'status' => 'processing',
                                                        'label' => 'Order Processing',
                                                        'date' => null,
                                                        'completed' => false
                                                    ];
                                                    break;
                                                case 'shipped':
                                                    $timelineItems[] = [
                                                        'status' => 'shipped',
                                                        'label' => 'Order Shipped',
                                                        'date' => null,
                                                        'completed' => false
                                                    ];
                                                    break;
                                                case 'out_for_delivery':
                                                    $timelineItems[] = [
                                                        'status' => 'out_for_delivery',
                                                        'label' => 'Out for Delivery',
                                                        'date' => null,
                                                        'completed' => false
                                                    ];
                                                    break;
                                                case 'delivered':
                                                    $timelineItems[] = [
                                                        'status' => 'delivered',
                                                        'label' => 'Order Delivered',
                                                        'date' => null,
                                                        'completed' => false
                                                    ];
                                                    break;
                                            }
                                        }
                                    } else if ($order['status'] === 'cancelled') {
                                        $timelineItems[] = [
                                            'status' => 'cancelled',
                                            'label' => 'Order Cancelled',
                                            'date' => $order['updated_at'] ?: $order['created_at'],
                                            'completed' => true
                                        ];
                                    }
                                    ?>
                                    
                                    <div class="timeline">
                                        <?php foreach ($timelineItems as $item): ?>
                                            <div class="timeline-item <?php echo $item['completed'] ? 'completed' : ''; ?>">
                                                <div class="timeline-marker"></div>
                                                <div class="timeline-content">
                                                    <h6 class="mb-0"><?php echo $item['label']; ?></h6>
                                                    <?php if ($item['date']): ?>
                                                        <small><?php echo date('F d, Y h:i A', strtotime($item['date'])); ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
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

.border-left-info {
    border-left: 4px solid #36b9cc;
}

.timeline {
    position: relative;
    padding-left: 30px;
    margin-top: 20px;
}

.timeline-item {
    position: relative;
    padding-bottom: 20px;
    opacity: 0.5;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-item.completed {
    opacity: 1;
}

.timeline::before {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    left: 9px;
    width: 2px;
    background-color: #e9ecef;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background-color: #e9ecef;
}

.timeline-item.completed .timeline-marker {
    background-color: #36b9cc;
}
</style>