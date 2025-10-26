<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Products - Shop Now</title>
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
        
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            padding: 60px 0 40px;
            margin-bottom: 40px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .filter-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
        
        .filter-title {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 20px;
            font-size: 1.1rem;
        }
        
        .product-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(209, 156, 151, 0.3);
        }
        
        .product-image-container {
            position: relative;
            width: 100%;
            height: 250px;
            overflow: hidden;
            background: linear-gradient(135deg, #f9f9f9, #ececec);
        }
        
        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .product-card:hover .product-image {
            transform: scale(1.1);
        }
        
        .product-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--primary-color);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .product-body {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        
        .product-category {
            color: var(--accent-color);
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .product-brand {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }
        
        .product-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
            min-height: 50px;
        }
        
        .product-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 15px;
        }
        
        .btn-add-cart {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: auto;
        }
        
        .btn-add-cart:hover {
            background: var(--accent-color);
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(209, 156, 151, 0.4);
        }
        
        .btn-view-details {
            background: transparent;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
            margin-bottom: 10px;
        }
        
        .btn-view-details:hover {
            background: var(--primary-color);
            color: white;
        }
        
        .pagination {
            margin-top: 40px;
        }
        
        .pagination .page-link {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .no-products {
            text-align: center;
            padding: 80px 20px;
            color: #666;
        }
        
        .no-products i {
            font-size: 5rem;
            color: var(--primary-color);
            opacity: 0.3;
            margin-bottom: 20px;
        }
        
        .filter-select {
            border-color: var(--primary-color);
            border-radius: 10px;
            padding: 10px 15px;
        }
        
        .filter-select:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.2rem rgba(209, 156, 151, 0.25);
        }
        
        .results-count {
            background: white;
            padding: 15px 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-4 mb-3">
                        <i class="fas fa-shopping-bag me-3"></i>Discover Our Products
                    </h1>
                    <p class="lead">Find the perfect items from our curated collection</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="../index.php" class="btn btn-light btn-lg">
                        <i class="fas fa-home me-2"></i>Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <!-- Filters Sidebar -->
            <div class="col-md-3">
                <div class="filter-card">
                    <h5 class="filter-title">
                        <i class="fas fa-filter me-2"></i>Filter Products
                    </h5>
                    
                    <div class="mb-3">
                        <label for="categoryFilter" class="form-label">Category</label>
                        <select class="form-select filter-select" id="categoryFilter">
                            <option value="">All Categories</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="brandFilter" class="form-label">Brand</label>
                        <select class="form-select filter-select" id="brandFilter">
                            <option value="">All Brands</option>
                        </select>
                    </div>
                    
                    <button class="btn btn-add-cart" id="clearFilters">
                        <i class="fas fa-redo me-2"></i>Clear Filters
                    </button>
                </div>
            </div>
            
            <!-- Products Grid -->
            <div class="col-md-9">
                <!-- Results Count -->
                <div class="results-count">
                    <span id="resultsText">Loading products...</span>
                </div>
                
                <!-- Products Container -->
                <div class="row" id="productsContainer">
                    <!-- Products will be loaded here via JavaScript -->
                </div>
                
                <!-- Pagination -->
                <nav aria-label="Product pagination">
                    <ul class="pagination justify-content-center" id="paginationContainer">
                        <!-- Pagination will be generated here -->
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/product_display.js"></script>
</body>
</html>
