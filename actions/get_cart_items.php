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

// Get cart items
$result = get_user_cart_ctr($customer_id);

if ($result === false) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to retrieve cart items'
    ]);
    exit();
}

$items = [];
$total = 0;

while ($row = $result->fetch_assoc()) {
    $items[] = $row;
    $total += $row['subtotal'];
}

echo json_encode([
    'success' => true,
    'items' => $items,
    'total' => $total
]);
?>
