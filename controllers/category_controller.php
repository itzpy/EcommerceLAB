<?php

require_once '../classes/category_class.php';


function add_category_ctr($cat_name, $user_id)
{
    $category = new Category();
    $category_id = $category->addCategory($cat_name, $user_id);
    if ($category_id) {
        return $category_id;
    }
    return false;
}

function get_categories_ctr($user_id)
{
    $category = new Category();
    return $category->getCategories($user_id);
}

function get_category_ctr($cat_id, $user_id)
{
    $category = new Category();
    return $category->getCategory($cat_id, $user_id);
}

function update_category_ctr($cat_id, $cat_name, $user_id)
{
    $category = new Category();
    return $category->updateCategory($cat_id, $cat_name, $user_id);
}

function delete_category_ctr($cat_id, $user_id)
{
    $category = new Category();
    return $category->deleteCategory($cat_id, $user_id);
}