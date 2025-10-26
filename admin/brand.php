<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../login/login.php");
    exit;
}

// Check if user is an admin
if ($_SESSION['role'] != 1) {
    header("Location: ../login/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brand Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .brand-card {
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
            background: rgba(255,255,255,0.9);
            border-radius: 8px;
        }
        .brand-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .edit-form {
            display: none;
        }
        .menu-tray {
            position: fixed;
            top: 16px;
            right: 16px;
            background: rgba(255,255,255,0.95);
            border: 1px solid #e6e6e6;
            border-radius: 8px;
            padding: 6px 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.06);
            z-index: 1000;
        }
        .menu-tray a { margin-left: 8px; }
        .welcome-message {
            background: rgba(255,255,255,0.9);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #D19C97;
        }
        .card-header.bg-primary {
            background-color: #D19C97 !important;
            border-color: #D19C97;
        }
        .card-header.bg-secondary {
            background-color: #c8908a !important;
            border-color: #c8908a;
        }
        .btn-primary {
            background-color: #D19C97;
            border-color: #D19C97;
        }
        .btn-primary:hover {
            background-color: #c8908a;
            border-color: #c8908a;
        }
        .text-primary {
            color: #D19C97 !important;
        }
        .btn-outline-primary {
            color: #D19C97;
            border-color: #D19C97;
        }
        .btn-outline-primary:hover {
            background-color: #D19C97;
            border-color: #D19C97;
        }
        .category-badge {
            background-color: #D19C97;
            color: white;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.875rem;
            display: inline-block;
            margin-bottom: 8px;
        }
        .category-section {
            margin-bottom: 30px;
        }
        .category-header {
            background: linear-gradient(135deg, #D19C97 0%, #c8908a 100%);
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Menu Tray -->
    <div class="menu-tray">
        <span class="me-2">Menu:</span>
        <a href="../index.php" class="btn btn-sm btn-outline-primary me-2">Home</a>
        <a href="category.php" class="btn btn-sm btn-outline-secondary me-2">Category</a>
        <a href="../login/logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
    </div>

    <div class="container mt-5 pt-4">
        <div class="row">
            <div class="col-12">
                <div class="welcome-message">
                    <h1 class="h3 text-primary">
                        <i class="fas fa-certificate me-2"></i>Brand Management
                    </h1>
                    <p class="text-muted mb-0">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>! Manage your brands organized by categories.</p>
                </div>
            </div>
        </div>

        <!-- Add Brand Form -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-plus me-2"></i>Add New Brand</h5>
                    </div>
                    <div class="card-body">
                        <form id="addBrandForm">
                            <div class="mb-3">
                                <label for="categorySelect" class="form-label">Category</label>
                                <select class="form-select" id="categorySelect" name="cat_id" required>
                                    <option value="">Select a category...</option>
                                </select>
                                <div class="form-text">Select the category for this brand.</div>
                            </div>
                            <div class="mb-3">
                                <label for="brandName" class="form-label">Brand Name</label>
                                <input type="text" class="form-control" id="brandName" name="brand_name" required>
                                <div class="form-text">Enter a unique brand name for the selected category.</div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Add Brand
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Brands List -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Your Brands by Category</h5>
                    </div>
                    <div class="card-body">
                        <div id="brandsContainer">
                            <div class="text-center py-4">
                                <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                                <p class="text-muted mt-2">Loading brands...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/brand.js"></script>
</body>
</html>
