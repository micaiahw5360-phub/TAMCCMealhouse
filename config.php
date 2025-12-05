<?php
// config.php - Database Configuration

// Check if we're on Render (production) or local
if (getenv('RENDER')) {
    // Render PostgreSQL (if you set up a database)
    $db_host = getenv('DB_HOST') ?: 'localhost';
    $db_name = getenv('DB_NAME') ?: 'tamcc_mealhouse';
    $db_user = getenv('DB_USER') ?: 'postgres';
    $db_pass = getenv('DB_PASSWORD') ?: '';
} else {
    // Local development
    $db_host = 'localhost';
    $db_name = 'tamcc_mealhouse';
    $db_user = 'root';
    $db_pass = '';
}

// Try to connect to MySQL
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Database connection failed
    $pdo = null;
    error_log("Database connection failed: " . $e->getMessage());
}

// Site Configuration
define('SITE_NAME', 'TAMCC Mealhouse');
define('SITE_URL', 'https://tamccmealhouse.onrender.com');
define('DEBUG_MODE', true);

// Error reporting
if (defined('DEBUG_MODE') && DEBUG_MODE) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// Timezone
date_default_timezone_set('UTC');

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Helper function to get database connection
function getDBConnection() {
    global $pdo;
    return $pdo;
}
?>