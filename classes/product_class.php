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
        parent::__construct();
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
        $sql = "INSERT INTO products (product_cat, product_brand, product_title, product_price, product_desc, product_image, product_keywords, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db_conn()->prepare($sql);
        $stmt->bind_param("iisdssi", $this->product_cat, $this->product_brand, $this->product_title, $this->product_price, $this->product_desc, $this->product_image, $this->product_keywords, $this->user_id);
        return $stmt->execute();
    }

    public function updateProduct() {
        $sql = "UPDATE products SET product_cat = ?, product_brand = ?, product_title = ?, product_price = ?, product_desc = ?, product_image = ?, product_keywords = ? WHERE product_id = ? AND user_id = ?";
        $stmt = $this->db_conn()->prepare($sql);
        $stmt->bind_param("iisdssiii", $this->product_cat, $this->product_brand, $this->product_title, $this->product_price, $this->product_desc, $this->product_image, $this->product_keywords, $this->product_id, $this->user_id);
        return $stmt->execute();
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
}
?>
