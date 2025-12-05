<?php
session_start();
require_once 'config.php';

$page_title = "TAMCC Mealhouse - Campus Dining Solution";
require_once 'header.php';

// Initialize arrays
$featured_items = [];
$todays_specials = [];
$combo_meals = [];
$categories = [];

// Only run queries if $pdo exists
if (isset($pdo) && $pdo) {
    try {
        // Get featured items
        $featured_stmt = $pdo->query("SELECT * FROM menu_items WHERE is_featured = true AND is_available = true LIMIT 6");
        $featured_items = $featured_stmt->fetchAll();
    } catch (Exception $e) {
        // Use fallback data
        $featured_items = [
            ['item_id' => 1, 'item_name' => 'Bacon & Egg Sandwich', 'description' => 'Crispy bacon, fried egg, and cheese', 'price' => 8.99, 'category_id' => 1],
            ['item_id' => 2, 'item_name' => 'Classic Cheeseburger', 'description' => 'Beef patty with cheese and veggies', 'price' => 11.99, 'category_id' => 3],
        ];
    }
} else {
    // Use fallback data if no database
    $featured_items = [
        ['item_id' => 1, 'item_name' => 'Bacon & Egg Sandwich', 'description' => 'Crispy bacon, fried egg, and cheese', 'price' => 8.99, 'category_id' => 1],
        ['item_id' => 2, 'item_name' => 'Classic Cheeseburger', 'description' => 'Beef patty with cheese and veggies', 'price' => 11.99, 'category_id' => 3],
    ];
}

?>
<!-- Keep the rest of your existing index.php HTML and CSS exactly as it was -->
<!-- Only the PHP database queries above were updated -->

<style>

    /* Hero Section Responsive Fixes */
/* Hero Section Responsive Fixes */
@media (max-width: 768px) {
    /* Fix for grid layout */
    .grid.grid-2 {
        display: flex !important;
        flex-direction: column !important;
    }
    
    .hero-section {
        padding: 40px 0 !important;
        min-height: auto !important;
    }
    
    .hero-section h1 {
        font-size: 2rem !important;
        text-align: center;
    }
    
    .hero-section .text-lead {
        font-size: 1rem !important;
        text-align: center;
    }
    
    /* Fix button layout */
    .hero-section .d-flex.gap-md {
        flex-direction: column !important;
        align-items: center !important;
    }
    
    .hero-section .btn {
        width: 100% !important;
        max-width: 300px;
        margin-bottom: 10px !important;
    }
    
    /* Center the emoji */
    .hero-section .text-center {
        margin-top: 30px;
    }
    
    .hero-section .text-center div:first-child {
        font-size: 4rem !important;
    }
    
    /* Fix stats */
    .stats-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 15px !important;
    }
    
    .stat-number {
        font-size: 1.8rem !important;
    }
}

@media (max-width: 480px) {
    .hero-section h1 {
        font-size: 1.5rem !important;
    }
    
    .stats-grid {
        grid-template-columns: 1fr !important;
    }
    
    .live-counter {
        font-size: 2rem !important;
    }
    
    .floating-cta {
        bottom: 15px !important;
        right: 15px !important;
        font-size: 14px !important;
        padding: 10px 15px !important;
    }
}
/* Enhanced index Styles */
.hero-section {
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    color: white;
    position: relative;
    overflow: hidden;
    padding: var(--space-4xl) 0;
    min-height: 80vh;
    display: flex;
    align-items: center;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
        radial-gradient(circle at 20% 80%, rgba(255,255,255,0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(255,255,255,0.05) 0%, transparent 50%);
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.feature-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    height: 100%;
    position: relative;
    overflow: hidden;
}

.feature-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.1), transparent);
    transition: left 0.5s ease;
}

.feature-card:hover::before {
    left: 100%;
}

.feature-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0,0,0,0.4);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: var(--space-lg);
    margin: var(--space-3xl) 0;
}

.stat-card {
    text-align: center;
    padding: var(--space-xl);
    background: var(--bg-secondary);
    border-radius: var(--radius-xl);
    border: 1px solid var(--border-light);
    transition: all 0.3s ease;
}

.stat-card:hover {
    border-color: var(--accent-primary);
    transform: translateY(-4px);
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--accent-primary);
    margin-bottom: var(--space-sm);
}

.live-counter {
    font-size: 3rem;
    font-weight: 800;
    color: var(--accent-secondary);
    text-shadow: 0 0 20px rgba(16, 185, 129, 0.3);
}

