<?php
// Database Connection
function getDbConnection() {
    static $conn = null;
    
    if ($conn === null) {
        // Check if config file exists
        if (file_exists(__DIR__ . '/config.php')) {
            // Use values from config file
            require_once __DIR__ . '/config.php';
            $host = defined('DB_HOST') ? DB_HOST : 'localhost';
            $username = defined('DB_USERNAME') ? DB_USERNAME : 'root';
            $password = defined('DB_PASSWORD') ? DB_PASSWORD : '';
            $database = defined('DB_NAME') ? DB_NAME : 'soil_shop';
        } else {
            // Default values
            $host = "localhost";     // XAMPP MySQL server
            $username = "root";      // Default XAMPP username
            $password = "";          // Default XAMPP password is empty
            $database = "soil_shop"; // Database name
        }
        
        try {
            // Create connection
            $conn = new mysqli($host, $username, $password, $database);
            
            // Check connection
            if ($conn->connect_error) {
                throw new Exception("Database connection failed: " . $conn->connect_error);
            }
            
            // Set UTF-8 character set
            $conn->set_charset("utf8mb4");
        } catch (Exception $e) {
            // Log error
            error_log($e->getMessage());
            
            // Show user-friendly error
            echo "<div style='color:red;padding:20px;'>
                    <h3>Database Connection Error</h3>
                    <p>Could not connect to the database. Please check your configuration or run the <a href='install.php'>installation wizard</a>.</p>
                  </div>";
            exit;
        }
    }
    
    return $conn;
}

// Escape string to prevent SQL injection
function escapeString($string) {
    $conn = getDbConnection();
    return $conn->real_escape_string($string);
}

// Format price in PHP Peso
function formatPrice($price) {
    return '₱' . number_format($price, 2);
}

// Get all products
function getAllProducts() {
    $conn = getDbConnection();
    $sql = "SELECT * FROM products ORDER BY name";
    $result = $conn->query($sql);
    
    $products = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    
    return $products;
}

// Get products by category
function getProductsByCategory($category) {
    $conn = getDbConnection();
    $category = escapeString($category);
    
    $sql = "SELECT * FROM products WHERE category = '$category' ORDER BY name";
    $result = $conn->query($sql);
    
    $products = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    
    return $products;
}

// Get product by ID
function getProductById($id) {
    $conn = getDbConnection();
    $id = (int)$id;
    
    $sql = "SELECT * FROM products WHERE id = $id";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

// Get user cart
function getCart($userId) {
    $conn = getDbConnection();
    $userId = (int)$userId;
    
    // Check if cart exists
    $sql = "SELECT * FROM carts WHERE user_id = $userId";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    // Create new cart if none exists
    $sql = "INSERT INTO carts (user_id) VALUES ($userId)";
    if ($conn->query($sql)) {
        return ['id' => $conn->insert_id, 'user_id' => $userId];
    }
    
    return null;
}

// Get cart with products
function getCartWithProducts($userId) {
    $conn = getDbConnection();
    $userId = (int)$userId;
    
    // Get or create cart
    $cart = getCart($userId);
    
    if (!$cart) {
        return null;
    }
    
    // Get cart items with product details
    $cartId = $cart['id'];
    $sql = "SELECT ci.*, p.* FROM cart_items ci 
            INNER JOIN products p ON ci.product_id = p.id
            WHERE ci.cart_id = $cartId";
    
    $result = $conn->query($sql);
    $items = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $items[] = [
                'id' => $row['id'],
                'cartId' => $row['cart_id'],
                'productId' => $row['product_id'],
                'quantity' => $row['quantity'],
                'product' => [
                    'id' => $row['product_id'],
                    'name' => $row['name'],
                    'price' => $row['price'],
                    'sale_price' => $row['sale_price'],
                    'image_url' => $row['image_url'],
                    'category' => $row['category'],
                    'in_stock' => $row['in_stock'],
                    'rating' => $row['rating'],
                    'review_count' => $row['review_count'],
                    'is_prime' => $row['is_prime'],
                    'description' => $row['description']
                ]
            ];
        }
    }
    
    return [
        'id' => $cart['id'],
        'userId' => $userId,
        'items' => $items
    ];
}

