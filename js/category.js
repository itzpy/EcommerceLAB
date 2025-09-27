$(document).ready(function() {
    
    loadCategories();
    
    $('#addCategoryForm').submit(function(e) {
        e.preventDefault();

        const cat_name = $('#categoryName').val().trim();
        
        // Validation patterns
        const namePattern = /^[a-zA-Z0-9\s\-_]{2,100}$/;
        
        // Field validation
        if (!cat_name) {
            Swal.fire({
                icon: 'error',
                title: 'Missing Field',
                text: 'Please enter a category name!',
            });
            return;
        }
        
        // Name validation
        if (!namePattern.test(cat_name)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Category Name',
                text: 'Category name must be 2-100 characters and contain only letters, numbers, spaces, hyphens, and underscores!',
            });
            return;
        }

        // Show loading indicator
        Swal.fire({
            title: 'Adding Category...',
            text: 'Please wait while we add your category.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '../actions/add_category_action.php',
            type: 'POST',
            data: { cat_name: cat_name },
            success: function(response) {
                Swal.close();
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                    });
                    $('#addCategoryForm')[0].reset();
                    loadCategories();
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
            displayCategories(response);
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to load categories',
            });
        }
    });
}

function displayCategories(data) {
    const container = $('#categoriesContainer');
    
    if (data.success && data.data && data.data.length > 0) {
        let html = '<div class="row">';
        
        data.data.forEach(category => {
            html += `
                <div class="col-md-4 mb-3">
                    <div class="card category-card">
                        <div class="card-body">
                            <div class="category-display" id="display-${category.cat_id}">
                                <h6 class="card-title">${escapeHtml(category.cat_name)}</h6>
                                <p class="text-muted small">ID: ${category.cat_id}</p>
                                <div class="d-flex justify-content-end">
                                    <button class="btn btn-sm btn-outline-primary me-2" onclick="editCategory(${category.cat_id}, '${escapeHtml(category.cat_name)}')">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteCategory(${category.cat_id}, '${escapeHtml(category.cat_name)}')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                            <div class="category-edit edit-form" id="edit-${category.cat_id}" style="display: none;">
                                <div class="mb-3">
                                    <input type="text" class="form-control" id="editName-${category.cat_id}" value="${escapeHtml(category.cat_name)}" required>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-sm btn-secondary me-2" onclick="cancelEdit(${category.cat_id})">Cancel</button>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="updateCategory(${category.cat_id})">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        container.html(html);
    } else {
        container.html(`
            <div class="text-center py-4">
                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No categories found</h5>
                <p class="text-muted">Create your first category using the form above.</p>
            </div>
        `);
    }
}

function editCategory(catId, catName) {
    $(`#display-${catId}`).hide();
    $(`#edit-${catId}`).show();
    $(`#editName-${catId}`).focus();
}

function cancelEdit(catId) {
    $(`#display-${catId}`).show();
    $(`#edit-${catId}`).hide();
}

function updateCategory(catId) {
    const cat_name = $(`#editName-${catId}`).val().trim();
    
    // Validation patterns
    const namePattern = /^[a-zA-Z0-9\s\-_]{2,100}$/;
    
    if (!cat_name) {
        Swal.fire({
            icon: 'error',
            title: 'Missing Field',
            text: 'Please enter a category name!',
        });
        return;
    }
    
    if (!namePattern.test(cat_name)) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Category Name',
            text: 'Category name must be 2-100 characters and contain only letters, numbers, spaces, hyphens, and underscores!',
        });
        return;
    }

    Swal.fire({
        title: 'Updating Category...',
        text: 'Please wait while we update your category.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '../actions/update_category_action.php',
        type: 'POST',
        data: {
            cat_id: catId,
            cat_name: cat_name
        },
        success: function(response) {
            Swal.close();
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message,
                });
                loadCategories();
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

function deleteCategory(catId, catName) {
    Swal.fire({
        title: 'Are you sure?',
        text: `Delete the category "${catName}"? This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Deleting Category...',
                text: 'Please wait while we delete the category.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '../actions/delete_category_action.php',
                type: 'POST',
                data: { cat_id: catId },
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: response.message,
                        });
                        loadCategories();
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