<?php

require_once '../settings/db_class.php';

/**
 * Brand class for handling brand operations
 */
class Brand extends db_connection
{
    private $brand_id;
    private $brand_name;
    private $cat_id;
    private $user_id;
    private $date_created;

    public function __construct($brand_id = null)
    {
        parent::db_connect();
        if ($brand_id) {
            $this->brand_id = $brand_id;
            $this->loadBrand();
        }
    }

    private function loadBrand($brand_id = null)
    {
        if ($brand_id) {
            $this->brand_id = $brand_id;
        }
        if (!$this->brand_id) {
            return false;
        }
        $stmt = $this->db->prepare("SELECT * FROM brands WHERE brand_id = ?");
        $stmt->bind_param("i", $this->brand_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if ($result) {
            $this->brand_name = $result['brand_name'];
            $this->cat_id = $result['cat_id'];
            $this->user_id = $result['user_id'];
            $this->date_created = isset($result['date_created']) ? $result['date_created'] : null;
        }
    }

    public function addBrand($brand_name, $cat_id, $user_id)
    {
        // Check if brand name already exists for this category and user
        if ($this->getBrandByName($brand_name, $cat_id, $user_id)) {
            return false; // Brand already exists in this category
        }
        
        $stmt = $this->db->prepare("INSERT INTO brands (brand_name, cat_id, user_id) VALUES (?, ?, ?)");
        $stmt->bind_param("sii", $brand_name, $cat_id, $user_id);
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    public function getBrandByName($brand_name, $cat_id, $user_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM brands WHERE brand_name = ? AND cat_id = ? AND user_id = ?");
        $stmt->bind_param("sii", $brand_name, $cat_id, $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getBrands($user_id)
    {
        $stmt = $this->db->prepare("SELECT b.brand_id, b.brand_name, b.cat_id, c.cat_name 
                                     FROM brands b 
                                     INNER JOIN categories c ON b.cat_id = c.cat_id 
                                     WHERE b.user_id = ? 
                                     ORDER BY c.cat_name ASC, b.brand_name ASC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $brands = [];
        while ($row = $result->fetch_assoc()) {
            $brands[] = $row;
        }
        return $brands;
    }

    public function getBrand($brand_id, $user_id)
    {
        $stmt = $this->db->prepare("SELECT b.brand_id, b.brand_name, b.cat_id, c.cat_name 
                                     FROM brands b 
                                     INNER JOIN categories c ON b.cat_id = c.cat_id 
                                     WHERE b.brand_id = ? AND b.user_id = ?");
        $stmt->bind_param("ii", $brand_id, $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateBrand($brand_id, $brand_name, $user_id)
    {
        // Get current brand to check category
        $current = $this->getBrand($brand_id, $user_id);
        if (!$current) {
            return false;
        }

        // Check if brand name already exists for this category and user (excluding current brand)
        $stmt = $this->db->prepare("SELECT brand_id FROM brands WHERE brand_name = ? AND cat_id = ? AND user_id = ? AND brand_id != ?");
        $stmt->bind_param("siii", $brand_name, $current['cat_id'], $user_id, $brand_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return false; // Brand name already exists in this category
        }

        // Update brand
        $stmt = $this->db->prepare("UPDATE brands SET brand_name = ? WHERE brand_id = ? AND user_id = ?");
        $stmt->bind_param("sii", $brand_name, $brand_id, $user_id);
        return $stmt->execute();
    }

    public function deleteBrand($brand_id, $user_id)
    {
        $stmt = $this->db->prepare("DELETE FROM brands WHERE brand_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $brand_id, $user_id);
        return $stmt->execute();
    }

    // Getter methods
    public function getBrandId()
    {
        return $this->brand_id;
    }

    public function getBrandName()
    {
        return $this->brand_name;
    }

    public function getCatId()
    {
        return $this->cat_id;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function getDateCreated()
    {
        return $this->date_created;
    }

    // Setter methods
    public function setBrandName($brand_name)
    {
        $this->brand_name = $brand_name;
    }

    public function setCatId($cat_id)
    {
        $this->cat_id = $cat_id;
    }

    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

}
