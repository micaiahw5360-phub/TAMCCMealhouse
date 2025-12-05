<?php
// TAMCC Mealhouse - PostgreSQL Configuration for Render

// Check if we're in production (Render) or local
if (getenv('RENDER') || getenv('DATABASE_URL')) {
    // Parse DATABASE_URL from Render
    $db_url = getenv('DATABASE_URL');
    
    if ($db_url) {
        $url = parse_url($db_url);
        $db_host = $url['host'];
        $db_port = $url['port'] ?? 5432;
        $db_name = ltrim($url['path'], '/');
        $db_user = $url['user'];
        $db_pass = $url['pass'];
    } else {
        // Use individual environment variables
        $db_host = getenv('DB_HOST') ?: 'localhost';
        $db_port = getenv('DB_PORT') ?: 5432;
        $db_name = getenv('DB_NAME') ?: 'tamccmealhouse';
        $db_user = getenv('DB_USER') ?: 'tamccmealhouse_user';
        $db_pass = getenv('DB_PASSWORD') ?: '';
    }
} else {
    // Local development
    $db_host = 'localhost';
    $db_port = 5432;
    $db_name = 'tamccmealhouse';
    $db_user = 'postgres';
    $db_pass = 'postgres'; // Try this password
}

// Site Configuration
define('SITE_NAME', 'TAMCC Mealhouse');
define('SITE_URL', 'https://tamccmealhouse.onrender.com');
define('SITE_EMAIL', 'contact@tamccmealhouse.com');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('UTC');

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// PostgreSQL Connection - SIMPLIFIED
try {
    $dsn = "pgsql:host=$db_host;port=$db_port;dbname=$db_name";
    $pdo = new PDO($dsn, $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<!-- Database connected successfully -->";
} catch (PDOException $e) {
    // Don't die, just set to null
    $pdo = null;
    echo "<!-- Database connection failed, running in fallback mode -->";
}
?>