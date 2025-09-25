<?php

require_once '../settings/db_class.php';

/**
 * Customer class for handling customer operations
 */
class Customer extends db_connection
{
    private $customer_id;
    private $name;
    private $email;
    private $password;
    private $role;
    private $date_created;
    private $phone_number;
    private $country;
    private $city;

    public function __construct($customer_id = null)
    {
        parent::db_connect();
        if ($customer_id) {
            $this->customer_id = $customer_id;
            $this->loadCustomer();
        }
    }

    private function loadCustomer($customer_id = null)
    {
        if ($customer_id) {
            $this->customer_id = $customer_id;
        }
        if (!$this->customer_id) {
            return false;
        }
        $stmt = $this->db->prepare("SELECT * FROM customer WHERE customer_id = ?");
        $stmt->bind_param("i", $this->customer_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if ($result) {
            $this->name = $result['customer_name'];
            $this->email = $result['customer_email'];
            $this->role = $result['user_role'];
            $this->date_created = isset($result['date_created']) ? $result['date_created'] : null;
            $this->phone_number = $result['customer_contact'];
            $this->country = $result['customer_country'];
            $this->city = $result['customer_city'];
        }
    }

    public function addCustomer($name, $email, $password, $phone_number, $country, $city, $role)
    {
        // Check if email already exists
        if ($this->getCustomerByEmail($email)) {
            return false; // Email already exists
        }
        
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO customer (customer_name, customer_email, customer_pass, customer_contact, customer_country, customer_city, user_role) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssi", $name, $email, $hashed_password, $phone_number, $country, $city, $role);
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    public function getCustomerByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM customer WHERE customer_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function loginCustomer($email, $password)
    {
        // Get customer by email
        $customer = $this->getCustomerByEmail($email);
        
        if (!$customer) {
            return false; // Customer not found
        }
        
        // Verify password
        if (password_verify($password, $customer['customer_pass'])) {
            // Password is correct, return customer data (without password)
            unset($customer['customer_pass']); // Remove password from returned data
            return $customer;
        }
        
        return false; // Invalid password
    }

}
