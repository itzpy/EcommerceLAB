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
        const files = e.target.files;
        const maxFiles = 10;
        const maxSize = 5 * 1024 * 1024; // 5MB
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        
        // Check file count
        if (files.length > maxFiles) {
            Swal.fire({
                icon: 'error',
                title: 'Too Many Files',
                text: `Maximum ${maxFiles} images allowed. You selected ${files.length}.`
            });
            $(this).val('');
            $('#imagePreviewContainer').empty();
            return;
        }

        $('#imagePreviewContainer').empty();
        let validFiles = 0;

        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            
            // Validate file size
            if (file.size > maxSize) {
                Swal.fire({
                    icon: 'warning',
                    title: 'File Too Large',
                    text: `${file.name} exceeds 5MB and was skipped.`
                });
                continue;
            }

            // Validate file type
            if (!allowedTypes.includes(file.type)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid File Type',
                    text: `${file.name} is not a valid image type and was skipped.`
                });
                continue;
            }

            // Create preview
            const reader = new FileReader();
            reader.onload = function(e) {
                const col = $('<div>').addClass('col-md-3 col-sm-4 col-6 preview-item');
                const img = $('<img>').attr('src', e.target.result);
                const removeBtn = $('<button>')
                    .addClass('remove-image')
                    .html('<i class="fas fa-times"></i>')
                    .attr('data-index', i)
                    .click(function() {
                        col.remove();
                    });
                
                col.append(img).append(removeBtn);
                $('#imagePreviewContainer').append(col);
            };
            reader.readAsDataURL(file);
            validFiles++;
        }

        if (validFiles > 0) {
            Swal.fire({
                icon: 'success',
                title: 'Images Selected',
                text: `${validFiles} image(s) ready for upload`,
                timer: 2000,
                showConfirmButton: false
            });
        }
    });

    $('#saveProductBtn').click(function() {
        if (!validateForm()) {
            return;
        }

        saveProduct();
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
                } else {
                    console.error('Load products error:', response);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to load products'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                console.error('Response:', xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load products. Check console for details.'
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

    function saveProduct() {
        const formData = {
            product_id: $('#productId').val(),
            product_cat: $('#productCategory').val(),
            product_brand: $('#productBrand').val(),
            product_title: $('#productTitle').val(),
            product_price: $('#productPrice').val(),
            product_desc: $('#productDesc').val(),
            product_keywords: $('#productKeywords').val(),
            product_image: ''
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
                    const productId = response.product_id || currentProductId;
                    
                    if ($('#productImage')[0].files.length > 0 && productId) {
                        uploadImageForProduct(productId);
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            timer: 2000
                        });
                        $('#productModal').modal('hide');
                        loadProducts();
                        resetForm();
                    }
                } else {
                    console.error('Server response:', response);
                    let errorMsg = response.message || 'Failed to save product';
                    if (response.debug) {
                        console.error('Debug info:', response.debug);
                    }
                    if (response.error) {
                        console.error('Error details:', response.error);
                        errorMsg += '<br><small>' + response.error + '</small>';
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: errorMsg
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error Details:');
                console.error('Status:', status);
                console.error('Error:', error);
                console.error('Response Text:', xhr.responseText);
                console.error('Response Status:', xhr.status);
                
                let errorMessage = 'Failed to save product. ';
                if (xhr.status === 500) {
                    errorMessage += 'Server error (500). Check server logs.';
                } else if (xhr.status === 404) {
                    errorMessage += 'Action file not found (404).';
                } else if (xhr.status === 0) {
                    errorMessage += 'Network error. Check your connection.';
                } else {
                    errorMessage += 'Status: ' + xhr.status;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: errorMessage + '<br><small>Check browser console for details.</small>'
                });
            }
        });
    }

    function uploadImageForProduct(productId) {
        const fileInput = $('#productImage')[0];
        const files = fileInput.files;
        
        if (files.length === 0) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Product saved successfully!',
                timer: 2000
            });
            $('#productModal').modal('hide');
            loadProducts();
            resetForm();
            return;
        }

        const formData = new FormData();
        
        // Append all selected files
        for (let i = 0; i < files.length; i++) {
            formData.append('product_images[]', files[i]);
        }
        formData.append('product_id', productId);

        Swal.fire({
            title: 'Uploading Images...',
            html: `Uploading ${files.length} image(s), please wait...`,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '../actions/bulk_upload_product_images_action.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                Swal.close();
                
                if (response.success) {
                    let message = response.message;
                    if (response.warnings && response.warnings.length > 0) {
                        message += '<br><br><small class="text-warning">Warnings:<br>' + 
                                   response.warnings.join('<br>') + '</small>';
                    }
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        html: message,
                        timer: 3000
                    });
                    
                    $('#productModal').modal('hide');
                    loadProducts();
                    resetForm();
                } else {
                    let errorMsg = response.message || 'Failed to upload images';
                    if (response.errors && response.errors.length > 0) {
                        errorMsg += '<br><br><small class="text-danger">Errors:<br>' + 
                                   response.errors.join('<br>') + '</small>';
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Upload Failed',
                        html: errorMsg
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                console.error('Upload Error:', status, error);
                console.error('Response:', xhr.responseText);
                
                Swal.fire({
                    icon: 'error',
                    title: 'Upload Error',
                    html: 'Failed to upload images. Check console for details.'
                });
            }
        });
    }

    function updateProductImage(productId, imagePath) {
        $.ajax({
            url: '../actions/update_product_action.php',
            method: 'POST',
            data: {
                product_id: productId,
                product_cat: $('#productCategory').val(),
                product_brand: $('#productBrand').val(),
                product_title: $('#productTitle').val(),
                product_price: $('#productPrice').val(),
                product_desc: $('#productDesc').val(),
                product_keywords: $('#productKeywords').val(),
                product_image: imagePath
            },
            dataType: 'json',
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Product and image saved successfully!',
                    timer: 2000
                });
                $('#productModal').modal('hide');
                loadProducts();
                resetForm();
            },
            error: function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Product Saved',
                    text: 'Product saved (image may not be linked)'
                });
                $('#productModal').modal('hide');
                loadProducts();
                resetForm();
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
