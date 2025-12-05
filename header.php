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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'TAMCC Mealhouse'; ?></title>
    <link rel="stylesheet" href="tamcc-mealhouse-style.css">
    <style>
        /* Reset and Base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            padding-top: 70px;
        }
        
        /* Header */
        .site-header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        
        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
        }
        
        /* Logo */
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        
        .logo-image {
            height: 40px;
            width: auto;
        }
        
        .logo-text {
            font-size: 1.25rem;
            font-weight: bold;
            color: #1e3a8a;
        }
        
        /* Desktop Navigation */
        .nav-menu {
            display: flex;
            list-style: none;
            gap: 20px;
            align-items: center;
        }
        
        .nav-link {
            text-decoration: none;
            color: #333;
            padding: 8px 16px;
            border-radius: 4px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover,
        .nav-link.active {
            background: #3b82f6;
            color: white;
        }
        
        .btn-primary {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
        }
        
        /* Mobile Menu Button - HIDDEN ON DESKTOP */
        .hamburger {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            width: 30px;
            height: 24px;
            position: relative;
            z-index: 1001;
        }
        
        .hamburger span {
            display: block;
            width: 100%;
            height: 3px;
            background: #333;
            position: absolute;
            left: 0;
            transition: 0.3s;
        }
        
        .hamburger span:nth-child(1) {
            top: 0;
        }
        
        .hamburger span:nth-child(2) {
            top: 50%;
            transform: translateY(-50%);
        }
        
        .hamburger span:nth-child(3) {
            bottom: 0;
        }
        
        /* Mobile Menu Styles */
        @media (max-width: 768px) {
            body {
                padding-top: 60px;
            }
            
            .hamburger {
                display: block;
            }
            
            /* Animated hamburger when active */
            .hamburger.active span:nth-child(1) {
                transform: rotate(45deg);
                top: 50%;
            }
            
            .hamburger.active span:nth-child(2) {
                opacity: 0;
            }
            
            .hamburger.active span:nth-child(3) {
                transform: rotate(-45deg);
                bottom: 50%;
            }
            
            /* Mobile navigation menu */
            .nav-menu {
                position: fixed;
                top: 60px;
                left: -100%;
                width: 100%;
                height: calc(100vh - 60px);
                background: white;
                flex-direction: column;
                align-items: stretch;
                padding: 20px;
                transition: left 0.3s ease;
                overflow-y: auto;
                box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                gap: 0;
            }
            
            .nav-menu.active {
                left: 0;
            }
            
            .nav-menu li {
                width: 100%;
                margin-bottom: 10px;
            }
            
            .nav-link {
                display: block;
                padding: 15px;
                border-bottom: 1px solid #eee;
                text-align: center;
            }
            
            .nav-link:last-child {
                border-bottom: none;
            }
            
            .logo-text {
                font-size: 1rem;
            }
            
            .logo-image {
                height: 35px;
            }
        }
        
        @media (max-width: 480px) {
            body {
                padding-top: 55px;
            }
            
            .nav-menu {
                top: 55px;
                height: calc(100vh - 55px);
            }
            
            .logo-text {
                font-size: 0.9rem;
            }
            
            .logo-image {
                height: 30px;
            }
        }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="header-container">
            <nav class="navbar">
                <a href="index.php" class="logo">
                    <img src="ta-logo-2048x683 (1).png" alt="T.A. Marryshow Community College" class="logo-image">
                    <span class="logo-text">TAMCC Mealhouse</span>
                </a>
                
                <!-- Mobile Menu Button -->
                <button class="hamburger" id="hamburgerBtn">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                
                <!-- Navigation Menu -->
                <ul class="nav-menu" id="navMenu">
                    <li><a href="index.php" class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">Home</a></li>
                    <li><a href="menu.php" class="nav-link <?php echo $current_page == 'menu.php' ? 'active' : ''; ?>">Menu</a></li>
                    <li><a href="cart.php" class="nav-link <?php echo $current_page == 'cart.php' ? 'active' : ''; ?>">Cart</a></li>
                    <li><a href="about.php" class="nav-link <?php echo $current_page == 'about.php' ? 'active' : ''; ?>">About</a></li>
                    <li><a href="contact.php" class="nav-link <?php echo $current_page == 'contact.php' ? 'active' : ''; ?>">Contact</a></li>
                    
                    <?php if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                        <li><a href="dashboard.php" class="nav-link <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">Dashboard</a></li>
                        <li><a href="logout.php" class="btn-primary">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php" class="btn-primary">Login</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <script>
        // Simple mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const hamburgerBtn = document.getElementById('hamburgerBtn');
            const navMenu = document.getElementById('navMenu');
            
            // Toggle menu when hamburger is clicked
            hamburgerBtn.addEventListener('click', function() {
                this.classList.toggle('active');
                navMenu.classList.toggle('active');
                
                // Prevent body scroll when menu is open
                if (navMenu.classList.contains('active')) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            });
            
            // Close menu when clicking on a link
            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', function() {
                    hamburgerBtn.classList.remove('active');
                    navMenu.classList.remove('active');
                    document.body.style.overflow = '';
                });
            });
            
            // Close menu when clicking outside
            document.addEventListener('click', function(event) {
                if (!navMenu.contains(event.target) && !hamburgerBtn.contains(event.target)) {
                    hamburgerBtn.classList.remove('active');
                    navMenu.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
            
            // Close menu on window resize if we go back to desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    hamburgerBtn.classList.remove('active');
                    navMenu.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
        });
    </script>
</body>
</html>