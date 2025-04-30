<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection and functions
require_once '../includes/db_connection.php';

// Get form data
$paymentMethod = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : '';

// Validate payment method
if (empty($paymentMethod) || !in_array($paymentMethod, ['gcash', 'counter'])) {
    $_SESSION['error'] = 'Invalid payment method.';
    header('Location: ../index.php?page=checkout&step=payment');
    exit;
}

// Process payment screenshot if GCash was selected
$paymentScreenshot = '';

if ($paymentMethod === 'gcash') {
    // Check if payment screenshot was uploaded
    if (!isset($_FILES['payment_screenshot']) || $_FILES['payment_screenshot']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['error'] = 'Payment screenshot is required for GCash payments.';
        header('Location: ../index.php?page=checkout&step=payment');
        exit;
    }
    
    // Define upload directory
    $uploadDir = '../uploads/payment_screenshots/';
    
    // Create directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // Generate unique filename
    $filename = uniqid() . '_' . $_FILES['payment_screenshot']['name'];
    $uploadFile = $uploadDir . $filename;
    
    // Check file type (allow only images)
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($_FILES['payment_screenshot']['type'], $allowedTypes)) {
        $_SESSION['error'] = 'Invalid file type. Only JPG, PNG, and GIF files are allowed.';
        header('Location: ../index.php?page=checkout&step=payment');
        exit;
    }
    
    // Move uploaded file
    if (!move_uploaded_file($_FILES['payment_screenshot']['tmp_name'], $uploadFile)) {
        $_SESSION['error'] = 'Failed to upload payment screenshot.';
        header('Location: ../index.php?page=checkout&step=payment');
        exit;
    }
    
    $paymentScreenshot = $uploadFile;
}

// Store payment info in session
$_SESSION['checkout_payment'] = [
    'payment_method' => $paymentMethod,
    'payment_screenshot' => $paymentScreenshot
];

// Redirect to review step
header('Location: ../index.php?page=checkout&step=review');
exit;
?>