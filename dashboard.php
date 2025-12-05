<?php
session_start();
require_once "config.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$page_title = "Dashboard - TAMCC Mealhouse";
require_once 'header.php';

// Get user information
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Fetch user's recent orders from database
$recent_orders = [];
$order_count = 0;
$total_spent = 0;

// In a real application, you would fetch this from the database
// For demo purposes, we'll use sample data
$sample_orders = [
    [
        'order_id' => 'TAMCC-' . (time() - 86400),
        'order_date' => date('Y-m-d H:i:s', time() - 86400),
        'total_amount' => 24.97,
        'status' => 'completed',
        'items' => ['Beef Burger', 'Soda', 'Chocolate Cake']
    ],
    [
        'order_id' => 'TAMCC-' . (time() - 172800),
        'order_date' => date('Y-m-d H:i:s', time() - 172800),
        'total_amount' => 18.99,
        'status' => 'completed',
        'items' => ['Grilled Chicken', 'Fresh Juice']
    ],
    [
        'order_id' => 'TAMCC-' . (time() - 259200),
        'order_date' => date('Y-m-d H:i:s', time() - 259200),
        'total_amount' => 32.50,
        'status' => 'completed',
        'items' => ['Student Meal Deal', 'Mozzarella Sticks', 'Ice Cream']
    ]
];

$recent_orders = $sample_orders;
$order_count = count($recent_orders);
foreach ($recent_orders as $order) {
    $total_spent += $order['total_amount'];
}

// Calculate user stats
$avg_order_value = $order_count > 0 ? $total_spent / $order_count : 0;
$member_since = '2024'; // This would come from the database
?>

<style>
.dashboard-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: var(--space-xl);
}

.dashboard-title {
    font-size: var(--text-3xl);
    font-weight: 600;
    letter-spacing: -1px;
    position: relative;
    display: flex;
    align-items: center;
    padding-left: var(--space-2xl);
    color: var(--primary);
    margin-bottom: var(--space-xl);
}

.dashboard-title::before,
.dashboard-title::after {
    position: absolute;
    content: "";
    height: 16px;
    width: 16px;
    border-radius: 50%;
    left: 0;
    background-color: var(--primary);
}

.dashboard-title::after {
    animation: pulse 1s linear infinite;
}

.welcome-section {
    background: linear-gradient(135deg, var(--primary) 0%, var(--sb-dark-green) 100%);
    color: white;
    border-radius: var(--radius-xl);
    padding: var(--space-2xl);
    margin-bottom: var(--space-2xl);
    position: relative;
    overflow: hidden;
}

.welcome-section::before {
    content: "";
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 200%;
    background: rgba(255,255,255,0.1);
    transform: rotate(45deg);
}

.welcome-content {
    position: relative;
    z-index: 2;
}

.welcome-text {
    font-size: var(--text-2xl);
    font-weight: 600;
    margin-bottom: var(--space-md);
}

.welcome-subtext {
    opacity: 0.9;
    margin-bottom: var(--space-lg);
}

.dashboard-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: var(--space-2xl);
    margin-bottom: var(--space-2xl);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--space-lg);
    margin-bottom: var(--space-2xl);
}

.stat-card {
    background: var(--card-bg);
    border-radius: var(--radius-lg);
    padding: var(--space-xl);
    text-align: center;
    border: 1px solid var(--border-color);
    transition: all var(--transition-normal);
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
    border-color: var(--primary);
}

.stat-number {
    font-size: var(--text-3xl);
    font-weight: 700;
    color: var(--primary);
    margin-bottom: var(--space-sm);
}

.stat-label {
    color: var(--text-muted);
    font-size: var(--text-sm);
    font-weight: 500;
}

.quick-actions {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--space-md);
    margin-bottom: var(--space-2xl);
}

.action-card {
    background: var(--card-bg);
    border-radius: var(--radius-lg);
    padding: var(--space-lg);
    text-align: center;
    border: 1px solid var(--border-color);
    transition: all var(--transition-normal);
    text-decoration: none;
    color: inherit;
}

.action-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-md);
    border-color: var(--primary);
    color: inherit;
}

.action-icon {
    font-size: 2.5rem;
    margin-bottom: var(--space-md);
    display: block;
}

