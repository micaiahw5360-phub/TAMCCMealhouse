-- PostgreSQL version of tamcc_mealhouse database
-- Converted from MySQL for Render

-- Drop existing tables (if any)
DROP TABLE IF EXISTS order_items CASCADE;
DROP TABLE IF EXISTS cart_items CASCADE;
DROP TABLE IF EXISTS daily_specials CASCADE;
DROP TABLE IF EXISTS orders CASCADE;
DROP TABLE IF EXISTS shopping_carts CASCADE;
DROP TABLE IF EXISTS combo_meals CASCADE;
DROP TABLE IF EXISTS menu_items CASCADE;
DROP TABLE IF EXISTS menu_categories CASCADE;
DROP TABLE IF EXISTS users CASCADE;

-- Create users table
CREATE TABLE users (
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
);

-- Create menu_categories table
CREATE TABLE menu_categories (
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
);

-- Create menu_items table
CREATE TABLE menu_items (
    item_id SERIAL PRIMARY KEY,
    item_name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category_id INTEGER,
    subcategory_id INTEGER,
    image_url VARCHAR(255),
    ingredients TEXT,
    nutritional_info JSONB,
    is_available BOOLEAN DEFAULT true,
    is_featured BOOLEAN DEFAULT false,
    is_daily_special BOOLEAN DEFAULT false,
    preparation_time INTEGER DEFAULT 15,
    spice_level VARCHAR(20) CHECK (spice_level IN ('mild', 'medium', 'hot', 'very hot')) DEFAULT 'mild',
    tags JSONB,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES menu_categories(category_id) ON DELETE SET NULL,
    FOREIGN KEY (subcategory_id) REFERENCES menu_categories(category_id) ON DELETE SET NULL
);

