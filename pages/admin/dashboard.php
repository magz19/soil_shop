<?php
// Get all orders for admin
$orders = getAllOrders();

// Calculate statistics
$totalOrders = count($orders);
$pendingOrders = 0;
$processingOrders = 0;
$shippedOrders = 0;
$deliveredOrders = 0;
$cancelledOrders = 0;
$totalRevenue = 0;

foreach ($orders as $order) {
    $totalRevenue += $order['total'];
    
    switch ($order['status']) {
        case 'pending':
            $pendingOrders++;
            break;
        case 'processing':
            $processingOrders++;
            break;
        case 'shipped':
        case 'out_for_delivery':
            $shippedOrders++;
            break;
        case 'delivered':
            $deliveredOrders++;
            break;
        case 'cancelled':
            $cancelledOrders++;
            break;
    }
}

// Get recent orders (last 5)
$recentOrders = array_slice($orders, 0, 5);
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Admin Dashboard</h1>
        <div>
            <a href="index.php" class="btn btn-outline-secondary me-2">View Store</a>
            <a href="index.php?page=admin&admin_page=orders" class="btn btn-primary">Manage Orders</a>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalOrders; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-bag fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo formatPrice($totalRevenue); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-peso-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $pendingOrders; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Delivered Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $deliveredOrders; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-truck fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Order Status Overview -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Order Status Overview</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:300px;">
                        <canvas id="orderStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Orders</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recentOrders)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No orders found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($recentOrders as $order): ?>
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
                                                <a href="index.php?page=admin&admin_page=order-details&id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary">View</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="index.php?page=admin&admin_page=orders" class="btn btn-outline-primary">View All Orders</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary {
    border-left: 4px solid #4e73df;
}

.border-left-success {
    border-left: 4px solid #1cc88a;
}

.border-left-warning {
    border-left: 4px solid #f6c23e;
}

.border-left-info {
    border-left: 4px solid #36b9cc;
}

.status-pending {
    color: #ffc107;
    font-weight: bold;
}

.status-processing {
    color: #17a2b8;
    font-weight: bold;
}

.status-shipped,
.status-out_for_delivery {
    color: #6610f2;
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

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Order Status Chart
    const ctx = document.getElementById('orderStatusChart').getContext('2d');
    const orderStatusChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'],
            datasets: [{
                data: [
                    <?php echo $pendingOrders; ?>,
                    <?php echo $processingOrders; ?>,
                    <?php echo $shippedOrders; ?>,
                    <?php echo $deliveredOrders; ?>,
                    <?php echo $cancelledOrders; ?>
                ],
                backgroundColor: [
                    '#f6c23e',
                    '#4e73df',
                    '#6610f2',
                    '#1cc88a',
                    '#e74a3b'
                ],
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>