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
    <title>Category Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .category-card {
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
            background: rgba(255,255,255,0.9);
            border-radius: 8px;
        }
        .category-card:hover {
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
    </style>
</head>
<body class="bg-light">
    <!-- Menu Tray -->
    <div class="menu-tray">
        <span class="me-2">Menu:</span>
        <a href="../index.php" class="btn btn-sm btn-outline-primary me-2">Home</a>
        <a href="../login/logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
    </div>

    <div class="container mt-5 pt-4">
        <div class="row">
            <div class="col-12">
                <div class="welcome-message">
                    <h1 class="h3 text-primary">
                        <i class="fas fa-tags me-2"></i>Category Management
                    </h1>
                    <p class="text-muted mb-0">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>! Manage your product categories here.</p>
                </div>
            </div>
        </div>

        <!-- Add Category Form -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-plus me-2"></i>Add New Category</h5>
                    </div>
                    <div class="card-body">
                        <form id="addCategoryForm">
                            <div class="mb-3">
                                <label for="categoryName" class="form-label">Category Name</label>
                                <input type="text" class="form-control" id="categoryName" name="cat_name" required>
                                <div class="form-text">Enter a unique category name.</div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Add Category
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Categories List -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Your Categories</h5>
                    </div>
                    <div class="card-body">
                        <div id="categoriesContainer">
                            <div class="text-center py-4">
                                <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                                <p class="text-muted mt-2">Loading categories...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Modal -->
    <div class="modal fade" id="messageModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="messageModalTitle">Notification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="messageModalBody"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="confirmModalBody"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmButton">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/category.js"></script>
</body>
</html>