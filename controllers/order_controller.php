<?php
require_once(dirname(__FILE__).'/../classes/order_class.php');

/**
 * Create a new order
 * @param int $customer_id
 * @param int $invoice_no
 * @param string $order_date
 * @param string $order_status
 * @return int|false
 */
function create_order_ctr($customer_id, $invoice_no, $order_date, $order_status) {
    $order = new Order();
    return $order->createOrder($customer_id, $invoice_no, $order_date, $order_status);
}

/**
 * Add order details
 * @param int $order_id
 * @param int $product_id
 * @param int $qty
 * @return bool
 */
function add_order_details_ctr($order_id, $product_id, $qty) {
    $order = new Order();
    return $order->addOrderDetails($order_id, $product_id, $qty);
}

/**
 * Record payment
 * @param float $amount
 * @param int $customer_id
 * @param int $order_id
 * @param string $currency
 * @param string $payment_date
 * @return int|false
 */
function record_payment_ctr($amount, $customer_id, $order_id, $currency, $payment_date) {
    $order = new Order();
    return $order->recordPayment($amount, $customer_id, $order_id, $currency, $payment_date);
}

/**
 * Get user orders
 * @param int $customer_id
 * @return array|false
 */
function get_user_orders_ctr($customer_id) {
    $order = new Order();
    return $order->getUserOrders($customer_id);
}

/**
 * Get order details
 * @param int $order_id
 * @return array|false
 */
function get_order_details_ctr($order_id) {
    $order = new Order();
    return $order->getOrderDetails($order_id);
}

/**
 * Generate invoice number
 * @return int
 */
function generate_invoice_number_ctr() {
    $order = new Order();
    return $order->generateInvoiceNumber();
}
?>