.specials-carousel {
    display: flex;
    gap: var(--space-lg);
    overflow-x: auto;
    padding: var(--space-lg) 0;
    scrollbar-width: thin;
    scrollbar-color: var(--accent-primary) var(--bg-surface);
}

.specials-carousel::-webkit-scrollbar {
    height: 8px;
}

.specials-carousel::-webkit-scrollbar-track {
    background: var(--bg-surface);
    border-radius: 4px;
}

.specials-carousel::-webkit-scrollbar-thumb {
    background: var(--accent-primary);
    border-radius: 4px;
}

.special-card {
    min-width: 300px;
    background: linear-gradient(135deg, var(--accent-secondary), #059669);
    color: white;
    border-radius: var(--radius-xl);
    padding: var(--space-xl);
    position: relative;
    overflow: hidden;
}

.special-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 200%;
    background: rgba(255,255,255,0.1);
    transform: rotate(45deg);
}

.menu-item-card {
    background: var(--bg-secondary);
    border-radius: var(--radius-xl);
    padding: var(--space-xl);
    border: 1px solid var(--border-light);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.menu-item-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.3);
    border-color: var(--accent-primary);
}

.menu-item-image {
    font-size: 4rem;
    text-align: center;
    margin-bottom: var(--space-lg);
    color: var(--accent-primary);
}

.menu-item-name {
    color: var(--text-primary);
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0 0 var(--space-md) 0;
}

.menu-item-description {
    color: var(--text-muted);
    font-size: 0.875rem;
    margin-bottom: var(--space-lg);
    line-height: 1.5;
}

.menu-item-price {
    color: var(--accent-primary);
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: var(--space-lg);
}

.quick-add-btn {
    background: var(--accent-primary);
    color: white;
    border: none;
    padding: var(--space-sm) var(--space-md);
    border-radius: var(--radius-lg);
    cursor: pointer;
    font-weight: 600;
    width: 100%;
    transition: all 0.3s ease;
}

.quick-add-btn:hover {
    background: #1e40af;
    transform: translateY(-2px);
}

.category-card {
    text-align: center;
    padding: var(--space-lg);
    background: var(--bg-secondary);
    border-radius: var(--radius-xl);
    border: 1px solid var(--border-light);
    transition: all 0.3s ease;
    text-decoration: none;
    color: inherit;
}

.category-card:hover {
    transform: translateY(-4px);
    border-color: var(--accent-primary);
    color: inherit;
}

.category-icon {
    font-size: 3rem;
    margin-bottom: var(--space-md);
    color: var(--accent-primary);
}

.combo-card {
    background: linear-gradient(135deg, var(--accent-primary), #1e40af);
    color: white;
    border-radius: var(--radius-xl);
    padding: var(--space-xl);
    position: relative;
    overflow: hidden;
}

.combo-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 200%;
    background: rgba(255,255,255,0.1);
    transform: rotate(45deg);
}

.floating-cta {
    position: fixed;
    bottom: var(--space-xl);
    right: var(--space-xl);
    background: var(--accent-primary);
    color: white;
    padding: var(--space-md) var(--space-lg);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-xl);
    z-index: 1000;
    animation: bounce 2s infinite;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: var(--space-sm);
    text-decoration: none;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
    40% { transform: translateY(-10px); }
    60% { transform: translateY(-5px); }
}

.typewriter {
    overflow: hidden;
    border-right: 2px solid var(--accent-primary);
    white-space: nowrap;
    animation: typing 3.5s steps(40, end), blink-caret 0.75s step-end infinite;
}

@keyframes typing {
    from { width: 0 }
    to { width: 100% }
}

@keyframes blink-caret {
    from, to { border-color: transparent }
    50% { border-color: var(--accent-primary) }
}

.pulse-dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    background: var(--accent-secondary);
    border-radius: 50%;
    margin-right: var(--space-sm);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}

