<?php
// Include helper functions if not already included
if (!function_exists('formatPrice')) {
    require_once 'includes/functions.php';
}

// Get cart items from session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cartItems = $_SESSION['cart'];

// Calculate cart totals
$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

$total = $subtotal; // No shipping cost as per requirements

// Initialize shipping and payment method
$shippingMethod = isset($_SESSION['checkout_shipping_method']) ? $_SESSION['checkout_shipping_method'] : '';
$paymentMethod = isset($_SESSION['checkout_payment_method']) ? $_SESSION['checkout_payment_method'] : '';

// Initialize checkout step
$checkoutStep = isset($_SESSION['checkout_step']) ? $_SESSION['checkout_step'] : 'shipping';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Shipping form submitted
    if (isset($_POST['shipping_step'])) {
        $shippingMethod = isset($_POST['shipping_method']) ? $_POST['shipping_method'] : '';
        
        if (!empty($shippingMethod)) {
            // Save form data to session
            $_SESSION['checkout_shipping_method'] = $shippingMethod;
            $_SESSION['checkout_step'] = 'payment';
            $checkoutStep = 'payment';
            
            // Save shipping address if delivery is selected
            if ($shippingMethod === 'delivery') {
                $_SESSION['checkout_shipping_address'] = [
                    'name' => isset($_POST['name']) ? $_POST['name'] : '',
                    'email' => isset($_POST['email']) ? $_POST['email'] : '',
                    'phone' => isset($_POST['phone']) ? $_POST['phone'] : '',
                    'address' => isset($_POST['address']) ? $_POST['address'] : '',
                    'city' => isset($_POST['city']) ? $_POST['city'] : '',
                    'state' => isset($_POST['state']) ? $_POST['state'] : '',
                    'zip' => isset($_POST['zip']) ? $_POST['zip'] : ''
                ];
            } else {
                // For pickup, just save name, email, phone
                $_SESSION['checkout_shipping_address'] = [
                    'name' => isset($_POST['name']) ? $_POST['name'] : '',
                    'email' => isset($_POST['email']) ? $_POST['email'] : '',
                    'phone' => isset($_POST['phone']) ? $_POST['phone'] : ''
                ];
            }
        } else {
            $shippingError = 'Please select a shipping method.';
        }
    }
    
    // Payment form submitted
    if (isset($_POST['payment_step'])) {
        $paymentMethod = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';
        
        if (!empty($paymentMethod)) {
            // Save payment method to session
            $_SESSION['checkout_payment_method'] = $paymentMethod;
            
            // Process payment screenshot upload if GCash is selected
            if ($paymentMethod === 'gcash' && isset($_FILES['payment_screenshot']) && $_FILES['payment_screenshot']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/payments/';
                
                // Create directory if it doesn't exist
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $fileName = time() . '_' . basename($_FILES['payment_screenshot']['name']);
                $uploadPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['payment_screenshot']['tmp_name'], $uploadPath)) {
                    $_SESSION['checkout_payment_screenshot'] = $uploadPath;
                } else {
                    $paymentError = 'Failed to upload payment screenshot. Please try again.';
                    $checkoutStep = 'payment';
                }
            }
            
            // If payment is valid, proceed to review step
            if (!isset($paymentError)) {
                $_SESSION['checkout_step'] = 'review';
                $checkoutStep = 'review';
            }
        } else {
            $paymentError = 'Please select a payment method.';
        }
    }
    
    // Order placement form submitted
    if (isset($_POST['place_order'])) {
        if (empty($cartItems)) {
            $orderError = 'Your cart is empty. Please add some products to your cart before placing an order.';
        } else {
            // Create order in database
            $order = createOrder([
                'user_id' => $_SESSION['user_id'] ?? 1,
                'total' => $total,
                'status' => 'pending',
                'payment_method' => $_SESSION['checkout_payment_method'],
                'payment_screenshot' => $_SESSION['checkout_payment_screenshot'] ?? '',
                'shipping_method' => $_SESSION['checkout_shipping_method'],
                'shipping_address' => $_SESSION['checkout_shipping_address']['address'] ?? '',
                'shipping_city' => $_SESSION['checkout_shipping_address']['city'] ?? '',
                'shipping_state' => $_SESSION['checkout_shipping_address']['state'] ?? '',
                'shipping_zip' => $_SESSION['checkout_shipping_address']['zip'] ?? '',
                'customer_name' => $_SESSION['checkout_shipping_address']['name'],
                'customer_email' => $_SESSION['checkout_shipping_address']['email'],
                'customer_phone' => $_SESSION['checkout_shipping_address']['phone'],
                'notes' => $_POST['notes'] ?? ''
            ]);
            
            if ($order) {
                // Store order ID in session
                $_SESSION['last_order_id'] = $order['id'];
                
                // Clear cart and checkout session data
                $_SESSION['cart'] = [];
                $_SESSION['cart_count'] = 0;
                unset($_SESSION['checkout_step']);
                unset($_SESSION['checkout_shipping_method']);
                unset($_SESSION['checkout_shipping_address']);
                unset($_SESSION['checkout_payment_method']);
                unset($_SESSION['checkout_payment_screenshot']);
                
                // Redirect to order confirmation page
                header('Location: index.php?page=order-confirmation');
                exit;
            } else {
                $orderError = 'Failed to create order. Please try again.';
            }
        }
    }
}

