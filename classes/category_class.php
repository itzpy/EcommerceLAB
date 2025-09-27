<?php

require_once '../settings/db_class.php';

/**
 * Category class for handling category operations
 */
class Category extends db_connection
{
    private $cat_id;
    private $cat_name;
    private $user_id;
    private $date_created;

    public function __construct($cat_id = null)
    {
        parent::db_connect();
        if ($cat_id) {
            $this->cat_id = $cat_id;
            $this->loadCategory();
        }
    }

    private function loadCategory($cat_id = null)
    {
        if ($cat_id) {
            $this->cat_id = $cat_id;
        }
        if (!$this->cat_id) {
            return false;
        }
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE cat_id = ?");
        $stmt->bind_param("i", $this->cat_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if ($result) {
            $this->cat_name = $result['cat_name'];
            $this->user_id = $result['user_id'];
            $this->date_created = isset($result['date_created']) ? $result['date_created'] : null;
        }
    }

    public function addCategory($cat_name, $user_id)
    {
        // Check if category name already exists for this user
        if ($this->getCategoryByName($cat_name, $user_id)) {
            return false; // Category already exists
        }
        
        $stmt = $this->db->prepare("INSERT INTO categories (cat_name, user_id) VALUES (?, ?)");
        $stmt->bind_param("si", $cat_name, $user_id);
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    public function getCategoryByName($cat_name, $user_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE cat_name = ? AND user_id = ?");
        $stmt->bind_param("si", $cat_name, $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getCategories($user_id)
    {
        $stmt = $this->db->prepare("SELECT cat_id, cat_name FROM categories WHERE user_id = ? ORDER BY cat_name ASC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
        return $categories;
    }

    public function getCategory($cat_id, $user_id)
    {
        $stmt = $this->db->prepare("SELECT cat_id, cat_name FROM categories WHERE cat_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $cat_id, $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateCategory($cat_id, $cat_name, $user_id)
    {
        // Check if category name already exists for this user (excluding current category)
        $stmt = $this->db->prepare("SELECT cat_id FROM categories WHERE cat_name = ? AND user_id = ? AND cat_id != ?");
        $stmt->bind_param("sii", $cat_name, $user_id, $cat_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return false; // Category name already exists
        }

        // Update category
        $stmt = $this->db->prepare("UPDATE categories SET cat_name = ? WHERE cat_id = ? AND user_id = ?");
        $stmt->bind_param("sii", $cat_name, $cat_id, $user_id);
        return $stmt->execute();
    }

    public function deleteCategory($cat_id, $user_id)
    {
        $stmt = $this->db->prepare("DELETE FROM categories WHERE cat_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $cat_id, $user_id);
        return $stmt->execute();
    }

    // Getter methods
    public function getCatId()
    {
        return $this->cat_id;
    }

    public function getCatName()
    {
        return $this->cat_name;
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
    public function setCatName($cat_name)
    {
        $this->cat_name = $cat_name;
    }

    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

}
?>