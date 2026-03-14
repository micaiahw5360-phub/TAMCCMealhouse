<?php
session_start();
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/csrf.php';

if (empty($_SESSION['cart'])) {
    header("Location: menu.php");
    exit;
}

// Fetch cart items
$ids = array_keys($_SESSION['cart']);
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$stmt = $conn->prepare("SELECT * FROM menu_items WHERE id IN ($placeholders)");
$stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = [];
$total = 0;
while ($row = $result->fetch_assoc()) {
    $row['quantity'] = $_SESSION['cart'][$row['id']];
    $row['subtotal'] = $row['price'] * $row['quantity'];
    $total += $row['subtotal'];
    $cart_items[] = $row;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateToken($_POST['csrf_token'])) {
        die('Invalid CSRF token');
    }

    $pickup_time = $_POST['pickup_time'] ? date('Y-m-d H:i:s', strtotime($_POST['pickup_time'])) : null;
    $instructions = trim($_POST['instructions']);
    $payment_method = $_POST['payment_method'];

    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $guest_email = null;
    if (!$user_id) {
        $guest_email = trim($_POST['guest_email']);
        if (!filter_var($guest_email, FILTER_VALIDATE_EMAIL)) {
            $error = "Valid email required for guest checkout.";
        }
    }

    if (!$error) {
        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare("INSERT INTO orders (user_id, guest_email, total, pickup_time, special_instructions, payment_status, payment_method) 
                                     VALUES (?, ?, ?, ?, ?, 'unpaid', ?)");
            $stmt->bind_param("isdsss", $user_id, $guest_email, $total, $pickup_time, $instructions, $payment_method);
            $stmt->execute();
            $order_id = $conn->insert_id;

            $stmt2 = $conn->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity, price) VALUES (?, ?, ?, ?)");
            foreach ($cart_items as $item) {
                $stmt2->bind_param("iiid", $order_id, $item['id'], $item['quantity'], $item['price']);
                $stmt2->execute();
            }

            // Award points if user is logged in
            if ($user_id) {
                $points_earned = floor($total);
                $update_points = $conn->prepare("UPDATE users SET points = points + ? WHERE id = ?");
                $update_points->bind_param("ii", $points_earned, $user_id);
                $update_points->execute();

                $update_order = $conn->prepare("UPDATE orders SET points_earned = ? WHERE id = ?");
                $update_order->bind_param("ii", $points_earned, $order_id);
                $update_order->execute();
            }

            $conn->commit();
            $_SESSION['cart'] = [];

            if ($user_id) {
                if ($payment_method === 'online') {
                    $_SESSION['pending_order'] = $order_id;
                    header("Location: payment.php");
                    exit;
                } else {
                    header("Location: order-confirmation.php?order_id=$order_id");
                    exit;
                }
            } else {
                $_SESSION['guest_order'] = $order_id;
                $_SESSION['guest_email'] = $guest_email;
                header("Location: guest-thanks.php");
                exit;
            }
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Failed to place order: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Checkout | TAMCC Deli</title>
    <link rel="stylesheet" href="assets/css/global.css">
    <style>
        .loading { display: none; }
        .btn.loading .btn-text { display: none; }
        .btn.loading .loading { display: inline-block; }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="checkout-container">
        <h1>Checkout</h1>
        <?php if ($error): ?><div class="error-message"><?= $error ?></div><?php endif; ?>

        <div class="order-summary">
            <h3>Order Summary</h3>
            <table>
                <thead><tr><th>Item</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr></thead>
                <tbody>
                <?php foreach ($cart_items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>$<?= number_format($item['price'], 2) ?></td>
                        <td>$<?= number_format($item['subtotal'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <div class="total">Total: $<?= number_format($total, 2) ?></div>
        </div>

        <form method="POST" id="checkout-form">
            <input type="hidden" name="csrf_token" value="<?= generateToken() ?>">

            <?php if (!isset($_SESSION['user_id'])): ?>
                <div class="form-group">
                    <label for="guest_email">Your Email (for order confirmation)</label>
                    <input type="email" id="guest_email" name="guest_email" required>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label>Pickup Time (optional)</label>
                <input type="datetime-local" name="pickup_time">
            </div>
            <div class="form-group">
                <label>Special Instructions</label>
                <textarea name="instructions" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>Payment Method</label>
                <select name="payment_method" required>
                    <option value="cash">Cash on Pickup</option>
                    <option value="online">Online Payment (Simulated)</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" id="place-order-btn">
                <span class="btn-text">Place Order</span>
                <span class="loading">⏳</span>
            </button>
            <a href="cart.php" class="btn">Return to Cart</a>
        </form>
    </div>

    <script>
        document.getElementById('checkout-form').addEventListener('submit', function() {
            const btn = document.getElementById('place-order-btn');
            btn.classList.add('loading');
            btn.disabled = true;
        });
    </script>

    <?php include 'includes/footer.php'; ?>
</body>
</html>