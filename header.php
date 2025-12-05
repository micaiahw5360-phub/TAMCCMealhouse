<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$config_file = 'config.php';
if (file_exists($config_file)) {
    require_once $config_file;
} else {
    $pdo = null;
    define('SITE_NAME', 'TAMCC Mealhouse');
    define('SITE_URL', 'https://tamccmealhouse.onrender.com');
}

$current_page = basename($_SERVER['PHP_SELF']);

function getHeaderMenuCategories($pdo) {
    if (!$pdo) {
        return [
            ['category_id' => 1, 'category_name' => 'Breakfast'],
            ['category_id' => 2, 'category_name' => 'Lunch'],
            ['category_id' => 3, 'category_name' => 'Dinner'],
            ['category_id' => 4, 'category_name' => 'Beverages'],
            ['category_id' => 5, 'category_name' => 'Desserts'],
        ];
    }
    
    try {
        $sql = "SELECT * FROM menu_categories WHERE parent_id IS NULL AND is_active = TRUE ORDER BY display_order";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [
            ['category_id' => 1, 'category_name' => 'Breakfast'],
            ['category_id' => 2, 'category_name' => 'Lunch'],
            ['category_id' => 3, 'category_name' => 'Dinner'],
        ];
    }
}

function getSubcategories($pdo, $parent_id) {
    if (!$pdo) {
        return [];
    }
    
    try {
        $sql = "SELECT * FROM menu_categories WHERE parent_id = ? AND is_active = TRUE ORDER BY display_order";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$parent_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

$menuCategories = getHeaderMenuCategories($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'TAMCC Mealhouse'; ?></title>
    <link rel="stylesheet" href="tamcc-mealhouse-style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* Enhanced Header Styles */
        .site-header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        body { 
            padding-top: 70px; /* Reduced for mobile */
            background: var(--background);
            margin: 0;
        }
        
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: var(--space-md) 0;
            position: relative;
        }
        
        .logo {
            display: flex;
            align-items: center;
            text-decoration: none;
            gap: var(--space-md);
        }
        
        .logo-image {
            height: 40px;
            width: auto;
        }
        
        .logo-text {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-primary);
        }
        
        .nav-menu {
            display: flex;
            align-items: center;
            list-style: none;
            margin: 0;
            padding: 0;
            gap: var(--space-md);
        }

        .nav-link {
            text-decoration: none;
            color: var(--text-primary);
            padding: var(--space-sm) var(--space-md);
            border-radius: var(--radius-md);
            transition: all 0.3s ease;
            font-weight: 500;
            white-space: nowrap;
        }
        
        .nav-link:hover,
        .nav-link.active {
            background: var(--accent-blue);
            color: white;
        }
        
        /* Dropdown Styles */
        .dropdown {
            position: relative;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background: var(--card-bg);
            min-width: 200px;
            box-shadow: var(--shadow-lg);
            z-index: 1000;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
            top: 100%;
            left: 0;
        }

        .dropdown-content a {
            color: var(--text-light);
            padding: var(--space-md) var(--space-lg);
            text-decoration: none;
            display: block;
            transition: all var(--transition-normal);
            border-bottom: 1px solid var(--border-color);
        }

        .dropdown-content a:last-child {
            border-bottom: none;
        }

        .dropdown-content a:hover {
            background: var(--accent-blue);
            color: var(--text-light);
            transform: translateX(5px);
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .mega-dropdown {
            position: static;
        }

        .mega-dropdown-content {
            display: none;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            width: 90%;
            max-width: 800px;
            background: var(--card-bg);
            box-shadow: var(--shadow-lg);
            z-index: 1000;
            padding: var(--space-xl);
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
        }

        .mega-dropdown:hover .mega-dropdown-content {
            display: block;
        }

        .mega-dropdown-columns {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: var(--space-lg);
        }

        .mega-dropdown-column h4 {
            color: var(--accent-blue);
            margin-bottom: var(--space-md);
            font-size: 16px;
            font-weight: 600;
        }

        .mega-dropdown-column a {
            display: block;
            padding: var(--space-sm) 0;
            color: var(--text-light);
            text-decoration: none;
            transition: all var(--transition-normal);
            border-bottom: 1px solid transparent;
        }

        .mega-dropdown-column a:hover {
            color: var(--accent-blue);
            transform: translateX(5px);
            border-bottom: 1px solid var(--accent-blue);
        }
        
        /* Mobile Menu Button */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: var(--space-sm);
            z-index: 1001;
        }
        
        .hamburger {
            display: block;
            width: 25px;
            height: 3px;
            background: var(--text-primary);
            position: relative;
            transition: all 0.3s ease;
        }
        
        .hamburger::before,
        .hamburger::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 3px;
            background: var(--text-primary);
            left: 0;
            transition: all 0.3s ease;
        }
        
        .hamburger::before {
            top: -8px;
        }
        
        .hamburger::after {
            bottom: -8px;
        }
        
        .mobile-menu-btn.active .hamburger {
            background: transparent;
        }
        
        .mobile-menu-btn.active .hamburger::before {
            transform: rotate(45deg);
            top: 0;
        }
        
        .mobile-menu-btn.active .hamburger::after {
            transform: rotate(-45deg);
            bottom: 0;
        }
        
        /* Mobile Menu Styles */
        @media (max-width: 1024px) {
            .mobile-menu-btn {
                display: block;
            }
            
            .nav-menu {
                position: fixed;
                top: 70px;
                left: -100%;
                width: 100%;
                height: calc(100vh - 70px);
                background: var(--card-bg);
                flex-direction: column;
                align-items: stretch;
                padding: var(--space-xl);
                gap: 0;
                transition: left 0.3s ease;
                overflow-y: auto;
                z-index: 999;
            }
            
            .nav-menu.active {
                left: 0;
            }
            
            .nav-menu li {
                width: 100%;
            }
            
            .nav-link {
                display: block;
                padding: var(--space-lg);
                border-bottom: 1px solid var(--border-color);
                text-align: center;
            }
            
            .dropdown-content,
            .mega-dropdown-content {
                position: static;
                display: none;
                width: 100%;
                box-shadow: none;
                border: none;
                padding: var(--space-md);
                background: rgba(0,0,0,0.03);
            }
            
            .dropdown.active .dropdown-content,
            .mega-dropdown.active .mega-dropdown-content {
                display: block;
            }
            
            .mega-dropdown-columns {
                grid-template-columns: 1fr;
            }
            
            /* Mobile dropdown toggle */
            .dropdown > .nav-link,
            .mega-dropdown > .nav-link {
                position: relative;
                padding-right: var(--space-2xl);
            }
            
            .dropdown > .nav-link::after,
            .mega-dropdown > .nav-link::after {
                content: '▾';
                position: absolute;
                right: var(--space-lg);
                transition: transform 0.3s ease;
            }
            
            .dropdown.active > .nav-link::after,
            .mega-dropdown.active > .nav-link::after {
                transform: rotate(180deg);
            }
            
            .logo-text {
                font-size: 1rem;
            }
            
            body {
                padding-top: 70px;
            }
        }
        
        @media (max-width: 480px) {
            .logo-image {
                height: 30px;
            }
            
            .logo-text {
                font-size: 0.875rem;
            }
            
            body {
                padding-top: 60px;
            }
            
            .site-header {
                padding: 0 var(--space-md);
            }
        }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="page-container">
            <nav class="navbar">
                <a href="index.php" class="logo">
                    <img src="ta-logo-2048x683 (1).png" alt="T.A. Marryshow Community College" class="logo-image">
                    <span class="logo-text">TAMCC Mealhouse</span>
                </a>
                
                <button class="mobile-menu-btn" id="mobileMenuBtn">
                    <span class="hamburger"></span>
                </button>
                
                <ul class="nav-menu" id="navMenu">
                    <li><a href="index.php" class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">Home</a></li>
                    
                    <li class="dropdown mega-dropdown">
                        <a href="menu.php" class="nav-link <?php echo $current_page == 'menu.php' ? 'active' : ''; ?>">
                            Menu ▾
                        </a>
                        <div class="mega-dropdown-content">
                            <div class="mega-dropdown-columns">
                                <?php foreach ($menuCategories as $category): ?>
                                    <?php $subcategories = getSubcategories($pdo, $category['category_id']); ?>
                                    <div class="mega-dropdown-column">
                                        <h4><?php echo htmlspecialchars($category['category_name']); ?></h4>
                                        <a href="menu.php?category=<?php echo $category['category_id']; ?>" class="dropdown-item">
                                            View All <?php echo htmlspecialchars($category['category_name']); ?>
                                        </a>
                                        <?php if (!empty($subcategories)): ?>
                                            <?php foreach ($subcategories as $subcategory): ?>
                                                <a href="menu.php?category=<?php echo $category['category_id']; ?>&subcategory=<?php echo $subcategory['category_id']; ?>" class="dropdown-item">
                                                    <?php echo htmlspecialchars($subcategory['category_name']); ?>
                                                </a>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </li>
                    
                    <li><a href="cart.php" class="nav-link <?php echo $current_page == 'cart.php' ? 'active' : ''; ?>">Cart</a></li>
                    <li><a href="about.php" class="nav-link <?php echo $current_page == 'about.php' ? 'active' : ''; ?>">About</a></li>
                    <li><a href="contact.php" class="nav-link <?php echo $current_page == 'contact.php' ? 'active' : ''; ?>">Contact</a></li>
            
                    <?php if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                        <li><a href="dashboard.php" class="nav-link <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">Dashboard</a></li>
                        <li>
                            <a href="logout.php" class="btn btn-primary btn-sm" id="logout-btn">
                                Logout
                                <div class="loading-spinner"></div>
                            </a>
                        </li>
                    <?php else: ?>
                        <li><a href="login.php" class="btn btn-primary btn-sm">Login</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <script>
        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const navMenu = document.getElementById('navMenu');
        const dropdowns = document.querySelectorAll('.dropdown, .mega-dropdown');
        
        mobileMenuBtn.addEventListener('click', function() {
            this.classList.toggle('active');
            navMenu.classList.toggle('active');
        });
        
        // Mobile dropdown toggle
        dropdowns.forEach(dropdown => {
            const link = dropdown.querySelector('.nav-link');
            
            link.addEventListener('click', function(e) {
                if (window.innerWidth <= 1024) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Close other dropdowns
                    dropdowns.forEach(other => {
                        if (other !== dropdown && other.classList.contains('active')) {
                            other.classList.remove('active');
                        }
                    });
                    
                    dropdown.classList.toggle('active');
                }
            });
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 1024 && 
                !navMenu.contains(e.target) && 
                !mobileMenuBtn.contains(e.target) &&
                navMenu.classList.contains('active')) {
                mobileMenuBtn.classList.remove('active');
                navMenu.classList.remove('active');
                dropdowns.forEach(dropdown => dropdown.classList.remove('active'));
            }
        });
        
        // Scroll effect
        window.addEventListener('scroll', function() {
            const header = document.querySelector('.site-header');
            const scrolled = window.pageYOffset > 50;
            
            if (scrolled) {
                header.classList.add('scrolled');
                header.style.backdropFilter = 'blur(20px)';
                header.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.1)';
            } else {
                header.classList.remove('scrolled');
                header.style.backdropFilter = 'blur(10px)';
                header.style.boxShadow = 'none';
            }
        });
    </script>
</body>
</html>