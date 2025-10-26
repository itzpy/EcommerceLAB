<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Product Add</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #fef5f4 0%, #fdeee8 100%);
            min-height: 100vh;
            padding: 50px 0;
        }
        .test-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .status-box {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        .status-success { background: #d4edda; color: #155724; }
        .status-error { background: #f8d7da; color: #721c24; }
        .status-info { background: #d1ecf1; color: #0c5460; }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="test-card">
            <h2 class="mb-4">🧪 Product Add Functionality Test</h2>
            
            <div class="mb-4">
                <button class="btn btn-primary" id="runTest">Run System Check</button>
                <a href="../admin/product.php" class="btn btn-success">Go to Product Management</a>
                <a href="../index.php" class="btn btn-secondary">Back to Home</a>
            </div>
            
            <div id="results"></div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $('#runTest').click(function() {
            $(this).prop('disabled', true).text('Running tests...');
            $('#results').html('<div class="status-box status-info">Running system checks...</div>');
            
            $.ajax({
                url: 'test_product_add.php',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    displayResults(response);
                },
                error: function(xhr, status, error) {
                    $('#results').html(`
                        <div class="status-box status-error">
                            <h5>❌ Test Failed</h5>
                            <p><strong>Error:</strong> ${error}</p>
                            <p><strong>Status:</strong> ${status}</p>
                            <pre>${xhr.responseText}</pre>
                        </div>
                    `);
                },
                complete: function() {
                    $('#runTest').prop('disabled', false).text('Run System Check');
                }
            });
        });
        
        function displayResults(data) {
            let html = '';
            
            if (data.success) {
                html += '<div class="status-box status-success"><h5>✅ All System Checks Passed!</h5></div>';
                
                // Session info
                html += '<div class="mb-3"><h6>Session Information:</h6><pre>' + JSON.stringify(data.session, null, 2) + '</pre></div>';
                
                // Database info
                html += '<div class="mb-3"><h6>Database Status:</h6>';
                html += '<ul class="list-unstyled">';
                html += '<li>✅ Database Connected</li>';
                html += '<li>' + (data.database.has_user_id ? '✅' : '❌') + ' user_id column exists</li>';
                html += '<li>' + (data.database.has_date_created ? '✅' : '❌') + ' date_created column exists</li>';
                html += '</ul>';
                html += '<details><summary>View all columns</summary><pre>' + JSON.stringify(data.database.products_columns, null, 2) + '</pre></details>';
                html += '</div>';
                
                // Data availability
                html += '<div class="mb-3"><h6>Available Data:</h6>';
                html += '<p><strong>Categories:</strong> ' + data.data.categories_count + '</p>';
                if (data.data.categories_count > 0) {
                    html += '<pre>' + JSON.stringify(data.data.categories, null, 2) + '</pre>';
                } else {
                    html += '<div class="alert alert-warning">⚠️ No categories found. Please add categories first!</div>';
                }
                
                html += '<p><strong>Brands:</strong> ' + data.data.brands_count + '</p>';
                if (data.data.brands_count > 0) {
                    html += '<pre>' + JSON.stringify(data.data.brands, null, 2) + '</pre>';
                } else {
                    html += '<div class="alert alert-warning">⚠️ No brands found. Please add brands first!</div>';
                }
                html += '</div>';
                
                if (data.data.categories_count > 0 && data.data.brands_count > 0) {
                    html += '<div class="alert alert-success mt-3">✅ <strong>System Ready!</strong> You can now add products.</div>';
                } else {
                    html += '<div class="alert alert-warning mt-3">⚠️ <strong>Action Required:</strong> Please add categories and brands before adding products.</div>';
                }
                
            } else {
                html += '<div class="status-box status-error">';
                html += '<h5>❌ System Check Failed</h5>';
                html += '<p><strong>Message:</strong> ' + data.message + '</p>';
                if (data.session_data) {
                    html += '<pre>' + JSON.stringify(data.session_data, null, 2) + '</pre>';
                }
                html += '</div>';
            }
            
            $('#results').html(html);
        }
        
        // Run test on page load
        $(document).ready(function() {
            $('#runTest').click();
        });
    </script>
</body>
</html>
