<?php

header('Content-Type: application/json');

require_once '../controllers/brand_controller.php';

session_start();

$response = array();

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && $_SESSION['role'] == 1) {
    $user_id = $_SESSION['customer_id'];
    $brands = get_brands_ctr($user_id);
    
    if ($brands) {
        $response['success'] = true;
        $response['data'] = $brands;
    } else {
        $response['success'] = false;
        $response['data'] = array();
    }
} else {
    $response['success'] = false;
    $response['message'] = 'Unauthorized access';
}

echo json_encode($response);
