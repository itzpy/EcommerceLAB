<?php

require_once '../classes/customer_class.php';


function register_customer_ctr($name, $email, $password, $phone_number, $country, $city, $role)
{
    $customer = new Customer();
    $customer_id = $customer->addCustomer($name, $email, $password, $phone_number, $country, $city, $role);
    if ($customer_id) {
        return $customer_id;
    }
    return false;
}

function get_customer_by_email_ctr($email)
{
    $customer = new Customer();
    return $customer->getCustomerByEmail($email);
}

function login_customer_ctr($email, $password)
{
    $customer = new Customer();
    return $customer->loginCustomer($email, $password);
}