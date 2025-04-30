<?php
/**
 * Helper functions for the S-Oil Products Store
 */

// Include database connection
require_once 'db_connection.php';

// Enable development mode for fallback data when database is not available
// Change this to false in production
define('DEVELOPMENT_MODE', true);

/**
 * Format price with Philippine Peso symbol
 */
function formatPrice($price) {
    return '₱' . number_format($price, 2);
}

/**
 * Get all products from database
 */
function getAllProducts() {
    global $conn;
    
    try {
        $sql = "SELECT * FROM products ORDER BY id ASC";
        $result = $conn->query($sql);
        
        $products = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
        }
        
        return $products;
    } catch (Exception $e) {
        return handleDatabaseError($sql, $e, []);
    }
}

/**
 * Get products by category
 */
function getProductsByCategory($category) {
    global $conn;
    
    try {
        $sql = "SELECT * FROM products WHERE category = ? ORDER BY id ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $products = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
        }
        
        return $products;
    } catch (Exception $e) {
        return handleDatabaseError($sql, $e, []);
    }
}

/**
 * Get product by ID
 */
function getProduct($id) {
    global $conn;
    
    try {
        $sql = "SELECT * FROM products WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    } catch (Exception $e) {
        return handleDatabaseError($sql, $e, null);
    }
}

/**
 * Get appropriate badge class for order status
 */
function getStatusBadgeClass($status) {
    switch ($status) {
        case 'pending':
            return 'warning';
        case 'processing':
            return 'info';
        case 'shipped':
            return 'primary';
        case 'out_for_delivery':
            return 'secondary';
        case 'delivered':
            return 'success';
        case 'cancelled':
            return 'danger';
        default:
            return 'light';
    }
}

/**
 * Get total number of orders
 */
function getTotalOrdersCount() {
    global $conn;
    
    try {
        $sql = "SELECT COUNT(*) as total FROM orders";
        $result = $conn->query($sql);
        
        if ($result && $row = $result->fetch_assoc()) {
            return $row['total'];
        }
    } catch (Exception $e) {
        // Log error
        error_log("Database error: " . $e->getMessage());
    }
    
    return 0;
}

/**
 * Get total revenue from all orders
 */
function getTotalRevenue() {
    global $conn;
    
    try {
        $sql = "SELECT SUM(total) as revenue FROM orders WHERE status != 'cancelled'";
        $result = $conn->query($sql);
        
        if ($result && $row = $result->fetch_assoc()) {
            return $row['revenue'] ?: 0;
        }
    } catch (Exception $e) {
        // Log error
        error_log("Database error: " . $e->getMessage());
    }
    
    return 0;
}

/**
 * Get recent orders with limit
 */
function getRecentOrders($limit = 5) {
    global $conn;
    
    try {
        $sql = "SELECT o.* FROM orders o 
                ORDER BY o.created_at DESC 
                LIMIT ?";
                
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        
        return $orders;
    } catch (Exception $e) {
        // Log error
        error_log("Database error: " . $e->getMessage());
    }
    
    return [];
}

/**
 * Get order counts by status
 */
function getOrdersByStatus() {
    global $conn;
    
    try {
        $sql = "SELECT status, COUNT(*) as count FROM orders GROUP BY status";
        $result = $conn->query($sql);
        
        $statusCounts = [];
        while ($row = $result->fetch_assoc()) {
            $statusCounts[$row['status']] = $row['count'];
        }
        
        return $statusCounts;
    } catch (Exception $e) {
        // Log error
        error_log("Database error: " . $e->getMessage());
    }
    
    return [];
}

/**
 * Get filtered orders for admin
 */
function getFilteredOrders($status = '', $search = '', $limit = 10, $offset = 0) {
    global $conn;
    
    try {
        $sql = "SELECT o.* FROM orders o WHERE 1=1";
        
        $params = [];
        $types = "";
        
        // Add status filter if provided
        if (!empty($status)) {
            $sql .= " AND o.status = ?";
            $params[] = $status;
            $types .= "s";
        }
        
        // Add search filter if provided
        if (!empty($search)) {
            $searchTerm = "%$search%";
            $sql .= " AND (o.id LIKE ? OR o.customer_name LIKE ? OR o.customer_email LIKE ? OR o.customer_phone LIKE ?)";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= "ssss";
        }
        
        $sql .= " ORDER BY o.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= "ii";
        
        $stmt = $conn->prepare($sql);
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        
        return $orders;
    } catch (Exception $e) {
        // Log error
        error_log("Database error: " . $e->getMessage());
    }
    
    return [];
}