.live-orders-counter {
    background: var(--accent-secondary);
    color: white;
    padding: var(--space-sm) var(--space-md);
    border-radius: var(--radius-full);
    font-weight: 600;
    display: inline-block;
    margin-left: var(--space-sm);
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .floating-cta {
        bottom: var(--space-md);
        right: var(--space-md);
    }
}
</style>
</head>
<body>
    <?php 
    require_once 'header.php';
    ?>

    <!-- Floating CTA -->
    <a href="menu.php" class="floating-cta">
        🍔 Order Now
    </a>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="page-container">
            <div class="grid grid-2 align-center">
                <div class="hero-content">
                    <div class="pulse-dot"></div>
                    <span class="text-small" style="color: var(--accent-secondary); font-weight: 600;">LIVE NOW</span>
                    <h1 class="h1 text-white mb-lg typewriter">Welcome to TAMCC Mealhouse</h1>
                    <p class="text-lead text-white mb-2xl" style="opacity: 0.95;">
                        Your premier campus dining experience at T.A. Marryshow Community College. 
                        Fresh meals, convenient ordering, and exceptional service delivered right to you.
                    </p>
                    
                    <div class="d-flex gap-md align-center mb-xl">
                        <div class="live-counter" id="liveOrders">0</div>
                        <span class="text-white">orders served today</span>
                    </div>
                    
                    <div class="d-flex gap-md">
                        <a href="menu.php" class="btn btn-secondary btn-lg">🍽️ Order Now</a>
                        <?php if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true): ?>
                            <a href="register.php" class="btn btn-ghost btn-lg" style="color: white; border-color: white;">🚀 Sign Up Free</a>
                        <?php endif; ?>
                        <a href="#specials" class="btn btn-outline btn-lg" style="color: white; border-color: white;">🔥 Today's Specials</a>
                    </div>
                </div>
                <div class="text-center">
                    <div style="font-size: 8rem; color: rgba(255,255,255,0.9); animation: float 6s ease-in-out infinite;">🍔</div>
                    <div class="mt-lg">
                        <div class="text-lead text-white font-weight-600">T.A. Marryshow Community College</div>
                        <div class="text-small text-white mt-sm" style="opacity: 0.8;">Official Campus Dining Partner</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="section">
        <div class="page-container">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number" data-target="<?php echo $total_orders; ?>">0</div>
                    <div class="text-small text-muted">Orders Today</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" data-target="<?php echo $total_items; ?>">0</div>
                    <div class="text-small text-muted">Menu Items</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" data-target="15">0</div>
                    <div class="text-small text-muted">Avg. Wait Time (min)</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" data-target="<?php echo $total_categories; ?>">0</div>
                    <div class="text-small text-muted">Food Categories</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Today's Specials -->
    <section id="specials" class="section">
        <div class="page-container">
            <div class="text-center mb-3xl">
                <h2 class="h2 mb-lg">🔥 Today's Specials</h2>
                <p class="text-lead text-muted">Fresh deals cooked up just for you</p>
            </div>
            
            <?php if (!empty($todays_specials)): ?>
                <div class="specials-carousel">
                    <?php foreach ($todays_specials as $special): ?>
                        <div class="special-card">
                            <h3 class="h4 mb-md"><?php echo htmlspecialchars($special['item_name']); ?></h3>
                            <p class="text-body" style="opacity: 0.9;"><?php echo htmlspecialchars($special['special_description'] ?: $special['description']); ?></p>
                            <div class="text-lead font-weight-700 mt-lg">$<?php echo number_format($special['special_price'] ?? $special['price'], 2); ?></div>
                            <div class="text-small mt-sm" style="opacity: 0.8;">
                                ⏱️ <?php echo $special['preparation_time'] ?? '15-20'; ?> min
                            </div>
                            <form method="post" action="add_to_cart.php" class="mt-lg">
                                <input type="hidden" name="item_id" value="<?php echo $special['item_id']; ?>">
                                <input type="hidden" name="item_name" value="<?php echo htmlspecialchars($special['item_name']); ?>">
                                <input type="hidden" name="item_price" value="<?php echo $special['special_price'] ?? $special['price']; ?>">
                                <input type="hidden" name="is_special" value="1">
                                <button type="submit" class="btn btn-outline btn-sm" style="color: white; border-color: white;">Add to Cart</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center">
                    <p class="text-lead text-muted">Check back later for today's specials!</p>
                    <a href="menu.php" class="btn btn-primary mt-lg">View Full Menu</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Featured Menu Items -->
    <section class="section bg-surface">
        <div class="page-container">
            <div class="text-center mb-3xl">
                <h2 class="h2 mb-lg">🌟 Student Favorites</h2>
                <p class="text-lead text-muted">Most loved items on campus</p>
            </div>
            
            <?php if (!empty($featured_items)): ?>
                <div class="grid grid-3">
                    <?php foreach ($featured_items as $item): ?>
                        <div class="menu-item-card">
                            <div class="menu-item-image">
                                <?php 
                                $emojis = [1=>'🍳', 2=>'🍽️', 3=>'📋', 4=>'🎯', 5=>'🍰', 6=>'🥤'];
                                echo $emojis[$item['category_id']] ?? '🍽️';
                                ?>
                            </div>
                            <h3 class="menu-item-name"><?php echo htmlspecialchars($item['item_name']); ?></h3>
                            <p class="menu-item-description"><?php echo htmlspecialchars($item['description']); ?></p>
                            <div class="menu-item-price">$<?php echo number_format($item['price'], 2); ?></div>
                            <?php if ($item['preparation_time']): ?>
                                <div class="text-small text-muted mb-lg">
                                    ⏱️ <?php echo $item['preparation_time']; ?> min
                                </div>
                            <?php endif; ?>
                            <form method="post" action="add_to_cart.php">
                                <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                                <input type="hidden" name="item_name" value="<?php echo htmlspecialchars($item['item_name']); ?>">
                                <input type="hidden" name="item_price" value="<?php echo $item['price']; ?>">
                                <button type="submit" class="quick-add-btn">Add to Cart</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center">
                    <p class="text-lead text-muted">No featured items available at the moment.</p>
                    <a href="menu.php" class="btn btn-primary mt-lg">Browse All Items</a>
                </div>
            <?php endif; ?>
            
            <div class="text-center mt-3xl">
                <a href="menu.php" class="btn btn-primary btn-lg">View Full Menu</a>
            </div>
        </div>
    </section>

    <!-- Food Categories -->
