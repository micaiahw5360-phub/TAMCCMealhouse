<?php
session_start();
$page_title = "Accessibility - TAMCC Mealhouse";
require_once 'header.php';
?>

<style>
.accessibility-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 40px 20px;
    color: #fff;
}

.accessibility-header {
    text-align: center;
    margin-bottom: 40px;
    border-bottom: 2px solid #00bfff;
    padding-bottom: 20px;
}

.accessibility-title {
    font-size: 36px;
    font-weight: 600;
    color: #00bfff;
    margin-bottom: 10px;
}

.commitment-statement {
    background-color: #252525;
    padding: 25px;
    border-radius: 15px;
    margin-bottom: 30px;
    border-left: 4px solid #00bfff;
    text-align: center;
    font-size: 18px;
    line-height: 1.6;
}

.accessibility-content {
    background-color: #1a1a1a;
    border-radius: 15px;
    padding: 30px;
    border: 1px solid #333;
}

.section {
    margin-bottom: 30px;
}

.section-title {
    color: #00bfff;
    font-size: 20px;
    margin-bottom: 15px;
    font-weight: 600;
}

.section-content {
    line-height: 1.6;
    color: rgba(255, 255, 255, 0.9);
}

.section-content ul {
    margin-left: 20px;
    margin-bottom: 15px;
}

.section-content li {
    margin-bottom: 10px;
}

.feature-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.feature-card {
    background-color: #252525;
    padding: 20px;
    border-radius: 10px;
    border: 1px solid #333;
}

.feature-card h4 {
    color: #00bfff;
    margin-bottom: 10px;
    font-size: 18px;
}

.contact-box {
    background-color: #252525;
    padding: 25px;
    border-radius: 10px;
    margin-top: 30px;
    border-left: 4px solid #00bfff;
}

.feedback-form {
    margin-top: 20px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    color: #00bfff;
    font-weight: 600;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #333;
    background-color: #1a1a1a;
    color: #fff;
}

.form-group textarea {
    height: 100px;
    resize: vertical;
}

.submit-btn {
    background-color: #00bfff;
    color: #1a1a1a;
    border: none;
    padding: 12px 25px;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.submit-btn:hover {
    background-color: #00bfff96;
}

.back-btn {
    display: inline-block;
    background-color: #00bfff;
    color: #1a1a1a;
    padding: 12px 25px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    margin-top: 30px;
    transition: background-color 0.3s ease;
}

.back-btn:hover {
    background-color: #00bfff96;
    color: #1a1a1a;
}
</style>

<div class="accessibility-container">
    <div class="accessibility-header">
        <h1 class="accessibility-title">Accessibility Statement</h1>
        <p class="last-updated">Last Updated: <?php echo date('F j, Y'); ?></p>
    </div>

    <div class="commitment-statement">
        <p>TAMCC Mealhouse is committed to ensuring digital accessibility for people with disabilities. We are continuously improving the user experience for everyone and applying the relevant accessibility standards.</p>
    </div>

    <div class="accessibility-content">
        <div class="section">
            <h2 class="section-title">Our Commitment</h2>
            <div class="section-content">
                <p>We believe in equal access to digital services and are dedicated to making our website and ordering platform accessible to all users, regardless of ability or technology.</p>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">Accessibility Features</h2>
            <div class="section-content">
                <p>Our website includes the following accessibility features:</p>
                <div class="feature-grid">
                    <div class="feature-card">
                        <h4>Keyboard Navigation</h4>
                        <p>Full website functionality accessible using keyboard-only navigation</p>
                    </div>
                    <div class="feature-card">
                        <h4>Screen Reader Compatibility</h4>
                        <p>Compatible with popular screen readers and assistive technologies</p>
                    </div>
                    <div class="feature-card">
                        <h4>Text Resizing</h4>
                        <p>Text can be resized using browser zoom functionality</p>
                    </div>
                    <div class="feature-card">
                        <h4>Color Contrast</h4>
                        <p>Sufficient color contrast ratios for text and background elements</p>
                    </div>
                    <div class="feature-card">
                        <h4>Alt Text</h4>
                        <p>Descriptive alt text for images and visual elements</p>
                    </div>
                    <div class="feature-card">
                        <h4>Form Labels</h4>
                        <p>Properly labeled form fields and clear error messages</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">Standards Compliance</h2>
            <div class="section-content">
                <p>We aim to conform to the following accessibility standards:</p>
                <ul>
                    <li><strong>WCAG 2.1:</strong> Web Content Accessibility Guidelines level AA</li>
                    <li><strong>ADA:</strong> Americans with Disabilities Act requirements</li>
                    <li><strong>Section 508:</strong> US federal accessibility standards</li>
                </ul>
                <p>Our ongoing efforts include regular accessibility audits and user testing with people who have disabilities.</p>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">Physical Accessibility</h2>
            <div class="section-content">
                <p>Our physical location at TAMCC Campus is designed to be accessible:</p>
                <ul>
                    <li>Wheelchair-accessible entrances and pathways</li>
                    <li>Accessible seating areas</li>
                    <li>Service animal friendly</li>
                    <li>Assistance available for customers with disabilities</li>
                    <li>Accessible parking spaces near the entrance</li>
                </ul>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">Alternative Ordering Methods</h2>
            <div class="section-content">
                <p>If you encounter accessibility barriers on our website, we offer alternative ordering methods:</p>
                <ul>
                    <li><strong>Phone Orders:</strong> Call (473) 444-1234 to place your order</li>
                    <li><strong>Email Orders:</strong> Send orders to orders@tamccmealhouse.com</li>
                    <li><strong>In-Person Assistance:</strong> Visit our location for staff assistance</li>
                    <li><strong>Campus Delivery:</strong> We deliver to accessible locations on campus</li>
                </ul>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">Continuous Improvement</h2>
            <div class="section-content">
                <p>We are continuously working to improve accessibility:</p>
                <ul>
                    <li>Regular accessibility testing and audits</li>
                    <li>Staff training on accessibility best practices</li>
                    <li>User feedback incorporation</li>
                    <li>Technology updates to improve accessibility</li>
                </ul>
            </div>
        </div>

        <div class="contact-box">
            <h2 class="section-title">Feedback and Assistance</h2>
            <div class="section-content">
                <p>We welcome your feedback on the accessibility of TAMCC Mealhouse. Please let us know if you encounter accessibility barriers:</p>
                
                <div class="feedback-form">
                    <form method="POST" action="process_feedback.php">
                        <div class="form-group">
                            <label for="name">Your Name</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="feedback">Accessibility Feedback</label>
                            <textarea id="feedback" name="feedback" placeholder="Please describe any accessibility issues you encountered or suggestions for improvement..." required></textarea>
                        </div>
                        <button type="submit" class="submit-btn">Submit Feedback</button>
                    </form>
                </div>

                <p><strong>Other Contact Methods:</strong></p>
                <p><strong>Email:</strong> accessibility@tamccmealhouse.com</p>
                <p><strong>Phone:</strong> (473) 444-1234</p>
                <p><strong>Address:</strong> TAMCC Campus, St. George's, Grenada</p>
                <p>We typically respond to accessibility feedback within 2 business days.</p>
            </div>
        </div>

        <center>
            <a href="index.php" class="back-btn">Back to Home</a>
        </center>
    </div>
</div>

<?php
require_once 'footer.php';
?>