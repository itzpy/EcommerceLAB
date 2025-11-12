<?php
require_once(dirname(__FILE__).'/../settings/db_class.php');

/**
 * Cart class to handle all cart operations
 */
class Cart extends db_connection {
    
    /**
     * Add product to cart or update quantity if already exists
     * @param int $product_id
     * @param int $customer_id
     * @param int $qty
     * @param string $ip_address
     * @return bool
     */
    public function addToCart($product_id, $customer_id, $qty, $ip_address) {
        // First check if product already exists in cart
        $check_sql = "SELECT qty FROM cart WHERE p_id = ? AND c_id = ?";
        $conn = $this->db_conn();
        $stmt = $conn->prepare($check_sql);
        
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            return false;
        }
        
        $stmt->bind_param("ii", $product_id, $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // If product exists, update quantity
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $new_qty = $row['qty'] + $qty;
            
            $update_sql = "UPDATE cart SET qty = ? WHERE p_id = ? AND c_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            
            if (!$update_stmt) {
                error_log("Update prepare failed: " . $conn->error);
                return false;
            }
            
            $update_stmt->bind_param("iii", $new_qty, $product_id, $customer_id);
            return $update_stmt->execute();
        }
        
        // Product doesn't exist, insert new row
        $insert_sql = "INSERT INTO cart (p_id, c_id, qty, ip_add) VALUES (?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        
        if (!$insert_stmt) {
            error_log("Insert prepare failed: " . $conn->error);
            return false;
        }
        
        $insert_stmt->bind_param("iiis", $product_id, $customer_id, $qty, $ip_address);
        return $insert_stmt->execute();
    }
    
    /**
     * Update quantity of cart item
     * @param int $product_id
     * @param int $customer_id
     * @param int $qty
     * @return bool
     */
    public function updateCartQty($product_id, $customer_id, $qty) {
        $sql = "UPDATE cart SET qty = ? WHERE p_id = ? AND c_id = ?";
        $conn = $this->db_conn();
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            return false;
        }
        
        $stmt->bind_param("iii", $qty, $product_id, $customer_id);
        return $stmt->execute();
    }
    
    /**
     * Remove product from cart
     * @param int $product_id
     * @param int $customer_id
     * @return bool
     */
    public function removeFromCart($product_id, $customer_id) {
        $sql = "DELETE FROM cart WHERE p_id = ? AND c_id = ?";
        $conn = $this->db_conn();
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            return false;
        }
        
        $stmt->bind_param("ii", $product_id, $customer_id);
        return $stmt->execute();
    }
    
    /**
     * Get all cart items for a user with product details
     * @param int $customer_id
     * @return array|false
     */
    public function getUserCart($customer_id) {
        $sql = "SELECT c.p_id, c.qty, c.ip_add,
                       p.product_title, p.product_price, p.product_image,
                       p.product_cat, p.product_brand,
                       (p.product_price * c.qty) as subtotal
                FROM cart c
                JOIN products p ON c.p_id = p.product_id
                WHERE c.c_id = ?
                ORDER BY c.p_id DESC";
        
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
     * Empty entire cart for a user
     * @param int $customer_id
     * @return bool
     */
    public function emptyCart($customer_id) {
        $sql = "DELETE FROM cart WHERE c_id = ?";
        $conn = $this->db_conn();
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            return false;
        }
        
        $stmt->bind_param("i", $customer_id);
        return $stmt->execute();
    }
    
    /**
     * Check if product exists in cart
     * @param int $product_id
     * @param int $customer_id
     * @return bool
     */
    public function checkProductInCart($product_id, $customer_id) {
        $sql = "SELECT * FROM cart WHERE p_id = ? AND c_id = ?";
        $conn = $this->db_conn();
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            return false;
        }
        
        $stmt->bind_param("ii", $product_id, $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->num_rows > 0;
    }
    
    /**
     * Get cart item count for a user
     * @param int $customer_id
     * @return int
     */
    public function getCartCount($customer_id) {
        $sql = "SELECT SUM(qty) as total FROM cart WHERE c_id = ?";
        $conn = $this->db_conn();
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            return 0;
        }
        
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['total'] ? (int)$row['total'] : 0;
    }
}
?>