<section class="section">
    <div class="page-container">
        <div class="text-center mb-3xl">
            <h2 class="h2 mb-lg">🍽️ Browse Categories</h2>
            <p class="text-lead text-muted">Something for every taste</p>
        </div>
        
        <!-- Category Buttons Grid -->
        <div class="grid grid-3">
            <?php foreach ($categories as $category): ?>
                <div class="text-center">
                    <a href="menu.php?category=<?php echo $category['category_id']; ?>" class="btn btn-primary btn-lg mb-md" style="width: 100%; padding: var(--space-xl); font-size: 1.1rem; display: flex; flex-direction: column; align-items: center; justify-content: center; height: 140px;">
                        <div style="font-size: 2.5rem; margin-bottom: var(--space-sm);">
                            <?php 
                            $category_icons = [
                                1 => '🍳', // Breakfast
                                2 => '🍽️', // Lunch
                                3 => '📋', // Daily Specials
                                4 => '🎯', // Combos
                                5 => '🍰', // Desserts
                                6 => '🥤'  // Beverages
                            ];
                            echo $category_icons[$category['category_id']] ?? '🍽️';
                            ?>
                        </div>
                        <span><?php echo htmlspecialchars($category['category_name']); ?></span>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Browse Full Menu Button -->
        <div class="text-center mt-3xl">
            <a href="menu.php" class="btn btn-secondary btn-lg" style="padding: var(--space-lg) var(--space-3xl); font-size: 1.2rem;">
                Browse Full Menu
            </a>
        </div>
    </div>
