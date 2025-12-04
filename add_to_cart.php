<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_id = $_POST['item_id'];
    $item_name = $_POST['item_name'];
    $item_price = floatval($_POST['item_price']);
    $item_image = $_POST['item_image'];
    $quantity = intval($_POST['quantity']);
    
    // Initialize cart if not exists
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // Check if item already exists in cart
    $item_exists = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $item_id) {
            $item['quantity'] += $quantity;
            $item_exists = true;
            break;
        }
    }
    
    // If item doesn't exist, add it
    if (!$item_exists) {
        $_SESSION['cart'][] = [
            'id' => $item_id,
            'name' => $item_name,
            'price' => $item_price,
            'image' => $item_image,
            'quantity' => $quantity
        ];
    }
    
    // Redirect back to menu with success message
    header('Location: menu.php?added=true');
    exit();
} else {
    header('Location: menu.php');
    exit();
}
?>