// Helper function to check if checkout step is active
function isStepActive($step, $currentStep) {
    return $step === $currentStep;
}

// Helper function to check if checkout step is completed
function isStepCompleted($step, $currentStep) {
    $steps = ['shipping', 'payment', 'review'];
    $currentIndex = array_search($currentStep, $steps);
    $stepIndex = array_search($step, $steps);
    
    return $stepIndex < $currentIndex;
}

// Helper function to create order
function createOrder($orderData) {
    global $conn;
    
    // Sample order creation for development
    if (DEVELOPMENT_MODE && !isset($conn)) {
        $orderId = time();
        $order = [
            'id' => $orderId,
            'user_id' => $orderData['user_id'],
            'total' => $orderData['total'],
            'status' => $orderData['status'],
            'payment_method' => $orderData['payment_method'],
            'payment_screenshot' => $orderData['payment_screenshot'],
            'shipping_method' => $orderData['shipping_method'],
            'shipping_address' => $orderData['shipping_address'],
            'shipping_city' => $orderData['shipping_city'],
            'shipping_state' => $orderData['shipping_state'],
            'shipping_zip' => $orderData['shipping_zip'],
            'customer_name' => $orderData['customer_name'],
            'customer_email' => $orderData['customer_email'],
            'customer_phone' => $orderData['customer_phone'],
            'notes' => $orderData['notes'],
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $order;
    }
    
    try {
        // Begin transaction
        $conn->begin_transaction();
        
        // Insert order
        $sql = "INSERT INTO orders (
                    user_id, total, status, payment_method, payment_screenshot, 
                    shipping_method, shipping_address, shipping_city, shipping_state, 
                    shipping_zip, customer_name, customer_email, customer_phone, notes, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "idssssssssssss",
            $orderData['user_id'],
            $orderData['total'],
            $orderData['status'],
            $orderData['payment_method'],
            $orderData['payment_screenshot'],
            $orderData['shipping_method'],
            $orderData['shipping_address'],
            $orderData['shipping_city'],
            $orderData['shipping_state'],
            $orderData['shipping_zip'],
            $orderData['customer_name'],
            $orderData['customer_email'],
            $orderData['customer_phone'],
            $orderData['notes']
        );
        
        $stmt->execute();
        $orderId = $conn->insert_id;
        
        // Insert order items from cart
        $cartItems = $_SESSION['cart'];
        foreach ($cartItems as $item) {
            $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iiid", $orderId, $item['product_id'], $item['quantity'], $item['price']);
            $stmt->execute();
        }
        
        // Commit transaction
        $conn->commit();
        
        // Return created order
        return [
            'id' => $orderId,
            'user_id' => $orderData['user_id'],
            'total' => $orderData['total'],
            'status' => $orderData['status'],
            'payment_method' => $orderData['payment_method'],
            'payment_screenshot' => $orderData['payment_screenshot'],
            'shipping_method' => $orderData['shipping_method'],
            'shipping_address' => $orderData['shipping_address'],
            'shipping_city' => $orderData['shipping_city'],
            'shipping_state' => $orderData['shipping_state'],
            'shipping_zip' => $orderData['shipping_zip'],
            'customer_name' => $orderData['customer_name'],
            'customer_email' => $orderData['customer_email'],
            'customer_phone' => $orderData['customer_phone'],
            'notes' => $orderData['notes'],
            'created_at' => date('Y-m-d H:i:s')
        ];
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        
        // Log error
        error_log("Order creation error: " . $e->getMessage());
        
        // Return false on error
        return false;
    }
}
?>

