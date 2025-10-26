<?php
// Diagnostic script - check if product system is working
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Product Management System Diagnostic</h2>";

// Test 1: Database Connection
echo "<h3>1. Database Connection</h3>";
try {
    $conn = new mysqli('localhost', 'root', '', 'shoppn');
    if ($conn->connect_error) {
        echo "❌ Connection failed: " . $conn->connect_error . "<br>";
    } else {
        echo "✅ Database connected successfully<br>";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

// Test 2: Check products table structure
echo "<h3>2. Products Table Structure</h3>";
try {
    $result = $conn->query("DESCRIBE products");
    if ($result) {
        echo "<table border='1'><tr><th>Field</th><th>Type</th></tr>";
        $has_user_id = false;
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td></tr>";
            if ($row['Field'] === 'user_id') $has_user_id = true;
        }
        echo "</table>";
        echo $has_user_id ? "<p style='color:green'>✅ user_id column exists</p>" : "<p style='color:red'>❌ user_id column missing!</p>";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

// Test 3: Check if product class file exists
echo "<h3>3. Product Class File</h3>";
$class_file = __DIR__ . '/classes/product_class.php';
if (file_exists($class_file)) {
    echo "✅ product_class.php exists<br>";
    require_once($class_file);
    echo "✅ product_class.php loaded successfully<br>";
} else {
    echo "❌ product_class.php NOT FOUND at: $class_file<br>";
}

// Test 4: Check if controller file exists
echo "<h3>4. Product Controller File</h3>";
$controller_file = __DIR__ . '/controllers/product_controller.php';
if (file_exists($controller_file)) {
    echo "✅ product_controller.php exists<br>";
    require_once($controller_file);
    echo "✅ product_controller.php loaded successfully<br>";
} else {
    echo "❌ product_controller.php NOT FOUND at: $controller_file<br>";
}

// Test 5: Test product fetching
echo "<h3>5. Test Product Fetch</h3>";
try {
    if (class_exists('Product')) {
        $product = new Product();
        echo "✅ Product class instantiated<br>";
        
        // Test query
        $sql = "SELECT COUNT(*) as total FROM products";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        echo "✅ Total products in database: " . $row['total'] . "<br>";
    } else {
        echo "❌ Product class not found<br>";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<hr><p><strong>Diagnostic Complete!</strong></p>";
echo "<p>If all checks show ✅, the system should work.</p>";
echo "<p><a href='admin/product.php'>Go to Product Management</a></p>";
?>
