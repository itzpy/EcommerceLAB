<?php

header('Content-Type: application/json');

require_once '../controllers/category_controller.php';

session_start();

$response = array();

if (isset($_POST['cat_name']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && $_SESSION['role'] == 1) {
    $cat_name = trim($_POST['cat_name']);
    $user_id = $_SESSION['customer_id'];
    
    if (!empty($cat_name)) {
        $result = add_category_ctr($cat_name, $user_id);
        
        if ($result) {
            $response['success'] = true;
            $response['message'] = 'Category added successfully';
        } else {
            $response['success'] = false;
            $response['message'] = 'Failed to add category';
        }
    } else {
        $response['success'] = false;
        $response['message'] = 'Category name is required';
    }
} else {
    $response['success'] = false;
    $response['message'] = 'Invalid request';
}

echo json_encode($response);