<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="mb-4">Checkout</h1>
        </div>
    </div>
    
    <?php if (empty($cartItems)): ?>
        <div class="text-center py-5">
            <i class="fas fa-shopping-cart fa-5x text-muted mb-4"></i>
            <h3>Your cart is empty</h3>
            <p class="text-muted mb-4">Add some products to your cart and they will appear here</p>
            <a href="index.php?page=products" class="btn btn-primary px-4">
                <i class="fas fa-shopping-bag me-2"></i> Continue Shopping
            </a>
        </div>
    <?php else: ?>
        <!-- Checkout Progress -->
        <div class="d-flex justify-content-center mb-5 checkout-progress">
            <div class="step <?php echo isStepActive('shipping', $checkoutStep) ? 'active' : ''; ?> <?php echo isStepCompleted('shipping', $checkoutStep) ? 'completed' : ''; ?>">
                <div class="step-icon">
                    <i class="fas fa-truck"></i>
                </div>
                <div class="step-label">Shipping</div>
            </div>
            <div class="step <?php echo isStepActive('payment', $checkoutStep) ? 'active' : ''; ?> <?php echo isStepCompleted('payment', $checkoutStep) ? 'completed' : ''; ?>">
                <div class="step-icon">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div class="step-label">Payment</div>
            </div>
            <div class="step <?php echo isStepActive('review', $checkoutStep) ? 'active' : ''; ?>">
                <div class="step-icon">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <div class="step-label">Review</div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Shipping Step -->
                <?php if ($checkoutStep === 'shipping'): ?>
                    <div class="card border-0 rounded-4 shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Shipping Information</h5>
                        </div>
                        <div class="card-body">
                            <?php if (isset($shippingError)): ?>
                                <div class="alert alert-danger" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i> <?php echo $shippingError; ?>
                                </div>
                            <?php endif; ?>
                            
                            <form method="post" id="shipping-form">
                                <input type="hidden" name="shipping_step" value="1">
                                
                                <div class="mb-4">
                                    <h6>Personal Information</h6>
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="name" name="name" placeholder="Full Name" required value="<?php echo $_SESSION['checkout_shipping_address']['name'] ?? ''; ?>">
                                                <label for="name">Full Name</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="email" class="form-control" id="email" name="email" placeholder="Email" required value="<?php echo $_SESSION['checkout_shipping_address']['email'] ?? ''; ?>">
                                                <label for="email">Email</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="Phone" required value="<?php echo $_SESSION['checkout_shipping_address']['phone'] ?? ''; ?>">
                                                <label for="phone">Phone</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <h6>Shipping Method</h6>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" name="shipping_method" id="pickup" value="pickup" <?php echo ($shippingMethod === 'pickup') ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="pickup">
                                            <strong>Personal Pick-up</strong> - Pick up your order at our store
                                        </label>
                                        <div class="ms-4 mt-2 text-muted small">
                                            <p class="mb-0">Address: 123 Mendiola St. Manila City</p>
                                            <p class="mb-0">Contact Person: Anjhela Geron 09454545</p>
                                        </div>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="shipping_method" id="delivery" value="delivery" <?php echo ($shippingMethod === 'delivery') ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="delivery">
                                            <strong>Grab/Lalamove Delivery</strong> - Arrange your own delivery
                                        </label>
                                    </div>
                                </div>
                                
                                <div id="delivery-address" class="mb-4 <?php echo ($shippingMethod === 'delivery') ? '' : 'd-none'; ?>">
                                    <h6>Delivery Address</h6>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="address" name="address" placeholder="Address" value="<?php echo $_SESSION['checkout_shipping_address']['address'] ?? ''; ?>">
                                                <label for="address">Address</label>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="city" name="city" placeholder="City" value="<?php echo $_SESSION['checkout_shipping_address']['city'] ?? ''; ?>">
                                                <label for="city">City</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="state" name="state" placeholder="State/Province" value="<?php echo $_SESSION['checkout_shipping_address']['state'] ?? ''; ?>">
                                                <label for="state">State/Province</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="zip" name="zip" placeholder="Zip Code" value="<?php echo $_SESSION['checkout_shipping_address']['zip'] ?? ''; ?>">
                                                <label for="zip">Zip Code</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-grid gap-2 mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        Continue to Payment <i class="fas fa-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                <?php elseif ($checkoutStep === 'payment'): ?>
                    <!-- Payment Step -->
                    <div class="card border-0 rounded-4 shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Payment Method</h5>
                        </div>
                        <div class="card-body">
                            <?php if (isset($paymentError)): ?>
                                <div class="alert alert-danger" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i> <?php echo $paymentError; ?>
                                </div>
                            <?php endif; ?>
                            
                            <form method="post" id="payment-form" enctype="multipart/form-data">
                                <input type="hidden" name="payment_step" value="1">
                                
                                <div class="mb-4">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" name="payment_method" id="gcash" value="gcash" <?php echo ($paymentMethod === 'gcash') ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="gcash">
                                            <strong>GCash</strong> - Pay using GCash mobile wallet
                                        </label>
                                        <div class="ms-4 mt-2 text-muted small">
                                            <p class="mb-0">Account Name: Martin Magno</p>
                                            <p class="mb-0">GCash Number: 091234567</p>
                                            <p class="mb-0">Please upload a screenshot of your payment</p>
                                        </div>
                                    </div>
                                    <div id="gcash-screenshot" class="ms-4 mb-3 <?php echo ($paymentMethod === 'gcash') ? '' : 'd-none'; ?>">
                                        <div class="mb-3">
                                            <label for="payment_screenshot" class="form-label">Payment Screenshot</label>
                                            <input class="form-control" type="file" id="payment_screenshot" name="payment_screenshot" accept="image/*">
                                        </div>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="otc" value="otc" <?php echo ($paymentMethod === 'otc') ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="otc">
                                            <strong>Over-the-counter Payment</strong> - Pay when you pick up your order
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between mt-4">
                                    <a href="index.php?page=checkout" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-2"></i> Back to Shipping
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        Continue to Review <i class="fas fa-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                <?php elseif ($checkoutStep === 'review'): ?>
                    <!-- Review Step -->
                    <div class="card border-0 rounded-4 shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Order Review</h5>
                        </div>
                        <div class="card-body">
                            <?php if (isset($orderError)): ?>
                                <div class="alert alert-danger" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i> <?php echo $orderError; ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="mb-4">
                                <h6>Personal Information</h6>
                                <p class="mb-0"><strong>Name:</strong> <?php echo htmlspecialchars($_SESSION['checkout_shipping_address']['name']); ?></p>
                                <p class="mb-0"><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['checkout_shipping_address']['email']); ?></p>
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($_SESSION['checkout_shipping_address']['phone']); ?></p>
                            </div>
                            
                            <div class="mb-4">
                                <h6>Shipping Method</h6>
                                <p>
                                    <?php if ($_SESSION['checkout_shipping_method'] === 'pickup'): ?>
                                        <strong>Personal Pick-up</strong>
                                        <div class="text-muted small">
                                            <p class="mb-0">Address: 123 Mendiola St. Manila City</p>
                                            <p class="mb-0">Contact Person: Anjhela Geron 09454545</p>
                                        </div>
                                    <?php else: ?>
                                        <strong>Grab/Lalamove Delivery</strong>
                                        <div class="text-muted small">
                                            <p class="mb-0"><?php echo htmlspecialchars($_SESSION['checkout_shipping_address']['address']); ?></p>
                                            <p class="mb-0">
                                                <?php echo htmlspecialchars($_SESSION['checkout_shipping_address']['city']); ?>, 
                                                <?php echo htmlspecialchars($_SESSION['checkout_shipping_address']['state']); ?> 
                                                <?php echo htmlspecialchars($_SESSION['checkout_shipping_address']['zip']); ?>
                                            </p>
                                        </div>
                                    <?php endif; ?>
                                </p>
                            </div>
                            
                            <div class="mb-4">
                                <h6>Payment Method</h6>
                                <p>
                                    <?php if ($_SESSION['checkout_payment_method'] === 'gcash'): ?>
                                        <strong>GCash</strong>
                                        <?php if (isset($_SESSION['checkout_payment_screenshot']) && !empty($_SESSION['checkout_payment_screenshot'])): ?>
                                            <div class="mt-2">
                                                <img src="<?php echo htmlspecialchars($_SESSION['checkout_payment_screenshot']); ?>" alt="Payment Screenshot" class="img-thumbnail" style="max-width: 200px;">
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <strong>Over-the-counter Payment</strong>
                                    <?php endif; ?>
                                </p>
                            </div>
                            
                            <div class="mb-4">
                                <h6>Order Items</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Product</th>
                                                <th class="text-center">Price</th>
                                                <th class="text-center">Quantity</th>
                                                <th class="text-end">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($cartItems as $item): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="me-3" style="width: 50px; height: 50px; object-fit: contain;">
                                                            <div>
                                                                <h6 class="mb-0"><?php echo htmlspecialchars($item['name']); ?></h6>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center"><?php echo formatPrice($item['price']); ?></td>
                                                    <td class="text-center"><?php echo $item['quantity']; ?></td>
                                                    <td class="text-end"><?php echo formatPrice($item['price'] * $item['quantity']); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <form method="post" id="place-order-form">
                                <div class="mb-4">
                                    <h6>Order Notes (Optional)</h6>
                                    <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Add any special instructions or notes for your order"></textarea>
                                </div>
                                
                                <div class="d-flex justify-content-between mt-4">
                                    <a href="index.php?page=checkout" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-2"></i> Back to Payment
                                    </a>
                                    <button type="submit" name="place_order" class="btn btn-primary">
                                        <i class="fas fa-shopping-cart me-2"></i> Place Order
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="card border-0 rounded-4 shadow-sm mb-4 sticky-top" style="top: 20px; z-index: 100;">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h6>Items (<?php echo count($cartItems); ?>)</h6>
                            <div class="summary-items">
                                <?php foreach ($cartItems as $item): ?>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span><?php echo htmlspecialchars($item['name']); ?> × <?php echo $item['quantity']; ?></span>
                                        <span><?php echo formatPrice($item['price'] * $item['quantity']); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span><?php echo formatPrice($subtotal); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping</span>
                            <span>Free</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-0">
                            <strong>Total</strong>
                            <strong class="text-primary h5"><?php echo formatPrice($total); ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.checkout-progress {
    position: relative;
    max-width: 600px;
    margin: 0 auto;
}

