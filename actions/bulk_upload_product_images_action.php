<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

if (!isset($_FILES['product_images']) || empty($_FILES['product_images']['name'][0])) {
    $response['success'] = false;
    $response['message'] = 'No files uploaded.';
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

$files = $_FILES['product_images'];
$allowed_types = array('image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp');
$max_size = 5 * 1024 * 1024; // 5MB
$max_files = 10; // Maximum 10 images

// Count actual files uploaded
$file_count = count(array_filter($files['name']));

if ($file_count > $max_files) {
    $response['success'] = false;
    $response['message'] = "Maximum {$max_files} images allowed. You uploaded {$file_count}.";
    echo json_encode($response);
    exit();
}

// Base uploads directory
$uploads_dir = dirname(__FILE__) . '/../uploads';

if (!file_exists($uploads_dir)) {
    $response['success'] = false;
    $response['message'] = 'Uploads directory does not exist. Contact administrator.';
    echo json_encode($response);
    exit();
}

// Create user directory
$user_dir = $uploads_dir . '/u' . $user_id;
if (!file_exists($user_dir)) {
    if (!mkdir($user_dir, 0755, true)) {
        $response['success'] = false;
        $response['message'] = 'Failed to create user directory.';
        echo json_encode($response);
        exit();
    }
}

// Create product directory
$product_dir = $user_dir . '/p' . $product_id;
if (!file_exists($product_dir)) {
    if (!mkdir($product_dir, 0755, true)) {
        $response['success'] = false;
        $response['message'] = 'Failed to create product directory.';
        echo json_encode($response);
        exit();
    }
}

$uploaded_images = array();
$errors = array();

// Process each file
for ($i = 0; $i < $file_count; $i++) {
    if ($files['error'][$i] !== UPLOAD_ERR_OK) {
        $errors[] = "File " . ($i + 1) . ": Upload error occurred.";
        continue;
    }

    // Validate file type
    if (!in_array($files['type'][$i], $allowed_types)) {
        $errors[] = "File " . ($i + 1) . " (" . $files['name'][$i] . "): Invalid file type.";
        continue;
    }

    // Validate file size
    if ($files['size'][$i] > $max_size) {
        $errors[] = "File " . ($i + 1) . " (" . $files['name'][$i] . "): Exceeds 5MB limit.";
        continue;
    }

    // Generate unique filename
    $file_ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
    $new_filename = 'image_' . time() . '_' . $i . '.' . $file_ext;
    $destination = $product_dir . '/' . $new_filename;

    // Verify destination is within uploads directory (security check)
    $real_uploads_dir = realpath($uploads_dir);
    $real_destination = realpath(dirname($destination)) . '/' . basename($destination);
    
    if (strpos($real_destination, $real_uploads_dir) !== 0) {
        $errors[] = "File " . ($i + 1) . ": Invalid upload path.";
        continue;
    }

    // Move uploaded file
    if (move_uploaded_file($files['tmp_name'][$i], $destination)) {
        // Store relative path from project root
        $relative_path = 'uploads/u' . $user_id . '/p' . $product_id . '/' . $new_filename;
        $uploaded_images[] = $relative_path;
    } else {
        $errors[] = "File " . ($i + 1) . " (" . $files['name'][$i] . "): Failed to save.";
    }
}

if (empty($uploaded_images)) {
    $response['success'] = false;
    $response['message'] = 'No images were uploaded successfully.';
    $response['errors'] = $errors;
    echo json_encode($response);
    exit();
}

// Update product with first image path (primary image)
require_once('../controllers/product_controller.php');

try {
    // Get current product data
    $product = get_product_by_id_ctr($product_id);
    
    if ($product) {
        // Use the first uploaded image as the primary image
        $primary_image = $uploaded_images[0];
        
        // Update product with primary image
        $result = update_product_image_ctr($product_id, $primary_image, $user_id);
        
        if ($result) {
            $response['success'] = true;
            $response['message'] = count($uploaded_images) . ' image(s) uploaded successfully!';
            $response['uploaded_images'] = $uploaded_images;
            $response['primary_image'] = $primary_image;
            if (!empty($errors)) {
                $response['warnings'] = $errors;
            }
        } else {
            $response['success'] = false;
            $response['message'] = 'Images uploaded but failed to update product.';
            $response['uploaded_images'] = $uploaded_images;
        }
    } else {
        $response['success'] = false;
        $response['message'] = 'Product not found.';
    }
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = 'Error updating product: ' . $e->getMessage();
    $response['uploaded_images'] = $uploaded_images;
}

echo json_encode($response);
?>
