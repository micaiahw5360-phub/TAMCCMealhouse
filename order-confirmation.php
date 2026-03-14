<?php
session_start();
require "config/database.php";

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
if (!$order_id || !isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Fetch order details
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmation | TAMCC Deli</title>
    <link rel="stylesheet" href="assets/css/global.css">
</head>
<body>
    <div class="confirmation-container">
        <div class="success-icon">✅</div>
        <h1>Thank You!</h1>
        <p>Your order #<?= $order['id'] ?> has been placed successfully.</p>
        <p><strong>Total:</strong> $<?= number_format($order['total'], 2) ?></p>
        <p><strong>Payment Status:</strong> <?= ucfirst($order['payment_status']) ?></p>
        <p><strong>Payment Method:</strong> <?= $order['payment_method'] === 'online' ? 'Online Payment' : 'Cash on Pickup' ?></p>
        <?php if ($order['payment_method'] === 'online' && $order['payment_status'] === 'paid'): ?>
            <p>A confirmation email has been sent (simulated).</p>
        <?php endif; ?>
        <a href="dashboard/orders.php" class="btn">View My Orders</a>
        <a href="menu.php" class="btn" style="background: #e67e22;">Order Again</a>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>