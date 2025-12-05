<?php
// Simple test to check Render connection
echo "<h2>Testing Render PostgreSQL Connection</h2>";

if (getenv('DATABASE_URL')) {
    echo "✅ DATABASE_URL environment variable is set<br>";
    $url = parse_url(getenv('DATABASE_URL'));
    
    echo "Host: " . ($url['host'] ?? 'N/A') . "<br>";
    echo "Database: " . (ltrim($url['path'] ?? '', '/')) . "<br>";
    echo "Username: " . ($url['user'] ?? 'N/A') . "<br>";
    echo "Password: " . (isset($url['pass']) ? '***' . substr($url['pass'], -4) : 'N/A') . "<br>";
} else {
    echo "⚠️ DATABASE_URL not set. Using local config.<br>";
}

try {
    require_once 'config.php';
    
    echo "<br>✅ config.php loaded successfully<br>";
    
    // Test query
    $stmt = $pdo->query("SELECT 
        version() as pg_version,
        current_database() as database,
        current_user as username,
        inet_server_addr() as server_ip,
        inet_server_port() as server_port");
    
    $result = $stmt->fetch();
    
    echo "<h3>Connection Details:</h3>";
    echo "<pre>";
    print_r($result);
    echo "</pre>";
    
    echo "<h3>Basic Tables Check:</h3>";
    $stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' ORDER BY table_name");
    $tables = $stmt->fetchAll();
    
    if (empty($tables)) {
        echo "No tables found. Run the setup script.<br>";
    } else {
        echo "Found " . count($tables) . " tables:<br>";
        foreach ($tables as $table) {
            echo "- " . $table['table_name'] . "<br>";
        }
    }
    
} catch (Exception $e) {
    echo "<div style='color: red; padding: 10px; border: 1px solid red;'>
            <strong>Error:</strong> " . $e->getMessage() . "
          </div>";
    
    echo "<h3>Debug Info:</h3>";
    echo "DSN: " . ($dsn ?? 'Not set') . "<br>";
    echo "Username: " . ($username ?? 'Not set') . "<br>";
}
?>