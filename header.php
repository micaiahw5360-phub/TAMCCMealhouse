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
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        /* Header Styles */
        .site-header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.98);
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            padding: 0;
        }

        .site-header.scrolled {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        body { 
            padding-top: 80px;
            background: var(--background, #f8fafc);
            margin: 0;
        }
        
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            position: relative;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }
        
        .logo {
            display: flex;
            align-items: center;
            text-decoration: none;
            gap: 12px;
            z-index: 1001;
        }
        
        .logo-image {
            height: 40px;
            width: auto;
            display: block;
        }
        
        .logo-text {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e3a8a;
            white-space: nowrap;
        }
        
        /* Desktop Navigation */
        .nav-menu {
            display: flex;
            align-items: center;
            list-style: none;
            margin: 0;
            padding: 0;
            gap: 1rem;
        }

        .nav-link {
            text-decoration: none;
            color: #374151;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 0.95rem;
            white-space: nowrap;
        }
        
        .nav-link:hover,
        .nav-link.active {
            background: #3b82f6;
            color: white;
        }
        
        /* Mobile Menu Button */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0.5rem;
            z-index: 1001;
            width: 44px;
            height: 44px;
            align-items: center;
            justify-content: center;
        }
        
        .hamburger {
            display: block;
            width: 24px;
            height: 2px;
            background: #374151;
            position: relative;
            transition: all 0.3s ease;
        }
        
        .hamburger::before,
        .hamburger::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 2px;
            background: #374151;
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
            body {
                padding-top: 70px;
            }
            
            .mobile-menu-btn {
                display: flex;
            }
            
            .nav-menu {
                position: fixed;
                top: 70px;
                left: 0;
                width: 100%;
                height: calc(100vh - 70px);
                background: white;
                flex-direction: column;
                align-items: stretch;
                padding: 2rem 1.5rem;
                gap: 0;
                transition: transform 0.3s ease;
                transform: translateX(-100%);
                overflow-y: auto;
                z-index: 999;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                margin: 0;
            }
            
            .nav-menu.active {
                transform: translateX(0);
            }
            
            .nav-menu li {
                width: 100%;
                margin: 0;
                padding: 0;
            }
            
            .nav-link {
                display: block;
                padding: 1rem;
                border-bottom: 1px solid #e5e7eb;
                text-align: left;
                border-radius: 0;
                margin: 0;
                width: 100%;
            }
            
            .nav-link:last-child {
                border-bottom: none;
            }
            
            .nav-link:hover,
            .nav-link.active {
                background: #3b82f6;
                color: white;
            }
            
            /* Mobile dropdown */
            .mega-dropdown-content {
                display: none;
                padding: 1rem;
                background: #f9fafb;
                margin-top: 0.5rem;
                border-radius: 8px;
            }
            
            .mega-dropdown.active .mega-dropdown-content {
                display: block;
            }
            
            .mega-dropdown-columns {
                display: block;
            }
            
            .mega-dropdown-column {
                margin-bottom: 1.5rem;
            }
            
            .mega-dropdown-column h4 {
                color: #1e3a8a;
                margin-bottom: 0.75rem;
                font-size: 1rem;
                font-weight: 600;
            }
            
            .dropdown-item {
                display: block;
                padding: 0.5rem 0;
                color: #374151;
                text-decoration: none;
                transition: all 0.3s ease;
                border-bottom: 1px solid transparent;
            }
            
            .dropdown-item:hover {
                color: #3b82f6;
                transform: translateX(5px);
            }
            
            /* Mobile dropdown toggle */
            .mega-dropdown > .nav-link {
                position: relative;
                padding-right: 3rem;
            }
            
            .mega-dropdown > .nav-link::after {
                content: '▼';
                position: absolute;
                right: 1rem;
                top: 50%;
                transform: translateY(-50%);
                font-size: 0.8rem;
                transition: transform 0.3s ease;
            }
            
            .mega-dropdown.active > .nav-link::after {
                transform: translateY(-50%) rotate(180deg);
            }
            
            .logo-text {
                font-size: 1rem;
            }
        }
        
        @media (max-width: 768px) {
            body {
                padding-top: 65px;
            }
            
            .nav-menu {
                top: 65px;
                height: calc(100vh - 65px);
            }
            
            .logo-image {
                height: 35px;
            }
            
            .logo-text {
                font-size: 0.9rem;
            }
        }
        
        @media (max-width: 480px) {
            body {
                padding-top: 60px;
            }
            
            .nav-menu {
                top: 60px;
                height: calc(100vh - 60px);
            }
            
            .logo-image {
                height: 30px;
            }
            
            .logo-text {
                font-size: 0.85rem;
            }
            
            .navbar {
                padding: 0.75rem 1rem;
            }
        }
        
        .page-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
            width: 100%;
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
                    
                    <li class="mega-dropdown">
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
                            <a href="logout.php" class="nav-link" style="background: #ef4444; color: white; border: none;">
                                Logout
                            </a>
                        </li>
                    <?php else: ?>
                        <li><a href="login.php" class="nav-link" style="background: #3b82f6; color: white;">Login</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <script>
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const navMenu = document.getElementById('navMenu');
            const dropdowns = document.querySelectorAll('.mega-dropdown');
            
            // Mobile menu toggle
            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    this.classList.toggle('active');
                    navMenu.classList.toggle('active');
                    
                    // Prevent body scroll when menu is open
                    if (navMenu.classList.contains('active')) {
                        document.body.style.overflow = 'hidden';
                    } else {
                        document.body.style.overflow = '';
                    }
                });
            }
            
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
            
            // Close mobile menu when clicking a link
            document.querySelectorAll('.nav-menu a').forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 1024) {
                        mobileMenuBtn.classList.remove('active');
                        navMenu.classList.remove('active');
                        document.body.style.overflow = '';
                        
                        // Close all dropdowns
                        dropdowns.forEach(dropdown => {
                            dropdown.classList.remove('active');
                        });
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
                    document.body.style.overflow = '';
                    
                    // Close all dropdowns
                    dropdowns.forEach(dropdown => {
                        dropdown.classList.remove('active');
                    });
                }
            });
            
            // Close menu on window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 1024) {
                    mobileMenuBtn.classList.remove('active');
                    navMenu.classList.remove('active');
                    document.body.style.overflow = '';
                    
                    // Close all dropdowns
                    dropdowns.forEach(dropdown => {
                        dropdown.classList.remove('active');
                    });
                }
            });
            
            // Scroll effect for header
            window.addEventListener('scroll', function() {
                const header = document.querySelector('.site-header');
                const scrolled = window.pageYOffset > 10;
                
                if (scrolled) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
                }
            });
            
            // Initialize header state
            const header = document.querySelector('.site-header');
            if (window.pageYOffset > 10) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    </script>
</body>
</html>