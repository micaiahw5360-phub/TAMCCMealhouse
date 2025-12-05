<?php
require_once 'config.php';

// Read the SQL file
$sql = file_get_contents('tamcc_mealhouse_postgresql.sql');

try {
    // Execute the SQL
    $pdo->exec($sql);
    echo "✅ Database setup completed successfully!<br>";
    echo "All tables created and populated with data.<br>";
    echo "You can now: <a href='login.php'>Login</a> or <a href='index.php'>Go to homepage</a>";
} catch(PDOException $e) {
    echo "❌ Error executing SQL: " . $e->getMessage() . "<br>";
    echo "SQL State: " . $e->getCode();
}
?>