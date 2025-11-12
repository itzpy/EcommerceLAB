<?php
session_start();
require_once(dirname(__FILE__).'/../controllers/cart_controller.php');

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    echo json_encode([
        'success' => false,
        'count' => 0
    ]);
    exit();
}

$customer_id = $_SESSION['customer_id'];
$count = get_cart_count_ctr($customer_id);

echo json_encode([
    'success' => true,
    'count' => $count
]);
?>
