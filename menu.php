<?php
session_start();
require_once "config.php";

$page_title = "Menu - TAMCC Mealhouse";
require_once 'header.php';

$category_id = isset($_GET['category']) ? intval($_GET['category']) : 1;
$subcategory_id = isset($_GET['subcategory']) ? intval($_GET['subcategory']) : null;

$categories_stmt = $pdo->query("SELECT * FROM menu_categories WHERE parent_id IS NULL AND is_active = TRUE ORDER BY display_order");
$categories = $categories_stmt->fetchAll();

$current_category_stmt = $pdo->prepare("SELECT category_name FROM menu_categories WHERE category_id = ?");
$current_category_stmt->execute([$category_id]);
$current_category = $current_category_stmt->fetch();

$subcategories_stmt = $pdo->prepare("SELECT * FROM menu_categories WHERE parent_id = ? AND is_active = TRUE ORDER BY display_order");
$subcategories_stmt->execute([$category_id]);
$subcategories = $subcategories_stmt->fetchAll();

if ($subcategory_id) {
    $items_sql = "
        SELECT mi.*, mc.category_name as main_category, sc.category_name as subcategory
        FROM menu_items mi
        JOIN menu_categories mc ON mi.category_id = mc.category_id
        LEFT JOIN menu_categories sc ON mi.subcategory_id = sc.category_id
        WHERE mi.subcategory_id = ? AND mi.is_available = TRUE AND mc.is_active = TRUE
        ORDER BY mi.item_name
    ";
    $items_stmt = $pdo->prepare($items_sql);
    $items_stmt->execute([$subcategory_id]);
} else {
    $items_sql = "
        SELECT mi.*, mc.category_name as main_category, sc.category_name as subcategory
        FROM menu_items mi
        JOIN menu_categories mc ON mi.category_id = mc.category_id
        LEFT JOIN menu_categories sc ON mi.subcategory_id = sc.category_id
        WHERE mi.category_id = ? AND mi.is_available = TRUE AND mc.is_active = TRUE
        ORDER BY COALESCE(sc.display_order, 0), mi.item_name
    ";
    $items_stmt = $pdo->prepare($items_sql);
    $items_stmt->execute([$category_id]);
}
$menu_items = $items_stmt->fetchAll();

$specials_sql = "
    SELECT mi.*, ds.special_price, ds.description as special_description
    FROM daily_specials ds
    JOIN menu_items mi ON ds.item_id = mi.item_id
    WHERE ds.special_date = CURRENT_DATE AND ds.is_active = TRUE AND mi.is_available = TRUE
";
$specials_stmt = $pdo->query($specials_sql);
$todays_specials = $specials_stmt->fetchAll();

$combos_stmt = $pdo->query("SELECT * FROM combo_meals WHERE is_available = TRUE ORDER BY display_order");
$combo_meals = $combos_stmt->fetchAll();

$featured_stmt = $pdo->query("SELECT * FROM menu_items WHERE is_featured = TRUE AND is_available = TRUE LIMIT 4");
$featured_items = $featured_stmt->fetchAll();
?>

