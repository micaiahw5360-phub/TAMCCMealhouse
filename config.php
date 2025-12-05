<?php
// TAMCC Mealhouse Database Configuration for Render PostgreSQL
// This supports both local development and Render deployment

// Check if we're on Render (DATABASE_URL environment variable exists)
if (getenv('DATABASE_URL')) {
    // Parse the DATABASE_URL from Render
    $url = parse_url(getenv('DATABASE_URL'));
    
    $host = $url['host'];
    $port = $url['port'] ?? '5432';
    $dbname = ltrim($url['path'], '/');
    $username = $url['user'];
    $password = $url['pass'];
    
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    
} else {
    // Local development settings
    $host = "localhost";
    $port = "5432";
    $dbname = "tamccmealhouse";
    $username = "tamccmealhouse_user";  // Your Render username
    $password = "2DL7YF3IDBJBxxE9Ieaf3tLlqkqFfjNo";  // Get this from Render dashboard
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
}

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Set timezone for the session
    $pdo->exec("SET timezone = 'UTC'");
    
} catch(PDOException $e) {
    // Don't expose the full error in production
    if (getenv('DATABASE_URL')) {
        // Production error
        die("Database connection failed. Please check your Render database connection.");
    } else {
        // Development error - show details
        die("Database connection failed: " . $e->getMessage());
    }
}

// Application constants
define('SITE_NAME', 'TAMCC Mealhouse');
define('SITE_URL', 'https://tamccmealhouse.onrender.com'); // Update this
?>