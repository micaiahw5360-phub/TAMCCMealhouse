<?php
session_start();
require 'config/database.php';

if (!isset($_SESSION['guest_order'])) {
    header('Location: index.php');
    exit;
}

$order_id = $_SESSION['guest_order'];
$email = $_SESSION['guest_email'] ?? '';

$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
if (!$order) {
    header('Location: index.php');
    exit;
}

$page_title = "Thank You";
include 'includes/header.php';
?>

<div class="container">
    <div class="card" style="text-align:center; max-width:600px; margin:0 auto;">
        <h1>Thank You for Your Order!</h1>
        <p>Your order #<?= $order_id ?> has been placed.</p>
        <p>We've sent a confirmation to <?= htmlspecialchars($email) ?>.</p>
        <hr>
        <h2>Create an Account</h2>
        <p>Save your order history and speed up future checkouts.</p>
        <form method="post" action="auth/register.php">
            <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
            <button type="submit" class="btn btn-primary">Register Now</button>
        </form>
        <a href="menu.php" class="btn">Continue Browsing</a>
    </div>
</div>

<?php
unset($_SESSION['guest_order'], $_SESSION['guest_email']);
include 'includes/footer.php';