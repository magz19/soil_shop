<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection and functions
require_once '../includes/db_connection.php';

// Get form data
$fullName = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$address = isset($_POST['address']) ? trim($_POST['address']) : '';
$city = isset($_POST['city']) ? trim($_POST['city']) : '';
$state = isset($_POST['state']) ? trim($_POST['state']) : '';
$zip = isset($_POST['zip']) ? trim($_POST['zip']) : '';
$shippingMethod = isset($_POST['shipping_method']) ? trim($_POST['shipping_method']) : '';

// Validate data
$errors = [];

if (empty($fullName)) {
    $errors[] = 'Full name is required.';
}

if (empty($phone)) {
    $errors[] = 'Phone number is required.';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Valid email is required.';
}

if (empty($address)) {
    $errors[] = 'Address is required.';
}

if (empty($city)) {
    $errors[] = 'City is required.';
}

if (empty($state)) {
    $errors[] = 'State/Province is required.';
}

if (empty($zip)) {
    $errors[] = 'Zip code is required.';
}

if (empty($shippingMethod)) {
    $errors[] = 'Shipping method is required.';
} elseif (!in_array($shippingMethod, ['pickup', 'delivery'])) {
    $errors[] = 'Invalid shipping method.';
}

// If there are errors, redirect back to checkout with errors
if (!empty($errors)) {
    $_SESSION['error'] = implode('<br>', $errors);
    $_SESSION['form_data'] = $_POST;
    header('Location: ../index.php?page=checkout&step=shipping');
    exit;
}

// Store shipping info in session
$_SESSION['checkout_shipping'] = [
    'full_name' => $fullName,
    'phone' => $phone,
    'email' => $email,
    'address' => $address,
    'city' => $city,
    'state' => $state,
    'zip' => $zip,
    'shipping_method' => $shippingMethod
];

// Redirect to payment step
header('Location: ../index.php?page=checkout&step=payment');
exit;
?>