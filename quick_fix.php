<?php
// Quick database setup
$host = "localhost";
$port = "5432";
$dbname = "tamcc_mealhouse";
$username = "tamccmealhouse_user"; // Change to your username
$password = "2DL7YF3IDBJBxxE9Ieaf3tLlqkqFfjNo"; // Change to your password

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Just create the essential tables
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        user_id SERIAL PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        full_name VARCHAR(100),
        role VARCHAR(20) DEFAULT 'student',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Add one test user
    $hash = password_hash('admin123', PASSWORD_DEFAULT);
    $pdo->exec("INSERT INTO users (username, password_hash, email, full_name, role) 
                VALUES ('admin', '$hash', 'admin@tamcc.edu', 'Admin User', 'admin') 
                ON CONFLICT (username) DO NOTHING");
    
    echo "✅ Database setup complete!<br>";
    echo "Login with: admin / admin123";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>