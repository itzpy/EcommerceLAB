$(document).ready(function() {
    // Load cart on page load
    loadCart();
    
    // Update cart count in navigation
    updateCartCount();
});

/**
 * Load and display cart items
 */
function loadCart() {
    $.ajax({
        url: '../actions/get_cart_items.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayCart(response.items, response.total);
            } else {
                $('#cartItems').html('<p class="text-center text-muted">Your cart is empty</p>');
                $('#cartSummary').hide();
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to load cart items'
            });
        }
    });
}

/**
 * Display cart items in the UI
 */
function displayCart(items, total) {
    if (items.length === 0) {
        $('#cartItems').html('<p class="text-center text-muted">Your cart is empty</p>');
        $('#cartSummary').hide();
        return;
    }
    
    let html = '<div class="table-responsive"><table class="table table-hover">';
    html += '<thead><tr>';
    html += '<th>Product</th>';
    html += '<th>Price</th>';
    html += '<th>Quantity</th>';
    html += '<th>Subtotal</th>';
    html += '<th>Action</th>';
    html += '</tr></thead><tbody>';
    
    items.forEach(function(item) {
        html += '<tr data-product-id="' + item.p_id + '">';
        html += '<td>';
        html += '<div class="d-flex align-items-center">';
        if (item.product_image) {
            html += '<img src="../' + item.product_image + '" alt="' + item.product_title + '" style="width: 60px; height: 60px; object-fit: cover; margin-right: 10px; border-radius: 5px;">';
        }
        html += '<span>' + item.product_title + '</span>';
        html += '</div>';
        html += '</td>';
        html += '<td>GHS ' + parseFloat(item.product_price).toFixed(2) + '</td>';
        html += '<td>';
        html += '<input type="number" class="form-control quantity-input" style="width: 80px;" value="' + item.qty + '" min="1" data-product-id="' + item.p_id + '">';
        html += '</td>';
        html += '<td>GHS ' + parseFloat(item.subtotal).toFixed(2) + '</td>';
        html += '<td>';
        html += '<button class="btn btn-sm btn-danger remove-item" data-product-id="' + item.p_id + '">';
        html += '<i class="fas fa-trash"></i>';
        html += '</button>';
        html += '</td>';
        html += '</tr>';
    });
    
    html += '</tbody></table></div>';
    
    $('#cartItems').html(html);
    
    // Update summary
    $('#cartTotal').text('GHS ' + parseFloat(total).toFixed(2));
    $('#cartSummary').show();
    
    // Attach event listeners
    attachCartEventListeners();
}

/**
 * Attach event listeners to cart elements
 */
function attachCartEventListeners() {
    // Quantity change
    $('.quantity-input').on('change', function() {
        const productId = $(this).data('product-id');
        const newQty = parseInt($(this).val());
        
        if (newQty < 1) {
            $(this).val(1);
            return;
        }
        
        updateQuantity(productId, newQty);
    });
    
    // Remove item
    $('.remove-item').on('click', function() {
        const productId = $(this).data('product-id');
        removeFromCart(productId);
    });
}

/**
 * Update item quantity
 */
function updateQuantity(productId, qty) {
    $.ajax({
        url: '../actions/update_quantity_action.php',
        type: 'POST',
        data: {
            product_id: productId,
            qty: qty
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                loadCart(); // Reload cart to update totals
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to update quantity'
            });
        }
    });
}

/**
 * Remove item from cart
 */
function removeFromCart(productId) {
    Swal.fire({
        title: 'Remove Item?',
        text: 'Are you sure you want to remove this item from your cart?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, remove it'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '../actions/remove_from_cart_action.php',
                type: 'POST',
                data: { product_id: productId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Removed',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        loadCart();
                        updateCartCount();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to remove item'
                    });
                }
            });
        }
    });
}

/**
 * Empty entire cart
 */
function emptyCart() {
    Swal.fire({
        title: 'Empty Cart?',
        text: 'Are you sure you want to remove all items from your cart?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, empty it'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '../actions/empty_cart_action.php',
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Cart Emptied',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        loadCart();
                        updateCartCount();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to empty cart'
                    });
                }
            });
        }
    });
}

/**
 * Update cart count badge in navigation
 */
function updateCartCount() {
    $.ajax({
        url: '../actions/get_cart_count.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#cartCount').text(response.count);
                if (response.count > 0) {
                    $('#cartCount').show();
                } else {
                    $('#cartCount').hide();
                }
            }
        }
    });
}

/**
 * Proceed to checkout
 */
function proceedToCheckout() {
    window.location.href = 'checkout.php';
}
