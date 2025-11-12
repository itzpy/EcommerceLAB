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

$customer_id = $_SESSION['customer_id'];

// Empty cart
$result = empty_cart_ctr($customer_id);

if ($result) {
    echo json_encode([
        'success' => true,
        'message' => 'Cart emptied successfully',
        'cart_count' => 0
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to empty cart'
    ]);
}
?>
