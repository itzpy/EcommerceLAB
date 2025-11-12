<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../login/login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
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
        .cart-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 11px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Menu Tray -->
    <div class="menu-tray">
        <span class="me-2">Menu:</span>
        <span class="text-muted me-2">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</span>
        <a href="../index.php" class="btn btn-sm btn-outline-primary me-1">
            <i class="fas fa-home"></i> Home
        </a>
        <a href="all_product.php" class="btn btn-sm btn-outline-info me-1">
            <i class="fas fa-shopping-bag"></i> All Products
        </a>
        <a href="cart.php" class="btn btn-sm btn-primary me-2" style="position: relative;">
            <i class="fas fa-shopping-cart"></i> Cart
            <span id="cartCount" class="cart-badge" style="display: none;">0</span>
        </a>
        <a href="../login/logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
    </div>

    <div class="container" style="padding-top: 80px;">
        <div class="card">
            <div class="card-header">
                <h3 class="mb-0"><i class="fas fa-shopping-cart"></i> Shopping Cart</h3>
            </div>
            <div class="card-body">
                <!-- Cart Items -->
                <div id="cartItems">
                    <!-- Cart items will be loaded here by JavaScript -->
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading cart...</p>
                    </div>
                </div>

                <!-- Cart Summary -->
                <div id="cartSummary" style="display: none;">
                    <div class="row mt-4">
                        <div class="col-md-8">
                            <button class="btn btn-outline-secondary" onclick="window.location.href='all_product.php'">
                                <i class="fas fa-arrow-left"></i> Continue Shopping
                            </button>
                            <button class="btn btn-outline-danger ms-2" onclick="emptyCart()">
                                <i class="fas fa-trash"></i> Empty Cart
                            </button>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5>Cart Total</h5>
                                    <h3 class="text-primary" id="cartTotal">GHS 0.00</h3>
                                    <button class="btn btn-primary w-100 mt-3" onclick="proceedToCheckout()">
                                        <i class="fas fa-credit-card"></i> Proceed to Checkout
                                    </button>
                                </div>
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
    <script src="../js/cart.js"></script>
</body>
</html>
