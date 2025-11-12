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
if (!isset($_POST['product_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing product ID'
    ]);
    exit();
}

$product_id = (int)$_POST['product_id'];
$customer_id = $_SESSION['customer_id'];

// Remove from cart
$result = remove_from_cart_ctr($product_id, $customer_id);

if ($result) {
    // Get updated cart count
    $cart_count = get_cart_count_ctr($customer_id);
    
    echo json_encode([
        'success' => true,
        'message' => 'Product removed from cart',
        'cart_count' => $cart_count
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to remove product from cart'
    ]);
}
?>
