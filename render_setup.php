<?php
require_once 'config.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Render PostgreSQL Setup - TAMCC Mealhouse</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f0f2f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: green; padding: 10px; background: #e7f7e7; border-radius: 5px; margin: 10px 0; }
        .error { color: red; padding: 10px; background: #ffe7e7; border-radius: 5px; margin: 10px 0; }
        .info { color: blue; padding: 10px; background: #e7f0ff; border-radius: 5px; margin: 10px 0; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🚀 TAMCC Mealhouse Render PostgreSQL Setup</h1>";
        
try {
    // Test connection
    echo "<div class='info'>Testing database connection...</div>";
    $stmt = $pdo->query("SELECT version() as version, current_database() as db, current_user as user");
    $info = $stmt->fetch();
    
    echo "<div class='success'>✅ Connected successfully!</div>";
    echo "<pre>Database: {$info['db']}
User: {$info['user']}
PostgreSQL: {$info['version']}</pre>";
    
    echo "<div class='info'>Importing SQL file...</div>";
    
    // Read and execute the SQL file
    $sqlFile = 'tamcc_mealhouse_postgresql.sql';
    if (!file_exists($sqlFile)) {
        die("<div class='error'>❌ SQL file '$sqlFile' not found!</div>");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Split into individual statements
    $statements = explode(';', $sql);
    $executed = 0;
    $errors = [];
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            try {
                $pdo->exec($statement . ';');
                $executed++;
            } catch (PDOException $e) {
                $errors[] = "Error: " . $e->getMessage() . "<br>Statement: " . substr($statement, 0, 200) . "...";
            }
        }
    }
    
    echo "<div class='success'>✅ Executed $executed SQL statements</div>";
    
    if (!empty($errors)) {
        echo "<div class='error'><h3>⚠️ Some errors occurred:</h3>";
        foreach ($errors as $error) {
            echo "<p>$error</p>";
        }
        echo "</div>";
    }
    
    // Verify tables
    echo "<div class='info'>Verifying tables...</div>";
    
    $tables = ['users', 'menu_categories', 'menu_items', 'combo_meals', 'daily_specials', 'orders'];
    
    echo "<table border='1' cellpadding='8' cellspacing='0' style='width:100%; border-collapse: collapse;'>
            <tr><th>Table</th><th>Status</th><th>Rows</th></tr>";
    
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
            $count = $stmt->fetch()['count'];
            echo "<tr><td>$table</td><td style='color:green;'>✅ Exists</td><td>$count</td></tr>";
        } catch (PDOException $e) {
            echo "<tr><td>$table</td><td style='color:red;'>❌ Missing</td><td>0</td></tr>";
        }
    }
    
    echo "</table>";
    
    // Test data
    echo "<div class='info'>Testing sample data...</div>";
    
    $tests = [
        "SELECT username, role FROM users WHERE is_active = true LIMIT 5" => "Active Users",
        "SELECT item_name, price FROM menu_items WHERE is_featured = true LIMIT 5" => "Featured Menu Items",
        "SELECT category_name FROM menu_categories WHERE is_featured = true LIMIT 5" => "Featured Categories"
    ];
    
    foreach ($tests as $query => $title) {
        echo "<h4>$title:</h4>";
        try {
            $stmt = $pdo->query($query);
            $results = $stmt->fetchAll();
            if (empty($results)) {
                echo "<p>No data found</p>";
            } else {
                echo "<ul>";
                foreach ($results as $row) {
                    echo "<li>" . implode(" - ", $row) . "</li>";
                }
                echo "</ul>";
            }
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
        }
    }
    
    // Success message
    echo "<div class='success' style='background: linear-gradient(135deg, #d4edda, #c3e6cb); padding: 20px; border-radius: 10px;'>
            <h2>🎉 Setup Complete!</h2>
            <p><strong>Your Render PostgreSQL database is now ready!</strong></p>
            
            <h3>🔑 Test Login Credentials:</h3>
            <ul>
                <li><strong>Username:</strong> admin</li>
                <li><strong>Password:</strong> password</li>
                <li><strong>Email:</strong> admin@tamccmealhouse.com</li>
            </ul>
            
            <h3>🚀 Next Steps:</h3>
            <ol>
                <li><a href='login.php' target='_blank'>Login to the system</a></li>
                <li><a href='index.php' target='_blank'>Visit the homepage</a></li>
                <li><a href='menu.php' target='_blank'>Browse the menu</a></li>
            </ol>
            
            <p><strong>Important:</strong> Delete this setup file after successful installation!</p>
        </div>";
        
} catch (PDOException $e) {
    echo "<div class='error'>
            <h2>❌ Connection Failed</h2>
            <p><strong>Error:</strong> " . $e->getMessage() . "</p>
            
            <h3>🔧 Troubleshooting Steps:</h3>
            <ol>
                <li>Check if your Render PostgreSQL instance is running</li>
                <li>Verify the database credentials in config.php</li>
                <li>Make sure you have the correct password from Render dashboard</li>
                <li>Check if your IP is whitelisted in Render (if connecting externally)</li>
            </ol>
            
            <h3>📋 To get your Render password:</h3>
            <ol>
                <li>Go to <a href='https://dashboard.render.com' target='_blank'>Render Dashboard</a></li>
                <li>Select your PostgreSQL database</li>
                <li>Click 'Connect' → 'External Connection'</li>
                <li>Copy the password and update config.php</li>
            </ol>
        </div>";
}

echo "</div></body></html>";
?>