.action-title {
    font-size: var(--text-lg);
    font-weight: 600;
    margin-bottom: var(--space-sm);
    color: var(--text-dark);
}

.action-description {
    color: var(--text-muted);
    font-size: var(--text-sm);
}

.section {
    background: var(--card-bg);
    border-radius: var(--radius-xl);
    padding: var(--space-2xl);
    border: 1px solid var(--border-color);
    margin-bottom: var(--space-2xl);
}

.section-title {
    font-size: var(--text-xl);
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: var(--space-xl);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.section-title a {
    font-size: var(--text-sm);
    font-weight: 500;
    color: var(--primary);
    text-decoration: none;
}

.orders-list {
    display: flex;
    flex-direction: column;
    gap: var(--space-lg);
}

.order-item {
    display: grid;
    grid-template-columns: 1fr auto auto;
    gap: var(--space-lg);
    align-items: center;
    padding: var(--space-lg);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg);
    transition: all var(--transition-fast);
}

.order-item:hover {
    border-color: var(--primary);
    box-shadow: var(--shadow-sm);
}

.order-info h4 {
    margin: 0 0 var(--space-xs) 0;
    color: var(--text-dark);
    font-size: var(--text-base);
}

.order-info p {
    margin: 0;
    color: var(--text-muted);
    font-size: var(--text-sm);
}

.order-items {
    color: var(--text-muted);
    font-size: var(--text-sm);
}

.order-amount {
    font-weight: 600;
    color: var(--primary);
    font-size: var(--text-lg);
}

.order-status {
    padding: var(--space-xs) var(--space-md);
    border-radius: var(--radius-full);
    font-size: var(--text-xs);
    font-weight: 600;
    text-transform: uppercase;
}

.status-completed {
    background: var(--success-light);
    color: var(--success);
}

.status-pending {
    background: var(--warning-light);
    color: var(--warning);
}

.status-cancelled {
    background: var(--error-light);
    color: var(--error);
}

.rewards-section {
    background: linear-gradient(135deg, var(--sb-cream) 0%, var(--sb-light-green) 100%);
    border-radius: var(--radius-xl);
    padding: var(--space-2xl);
    text-align: center;
}

.rewards-title {
    font-size: var(--text-xl);
    font-weight: 600;
    color: var(--sb-dark-green);
    margin-bottom: var(--space-md);
}

.rewards-points {
    font-size: var(--text-3xl);
    font-weight: 700;
    color: var(--primary);
    margin-bottom: var(--space-lg);
}

.rewards-description {
    color: var(--text-muted);
    margin-bottom: var(--space-lg);
}

.empty-state {
    text-align: center;
    padding: var(--space-3xl);
    color: var(--text-muted);
}

.empty-icon {
    font-size: 4rem;
    margin-bottom: var(--space-lg);
    opacity: 0.5;
}

.profile-info {
    display: grid;
    gap: var(--space-lg);
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--space-md) 0;
    border-bottom: 1px solid var(--border-color);
}

.info-label {
    color: var(--text-muted);
    font-weight: 500;
}

.info-value {
    color: var(--text-dark);
    font-weight: 600;
}

.edit-profile-btn {
    background: var(--primary);
    color: white;
    border: none;
    padding: var(--space-md) var(--space-lg);
    border-radius: var(--radius-lg);
    cursor: pointer;
    font-weight: 600;
    transition: all var(--transition-normal);
    margin-top: var(--space-lg);
    width: 100%;
}

.edit-profile-btn:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
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

