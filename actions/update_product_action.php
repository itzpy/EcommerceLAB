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

if ($_SESSION['role'] != 1) {
    $response['success'] = false;
    $response['message'] = 'Access denied. Admin privileges required.';
    echo json_encode($response);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['success'] = false;
    $response['message'] = 'Invalid request method.';
    echo json_encode($response);
    exit();
}

$product_id = $_POST['product_id'] ?? '';
$product_cat = $_POST['product_cat'] ?? '';
$product_brand = $_POST['product_brand'] ?? '';
$product_title = $_POST['product_title'] ?? '';
$product_price = $_POST['product_price'] ?? '';
$product_desc = $_POST['product_desc'] ?? '';
$product_image = $_POST['product_image'] ?? '';
$product_keywords = $_POST['product_keywords'] ?? '';
$user_id = $_SESSION['customer_id'];

if (empty($product_id) || empty($product_cat) || empty($product_brand) || empty($product_title) || empty($product_price)) {
    $response['success'] = false;
    $response['message'] = 'All required fields must be filled.';
    echo json_encode($response);
    exit();
}

$result = update_product_ctr($product_id, $product_cat, $product_brand, $product_title, $product_price, $product_desc, $product_image, $product_keywords, $user_id);

if ($result) {
    $response['success'] = true;
    $response['message'] = 'Product updated successfully!';
} else {
    $response['success'] = false;
    $response['message'] = 'Failed to update product.';
}

echo json_encode($response);
?>
