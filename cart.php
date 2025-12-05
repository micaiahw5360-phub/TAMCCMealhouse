<?php
// Start session and include config
session_start();
require_once "config.php";

// Set page title
$page_title = "Cart - TAMCC Mealhouse";
require_once 'header.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle cart actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Add item to cart
    if (isset($_POST['add_to_cart'])) {
        $item_id = $_POST['item_id'];
        $item_name = $_POST['item_name'];
        $item_price = $_POST['item_price'];
        $item_quantity = $_POST['quantity'];
        
        // Check if item already exists in cart
        $item_exists = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $item_id) {
                $item['quantity'] += $item_quantity;
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
                'quantity' => $item_quantity
            ];
        }
        
        $success_message = "Item added to cart successfully!";
    }
    
    // Update cart quantities
    if (isset($_POST['update_cart'])) {
        foreach ($_POST['quantity'] as $item_id => $quantity) {
            if ($quantity == 0) {
                // Remove item if quantity is 0
                foreach ($_SESSION['cart'] as $key => $item) {
                    if ($item['id'] == $item_id) {
                        unset($_SESSION['cart'][$key]);
                        break;
                    }
                }
            } else {
                // Update quantity
                foreach ($_SESSION['cart'] as &$item) {
                    if ($item['id'] == $item_id) {
                        $item['quantity'] = $quantity;
                        break;
                    }
                }
            }
        }
        // Reindex array
        $_SESSION['cart'] = array_values($_SESSION['cart']);
        $success_message = "Cart updated successfully!";
    }
    
    // Remove item from cart
    if (isset($_POST['remove_item'])) {
        $item_id = $_POST['item_id'];
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['id'] == $item_id) {
                unset($_SESSION['cart'][$key]);
                break;
            }
        }
        // Reindex array
        $_SESSION['cart'] = array_values($_SESSION['cart']);
        $success_message = "Item removed from cart!";
    }
    
    // Clear cart
    if (isset($_POST['clear_cart'])) {
        $_SESSION['cart'] = [];
        $success_message = "Cart cleared successfully!";
    }
}

// Calculate cart totals
$subtotal = 0;
$tax_rate = 0.10; // 10% tax
$delivery_fee = 2.99; // Fixed delivery fee

foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

$tax = $subtotal * $tax_rate;
$total = $subtotal + $tax + $delivery_fee;
?>

<style>
/* Cart Styles - Updated for Dark Theme */
.cart-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: var(--space-xl);
}

.cart-title {
    font-size: var(--text-3xl);
    font-weight: 600;
    letter-spacing: -1px;
    position: relative;
    display: flex;
    align-items: center;
    padding-left: var(--space-2xl);
    color: var(--accent-blue);
    margin-bottom: var(--space-xl);
}

.cart-title::before,
.cart-title::after {
    position: absolute;
    content: "";
    height: 16px;
    width: 16px;
    border-radius: 50%;
    left: 0;
    background-color: var(--accent-blue);
}

.cart-title::after {
    animation: pulse 1s linear infinite;
}

.cart-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: var(--space-xl);
}

.cart-items {
    background-color: var(--card-bg);
    border-radius: var(--radius-xl);
    padding: var(--space-xl);
    border: 1px solid var(--border-color);
}

.cart-summary {
    background-color: var(--card-bg);
    border-radius: var(--radius-xl);
    padding: var(--space-xl);
    border: 1px solid var(--border-color);
    height: fit-content;
    position: sticky;
    top: 100px;
}

.cart-item {
    display: grid;
    grid-template-columns: 80px 1fr auto auto auto;
    gap: var(--space-md);
    align-items: center;
    padding: var(--space-lg) 0;
    border-bottom: 1px solid var(--border-color);
    transition: all var(--transition-normal);
}

.cart-item:hover {
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--radius-lg);
    padding: var(--space-lg) var(--space-md);
}

.cart-item:last-child {
    border-bottom: none;
}

.item-image {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--navy-light) 0%, var(--grey-light) 100%);
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-light);
    font-size: 1.5rem;
    transition: transform var(--transition-normal);
}

.cart-item:hover .item-image {
    transform: scale(1.1);
}

.item-details h3 {
    margin: 0;
    color: var(--text-light);
    font-size: 1.1rem;
    font-weight: 600;
}

.item-details p {
    margin: var(--space-xs) 0 0;
    color: var(--text-muted);
    font-size: 0.9rem;
}

.item-price {
    color: var(--accent-blue);
    font-weight: 600;
    font-size: 1.1rem;
}

.quantity-controls {
    display: flex;
    align-items: center;
    gap: var(--space-sm);
}

.quantity-btn {
    background-color: var(--grey-dark);
    border: 1px solid var(--border-color);
    color: var(--text-light);
    width: 32px;
    height: 32px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all var(--transition-fast);
}

.quantity-btn:hover {
    background-color: var(--accent-blue);
    transform: scale(1.1);
}

