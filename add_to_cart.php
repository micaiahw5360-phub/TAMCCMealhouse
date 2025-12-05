<?php
session_start();
require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_id = $_POST['item_id'] ?? null;
    $item_name = $_POST['item_name'] ?? 'Unknown Item';
    $item_price = floatval($_POST['item_price'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 1);
    $is_special = isset($_POST['is_special']) ? true : false;
    $is_combo = isset($_POST['is_combo']) ? true : false;
    
    if ($quantity < 1) $quantity = 1;
    if ($quantity > 10) $quantity = 10;
    
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    $item_exists = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $item_id && $item['type'] == ($is_special ? 'special' : ($is_combo ? 'combo' : 'regular'))) {
            $item['quantity'] += $quantity;
            $item_exists = true;
            break;
        }
    }
    
    if (!$item_exists) {
        $_SESSION['cart'][] = [
            'id' => $item_id,
            'name' => $item_name,
            'price' => $item_price,
            'quantity' => $quantity,
            'type' => $is_special ? 'special' : ($is_combo ? 'combo' : 'regular')
        ];
    }
    
    header('Location: menu.php?added=true');
    exit();
} else {
    header('Location: menu.php');
    exit();
}
?>