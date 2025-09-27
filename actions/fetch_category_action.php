<?php

header('Content-Type: application/json');

require_once '../controllers/category_controller.php';

session_start();

$response = array();

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && $_SESSION['role'] == 1) {
    $user_id = $_SESSION['id'];
    $categories = get_categories_ctr($user_id);
    
    if ($categories) {
        $response['success'] = true;
        $response['data'] = $categories;
    } else {
        $response['success'] = false;
        $response['data'] = array();
    }
} else {
    $response['success'] = false;
    $response['message'] = 'Unauthorized access';
}

echo json_encode($response);