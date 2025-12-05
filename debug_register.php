<?php
// Turn on all error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "=== Debug Mode Started ===<br>";
echo "PHP Version: " . phpversion() . "<br>";

// Check if config.php exists
if (file_exists('config.php')) {
    echo "✅ config.php exists<br>";
    require_once 'config.php';
    echo "✅ config.php loaded<br>";
} else {
    echo "❌ config.php NOT FOUND<br>";
    die("Cannot continue without config.php");
}

// Check database connection
try {
    $stmt = $pdo->query("SELECT 1");
    echo "✅ Database connection successful<br>";
    
    // Check if users table exists
    $stmt = $pdo->query("SELECT EXISTS (SELECT FROM information_schema.tables WHERE table_name = 'users')");
    $table_exists = $stmt->fetchColumn();
    echo "✅ Users table exists: " . ($table_exists ? 'YES' : 'NO') . "<br>";
    
    // Check if session is started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
        echo "✅ Session started<br>";
    } else {
        echo "✅ Session already active<br>";
    }
    
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

echo "=== Debug Mode Ended ===<br>";
?>