-- Create combo_meals table
CREATE TABLE combo_meals (
    combo_id SERIAL PRIMARY KEY,
    combo_name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image_url VARCHAR(255),
    includes_items JSONB,
    is_available BOOLEAN DEFAULT true,
    display_order INTEGER DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create daily_specials table
CREATE TABLE daily_specials (
    special_id SERIAL PRIMARY KEY,
    item_id INTEGER NOT NULL,
    special_date DATE NOT NULL,
    special_price DECIMAL(10,2),
    description TEXT,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(special_date, item_id),
    FOREIGN KEY (item_id) REFERENCES menu_items(item_id) ON DELETE CASCADE
);

-- Create shopping_carts table
CREATE TABLE shopping_carts (
    cart_id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL,
    session_id VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(user_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Create cart_items table
CREATE TABLE cart_items (
    cart_item_id SERIAL PRIMARY KEY,
    cart_id INTEGER NOT NULL,
    item_id INTEGER NOT NULL,
    item_type VARCHAR(20) CHECK (item_type IN ('regular', 'combo', 'daily_special')) DEFAULT 'regular',
    quantity INTEGER DEFAULT 1,
    special_instructions TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(cart_id, item_id, item_type),
    FOREIGN KEY (cart_id) REFERENCES shopping_carts(cart_id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES menu_items(item_id) ON DELETE CASCADE
);

-- Create orders table
CREATE TABLE orders (
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
);

-- Create order_items table
CREATE TABLE order_items (
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
);

-- Insert data into menu_categories
INSERT INTO menu_categories (category_id, category_name, description, display_order, is_active, parent_id, is_featured, show_in_header) VALUES
(1, 'Breakfast', 'Start your day with our delicious breakfast options', 1, true, NULL, true, true),
(2, 'Lunch of the Day', 'Daily changing lunch specials', 2, true, NULL, true, true),
(3, 'A La Carte', 'Individual items ordered separately', 3, true, NULL, false, true),
(4, 'Combos', 'Complete meal combinations at great value', 4, true, NULL, true, true),
(5, 'Desserts', 'Sweet treats to complete your meal', 5, true, NULL, false, true),
(6, 'Beverages', 'Refreshing drinks and beverages', 6, true, NULL, false, true),
(7, 'Breakfast Sandwiches', 'Hearty breakfast sandwiches and wraps', 1, true, 1, false, false),
(8, 'Pancakes & Waffles', 'Fluffy pancakes and crispy waffles', 2, true, 1, false, false),
(9, 'Omelettes', 'Custom made omelettes with fresh ingredients', 3, true, 1, false, false),
(10, 'Breakfast Sides', 'Perfect accompaniments to your breakfast', 4, true, 1, false, false),
(11, 'Burgers', 'Juicy burgers with various toppings', 1, true, 3, false, false),
(12, 'Sandwiches', 'Freshly made sandwiches and wraps', 2, true, 3, false, false),
(13, 'Salads', 'Fresh and healthy salad options', 3, true, 3, false, false),
(14, 'Sides', 'Perfect side dishes for any meal', 4, true, 3, false, false),
(15, 'Cakes & Pies', 'Delicious cakes and pies', 1, true, 5, false, false),
(16, 'Ice Cream', 'Creamy ice cream and sundaes', 2, true, 5, false, false),
(17, 'Cookies & Pastries', 'Freshly baked cookies and pastries', 3, true, 5, false, false),
(18, 'Hot Drinks', 'Coffee, tea, and hot chocolate', 1, true, 6, false, false),
(19, 'Cold Drinks', 'Iced beverages and soft drinks', 2, true, 6, false, false),
(20, 'Smoothies', 'Fresh fruit smoothies and shakes', 3, true, 6, false, false);

-- Insert data into menu_items
INSERT INTO menu_items (item_id, item_name, description, price, category_id, subcategory_id, ingredients, tags, is_available) VALUES
(1, 'Bacon & Egg Sandwich', 'Crispy bacon, fried egg, and cheese on toasted bun', 8.99, 1, 7, 'Bacon, egg, cheese, bun, butter', '["breakfast", "sandwich", "bacon"]', true),
(2, 'Sausage Breakfast Wrap', 'Sausage, scrambled eggs, and cheese in flour tortilla', 7.99, 1, 7, 'Sausage, eggs, cheese, tortilla', '["breakfast", "wrap", "sausage"]', true),
(3, 'Veggie Breakfast Bagel', 'Cream cheese, tomato, cucumber, and avocado on bagel', 6.99, 1, 7, 'Cream cheese, tomato, cucumber, avocado, bagel', '["breakfast", "vegetarian", "bagel"]', true),
(4, 'Classic Pancakes', 'Stack of three fluffy pancakes with maple syrup', 5.99, 1, 8, 'Flour, eggs, milk, butter, maple syrup', '["breakfast", "pancakes", "vegetarian"]', true),
(5, 'Belgian Waffle', 'Crispy Belgian waffle with butter and syrup', 6.49, 1, 8, 'Flour, eggs, milk, butter, maple syrup', '["breakfast", "waffle", "vegetarian"]', true),
(6, 'Blueberry Pancakes', 'Pancakes filled with fresh blueberries', 6.99, 1, 8, 'Flour, eggs, milk, blueberries, butter, syrup', '["breakfast", "pancakes", "vegetarian"]', true),
(7, 'Western Omelette', 'Ham, bell peppers, onions, and cheese', 9.99, 1, 9, 'Eggs, ham, bell peppers, onions, cheese', '["breakfast", "omelette"]', true),
(8, 'Vegetable Omelette', 'Fresh vegetables with cheese', 8.99, 1, 9, 'Eggs, mushrooms, tomatoes, spinach, cheese', '["breakfast", "omelette", "vegetarian"]', true),
(9, 'Hash Browns', 'Crispy golden hash browns', 3.99, 1, 10, 'Potatoes, oil, salt, pepper', '["breakfast", "side", "vegetarian"]', true),
(10, 'Fresh Fruit Cup', 'Seasonal fresh fruits', 4.49, 1, 10, 'Mixed seasonal fruits', '["breakfast", "side", "vegetarian", "healthy"]', true),
(11, 'Classic Cheeseburger', 'Beef patty with cheese, lettuce, tomato, and special sauce', 11.99, 3, 11, 'Beef patty, cheese, bun, lettuce, tomato, sauce', '["burger", "beef"]', true),
(12, 'Veggie Burger', 'Plant-based patty with avocado and sprouts', 10.99, 3, 11, 'Vegetable patty, bun, avocado, sprouts, sauce', '["burger", "vegetarian"]', true),
(13, 'BBQ Bacon Burger', 'Beef patty with bacon, BBQ sauce, and onion rings', 13.99, 3, 11, 'Beef patty, bacon, BBQ sauce, onion rings, bun', '["burger", "beef", "bacon"]', true),
(14, 'Grilled Chicken Sandwich', 'Grilled chicken breast with mayo and vegetables', 9.99, 3, 12, 'Chicken breast, bun, lettuce, tomato, mayo', '["sandwich", "chicken"]', true),
(15, 'Club Sandwich', 'Triple decker with turkey, bacon, and avocado', 10.99, 3, 12, 'Turkey, bacon, avocado, bread, lettuce, tomato', '["sandwich", "turkey", "bacon"]', true),
(16, 'Mediterranean Wrap', 'Hummus, vegetables, and feta in whole wheat wrap', 8.99, 3, 12, 'Hummus, vegetables, feta, whole wheat wrap', '["sandwich", "vegetarian", "wrap"]', true),
(17, 'Caesar Salad', 'Romaine lettuce with Caesar dressing and croutons', 7.99, 3, 13, 'Romaine, Caesar dressing, croutons, parmesan', '["salad", "vegetarian"]', true),
(18, 'Greek Salad', 'Fresh vegetables with feta and olives', 8.49, 3, 13, 'Cucumbers, tomatoes, olives, feta, olive oil', '["salad", "vegetarian"]', true),
(19, 'French Fries', 'Crispy golden fries', 3.99, 3, 14, 'Potatoes, oil, salt', '["side", "vegetarian"]', true),
(20, 'Onion Rings', 'Beer-battered onion rings', 4.99, 3, 14, 'Onions, flour, beer, oil', '["side", "vegetarian"]', true),
(21, 'Chocolate Cake', 'Rich chocolate layer cake', 5.99, 5, 15, 'Flour, sugar, cocoa, eggs, butter', '["dessert", "cake", "chocolate", "vegetarian"]', true),
(22, 'Apple Pie', 'Warm apple pie with cinnamon', 4.99, 5, 15, 'Apples, flour, sugar, cinnamon, butter', '["dessert", "pie", "vegetarian"]', true),
(23, 'Vanilla Ice Cream', 'Creamy vanilla ice cream', 3.99, 5, 16, 'Milk, cream, sugar, vanilla', '["dessert", "ice cream", "vegetarian"]', true),
(24, 'Chocolate Sundae', 'Ice cream with chocolate sauce and nuts', 5.49, 5, 16, 'Ice cream, chocolate sauce, nuts, whipped cream', '["dessert", "ice cream", "vegetarian"]', true),
(25, 'Chocolate Chip Cookies', 'Freshly baked chocolate chip cookies', 2.99, 5, 17, 'Flour, chocolate chips, butter, sugar, eggs', '["dessert", "cookies", "vegetarian"]', true),
(26, 'Coffee', 'Freshly brewed coffee', 2.49, 6, 18, 'Coffee beans, water', '["beverage", "hot", "vegetarian"]', true),
(27, 'Cappuccino', 'Espresso with steamed milk foam', 3.99, 6, 18, 'Espresso, milk, foam', '["beverage", "hot", "vegetarian"]', true),
(28, 'Hot Chocolate', 'Rich creamy hot chocolate', 3.49, 6, 18, 'Milk, chocolate, sugar', '["beverage", "hot", "vegetarian"]', true),
(29, 'Iced Tea', 'Refreshing iced tea', 2.99, 6, 19, 'Tea, water, ice, lemon', '["beverage", "cold", "vegetarian"]', true),
(30, 'Lemonade', 'Homemade lemonade', 3.49, 6, 19, 'Lemons, sugar, water, ice', '["beverage", "cold", "vegetarian"]', true),
(31, 'Berry Blast Smoothie', 'Mixed berries with yogurt', 5.99, 6, 20, 'Mixed berries, yogurt, honey, ice', '["beverage", "smoothie", "vegetarian"]', true),
(32, 'Tropical Smoothie', 'Mango and pineapple smoothie', 5.99, 6, 20, 'Mango, pineapple, yogurt, ice', '["beverage", "smoothie", "vegetarian"]', true);

-- Insert data into combo_meals
INSERT INTO combo_meals (combo_id, combo_name, description, price, includes_items, is_available, display_order) VALUES
(1, 'Breakfast Combo', 'Egg sandwich, hash browns, and coffee', 12.99, '[1, 9, 21]', true, 1),
(2, 'Burger Combo', 'Cheeseburger, fries, and drink', 15.99, '[11, 19, 25]', true, 2),
(3, 'Healthy Combo', 'Greek salad, fruit cup, and smoothie', 14.99, '[18, 10, 28]', true, 3),
(4, 'Student Special', 'Chicken sandwich, fries, and drink', 13.99, '[14, 19, 25]', true, 4);

-- Insert data into daily_specials
INSERT INTO daily_specials (special_id, item_id, special_date, special_price, description, is_active) VALUES
(1, 11, '2025-11-26', 10.99, 'Today''s special - Classic Cheeseburger at discounted price', true),
(2, 14, '2025-11-26', 8.99, 'Lunch special - Grilled Chicken Sandwich', true),
(3, 17, '2025-11-26', 6.99, 'Special price on Greek Salad for lunch', true);

-- Insert data into users
INSERT INTO users (user_id, username, email, password_hash, full_name, phone, role, is_active) VALUES
(1, 'admin', 'admin@tamccmealhouse.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', '(473) 444-1000', 'admin', true),
(2, 'Micaiah', 'MicaiahW5360@TAMCC.EDU.GD', '$2y$10$SNYI7LaLoUze.mRwTi2.AObE/UnEnGJcKGNtBZvKqE70qJOwNeK2S', 'Micaiah Walker', '', 'customer', true);

-- Create indexes
CREATE INDEX idx_menu_categories_parent ON menu_categories(parent_id);
CREATE INDEX idx_menu_categories_active ON menu_categories(is_active);
CREATE INDEX idx_menu_items_category ON menu_items(category_id);
CREATE INDEX idx_menu_items_subcategory ON menu_items(subcategory_id);
CREATE INDEX idx_menu_items_available ON menu_items(is_available);
CREATE INDEX idx_daily_specials_date ON daily_specials(special_date);
CREATE INDEX idx_orders_user_id ON orders(user_id);
CREATE INDEX idx_orders_status ON orders(order_status);

-- Create view for header menu categories (PostgreSQL version)
CREATE OR REPLACE VIEW header_menu_categories AS
SELECT 
    mc.category_id,
    mc.category_name,
    mc.description,
    mc.display_order,
    COUNT(DISTINCT mi.item_id) AS item_count,
    STRING_AGG(DISTINCT sc.category_name, '|' ORDER BY sc.display_order) AS subcategories
FROM menu_categories mc
LEFT JOIN menu_categories sc ON mc.category_id = sc.parent_id
LEFT JOIN menu_items mi ON mc.category_id = mi.category_id AND mi.is_available = true
WHERE mc.show_in_header = true 
    AND mc.is_active = true 
    AND mc.parent_id IS NULL
GROUP BY mc.category_id, mc.category_name, mc.description, mc.display_order
ORDER BY mc.display_order;