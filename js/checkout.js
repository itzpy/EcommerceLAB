$(document).ready(function() {
    // Load checkout summary
    loadCheckoutSummary();
});

/**
 * Load checkout summary
 */
function loadCheckoutSummary() {
    $.ajax({
        url: '../actions/get_cart_items.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayCheckoutSummary(response.items, response.total);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Empty Cart',
                    text: 'Your cart is empty'
                }).then(() => {
                    window.location.href = 'all_product.php';
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to load checkout summary'
            });
        }
    });
}

/**
 * Display checkout summary
 */
function displayCheckoutSummary(items, total) {
    if (items.length === 0) {
        Swal.fire({
            icon: 'info',
            title: 'Empty Cart',
            text: 'Your cart is empty'
        }).then(() => {
            window.location.href = 'all_product.php';
        });
        return;
    }
    
    let html = '<div class="table-responsive"><table class="table">';
    html += '<thead><tr>';
    html += '<th>Product</th>';
    html += '<th>Price</th>';
    html += '<th>Quantity</th>';
    html += '<th>Subtotal</th>';
    html += '</tr></thead><tbody>';
    
    items.forEach(function(item) {
        html += '<tr>';
        html += '<td>';
        html += '<div class="d-flex align-items-center">';
        if (item.product_image) {
            html += '<img src="../' + item.product_image + '" alt="' + item.product_title + '" style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px; border-radius: 5px;">';
        }
        html += '<span>' + item.product_title + '</span>';
        html += '</div>';
        html += '</td>';
        html += '<td>GHS ' + parseFloat(item.product_price).toFixed(2) + '</td>';
        html += '<td>' + item.qty + '</td>';
        html += '<td>GHS ' + parseFloat(item.subtotal).toFixed(2) + '</td>';
        html += '</tr>';
    });
    
    html += '<tr class="table-active">';
    html += '<td colspan="3" class="text-end"><strong>Total:</strong></td>';
    html += '<td><strong>GHS ' + parseFloat(total).toFixed(2) + '</strong></td>';
    html += '</tr>';
    
    html += '</tbody></table></div>';
    
    $('#checkoutSummary').html(html);
    $('#totalAmount').text('GHS ' + parseFloat(total).toFixed(2));
}

/**
 * Show payment modal
 */
function showPaymentModal() {
    Swal.fire({
        title: 'Simulate Payment',
        html: `
            <div class="text-start">
                <p><strong>Order Total:</strong> <span id="modalTotal"></span></p>
                <p class="text-muted">This is a simulated payment. Click "Yes, I've Paid" to complete your order.</p>
            </div>
        `,
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, I\'ve Paid',
        cancelButtonText: 'Cancel',
        didOpen: () => {
            // Set the total in the modal
            const total = $('#totalAmount').text();
            $('#modalTotal').text(total);
        }
    }).then((result) => {
        if (result.isConfirmed) {
            processCheckout();
        }
    });
}

/**
 * Process checkout
 */
function processCheckout() {
    // Show loading
    Swal.fire({
        title: 'Processing Order...',
        html: 'Please wait while we process your order',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    $.ajax({
        url: '../actions/process_checkout_action.php',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Success - show order confirmation
                Swal.fire({
                    icon: 'success',
                    title: 'Order Placed Successfully!',
                    html: `
                        <div class="text-start">
                            <p><strong>Invoice Number:</strong> ${response.invoice_no}</p>
                            <p><strong>Order ID:</strong> ${response.order_id}</p>
                            <p><strong>Total Amount:</strong> GHS ${response.total_amount}</p>
                            <p class="text-success mt-3">Thank you for your order!</p>
                        </div>
                    `,
                    confirmButtonText: 'Continue Shopping'
                }).then(() => {
                    window.location.href = 'all_product.php';
                });
            } else {
                // Failure
                Swal.fire({
                    icon: 'error',
                    title: 'Checkout Failed',
                    text: response.message
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while processing your order. Please try again.'
            });
        }
    });
}
