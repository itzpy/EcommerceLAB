<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../login/login.php');
    exit();
}
if ($_SESSION['role'] != 1) {
    header('Location: ../login/login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, rgba(209, 156, 151, 0.1) 0%, rgba(200, 144, 138, 0.1) 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .card-header {
            background: linear-gradient(135deg, #D19C97 0%, #c8908a 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #D19C97 0%, #c8908a 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #c8908a 0%, #D19C97 100%);
        }
        .product-card {
            transition: transform 0.2s;
            margin-bottom: 20px;
        }
        .product-card:hover {
            transform: translateY(-5px);
        }
        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
        }
        .category-section {
            margin-bottom: 30px;
        }
        .category-header {
            background: #D19C97;
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        .brand-header {
            background: rgba(209, 156, 151, 0.3);
            padding: 8px 15px;
            border-radius: 8px;
            margin: 15px 0 10px 0;
            font-weight: 600;
        }
        #imagePreview {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
            display: none;
            border-radius: 8px;
        }
        #imagePreviewContainer .preview-item {
            position: relative;
            margin-bottom: 15px;
        }
        #imagePreviewContainer .preview-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #ddd;
        }
        #imagePreviewContainer .preview-item .remove-image {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(220, 53, 69, 0.9);
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        #imagePreviewContainer .preview-item .remove-image:hover {
            background: rgba(220, 53, 69, 1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3 class="mb-0"><i class="fas fa-box"></i> Product Management</h3>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5>Product List</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal" id="addProductBtn">
                        <i class="fas fa-plus"></i> Add Product
                    </button>
                </div>
                <div id="productList"></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="productModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: #D19C97; color: white;">
                    <h5 class="modal-title" id="modalTitle">Add Product</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="productForm">
                        <input type="hidden" id="productId" name="product_id">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="productCategory" class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select" id="productCategory" name="product_cat" required>
                                    <option value="">Select Category</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="productBrand" class="form-label">Brand <span class="text-danger">*</span></label>
                                <select class="form-select" id="productBrand" name="product_brand" required disabled>
                                    <option value="">Select Category First</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="productTitle" class="form-label">Product Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="productTitle" name="product_title" required>
                        </div>

                        <div class="mb-3">
                            <label for="productPrice" class="form-label">Price <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="productPrice" name="product_price" step="0.01" min="0" required>
                        </div>

                        <div class="mb-3">
                            <label for="productDesc" class="form-label">Description</label>
                            <textarea class="form-control" id="productDesc" name="product_desc" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="productKeywords" class="form-label">Keywords</label>
                            <input type="text" class="form-control" id="productKeywords" name="product_keywords" placeholder="e.g. smartphone, electronics, gadget">
                        </div>

                        <div class="mb-3">
                            <label for="productImage" class="form-label">Product Image</label>
                            <input type="file" class="form-control" id="productImage" name="product_image" accept="image/*">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> Upload product image (Max 5MB). Formats: JPG, PNG, GIF, WebP
                            </small>
                            <input type="hidden" id="imagePath" name="product_image_path">
                            <div id="currentImage" style="margin-top: 10px;"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveProductBtn">Save Product</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/product.js"></script>
</body>
</html>
