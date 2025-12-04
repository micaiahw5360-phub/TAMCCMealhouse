<?php
session_start();
require_once "config.php";

// Initialize variables to avoid undefined errors
$order_data = [];
$ordered_items = [];
$subtotal = 0;
$tax = 0;
$delivery_fee = 2.99;
$total = 0;
$is_online_payment = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_SESSION['cart'])) {
    // Check which payment method was selected
    $payment_method = $_POST['payment_method'] ?? 'cash';
    
    // Process the order
    $order_data = [
        'customer_name' => $_POST['customer_name'] ?? 'Not Provided',
        'customer_email' => $_POST['customer_email'] ?? 'Not Provided',
        'customer_phone' => $_POST['customer_phone'] ?? 'Not Provided',
        'special_instructions' => $_POST['special_instructions'] ?? '',
        'order_total' => $_POST['order_total'] ?? 0,
        'payment_method' => $payment_method,
        'order_date' => date('Y-m-d H:i:s'),
        'order_id' => 'TAMCC-' . time() . '-' . rand(1000, 9999)
    ];
    
    // Store order data in session
    $_SESSION['last_order'] = $order_data;
    
    // Store cart items before clearing
    $ordered_items = $_SESSION['cart'];
    
    // Clear the cart after successful order
    $_SESSION['cart'] = [];
    
    // Calculate totals for receipt
    $tax_rate = 0.10;
    
    foreach ($ordered_items as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }
    
    $tax = $subtotal * $tax_rate;
    $total = $subtotal + $tax + $delivery_fee;
    
    // Check if online payment was selected
    if ($payment_method === 'online') {
        $is_online_payment = true;
    }
    
} else {
    // Redirect if no cart items or not POST request
    if (empty($_SESSION['cart'])) {
        header("Location: menu.php?error=empty_cart");
        exit;
    } else {
        // If accessed directly without POST, show error
        $page_title = "Invalid Access - TAMCC Mealhouse";
        require_once 'header.php';
        echo "<div class='container' style='color: white; text-align: center; padding: 50px;'>
                <h2>Invalid Access</h2>
                <p>Please complete the checkout form to view your receipt.</p>
                <a href='menu.php' class='btn btn-primary'>Return to Menu</a>
              </div>";
        require_once 'footer.php';
        exit;
    }
}

$page_title = "Order Receipt - TAMCC Mealhouse";
require_once 'header.php';

// If online payment, show JavaScript redirect
if ($is_online_payment) {
    echo "
    <script>
        window.onload = function() {
            window.location.href = 'online_pay.php';
        }
    </script>
    <div style='color: white; text-align: center; padding: 50px;'>
        <h2>Redirecting to Payment...</h2>
        <p>Please wait while we redirect you to the secure payment page.</p>
    </div>";
    require_once 'footer.php';
    exit;
}
?>

<style>
.receipt-container {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
}

.receipt {
    background-color: #1a1a1a;
    border-radius: 20px;
    padding: 30px;
    border: 1px solid #333;
    color: #fff;
}

.receipt-header {
    text-align: center;
    margin-bottom: 30px;
    border-bottom: 2px solid #00bfff;
    padding-bottom: 20px;
}

.receipt-title {
    font-size: 28px;
    font-weight: 600;
    color: #00bfff;
    margin-bottom: 10px;
}

.receipt-subtitle {
    color: rgba(255, 255, 255, 0.7);
    margin-bottom: 5px;
}

.order-id {
    font-size: 18px;
    font-weight: 600;
    color: #00bfff;
    margin: 10px 0;
}

.receipt-section {
    margin-bottom: 25px;
}

.section-title {
    color: #00bfff;
    font-size: 18px;
    margin-bottom: 15px;
    border-bottom: 1px solid #333;
    padding-bottom: 5px;
}

.customer-info p, .order-info p {
    margin: 5px 0;
    color: rgba(255, 255, 255, 0.8);
}

.order-items {
    width: 100%;
    border-collapse: collapse;
}

.order-items th {
    text-align: left;
    padding: 10px 0;
    border-bottom: 1px solid #333;
    color: #00bfff;
}

.order-items td {
    padding: 10px 0;
    border-bottom: 1px solid #333;
}

.order-items tr:last-child td {
    border-bottom: none;
}

.item-name {
    text-align: left;
}

.item-quantity, .item-price {
    text-align: center;
}

.item-total {
    text-align: right;
}

.totals-table {
    width: 100%;
    margin-top: 20px;
}

.totals-table td {
    padding: 8px 0;
    border-bottom: 1px solid #333;
}

