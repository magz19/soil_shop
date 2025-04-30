<?php
// Check if user is admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit;
}

// Include helper functions
require_once 'includes/functions.php';

// Get total orders count
$totalOrders = getTotalOrdersCount();

// Get total revenue
$totalRevenue = getTotalRevenue();

// Get recent orders
$recentOrders = getRecentOrders(5);

// Get orders by status
$ordersByStatus = getOrdersByStatus();

// Format for chart data
$statusLabels = [];
$statusData = [];
foreach ($ordersByStatus as $status => $count) {
    $label = ucfirst(str_replace('_', ' ', $status));
    $statusLabels[] = $label;
    $statusData[] = $count;
}
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Admin Dashboard</h1>
        <div>
            <a href="index.php?page=admin&admin_page=orders" class="btn btn-primary">
                <i class="fas fa-list me-2"></i> View All Orders
            </a>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="row">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1 text-muted">
                                Total Orders
                            </div>
                            <div class="h3 mb-0 font-weight-bold"><?php echo $totalOrders; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-bag fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1 text-muted">
                                Total Revenue
                            </div>
                            <div class="h3 mb-0 font-weight-bold"><?php echo formatPrice($totalRevenue); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-peso-sign fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1 text-muted">
                                Pending Orders
                            </div>
                            <div class="h3 mb-0 font-weight-bold">
                                <?php echo isset($ordersByStatus['pending']) ? $ordersByStatus['pending'] : 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1 text-muted">
                                Delivered Orders
                            </div>
                            <div class="h3 mb-0 font-weight-bold">
                                <?php echo isset($ordersByStatus['delivered']) ? $ordersByStatus['delivered'] : 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Status Chart -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Orders by Status</h5>
                </div>
                <div class="card-body">
                    <canvas id="orderStatusChart" height="250"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Recent Orders -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Orders</h5>
                    <a href="index.php?page=admin&admin_page=orders" class="text-decoration-none">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recentOrders)): ?>
                                    <?php foreach ($recentOrders as $order): ?>
                                        <tr>
                                            <td>#<?php echo $order['id']; ?></td>
                                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                            <td><?php echo isset($order['customer_name']) ? htmlspecialchars($order['customer_name']) : 'Customer #' . $order['user_id']; ?></td>
                                            <td><?php echo formatPrice($order['total']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo getStatusBadgeClass($order['status']); ?>">
                                                    <?php echo ucfirst(str_replace('_', ' ', $order['status'])); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="index.php?page=admin&admin_page=order_details&id=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No recent orders found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Order Status Chart
    const statusLabels = <?php echo json_encode($statusLabels); ?>;
    const statusData = <?php echo json_encode($statusData); ?>;
    
    const statusColors = [
        '#ffc107', // pending
        '#17a2b8', // processing
        '#6610f2', // shipped
        '#007bff', // out_for_delivery
        '#28a745', // delivered
        '#dc3545'  // cancelled
    ];
    
    const ctx = document.getElementById('orderStatusChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusData,
                backgroundColor: statusColors.slice(0, statusLabels.length),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        padding: 20,
                        boxWidth: 15
                    }
                }
            },
            cutout: '65%'
        }
    });
});
</script>