<?php
session_start();
 
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - TAMCC Mealhouse</title>
    <link rel="stylesheet" href="tamcc-mealhouse-style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php 
    require_once 'header.php';
?>
    <!-- Main Content -->
    <main class="page-container">
        <section class="section text-center">
            <div class="card">
                <div class="card-body">
                    <h1 class="h2 mb-lg">Welcome, <span class="text-primary"><?php echo htmlspecialchars($_SESSION["username"]); ?></span>!</h1>
                    <p class="text-lead text-muted mb-xl">
                        Welcome to TAMCC Mealhouse - Your campus dining solution
                    </p>
                    
                    <div class="grid grid-2" style="max-width: 600px; margin: 0 auto;">
                        <a href="reset-password.php" class="btn btn-warning btn-lg">
                            🔒 Reset Password
                        </a>
                        <a href="logout.php" class="btn btn-danger btn-lg">
                            🚪 Sign Out
                        </a>
                    </div>
                    
                    <div class="mt-3xl">
                        <h3 class="h4 mb-lg">Quick Actions</h3>
                        <div class="grid grid-3">
                            <a href="menu.php" class="card p-xl text-center">
                                <div class="text-primary" style="font-size: 2rem;">📖</div>
                                <h4 class="h5 mt-md">View Menu</h4>
                            </a>
                            <a href="cart.php" class="card p-xl text-center">
                                <div class="text-primary" style="font-size: 2rem;">🛒</div>
                                <h4 class="h5 mt-md">My Cart</h4>
                            </a>
                            <a href="checkout.php" class="card p-xl text-center">
                                <div class="text-primary" style="font-size: 2rem;">📦</div>
                                <h4 class="h5 mt-md">Checkout</h4>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

<?php
// Include footer at the end
require_once 'footer.php';
?>
</body>
</html>