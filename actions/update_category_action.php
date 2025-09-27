<?php

header('Content-Type: application/json');

require_once '../controllers/category_controller.php';

session_start();

$response = array();

if (isset($_POST['cat_id']) && isset($_POST['cat_name']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && $_SESSION['role'] == 1) {
    $cat_id = (int)$_POST['cat_id'];
    $cat_name = trim($_POST['cat_name']);
    $user_id = $_SESSION['id'];
    
    if (!empty($cat_name) && $cat_id > 0) {
        $result = update_category_ctr($cat_id, $cat_name, $user_id);
        
        if ($result) {
            $response['success'] = true;
            $response['message'] = 'Category updated successfully';
        } else {
            $response['success'] = false;
            $response['message'] = 'Failed to update category';
        }
    } else {
        $response['success'] = false;
        $response['message'] = 'Invalid category data';
    }
} else {
    $response['success'] = false;
    $response['message'] = 'Invalid request';
}

echo json_encode($response);