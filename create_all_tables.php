<?php
require_once 'config.php';

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>TAMCC Mealhouse - Database Setup</title>
    <style>
        body { font-family: 'Inter', Arial, sans-serif; background: #f8fafc; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .success { color: #059669; background: #d1fae5; padding: 10px; border-radius: 8px; margin: 10px 0; }
        .error { color: #dc2626; background: #fee2e2; padding: 10px; border-radius: 8px; margin: 10px 0; }
        .info { color: #2563eb; background: #dbeafe; padding: 10px; border-radius: 8px; margin: 10px 0; }
        .card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin: 20px 0; }
        .btn { background: #3b82f6; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; display: inline-block; margin: 10px 5px; }
        .btn:hover { background: #2563eb; }
        h1, h2, h3 { color: #1e293b; }
        ul { line-height: 1.8; }
        .table-status { border-collapse: collapse; width: 100%; }
        .table-status td, .table-status th { padding: 10px; border-bottom: 1px solid #e5e7eb; }
    </style>
    <link href='https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap' rel='stylesheet'>
</head>
<body>
    <div class='container'>
        <h1>🏗️ TAMCC Mealhouse Database Setup</h1>
        <div class='info'>This script will create all necessary tables and populate them with sample data.</div>";

try {
    // Read the SQL file
    $sql_file = 'tamcc_mealhouse_postgresql.sql';
    
    if (!file_exists($sql_file)) {
        die("<div class='error'>❌ SQL file not found: $sql_file</div>");
    }
    
    $sql_content = file_get_contents($sql_file);
    
    // Split the SQL file into individual statements
    // Remove comments and split by semicolon
    $sql_content = preg_replace('/--.*$/m', '', $sql_content);
    $statements = array_filter(array_map('trim', explode(';', $sql_content)));
    
    echo "<div class='card'>
            <h3>📋 Executing SQL Statements</h3>
            <p>Found " . count($statements) . " SQL statements to execute</p>
        </div>";
    
    $success_count = 0;
    $error_count = 0;
    $errors = [];
    
    // Execute each statement
    foreach ($statements as $index => $statement) {
        if (empty($statement)) {
            continue;
        }
        
        try {
            $pdo->exec($statement . ';');
            
            // Check what type of statement this is
            $stmt_type = 'Statement';
            if (stripos($statement, 'CREATE TABLE') === 0) {
                preg_match('/CREATE TABLE (IF NOT EXISTS )?([a-zA-Z_]+)/i', $statement, $matches);
                $stmt_type = "Table: " . ($matches[2] ?? 'unknown');
                echo "<div class='success'>✅ Created $stmt_type</div>";
            } elseif (stripos($statement, 'INSERT INTO') === 0) {
                preg_match('/INSERT INTO ([a-zA-Z_]+)/i', $statement, $matches);
                $stmt_type = "Data: " . ($matches[1] ?? 'unknown');
                echo "<div class='success'>✅ Inserted data into " . ($matches[1] ?? 'unknown') . "</div>";
            } elseif (stripos($statement, 'CREATE INDEX') === 0 || stripos($statement, 'CREATE VIEW') === 0) {
                echo "<div class='success'>✅ Created index/view</div>";
            }
            
            $success_count++;
            
        } catch (PDOException $e) {
            $error_count++;
            $error_msg = $e->getMessage();
            $errors[] = "Statement " . ($index + 1) . ": " . substr($statement, 0, 100) . "...<br>Error: $error_msg";
            echo "<div class='error'>❌ Error in statement " . ($index + 1) . ": " . $error_msg . "</div>";
        }
    }
    
    echo "<div class='card'>
            <h3>📊 Execution Summary</h3>
            <ul>
                <li>✅ Successful statements: $success_count</li>
                <li>❌ Failed statements: $error_count</li>
                <li>📈 Total statements: " . count($statements) . "</li>
            </ul>
        </div>";
    
    if ($error_count > 0) {
        echo "<div class='error'>
                <h3>⚠️ Errors Encountered</h3>
                <ul>";
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul></div>";
    }
    
    // Verify the tables were created
    echo "<div class='card'>
            <h3>🔍 Verifying Database Structure</h3>";
    
    $tables_to_check = [
        'users' => 'User accounts and authentication',
        'menu_categories' => 'Food categories and organization',
        'menu_items' => 'Menu items with details',
        'combo_meals' => 'Combo meal packages',
        'daily_specials' => 'Daily special offers',
        'shopping_carts' => 'User shopping carts',
        'cart_items' => 'Items in shopping carts',
        'orders' => 'Customer orders',
        'order_items' => 'Items within orders'
    ];
    
    echo "<table class='table-status'>
            <tr><th>Table Name</th><th>Status</th><th>Rows</th><th>Description</th></tr>";
    
    foreach ($tables_to_check as $table => $description) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
            $count = $stmt->fetchColumn();
            echo "<tr>
                    <td><strong>$table</strong></td>
                    <td><span style='color: #059669;'>✅ Exists</span></td>
                    <td>$count records</td>
                    <td>$description</td>
                  </tr>";
        } catch (PDOException $e) {
            echo "<tr>
                    <td><strong>$table</strong></td>
                    <td><span style='color: #dc2626;'>❌ Missing</span></td>
                    <td>0</td>
                    <td>$description</td>
                  </tr>";
        }
    }
    
    echo "</table></div>";
    
    // Test data access
    echo "<div class='card'>
            <h3>🧪 Testing Data Access</h3>";
    
    $tests = [
        'Featured Items' => "SELECT COUNT(*) FROM menu_items WHERE is_featured = true",
        'Active Users' => "SELECT COUNT(*) FROM users WHERE is_active = true",
        'Available Categories' => "SELECT COUNT(*) FROM menu_categories WHERE is_active = true",
        'Daily Specials' => "SELECT COUNT(*) FROM daily_specials WHERE is_active = true AND special_date = CURRENT_DATE",
        'Combo Meals' => "SELECT COUNT(*) FROM combo_meals WHERE is_available = true"
    ];
    
    foreach ($tests as $test_name => $query) {
        try {
            $stmt = $pdo->query($query);
            $count = $stmt->fetchColumn();
            echo "<div class='success'>✅ $test_name: $count found</div>";
        } catch (PDOException $e) {
            echo "<div class='error'>❌ $test_name: Error - " . $e->getMessage() . "</div>";
        }
    }
    
    echo "</div>";
    
    // Login test credentials
    echo "<div class='card'>
            <h3>🔑 Test Login Credentials</h3>
            <p>Use these credentials to test the login system:</p>
            <table class='table-status'>
                <tr><th>Username</th><th>Password</th><th>Role</th><th>Email</th></tr>";
    
    $test_users = $pdo->query("SELECT username, role, email FROM users LIMIT 5");
    while ($user = $test_users->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>
                <td><strong>{$user['username']}</strong></td>
                <td>password (default)</td>
                <td>{$user['role']}</td>
                <td>{$user['email']}</td>
              </tr>";
    }
    
    echo "</table>
          <p><em>Note: The passwords are hashed in the database. Use 'password' for all test accounts.</em></p>
        </div>";
    
    // Success message with next steps
    echo "<div class='card' style='background: linear-gradient(135deg, #dbeafe, #d1fae5);'>
            <h2>🎉 Database Setup Complete!</h2>
            <p>Your TAMCC Mealhouse database is now ready. You can:</p>
            <ul>
                <li><a href='index.php' class='btn'>🏠 Go to Homepage</a></li>
                <li><a href='login.php' class='btn'>🔐 Test Login</a></li>
                <li><a href='menu.php' class='btn'>📋 View Menu</a></li>
            </ul>
            
            <h3>Quick Start Guide:</h3>
            <ol>
                <li>Test login with username: <strong>admin</strong> and password: <strong>password</strong></li>
                <li>Browse the menu at <a href='menu.php'>menu.php</a></li>
                <li>Test the shopping cart functionality</li>
                <li>Check the dashboard if logged in as admin</li>
            </ol>
            
            <div class='info'>
                <h4>⚠️ Important Notes:</h4>
                <ul>
                    <li>All passwords are hashed. Use 'password' for test accounts.</li>
                    <li>The database includes sample data for testing.</li>
                    <li>You can modify or add data via the admin interface or directly in the database.</li>
                    <li>For production, change all default passwords and remove this setup script.</li>
                </ul>
            </div>
        </div>";

} catch (PDOException $e) {
    echo "<div class='error'>
            <h2>❌ Database Connection Error</h2>
            <p>Unable to connect to the database. Please check:</p>
            <ul>
                <li>Database credentials in config.php</li>
                <li>PostgreSQL server is running</li>
                <li>Database 'tamcc_mealhouse' exists</li>
            </ul>
            <p><strong>Error Details:</strong> " . $e->getMessage() . "</p>
            <p>Make sure your config.php has:</p>
            <pre>
\$host = 'localhost';
\$port = '5432';
\$dbname = 'tamcc_mealhouse';
\$username = 'your_username';
\$password = 'your_password';
            </pre>
        </div>";
}

echo "</div></body></html>";