<style>
.menu-container { max-width: 1200px; margin: 0 auto; padding: var(--space-xl); }
.menu-title { font-size: var(--text-3xl); font-weight: 600; color: var(--primary); margin-bottom: var(--space-xl); }
.category-tabs { display: flex; gap: var(--space-md); margin-bottom: var(--space-2xl); flex-wrap: wrap; justify-content: center; }
.category-tab { background-color: var(--sb-gray); color: var(--text-light); padding: var(--space-md) var(--space-xl); border-radius: var(--radius-lg); text-decoration: none; transition: all var(--transition-normal); }
.category-tab:hover, .category-tab.active { background-color: var(--primary); color: white; transform: translateY(-2px); }
.subcategory-tabs { display: flex; gap: var(--space-sm); margin-bottom: var(--space-xl); flex-wrap: wrap; justify-content: center; }
.subcategory-tab { background-color: transparent; color: var(--text-muted); border: 1px solid var(--border-color); padding: var(--space-sm) var(--space-lg); border-radius: var(--radius-full); text-decoration: none; transition: all var(--transition-normal); }
.subcategory-tab:hover, .subcategory-tab.active { background-color: var(--primary); color: white; border-color: var(--primary); }
.menu-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: var(--space-xl); margin-bottom: var(--space-3xl); }
.menu-item { background: var(--card-bg); border-radius: var(--radius-xl); padding: var(--space-xl); border: 1px solid var(--border-color); transition: all var(--transition-normal); position: relative; }
.menu-item:hover { transform: translateY(-5px); box-shadow: var(--shadow-lg); border-color: var(--primary); }
.featured-badge, .special-badge { position: absolute; top: var(--space-md); right: var(--space-md); color: white; padding: var(--space-xs) var(--space-md); border-radius: var(--radius-full); font-size: var(--text-sm); font-weight: 600; }
.featured-badge { background: var(--primary); }
.special-badge { background: var(--accent-red); }
.item-image { font-size: 3rem; text-align: center; margin-bottom: var(--space-lg); height: 80px; display: flex; align-items: center; justify-content: center; }
.item-name { color: var(--text-dark); font-size: var(--text-lg); font-weight: 600; margin: 0 0 var(--space-md) 0; }
.item-description { color: var(--text-muted); font-size: var(--text-sm); margin-bottom: var(--space-lg); line-height: 1.5; }
.item-price { color: var(--primary); font-size: var(--text-xl); font-weight: 700; }
.add-to-cart-form { display: flex; gap: var(--space-md); align-items: center; }
.quantity-input { background-color: var(--input-bg); border: 1px solid var(--border-color); width: 70px; text-align: center; border-radius: var(--radius-md); padding: var(--space-md); }
.add-to-cart-btn { background-color: var(--primary); color: white; border: none; padding: var(--space-md) var(--space-lg); border-radius: var(--radius-lg); cursor: pointer; flex: 1; font-weight: 600; }
.success-message { background-color: var(--success); color: white; padding: var(--space-md) var(--space-lg); border-radius: var(--radius-lg); margin-bottom: var(--space-xl); text-align: center; }
.quick-add-section { background: var(--card-bg); border-radius: var(--radius-xl); padding: var(--space-2xl); margin-top: var(--space-3xl); border: 1px solid var(--border-color); }
.quick-add-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--space-lg); margin-top: var(--space-lg); }
.quick-add-item { text-align: center; padding: var(--space-lg); border: 1px solid var(--border-color); border-radius: var(--radius-lg); transition: all var(--transition-fast); cursor: pointer; }
.quick-add-item:hover { border-color: var(--primary); transform: translateY(-2px); }
.combo-section { background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); border-radius: var(--radius-xl); padding: var(--space-2xl); margin: var(--space-3xl) 0; color: white; }
.specials-section { background: var(--accent-red); border-radius: var(--radius-xl); padding: var(--space-2xl); margin: var(--space-3xl) 0; color: white; }

@media (max-width: 768px) {
    .menu-container { padding: var(--space-md); }
    .menu-grid { grid-template-columns: 1fr; }
    .category-tabs, .subcategory-tabs { gap: var(--space-sm); }
    .add-to-cart-form { flex-direction: column; }
    .quantity-input, .add-to-cart-btn { width: 100%; }
    .combo-grid, .quick-add-grid { grid-template-columns: 1fr; }
}
</style>

