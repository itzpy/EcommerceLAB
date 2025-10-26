<?php
session_start();
header('Content-Type: application/json');
require_once('../controllers/brand_controller.php');

$response = array();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $response['success'] = false;
    $response['message'] = 'Unauthorized. Please log in.';
    echo json_encode($response);
    exit();
}

$cat_id = $_GET['cat_id'] ?? '';
$user_id = $_SESSION['customer_id'];

if (empty($cat_id)) {
    $response['success'] = false;
    $response['message'] = 'Category ID is required.';
    echo json_encode($response);
    exit();
}

require_once('../classes/brand_class.php');
$brand = new Brand();
$sql = "SELECT brand_id, brand_name FROM brands WHERE cat_id = ? AND user_id = ? ORDER BY brand_name";
$stmt = $brand->db_conn()->prepare($sql);
$stmt->bind_param("ii", $cat_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$brands = $result->fetch_all(MYSQLI_ASSOC);

$response['success'] = true;
$response['data'] = $brands;
echo json_encode($response);
?>
