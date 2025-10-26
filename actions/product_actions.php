<?php
session_start();
header('Content-Type: application/json');
require_once(dirname(__FILE__).'/../controllers/product_controller.php');
require_once(dirname(__FILE__).'/../controllers/category_controller.php');
require_once(dirname(__FILE__).'/../controllers/brand_controller.php');

$response = array();

// Get the action parameter
$action = $_GET['action'] ?? $_POST['action'] ?? '';

if (empty($action)) {
    $response['success'] = false;
    $response['message'] = 'No action specified.';
    echo json_encode($response);
    exit();
}

// Handle different actions
switch ($action) {
    
    case 'fetch_all':
        // Fetch all products
        try {
            $products = view_all_products_ctr();
            $response['success'] = true;
            $response['data'] = $products;
        } catch (Exception $e) {
            $response['success'] = false;
            $response['message'] = 'Error fetching products: ' . $e->getMessage();
            $response['data'] = [];
        }
        break;
    
    case 'search':
        // Search products by title
        $query = $_GET['query'] ?? $_POST['query'] ?? '';
        if (empty($query)) {
            $response['success'] = false;
            $response['message'] = 'Search query is required.';
        } else {
            try {
                $products = search_products_ctr($query);
                $response['success'] = true;
                $response['data'] = $products;
                $response['query'] = $query;
            } catch (Exception $e) {
                $response['success'] = false;
                $response['message'] = 'Error searching products: ' . $e->getMessage();
                $response['data'] = [];
            }
        }
        break;
    
    case 'filter_by_category':
        // Filter products by category
        $cat_id = $_GET['cat_id'] ?? $_POST['cat_id'] ?? '';
        if (empty($cat_id)) {
            $response['success'] = false;
            $response['message'] = 'Category ID is required.';
        } else {
            try {
                $products = filter_products_by_category_ctr($cat_id);
                $response['success'] = true;
                $response['data'] = $products;
            } catch (Exception $e) {
                $response['success'] = false;
                $response['message'] = 'Error filtering by category: ' . $e->getMessage();
                $response['data'] = [];
            }
        }
        break;
    
    case 'filter_by_brand':
        // Filter products by brand
        $brand_id = $_GET['brand_id'] ?? $_POST['brand_id'] ?? '';
        if (empty($brand_id)) {
            $response['success'] = false;
            $response['message'] = 'Brand ID is required.';
        } else {
            try {
                $products = filter_products_by_brand_ctr($brand_id);
                $response['success'] = true;
                $response['data'] = $products;
            } catch (Exception $e) {
                $response['success'] = false;
                $response['message'] = 'Error filtering by brand: ' . $e->getMessage();
                $response['data'] = [];
            }
        }
        break;
    
    case 'get_single_product':
        // Get single product details
        $product_id = $_GET['product_id'] ?? $_POST['product_id'] ?? '';
        if (empty($product_id)) {
            $response['success'] = false;
            $response['message'] = 'Product ID is required.';
        } else {
            try {
                $product = view_single_product_ctr($product_id);
                if ($product) {
                    $response['success'] = true;
                    $response['data'] = $product;
                } else {
                    $response['success'] = false;
                    $response['message'] = 'Product not found.';
                }
            } catch (Exception $e) {
                $response['success'] = false;
                $response['message'] = 'Error fetching product: ' . $e->getMessage();
            }
        }
        break;
    
    case 'search_by_keyword':
        // EXTRA CREDIT: Search by keyword
        $keyword = $_GET['keyword'] ?? $_POST['keyword'] ?? '';
        if (empty($keyword)) {
            $response['success'] = false;
            $response['message'] = 'Keyword is required.';
        } else {
            try {
                $products = search_by_keyword_ctr($keyword);
                $response['success'] = true;
                $response['data'] = $products;
                $response['keyword'] = $keyword;
            } catch (Exception $e) {
                $response['success'] = false;
                $response['message'] = 'Error searching by keyword: ' . $e->getMessage();
                $response['data'] = [];
            }
        }
        break;
    
    case 'composite_search':
        // EXTRA CREDIT: Composite search with multiple filters
        $filters = array(
            'category' => $_GET['category'] ?? $_POST['category'] ?? '',
            'brand' => $_GET['brand'] ?? $_POST['brand'] ?? '',
            'max_price' => $_GET['max_price'] ?? $_POST['max_price'] ?? '',
            'keyword' => $_GET['keyword'] ?? $_POST['keyword'] ?? ''
        );
        
        // Remove empty filters
        $filters = array_filter($filters);
        
        if (empty($filters)) {
            // No filters, return all products
            $products = view_all_products_ctr();
        } else {
            try {
                $products = composite_search_ctr($filters);
                $response['filters_applied'] = $filters;
            } catch (Exception $e) {
                $response['success'] = false;
                $response['message'] = 'Error in composite search: ' . $e->getMessage();
                $response['data'] = [];
                echo json_encode($response);
                exit();
            }
        }
        
        $response['success'] = true;
        $response['data'] = $products;
        break;
    
    case 'get_all_categories':
        // Get all categories for filter dropdown
        try {
            $categories = get_all_categories_ctr();
            $response['success'] = true;
            $response['data'] = $categories;
        } catch (Exception $e) {
            $response['success'] = false;
            $response['message'] = 'Error fetching categories: ' . $e->getMessage();
            $response['data'] = [];
        }
        break;
    
    case 'get_all_brands':
        // Get all brands for filter dropdown
        try {
            $brands = get_all_brands_ctr();
            $response['success'] = true;
            $response['data'] = $brands;
        } catch (Exception $e) {
            $response['success'] = false;
            $response['message'] = 'Error fetching brands: ' . $e->getMessage();
            $response['data'] = [];
        }
        break;
    
    default:
        $response['success'] = false;
        $response['message'] = 'Invalid action: ' . $action;
        break;
}

echo json_encode($response);
?>
