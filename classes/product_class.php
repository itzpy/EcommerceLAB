<?php
require_once(dirname(__FILE__).'/../settings/db_class.php');

class Product extends db_connection {
    private $product_id;
    private $product_cat;
    private $product_brand;
    private $product_title;
    private $product_price;
    private $product_desc;
    private $product_image;
    private $product_keywords;
    private $user_id;

    public function __construct($product_id = null) {
        // Initialize database connection (don't call parent constructor)
        // Call db_connect if needed for immediate connection
        if ($product_id !== null) {
            $this->product_id = $product_id;
            $this->loadProduct();
        }
    }

    private function loadProduct() {
        $sql = "SELECT * FROM products WHERE product_id = ?";
        $stmt = $this->db_conn()->prepare($sql);
        $stmt->bind_param("i", $this->product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $this->product_cat = $row['product_cat'];
            $this->product_brand = $row['product_brand'];
            $this->product_title = $row['product_title'];
            $this->product_price = $row['product_price'];
            $this->product_desc = $row['product_desc'];
            $this->product_image = $row['product_image'];
            $this->product_keywords = $row['product_keywords'];
            $this->user_id = $row['user_id'];
        }
    }

    public function setProductCat($product_cat) { $this->product_cat = $product_cat; }
    public function setProductBrand($product_brand) { $this->product_brand = $product_brand; }
    public function setProductTitle($product_title) { $this->product_title = $product_title; }
    public function setProductPrice($product_price) { $this->product_price = $product_price; }
    public function setProductDesc($product_desc) { $this->product_desc = $product_desc; }
    public function setProductImage($product_image) { $this->product_image = $product_image; }
    public function setProductKeywords($product_keywords) { $this->product_keywords = $product_keywords; }
    public function setUserId($user_id) { $this->user_id = $user_id; }

    public function getProductId() { return $this->product_id; }
    public function getProductCat() { return $this->product_cat; }
    public function getProductBrand() { return $this->product_brand; }
    public function getProductTitle() { return $this->product_title; }
    public function getProductPrice() { return $this->product_price; }
    public function getProductDesc() { return $this->product_desc; }
    public function getProductImage() { return $this->product_image; }
    public function getProductKeywords() { return $this->product_keywords; }
    public function getUserId() { return $this->user_id; }

    public function addProduct() {
        $conn = $this->db_conn();
        $sql = "INSERT INTO products (product_cat, product_brand, product_title, product_price, product_desc, product_image, product_keywords, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("iisdsssi", $this->product_cat, $this->product_brand, $this->product_title, $this->product_price, $this->product_desc, $this->product_image, $this->product_keywords, $this->user_id);
        $result = $stmt->execute();
        if (!$result) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        // Store the insert ID immediately after execution from the SAME connection
        $this->product_id = $conn->insert_id;
        return $result;
    }

