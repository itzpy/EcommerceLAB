<?php
session_start();
require_once(dirname(__FILE__).'/../controllers/cart_controller.php');

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please login to add items to cart'
    ]);
    exit();
}

// Validate required parameters
if (!isset($_POST['product_id']) || !isset($_POST['qty'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required parameters'
    ]);
    exit();
}

$product_id = (int)$_POST['product_id'];
$qty = (int)$_POST['qty'];
$customer_id = $_SESSION['customer_id'];
$ip_address = $_SERVER['REMOTE_ADDR'];

// Validate quantity
if ($qty <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Quantity must be greater than 0'
    ]);
    exit();
}

// Add to cart
$result = add_to_cart_ctr($product_id, $customer_id, $qty, $ip_address);

if ($result) {
    // Get updated cart count
    $cart_count = get_cart_count_ctr($customer_id);
    
    echo json_encode([
        'success' => true,
        'message' => 'Product added to cart successfully',
        'cart_count' => $cart_count
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to add product to cart'
    ]);
}
?>
