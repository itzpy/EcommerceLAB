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
    <title>Checkout</title>
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
        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20a039 100%);
            border: none;
        }
        .btn-success:hover {
            background: linear-gradient(135deg, #20a039 0%, #28a745 100%);
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
        <a href="cart.php" class="btn btn-sm btn-outline-warning me-2">
            <i class="fas fa-shopping-cart"></i> Cart
        </a>
        <a href="../login/logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
    </div>

    <div class="container" style="padding-top: 80px;">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0"><i class="fas fa-credit-card"></i> Checkout</h3>
                    </div>
                    <div class="card-body">
                        <!-- Order Summary -->
                        <h5 class="mb-3">Order Summary</h5>
                        <div id="checkoutSummary">
                            <!-- Checkout summary will be loaded here by JavaScript -->
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2">Loading order summary...</p>
                            </div>
                        </div>

                        <!-- Payment Section -->
                        <div class="row mt-4">
                            <div class="col-md-8">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Payment Information:</strong><br>
                                    This is a simulated checkout. Click "Simulate Payment" to complete your order.
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h5>Total Amount</h5>
                                        <h3 class="text-success" id="totalAmount">GHS 0.00</h3>
                                        <button class="btn btn-success w-100 mt-3" onclick="showPaymentModal()">
                                            <i class="fas fa-money-bill-wave"></i> Simulate Payment
                                        </button>
                                        <button class="btn btn-outline-secondary w-100 mt-2" onclick="window.location.href='cart.php'">
                                            <i class="fas fa-arrow-left"></i> Back to Cart
                                        </button>
                                    </div>
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
    <script src="../js/checkout.js"></script>
</body>
</html>