// Add item to cart
function addToCart($userId, $productId, $quantity) {
    $conn = getDbConnection();
    $userId = (int)$userId;
    $productId = (int)$productId;
    $quantity = (int)$quantity;
    
    // Get or create cart
    $cart = getCart($userId);
    
    if (!$cart) {
        return null;
    }
    
    $cartId = $cart['id'];
    
    // Check if product already in cart
    $sql = "SELECT * FROM cart_items WHERE cart_id = $cartId AND product_id = $productId";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $cartItem = $result->fetch_assoc();
        $newQuantity = $cartItem['quantity'] + $quantity;
        
        // Update quantity
        $sql = "UPDATE cart_items SET quantity = $newQuantity WHERE id = {$cartItem['id']}";
        if ($conn->query($sql)) {
            return getCartItemWithProduct($cartItem['id']);
        }
    } else {
        // Add new item
        $sql = "INSERT INTO cart_items (cart_id, product_id, quantity) VALUES ($cartId, $productId, $quantity)";
        if ($conn->query($sql)) {
            $cartItemId = $conn->insert_id;
            return getCartItemWithProduct($cartItemId);
        }
    }
    
    return null;
}

// Update cart item quantity
function updateCartItemQuantity($cartItemId, $quantity) {
    $conn = getDbConnection();
    $cartItemId = (int)$cartItemId;
    $quantity = (int)$quantity;
    
    $sql = "UPDATE cart_items SET quantity = $quantity WHERE id = $cartItemId";
    
    if ($conn->query($sql)) {
        $sql = "SELECT * FROM cart_items WHERE id = $cartItemId";
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
    }
    
    return null;
}

// Remove cart item
function removeCartItem($cartItemId) {
    $conn = getDbConnection();
    $cartItemId = (int)$cartItemId;
    
    $sql = "DELETE FROM cart_items WHERE id = $cartItemId";
    
    return $conn->query($sql);
}

// Get cart item with product details
function getCartItemWithProduct($cartItemId) {
    $conn = getDbConnection();
    $cartItemId = (int)$cartItemId;
    
    $sql = "SELECT ci.*, p.* FROM cart_items ci 
            INNER JOIN products p ON ci.product_id = p.id
            WHERE ci.id = $cartItemId";
    
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        return [
            'id' => $row['id'],
            'cartId' => $row['cart_id'],
            'productId' => $row['product_id'],
            'quantity' => $row['quantity'],
            'product' => [
                'id' => $row['product_id'],
                'name' => $row['name'],
                'price' => $row['price'],
                'sale_price' => $row['sale_price'],
                'image_url' => $row['image_url'],
                'category' => $row['category'],
                'in_stock' => $row['in_stock'],
                'rating' => $row['rating'],
                'review_count' => $row['review_count'],
                'is_prime' => $row['is_prime'],
                'description' => $row['description']
            ]
        ];
    }
    
    return null;
}

