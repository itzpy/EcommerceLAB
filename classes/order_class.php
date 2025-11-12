<?php
require_once(dirname(__FILE__).'/../settings/db_class.php');

/**
 * Order class to handle all order operations
 */
class Order extends db_connection {
    
    /**
     * Create a new order
     * @param int $customer_id
     * @param int $invoice_no
     * @param string $order_date
     * @param string $order_status
     * @return int|false Returns order_id on success, false on failure
     */
    public function createOrder($customer_id, $invoice_no, $order_date, $order_status) {
        $sql = "INSERT INTO orders (customer_id, invoice_no, order_date, order_status) 
                VALUES (?, ?, ?, ?)";
        
        $conn = $this->db_conn();
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            return false;
        }
        
        $stmt->bind_param("iiss", $customer_id, $invoice_no, $order_date, $order_status);
        
        if ($stmt->execute()) {
            return $conn->insert_id;
        }
        
        error_log("Execute failed: " . $stmt->error);
        return false;
    }
    
    /**
     * Add order details (line items)
     * @param int $order_id
     * @param int $product_id
     * @param int $qty
     * @return bool
     */
    public function addOrderDetails($order_id, $product_id, $qty) {
        $sql = "INSERT INTO orderdetails (order_id, product_id, qty) 
                VALUES (?, ?, ?)";
        
        $conn = $this->db_conn();
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            return false;
        }
        
        $stmt->bind_param("iii", $order_id, $product_id, $qty);
        return $stmt->execute();
    }
    
    /**
     * Record payment
     * @param float $amount
     * @param int $customer_id
     * @param int $order_id
     * @param string $currency
     * @param string $payment_date
     * @return int|false Returns payment_id on success, false on failure
     */
    public function recordPayment($amount, $customer_id, $order_id, $currency, $payment_date) {
        $sql = "INSERT INTO payment (amt, customer_id, order_id, currency, payment_date) 
                VALUES (?, ?, ?, ?, ?)";
        
        $conn = $this->db_conn();
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            return false;
        }
        
        $stmt->bind_param("diiss", $amount, $customer_id, $order_id, $currency, $payment_date);
        
        if ($stmt->execute()) {
            return $conn->insert_id;
        }
        
        error_log("Execute failed: " . $stmt->error);
        return false;
    }
    
    /**
     * Get all orders for a user
     * @param int $customer_id
     * @return array|false
     */
    public function getUserOrders($customer_id) {
        $sql = "SELECT o.order_id, o.invoice_no, o.order_date, o.order_status,
                       p.amt as total_amount, p.currency, p.payment_date
                FROM orders o
                LEFT JOIN payment p ON o.order_id = p.order_id
                WHERE o.customer_id = ?
                ORDER BY o.order_date DESC, o.order_id DESC";
        
        $conn = $this->db_conn();
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            return false;
        }
        
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result;
    }
    
    /**
     * Get order details (items) for a specific order
     * @param int $order_id
     * @return array|false
     */
    public function getOrderDetails($order_id) {
        $sql = "SELECT od.order_id, od.product_id, od.qty,
                       p.product_title, p.product_price, p.product_image,
                       (p.product_price * od.qty) as subtotal
                FROM orderdetails od
                JOIN products p ON od.product_id = p.product_id
                WHERE od.order_id = ?";
        
        $conn = $this->db_conn();
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            return false;
        }
        
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result;
    }
    
    /**
     * Generate unique invoice number based on date
     * Format: YYYYMMDDNNN (e.g., 20251112001)
     * @return int
     */
    public function generateInvoiceNumber() {
        $date_prefix = date('Ymd'); // e.g., 20251112
        
        // Get the last invoice number for today
        $sql = "SELECT MAX(invoice_no) as last_invoice 
                FROM orders 
                WHERE invoice_no LIKE ?";
        
        $conn = $this->db_conn();
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            return (int)($date_prefix . '001');
        }
        
        $pattern = $date_prefix . '%';
        $stmt->bind_param("s", $pattern);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row && $row['last_invoice']) {
            // Extract the sequence number and increment
            $last_invoice = (int)$row['last_invoice'];
            $sequence = ($last_invoice % 1000) + 1;
            return (int)($date_prefix . str_pad($sequence, 3, '0', STR_PAD_LEFT));
        }
        
        // First invoice of the day
        return (int)($date_prefix . '001');
    }
}
?>
