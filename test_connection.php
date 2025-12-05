<?php
require_once 'config.php';

try {
    // Test connection
    echo "✅ Database connection successful!<br>";
    
    // Test if we can run a simple query
    $stmt = $pdo->query("SELECT version()");
    $version = $stmt->fetchColumn();
    echo "✅ PostgreSQL version: " . $version . "<br>";
    
    // Check if users table exists
    $stmt = $pdo->query("SELECT EXISTS (SELECT FROM information_schema.tables WHERE table_name = 'users')");
    $table_exists = $stmt->fetchColumn();
    
    if ($table_exists) {
        echo "✅ 'users' table exists!";
    } else {
        echo "❌ 'users' table does NOT exist!";
    }
    
} catch(PDOException $e) {
    echo "❌ Connection failed: " . $e->getMessage();
}
?>