<?php
session_start();
$page_title = "Terms of Service - TAMCC Mealhouse";
require_once 'header.php';
?>

<style>
.terms-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 40px 20px;
    color: #fff;
}

.terms-header {
    text-align: center;
    margin-bottom: 40px;
    border-bottom: 2px solid #00bfff;
    padding-bottom: 20px;
}

.terms-title {
    font-size: 36px;
    font-weight: 600;
    color: #00bfff;
    margin-bottom: 10px;
}

.last-updated {
    color: rgba(255, 255, 255, 0.7);
    font-style: italic;
}

.terms-content {
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
    margin-bottom: 8px;
}

.important-note {
    background-color: #252525;
    padding: 20px;
    border-radius: 10px;
    margin: 30px 0;
    border-left: 4px solid #ff6b6b;
}

.important-note .section-title {
    color: #ff6b6b;
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

<div class="terms-container">
    <div class="terms-header">
        <h1 class="terms-title">Terms of Service</h1>
        <p class="last-updated">Last Updated: <?php echo date('F j, Y'); ?></p>
    </div>

    <div class="terms-content">
        <div class="important-note">
            <h2 class="section-title">Important Notice</h2>
            <div class="section-content">
                <p>Please read these Terms of Service carefully before using TAMCC Mealhouse. By accessing or using our service, you agree to be bound by these terms.</p>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">1. Acceptance of Terms</h2>
            <div class="section-content">
                <p>By accessing and using TAMCC Mealhouse ("the Service"), you accept and agree to be bound by the terms and provision of this agreement.</p>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">2. User Accounts</h2>
            <div class="section-content">
                <p>When you create an account with us, you must provide accurate and complete information. You are responsible for:</p>
                <ul>
                    <li>Maintaining the confidentiality of your account</li>
                    <li>All activities that occur under your account</li>
                    <li>Notifying us immediately of any unauthorized use</li>
                </ul>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">3. Ordering and Payment</h2>
            <div class="section-content">
                <p><strong>3.1 Order Placement:</strong> All orders are subject to availability and confirmation.</p>
                <p><strong>3.2 Pricing:</strong> Prices are subject to change without notice. Menu prices include applicable taxes.</p>
                <p><strong>3.3 Payment:</strong> We accept various payment methods as displayed during checkout.</p>
                <p><strong>3.4 Order Modifications:</strong> Order modifications are only possible before food preparation begins.</p>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">4. Cancellation and Refunds</h2>
            <div class="section-content">
                <p><strong>4.1 Cancellation Policy:</strong></p>
                <ul>
                    <li>Orders can be cancelled within 5 minutes of placement</li>
                    <li>Once food preparation begins, cancellations may not be possible</li>
                    <li>Contact us immediately for cancellation requests</li>
                </ul>
                <p><strong>4.2 Refund Policy:</strong></p>
                <ul>
                    <li>Refunds are processed for cancelled orders</li>
                    <li>Refunds may take 5-7 business days to appear on your statement</li>
                    <li>No refunds for completed orders unless there's an error on our part</li>
                </ul>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">5. Food Safety and Allergies</h2>
            <div class="section-content">
                <p><strong>5.1 Allergy Information:</strong> While we take precautions, we cannot guarantee allergen-free preparation environments.</p>
                <p><strong>5.2 Food Safety:</strong> We follow proper food handling and safety procedures.</p>
                <p><strong>5.3 Special Requests:</strong> Please inform us of any dietary restrictions or allergies when ordering.</p>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">6. Delivery and Pickup</h2>
            <div class="section-content">
                <p><strong>6.1 Delivery Times:</strong> Estimated delivery times are provided but not guaranteed.</p>
                <p><strong>6.2 Pickup Orders:</strong> Orders must be picked up within 30 minutes of notification.</p>
                <p><strong>6.3 Campus Delivery:</strong> Delivery is available to designated campus locations only.</p>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">7. User Conduct</h2>
            <div class="section-content">
                <p>You agree not to:</p>
                <ul>
                    <li>Use the Service for any illegal purpose</li>
                    <li>Harass, abuse, or harm our staff</li>
                    <li>Attempt to gain unauthorized access to the Service</li>
                    <li>Interfere with the proper working of the Service</li>
                    <li>Submit false or misleading information</li>
                </ul>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">8. Intellectual Property</h2>
            <div class="section-content">
                <p>All content included on the Service, such as text, graphics, logos, images, and software, is the property of TAMCC Mealhouse or its content suppliers and protected by copyright laws.</p>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">9. Limitation of Liability</h2>
            <div class="section-content">
                <p>TAMCC Mealhouse shall not be liable for any indirect, incidental, special, consequential, or punitive damages resulting from your use of or inability to use the Service.</p>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">10. Changes to Terms</h2>
            <div class="section-content">
                <p>We reserve the right to modify these terms at any time. We will notify users of significant changes. Continued use of the Service constitutes acceptance of modified terms.</p>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">11. Contact Information</h2>
            <div class="section-content">
                <p>For questions about these Terms of Service, please contact us:</p>
                <p><strong>Email:</strong> legal@tamccmealhouse.com</p>
                <p><strong>Phone:</strong> (473) 444-1234</p>
                <p><strong>Address:</strong> TAMCC Campus, St. George's, Grenada</p>
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