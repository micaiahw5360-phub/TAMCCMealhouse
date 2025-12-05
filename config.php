<?php
if (getenv('RENDER') || getenv('DATABASE_URL')) {
    $db_url = getenv('DATABASE_URL');
    
    if ($db_url) {
        $url = parse_url($db_url);
        $db_host = $url['host'];
        $db_port = $url['port'] ?? 5432;
        $db_name = ltrim($url['path'], '/');
        $db_user = $url['user'];
        $db_pass = $url['pass'];
    } else {
        $db_host = getenv('DB_HOST') ?: 'localhost';
        $db_port = getenv('DB_PORT') ?: 5432;
        $db_name = getenv('DB_NAME') ?: 'tamccmealhouse';
        $db_user = getenv('DB_USER') ?: 'tamccmealhouse_user';
        $db_pass = getenv('DB_PASSWORD') ?: '';
    }
} else {
    $db_host = 'localhost';
    $db_port = 5432;
    $db_name = 'tamccmealhouse';
    $db_user = 'postgres';
    $db_pass = 'your_local_password';
}

define('SITE_NAME', 'TAMCC Mealhouse');
define('SITE_URL', 'https://tamccmealhouse.onrender.com');
define('SITE_EMAIL', 'contact@tamccmealhouse.com');

define('DEBUG_MODE', true);
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

date_default_timezone_set('UTC');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

try {
    $dsn = "pgsql:host=$db_host;port=$db_port;dbname=$db_name;";
    
    $pdo = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_PERSISTENT => false
    ]);
    
    $pdo->exec("SET TIME ZONE 'UTC'");
    $pdo->query("SELECT 1");
    $database_connected = true;
    
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    $pdo = null;
    $database_connected = false;
}

function getDB() {
    global $pdo;
    return $pdo;
}

function getCurrentDate() {
    return date('Y-m-d');
}

function pgCurrentDate() {
    return "CURRENT_DATE";
}
?>