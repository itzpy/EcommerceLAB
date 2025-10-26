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
