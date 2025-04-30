<?php
// Get user ID (would come from session in a real auth system)
$userId = 1;

// Get user orders
$orders = getUserOrders($userId);
?>

<div class="container">
    <h1 class="mb-4">My Orders</h1>
    
    <?php if (empty($orders)): ?>
        <div class="alert alert-info">
            You don't have any orders yet. <a href="index.php" class="alert-link">Continue shopping</a>.
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Order History</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order Number</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td>#<?php echo $order['id']; ?></td>
                                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                            <td>
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
                                            </td>
                                            <td><?php echo formatPrice($order['total']); ?></td>
                                            <td>
                                                <a href="index.php?page=order-details&id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary">View Details</a>
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
    <?php endif; ?>
</div>

<style>
.status-pending {
    color: #ffc107;
}

.status-processing {
    color: #17a2b8;
}

.status-shipped {
    color: #6610f2;
}

.status-out_for_delivery {
    color: #007bff;
}

.status-delivered {
    color: #28a745;
}

.status-cancelled {
    color: #dc3545;
}
</style>