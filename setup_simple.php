<?php
require_once 'config.php';

// Set maximum execution time
set_time_limit(300); // 5 minutes
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<pre>";
echo "TAMCC Mealhouse Database Setup\n";
echo "==============================\n\n";

try {
    // Test connection first
    echo "1. Testing database connection... ";
    $stmt = $pdo->query("SELECT version()");
    $version = $stmt->fetchColumn();
    echo "✅ Connected to PostgreSQL: $version\n\n";
    
    // Create tables one by one
    $tables = [
        'users' => "CREATE TABLE IF NOT EXISTS users (
            user_id SERIAL PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            full_name VARCHAR(100) NOT NULL,
            phone VARCHAR(20),
            role VARCHAR(20) CHECK (role IN ('customer', 'admin', 'staff')) DEFAULT 'customer',
            is_active BOOLEAN DEFAULT true,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        'menu_categories' => "CREATE TABLE IF NOT EXISTS menu_categories (
            category_id SERIAL PRIMARY KEY,
            category_name VARCHAR(50) NOT NULL,
            description TEXT,
            image_url VARCHAR(255),
            display_order INTEGER DEFAULT 0,
            is_active BOOLEAN DEFAULT true,
            parent_id INTEGER,
            is_featured BOOLEAN DEFAULT false,
            show_in_header BOOLEAN DEFAULT true,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (parent_id) REFERENCES menu_categories(category_id) ON DELETE SET NULL
        )",
        
        'menu_items' => "CREATE TABLE IF NOT EXISTS menu_items (
            item_id SERIAL PRIMARY KEY,
            item_name VARCHAR(100) NOT NULL,
            description TEXT,
            price DECIMAL(10,2) NOT NULL,
            category_id INTEGER,
            subcategory_id INTEGER,
            image_url VARCHAR(255),
            ingredients TEXT,
            nutritional_info TEXT,
            is_available BOOLEAN DEFAULT true,
            is_featured BOOLEAN DEFAULT false,
            is_daily_special BOOLEAN DEFAULT false,
            preparation_time INTEGER DEFAULT 15,
            spice_level VARCHAR(20) CHECK (spice_level IN ('mild', 'medium', 'hot', 'very hot')) DEFAULT 'mild',
            tags TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES menu_categories(category_id) ON DELETE SET NULL,
            FOREIGN KEY (subcategory_id) REFERENCES menu_categories(category_id) ON DELETE SET NULL
        )",
        
        'combo_meals' => "CREATE TABLE IF NOT EXISTS combo_meals (
            combo_id SERIAL PRIMARY KEY,
            combo_name VARCHAR(100) NOT NULL,
            description TEXT,
            price DECIMAL(10,2) NOT NULL,
            image_url VARCHAR(255),
            includes_items TEXT,
            is_available BOOLEAN DEFAULT true,
            display_order INTEGER DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        'daily_specials' => "CREATE TABLE IF NOT EXISTS daily_specials (
            special_id SERIAL PRIMARY KEY,
            item_id INTEGER NOT NULL,
            special_date DATE NOT NULL,
            special_price DECIMAL(10,2),
            description TEXT,
            is_active BOOLEAN DEFAULT true,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE(special_date, item_id),
            FOREIGN KEY (item_id) REFERENCES menu_items(item_id) ON DELETE CASCADE
        )",
        
        'orders' => "CREATE TABLE IF NOT EXISTS orders (
            order_id SERIAL PRIMARY KEY,
            order_number VARCHAR(20) UNIQUE NOT NULL,
            user_id INTEGER,
            customer_name VARCHAR(100) NOT NULL,
            customer_email VARCHAR(100) NOT NULL,
            customer_phone VARCHAR(20) NOT NULL,
            special_instructions TEXT,
            order_type VARCHAR(20) CHECK (order_type IN ('pickup', 'delivery')) DEFAULT 'pickup',
            order_status VARCHAR(20) CHECK (order_status IN ('pending', 'confirmed', 'preparing', 'ready', 'completed', 'cancelled')) DEFAULT 'pending',
            payment_method VARCHAR(20) CHECK (payment_method IN ('cash', 'online')) DEFAULT 'cash',
            payment_status VARCHAR(20) CHECK (payment_status IN ('pending', 'paid', 'failed', 'refunded')) DEFAULT 'pending',
            subtotal DECIMAL(10,2) NOT NULL,
            tax_amount DECIMAL(10,2) NOT NULL,
            delivery_fee DECIMAL(10,2) DEFAULT 2.99,
            total_amount DECIMAL(10,2) NOT NULL,
            estimated_time TIMESTAMP,
            completed_at TIMESTAMP,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
        )",
        
        'order_items' => "CREATE TABLE IF NOT EXISTS order_items (
            order_item_id SERIAL PRIMARY KEY,
            order_id INTEGER NOT NULL,
            item_id INTEGER NOT NULL,
            item_type VARCHAR(20) CHECK (item_type IN ('regular', 'combo', 'daily_special')) DEFAULT 'regular',
            quantity INTEGER DEFAULT 1,
            unit_price DECIMAL(10,2) NOT NULL,
            item_name VARCHAR(100) NOT NULL,
            special_instructions TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
            FOREIGN KEY (item_id) REFERENCES menu_items(item_id) ON DELETE CASCADE
        )"
    ];
    
    // Create tables
    foreach ($tables as $name => $sql) {
        echo "2. Creating $name table... ";
        $pdo->exec($sql);
        echo "✅ Done\n";
    }
    
    echo "\n3. Inserting sample data...\n";
    
    // Insert categories
    $categories = [
        [1, 'Breakfast', 'Start your day with our delicious breakfast options', 1, true, null, true, true],
        [2, 'Lunch of the Day', 'Daily changing lunch specials', 2, true, null, true, true],
        [3, 'A La Carte', 'Individual items ordered separately', 3, true, null, false, true],
        [4, 'Combos', 'Complete meal combinations at great value', 4, true, null, true, true],
        [5, 'Desserts', 'Sweet treats to complete your meal', 5, true, null, false, true],
        [6, 'Beverages', 'Refreshing drinks and beverages', 6, true, null, false, true],
        [7, 'Breakfast Sandwiches', 'Hearty breakfast sandwiches and wraps', 1, true, 1, false, false],
        [8, 'Pancakes & Waffles', 'Fluffy pancakes and crispy waffles', 2, true, 1, false, false],
        [9, 'Omelettes', 'Custom made omelettes with fresh ingredients', 3, true, 1, false, false],
        [10, 'Breakfast Sides', 'Perfect accompaniments to your breakfast', 4, true, 1, false, false]
    ];
    
    foreach ($categories as $cat) {
        $stmt = $pdo->prepare("INSERT INTO menu_categories (category_id, category_name, description, display_order, is_active, parent_id, is_featured, show_in_header) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?) 
                               ON CONFLICT (category_id) DO NOTHING");
        $stmt->execute($cat);
    }
    echo "   - Categories: ✅\n";
    
    // Insert menu items
    $items = [
        [1, 'Bacon & Egg Sandwich', 'Crispy bacon, fried egg, and cheese on toasted bun', 8.99, 1, 7, 'Bacon, egg, cheese, bun, butter', '["breakfast", "sandwich", "bacon"]', true, true],
        [2, 'Classic Cheeseburger', 'Beef patty with cheese, lettuce, tomato, and special sauce', 11.99, 3, 11, 'Beef patty, cheese, bun, lettuce, tomato, sauce', '["burger", "beef"]', true, true],
        [3, 'Greek Salad', 'Fresh vegetables with feta and olives', 8.49, 3, 13, 'Cucumbers, tomatoes, olives, feta, olive oil', '["salad", "vegetarian"]', true, false],
        [4, 'Chocolate Cake', 'Rich chocolate layer cake', 5.99, 5, 15, 'Flour, sugar, cocoa, eggs, butter', '["dessert", "cake", "chocolate", "vegetarian"]', true, false],
        [5, 'Coffee', 'Freshly brewed coffee', 2.49, 6, 18, 'Coffee beans, water', '["beverage", "hot", "vegetarian"]', true, false]
    ];
    
    foreach ($items as $item) {
        $stmt = $pdo->prepare("INSERT INTO menu_items (item_id, item_name, description, price, category_id, subcategory_id, ingredients, tags, is_available, is_featured) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?) 
                               ON CONFLICT (item_id) DO NOTHING");
        $stmt->execute($item);
    }
    echo "   - Menu items: ✅\n";
    
    // Insert users
    $users = [
        [1, 'admin', 'admin@tamccmealhouse.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', '(473) 444-1000', 'admin', true],
        [2, 'student1', 'student@tamcc.edu', '$2y$10$SNYI7LaLoUze.mRwTi2.AObE/UnEnGJcKGNtBZvKqE70qJOwNeK2S', 'Test Student', '', 'customer', true]
    ];
    
    foreach ($users as $user) {
        $stmt = $pdo->prepare("INSERT INTO users (user_id, username, email, password_hash, full_name, phone, role, is_active) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?) 
                               ON CONFLICT (user_id) DO NOTHING");
        $stmt->execute($user);
    }
    echo "   - Users: ✅\n";
    
    // Create indexes
    $indexes = [
        "CREATE INDEX IF NOT EXISTS idx_menu_items_category ON menu_items(category_id)",
        "CREATE INDEX IF NOT EXISTS idx_menu_items_featured ON menu_items(is_featured) WHERE is_featured = true",
        "CREATE INDEX IF NOT EXISTS idx_menu_items_available ON menu_items(is_available) WHERE is_available = true",
        "CREATE INDEX IF NOT EXISTS idx_users_username ON users(username)",
        "CREATE INDEX IF NOT EXISTS idx_users_email ON users(email)"
    ];
    
    foreach ($indexes as $index) {
        $pdo->exec($index);
    }
    echo "   - Indexes: ✅\n";
    
    echo "\n========================================\n";
    echo "✅ DATABASE SETUP COMPLETE!\n";
    echo "========================================\n\n";
    
    echo "Test Login Credentials:\n";
    echo "=======================\n";
    echo "Admin User:\n";
    echo "  Username: admin\n";
    echo "  Password: password\n\n";
    
    echo "Student User:\n";
    echo "  Username: student1\n";
    echo "  Password: password\n\n";
    
    echo "Next Steps:\n";
    echo "===========\n";
    echo "1. <a href='login.php'>Login to the system</a>\n";
    echo "2. <a href='index.php'>Visit the homepage</a>\n";
    echo "3. <a href='menu.php'>Browse the menu</a>\n";
    
} catch (PDOException $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Code: " . $e->getCode() . "\n";
    
    echo "\nTroubleshooting Tips:\n";
    echo "1. Check config.php database credentials\n";
    echo "2. Make sure PostgreSQL is running\n";
    echo "3. Check if the database exists: tamcc_mealhouse\n";
    echo "4. Check user permissions\n";
}
echo "</pre>";
?>