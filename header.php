<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'TAMCC Deli'; ?></title>
    <link rel="stylesheet" href="/tamccdeli/assets/css/global.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/WordPress/WordPress@master/wp-includes/css/dashicons.min.css">
    <!-- PWA manifest – commented out to avoid 404 errors -->
    <!-- <link rel="manifest" href="/tamccdeli/manifest.json"> -->
    <meta name="mobile-web-app-capable" content="yes">
</head>
<body>
    <div class="navbar">
        <a href="/tamccdeli/index.php" class="logo">TAMCC Deli</a>
        <button class="menu-toggle" aria-label="Toggle menu">☰</button>
        <div class="nav-links">
            <a href="/tamccdeli/index.php">Home</a>
            <!-- Dropdown Menu -->
            <div class="dropdown">
                <a href="/tamccdeli/menu.php">Menu ▾</a>
                <div class="dropdown-content">
                    <a href="/tamccdeli/menu.php#breakfast">Breakfast</a>
                    <a href="/tamccdeli/menu.php#alacarte">A La Carte</a>
                    <a href="/tamccdeli/menu.php#combo">Combo</a>
                    <a href="/tamccdeli/menu.php#beverage">Beverage</a>
                    <a href="/tamccdeli/menu.php#dessert">Dessert</a>
                </div>
            </div>
            <a href="/tamccdeli/cart.php"><span class="dashicons dashicons-cart"></span> Cart <span id="cart-count" class="cart-count">0</span></a>

            <!-- Staff/Admin Panel Links -->
            <?php if (isset($_SESSION['user_id']) && isset($_SESSION['role'])): ?>
                <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'staff'): ?>
                    <a href="/tamccdeli/staff/orders.php"><span class="dashicons dashicons-clipboard"></span> Staff Panel</a>
                <?php endif; ?>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="/tamccdeli/admin/index.php"><span class="dashicons dashicons-admin-tools"></span> Admin Panel</a>
                <?php endif; ?>
            <?php endif; ?>

            <!-- User links -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/tamccdeli/dashboard/index.php"><span class="dashicons dashicons-dashboard"></span> Dashboard</a>
                <a href="/tamccdeli/auth/logout.php"><span class="dashicons dashicons-exit"></span> Logout</a>
            <?php else: ?>
                <a href="/tamccdeli/auth/login.php"><span class="dashicons dashicons-lock"></span> Login</a>
                <a href="/tamccdeli/auth/register.php"><span class="dashicons dashicons-edit"></span> Register</a>
            <?php endif; ?>
        </div>
    </div>