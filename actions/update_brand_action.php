<?php

header('Content-Type: application/json');

require_once '../controllers/brand_controller.php';

session_start();

$response = array();

if (isset($_POST['brand_id']) && isset($_POST['brand_name']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && $_SESSION['role'] == 1) {
    $brand_id = (int)$_POST['brand_id'];
    $brand_name = trim($_POST['brand_name']);
    $user_id = $_SESSION['customer_id'];
    
    if (!empty($brand_name) && $brand_id > 0) {
        $result = update_brand_ctr($brand_id, $brand_name, $user_id);
        
        if ($result) {
            $response['success'] = true;
            $response['message'] = 'Brand updated successfully';
        } else {
            $response['success'] = false;
            $response['message'] = 'Failed to update brand';
        }
    } else {
        $response['success'] = false;
        $response['message'] = 'Invalid brand data';
    }
} else {
    $response['success'] = false;
    $response['message'] = 'Invalid request';
}

echo json_encode($response);
