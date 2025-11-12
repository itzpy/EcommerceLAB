<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Home</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
	<style>
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
	</style>
</head>
<body>
	<?php
	session_start();
	$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
	?>

	<div class="menu-tray">
		<span class="me-2">Menu:</span>
		<?php if ($isLoggedIn): ?>
			<span class="text-muted me-2">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</span>
			<!-- Customer-facing links -->
			<a href="customer/all_product.php" class="btn btn-sm btn-outline-primary me-1">
				<i class="fas fa-shopping-bag"></i> All Products
			</a>
			<a href="customer/cart.php" class="btn btn-sm btn-outline-success me-1" style="position: relative;">
				<i class="fas fa-shopping-cart"></i> Cart
				<span id="cartCount" class="badge bg-danger" style="position: absolute; top: -5px; right: -5px; display: none;">0</span>
			</a>
			<?php if ($_SESSION['role'] == 1): ?>
				<!-- Admin-only links -->
				<a href="admin/category.php" class="btn btn-sm btn-outline-success me-1">Category</a>
				<a href="admin/brand.php" class="btn btn-sm btn-outline-info me-1">Brand</a>
				<a href="admin/product.php" class="btn btn-sm btn-outline-warning me-2">Add Product</a>
			<?php endif; ?>
			<a href="login/logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
		<?php else: ?>
			<a href="login/register.php" class="btn btn-sm btn-outline-primary me-1">Register</a>
			<a href="customer/all_product.php" class="btn btn-sm btn-outline-info me-1">
				<i class="fas fa-shopping-bag"></i> All Products
			</a>
			<a href="login/login.php" class="btn btn-sm btn-outline-secondary">Login</a>
		<?php endif; ?>
	</div>

	<!-- Search Box & Filters -->
	<div class="container" style="padding-top:80px;">
		<div class="row mb-4">
			<div class="col-md-12">
				<div class="card border-0 shadow-sm" style="border-radius: 15px; background: rgba(255,255,255,0.95);">
					<div class="card-body p-4">
						<h5 class="mb-3">
							<i class="fas fa-search"></i> Search Products
						</h5>
						<form action="customer/product_search_result.php" method="GET" class="row g-3">
							<div class="col-md-5">
								<input type="text" class="form-control" name="q" placeholder="Search by product name..." required>
							</div>
							<div class="col-md-3">
								<select class="form-select" id="categoryFilterHome">
									<option value="">All Categories</option>
								</select>
							</div>
							<div class="col-md-3">
								<select class="form-select" id="brandFilterHome">
									<option value="">All Brands</option>
								</select>
							</div>
							<div class="col-md-1">
								<button type="submit" class="btn btn-primary w-100" style="background: #D19C97; border-color: #D19C97;">
									<i class="fas fa-search"></i>
								</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="container" style="padding-top:20px;">
		<div class="text-center">
			<?php if ($isLoggedIn): ?>
				<div class="welcome-message">
					<h1>Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h1>
					<p class="text-muted">You are successfully logged in to your account.</p>
					<p class="small text-muted">
						Account Details: 
						<strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?> | 
						<strong>Role:</strong> <?php echo $_SESSION['role'] == 1 ? 'Admin' : 'Customer'; ?> |
						<strong>Location:</strong> <?php echo htmlspecialchars($_SESSION['city'] . ', ' . $_SESSION['country']); ?>
					</p>
				</div>
			<?php else: ?>
				<h1>Welcome</h1>
				<p class="text-muted">Use the menu in the top-right to Register or Login.</p>
			<?php endif; ?>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script>
		// Load categories and brands for homepage filters
		$(document).ready(function() {
			loadCategories();
			loadBrands();
		});
		
		function loadCategories() {
			$.ajax({
				url: 'actions/product_actions.php',
				method: 'GET',
				data: { action: 'get_all_categories' },
				dataType: 'json',
				success: function(response) {
					if (response.success && response.data) {
						let options = '<option value="">All Categories</option>';
						response.data.forEach(function(category) {
							options += `<option value="${category.cat_id}">${category.cat_name}</option>`;
						});
						$('#categoryFilterHome').html(options);
					}
				}
			});
		}
		
		function loadBrands() {
			$.ajax({
				url: 'actions/product_actions.php',
				method: 'GET',
				data: { action: 'get_all_brands' },
				dataType: 'json',
				success: function(response) {
					if (response.success && response.data) {
						let options = '<option value="">All Brands</option>';
						response.data.forEach(function(brand) {
							options += `<option value="${brand.brand_id}">${brand.brand_name}</option>`;
						});
						$('#brandFilterHome').html(options);
					}
				}
			});
		}
		
		// Update cart count
		function updateCartCount() {
			<?php if ($isLoggedIn): ?>
			$.ajax({
				url: 'actions/get_cart_count.php',
				type: 'GET',
				dataType: 'json',
				success: function(response) {
					if (response.success && response.count > 0) {
						$('#cartCount').text(response.count).show();
					} else {
						$('#cartCount').hide();
					}
				}
			});
			<?php endif; ?>
		}
		
		// Load cart count on page load
		$(document).ready(function() {
			updateCartCount();
		});
	</script>
</body>
</html>
