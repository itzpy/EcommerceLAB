$(document).ready(function() {
    let currentPage = 1;
    const itemsPerPage = 12;
    let allProducts = [];
    let filteredProducts = [];
    
    // Load initial data
    loadCategories();
    loadBrands();
    loadProducts();
    updateCartCount();
    
    // Event listeners
    $('#categoryFilter').change(function() {
        applyFilters();
    });
    
    $('#brandFilter').change(function() {
        applyFilters();
    });
    
    $('#clearFilters').click(function() {
        $('#categoryFilter').val('');
        $('#brandFilter').val('');
        applyFilters();
    });
    
    // Load all categories for filter dropdown
    function loadCategories() {
        $.ajax({
            url: '../actions/product_actions.php',
            method: 'GET',
            data: { action: 'get_all_categories' },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data) {
                    let options = '<option value="">All Categories</option>';
                    response.data.forEach(function(category) {
                        options += `<option value="${category.cat_id}">${category.cat_name}</option>`;
                    });
                    $('#categoryFilter').html(options);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading categories:', error);
            }
        });
    }
    
    // Load all brands for filter dropdown
    function loadBrands() {
        $.ajax({
            url: '../actions/product_actions.php',
            method: 'GET',
            data: { action: 'get_all_brands' },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data) {
                    let options = '<option value="">All Brands</option>';
                    response.data.forEach(function(brand) {
                        options += `<option value="${brand.brand_id}">${brand.brand_name}</option>`;
                    });
                    $('#brandFilter').html(options);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading brands:', error);
            }
        });
    }
    
    // Load all products
    function loadProducts() {
        $.ajax({
            url: '../actions/product_actions.php',
            method: 'GET',
            data: { action: 'fetch_all' },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    allProducts = response.data;
                    filteredProducts = allProducts;
                    currentPage = 1;
                    displayProducts();
                } else {
                    showNoProducts('Failed to load products. Please try again.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading products:', error);
                showNoProducts('Failed to load products. Please try again.');
            }
        });
    }
    
    // Apply filters
    function applyFilters() {
        const categoryId = $('#categoryFilter').val();
        const brandId = $('#brandFilter').val();
        
        if (!categoryId && !brandId) {
            // No filters, show all products
            filteredProducts = allProducts;
            currentPage = 1;
            displayProducts();
            return;
        }
        
        // Filter products
        filteredProducts = allProducts.filter(function(product) {
            let matchesCategory = !categoryId || product.product_cat == categoryId;
            let matchesBrand = !brandId || product.product_brand == brandId;
            return matchesCategory && matchesBrand;
        });
        
        currentPage = 1;
        displayProducts();
    }
    
    // Display products with pagination
    function displayProducts() {
        if (filteredProducts.length === 0) {
            showNoProducts('No products found matching your criteria.');
            return;
        }
        
        // Update results count
        const totalCount = filteredProducts.length;
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = Math.min(startIndex + itemsPerPage, totalCount);
        
        $('#resultsText').text(`Showing ${startIndex + 1} - ${endIndex} of ${totalCount} products`);
        
        // Get products for current page
        const productsToDisplay = filteredProducts.slice(startIndex, endIndex);
        
        // Generate HTML
        let html = '';
        productsToDisplay.forEach(function(product) {
            const imageSrc = product.product_image 
                ? `../${product.product_image}` 
                : 'https://via.placeholder.com/300x300?text=No+Image';
            
            html += `
                <div class="col-md-4 mb-4">
                    <div class="product-card">
                        <div class="product-image-container">
                            <img src="${imageSrc}" class="product-image" alt="${escapeHtml(product.product_title)}">
                            <div class="product-badge">ID: ${product.product_id}</div>
                        </div>
                        <div class="product-body">
                            <div class="product-category">${escapeHtml(product.cat_name || 'Uncategorized')}</div>
                            <div class="product-brand"><i class="fas fa-tag me-1"></i>${escapeHtml(product.brand_name || 'No Brand')}</div>
                            <h5 class="product-title">${escapeHtml(product.product_title)}</h5>
                            <div class="product-price">$${parseFloat(product.product_price).toFixed(2)}</div>
                            <a href="single_product.php?id=${product.product_id}" class="btn btn-view-details">
                                <i class="fas fa-eye me-2"></i>View Details
                            </a>
                            <button class="btn btn-add-cart" onclick="addToCart(${product.product_id})">
                                <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
        
        $('#productsContainer').html(html);
        
        // Generate pagination
        generatePagination(totalCount);
    }
    
    // Generate pagination
    function generatePagination(totalCount) {
        const totalPages = Math.ceil(totalCount / itemsPerPage);
        
        if (totalPages <= 1) {
            $('#paginationContainer').empty();
            return;
        }
        
        let html = '';
        
        // Previous button
        html += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage - 1}">Previous</a>
            </li>
        `;
        
        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                html += `
                    <li class="page-item ${i === currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>
                `;
            } else if (i === currentPage - 3 || i === currentPage + 3) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }
        
        // Next button
        html += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage + 1}">Next</a>
            </li>
        `;
        
        $('#paginationContainer').html(html);
        
        // Attach click events
        $('.page-link').click(function(e) {
            e.preventDefault();
            const page = parseInt($(this).data('page'));
            if (page && page !== currentPage && page >= 1 && page <= totalPages) {
                currentPage = page;
                displayProducts();
                // Scroll to top of products
                $('html, body').animate({
                    scrollTop: $('#productsContainer').offset().top - 100
                }, 500);
            }
        });
    }
    
    // Show no products message
    function showNoProducts(message) {
        $('#resultsText').text('0 products found');
        $('#productsContainer').html(`
            <div class="col-12">
                <div class="no-products">
                    <i class="fas fa-box-open"></i>
                    <h4>${message}</h4>
                    <p>Try adjusting your filters or browse all products.</p>
                </div>
            </div>
        `);
        $('#paginationContainer').empty();
    }
    
    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
    }
});

// Global function for add to cart
function addToCart(productId, qty = 1) {
    $.ajax({
        url: '../actions/add_to_cart_action.php',
        type: 'POST',
        data: {
            product_id: productId,
            qty: qty
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Added to Cart!',
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false,
                    confirmButtonColor: '#D19C97'
                });
                // Update cart count if function exists
                if (typeof updateCartCount === 'function') {
                    updateCartCount();
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message,
                    confirmButtonColor: '#D19C97'
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to add product to cart. Please try again.',
                confirmButtonColor: '#D19C97'
            });
        }
    });
}

// Update cart count badge
function updateCartCount() {
    $.ajax({
        url: '../actions/get_cart_count.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success && response.count > 0) {
                $('#cartCount').text(response.count).show();
            } else {
                $('#cartCount').hide();
            }
        }
    });
}

