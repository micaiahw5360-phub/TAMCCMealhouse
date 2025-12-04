<?php
session_start();
require_once "config.php";

$page_title = "Checkout - TAMCC Mealhouse";
require_once 'header.php';

// Check if cart is empty
if (empty($_SESSION['cart'])) {
    header("Location: menu.php?error=empty_cart");
    exit;
}

// Calculate cart total
$subtotal = 0;
$tax_rate = 0.10;
$delivery_fee = 2.99;

foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

$tax = $subtotal * $tax_rate;
$total = $subtotal + $tax + $delivery_fee;
?>

<style>
.checkout-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.checkout-form {
    background-color: #1a1a1a;
    border-radius: 20px;
    padding: 30px;
    border: 1px solid #333;
    color: #fff;
}

.form-section {
    margin-bottom: 30px;
}

.section-title {
    color: #00bfff;
    font-size: 20px;
    margin-bottom: 20px;
    font-weight: 600;
}

.form-row {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
}

.form-group {
    flex: 1;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: rgba(255, 255, 255, 0.9);
    font-weight: 600;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #333;
    border-radius: 10px;
    background-color: #252525;
    color: #fff;
    font-size: 16px;
}

.form-control:focus {
    outline: none;
    border-color: #00bfff;
}

.payment-options {
    display: flex;
    gap: 20px;
    margin-top: 15px;
}

.payment-option {
    flex: 1;
    text-align: center;
}

.payment-option input[type="radio"] {
    display: none;
}

.payment-option label {
    display: block;
    padding: 20px;
    background-color: #252525;
    border: 2px solid #333;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.payment-option input[type="radio"]:checked + label {
    border-color: #00bfff;
    background-color: #003366;
}

.payment-icon {
    font-size: 24px;
    margin-bottom: 10px;
}

.order-summary {
    background-color: #252525;
    border-radius: 15px;
    padding: 20px;
    margin-top: 30px;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    padding-bottom: 10px;
    border-bottom: 1px solid #333;
}

.summary-total {
    display: flex;
    justify-content: space-between;
    font-size: 18px;
    font-weight: 600;
    color: #00bfff;
    margin-top: 15px;
    padding-top: 15px;
    border-top: 2px solid #333;
}

.submit-btn {
    width: 100%;
    background-color: #00bfff;
    color: #1a1a1a;
    border: none;
    padding: 15px;
    border-radius: 10px;
    font-size: 18px;
    font-weight: 600;
    cursor: pointer;
    margin-top: 20px;
    transition: background-color 0.3s ease;
}

.submit-btn:hover {
    background-color: #00bfff96;
}

.cart-items {
    margin-bottom: 20px;
}

.cart-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    padding-bottom: 10px;
    border-bottom: 1px solid #333;
}
</style>

<div class="checkout-container">
    <h1 style="text-align: center; color: #00bfff; margin-bottom: 30px;">Checkout</h1>
    
    <div class="checkout-form">
        <form method="POST" action="checkout.php">
            <div class="form-section">
                <h2 class="section-title">Customer Information</h2>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="customer_name">Full Name *</label>
                        <input type="text" id="customer_name" name="customer_name" class="form-control" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="customer_email">Email Address *</label>
                        <input type="email" id="customer_email" name="customer_email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="customer_phone">Phone Number *</label>
                        <input type="tel" id="customer_phone" name="customer_phone" class="form-control" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="special_instructions">Special Instructions (Optional)</label>
                        <textarea id="special_instructions" name="special_instructions" class="form-control" rows="4" placeholder="Any special requests or dietary restrictions..."></textarea>
                    </div>
                </div>
            </div>
            
            <div class="form-section">
                <h2 class="section-title">Payment Method</h2>
                
                <div class="payment-options">
                    <div class="payment-option">
                        <input type="radio" id="cash" name="payment_method" value="cash" checked>
                        <label for="cash">
                            <div class="payment-icon">💵</div>
                            <div>Cash on Pickup</div>
                            <small>Pay when you collect your order</small>
                        </label>
                    </div>
                    
                    <div class="payment-option">
                        <input type="radio" id="online" name="payment_method" value="online">
                        <label for="online">
                            <div class="payment-icon">💳</div>
                            <div>Online Payment</div>
                            <small>Pay securely online</small>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="form-section">
                <h2 class="section-title">Order Summary</h2>
                
                <div class="order-summary">
                    <div class="cart-items">
                        <?php foreach ($_SESSION['cart'] as $item): ?>
                            <div class="cart-item">
                                <span><?php echo htmlspecialchars($item['name']); ?> x <?php echo $item['quantity']; ?></span>
                                <span>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="summary-item">
                        <span>Subtotal:</span>
                        <span>$<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <div class="summary-item">
                        <span>Tax (10%):</span>
                        <span>$<?php echo number_format($tax, 2); ?></span>
                    </div>
                    <div class="summary-item">
                        <span>Delivery Fee:</span>
                        <span>$<?php echo number_format($delivery_fee, 2); ?></span>
                    </div>
                    <div class="summary-total">
                        <span>Total:</span>
                        <span>$<?php echo number_format($total, 2); ?></span>
                    </div>
                </div>
                
                <input type="hidden" name="order_total" value="<?php echo $total; ?>">
            </div>
            
            <button type="submit" class="submit-btn">Place Order</button>
        </form>
    </div>
</div>

<?php
require_once 'footer.php';
?>