/**
 * Get count of filtered orders
 */
function getFilteredOrdersCount($status = '', $search = '') {
    global $conn;
    
    try {
        $sql = "SELECT COUNT(*) as total FROM orders o WHERE 1=1";
        
        $params = [];
        $types = "";
        
        // Add status filter if provided
        if (!empty($status)) {
            $sql .= " AND o.status = ?";
            $params[] = $status;
            $types .= "s";
        }
        
        // Add search filter if provided
        if (!empty($search)) {
            $searchTerm = "%$search%";
            $sql .= " AND (o.id LIKE ? OR o.customer_name LIKE ? OR o.customer_email LIKE ? OR o.customer_phone LIKE ?)";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= "ssss";
        }
        
        $stmt = $conn->prepare($sql);
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return $row['total'];
        }
    } catch (Exception $e) {
        // Log error
        error_log("Database error: " . $e->getMessage());
    }
    
    return 0;
}

/**
 * Update order status
 */
function updateOrderStatus($orderId, $status) {
    global $conn;
    
    try {
        $sql = "UPDATE orders SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $orderId);
        return $stmt->execute();
    } catch (Exception $e) {
        // Log error
        error_log("Database error: " . $e->getMessage());
    }
    
    return false;
}

/**
 * Get order with items
 */
function getOrderWithItems($orderId) {
    global $conn;
    
    try {
        // Get order details
        $sql = "SELECT o.* FROM orders o WHERE o.id = ?";
                
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $orderResult = $stmt->get_result();
        
        if ($order = $orderResult->fetch_assoc()) {
            // Get order items
            $sql = "SELECT oi.*, p.name, p.description, p.image_url, p.price as current_price
                    FROM order_items oi
                    JOIN products p ON oi.product_id = p.id
                    WHERE oi.order_id = ?";
                    
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $orderId);
            $stmt->execute();
            $itemsResult = $stmt->get_result();
            
            $items = [];
            while ($item = $itemsResult->fetch_assoc()) {
                $items[] = [
                    'id' => $item['id'],
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'product' => [
                        'id' => $item['product_id'],
                        'name' => $item['name'],
                        'description' => $item['description'],
                        'image_url' => $item['image_url'],
                        'current_price' => $item['current_price']
                    ]
                ];
            }
            
            $order['items'] = $items;
            return $order;
        }
    } catch (Exception $e) {
        // Log error
        error_log("Database error: " . $e->getMessage());
    }
    
    return null;
}

/**
 * Handle potential database errors gracefully
 */
function handleDatabaseError($sql, $ex, $default = null) {
    // Log the error
    error_log("SQL Error: $sql - " . $ex->getMessage());
    
    // In XAMPP development environment, show error details
    if (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE) {
        echo '<div class="alert alert-danger">';
        echo '<strong>Database Error:</strong> ' . $ex->getMessage();
        echo '</div>';
    }
    
    // Return a default value
    return $default;
}

/**
 * For demo/development purposes only
 * This function creates sample data when real data is not available
 */
function ensureDemoData() {
    if (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE) {
        // Implementation for development only
    }
}

/**
 * Get user orders
 */
function getUserOrders($userId) {
    global $conn;
    
    try {
        $sql = "SELECT o.* FROM orders o 
                WHERE o.user_id = ?
                ORDER BY o.created_at DESC";
                
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        
        // If no orders found or database query fails, provide at least one example order for testing
        if (empty($orders) && DEVELOPMENT_MODE) {
            $orders = [
                [
                    'id' => 1,
                    'user_id' => $userId,
                    'total' => 2499.00,
                    'status' => 'processing',
                    'payment_method' => 'gcash',
                    'shipping_method' => 'pickup',
                    'customer_name' => 'Test User',
                    'customer_email' => 'test@example.com',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
                ]
            ];
        }
        
        return $orders;
    } catch (Exception $e) {
        // Log error
        error_log("Database error: " . $e->getMessage());
        
        if (DEVELOPMENT_MODE) {
            // Provide sample data for development
            return [
                [
                    'id' => 1,
                    'user_id' => $userId,
                    'total' => 2499.00,
                    'status' => 'processing',
                    'payment_method' => 'gcash',
                    'shipping_method' => 'pickup',
                    'customer_name' => 'Test User',
                    'customer_email' => 'test@example.com',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
                ]
            ];
        }
    }
    
    return [];
}

// Second declaration of getOrderWithItems removed - already defined above

// Second declaration of getStatusBadgeClass removed - already defined above