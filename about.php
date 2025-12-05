<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - TAMCC Mealhouse</title>
    <link rel="stylesheet" href="tamcc-mealhouse-style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <?php 
    require_once 'header.php';
?>


    <!-- Page Hero -->
    <section class="hero-section">
        <div class="page-container">
            <div class="text-center">
                <h1 class="h1 text-white mb-lg">Our Story</h1>
                <p class="text-lead text-white" style="opacity: 0.9;">
                    Serving the TAMCC community with passion and purpose
                </p>
            </div>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="section">
        <div class="page-container">
            <div class="grid grid-2 align-center gap-2xl">
                <div>
                    <h2 class="h2 mb-lg">Our Mission</h2>
                    <p class="text-lead text-muted mb-xl">
                        To provide exceptional campus dining experiences that nourish both body and mind, 
                        while fostering community and supporting student success at T.A. Marryshow Community College.
                    </p>
                    <div class="d-flex gap-md">
                        <div class="card p-lg text-center">
                            <div class="text-green" style="font-size: 2rem;">🎯</div>
                            <h4 class="h6 mt-md">Quality First</h4>
                            <p class="text-small text-muted">Premium ingredients, exceptional taste</p>
                        </div>
                        <div class="card p-lg text-center">
                            <div class="text-green" style="font-size: 2rem;">🤝</div>
                            <h4 class="h6 mt-md">Community Focus</h4>
                            <p class="text-small text-muted">Serving our campus family</p>
                        </div>
                    </div>
                </div>
                <div class="card p-2xl">
                    <div class="text-green" style="font-size: 4rem; text-align: center;">🏫</div>
                    <h3 class="h4 text-center mt-lg">TAMCC Partnership</h3>
                    <p class="text-body text-muted text-center">
                        As the official dining partner of T.A. Marryshow Community College, 
                        we're committed to supporting academic excellence through quality nutrition.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="section bg-cream">
        <div class="page-container">
            <div class="text-center mb-4xl">
                <h2 class="h2 mb-lg">Our Values</h2>
                <p class="text-lead text-muted">The principles that guide everything we do</p>
            </div>
            
            <div class="grid grid-3">
                <div class="card p-xl text-center">
                    <div class="text-green" style="font-size: 3rem;">🌱</div>
                    <h3 class="h4 mt-lg">Fresh & Local</h3>
                    <p class="text-body text-muted">
                        We prioritize locally sourced ingredients and prepare meals fresh daily, 
                        supporting Grenadian producers whenever possible.
                    </p>
                </div>
                
                <div class="card p-xl text-center">
                    <div class="text-green" style="font-size: 3rem;">⚡</div>
                    <h3 class="h4 mt-lg">Convenience</h3>
                    <p class="text-body text-muted">
                        Understanding the busy lives of students and faculty, we've designed 
                        our service to save time without compromising quality.
                    </p>
                </div>
                
                <div class="card p-xl text-center">
                    <div class="text-green" style="font-size: 3rem;">💚</div>
                    <h3 class="h4 mt-lg">Sustainability</h3>
                    <p class="text-body text-muted">
                        We're committed to environmentally responsible practices, from 
                        eco-friendly packaging to waste reduction initiatives.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="section">
        <div class="page-container">
            <div class="text-center mb-4xl">
                <h2 class="h2 mb-lg">Our Team</h2>
                <p class="text-lead text-muted">Meet the people behind your campus dining experience</p>
            </div>
            
            <div class="grid grid-3">
                <div class="card p-xl text-center">
                    <div class="meal-image" style="height: 120px; border-radius: 50%; margin: 0 auto 20px; width: 120px;">
                        👨‍🍳
                    </div>
                    <h3 class="h5 mt-lg">Chef Marcus</h3>
                    <p class="text-small text-green font-weight-600">Head Chef</p>
                    <p class="text-small text-muted mt-md">
                        With 15 years of culinary experience, Chef Marcus brings creativity 
                        and passion to every dish we serve.
                    </p>
                </div>
                
                <div class="card p-xl text-center">
                    <div class="meal-image" style="height: 120px; border-radius: 50%; margin: 0 auto 20px; width: 120px;">
                        👩‍💼
                    </div>
                    <h3 class="h5 mt-lg">Sarah Johnson</h3>
                    <p class="text-small text-green font-weight-600">Operations Manager</p>
                    <p class="text-small text-muted mt-md">
                        Sarah ensures everything runs smoothly, from inventory management 
                        to customer service excellence.
                    </p>
                </div>
                
                <div class="card p-xl text-center">
                    <div class="meal-image" style="height: 120px; border-radius: 50%; margin: 0 auto 20px; width: 120px;">
                        👨‍🎓
                    </div>
                    <h3 class="h5 mt-lg">David Chen</h3>
                    <p class="text-small text-green font-weight-600">Student Coordinator</p>
                    <p class="text-small text-muted mt-md">
                        As a TAMCC student himself, David bridges the gap between our 
                        service and the student community.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="section bg-dark-green">
        <div class="page-container">
            <div class="grid grid-4 text-center">
                <div>
                    <div class="h1 text-white font-weight-800">500+</div>
                    <div class="text-lead text-white" style="opacity: 0.9;">Daily Meals Served</div>
                </div>
                <div>
                    <div class="h1 text-white font-weight-800">50+</div>
                    <div class="text-lead text-white" style="opacity: 0.9;">Menu Items</div>
                </div>
                <div>
                    <div class="h1 text-white font-weight-800">15</div>
                    <div class="text-lead text-white" style="opacity: 0.9;">Team Members</div>
                </div>
                <div>
                    <div class="h1 text-white font-weight-800">4.8</div>
                    <div class="text-lead text-white" style="opacity: 0.9;">Student Rating</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section">
        <div class="page-container text-center">
            <h2 class="h2 mb-lg">Join Our Community</h2>
            <p class="text-lead text-muted mb-2xl">
                Experience the difference of campus dining done right
            </p>
            <div class="d-flex gap-md justify-center">
                <a href="menu.php" class="btn btn-primary btn-lg">Order Now</a>
                <a href="contact.php" class="btn btn-outline btn-lg">Get In Touch</a>
            </div>
        </div>
    </section>

    <script>
        // Simple header scroll effect
        window.addEventListener('scroll', function() {
            const header = document.querySelector('.site-header');
            if (window.scrollY > 100) {
                header.style.boxShadow = 'var(--shadow-md)';
            } else {
                header.style.boxShadow = 'none';
            }
        });
    </script>
    <?php
// Include footer at the end
require_once 'footer.php';
?>
</body>
</html>