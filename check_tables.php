<?php
require_once 'config.php';

$tables = ['users', 'menu_items', 'categories', 'orders', 'order_items', 'combo_meals', 'specials'];

echo "<h2>Checking Database Tables</h2>";

foreach ($tables as $table) {
    try {
        $stmt = $pdo->query("SELECT EXISTS (SELECT FROM information_schema.tables WHERE table_name = '$table')");
        $exists = $stmt->fetchColumn();
        
        if ($exists) {
            echo "✅ <strong>$table</strong> table exists<br>";
            
            // Count rows
            $count_stmt = $pdo->query("SELECT COUNT(*) FROM $table");
            $count = $count_stmt->fetchColumn();
            echo "&nbsp;&nbsp;&nbsp;&nbsp;Rows: $count<br>";
        } else {
            echo "❌ <strong>$table</strong> table is MISSING<br>";
        }
    } catch (Exception $e) {
        echo "❌ <strong>$table</strong> - Error checking: " . $e->getMessage() . "<br>";
    }
    echo "<hr>";
}
?>