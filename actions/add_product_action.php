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

$product_cat = $_POST['product_cat'] ?? '';
$product_brand = $_POST['product_brand'] ?? '';
$product_title = $_POST['product_title'] ?? '';
$product_price = $_POST['product_price'] ?? '';
$product_desc = $_POST['product_desc'] ?? '';
$product_image = $_POST['product_image'] ?? '';
$product_keywords = $_POST['product_keywords'] ?? '';
$user_id = $_SESSION['customer_id'];

if (empty($product_cat) || empty($product_brand) || empty($product_title) || empty($product_price)) {
    $response['success'] = false;
    $response['message'] = 'Category, Brand, Title, and Price are required.';
    echo json_encode($response);
    exit();
}

$existing = get_product_by_title_ctr($product_title, $product_brand, $user_id);
if ($existing) {
    $response['success'] = false;
    $response['message'] = 'Product already exists for this brand.';
    echo json_encode($response);
    exit();
}

try {
    $result = add_product_ctr($product_cat, $product_brand, $product_title, $product_price, $product_desc, $product_image, $product_keywords, $user_id);

    if ($result) {
        $response['success'] = true;
        $response['message'] = 'Product added successfully!';
        $response['product_id'] = $result;
    } else {
        $response['success'] = false;
        $response['message'] = 'Failed to add product. Database operation returned false.';
        $response['debug'] = [
            'user_id' => $user_id,
            'product_cat' => $product_cat,
            'product_brand' => $product_brand,
            'product_title' => $product_title
        ];
    }
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = 'Error adding product: ' . $e->getMessage();
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
?>
