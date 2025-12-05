<?php
// TAMCC Mealhouse Configuration File

// Database Configuration (if needed)
define('DB_HOST', 'localhost');
define('DB_NAME', 'tamcc_mealhouse');
define('DB_USER', 'root');
define('DB_PASS', '');

// Site Configuration
define('SITE_NAME', 'TAMCC Mealhouse');
define('SITE_URL', 'https://tamccmealhouse.onrender.com');
define('SITE_EMAIL', 'contact@tamccmealhouse.com');

// Display errors (only for development)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('UTC');

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Simple database connection function (if needed)
function getDB() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            return null;
        }
        return $conn;
    } catch(Exception $e) {
        return null;
    }
}
?>