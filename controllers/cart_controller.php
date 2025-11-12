<?php
require_once(dirname(__FILE__).'/../classes/cart_class.php');

/**
 * Add product to cart
 * @param int $product_id
 * @param int $customer_id
 * @param int $qty
 * @param string $ip_address
 * @return bool
 */
function add_to_cart_ctr($product_id, $customer_id, $qty, $ip_address) {
    $cart = new Cart();
    return $cart->addToCart($product_id, $customer_id, $qty, $ip_address);
}

/**
 * Update cart item quantity
 * @param int $product_id
 * @param int $customer_id
 * @param int $qty
 * @return bool
 */
function update_cart_item_ctr($product_id, $customer_id, $qty) {
    $cart = new Cart();
    return $cart->updateCartQty($product_id, $customer_id, $qty);
}

/**
 * Remove product from cart
 * @param int $product_id
 * @param int $customer_id
 * @return bool
 */
function remove_from_cart_ctr($product_id, $customer_id) {
    $cart = new Cart();
    return $cart->removeFromCart($product_id, $customer_id);
}

/**
 * Get all cart items for a user
 * @param int $customer_id
 * @return array|false
 */
function get_user_cart_ctr($customer_id) {
    $cart = new Cart();
    return $cart->getUserCart($customer_id);
}

/**
 * Empty user's cart
 * @param int $customer_id
 * @return bool
 */
function empty_cart_ctr($customer_id) {
    $cart = new Cart();
    return $cart->emptyCart($customer_id);
}

/**
 * Check if product exists in cart
 * @param int $product_id
 * @param int $customer_id
 * @return bool
 */
function check_product_in_cart_ctr($product_id, $customer_id) {
    $cart = new Cart();
    return $cart->checkProductInCart($product_id, $customer_id);
}

/**
 * Get cart item count
 * @param int $customer_id
 * @return int
 */
function get_cart_count_ctr($customer_id) {
    $cart = new Cart();
    return $cart->getCartCount($customer_id);
}
?>
