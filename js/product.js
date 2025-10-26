$(document).ready(function() {
    let currentProductId = null;
    let uploadedImagePath = '';

    loadCategories();
    loadProducts();

    $('#addProductBtn').click(function() {
        resetForm();
        $('#modalTitle').text('Add Product');
        currentProductId = null;
        uploadedImagePath = '';
    });

    $('#productCategory').change(function() {
        const catId = $(this).val();
        if (catId) {
            loadBrandsByCategory(catId);
        } else {
            $('#productBrand').prop('disabled', true).html('<option value="">Select Category First</option>');
        }
    });

    $('#productImage').change(function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 5 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: 'Image size must be less than 5MB'
                });
                $(this).val('');
                $('#imagePreview').hide();
                return;
            }

            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid File Type',
                    text: 'Only JPG, PNG, GIF, and WebP images are allowed'
                });
                $(this).val('');
                $('#imagePreview').hide();
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').attr('src', e.target.result).show();
            };
            reader.readAsDataURL(file);
        }
    });

    $('#saveProductBtn').click(function() {
        if (!validateForm()) {
            return;
        }

        const hasNewImage = $('#productImage')[0].files.length > 0;

        if (currentProductId && !hasNewImage && !uploadedImagePath && !$('#imagePath').val()) {
            saveProduct();
        } else if (hasNewImage) {
            uploadImage();
        } else {
            saveProduct();
        }
    });

    function loadCategories() {
        $.ajax({
            url: '../actions/fetch_category_action.php',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    let options = '<option value="">Select Category</option>';
                    response.data.forEach(function(category) {
                        options += `<option value="${category.cat_id}">${category.cat_name}</option>`;
                    });
                    $('#productCategory').html(options);
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load categories'
                });
            }
        });
    }

    function loadBrandsByCategory(catId) {
        $.ajax({
            url: '../actions/get_brands_by_category_action.php',
            method: 'GET',
            data: { cat_id: catId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    let options = '<option value="">Select Brand</option>';
                    response.data.forEach(function(brand) {
                        options += `<option value="${brand.brand_id}">${brand.brand_name}</option>`;
                    });
                    $('#productBrand').prop('disabled', false).html(options);
                } else {
                    $('#productBrand').prop('disabled', true).html('<option value="">No brands available</option>');
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load brands'
                });
            }
        });
    }

    function loadProducts() {
        $.ajax({
            url: '../actions/fetch_product_action.php',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    displayProducts(response.data);
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load products'
                });
            }
        });
    }

    function displayProducts(products) {
        if (products.length === 0) {
            $('#productList').html('<div class="alert alert-info">No products found. Click "Add Product" to create your first product.</div>');
            return;
        }

        let grouped = {};
        products.forEach(function(product) {
            if (!grouped[product.cat_name]) {
                grouped[product.cat_name] = {};
            }
            if (!grouped[product.cat_name][product.brand_name]) {
                grouped[product.cat_name][product.brand_name] = [];
            }
            grouped[product.cat_name][product.brand_name].push(product);
        });

        let html = '';
        Object.keys(grouped).forEach(function(category) {
            html += `<div class="category-section">
                        <div class="category-header">
                            <i class="fas fa-folder"></i> ${category}
                        </div>`;
            
            Object.keys(grouped[category]).forEach(function(brand) {
                html += `<div class="brand-header">
                            <i class="fas fa-tag"></i> ${brand}
                         </div>
                         <div class="row">`;
                
                grouped[category][brand].forEach(function(product) {
                    const imageSrc = product.product_image 
                        ? `../${product.product_image}` 
                        : 'https://via.placeholder.com/200x200?text=No+Image';
                    
                    html += `
                        <div class="col-md-4">
                            <div class="card product-card">
                                <img src="${imageSrc}" class="product-image" alt="${product.product_title}">
                                <div class="card-body">
                                    <h6 class="card-title">${product.product_title}</h6>
                                    <p class="text-muted mb-2">$${parseFloat(product.product_price).toFixed(2)}</p>
                                    <p class="small text-muted">${product.product_desc || 'No description'}</p>
                                    <button class="btn btn-sm btn-primary editProductBtn" data-id="${product.product_id}">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                </div>
                            </div>
                        </div>`;
                });
                
                html += `</div>`;
            });
            
            html += `</div>`;
        });

        $('#productList').html(html);

        $('.editProductBtn').click(function() {
            const productId = $(this).data('id');
            editProduct(productId);
        });
    }

    function editProduct(productId) {
        $.ajax({
            url: '../controllers/product_controller.php',
            method: 'POST',
            data: { 
                action: 'get_product',
                product_id: productId 
            },
            dataType: 'json',
            success: function(product) {
                currentProductId = productId;
                $('#modalTitle').text('Edit Product');
                $('#productId').val(product.product_id);
                $('#productCategory').val(product.product_cat).trigger('change');
                
                setTimeout(function() {
                    $('#productBrand').val(product.product_brand);
                }, 500);
                
                $('#productTitle').val(product.product_title);
                $('#productPrice').val(product.product_price);
                $('#productDesc').val(product.product_desc);
                $('#productKeywords').val(product.product_keywords);
                $('#imagePath').val(product.product_image);
                uploadedImagePath = product.product_image;
                
                if (product.product_image) {
                    $('#currentImage').html(`
                        <div class="mt-2">
                            <small class="text-muted">Current Image:</small><br>
                            <img src="../${product.product_image}" style="max-width: 150px; border-radius: 8px;">
                        </div>
                    `);
                } else {
                    $('#currentImage').html('');
                }
                
                $('#productModal').modal('show');
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load product details'
                });
            }
        });
    }

    function uploadImage() {
        const fileInput = $('#productImage')[0];
        if (fileInput.files.length === 0) {
            saveProduct();
            return;
        }

        const formData = new FormData();
        formData.append('product_image', fileInput.files[0]);
        formData.append('product_id', currentProductId || 'temp_' + Date.now());

        $.ajax({
            url: '../actions/upload_product_image_action.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    uploadedImagePath = response.image_path;
                    $('#imagePath').val(uploadedImagePath);
                    saveProduct();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Upload Failed',
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to upload image'
                });
            }
        });
    }

    function saveProduct() {
        const formData = {
            product_id: $('#productId').val(),
            product_cat: $('#productCategory').val(),
            product_brand: $('#productBrand').val(),
            product_title: $('#productTitle').val(),
            product_price: $('#productPrice').val(),
            product_desc: $('#productDesc').val(),
            product_keywords: $('#productKeywords').val(),
            product_image: uploadedImagePath || $('#imagePath').val() || ''
        };

        const url = currentProductId 
            ? '../actions/update_product_action.php' 
            : '../actions/add_product_action.php';

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        timer: 2000
                    });
                    $('#productModal').modal('hide');
                    loadProducts();
                    resetForm();
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
                    text: 'Failed to save product'
                });
            }
        });
    }

    function validateForm() {
        const category = $('#productCategory').val();
        const brand = $('#productBrand').val();
        const title = $('#productTitle').val().trim();
        const price = $('#productPrice').val();

        if (!category) {
            Swal.fire({
                icon: 'warning',
                title: 'Validation Error',
                text: 'Please select a category'
            });
            return false;
        }

        if (!brand) {
            Swal.fire({
                icon: 'warning',
                title: 'Validation Error',
                text: 'Please select a brand'
            });
            return false;
        }

        if (!title) {
            Swal.fire({
                icon: 'warning',
                title: 'Validation Error',
                text: 'Product title is required'
            });
            return false;
        }

        if (title.length < 3) {
            Swal.fire({
                icon: 'warning',
                title: 'Validation Error',
                text: 'Product title must be at least 3 characters'
            });
            return false;
        }

        if (!price || parseFloat(price) <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Validation Error',
                text: 'Please enter a valid price greater than 0'
            });
            return false;
        }

        return true;
    }

    function resetForm() {
        $('#productForm')[0].reset();
        $('#productId').val('');
        $('#imagePath').val('');
        $('#imagePreview').hide();
        $('#currentImage').html('');
        $('#productBrand').prop('disabled', true).html('<option value="">Select Category First</option>');
        currentProductId = null;
        uploadedImagePath = '';
    }
});
