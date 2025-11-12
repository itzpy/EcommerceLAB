<?php
session_start();
require_once(dirname(__FILE__).'/../controllers/cart_controller.php');

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please login first'
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

// Validate quantity
if ($qty <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Quantity must be greater than 0'
    ]);
    exit();
}

// Update quantity
$result = update_cart_item_ctr($product_id, $customer_id, $qty);

if ($result) {
    echo json_encode([
        'success' => true,
        'message' => 'Cart updated successfully'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to update cart'
    ]);
}
?>
