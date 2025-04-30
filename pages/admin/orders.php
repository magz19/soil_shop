<?php
// Get all orders for admin
$orders = getAllOrders();

// Get filter parameters
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Filter orders by status if specified
if (!empty($status)) {
    $filteredOrders = array_filter($orders, function($order) use ($status) {
        return $order['status'] === $status;
    });
} else {
    $filteredOrders = $orders;
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Manage Orders</h1>
        <a href="index.php?page=admin" class="btn btn-outline-secondary">Back to Dashboard</a>
    </div>
    
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="" method="get" class="row g-3 align-items-end">
                <input type="hidden" name="page" value="admin">
                <input type="hidden" name="admin_page" value="orders">
                
                <div class="col-md-3">
                    <label for="status" class="form-label">Filter by Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">All Orders</option>
                        <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="processing" <?php echo $status === 'processing' ? 'selected' : ''; ?>>Processing</option>
                        <option value="shipped" <?php echo $status === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                        <option value="out_for_delivery" <?php echo $status === 'out_for_delivery' ? 'selected' : ''; ?>>Out for Delivery</option>
                        <option value="delivered" <?php echo $status === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                        <option value="cancelled" <?php echo $status === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <?php if (!empty($status)): ?>
                        <a href="index.php?page=admin&admin_page=orders" class="btn btn-outline-secondary">Clear Filters</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Orders Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Orders 
                <?php if (!empty($status)): ?>
                    (Status: <?php echo ucfirst($status); ?>)
                <?php endif; ?>
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($filteredOrders)): ?>
                            <tr>
                                <td colspan="7" class="text-center">No orders found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($filteredOrders as $order): ?>
                                <tr>
                                    <td>#<?php echo $order['id']; ?></td>
                                    <td><?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></td>
                                    <td>
                                        <?php 
                                        // Get user details (in a real application)
                                        echo "User #" . $order['user_id']; 
                                        ?>
                                    </td>
                                    <td><?php echo isset($order['item_count']) ? $order['item_count'] : '—'; ?></td>
                                    <td><?php echo formatPrice($order['total']); ?></td>
                                    <td>
                                        <form action="actions/admin/update_order_status.php" method="post" class="update-status-form">
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            <select name="status" class="form-select form-select-sm status-select" data-original-status="<?php echo $order['status']; ?>">
                                                <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                                <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                                <option value="out_for_delivery" <?php echo $order['status'] === 'out_for_delivery' ? 'selected' : ''; ?>>Out for Delivery</option>
                                                <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                                <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td>
                                        <a href="index.php?page=admin&admin_page=order-details&id=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Status change handling
    const statusSelects = document.querySelectorAll('.status-select');
    
    statusSelects.forEach(select => {
        const originalStatus = select.getAttribute('data-original-status');
        
        select.addEventListener('change', function() {
            const form = this.closest('form');
            const newStatus = this.value;
            
            if (newStatus !== originalStatus) {
                if (confirm('Are you sure you want to update the order status to ' + this.options[this.selectedIndex].text + '?')) {
                    form.submit();
                } else {
                    this.value = originalStatus;
                }
            }
        });
    });
});
</script>