.quantity-input {
    background-color: var(--grey-dark);
    border: 1px solid var(--border-color);
    color: var(--text-light);
    width: 60px;
    text-align: center;
    border-radius: var(--radius-md);
    padding: var(--space-sm);
    font-size: 1rem;
    transition: border-color var(--transition-fast);
}

.quantity-input:focus {
    outline: none;
    border-color: var(--accent-blue);
    box-shadow: 0 0 0 2px rgba(0, 191, 255, 0.1);
}

.remove-btn {
    background: none;
    border: none;
    color: var(--accent-red);
    cursor: pointer;
    font-size: 1.2rem;
    padding: var(--space-sm);
    border-radius: var(--radius-md);
    transition: all var(--transition-fast);
}

.remove-btn:hover {
    background-color: rgba(255, 42, 42, 0.1);
    transform: scale(1.1);
}

.cart-actions {
    display: flex;
    justify-content: space-between;
    margin-top: var(--space-xl);
    gap: var(--space-md);
}

.btn {
    border: none;
    outline: none;
    padding: var(--space-md) var(--space-xl);
    border-radius: var(--radius-lg);
    color: var(--text-light);
    font-size: 1rem;
    cursor: pointer;
    transition: all var(--transition-normal);
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: var(--space-sm);
}

.btn-primary {
    background: linear-gradient(135deg, var(--accent-blue) 0%, #0099cc 100%);
    box-shadow: var(--shadow-glow);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 191, 255, 0.4);
}

.btn-secondary {
    background-color: var(--grey-dark);
    border: 1px solid var(--border-color);
}

.btn-secondary:hover {
    background-color: var(--grey-light);
    transform: translateY(-2px);
}

.btn-danger {
    background: linear-gradient(135deg, var(--accent-red) 0%, #cc0000 100%);
}

.btn-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 42, 42, 0.4);
}

.summary-row {
    display: flex;
    justify-content: space-between;
    padding: var(--space-md) 0;
    border-bottom: 1px solid var(--border-color);
    color: var(--text-muted);
}

.summary-row.total {
    font-weight: 700;
    color: var(--text-light);
    font-size: 1.2rem;
    border-bottom: none;
    margin-top: var(--space-md);
    padding-top: var(--space-lg);
    border-top: 2px solid var(--accent-blue);
}

.summary-title {
    color: var(--text-light);
    font-size: 1.5rem;
    margin-bottom: var(--space-lg);
    text-align: center;
}

.empty-cart {
    text-align: center;
    padding: var(--space-3xl);
    color: var(--text-muted);
}

.empty-cart-icon {
    font-size: 4rem;
    margin-bottom: var(--space-lg);
    opacity: 0.5;
}

.success-message {
    background: rgba(0, 191, 255, 0.1);
    color: var(--accent-blue);
    padding: var(--space-lg);
    border-radius: var(--radius-lg);
    margin-bottom: var(--space-xl);
    border: 1px solid var(--accent-blue);
    text-align: center;
    animation: slideDown 0.3s ease;
}

.continue-shopping {
    display: inline-block;
    margin-top: var(--space-lg);
    color: var(--accent-blue);
    text-decoration: none;
    font-weight: 600;
    transition: all var(--transition-normal);
}

.continue-shopping:hover {
    color: var(--text-light);
    transform: translateX(5px);
}

.checkout-btn {
    width: 100%;
    margin-top: var(--space-xl);
    padding: var(--space-lg);
    font-size: 1.1rem;
    text-decoration: none;
    text-align: center;
}

@keyframes pulse {
    from {
        transform: scale(0.9);
        opacity: 1;
    }
    to {
        transform: scale(1.8);
        opacity: 0;
    }
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive */
@media (max-width: 768px) {
    .cart-grid {
        grid-template-columns: 1fr;
    }
    
    .cart-item {
        grid-template-columns: 60px 1fr auto;
        gap: var(--space-sm);
        padding: var(--space-md) 0;
    }
    
    .item-price, .quantity-controls {
        grid-column: 2;
    }
    
    .remove-btn {
        grid-column: 3;
        grid-row: 1;
    }
    
    .cart-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
    }
}

@media (max-width: 480px) {
    .cart-container {
        padding: var(--space-md);
    }
    
    .cart-title {
        font-size: var(--text-2xl);
        padding-left: var(--space-xl);
    }
    
    .cart-title::before,
    .cart-title::after {
        height: 12px;
        width: 12px;
    }
    
    .item-image {
        width: 60px;
        height: 60px;
        font-size: 1.2rem;
    }
}
</style>

