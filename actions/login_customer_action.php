<?php

header('Content-Type: application/json');

session_start();

$response = array();

// Check if the user is already logged in and redirect to the dashboard
if (isset($_SESSION['customer_id'])) {
    $response['status'] = 'error';
    $response['message'] = 'You are already logged in';
    echo json_encode($response);
    exit();
}

require_once '../controllers/customer_controller.php';

$email = $_POST['email'];
$password = $_POST['password'];

$customer_data = login_customer_ctr($email, $password);

if ($customer_data) {
    // Set session variables
    $_SESSION['id'] = $customer_data['customer_id'];
    $_SESSION['customer_id'] = $customer_data['customer_id'];
    $_SESSION['name'] = $customer_data['customer_name'];
    $_SESSION['email'] = $customer_data['customer_email'];
    $_SESSION['role'] = $customer_data['user_role'];
    $_SESSION['country'] = $customer_data['customer_country'];
    $_SESSION['city'] = $customer_data['customer_city'];
    $_SESSION['contact'] = $customer_data['customer_contact'];
    $_SESSION['logged_in'] = true;
    
    $response['status'] = 'success';
    $response['message'] = 'Login successful';
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid email or password';
}

echo json_encode($response);