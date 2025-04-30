<?php
// Helper functions for the S-Oil e-commerce site

// Sanitize user input
function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($data)));
}

// Format price in Philippine Peso
function formatPrice($price) {
    return '₱' . number_format($price, 2);
}

// Get all products
function getAllProducts() {
    global $conn;
    $products = array();
    
    $sql = "SELECT * FROM products WHERE in_stock = 1";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    
    return $products;
}

// Get product by ID
function getProductById($id) {
    global $conn;
    
    $id = sanitize($id);
    $sql = "SELECT * FROM products WHERE id = '$id'";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

// Get products by category
function getProductsByCategory($category) {
    global $conn;
    
    $category = sanitize($category);
    $sql = "SELECT * FROM products WHERE category = '$category' AND in_stock = 1";
    $result = $conn->query($sql);
    
    $products = array();
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    
    return $products;
}

// Get cart items for a user
function getCartItems($userId) {
    global $conn;
    
    $userId = sanitize($userId);
    $cart = array();
    
    $sql = "SELECT c.id as cart_id, ci.id, ci.product_id, ci.quantity, p.* 
            FROM carts c 
            JOIN cart_items ci ON c.id = ci.cart_id 
            JOIN products p ON ci.product_id = p.id 
            WHERE c.user_id = '$userId'";
    
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $cart[] = $row;
        }
    }
    
    return $cart;
}

// Add product to cart
function addToCart($userId, $productId, $quantity) {
    global $conn;
    
    $userId = sanitize($userId);
    $productId = sanitize($productId);
    $quantity = sanitize($quantity);
    
    // First, check if user has a cart
    $sql = "SELECT id FROM carts WHERE user_id = '$userId'";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $cart = $result->fetch_assoc();
        $cartId = $cart['id'];
    } else {
        // Create new cart
        $sql = "INSERT INTO carts (user_id) VALUES ('$userId')";
        $conn->query($sql);
        $cartId = $conn->insert_id;
    }
    
    // Check if product already in cart
    $sql = "SELECT id, quantity FROM cart_items WHERE cart_id = '$cartId' AND product_id = '$productId'";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        // Update quantity
        $item = $result->fetch_assoc();
        $newQuantity = $item['quantity'] + $quantity;
        
        $sql = "UPDATE cart_items SET quantity = '$newQuantity' WHERE id = '{$item['id']}'";
        return $conn->query($sql);
    } else {
        // Add new item
        $sql = "INSERT INTO cart_items (cart_id, product_id, quantity) VALUES ('$cartId', '$productId', '$quantity')";
        return $conn->query($sql);
    }
}

// Update cart item quantity
function updateCartItemQuantity($itemId, $quantity) {
    global $conn;
    
    $itemId = sanitize($itemId);
    $quantity = sanitize($quantity);
    
    $sql = "UPDATE cart_items SET quantity = '$quantity' WHERE id = '$itemId'";
    return $conn->query($sql);
}

// Remove item from cart
function removeCartItem($itemId) {
    global $conn;
    
    $itemId = sanitize($itemId);
    
    $sql = "DELETE FROM cart_items WHERE id = '$itemId'";
    return $conn->query($sql);
}

// Calculate cart totals
function calculateCartTotal($cartItems) {
    $subtotal = 0;
    
    foreach ($cartItems as $item) {
        $price = (!empty($item['sale_price'])) ? $item['sale_price'] : $item['price'];
        $subtotal += $price * $item['quantity'];
    }
    
    return $subtotal;
}

// Create a new order
function createOrder($userId, $total, $shippingAddress, $shippingCity, $shippingState, $shippingZip, $shippingMethod, $paymentMethod) {
    global $conn;
    
    $userId = sanitize($userId);
    $total = sanitize($total);
    $shippingAddress = sanitize($shippingAddress);
    $shippingCity = sanitize($shippingCity);
    $shippingState = sanitize($shippingState);
    $shippingZip = sanitize($shippingZip);
    $shippingMethod = sanitize($shippingMethod);
    $paymentMethod = sanitize($paymentMethod);
    
    $sql = "INSERT INTO orders (user_id, total, status, shipping_address, shipping_city, shipping_state, shipping_zip, shipping_method, payment_method, created_at) 
            VALUES ('$userId', '$total', 'pending', '$shippingAddress', '$shippingCity', '$shippingState', '$shippingZip', '$shippingMethod', '$paymentMethod', NOW())";
    
    if ($conn->query($sql)) {
        return $conn->insert_id;
    }
    
    return false;
}

// Add items to order
function addOrderItems($orderId, $cartItems) {
    global $conn;
    
    $orderId = sanitize($orderId);
    
    foreach ($cartItems as $item) {
        $productId = sanitize($item['product_id']);
        $quantity = sanitize($item['quantity']);
        $price = sanitize(!empty($item['sale_price']) ? $item['sale_price'] : $item['price']);
        
        $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                VALUES ('$orderId', '$productId', '$quantity', '$price')";
        
        $conn->query($sql);
    }
    
    return true;
}

// Get order details
function getOrderById($orderId) {
    global $conn;
    
    $orderId = sanitize($orderId);
    
    $sql = "SELECT * FROM orders WHERE id = '$orderId'";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

// Get order items
function getOrderItems($orderId) {
    global $conn;
    
    $orderId = sanitize($orderId);
    $items = array();
    
    $sql = "SELECT oi.*, p.* 
            FROM order_items oi 
            JOIN products p ON oi.product_id = p.id 
            WHERE oi.order_id = '$orderId'";
    
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
    }
    
    return $items;
}

// Get all orders (admin)
function getAllOrders() {
    global $conn;
    $orders = array();
    
    $sql = "SELECT * FROM orders ORDER BY created_at DESC";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
    }
    
    return $orders;
}

// Update order status
function updateOrderStatus($orderId, $status) {
    global $conn;
    
    $orderId = sanitize($orderId);
    $status = sanitize($status);
    
    $sql = "UPDATE orders SET status = '$status', updated_at = NOW() WHERE id = '$orderId'";
    return $conn->query($sql);
}

// Upload payment screenshot
function uploadPaymentScreenshot($orderId, $file) {
    // File upload directory
    $targetDir = "../uploads/payments/";
    
    // Create directory if it doesn't exist
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    // Get file extension
    $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    
    // Generate unique filename
    $fileName = "payment_" . $orderId . "_" . time() . "." . $imageFileType;
    $targetFile = $targetDir . $fileName;
    
    // Check if image file is an actual image
    $check = getimagesize($file["tmp_name"]);
    if($check === false) {
        return false;
    }
    
    // Check file size (limit to 5MB)
    if ($file["size"] > 5000000) {
        return false;
    }
    
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
        return false;
    }
    
    // Upload file
    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
        // Update order with payment screenshot
        global $conn;
        $orderId = sanitize($orderId);
        $fileName = sanitize($fileName);
        
        $sql = "UPDATE orders SET payment_screenshot = '$fileName' WHERE id = '$orderId'";
        $conn->query($sql);
        
        return true;
    } else {
        return false;
    }
}
?>