.checkout-progress::before {
    content: '';
    position: absolute;
    top: 24px;
    left: 15%;
    right: 15%;
    height: 2px;
    background-color: #e9ecef;
    z-index: 0;
}

.checkout-progress .step {
    flex: 1;
    text-align: center;
    position: relative;
    z-index: 1;
}

.checkout-progress .step-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background-color: #fff;
    border: 2px solid #e9ecef;
    color: #adb5bd;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
    font-size: 1.2rem;
}

.checkout-progress .step.active .step-icon {
    border-color: #007bff;
    color: #007bff;
}

.checkout-progress .step.completed .step-icon {
    background-color: #28a745;
    border-color: #28a745;
    color: #fff;
}

.checkout-progress .step-label {
    font-size: 0.9rem;
    color: #6c757d;
}

.checkout-progress .step.active .step-label {
    color: #007bff;
    font-weight: 600;
}

.checkout-progress .step.completed .step-label {
    color: #28a745;
    font-weight: 600;
}

.summary-items {
    max-height: 200px;
    overflow-y: auto;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Shipping method toggle for delivery address
    const pickupRadio = document.getElementById('pickup');
    const deliveryRadio = document.getElementById('delivery');
    const deliveryAddressSection = document.getElementById('delivery-address');
    
    if (pickupRadio && deliveryRadio && deliveryAddressSection) {
        const toggleAddressSection = function() {
            if (deliveryRadio.checked) {
                deliveryAddressSection.classList.remove('d-none');
                
                // Make address fields required
                const addressFields = deliveryAddressSection.querySelectorAll('input');
                addressFields.forEach(field => field.setAttribute('required', ''));
            } else {
                deliveryAddressSection.classList.add('d-none');
                
                // Remove required attribute from address fields
                const addressFields = deliveryAddressSection.querySelectorAll('input');
                addressFields.forEach(field => field.removeAttribute('required'));
            }
        };
        
        pickupRadio.addEventListener('change', toggleAddressSection);
        deliveryRadio.addEventListener('change', toggleAddressSection);
    }
    
    // Payment method toggle for GCash screenshot
    const gcashRadio = document.getElementById('gcash');
    const otcRadio = document.getElementById('otc');
    const gcashScreenshotSection = document.getElementById('gcash-screenshot');
    
    if (gcashRadio && otcRadio && gcashScreenshotSection) {
        const toggleScreenshotSection = function() {
            if (gcashRadio.checked) {
                gcashScreenshotSection.classList.remove('d-none');
                
                // Make screenshot field required
                const screenshotField = document.getElementById('payment_screenshot');
                screenshotField.setAttribute('required', '');
            } else {
                gcashScreenshotSection.classList.add('d-none');
                
                // Remove required attribute from screenshot field
                const screenshotField = document.getElementById('payment_screenshot');
                screenshotField.removeAttribute('required');
            }
        };
        
        gcashRadio.addEventListener('change', toggleScreenshotSection);
        otcRadio.addEventListener('change', toggleScreenshotSection);
    }
});
</script>