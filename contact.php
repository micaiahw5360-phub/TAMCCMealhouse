<?php
session_start();

// Simple contact form processing
$name = $email = $subject = $message = "";
$success_message = $error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST["name"]));
    $email = htmlspecialchars(trim($_POST["email"]));
    $subject = htmlspecialchars(trim($_POST["subject"]));
    $message = htmlspecialchars(trim($_POST["message"]));
    
    // Basic validation
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_message = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } else {
        // In a real application, you would send an email or save to database here
        // For demo purposes, we'll just show a success message
        $success_message = "Thank you for your message, $name! We'll get back to you within 24 hours.";
        
        // Clear form fields
        $name = $email = $subject = $message = "";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - TAMCC Mealhouse</title>
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
                <h1 class="h1 text-white mb-lg">Get In Touch</h1>
                <p class="text-lead text-white" style="opacity: 0.9;">
                    We're here to help with any questions or feedback
                </p>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="section">
        <div class="page-container">
            <div class="grid grid-2 gap-4xl">
                <!-- Contact Form -->
                <div class="card p-2xl">
                    <h2 class="h2 mb-lg">Send us a Message</h2>
                    
                    <?php if ($success_message): ?>
                        <div class="card p-lg mb-xl" style="background: var(--sb-light-green); border: 1px solid var(--primary);">
                            <div class="text-green font-weight-600">✓ <?php echo $success_message; ?></div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error_message): ?>
                        <div class="card p-lg mb-xl" style="background: #fee; border: 1px solid #fcc;">
                            <div class="text-muted font-weight-600">⚠ <?php echo $error_message; ?></div>
                        </div>
                    <?php endif; ?>
                    
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="name" class="form-control" value="<?php echo $name; ?>" required placeholder="Enter your full name">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Email Address *</label>
                            <input type="email" name="email" class="form-control" value="<?php echo $email; ?>" required placeholder="your.email@example.com">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Subject *</label>
                            <select name="subject" class="form-control" required>
                                <option value="">Select a subject</option>
                                <option value="General Inquiry" <?php echo $subject == 'General Inquiry' ? 'selected' : ''; ?>>General Inquiry</option>
                                <option value="Feedback" <?php echo $subject == 'Feedback' ? 'selected' : ''; ?>>Feedback</option>
                                <option value="Partnership" <?php echo $subject == 'Partnership' ? 'selected' : ''; ?>>Partnership</option>
                                <option value="Technical Issue" <?php echo $subject == 'Technical Issue' ? 'selected' : ''; ?>>Technical Issue</option>
                                <option value="Other" <?php echo $subject == 'Other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Message *</label>
                            <textarea name="message" class="form-control" rows="6" required placeholder="Tell us how we can help you..."><?php echo $message; ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg w-100">Send Message</button>
                    </form>
                </div>
                
                <!-- Contact Information -->
                <div>
                    <h2 class="h2 mb-lg">Contact Information</h2>
                    <p class="text-lead text-muted mb-2xl">
                        Reach out to us through any of these channels. We typically respond within 24 hours.
                    </p>
                    
                    <div class="space-y-xl">
                        <!-- Location -->
                        <div class="card p-xl">
                            <div class="d-flex align-center gap-lg">
                                <div class="text-green" style="font-size: 2.5rem;">📍</div>
                                <div>
                                    <h3 class="h5 mb-sm">Visit Us</h3>
                                    <p class="text-body text-muted">
                                        Campus Deli Building<br>
                                        T.A. Marryshow Community College<br>
                                        St. George's, Grenada
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Phone -->
                        <div class="card p-xl">
                            <div class="d-flex align-center gap-lg">
                                <div class="text-green" style="font-size: 2.5rem;">📞</div>
                                <div>
                                    <h3 class="h5 mb-sm">Call Us</h3>
                                    <p class="text-body text-muted">
                                        Main: (473) 440-1234<br>
                                        Support: (473) 440-1235
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Email -->
                        <div class="card p-xl">
                            <div class="d-flex align-center gap-lg">
                                <div class="text-green" style="font-size: 2.5rem;">✉️</div>
                                <div>
                                    <h3 class="h5 mb-sm">Email Us</h3>
                                    <p class="text-body text-muted">
                                        General: hello@tamcc-mealhouse.edu<br>
                                        Support: help@tamcc-mealhouse.edu<br>
                                        Careers: careers@tamcc-mealhouse.edu
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Hours -->
                        <div class="card p-xl">
                            <div class="d-flex align-center gap-lg">
                                <div class="text-green" style="font-size: 2.5rem;">🕒</div>
                                <div>
                                    <h3 class="h5 mb-sm">Operating Hours</h3>
                                    <p class="text-body text-muted">
                                        <strong>Monday - Friday:</strong> 7:30 AM - 8:00 PM<br>
                                        <strong>Saturday:</strong> 9:00 AM - 6:00 PM<br>
                                        <strong>Sunday:</strong> 10:00 AM - 4:00 PM
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="section bg-cream">
        <div class="page-container">
            <div class="text-center mb-4xl">
                <h2 class="h2 mb-lg">Frequently Asked Questions</h2>
                <p class="text-lead text-muted">Quick answers to common questions</p>
            </div>
            
            <div class="grid grid-2 gap-xl">
                <div class="card p-xl">
                    <h3 class="h5 mb-md">How do I place an order?</h3>
                    <p class="text-body text-muted">
                        Simply browse our menu, add items to your cart, and checkout. 
                        You can pay online or choose to pay when you pick up your order.
                    </p>
                </div>
                
                <div class="card p-xl">
                    <h3 class="h5 mb-md">Do you accept campus meal cards?</h3>
                    <p class="text-body text-muted">
                        Yes! We accept all TAMCC campus meal cards, as well as credit/debit cards 
                        and mobile payment options.
                    </p>
                </div>
                
                <div class="card p-xl">
                    <h3 class="h5 mb-md">How long does order preparation take?</h3>
                    <p class="text-body text-muted">
                        Most orders are ready within 15-20 minutes. You'll receive a notification 
                        when your order is ready for pickup.
                    </p>
                </div>
                
                <div class="card p-xl">
                    <h3 class="h5 mb-md">Can I customize my order?</h3>
                    <p class="text-body text-muted">
                        Absolutely! You can add special instructions for any item, and we'll do 
                        our best to accommodate your preferences.
                    </p>
                </div>
            </div>
            
            <div class="text-center mt-3xl">
                <p class="text-body text-muted">
                    Still have questions? <a href="#contact-form" class="text-green font-weight-600">Send us a message</a> above!
                </p>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="section">
        <div class="page-container">
            <div class="card p-2xl">
                <h2 class="h2 mb-lg text-center">Find Us on Campus</h2>
                <div style="background: var(--sb-light-green); border-radius: var(--radius-lg); padding: 40px; text-align: center;">
                    <div class="text-green" style="font-size: 4rem;">🗺️</div>
                    <h3 class="h4 mt-lg">Campus Deli Location</h3>
                    <p class="text-body text-muted mt-md">
                        We're located in the main Campus Deli building, next to the student center.<br>
                        Look for the TAMCC Mealhouse signage!
                    </p>
                    <div class="mt-xl">
                        <a href="https://maps.google.com" class="btn btn-primary" target="_blank">Open in Google Maps</a>
                    </div>
                </div>
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

        // Simple form enhancement
        document.querySelector('form').addEventListener('submit', function(e) {
            const inputs = this.querySelectorAll('input[required], select[required], textarea[required]');
            let valid = true;
            
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    valid = false;
                    input.style.borderColor = '#fcc';
                } else {
                    input.style.borderColor = '';
                }
            });
            
            if (!valid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    </script>
    <?php
// Include footer at the end
require_once 'footer.php';
?>
</body>
</html>