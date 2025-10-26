<?php
require_once(dirname(__FILE__).'/../classes/product_class.php');

if (isset($_POST['action']) && $_POST['action'] === 'get_product') {
    header('Content-Type: application/json');
    $product_id = $_POST['product_id'] ?? '';
    if ($product_id) {
        $product = new Product($product_id);
        echo json_encode(array(
            'product_id' => $product->getProductId(),
            'product_cat' => $product->getProductCat(),
            'product_brand' => $product->getProductBrand(),
            'product_title' => $product->getProductTitle(),
            'product_price' => $product->getProductPrice(),
            'product_desc' => $product->getProductDesc(),
            'product_image' => $product->getProductImage(),
            'product_keywords' => $product->getProductKeywords(),
            'user_id' => $product->getUserId()
        ));
    }
    exit();
}

function add_product_ctr($product_cat, $product_brand, $product_title, $product_price, $product_desc, $product_image, $product_keywords, $user_id) {
    $product = new Product();
    $product->setProductCat($product_cat);
    $product->setProductBrand($product_brand);
    $product->setProductTitle($product_title);
    $product->setProductPrice($product_price);
    $product->setProductDesc($product_desc);
    $product->setProductImage($product_image);
    $product->setProductKeywords($product_keywords);
    $product->setUserId($user_id);
    return $product->addProduct() ? $product->getLastInsertedId() : false;
}

function update_product_ctr($product_id, $product_cat, $product_brand, $product_title, $product_price, $product_desc, $product_image, $product_keywords, $user_id) {
    $product = new Product($product_id);
    $product->setProductCat($product_cat);
    $product->setProductBrand($product_brand);
    $product->setProductTitle($product_title);
    $product->setProductPrice($product_price);
    $product->setProductDesc($product_desc);
    $product->setProductImage($product_image);
    $product->setProductKeywords($product_keywords);
    $product->setUserId($user_id);
    return $product->updateProduct();
}

function get_products_ctr($user_id) {
    $product = new Product();
    return $product->getProducts($user_id);
}

function get_product_by_id_ctr($product_id) {
    $product = new Product($product_id);
    return array(
        'product_id' => $product->getProductId(),
        'product_cat' => $product->getProductCat(),
        'product_brand' => $product->getProductBrand(),
        'product_title' => $product->getProductTitle(),
        'product_price' => $product->getProductPrice(),
        'product_desc' => $product->getProductDesc(),
        'product_image' => $product->getProductImage(),
        'product_keywords' => $product->getProductKeywords(),
        'user_id' => $product->getUserId()
    );
}

function get_product_by_title_ctr($product_title, $product_brand, $user_id) {
    $product = new Product();
    return $product->getProductByTitle($product_title, $product_brand, $user_id);
}
?>