// Create order
function createOrder($userId, $shippingInfo, $paymentInfo, $cartItems) {
    $conn = getDbConnection();
    $userId = (int)$userId;
    
    // Calculate total
    $total = 0;
    foreach ($cartItems as $item) {
        $price = !empty($item['product']['sale_price']) ? $item['product']['sale_price'] : $item['product']['price'];
        $total += $price * $item['quantity'];
    }
    
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // Create order
        $sql = "INSERT INTO orders (
                user_id, 
                total, 
                shipping_address, 
                shipping_city, 
                shipping_state, 
                shipping_zip, 
                shipping_method, 
                payment_method, 
                payment_screenshot
            ) VALUES (
                $userId,
                $total,
                '{$shippingInfo['address']}',
                '{$shippingInfo['city']}',
                '{$shippingInfo['state']}',
                '{$shippingInfo['zip']}',
                '{$shippingInfo['shipping_method']}',
                '{$paymentInfo['payment_method']}',
                " . (!empty($paymentInfo['payment_screenshot']) ? "'{$paymentInfo['payment_screenshot']}'" : "NULL") . "
            )";
        
        if (!$conn->query($sql)) {
            throw new Exception("Failed to create order: " . $conn->error);
        }
        
        $orderId = $conn->insert_id;
        
        // Create order items
        foreach ($cartItems as $item) {
            $productId = $item['productId'];
            $quantity = $item['quantity'];
            $price = !empty($item['product']['sale_price']) ? $item['product']['sale_price'] : $item['product']['price'];
            
            $sql = "INSERT INTO order_items (order_id, product_id, quantity, price)
                   VALUES ($orderId, $productId, $quantity, $price)";
            
            if (!$conn->query($sql)) {
                throw new Exception("Failed to create order item: " . $conn->error);
            }
        }
        
        // Clear cart
        $sql = "DELETE FROM cart_items WHERE cart_id = {$cartItems[0]['cartId']}";
        if (!$conn->query($sql)) {
            throw new Exception("Failed to clear cart: " . $conn->error);
        }
        
        // Commit transaction
        $conn->commit();
        
        // Get full order
        return getOrderById($orderId);
        
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        return null;
    }
}

// Get order by ID
function getOrderById($orderId) {
    $conn = getDbConnection();
    $orderId = (int)$orderId;
    
    $sql = "SELECT * FROM orders WHERE id = $orderId";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

// Get order with items
function getOrderWithItems($orderId) {
    $conn = getDbConnection();
    $orderId = (int)$orderId;
    
    // Get order
    $order = getOrderById($orderId);
    
    if (!$order) {
        return null;
    }
    
    // Get order items with product details
    $sql = "SELECT oi.*, p.* FROM order_items oi 
            INNER JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = $orderId";
    
    $result = $conn->query($sql);
    $items = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $items[] = [
                'id' => $row['id'],
                'orderId' => $row['order_id'],
                'productId' => $row['product_id'],
                'quantity' => $row['quantity'],
                'price' => $row['price'],
                'product' => [
                    'id' => $row['product_id'],
                    'name' => $row['name'],
                    'image_url' => $row['image_url'],
                    'category' => $row['category'],
                    'description' => $row['description']
                ]
            ];
        }
    }
    
    return [
        'id' => $order['id'],
        'userId' => $order['user_id'],
        'total' => $order['total'],
        'status' => $order['status'],
        'shipping_address' => $order['shipping_address'],
        'shipping_city' => $order['shipping_city'],
        'shipping_state' => $order['shipping_state'],
        'shipping_zip' => $order['shipping_zip'],
        'shipping_method' => $order['shipping_method'],
        'payment_method' => $order['payment_method'],
        'payment_screenshot' => $order['payment_screenshot'],
        'created_at' => $order['created_at'],
        'updated_at' => $order['updated_at'],
        'items' => $items
    ];
}

// Get user orders
function getUserOrders($userId) {
    $conn = getDbConnection();
    $userId = (int)$userId;
    
    $sql = "SELECT * FROM orders WHERE user_id = $userId ORDER BY created_at DESC";
    $result = $conn->query($sql);
    
    $orders = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
    }
    
    return $orders;
}

// Get all orders (for admin)
function getAllOrders() {
    $conn = getDbConnection();
    
    $sql = "SELECT o.*, COUNT(oi.id) as item_count 
            FROM orders o
            LEFT JOIN order_items oi ON o.id = oi.order_id
            GROUP BY o.id
            ORDER BY o.created_at DESC";
            
    $result = $conn->query($sql);
    
    $orders = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
    }
    
    return $orders;
}

// Update order status
function updateOrderStatus($orderId, $status) {
    $conn = getDbConnection();
    $orderId = (int)$orderId;
    $status = escapeString($status);
    
    $sql = "UPDATE orders SET status = '$status' WHERE id = $orderId";
    
    if ($conn->query($sql)) {
        return getOrderById($orderId);
    }
    
    return null;
}