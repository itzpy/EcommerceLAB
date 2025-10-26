<?php

header('Content-Type: application/json');

require_once '../controllers/brand_controller.php';

session_start();

$response = array();

if (isset($_POST['brand_name']) && isset($_POST['cat_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && $_SESSION['role'] == 1) {
    $brand_name = trim($_POST['brand_name']);
    $cat_id = (int)$_POST['cat_id'];
    $user_id = $_SESSION['id'];
    
    if (!empty($brand_name) && $cat_id > 0) {
        $result = add_brand_ctr($brand_name, $cat_id, $user_id);
        
        if ($result) {
            $response['success'] = true;
            $response['message'] = 'Brand added successfully';
        } else {
            $response['success'] = false;
            $response['message'] = 'Failed to add brand';
        }
    } else {
        $response['success'] = false;
        $response['message'] = 'Brand name and category are required';
    }
} else {
    $response['success'] = false;
    $response['message'] = 'Invalid request';
}

echo json_encode($response);
