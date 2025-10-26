<?php

header('Content-Type: application/json');

require_once '../controllers/brand_controller.php';

session_start();

$response = array();

if (isset($_POST['brand_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && $_SESSION['role'] == 1) {
    $brand_id = (int)$_POST['brand_id'];
    $user_id = $_SESSION['customer_id'];
    
    if ($brand_id > 0) {
        $result = delete_brand_ctr($brand_id, $user_id);
        
        if ($result) {
            $response['success'] = true;
            $response['message'] = 'Brand deleted successfully';
        } else {
            $response['success'] = false;
            $response['message'] = 'Failed to delete brand';
        }
    } else {
        $response['success'] = false;
        $response['message'] = 'Invalid brand ID';
    }
} else {
    $response['success'] = false;
    $response['message'] = 'Invalid request';
}

echo json_encode($response);
