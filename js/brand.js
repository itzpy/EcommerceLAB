$(document).ready(function() {
    
    loadCategories();
    loadBrands();
    
    $('#addBrandForm').submit(function(e) {
        e.preventDefault();

        const brand_name = $('#brandName').val().trim();
        const cat_id = $('#categorySelect').val();
        
        // Validation patterns
        const namePattern = /^[a-zA-Z0-9\s\-_&.]{2,100}$/;
        
        // Field validation
        if (!cat_id || cat_id === '') {
            Swal.fire({
                icon: 'error',
                title: 'Missing Category',
                text: 'Please select a category!',
            });
            return;
        }
        
        if (!brand_name) {
            Swal.fire({
                icon: 'error',
                title: 'Missing Field',
                text: 'Please enter a brand name!',
            });
            return;
        }
        
        // Name validation
        if (!namePattern.test(brand_name)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Brand Name',
                text: 'Brand name must be 2-100 characters and contain only letters, numbers, spaces, hyphens, underscores, ampersands, and periods!',
            });
            return;
        }

        // Show loading indicator
        Swal.fire({
            title: 'Adding Brand...',
            text: 'Please wait while we add your brand.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '../actions/add_brand_action.php',
            type: 'POST',
            data: { 
                brand_name: brand_name,
                cat_id: cat_id
            },
            success: function(response) {
                Swal.close();
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                    });
                    $('#addBrandForm')[0].reset();
                    loadBrands();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: response.message,
                    });
                }
            },
            error: function() {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Server Error',
                    text: 'An error occurred! Please try again later.',
                });
            }
        });
    });
});

function loadCategories() {
    $.ajax({
        url: '../actions/fetch_category_action.php',
        type: 'POST',
        success: function(response) {
            if (response.success && response.data && response.data.length > 0) {
                let options = '<option value="">Select a category...</option>';
                response.data.forEach(category => {
                    options += `<option value="${category.cat_id}">${escapeHtml(category.cat_name)}</option>`;
                });
                $('#categorySelect').html(options);
            } else {
                $('#categorySelect').html('<option value="">No categories available</option>');
            }
        },
        error: function() {
            $('#categorySelect').html('<option value="">Error loading categories</option>');
        }
    });
}

function loadBrands() {
    $.ajax({
        url: '../actions/fetch_brand_action.php',
        type: 'POST',
        success: function(response) {
            displayBrands(response);
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to load brands',
            });
        }
    });
}

function displayBrands(data) {
    const container = $('#brandsContainer');
    
    if (data.success && data.data && data.data.length > 0) {
        // Group brands by category
        const brandsByCategory = {};
        data.data.forEach(brand => {
            if (!brandsByCategory[brand.cat_name]) {
                brandsByCategory[brand.cat_name] = [];
            }
            brandsByCategory[brand.cat_name].push(brand);
        });
        
        let html = '';
        
        // Display brands organized by category
        Object.keys(brandsByCategory).sort().forEach(catName => {
            html += `
                <div class="category-section">
                    <div class="category-header">
                        <h5 class="mb-0">
                            <i class="fas fa-tag me-2"></i>${escapeHtml(catName)}
                            <span class="badge bg-light text-dark ms-2">${brandsByCategory[catName].length}</span>
                        </h5>
                    </div>
                    <div class="row">
            `;
            
            brandsByCategory[catName].forEach(brand => {
                html += `
                    <div class="col-md-4 mb-3">
                        <div class="card brand-card">
                            <div class="card-body">
                                <div class="brand-display" id="display-${brand.brand_id}">
                                    <span class="category-badge">${escapeHtml(brand.cat_name)}</span>
                                    <h6 class="card-title">${escapeHtml(brand.brand_name)}</h6>
                                    <p class="text-muted small">ID: ${brand.brand_id}</p>
                                    <div class="d-flex justify-content-end">
                                        <button class="btn btn-sm btn-outline-primary me-2" onclick="editBrand(${brand.brand_id}, '${escapeHtml(brand.brand_name)}')">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteBrand(${brand.brand_id}, '${escapeHtml(brand.brand_name)}')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </div>
                                <div class="brand-edit edit-form" id="edit-${brand.brand_id}" style="display: none;">
                                    <div class="mb-3">
                                        <input type="text" class="form-control" id="editName-${brand.brand_id}" value="${escapeHtml(brand.brand_name)}" required>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <button type="button" class="btn btn-sm btn-secondary me-2" onclick="cancelEdit(${brand.brand_id})">Cancel</button>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="updateBrand(${brand.brand_id})">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += `
                    </div>
                </div>
            `;
        });
        
        container.html(html);
    } else {
        container.html(`
            <div class="text-center py-4">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No brands found</h5>
                <p class="text-muted">Create your first brand using the form above.</p>
            </div>
        `);
    }
}

function editBrand(brandId, brandName) {
    $(`#display-${brandId}`).hide();
    $(`#edit-${brandId}`).show();
    $(`#editName-${brandId}`).focus();
}

function cancelEdit(brandId) {
    $(`#display-${brandId}`).show();
    $(`#edit-${brandId}`).hide();
}

function updateBrand(brandId) {
    const brand_name = $(`#editName-${brandId}`).val().trim();
    
    // Validation patterns
    const namePattern = /^[a-zA-Z0-9\s\-_&.]{2,100}$/;
    
    if (!brand_name) {
        Swal.fire({
            icon: 'error',
            title: 'Missing Field',
            text: 'Please enter a brand name!',
        });
        return;
    }
    
    if (!namePattern.test(brand_name)) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Brand Name',
            text: 'Brand name must be 2-100 characters and contain only letters, numbers, spaces, hyphens, underscores, ampersands, and periods!',
        });
        return;
    }

    Swal.fire({
        title: 'Updating Brand...',
        text: 'Please wait while we update your brand.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '../actions/update_brand_action.php',
        type: 'POST',
        data: {
            brand_id: brandId,
            brand_name: brand_name
        },
        success: function(response) {
            Swal.close();
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message,
                });
                loadBrands();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: response.message,
                });
            }
        },
        error: function() {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Server Error',
                text: 'An error occurred! Please try again later.',
            });
        }
    });
}

function deleteBrand(brandId, brandName) {
    Swal.fire({
        title: 'Are you sure?',
        text: `Delete the brand "${brandName}"? This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Deleting Brand...',
                text: 'Please wait while we delete the brand.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '../actions/delete_brand_action.php',
                type: 'POST',
                data: { brand_id: brandId },
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: response.message,
                        });
                        loadBrands();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text: response.message,
                        });
                    }
                },
                error: function() {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Server Error',
                        text: 'An error occurred! Please try again later.',
                    });
                }
            });
        }
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
