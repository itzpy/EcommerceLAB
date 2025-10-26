<?php
session_start();

// Get product ID from URL
$product_id = $_GET['id'] ?? '';

if (empty($product_id)) {
    header('Location: all_product.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #D19C97;
            --secondary-color: #c8908a;
            --accent-color: #b37f7a;
        }
        
        body {
            background: linear-gradient(135deg, #fef5f4 0%, #fdeee8 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .product-detail-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            margin: 40px 0;
        }
        
        .product-image-main {
            width: 100%;
            height: 500px;
            object-fit: cover;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        
        .breadcrumb-custom {
            background: white;
            padding: 15px 25px;
            border-radius: 10px;
            margin: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .breadcrumb-custom a {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .breadcrumb-custom a:hover {
            text-decoration: underline;
        }
        
        .category-badge {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 600;
            display: inline-block;
            margin-right: 10px;
        }
        
        .brand-badge {
            background: white;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
            padding: 8px 20px;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 600;
            display: inline-block;
        }
        
        .product-title-main {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin: 20px 0;
        }
        
        .product-price-main {
            font-size: 3rem;
            font-weight: 700;
            color: var(--primary-color);
            margin: 20px 0;
        }
        
        .product-description {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #555;
            margin: 30px 0;
            padding: 25px;
            background: #f9f9f9;
            border-left: 4px solid var(--primary-color);
            border-radius: 10px;
        }
        
        .product-keywords {
            margin: 20px 0;
        }
        
        .keyword-tag {
            background: var(--secondary-color);
            color: white;
            padding: 6px 15px;
            border-radius: 15px;
            font-size: 0.85rem;
            display: inline-block;
            margin: 5px;
        }
        
        .btn-add-to-cart-main {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            border: none;
            padding: 18px 50px;
            border-radius: 30px;
            font-size: 1.2rem;
            font-weight: 700;
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px rgba(209, 156, 151, 0.4);
        }
        
        .btn-add-to-cart-main:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(209, 156, 151, 0.6);
        }
        
        .info-section {
            background: white;
            padding: 25px;
            border-radius: 15px;
            margin: 20px 0;
            border: 2px solid #f0f0f0;
        }
        
        .info-section h5 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: #666;
        }
        
        .info-value {
            color: #333;
        }
        
        .loading-spinner {
            text-align: center;
            padding: 100px 0;
        }
        
        .spinner-border {
            color: var(--primary-color);
            width: 4rem;
            height: 4rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Breadcrumb -->
        <nav class="breadcrumb-custom" aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="../index.php"><i class="fas fa-home"></i> Home</a></li>
                <li class="breadcrumb-item"><a href="all_product.php">All Products</a></li>
                <li class="breadcrumb-item active" aria-current="page" id="breadcrumb-product">Loading...</li>
            </ol>
        </nav>
        
        <!-- Loading Spinner -->
        <div id="loadingSpinner" class="loading-spinner">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3">Loading product details...</p>
        </div>
        
        <!-- Product Detail Card -->
        <div id="productDetailContainer" style="display: none;">
            <div class="product-detail-card">
                <div class="row">
                    <!-- Product Image -->
                    <div class="col-md-6">
                        <img id="productImage" src="" alt="Product Image" class="product-image-main">
                    </div>
                    
                    <!-- Product Info -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <span class="category-badge" id="productCategory"></span>
                            <span class="brand-badge" id="productBrand"></span>
                        </div>
                        
                        <h1 class="product-title-main" id="productTitle"></h1>
                        
                        <div class="product-price-main" id="productPrice"></div>
                        
                        <div class="product-description" id="productDescription">
                            <i class="fas fa-quote-left me-2"></i>
                            <span id="descriptionText"></span>
                        </div>
                        
                        <div class="product-keywords" id="productKeywordsContainer">
                            <h6><i class="fas fa-tags me-2"></i>Keywords</h6>
                            <div id="productKeywords"></div>
                        </div>
                        
                        <div class="d-grid gap-2 mt-4">
                            <button class="btn btn-add-to-cart-main" id="addToCartBtn">
                                <i class="fas fa-shopping-cart me-3"></i>Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Additional Info Section -->
            <div class="info-section">
                <h5><i class="fas fa-info-circle me-2"></i>Product Information</h5>
                <div class="info-row">
                    <span class="info-label">Product ID:</span>
                    <span class="info-value" id="productId"></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Category:</span>
                    <span class="info-value" id="categoryInfo"></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Brand:</span>
                    <span class="info-value" id="brandInfo"></span>
                </div>
            </div>
        </div>
        
        <!-- Error Message -->
        <div id="errorMessage" style="display: none;" class="alert alert-danger mt-4">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <span id="errorText"></span>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const productId = <?php echo json_encode($product_id); ?>;
        
        $(document).ready(function() {
            loadProductDetails();
            
            $('#addToCartBtn').click(function() {
                Swal.fire({
                    icon: 'info',
                    title: 'Coming Soon',
                    text: 'Add to Cart functionality will be implemented in a future update.',
                    confirmButtonColor: '#D19C97'
                });
            });
        });
        
        function loadProductDetails() {
            $.ajax({
                url: '../actions/product_actions.php',
                method: 'GET',
                data: {
                    action: 'get_single_product',
                    product_id: productId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        displayProduct(response.data);
                    } else {
                        showError(response.message || 'Product not found.');
                    }
                },
                error: function() {
                    showError('Failed to load product details. Please try again.');
                }
            });
        }
        
        function displayProduct(product) {
            $('#loadingSpinner').hide();
            $('#productDetailContainer').show();
            
            // Set breadcrumb
            $('#breadcrumb-product').text(product.product_title);
            
            // Set product image
            const imageSrc = product.product_image 
                ? `../${product.product_image}` 
                : 'https://via.placeholder.com/500x500?text=No+Image';
            $('#productImage').attr('src', imageSrc).attr('alt', product.product_title);
            
            // Set product info
            $('#productCategory').text(product.cat_name || 'Uncategorized');
            $('#productBrand').text(product.brand_name || 'No Brand');
            $('#productTitle').text(product.product_title);
            $('#productPrice').text('$' + parseFloat(product.product_price).toFixed(2));
            
            // Set description
            if (product.product_desc && product.product_desc.trim() !== '') {
                $('#descriptionText').text(product.product_desc);
            } else {
                $('#productDescription').html('<em class="text-muted">No description available for this product.</em>');
            }
            
            // Set keywords
            if (product.product_keywords && product.product_keywords.trim() !== '') {
                const keywords = product.product_keywords.split(',');
                let keywordsHtml = '';
                keywords.forEach(keyword => {
                    keywordsHtml += `<span class="keyword-tag">${keyword.trim()}</span>`;
                });
                $('#productKeywords').html(keywordsHtml);
            } else {
                $('#productKeywordsContainer').hide();
            }
            
            // Set additional info
            $('#productId').text(product.product_id);
            $('#categoryInfo').text(product.cat_name || 'N/A');
            $('#brandInfo').text(product.brand_name || 'N/A');
        }
        
        function showError(message) {
            $('#loadingSpinner').hide();
            $('#errorText').text(message);
            $('#errorMessage').show();
        }
    </script>
</body>
</html>
