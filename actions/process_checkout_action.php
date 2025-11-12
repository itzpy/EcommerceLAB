<?php
session_start();
require_once(dirname(__FILE__).'/../controllers/cart_controller.php');
require_once(dirname(__FILE__).'/../controllers/order_controller.php');

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please login to complete checkout'
    ]);
    exit();
}

$customer_id = $_SESSION['customer_id'];

// Get user's cart items
$cart_result = get_user_cart_ctr($customer_id);

if (!$cart_result || $cart_result->num_rows == 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Your cart is empty'
    ]);
    exit();
}

// Calculate total amount
$total_amount = 0;
$cart_items = [];

while ($item = $cart_result->fetch_assoc()) {
    $cart_items[] = $item;
    $total_amount += $item['subtotal'];
}

// Begin transaction
$conn = (new Order())->db_conn();
$conn->begin_transaction();

try {
    // 1. Generate invoice number
    $invoice_no = generate_invoice_number_ctr();
    
    // 2. Create order
    $order_date = date('Y-m-d');
    $order_status = 'Pending';
    $order_id = create_order_ctr($customer_id, $invoice_no, $order_date, $order_status);
    
    if (!$order_id) {
        throw new Exception('Failed to create order');
    }
    
    // 3. Add order details for each cart item
    foreach ($cart_items as $item) {
        $result = add_order_details_ctr($order_id, $item['p_id'], $item['qty']);
        if (!$result) {
            throw new Exception('Failed to add order details');
        }
    }
    
    // 4. Record payment
    $payment_date = date('Y-m-d');
    $currency = 'GHS';
    $payment_id = record_payment_ctr($total_amount, $customer_id, $order_id, $currency, $payment_date);
    
    if (!$payment_id) {
        throw new Exception('Failed to record payment');
    }
    
    // 5. Empty the cart
    $empty_result = empty_cart_ctr($customer_id);
    if (!$empty_result) {
        throw new Exception('Failed to empty cart');
    }
    
    // Commit transaction
    $conn->commit();
    
    // Success response
    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully!',
        'order_id' => $order_id,
        'invoice_no' => $invoice_no,
        'total_amount' => number_format($total_amount, 2),
        'currency' => $currency
    ]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    
    error_log("Checkout failed: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Checkout failed: ' . $e->getMessage()
    ]);
}
?>