.totals-table tr:last-child td {
    border-bottom: none;
    font-weight: 600;
    font-size: 18px;
    color: #00bfff;
}

.amount {
    text-align: right;
}

.thank-you {
    text-align: center;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 2px solid #00bfff;
    color: rgba(255, 255, 255, 0.8);
}

.print-btn {
    background-color: #00bfff;
    color: #1a1a1a;
    border: none;
    padding: 12px 25px;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    margin-top: 20px;
    transition: background-color 0.3s ease;
}

.print-btn:hover {
    background-color: #00bfff96;
}

.receipt-actions {
    text-align: center;
    margin-top: 30px;
}

.continue-btn {
    display: inline-block;
    background-color: #333;
    color: #fff;
    padding: 10px 20px;
    border-radius: 10px;
    text-decoration: none;
    margin-left: 10px;
    transition: background-color 0.3s ease;
}

.continue-btn:hover {
    background-color: #444;
}

/* Payment Method Styles */
.payment-method {
    background-color: #00bfff;
    color: #1a1a1a;
    padding: 8px 15px;
    border-radius: 8px;
    font-weight: 600;
    display: inline-block;
    margin-top: 10px;
}
</style>

<div class="receipt-container">
    <div class="receipt">
        <div class="receipt-header">
            <h1 class="receipt-title">TAMCC Mealhouse</h1>
            <p class="receipt-subtitle">Campus Dining Excellence</p>
            <p class="receipt-subtitle">TAMCC Campus, St. George's, Grenada</p>
            <p class="receipt-subtitle">Tel: (473) 444-1234</p>
            <div class="order-id">Order #: <?php echo htmlspecialchars($order_data['order_id']); ?></div>
            <p>Date: <?php echo date('F j, Y g:i A', strtotime($order_data['order_date'])); ?></p>
            <div class="payment-method">
                Payment Method: <?php echo $order_data['payment_method'] === 'online' ? 'Online Payment' : 'Cash on Pickup'; ?>
            </div>
        </div>
        
        <div class="receipt-section">
            <h2 class="section-title">Customer Information</h2>
            <div class="customer-info">
                <p><strong>Name:</strong> <?php echo htmlspecialchars($order_data['customer_name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($order_data['customer_email']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($order_data['customer_phone']); ?></p>
                <?php if (!empty($order_data['special_instructions'])): ?>
                    <p><strong>Special Instructions:</strong> <?php echo htmlspecialchars($order_data['special_instructions']); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="receipt-section">
            <h2 class="section-title">Order Details</h2>
            <table class="order-items">
                <thead>
                    <tr>
                        <th class="item-name">Item</th>
                        <th class="item-quantity">Qty</th>
                        <th class="item-price">Price</th>
                        <th class="item-total">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($ordered_items)): ?>
                        <?php foreach ($ordered_items as $item): ?>
                            <tr>
                                <td class="item-name"><?php echo htmlspecialchars($item['name']); ?></td>
                                <td class="item-quantity"><?php echo $item['quantity']; ?></td>
                                <td class="item-price">$<?php echo number_format($item['price'], 2); ?></td>
                                <td class="item-total">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: #ff6b6b;">No items in order</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <table class="totals-table">
                <tr>
                    <td>Subtotal:</td>
                    <td class="amount">$<?php echo number_format($subtotal, 2); ?></td>
                </tr>
                <tr>
                    <td>Tax (10%):</td>
                    <td class="amount">$<?php echo number_format($tax, 2); ?></td>
                </tr>
                <tr>
                    <td>Delivery Fee:</td>
                    <td class="amount">$<?php echo number_format($delivery_fee, 2); ?></td>
                </tr>
                <tr>
                    <td><strong>Total:</strong></td>
                    <td class="amount"><strong>$<?php echo number_format($total, 2); ?></strong></td>
                </tr>
            </table>
        </div>
        
        <div class="thank-you">
            <p><strong>Thank you for your order!</strong></p>
            <p>Your food is being prepared and will be ready for pickup shortly.</p>
            <p>Estimated preparation time: 20-35 minutes</p>
            <?php if ($order_data['payment_method'] === 'cash'): ?>
                <p><strong>Please pay with cash when you pick up your order.</strong></p>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="receipt-actions">
        <button class="print-btn" onclick="window.print()">Print Receipt</button>
        <a href="menu.php" class="continue-btn">Order Again</a>
    </div>
</div>

<script>
// Auto-print option (optional)
// window.onload = function() {
//     window.print();
// };
</script>

<?php
require_once 'footer.php';
?>