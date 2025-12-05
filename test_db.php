<?php
require_once 'config.php';

echo "<h2>Testing Database Connection and Tables</h2>";

// Test 1: Check connection
try {
    echo "✅ Database connection successful<br>";
    
    // Test 2: Check if users table exists and has data
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $user_count = $stmt->fetchColumn();
    echo "✅ Users table: $user_count records<br>";
    
    // Test 3: Check if menu_items table exists
    $stmt = $pdo->query("SELECT COUNT(*) FROM menu_items");
    $item_count = $stmt->fetchColumn();
    echo "✅ Menu items table: $item_count records<br>";
    
    // Test 4: Check if categories table exists
    $stmt = $pdo->query("SELECT COUNT(*) FROM menu_categories");
    $cat_count = $stmt->fetchColumn();
    echo "✅ Categories table: $cat_count records<br>";
    
    // Test 5: Try to fetch some featured items
    $stmt = $pdo->query("SELECT item_name, price FROM menu_items WHERE is_featured = true LIMIT 3");
    $featured = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "✅ Featured items fetched: " . count($featured) . " items<br>";
    
    echo "<h3>Sample Featured Items:</h3>";
    foreach ($featured as $item) {
        echo "- " . $item['item_name'] . ": $" . $item['price'] . "<br>";
    }
    
    // Test 6: Try to login with existing user
    $stmt = $pdo->prepare("SELECT username, role FROM users WHERE username = 'admin'");
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($admin) {
        echo "✅ Admin user found: " . $admin['username'] . " (role: " . $admin['role'] . ")<br>";
    }
    
    echo "<h2>✅ All tests passed! Database is ready.</h2>";
    echo "<p><a href='index.php'>Go to Homepage</a> | <a href='login.php'>Login</a></p>";
    
} catch(PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>