</section>

    <!-- Value Combos -->
    <?php if (!empty($combo_meals)): ?>
    <section class="section bg-surface">
        <div class="page-container">
            <div class="text-center mb-3xl">
                <h2 class="h2 mb-lg">🎯 Great Value Combos</h2>
                <p class="text-lead text-muted">Perfect meals for busy campus life</p>
            </div>
            
            <div class="grid grid-3">
                <?php foreach ($combo_meals as $combo): ?>
                    <div class="combo-card">
                        <h3 class="h4 mb-md"><?php echo htmlspecialchars($combo['combo_name']); ?></h3>
                        <p class="text-body" style="opacity: 0.9;"><?php echo htmlspecialchars($combo['description']); ?></p>
                        <div class="text-lead font-weight-700 mt-lg">$<?php echo number_format($combo['price'], 2); ?></div>
                        <div class="text-small mt-sm" style="opacity: 0.8;">Save up to 20%</div>
                        <form method="post" action="add_to_cart.php" class="mt-lg">
                            <input type="hidden" name="combo_id" value="<?php echo $combo['combo_id']; ?>">
                            <input type="hidden" name="item_name" value="<?php echo htmlspecialchars($combo['combo_name']); ?>">
                            <input type="hidden" name="item_price" value="<?php echo $combo['price']; ?>">
                            <input type="hidden" name="is_combo" value="1">
                            <button type="submit" class="btn btn-outline btn-sm" style="color: white; border-color: white;">Add Combo</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- How It Works -->
    <section class="section">
        <div class="page-container">
            <div class="text-center mb-3xl">
                <h2 class="h2 mb-lg">🚀 How It Works</h2>
                <p class="text-lead text-muted">Get your meal in three simple steps</p>
            </div>
            
            <div class="grid grid-3">
                <div class="text-center">
                    <div class="card p-xl feature-card">
                        <div style="font-size: 4rem; color: var(--accent-primary); font-weight: 800; margin-bottom: var(--space-lg);">1</div>
                        <h3 class="h4 mb-md">Browse & Select</h3>
                        <p class="text-body text-muted">Explore our diverse menu and choose your favorites</p>
                        <div class="mt-lg">
                            <span class="text-small" style="color: var(--accent-primary);">📱 Mobile-friendly</span>
                        </div>
                    </div>
                </div>
                
                <div class="text-center">
                    <div class="card p-xl feature-card">
                        <div style="font-size: 4rem; color: var(--accent-primary); font-weight: 800; margin-bottom: var(--space-lg);">2</div>
                        <h3 class="h4 mb-md">Customize & Order</h3>
                        <p class="text-body text-muted">Add special instructions and checkout securely</p>
                        <div class="mt-lg">
                            <span class="text-small" style="color: var(--accent-primary);">💳 Multiple payments</span>
                        </div>
                    </div>
                </div>
                
                <div class="text-center">
                    <div class="card p-xl feature-card">
                        <div style="font-size: 4rem; color: var(--accent-primary); font-weight: 800; margin-bottom: var(--space-lg);">3</div>
                        <h3 class="h4 mb-md">Pickup & Enjoy</h3>
                        <p class="text-body text-muted">Collect your ready meal on campus</p>
                        <div class="mt-lg">
                            <span class="text-small" style="color: var(--accent-primary);">⚡ Fast pickup</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section class="section bg-surface">
        <div class="page-container text-center">
            <h2 class="h2 mb-lg">Ready to Experience Campus Dining?</h2>
            <p class="text-lead text-muted mb-2xl">Join thousands of TAMCC students enjoying better meals</p>
            
            <div class="d-flex gap-md justify-center">
                <a href="menu.php" class="btn btn-primary btn-lg">🍔 Start Ordering</a>
                <?php if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true): ?>
                    <a href="register.php" class="btn btn-outline btn-lg">Create Account</a>
                <?php else: ?>
                    <a href="dashboard.php" class="btn btn-outline btn-lg">My Dashboard</a>
                <?php endif; ?>
            </div>
            
            <div class="mt-3xl">
                <div class="d-flex gap-lg justify-center text-small text-muted">
                    <span>✅ Fresh Ingredients</span>
                    <span>✅ Campus Card Payments</span>
                    <span>✅ 15-20 Min Preparation</span>
                    <span>✅ Student Discounts</span>
                </div>
            </div>
        </div>
    </section>

    <script>
    // Animated counters
    function animateCounter(element, target, duration = 2000) {
        let start = 0;
        const increment = target / (duration / 16);
        const timer = setInterval(() => {
            start += increment;
            if (start >= target) {
                element.textContent = target;
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(start);
            }
        }, 16);
    }

    // Live orders counter (simulated)
    function updateLiveOrders() {
        const counter = document.getElementById('liveOrders');
        let current = parseInt(counter.textContent);
        const increment = Math.floor(Math.random() * 3) + 1; // Random increment 1-3
        animateCounter(counter, current + increment, 1000);
        
        // Update every 5-10 seconds randomly
        setTimeout(updateLiveOrders, Math.random() * 5000 + 5000);
    }

    // Initialize animations when page loads
    document.addEventListener('DOMContentLoaded', function() {
        // Animate stat numbers
        document.querySelectorAll('.stat-number').forEach(stat => {
            const target = parseInt(stat.getAttribute('data-target'));
            animateCounter(stat, target);
        });

        // Start live orders counter
        setTimeout(updateLiveOrders, 2000);

        // Add scroll animations to feature cards
        const featureCards = document.querySelectorAll('.feature-card');
        featureCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.6s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 200);
        });

        // Add hover effects to menu items
        const menuItems = document.querySelectorAll('.menu-item-card');
        menuItems.forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px) scale(1.02)';
            });
            
            item.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(-8px)';
            });
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    });

    // Add to cart functionality
    function quickAddToCart(itemId, itemName, itemPrice) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'add_to_cart.php';
        
        const inputs = [
            {name: 'item_id', value: itemId},
            {name: 'item_name', value: itemName},
            {name: 'item_price', value: itemPrice},
            {name: 'quantity', value: 1}
        ];
        
        inputs.forEach(input => {
            const el = document.createElement('input');
            el.type = 'hidden';
            el.name = input.name;
            el.value = input.value;
            form.appendChild(el);
        });
        
        document.body.appendChild(form);
        form.submit();
    }
    </script>

    <?php
    require_once 'footer.php';
    ?>
</body>
</html>