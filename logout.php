<?php
session_start();
 
// Unset all of the session variables
$_SESSION = array();
 
// Destroy the session.
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logged Out - TAMCC Mealhouse</title>
    <link rel="stylesheet" href="tamcc-mealhouse-style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div style="font-size: 3rem; margin-bottom: var(--space-lg);">👋</div>
                <h2 class="h3 mb-0">Successfully Logged Out</h2>
                <p class="text-small text-muted mt-sm">You have been safely signed out</p>
            </div>
            
            <div class="auth-body text-center">
                <p class="text-body text-muted mb-xl">
                    Thank you for using TAMCC Mealhouse. You have been successfully logged out of your account.
                </p>
                
                <div class="alert alert-success">
                    <strong>Session ended:</strong> Your personal information is secure.
                </div>
                
                <div class="mt-xl">
                    <a href="login.php" class="btn btn-primary btn-lg">Sign In Again</a>
                </div>
                
                <div class="mt-lg">
                    <a href="homepage.php" class="btn btn-outline">Return to Homepage</a>
                </div>
            </div>
            
            <div class="auth-footer">
                <p class="text-small text-muted mb-0">
                    Need help? <a href="contact.php" class="text-primary">Contact support</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        // Optional: Auto-redirect after 10 seconds
        setTimeout(function() {
            window.location.href = 'homepage.php';
        }, 10000);
    </script>
</body>
</html>