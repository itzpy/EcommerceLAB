<?php
session_start();
header('Content-Type: application/json');
require_once('../controllers/product_controller.php');

$response = array();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $response['success'] = false;
    $response['message'] = 'Unauthorized. Please log in.';
    echo json_encode($response);
    exit();
}

$user_id = $_SESSION['customer_id'];
$products = get_products_ctr($user_id);

$response['success'] = true;
$response['data'] = $products;
echo json_encode($response);
?>