    public function updateProduct() {
        $sql = "UPDATE products SET product_cat = ?, product_brand = ?, product_title = ?, product_price = ?, product_desc = ?, product_image = ?, product_keywords = ? WHERE product_id = ? AND user_id = ?";
        $stmt = $this->db_conn()->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->db_conn()->error);
        }
        $stmt->bind_param("iisdsssii", $this->product_cat, $this->product_brand, $this->product_title, $this->product_price, $this->product_desc, $this->product_image, $this->product_keywords, $this->product_id, $this->user_id);
        $result = $stmt->execute();
        if (!$result) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        return $result;
    }

    public function updateProductImage($user_id) {
        $sql = "UPDATE products SET product_image = ? WHERE product_id = ? AND user_id = ?";
        $stmt = $this->db_conn()->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->db_conn()->error);
        }
        $stmt->bind_param("sii", $this->product_image, $this->product_id, $user_id);
        $result = $stmt->execute();
        if (!$result) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        return $result;
    }

    public function getProducts($user_id) {
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p 
                JOIN categories c ON p.product_cat = c.cat_id 
                JOIN brands b ON p.product_brand = b.brand_id 
                WHERE p.user_id = ? 
                ORDER BY c.cat_name, b.brand_name, p.product_title";
        $stmt = $this->db_conn()->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getProductByTitle($product_title, $product_brand, $user_id) {
        $sql = "SELECT * FROM products WHERE product_title = ? AND product_brand = ? AND user_id = ?";
        $stmt = $this->db_conn()->prepare($sql);
        $stmt->bind_param("sii", $product_title, $product_brand, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getLastInsertedId() {
        return $this->db_conn()->insert_id;
    }

    // Customer-facing methods for product display and search
    
    /**
     * View all products with category and brand information
     * @return array Array of all products
     */
    public function view_all_products() {
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p 
                JOIN categories c ON p.product_cat = c.cat_id 
                JOIN brands b ON p.product_brand = b.brand_id 
                ORDER BY p.date_created DESC";
        $stmt = $this->db_conn()->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Search products by title, description, and keywords
     * @param string $query Search query
     * @return array Array of matching products
     */
    public function search_products($query) {
        $searchTerm = "%{$query}%";
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p 
                JOIN categories c ON p.product_cat = c.cat_id 
                JOIN brands b ON p.product_brand = b.brand_id 
                WHERE p.product_title LIKE ? 
                   OR p.product_desc LIKE ?
                   OR p.product_keywords LIKE ?
                ORDER BY p.date_created DESC";
        $stmt = $this->db_conn()->prepare($sql);
        $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Filter products by category
     * @param int $cat_id Category ID
     * @return array Array of products in category
     */
    public function filter_products_by_category($cat_id) {
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p 
                JOIN categories c ON p.product_cat = c.cat_id 
                JOIN brands b ON p.product_brand = b.brand_id 
                WHERE p.product_cat = ? 
                ORDER BY p.date_created DESC";
        $stmt = $this->db_conn()->prepare($sql);
        $stmt->bind_param("i", $cat_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Filter products by brand
     * @param int $brand_id Brand ID
     * @return array Array of products for brand
     */
    public function filter_products_by_brand($brand_id) {
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p 
                JOIN categories c ON p.product_cat = c.cat_id 
                JOIN brands b ON p.product_brand = b.brand_id 
                WHERE p.product_brand = ? 
                ORDER BY p.date_created DESC";
        $stmt = $this->db_conn()->prepare($sql);
        $stmt->bind_param("i", $brand_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * View single product details
     * @param int $id Product ID
     * @return array|null Product details or null
     */
    public function view_single_product($id) {
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p 
                JOIN categories c ON p.product_cat = c.cat_id 
                JOIN brands b ON p.product_brand = b.brand_id 
                WHERE p.product_id = ?";
        $stmt = $this->db_conn()->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * EXTRA CREDIT: Search products by keyword
     * @param string $keyword Keyword to search
     * @return array Array of matching products
     */
    public function search_by_keyword($keyword) {
        $searchTerm = "%{$keyword}%";
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p 
                JOIN categories c ON p.product_cat = c.cat_id 
                JOIN brands b ON p.product_brand = b.brand_id 
                WHERE p.product_keywords LIKE ? 
                ORDER BY p.date_created DESC";
        $stmt = $this->db_conn()->prepare($sql);
        $stmt->bind_param("s", $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * EXTRA CREDIT: Composite search with multiple filters
     * @param array $filters Associative array of filters (category, brand, max_price, keyword)
     * @return array Array of matching products
     */
    public function composite_search($filters) {
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p 
                JOIN categories c ON p.product_cat = c.cat_id 
                JOIN brands b ON p.product_brand = b.brand_id 
                WHERE 1=1";
        
        $params = [];
        $types = "";
        
        if (!empty($filters['category'])) {
            $sql .= " AND p.product_cat = ?";
            $params[] = $filters['category'];
            $types .= "i";
        }
        
        if (!empty($filters['brand'])) {
            $sql .= " AND p.product_brand = ?";
            $params[] = $filters['brand'];
            $types .= "i";
        }
        
        if (!empty($filters['max_price'])) {
            $sql .= " AND p.product_price <= ?";
            $params[] = $filters['max_price'];
            $types .= "d";
        }
        
        if (!empty($filters['keyword'])) {
            $sql .= " AND (p.product_keywords LIKE ? OR p.product_title LIKE ?)";
            $searchTerm = "%{$filters['keyword']}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= "ss";
        }
        
        $sql .= " ORDER BY p.date_created DESC";
        
        $stmt = $this->db_conn()->prepare($sql);
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>
