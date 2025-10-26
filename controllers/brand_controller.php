<?php

require_once '../classes/brand_class.php';


function add_brand_ctr($brand_name, $cat_id, $user_id)
{
    $brand = new Brand();
    $brand_id = $brand->addBrand($brand_name, $cat_id, $user_id);
    if ($brand_id) {
        return $brand_id;
    }
    return false;
}

function get_brands_ctr($user_id)
{
    $brand = new Brand();
    return $brand->getBrands($user_id);
}

function get_brand_ctr($brand_id, $user_id)
{
    $brand = new Brand();
    return $brand->getBrand($brand_id, $user_id);
}

function update_brand_ctr($brand_id, $brand_name, $user_id)
{
    $brand = new Brand();
    return $brand->updateBrand($brand_id, $brand_name, $user_id);
}

function delete_brand_ctr($brand_id, $user_id)
{
    $brand = new Brand();
    return $brand->deleteBrand($brand_id, $user_id);
}

// Customer-facing function to get all brands (not user-specific)
function get_all_brands_ctr()
{
    $brand = new Brand();
    $sql = "SELECT DISTINCT b.brand_id, b.brand_name, b.cat_id, c.cat_name 
            FROM brands b 
            JOIN categories c ON b.cat_id = c.cat_id 
            ORDER BY b.brand_name";
    $stmt = $brand->db_conn()->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}