<div class="menu-container">
    <h1 class="menu-title">
        <?php 
        if ($current_category) {
            $title = htmlspecialchars($current_category['category_name']);
            if ($subcategory_id && !empty($subcategories)) {
                foreach ($subcategories as $subcat) {
                    if ($subcat['category_id'] == $subcategory_id) {
                        $title .= ' - ' . htmlspecialchars($subcat['category_name']);
                        break;
                    }
                }
            }
            echo $title;
        } else {
            echo "Menu";
        }
        ?>
    </h1>
    
    <?php if (isset($_GET['added']) && $_GET['added'] == 'true'): ?>
        <div class="success-message">
            ✅ Item added to cart! <a href="cart.php" style="color: white; text-decoration: underline; margin-left: var(--space-md);">View Cart</a>
        </div>
    <?php endif; ?>
    
    <!-- Category Navigation -->
    <div class="category-tabs">
        <?php foreach ($categories as $category): ?>
            <a href="menu.php?category=<?= $category['category_id'] ?>" 
               class="category-tab <?= $category['category_id'] == $category_id ? 'active' : '' ?>">
                <?= htmlspecialchars($category['category_name']) ?>
            </a>
        <?php endforeach; ?>
    </div>
    
    <!-- Subcategory Navigation -->
    <?php if (!empty($subcategories)): ?>
        <div class="subcategory-tabs">
            <a href="menu.php?category=<?= $category_id ?>" class="subcategory-tab <?= !$subcategory_id ? 'active' : '' ?>">
                All <?= htmlspecialchars($current_category['category_name']) ?>
            </a>
            <?php foreach ($subcategories as $subcategory): ?>
                <a href="menu.php?category=<?= $category_id ?>&subcategory=<?= $subcategory['category_id'] ?>" 
                   class="subcategory-tab <?= $subcategory_id == $subcategory['category_id'] ? 'active' : '' ?>">
                    <?= htmlspecialchars($subcategory['category_name']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <!-- Special Sections -->
    <?php if ($category_id == 2 && !empty($todays_specials)): ?>
        <div class="specials-section">
            <h2>🍽️ Today's Lunch Specials</h2>
            <div class="menu-grid">
                <?php foreach ($todays_specials as $special): ?>
                    <div class="menu-item">
                        <div class="special-badge">Today's Special</div>
                        <div class="item-image">🍽️</div>
                        <h3 class="item-name"><?= htmlspecialchars($special['item_name']) ?></h3>
                        <p class="item-description"><?= htmlspecialchars($special['special_description'] ?: $special['description']) ?></p>
                        <div class="item-price">$<?= number_format($special['special_price'] ?? $special['price'], 2) ?></div>
                        <form class="add-to-cart-form" method="post" action="add_to_cart.php">
                            <input type="hidden" name="item_id" value="<?= $special['item_id'] ?>">
                            <input type="hidden" name="item_name" value="<?= htmlspecialchars($special['item_name']) ?>">
                            <input type="hidden" name="item_price" value="<?= $special['special_price'] ?? $special['price'] ?>">
                            <input type="hidden" name="is_special" value="1">
                            <input type="number" name="quantity" class="quantity-input" value="1" min="1" max="10">
                            <button type="submit" class="add-to-cart-btn">Add to Cart</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if ($category_id == 4 && !empty($combo_meals)): ?>
        <div class="combo-section">
            <h2>🎯 Great Value Combos</h2>
            <div class="menu-grid">
                <?php foreach ($combo_meals as $combo): ?>
                    <div class="menu-item">
                        <h3 class="item-name"><?= htmlspecialchars($combo['combo_name']) ?></h3>
                        <p class="item-description"><?= htmlspecialchars($combo['description']) ?></p>
                        <div class="item-price">$<?= number_format($combo['price'], 2) ?></div>
                        <form class="add-to-cart-form" method="post" action="add_to_cart.php">
                            <input type="hidden" name="combo_id" value="<?= $combo['combo_id'] ?>">
                            <input type="hidden" name="item_name" value="<?= htmlspecialchars($combo['combo_name']) ?>">
                            <input type="hidden" name="item_price" value="<?= $combo['price'] ?>">
                            <input type="hidden" name="is_combo" value="1">
                            <input type="number" name="quantity" class="quantity-input" value="1" min="1" max="5">
                            <button type="submit" class="add-to-cart-btn">Add Combo</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Regular Menu Items -->
    <?php if (!empty($menu_items)): ?>
        <div class="menu-grid">
            <?php foreach ($menu_items as $item): ?>
                <div class="menu-item <?= $item['is_featured'] ? 'featured' : '' ?>">
                    <?php if ($item['is_featured']): ?><div class="featured-badge">Popular</div><?php endif; ?>
                    
                    <div class="item-image">
                        <?php 
                        $emojis = [1=>'🍳', 2=>'🍽️', 3=>'📋', 4=>'🎯', 5=>'🍰', 6=>'🥤'];
                        echo $emojis[$item['category_id']] ?? '🍽️';
                        ?>
                    </div>
                    
                    <h3 class="item-name"><?= htmlspecialchars($item['item_name']) ?></h3>
                    <p class="item-description"><?= htmlspecialchars($item['description']) ?></p>
                    <div class="item-price">$<?= number_format($item['price'], 2) ?></div>
                    
                    <?php if ($item['preparation_time']): ?>
                        <div style="color: var(--text-muted); font-size: var(--text-sm); margin-bottom: var(--space-md);">
                            ⏱️ <?= $item['preparation_time'] ?> min
                        </div>
                    <?php endif; ?>
                    
                    <form class="add-to-cart-form" method="post" action="add_to_cart.php">
                        <input type="hidden" name="item_id" value="<?= $item['item_id'] ?>">
                        <input type="hidden" name="item_name" value="<?= htmlspecialchars($item['item_name']) ?>">
                        <input type="hidden" name="item_price" value="<?= $item['price'] ?>">
                        <input type="number" name="quantity" class="quantity-input" value="1" min="1" max="10">
                        <button type="submit" class="add-to-cart-btn">Add to Cart</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: var(--space-2xl); color: var(--text-muted);">
            <h3>No items found in this category.</h3>
            <p>Please check back later or try another category.</p>
        </div>
    <?php endif; ?>
</div>

<script>
function quickAddToCart(itemId) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'add_to_cart.php';
    
    const inputs = [
        {name: 'item_id', value: itemId},
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

document.querySelectorAll('.quantity-input').forEach(input => {
    input.addEventListener('change', function() {
        let value = parseInt(this.value);
        if (isNaN(value) || value < 1) this.value = 1;
        else if (value > 10) this.value = 10;
    });
});
</script>

<?php require_once 'footer.php'; ?>