/* Responsive Design */
@media (max-width: 1024px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
        gap: var(--space-xl);
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .dashboard-container {
        padding: var(--space-md);
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .quick-actions {
        grid-template-columns: 1fr;
    }
    
    .order-item {
        grid-template-columns: 1fr;
        gap: var(--space-md);
        text-align: center;
    }
    
    .welcome-text {
        font-size: var(--text-xl);
    }
}

@media (max-width: 480px) {
    .dashboard-title {
        font-size: var(--text-2xl);
        padding-left: var(--space-xl);
    }
    
    .dashboard-title::before,
    .dashboard-title::after {
        height: 12px;
        width: 12px;
    }
    
    .section {
        padding: var(--space-xl);
    }
}
</style>

<div class="dashboard-container">
    <!-- Welcome Section -->
    <div class="welcome-section">
        <div class="welcome-content">
            <h1 class="welcome-text">Welcome back, <?php echo htmlspecialchars($username); ?>! 👋</h1>
            <p class="welcome-subtext">Here's what's happening with your TAMCC Mealhouse account today.</p>
            <div class="flex gap-md" style="flex-wrap: wrap;">
                <a href="menu.php" class="btn btn-light">Order Food</a>
                <a href="cart.php" class="btn btn-outline-light">View Cart</a>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?php echo $order_count; ?></div>
            <div class="stat-label">Total Orders</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">$<?php echo number_format($total_spent, 2); ?></div>
            <div class="stat-label">Total Spent</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">$<?php echo number_format($avg_order_value, 2); ?></div>
            <div class="stat-label">Avg. Order Value</div>
        </div>
    </div>

    <div class="dashboard-grid">
        <!-- Left Column -->
        <div>
            <!-- Quick Actions -->
            <div class="section">
                <h2 class="section-title">Quick Actions</h2>
                <div class="quick-actions">
                    <a href="menu.php" class="action-card">
                        <span class="action-icon">🍽️</span>
                        <div class="action-title">Order Food</div>
                        <div class="action-description">Browse our delicious menu</div>
                    </a>
                    
                    <a href="cart.php" class="action-card">
                        <span class="action-icon">🛒</span>
                        <div class="action-title">View Cart</div>
                        <div class="action-description">Review your current order</div>
                    </a>
                    
                    <a href="order_history.php" class="action-card">
                        <span class="action-icon">📋</span>
                        <div class="action-title">Order History</div>
                        <div class="action-description">View past orders</div>
                    </a>
                    
                    <a href="profile.php" class="action-card">
                        <span class="action-icon">👤</span>
                        <div class="action-title">Profile Settings</div>
                        <div class="action-description">Update your information</div>
                    </a>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="section">
                <h2 class="section-title">
                    Recent Orders
                    <a href="order_history.php">View All</a>
                </h2>
                
                <?php if (!empty($recent_orders)): ?>
                    <div class="orders-list">
                        <?php foreach ($recent_orders as $order): ?>
                            <div class="order-item">
                                <div class="order-info">
                                    <h4>Order #<?php echo $order['order_id']; ?></h4>
                                    <p><?php echo date('F j, Y g:i A', strtotime($order['order_date'])); ?></p>
                                    <p class="order-items"><?php echo implode(', ', $order['items']); ?></p>
                                </div>
                                <div class="order-amount">$<?php echo number_format($order['total_amount'], 2); ?></div>
                                <div class="order-status status-<?php echo $order['status']; ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon">📋</div>
                        <h3>No orders yet</h3>
                        <p>Place your first order and it will show up here!</p>
                        <a href="menu.php" class="btn btn-primary mt-lg">Start Ordering</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right Column -->
        <div>
            <!-- Profile Information -->
            <div class="section">
                <h2 class="section-title">Profile Information</h2>
                <div class="profile-info">
                    <div class="info-item">
                        <span class="info-label">Username</span>
                        <span class="info-value"><?php echo htmlspecialchars($username); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Member Since</span>
                        <span class="info-value"><?php echo $member_since; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Total Orders</span>
                        <span class="info-value"><?php echo $order_count; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Loyalty Points</span>
                        <span class="info-value"><?php echo $order_count * 10; ?> pts</span>
                    </div>
                    <button class="edit-profile-btn" onclick="window.location.href='profile.php'">Edit Profile</button>
                </div>
            </div>

            <!-- Rewards Program -->
            <div class="rewards-section">
                <h3 class="rewards-title">🎯 Loyalty Rewards</h3>
                <div class="rewards-points"><?php echo $order_count * 10; ?> Points</div>
                <p class="rewards-description">
                    Earn 10 points for every dollar spent. Redeem points for free meals and exclusive discounts!
                </p>
                <div class="progress-bar" style="background: var(--border-color); border-radius: var(--radius-full); height: 8px; margin-bottom: var(--space-md);">
                    <div style="background: var(--primary); height: 100%; border-radius: var(--radius-full); width: <?php echo min(($order_count * 10) / 2, 100); ?>%;"></div>
                </div>
                <p style="color: var(--text-muted); font-size: var(--text-sm);">
                    <?php echo $order_count * 10; ?> / 200 points to next reward
                </p>
            </div>

            <!-- Quick Tips -->
            <div class="section">
                <h2 class="section-title">💡 Quick Tips</h2>
                <div style="display: flex; flex-direction: column; gap: var(--space-md);">
                    <div style="padding: var(--space-md); background: var(--sb-cream); border-radius: var(--radius-lg);">
                        <strong>Fast Reordering</strong>
                        <p style="margin: var(--space-xs) 0 0 0; color: var(--text-muted); font-size: var(--text-sm);">
                            Reorder your favorites quickly from your order history.
                        </p>
                    </div>
                    <div style="padding: var(--space-md); background: var(--sb-cream); border-radius: var(--radius-lg);">
                        <strong>Campus Discount</strong>
                        <p style="margin: var(--space-xs) 0 0 0; color: var(--text-muted); font-size: var(--text-sm);">
                            Show your student ID for 10% off all orders.
                        </p>
                    </div>
                    <div style="padding: var(--space-md); background: var(--sb-cream); border-radius: var(--radius-lg);">
                        <strong>Group Orders</strong>
                        <p style="margin: var(--space-xs) 0 0 0; color: var(--text-muted); font-size: var(--text-sm);">
                        Coordinate with classmates for group discounts.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Special Offers -->
    <div class="section">
        <h2 class="section-title">🎁 Special Offers Just For You</h2>
        <div class="grid grid-3" style="gap: var(--space-lg);">
            <div style="text-align: center; padding: var(--space-xl); background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: var(--radius-lg);">
                <div style="font-size: 2rem; margin-bottom: var(--space-md);">🍕</div>
                <h3 style="margin: 0 0 var(--space-sm) 0;">Free Drink</h3>
                <p style="margin: 0; opacity: 0.9; font-size: var(--text-sm);">Get a free soda with any order over $15</p>
            </div>
            <div style="text-align: center; padding: var(--space-xl); background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; border-radius: var(--radius-lg);">
                <div style="font-size: 2rem; margin-bottom: var(--space-md);">🎓</div>
                <h3 style="margin: 0 0 var(--space-sm) 0;">Student Special</h3>
                <p style="margin: 0; opacity: 0.9; font-size: var(--text-sm);">15% off for TAMCC students every Wednesday</p>
            </div>
            <div style="text-align: center; padding: var(--space-xl); background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; border-radius: var(--radius-lg);">
                <div style="font-size: 2rem; margin-bottom: var(--space-md);">👥</div>
                <h3 style="margin: 0 0 var(--space-sm) 0;">Group Deal</h3>
                <p style="margin: 0; opacity: 0.9; font-size: var(--text-sm);">Order for 4+ people and get free delivery</p>
            </div>
        </div>
    </div>
</div>

<script>
// Add interactive elements
document.addEventListener('DOMContentLoaded', function() {
    // Add click animation to action cards
    const actionCards = document.querySelectorAll('.action-card');
    actionCards.forEach(card => {
        card.addEventListener('click', function(e) {
            // Add ripple effect
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.cssText = `
                position: absolute;
                border-radius: 50%;
                background: rgba(0, 112, 74, 0.3);
                transform: scale(0);
                animation: ripple 0.6s linear;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
            `;
            
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    // Add loading animation to stats cards
    const statNumbers = document.querySelectorAll('.stat-number');
    statNumbers.forEach(stat => {
        const originalText = stat.textContent;
        let finalValue;
        
        if (originalText.includes('$')) {
            finalValue = parseFloat(originalText.replace('$', '').replace(',', ''));
        } else {
            finalValue = parseInt(originalText);
        }
        
        if (!isNaN(finalValue)) {
            let current = 0;
            const increment = finalValue / 20; // Animate over 20 steps
            const timer = setInterval(() => {
                current += increment;
                if (current >= finalValue) {
                    clearInterval(timer);
                    current = finalValue;
                    stat.textContent = originalText; // Restore original formatting
                } else {
                    if (originalText.includes('$')) {
                        stat.textContent = '$' + Math.floor(current).toLocaleString();
                    } else {
                        stat.textContent = Math.floor(current);
                    }
                }
            }, 50);
        }
    });
});

// Add CSS for ripple effect
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
</script>

<?php
require_once 'footer.php';
?>