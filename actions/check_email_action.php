<?php

header('Content-Type: application/json');

require_once '../controllers/customer_controller.php';

$response = array();

if (isset($_POST['email'])) {
    $email = $_POST['email'];
    $customer = get_customer_by_email_ctr($email);
    
    if ($customer) {
        $response['exists'] = true;
    } else {
        $response['exists'] = false;
    }
} else {
    $response['exists'] = false;
}

echo json_encode($response);