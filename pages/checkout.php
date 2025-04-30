<?php
// Get user cart
$userId = 1; // Default user ID for now (until we implement authentication)
$cart = getCartWithProducts($userId);

// If cart is empty or doesn't exist, redirect to cart page
if (!$cart || empty($cart['items'])) {
    echo '<script>window.location.href = "index.php?page=cart";</script>';
    exit;
}

// Initialize variables
$cartItems = $cart['items'];
$subtotal = 0;

// Calculate subtotal
foreach ($cartItems as $item) {
    $price = !empty($item['product']['sale_price']) ? $item['product']['sale_price'] : $item['product']['price'];
    $subtotal += $price * $item['quantity'];
}

// Determine current step
$currentStep = isset($_GET['step']) ? $_GET['step'] : 'shipping';
?>

<div class="container">
    <h1 class="mb-4">Checkout</h1>
    
    <!-- Checkout Progress -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between checkout-progress">
                <div class="checkout-step <?php echo in_array($currentStep, ['shipping', 'payment', 'review']) ? 'active' : ''; ?>">
                    <div class="step-number">1</div>
                    <div class="step-label">Shipping</div>
                </div>
                <div class="checkout-step <?php echo in_array($currentStep, ['payment', 'review']) ? 'active' : ''; ?>">
                    <div class="step-number">2</div>
                    <div class="step-label">Payment</div>
                </div>
                <div class="checkout-step <?php echo $currentStep == 'review' ? 'active' : ''; ?>">
                    <div class="step-number">3</div>
                    <div class="step-label">Review & Place Order</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body">
                    <?php if ($currentStep == 'shipping'): ?>
                        <!-- Shipping Step -->
                        <h3 class="mb-4">Shipping Information</h3>
                        
                        <form action="actions/process_shipping.php" method="post">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="full_name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" required>
                                </div>
                                
                                <div class="col-12">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                
                                <div class="col-12">
                                    <label for="address" class="form-label">Address</label>
                                    <input type="text" class="form-control" id="address" name="address" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" class="form-control" id="city" name="city" required>
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="state" class="form-label">State/Province</label>
                                    <input type="text" class="form-control" id="state" name="state" required>
                                </div>
                                
                                <div class="col-md-2">
                                    <label for="zip" class="form-label">Zip Code</label>
                                    <input type="text" class="form-control" id="zip" name="zip" required>
                                </div>
                                
                                <div class="col-12 mt-4">
                                    <h5>Shipping Options</h5>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" name="shipping_method" id="pickup" value="pickup" checked>
                                        <label class="form-check-label" for="pickup">
                                            <strong>Personal Pick-up</strong>
                                            <p class="mb-0 text-muted">Pick up your order at 123 Mendiola St. Manila City</p>
                                            <p class="mb-0 text-muted">Contact Person: Anjhela Geron 09454545</p>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="shipping_method" id="delivery" value="delivery">
                                        <label class="form-check-label" for="delivery">
                                            <strong>Grab/Lalamove Delivery</strong>
                                            <p class="mb-0 text-muted">Arranged by client</p>
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary">Continue to Payment</button>
                                </div>
                            </div>
                        </form>
                        
                    <?php elseif ($currentStep == 'payment'): ?>
                        <!-- Payment Step -->
                        <h3 class="mb-4">Payment Method</h3>
                        
                        <form action="actions/process_payment.php" method="post" enctype="multipart/form-data" id="checkout-form">
                            <div class="row g-3">
                                <!-- Payment Options -->
                                <div class="col-12">
                                    <input type="hidden" name="payment_method" id="payment_method" value="">
                                    
                                    <div class="payment-option" data-method="gcash">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_method_radio" id="gcash" value="gcash">
                                            <label class="form-check-label" for="gcash">
                                                <strong>GCash</strong>
                                                <p class="mb-0 text-muted">Send payment to Martin Magno 091234567</p>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="payment-option" data-method="counter">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_method_radio" id="counter" value="counter">
                                            <label class="form-check-label" for="counter">
                                                <strong>Over-the-counter Payment</strong>
                                                <p class="mb-0 text-muted">Pay when you pick up your order</p>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Payment Screenshot Upload (Only for GCash) -->
                                <div class="col-12 mt-3" id="screenshot-upload-section" style="display: none;">
                                    <label for="payment_screenshot" class="form-label">Upload Payment Screenshot</label>
                                    <div>
                                        <label class="custom-file-upload">
                                            <input type="file" name="payment_screenshot" id="payment_screenshot" accept="image/*" style="display:none;">
                                            Choose File
                                        </label>
                                        <span id="file-name-display" class="ms-2"></span>
                                    </div>
                                    <small class="text-muted">Please upload a screenshot of your GCash payment</small>
                                </div>
                                
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary">Continue to Review</button>
                                </div>
                            </div>
                        </form>
                        
                    <?php elseif ($currentStep == 'review'): ?>
                        <!-- Review Step -->
                        <h3 class="mb-4">Order Review</h3>
                        
                        <?php
                        // Get shipping details from session
                        $shipping = isset($_SESSION['checkout_shipping']) ? $_SESSION['checkout_shipping'] : null;
                        $payment = isset($_SESSION['checkout_payment']) ? $_SESSION['checkout_payment'] : null;
                        
                        if (!$shipping || !$payment) {
                            echo '<div class="alert alert-danger">Shipping or payment information missing. Please go back to previous steps.</div>';
                        } else {
                        ?>
                            <!-- Shipping Information -->
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Shipping Information</h5>
                                    <a href="index.php?page=checkout&step=shipping" class="btn btn-sm btn-outline-primary">Edit</a>
                                </div>
                                <div class="card-body">
                                    <p><strong><?php echo htmlspecialchars($shipping['full_name']); ?></strong></p>
                                    <p><?php echo htmlspecialchars($shipping['address']); ?></p>
                                    <p><?php echo htmlspecialchars($shipping['city']) . ', ' . htmlspecialchars($shipping['state']) . ' ' . htmlspecialchars($shipping['zip']); ?></p>
                                    <p>Phone: <?php echo htmlspecialchars($shipping['phone']); ?></p>
                                    <p>Email: <?php echo htmlspecialchars($shipping['email']); ?></p>
                                    <p>Shipping Method: <?php echo $shipping['shipping_method'] == 'pickup' ? 'Personal Pick-up' : 'Grab/Lalamove Delivery'; ?></p>
                                </div>
                            </div>
                            
                            <!-- Payment Information -->
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Payment Information</h5>
                                    <a href="index.php?page=checkout&step=payment" class="btn btn-sm btn-outline-primary">Edit</a>
                                </div>
                                <div class="card-body">
                                    <p>Payment Method: <?php echo $payment['payment_method'] == 'gcash' ? 'GCash' : 'Over-the-counter Payment'; ?></p>
                                    
                                    <?php if ($payment['payment_method'] == 'gcash' && !empty($payment['payment_screenshot'])): ?>
                                        <p>Payment Screenshot: <span class="text-success">Uploaded</span></p>
                                        <img src="<?php echo htmlspecialchars($payment['payment_screenshot']); ?>" alt="Payment Screenshot" class="img-thumbnail" style="max-width: 200px;">
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Order Items -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Order Items</h5>
                                </div>
                                <div class="card-body">
                                    <?php foreach ($cartItems as $item): ?>
                                        <div class="d-flex mb-3 pb-3 border-bottom">
                                            <img src="<?php echo htmlspecialchars($item['product']['image_url']); ?>" alt="<?php echo htmlspecialchars($item['product']['name']); ?>" class="me-3" style="width: 60px; height: 60px; object-fit: contain;">
                                            
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0"><?php echo htmlspecialchars($item['product']['name']); ?></h6>
                                                <div class="text-muted">Quantity: <?php echo $item['quantity']; ?></div>
                                            </div>
                                            
                                            <div class="text-end">
                                                <?php 
                                                $price = !empty($item['product']['sale_price']) ? $item['product']['sale_price'] : $item['product']['price'];
                                                $totalPrice = $price * $item['quantity'];
                                                echo formatPrice($totalPrice); 
                                                ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <form action="actions/place_order.php" method="post">
                                <button type="submit" class="btn btn-warning btn-lg w-100">Place Order</button>
                            </form>
                        <?php } ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Order Summary -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal (<?php echo count($cartItems); ?> items)</span>
                        <span><?php echo formatPrice($subtotal); ?></span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-4">
                        <span class="fw-bold">Total</span>
                        <span class="fw-bold"><?php echo formatPrice($subtotal); ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Order Items Preview -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Items in Your Order</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="d-flex mb-3 pb-3 border-bottom">
                            <img src="<?php echo htmlspecialchars($item['product']['image_url']); ?>" alt="<?php echo htmlspecialchars($item['product']['name']); ?>" class="me-3" style="width: 50px; height: 50px; object-fit: contain;">
                            
                            <div>
                                <h6 class="mb-0"><?php echo htmlspecialchars($item['product']['name']); ?></h6>
                                <div class="text-muted">Qty: <?php echo $item['quantity']; ?></div>
                                <div><?php echo formatPrice(!empty($item['product']['sale_price']) ? $item['product']['sale_price'] : $item['product']['price']); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.checkout-progress {
    position: relative;
    margin-bottom: 30px;
}

