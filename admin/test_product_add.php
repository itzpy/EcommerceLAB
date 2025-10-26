<?php
session_start();

// Check if logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode([
        'success' => false,
        'message' => 'Not logged in',
        'session_data' => $_SESSION
    ]);
    exit();
}

// Check if admin
if ($_SESSION['role'] != 1) {
    echo json_encode([
        'success' => false,
        'message' => 'Not admin. Your role is: ' . $_SESSION['role']
    ]);
    exit();
}

require_once('../settings/db_class.php');

// Test database connection
$db = new db_connection();
$conn = $db->db_conn();

if (!$conn) {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed'
    ]);
    exit();
}

// Test if products table exists and has correct structure
$result = $conn->query("DESCRIBE products");
$columns = [];
while ($row = $result->fetch_assoc()) {
    $columns[] = $row['Field'];
}

// Test if categories and brands exist
$cat_result = $conn->query("SELECT cat_id, cat_name FROM categories WHERE user_id = " . $_SESSION['customer_id']);
$categories = [];
while ($row = $cat_result->fetch_assoc()) {
    $categories[] = $row;
}

$brand_result = $conn->query("SELECT brand_id, brand_name FROM brands WHERE user_id = " . $_SESSION['customer_id']);
$brands = [];
while ($row = $brand_result->fetch_assoc()) {
    $brands[] = $row;
}

echo json_encode([
    'success' => true,
    'message' => 'All checks passed!',
    'session' => [
        'customer_id' => $_SESSION['customer_id'],
        'name' => $_SESSION['name'],
        'email' => $_SESSION['email'],
        'role' => $_SESSION['role']
    ],
    'database' => [
        'connected' => true,
        'products_columns' => $columns,
        'has_user_id' => in_array('user_id', $columns),
        'has_date_created' => in_array('date_created', $columns)
    ],
    'data' => [
        'categories_count' => count($categories),
        'categories' => $categories,
        'brands_count' => count($brands),
        'brands' => $brands
    ]
]);
?>
