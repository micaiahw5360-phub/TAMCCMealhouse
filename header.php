<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// SAFELY include database configuration
$config_file = 'config.php';
if (file_exists($config_file)) {
    require_once $config_file;
} else {
    // If config.php doesn't exist, set dummy values
    $pdo = null;
    define('SITE_NAME', 'TAMCC Mealhouse');
    define('SITE_URL', 'https://tamccmealhouse.onrender.com');
}

// Get current page for active navigation highlighting
$current_page = basename($_SERVER['PHP_SELF']);

// Functions for menu categories - SAFE VERSIONS
function getHeaderMenuCategories($pdo) {
    // If no database connection, return dummy data
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
        $sql = "SELECT * FROM header_menu_categories ORDER BY display_order";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // If query fails, return dummy data
        return [
            ['category_id' => 1, 'category_name' => 'Breakfast'],
            ['category_id' => 2, 'category_name' => 'Lunch'],
            ['category_id' => 3, 'category_name' => 'Dinner'],
        ];
    }
}

function getSubcategories($pdo, $parent_id) {
    // If no database connection, return empty array
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

// Get menu categories for dropdown - SAFE CALL
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
        /* FIXED: Ensure header stays at top */
        .site-header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-color);
        }

        /* Ensure header doesn't overlap content */
        body { 
            padding-top: 80px; 
            background: var(--background);
            margin: 0;
        }
        
        /* Header Dropdown Menu Styles */
        .nav-menu {
            position: relative;
            display: flex;
            align-items: center;
            list-style: none;
            margin: 0;
            padding: 0;
        }

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

        /* Mega dropdown for categories with subcategories */
        .mega-dropdown {
            position: static;
        }

        .mega-dropdown-content {
            display: none;
            position: absolute;
            left: 0;
            right: 0;
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

        /* Improved Mobile Menu Button */
        .mobile-menu-btn {
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: none;
            border: none;
            cursor: pointer;
            padding: var(--space-sm);
            width: 44px;
            height: 44px;
            z-index: 1001;
        }

        .mobile-menu-btn span {
            display: block;
            height: 3px;
            width: 24px;
            background: var(--text-light);
            margin: 3px 0;
            transition: all 0.3s ease;
            border-radius: 3px;
        }

        .mobile-menu-btn.active span:nth-child(1) {
            transform: rotate(45deg) translate(6px, 6px);
            width: 24px;
        }

        .mobile-menu-btn.active span:nth-child(2) {
            opacity: 0;
            transform: translateX(-10px);
        }

        .mobile-menu-btn.active span:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -7px);
            width: 24px;
        }

        .mobile-menu {
            display: none;
            position: fixed;
            top: 80px;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--card-bg);
            z-index: 999;
            transform: translateX(-100%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }

        .mobile-menu.active {
            transform: translateX(0);
        }

        .mobile-menu-content {
            padding: var(--space-lg);
            display: flex;
            flex-direction: column;
            gap: var(--space-sm);
        }

        .mobile-nav-link {
            padding: 16px 20px;
            text-decoration: none;
            color: var(--text-light);
            border-radius: var(--radius-md);
            transition: all var(--transition-normal);
            font-weight: 500;
            font-size: 16px;
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            min-height: 56px;
        }

        .mobile-nav-link:hover,
        .mobile-nav-link.active {
            background: var(--accent-blue);
            color: var(--text-light);
            transform: translateX(8px);
        }

        .mobile-nav-link.btn-primary {
            background: var(--accent-blue);
            color: var(--text-light);
            text-align: center;
            border: none;
        }

        .mobile-nav-link.btn-primary:hover {
            background: var(--accent-red);
            transform: translateX(8px);
        }

        /* Mobile Category Dropdown Styles */
        .mobile-menu-category-dropdown {
            margin-bottom: var(--space-sm);
        }

        .mobile-category-toggle {
            width: 100%;
            text-align: left;
            background: none;
            border: 1px solid var(--border-color);
            color: var(--text-light);
            padding: 16px 20px;
            font-size: 16px;
            font-weight: 500;
            border-radius: var(--radius-md);
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            min-height: 56px;
        }

        .mobile-category-toggle:after {
            content: '▾';
            transition: transform 0.3s ease;
        }

        .mobile-category-toggle.active:after {
            transform: rotate(180deg);
        }

        .mobile-categories-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            padding-left: var(--space-md);
            margin-top: var(--space-sm);
        }

        .mobile-categories-content.active {
            max-height: 500px;
            overflow-y: auto;
        }

        .mobile-category-group {
            margin-bottom: var(--space-md);
            padding-left: var(--space-sm);
            border-left: 2px solid var(--accent-blue);
        }

        .mobile-category-title {
            display: block;
            padding: 12px 16px;
            color: var(--accent-blue);
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            border-radius: var(--radius-sm);
            transition: all var(--transition-normal);
            margin-bottom: var(--space-xs);
        }

        .mobile-category-title:hover {
            background: rgba(var(--accent-blue-rgb, 30, 136, 229), 0.1);
            transform: translateX(4px);
        }

        .mobile-subcategories {
            padding-left: var(--space-md);
        }

        .mobile-subcategory {
            display: block;
            padding: 10px 16px;
            color: var(--text-light);
            text-decoration: none;
            font-size: 14px;
            border-radius: var(--radius-sm);
            transition: all var(--transition-normal);
            margin-bottom: 2px;
            opacity: 0.9;
        }

        .mobile-subcategory:hover {
            background: rgba(var(--accent-blue-rgb, 30, 136, 229), 0.1);
            transform: translateX(4px);
            opacity: 1;
        }

        body.mobile-menu-open {
            overflow: hidden;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .mobile-menu-btn,
            .mobile-menu {
                display: block;
            }
            
            .nav-menu {
                display: none !important;
            }

            .mega-dropdown-content {
                display: none !important;
            }

            body.mobile-menu-open {
                overflow: hidden;
                position: fixed;
                width: 100%;
                height: 100%;
            }

            /* Better mobile spacing */
            .mobile-menu-content {
                padding-bottom: calc(env(safe-area-inset-bottom) + var(--space-xl));
            }

            /* Ensure iOS safe areas */
            @supports (padding: max(0px)) {
                .mobile-menu {
                    padding-left: max(var(--space-lg), env(safe-area-inset-left));
                    padding-right: max(var(--space-lg), env(safe-area-inset-right));
                }
            }
        }

        @media (max-width: 480px) {
            .mobile-menu {
                top: 70px;
            }

            body {
                padding-top: 70px;
            }

            .mobile-nav-link,
            .mobile-category-toggle {
                padding: 14px 18px;
                font-size: 15px;
                min-height: 52px;
            }
        }

        /* Prevent body scroll when mobile menu is open */
        body.mobile-menu-open {
            overflow: hidden;
        }

        /* Smooth overlay effect */
        .mobile-menu:before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .mobile-menu.active:before {
            opacity: 1;
            pointer-events: all;
        }

        /* Ensure content starts below fixed header */
        main {
            min-height: calc(100vh - 80px);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="site-header">
        <div class="page-container">
            <nav class="navbar">
                <a href="index.php" class="logo">
                    <img src="ta-logo-2048x683 (1).png" alt="T.A. Marryshow Community College" class="logo-image">
                    <span class="logo-text">TAMCC Mealhouse</span>
                </a>
                <ul class="nav-menu">
                    <li><a href="index.php" class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">Home</a></li>
                    
                    <!-- Menu Dropdown -->
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
                
                <!-- Mobile Menu Button -->
                <button class="mobile-menu-btn" id="mobileMenuBtn">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </nav>
        </div>
    </header>

    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobileMenu">
        <div class="mobile-menu-content">
            <a href="index.php" class="mobile-nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">Home</a>
            
            <!-- Mobile Menu Categories Dropdown -->
            <div class="mobile-menu-category-dropdown">
                <button class="mobile-nav-link mobile-category-toggle" data-target="mobile-categories-content">
                    Menu ▾
                </button>
                <div id="mobile-categories-content" class="mobile-categories-content">
                    <?php foreach ($menuCategories as $category): ?>
                        <?php $subcategories = getSubcategories($pdo, $category['category_id']); ?>
                        <div class="mobile-category-group">
                            <a href="menu.php?category=<?php echo $category['category_id']; ?>" 
                               class="mobile-category-title">
                                <?php echo htmlspecialchars($category['category_name']); ?>
                            </a>
                            <?php if (!empty($subcategories)): ?>
                                <div class="mobile-subcategories">
                                    <?php foreach ($subcategories as $subcategory): ?>
                                        <a href="menu.php?category=<?php echo $category['category_id']; ?>&subcategory=<?php echo $subcategory['category_id']; ?>" 
                                           class="mobile-subcategory">
                                            <?php echo htmlspecialchars($subcategory['category_name']); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <a href="cart.php" class="mobile-nav-link <?php echo $current_page == 'cart.php' ? 'active' : ''; ?>">Cart</a>
            <a href="about.php" class="mobile-nav-link <?php echo $current_page == 'about.php' ? 'active' : ''; ?>">About</a>
            <a href="contact.php" class="mobile-nav-link <?php echo $current_page == 'contact.php' ? 'active' : ''; ?>">Contact</a>
            
            <?php if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                <a href="dashboard.php" class="mobile-nav-link <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">Dashboard</a>
                <a href="logout.php" class="mobile-nav-link btn-primary">Logout (<?php echo htmlspecialchars($_SESSION["username"]); ?>)</a>
            <?php else: ?>
                <a href="login.php" class="mobile-nav-link btn-primary">Login</a>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Enhanced header scroll effect
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

        // Enhanced mobile menu functionality
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const mobileMenu = document.getElementById('mobileMenu');
            const body = document.body;

            if (mobileMenuBtn && mobileMenu) {
                mobileMenuBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const isActive = mobileMenu.classList.toggle('active');
                    mobileMenuBtn.classList.toggle('active');
                    body.classList.toggle('mobile-menu-open', isActive);
                    
                    // Prevent body scroll
                    if (isActive) {
                        document.documentElement.style.overflow = 'hidden';
                    } else {
                        document.documentElement.style.overflow = '';
                    }
                });

                // Mobile category dropdown toggle
                const categoryToggles = document.querySelectorAll('.mobile-category-toggle');
                categoryToggles.forEach(toggle => {
                    toggle.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        const targetId = this.getAttribute('data-target');
                        const target = document.getElementById(targetId);
                        if (target) {
                            const isActive = target.classList.toggle('active');
                            this.classList.toggle('active', isActive);
                            
                            // Close other open dropdowns
                            categoryToggles.forEach(otherToggle => {
                                if (otherToggle !== this) {
                                    const otherTargetId = otherToggle.getAttribute('data-target');
                                    const otherTarget = document.getElementById(otherTargetId);
                                    if (otherTarget) {
                                        otherTarget.classList.remove('active');
                                        otherToggle.classList.remove('active');
                                    }
                                }
                            });
                        }
                    });
                });

                // Close mobile menu when clicking on a link
                const mobileLinks = mobileMenu.querySelectorAll('a');
                mobileLinks.forEach(link => {
                    link.addEventListener('click', function(e) {
                        // Don't close for category dropdown toggle buttons
                        if (!this.classList.contains('mobile-category-toggle')) {
                            closeMobileMenu();
                        }
                    });
                });

                // Close mobile menu when clicking outside
                document.addEventListener('click', function(event) {
                    if (!mobileMenu.contains(event.target) && !mobileMenuBtn.contains(event.target)) {
                        closeMobileMenu();
                    }
                });

                // Close mobile menu on escape key
                document.addEventListener('keydown', function(event) {
                    if (event.key === 'Escape') {
                        closeMobileMenu();
                    }
                });

                function closeMobileMenu() {
                    mobileMenu.classList.remove('active');
                    mobileMenuBtn.classList.remove('active');
                    body.classList.remove('mobile-menu-open');
                    document.documentElement.style.overflow = '';
                    
                    // Close all category dropdowns
                    document.querySelectorAll('.mobile-categories-content').forEach(content => {
                        content.classList.remove('active');
                    });
                    document.querySelectorAll('.mobile-category-toggle').forEach(toggle => {
                        toggle.classList.remove('active');
                    });
                }
            }

            // Loading state for logout button
            const logoutBtn = document.getElementById('logout-btn');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', function(e) {
                    this.classList.add('btn-loading');
                    setTimeout(() => {
                        this.classList.remove('btn-loading');
                    }, 2000);
                });
            }

            // Add hover effects to navigation
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                });
                
                link.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });

            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        // Calculate the position considering the fixed header
                        const headerHeight = document.querySelector('.site-header').offsetHeight;
                        const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - headerHeight;
                        
                        window.scrollTo({
                            top: targetPosition,
                            behavior: 'smooth'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>