.checkout-progress::after {
    content: '';
    position: absolute;
    top: 20px;
    left: 0;
    right: 0;
    height: 2px;
    background-color: #e9ecef;
    z-index: 0;
}

.checkout-step {
    position: relative;
    z-index: 1;
    text-align: center;
    width: 33.333%;
}

.step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #e9ecef;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 8px;
    font-weight: bold;
}

.checkout-step.active .step-number {
    background-color: #007bff;
    color: white;
}

.checkout-step.active .step-label {
    font-weight: bold;
    color: #212529;
}

.payment-option {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    cursor: pointer;
}

.payment-option:hover, 
.payment-option.selected {
    border-color: #007bff;
    background-color: #f8f9fa;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Payment method selection
    const paymentOptions = document.querySelectorAll('.payment-option');
    const paymentMethodInput = document.getElementById('payment_method');
    const paymentRadios = document.querySelectorAll('input[name="payment_method_radio"]');
    const screenshotUploadSection = document.getElementById('screenshot-upload-section');
    
    paymentOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Remove selected class from all options
            paymentOptions.forEach(opt => opt.classList.remove('selected'));
            
            // Add selected class to clicked option
            this.classList.add('selected');
            
            // Get method from data attribute
            const method = this.getAttribute('data-method');
            
            // Update hidden input value
            if (paymentMethodInput) {
                paymentMethodInput.value = method;
            }
            
            // Check the corresponding radio button
            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
            }
            
            // Show/hide screenshot upload section
            if (screenshotUploadSection) {
                if (method === 'gcash') {
                    screenshotUploadSection.style.display = 'block';
                } else {
                    screenshotUploadSection.style.display = 'none';
                }
            }
        });
    });
    
    // Also trigger change on radio button click
    paymentRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const method = this.value;
            
            // Update hidden input
            if (paymentMethodInput) {
                paymentMethodInput.value = method;
            }
            
            // Update selected class
            paymentOptions.forEach(opt => {
                if (opt.getAttribute('data-method') === method) {
                    opt.classList.add('selected');
                } else {
                    opt.classList.remove('selected');
                }
            });
            
            // Show/hide screenshot upload
            if (screenshotUploadSection) {
                if (method === 'gcash') {
                    screenshotUploadSection.style.display = 'block';
                } else {
                    screenshotUploadSection.style.display = 'none';
                }
            }
        });
    });
    
    // File upload
    const fileInput = document.getElementById('payment_screenshot');
    const fileNameDisplay = document.getElementById('file-name-display');
    
    if (fileInput && fileNameDisplay) {
        fileInput.addEventListener('change', function() {
            if (fileInput.files.length > 0) {
                fileNameDisplay.textContent = fileInput.files[0].name;
                fileNameDisplay.classList.add('file-upload-success');
            } else {
                fileNameDisplay.textContent = '';
                fileNameDisplay.classList.remove('file-upload-success');
            }
        });
    }
    
    // Form validation
    const checkoutForm = document.getElementById('checkout-form');
    
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(event) {
            const paymentMethod = paymentMethodInput.value;
            
            // Validate payment method selection
            if (!paymentMethod) {
                event.preventDefault();
                alert('Please select a payment method');
                return false;
            }
            
            // Validate payment screenshot for GCash payments
            if (paymentMethod === 'gcash' && fileInput && fileInput.files.length === 0) {
                event.preventDefault();
                alert('Please upload a payment screenshot');
                return false;
            }
        });
    }
});
</script>