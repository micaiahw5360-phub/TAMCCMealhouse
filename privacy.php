<?php
session_start();
$page_title = "Privacy Policy - TAMCC Mealhouse";
require_once 'header.php';
?>

<style>
.policy-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 40px 20px;
    color: #fff;
}

.policy-header {
    text-align: center;
    margin-bottom: 40px;
    border-bottom: 2px solid #00bfff;
    padding-bottom: 20px;
}

.policy-title {
    font-size: 36px;
    font-weight: 600;
    color: #00bfff;
    margin-bottom: 10px;
}

.last-updated {
    color: rgba(255, 255, 255, 0.7);
    font-style: italic;
}

.policy-content {
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

.contact-info {
    background-color: #252525;
    padding: 20px;
    border-radius: 10px;
    margin-top: 30px;
    border-left: 4px solid #00bfff;
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

<div class="policy-container">
    <div class="policy-header">
        <h1 class="policy-title">Privacy Policy</h1>
        <p class="last-updated">Last Updated: <?php echo date('F j, Y'); ?></p>
    </div>

    <div class="policy-content">
        <div class="section">
            <h2 class="section-title">1. Information We Collect</h2>
            <div class="section-content">
                <p>We collect information to provide better services to our users. The types of information we collect include:</p>
                <ul>
                    <li><strong>Personal Information:</strong> Name, email address, phone number when you place an order</li>
                    <li><strong>Order Information:</strong> Food preferences, special instructions, payment details</li>
                    <li><strong>Technical Information:</strong> IP address, browser type, device information</li>
                    <li><strong>Location Data:</strong> Delivery address or pickup location preferences</li>
                </ul>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">2. How We Use Your Information</h2>
            <div class="section-content">
                <p>We use the information we collect for the following purposes:</p>
                <ul>
                    <li>Process and fulfill your food orders</li>
                    <li>Communicate order status and updates</li>
                    <li>Provide customer support</li>
                    <li>Improve our services and user experience</li>
                    <li>Send promotional offers (only with your consent)</li>
                    <li>Ensure the security of our platform</li>
                </ul>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">3. Data Sharing and Disclosure</h2>
            <div class="section-content">
                <p>We do not sell your personal information. We may share your information with:</p>
                <ul>
                    <li><strong>Service Providers:</strong> Payment processors, delivery services</li>
                    <li><strong>Legal Requirements:</strong> When required by law or to protect our rights</li>
                    <li><strong>Business Transfers:</strong> In case of merger or acquisition</li>
                </ul>
                <p>All third-party service providers are required to maintain the confidentiality of your information.</p>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">4. Data Security</h2>
            <div class="section-content">
                <p>We implement appropriate security measures to protect your personal information:</p>
                <ul>
                    <li>SSL encryption for data transmission</li>
                    <li>Secure storage of sensitive information</li>
                    <li>Regular security assessments</li>
                    <li>Limited access to personal data</li>
                </ul>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">5. Your Rights</h2>
            <div class="section-content">
                <p>You have the right to:</p>
                <ul>
                    <li>Access your personal information</li>
                    <li>Correct inaccurate data</li>
                    <li>Request deletion of your data</li>
                    <li>Opt-out of marketing communications</li>
                    <li>Data portability</li>
                </ul>
                <p>To exercise these rights, please contact us using the information below.</p>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">6. Cookies and Tracking</h2>
            <div class="section-content">
                <p>We use cookies and similar technologies to:</p>
                <ul>
                    <li>Remember your preferences</li>
                    <li>Maintain your shopping cart</li>
                    <li>Analyze website traffic</li>
                    <li>Improve our services</li>
                </ul>
                <p>You can control cookie settings through your browser preferences.</p>
            </div>
        </div>

        <div class="contact-info">
            <h3 class="section-title">Contact Us</h3>
            <p>If you have any questions about this Privacy Policy, please contact us:</p>
            <p><strong>Email:</strong> privacy@tamccmealhouse.com</p>
            <p><strong>Phone:</strong> (473) 444-1234</p>
            <p><strong>Address:</strong> TAMCC Campus, St. George's, Grenada</p>
        </div>

        <center>
            <a href="index.php" class="back-btn">Back to Home</a>
        </center>
    </div>
</div>

<?php
require_once 'footer.php';
?>