<div class="cart-container">
    <h1 class="cart-title">Your Cart</h1>
    
    <?php if (isset($success_message)): ?>
        <div class="success-message"><?php echo $success_message; ?></div>
    <?php endif; ?>
    
    <div class="cart-grid">
        <!-- Cart Items -->
        <div class="cart-items">
            <?php if (empty($_SESSION['cart'])): ?>
                <div class="empty-cart">
                    <div class="empty-cart-icon">🛒</div>
                    <h3>Your cart is empty</h3>
                    <p>Add some delicious meals from our menu</p>
                    <a href="menu.php" class="continue-shopping">Browse Menu</a>
                </div>
            <?php else: ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                        <div class="cart-item">
                            <div class="item-image">
                                🍽️
                            </div>
                            <div class="item-details">
                                <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                <p>Fresh and delicious meal</p>
                            </div>
                            <div class="item-price">$<?php echo number_format($item['price'], 2); ?></div>
                            <div class="quantity-controls">
                                <button type="button" class="quantity-btn minus" data-id="<?php echo $item['id']; ?>">-</button>
                                <input type="number" name="quantity[<?php echo $item['id']; ?>]" 
                                       class="quantity-input" 
                                       value="<?php echo $item['quantity']; ?>" 
                                       min="0" max="10">
                                <button type="button" class="quantity-btn plus" data-id="<?php echo $item['id']; ?>">+</button>
                            </div>
                            <button type="submit" name="remove_item" class="remove-btn" 
                                    onclick="this.form.querySelector('input[name=\"item_id\"]').value='<?php echo $item['id']; ?>'">
                                ✕
                            </button>
                        </div>
                    <?php endforeach; ?>
                    
                    <input type="hidden" name="item_id" value="">
                    
                    <div class="cart-actions">
                        <button type="submit" name="update_cart" class="btn btn-primary">Update Cart</button>
                        <button type="submit" name="clear_cart" class="btn btn-danger" 
                                onclick="return confirm('Are you sure you want to clear your cart?')">Clear Cart</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
        
        <!-- Cart Summary -->
        <?php if (!empty($_SESSION['cart'])): ?>
            <div class="cart-summary">
                <h2 class="summary-title">Order Summary</h2>
                
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>$<?php echo number_format($subtotal, 2); ?></span>
                </div>
                
                <div class="summary-row">
                    <span>Tax (10%)</span>
                    <span>$<?php echo number_format($tax, 2); ?></span>
                </div>
                
                <div class="summary-row">
                    <span>Delivery Fee</span>
                    <span>$<?php echo number_format($delivery_fee, 2); ?></span>
                </div>
                
                <div class="summary-row total">
                    <span>Total</span>
                    <span>$<?php echo number_format($total, 2); ?></span>
                </div>
                
                <a href="checkout_form.php" class="btn btn-primary checkout-btn">
                    Proceed to Checkout
                    <span style="font-size: 1.2rem;">→</span>
                </a>
                
                <a href="menu.php" class="continue-shopping" style="display: block; text-align: center; margin-top: var(--space-lg);">
                    Continue Shopping
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// JavaScript for quantity controls
document.addEventListener('DOMContentLoaded', function() {
    // Plus button
    document.querySelectorAll('.quantity-btn.plus').forEach(button => {
        button.addEventListener('click', function() {
            const itemId = this.getAttribute('data-id');
            const input = document.querySelector(`input[name="quantity[${itemId}]"]`);
            let value = parseInt(input.value);
            if (value < 10) {
                input.value = value + 1;
                // Add visual feedback
                this.style.backgroundColor = 'var(--accent-blue)';
                setTimeout(() => {
                    this.style.backgroundColor = '';
                }, 200);
            }
        });
    });
    
    // Minus button
    document.querySelectorAll('.quantity-btn.minus').forEach(button => {
        button.addEventListener('click', function() {
            const itemId = this.getAttribute('data-id');
            const input = document.querySelector(`input[name="quantity[${itemId}]"]`);
            let value = parseInt(input.value);
            if (value > 0) {
                input.value = value - 1;
                // Add visual feedback
                this.style.backgroundColor = 'var(--accent-red)';
                setTimeout(() => {
                    this.style.backgroundColor = '';
                }, 200);
            }
        });
    });
    
    // Quantity input validation
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            let value = parseInt(this.value);
            if (isNaN(value) || value < 0) {
                this.value = 0;
            } else if (value > 10) {
                this.value = 10;
            }
        });
        
        input.addEventListener('keydown', function(e) {
            // Prevent negative numbers and allow only numbers
            if (['-', 'e', 'E', '+'].includes(e.key)) {
                e.preventDefault();
            }
        });
    });
    
    // Add hover effects to checkout button
    const checkoutBtn = document.querySelector('.checkout-btn');
    if (checkoutBtn) {
        checkoutBtn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px) scale(1.02)';
        });
        
        checkoutBtn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    }
    
    // Add animation to cart items when they load
    const cartItems = document.querySelectorAll('.cart-item');
    cartItems.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateX(-20px)';
        item.style.transition = `opacity 0.5s ease, transform 0.5s ease ${index * 0.1}s`;
        
        setTimeout(() => {
            item.style.opacity = '1';
            item.style.transform = 'translateX(0)';
        }, 100);
    });
});
</script>

<?php
// Include footer at the end
require_once 'footer.php';
?>