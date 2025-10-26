$(document).ready(function() {
    let currentPage = 1;
    const itemsPerPage = 12;
    let allResults = [];
    let filteredResults = [];
    let currentQuery = '';
    
    // Get search query from URL
    const urlParams = new URLSearchParams(window.location.search);
    currentQuery = urlParams.get('q') || '';
    
    // Load initial data
    loadCategories();
    loadBrands();
    
    if (currentQuery) {
        performSearch(currentQuery);
    }
    
    // Event listeners
    $('#searchForm').submit(function(e) {
        e.preventDefault();
        const query = $('#searchInput').val().trim();
        if (query) {
            window.location.href = `product_search_result.php?q=${encodeURIComponent(query)}`;
        }
    });
    
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
    
    // Perform search
    function performSearch(query) {
        $('#resultsText').text('Searching...');
        
        $.ajax({
            url: '../actions/product_actions.php',
            method: 'GET',
            data: { 
                action: 'search',
                query: query
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    allResults = response.data;
                    filteredResults = allResults;
                    currentPage = 1;
                    displayResults();
                } else {
                    showNoResults('Search failed. Please try again.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Search error:', error);
                showNoResults('Search failed. Please try again.');
            }
        });
    }
    
    // Apply filters to search results
    function applyFilters() {
        const categoryId = $('#categoryFilter').val();
        const brandId = $('#brandFilter').val();
        
        if (!categoryId && !brandId) {
            // No filters, show all results
            filteredResults = allResults;
            currentPage = 1;
            displayResults();
            return;
        }
        
        // Filter results
        filteredResults = allResults.filter(function(product) {
            let matchesCategory = !categoryId || product.product_cat == categoryId;
            let matchesBrand = !brandId || product.product_brand == brandId;
            return matchesCategory && matchesBrand;
        });
        
        currentPage = 1;
        displayResults();
    }
    
    // Display search results with pagination
    function displayResults() {
        if (filteredResults.length === 0) {
            showNoResults(`No results found for "${currentQuery}"`);
            return;
        }
        
        // Update results count
        const totalCount = filteredResults.length;
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = Math.min(startIndex + itemsPerPage, totalCount);
        
        $('#resultsText').html(`Found <strong>${totalCount}</strong> result(s) - Showing ${startIndex + 1} - ${endIndex}`);
        
        // Get results for current page
        const resultsToDisplay = filteredResults.slice(startIndex, endIndex);
        
        // Generate HTML
        let html = '';
        resultsToDisplay.forEach(function(product) {
            const imageSrc = product.product_image 
                ? `../${product.product_image}` 
                : 'https://via.placeholder.com/300x300?text=No+Image';
            
            // Highlight search term in title
            const highlightedTitle = highlightSearchTerm(product.product_title, currentQuery);
            
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
                            <h5 class="product-title">${highlightedTitle}</h5>
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
                displayResults();
                // Scroll to top of results
                $('html, body').animate({
                    scrollTop: $('#productsContainer').offset().top - 100
                }, 500);
            }
        });
    }
    
    // Show no results message
    function showNoResults(message) {
        $('#resultsText').text('0 results found');
        $('#productsContainer').html(`
            <div class="col-12">
                <div class="no-results">
                    <i class="fas fa-search"></i>
                    <h4>${message}</h4>
                    <p>Try different search terms or browse all products.</p>
                    <a href="all_product.php" class="btn btn-add-cart mt-3">
                        <i class="fas fa-shopping-bag me-2"></i>Browse All Products
                    </a>
                </div>
            </div>
        `);
        $('#paginationContainer').empty();
    }
    
    // Highlight search term in text
    function highlightSearchTerm(text, term) {
        if (!term || term.length === 0) {
            return escapeHtml(text);
        }
        
        const escapedText = escapeHtml(text);
        const escapedTerm = escapeHtml(term);
        const regex = new RegExp(`(${escapedTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
        
        return escapedText.replace(regex, '<mark style="background: #FFD700; padding: 2px 4px; border-radius: 3px;">$1</mark>');
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

// Global function for add to cart (placeholder)
function addToCart(productId) {
    // Placeholder for add to cart functionality
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'info',
            title: 'Coming Soon',
            text: 'Add to Cart functionality will be implemented in a future update.',
            confirmButtonColor: '#D19C97'
        });
    } else {
        alert('Add to Cart functionality will be implemented in a future update.');
    }
}
