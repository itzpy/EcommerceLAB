<?php
session_start();
header('Content-Type: application/json');

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

if (!isset($_FILES['product_image']) || $_FILES['product_image']['error'] !== UPLOAD_ERR_OK) {
    $response['success'] = false;
    $response['message'] = 'No file uploaded or upload error occurred.';
    echo json_encode($response);
    exit();
}

$user_id = $_SESSION['customer_id'];
$product_id = $_POST['product_id'] ?? '';

if (empty($product_id)) {
    $response['success'] = false;
    $response['message'] = 'Product ID is required.';
    echo json_encode($response);
    exit();
}

$file = $_FILES['product_image'];
$allowed_types = array('image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp');
$max_size = 5 * 1024 * 1024;

if (!in_array($file['type'], $allowed_types)) {
    $response['success'] = false;
    $response['message'] = 'Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.';
    echo json_encode($response);
    exit();
}

if ($file['size'] > $max_size) {
    $response['success'] = false;
    $response['message'] = 'File size exceeds 5MB limit.';
    echo json_encode($response);
    exit();
}

$uploads_dir = dirname(__FILE__) . '/../uploads';

if (!file_exists($uploads_dir)) {
    $response['success'] = false;
    $response['message'] = 'Uploads directory does not exist. Contact administrator.';
    echo json_encode($response);
    exit();
}

$user_dir = $uploads_dir . '/u' . $user_id;
if (!file_exists($user_dir)) {
    if (!mkdir($user_dir, 0755, true)) {
        $response['success'] = false;
        $response['message'] = 'Failed to create user directory.';
        echo json_encode($response);
        exit();
    }
}

$product_dir = $user_dir . '/p' . $product_id;
if (!file_exists($product_dir)) {
    if (!mkdir($product_dir, 0755, true)) {
        $response['success'] = false;
        $response['message'] = 'Failed to create product directory.';
        echo json_encode($response);
        exit();
    }
}

$realpath = realpath($product_dir);
$uploads_realpath = realpath($uploads_dir);

if (strpos($realpath, $uploads_realpath) !== 0) {
    $response['success'] = false;
    $response['message'] = 'Security violation: Upload path is outside uploads directory.';
    echo json_encode($response);
    exit();
}

$file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$file_name = 'image_' . time() . '.' . $file_extension;
$destination = $product_dir . '/' . $file_name;

if (move_uploaded_file($file['tmp_name'], $destination)) {
    $relative_path = 'uploads/u' . $user_id . '/p' . $product_id . '/' . $file_name;
    $response['success'] = true;
    $response['message'] = 'Image uploaded successfully!';
    $response['image_path'] = $relative_path;
} else {
    $response['success'] = false;
    $response['message'] = 'Failed to move uploaded file.';
}

echo json_encode($response);
?>
