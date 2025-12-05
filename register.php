<?php
// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$username = $email = $full_name = $phone = $password = $confirm_password = "";
$username_err = $email_err = $full_name_err = $phone_err = $password_err = $confirm_password_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate username
    if(empty(trim($_POST["username"] ?? ""))){
        $username_err = "Please enter a username.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"] ?? ""))){
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else{
        // Prepare a select statement using PDO
        $sql = "SELECT user_id FROM users WHERE username = :username";
        
        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            
            // Set parameters
            $param_username = trim($_POST["username"] ?? "");
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"] ?? "");
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            unset($stmt);
        }
    }

    // Validate email
    if(empty(trim($_POST["email"] ?? ""))){
        $email_err = "Please enter an email.";
    } elseif(!filter_var(trim($_POST["email"] ?? ""), FILTER_VALIDATE_EMAIL)){
        $email_err = "Please enter a valid email address.";
    } else{
        // Check if email exists
        $sql = "SELECT user_id FROM users WHERE email = :email";
        
        if($stmt = $pdo->prepare($sql)){
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            $param_email = trim($_POST["email"] ?? "");
            
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    $email_err = "This email is already registered.";
                } else{
                    $email = trim($_POST["email"] ?? "");
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
            unset($stmt);
        }
    }

    // Validate full name
    if(empty(trim($_POST["full_name"] ?? ""))){
        $full_name_err = "Please enter your full name.";
    } else{
        $full_name = trim($_POST["full_name"] ?? "");
    }

    // Validate phone (optional)
    if(!empty(trim($_POST["phone"] ?? ""))){
        $phone = trim($_POST["phone"] ?? "");
    }

    // Validate password
    if(empty(trim($_POST["password"] ?? ""))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"] ?? "")) < 6){
        $password_err = "Password must have at least 6 characters.";
    } else{
        $password = trim($_POST["password"] ?? "");
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"] ?? ""))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"] ?? "");
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
    
    // Check input errors before inserting in database
    if(empty($username_err) && empty($email_err) && empty($full_name_err) && empty($password_err) && empty($confirm_password_err)){
        
        // Prepare an insert statement using PDO
        $sql = "INSERT INTO users (username, email, password_hash, full_name, phone) VALUES (:username, :email, :password, :full_name, :phone)";
         
        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
            $stmt->bindParam(":full_name", $param_full_name, PDO::PARAM_STR);
            $stmt->bindParam(":phone", $param_phone, PDO::PARAM_STR);
            
            // Set parameters
            $param_username = $username;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $param_full_name = $full_name;
            $param_phone = $phone;
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Redirect to login page
                header("location: login.php");
                exit();
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            unset($stmt);
        }
    }
    
    // DON'T unset $pdo here - header.php needs it!
    // unset($pdo); // REMOVED THIS LINE
}

// Set page title before including header
$page_title = "Create Account - TAMCC Mealhouse";
require_once 'header.php';
?>

<!-- Auth Container - REDUCED TOP PADDING -->
<div class="auth-container" style="min-height: calc(100vh - 80px); padding: var(--space-2xl) 0 var(--space-4xl) 0;">
    <div class="page-container">
        <div class="grid grid-2 align-center gap-4xl">
            <!-- Left Side - Branding -->
            <div class="text-center">
                <div style="font-size: 6rem; color: var(--primary);">🍽️</div>
                <h1 class="h1 mt-lg" style="color: var(--sb-dark-green);">Join TAMCC Mealhouse</h1>
                <p class="text-lead text-muted mt-lg">
                    Create your account to start ordering delicious meals from our campus deli. 
                    Skip the lines and enjoy fresh food when you want it.
                </p>
                
                <div class="card p-xl mt-2xl"> <!-- Reduced mt-3xl to mt-2xl -->
                    <div class="grid grid-3 text-center">
                        <div>
                            <div class="text-green" style="font-size: 2rem;">⚡</div>
                            <div class="text-small text-muted mt-sm">Fast Ordering</div>
                        </div>
                        <div>
                            <div class="text-green" style="font-size: 2rem;">💳</div>
                            <div class="text-small text-muted mt-sm">Easy Payment</div>
                        </div>
                        <div>
                            <div class="text-green" style="font-size: 2rem;">🎯</div>
                            <div class="text-small text-muted mt-sm">Campus Discounts</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Side - Registration Form -->
            <div class="card p-2xl" id="registration-form">
                <div class="text-center mb-lg"> <!-- Reduced mb-xl to mb-lg -->
                    <h2 class="h2 mb-md">Create Your Account</h2>
                    <p class="text-muted">Join thousands of TAMCC students and staff</p>
                </div>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <!-- Full Name Field -->
                    <div class="form-group">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="full_name" 
                               class="form-control <?php echo (!empty($full_name_err)) ? 'is-invalid' : ''; ?>" 
                               value="<?php echo htmlspecialchars($full_name); ?>" 
                               placeholder="Enter your full name">
                        <span class="invalid-feedback"><?php echo $full_name_err; ?></span>
                    </div>

                    <!-- Email Field -->
                    <div class="form-group">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" 
                               class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" 
                               value="<?php echo htmlspecialchars($email); ?>" 
                               placeholder="Enter your email">
                        <span class="invalid-feedback"><?php echo $email_err; ?></span>
                    </div>

                    <!-- Username Field -->
                    <div class="form-group">
                        <label class="form-label">Username *</label>
                        <input type="text" name="username" 
                               class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" 
                               value="<?php echo htmlspecialchars($username); ?>" 
                               placeholder="Choose a username">
                        <span class="invalid-feedback"><?php echo $username_err; ?></span>
                        <div class="text-small text-muted mt-sm">
                            Letters, numbers, and underscores only
                        </div>
                    </div>

                    <!-- Phone Field -->
                    <div class="form-group">
                        <label class="form-label">Phone (Optional)</label>
                        <input type="tel" name="phone" 
                               class="form-control <?php echo (!empty($phone_err)) ? 'is-invalid' : ''; ?>" 
                               value="<?php echo htmlspecialchars($phone); ?>" 
                               placeholder="Enter your phone number">
                        <span class="invalid-feedback"><?php echo $phone_err; ?></span>
                    </div>
                    
                    <!-- Password Field -->
                    <div class="form-group">
                        <label class="form-label">Password *</label>
                        <input type="password" name="password" 
                               class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" 
                               value="<?php echo htmlspecialchars($password); ?>" 
                               placeholder="Create a password">
                        <span class="invalid-feedback"><?php echo $password_err; ?></span>
                        <div class="text-small text-muted mt-sm">
                            Minimum 6 characters
                        </div>
                    </div>
                    
                    <!-- Confirm Password Field -->
                    <div class="form-group">
                        <label class="form-label">Confirm Password *</label>
                        <input type="password" name="confirm_password" 
                               class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" 
                               value="<?php echo htmlspecialchars($confirm_password); ?>" 
                               placeholder="Confirm your password">
                        <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-lg w-100">Create Account</button>
                    </div>
                    
                    <!-- Terms -->
                    <div class="text-center">
                        <p class="text-small text-muted">
                            By creating an account, you agree to our 
                            <a href="terms.php" class="text-green">Terms of Service</a> and 
                            <a href="privacy.php" class="text-green">Privacy Policy</a>
                        </p>
                    </div>
                </form>
                
                <!-- Login Redirect -->
                <div class="text-center mt-lg pt-lg" style="border-top: 1px solid var(--sb-border);"> <!-- Reduced mt-xl to mt-lg -->
                    <p class="text-body text-muted">
                        Already have an account? 
                        <a href="login.php" class="text-green font-weight-600">Sign in here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Benefits Section -->
<section class="section bg-cream">
    <div class="page-container">
        <div class="text-center mb-4xl">
            <h2 class="h2 mb-lg">Why Create an Account?</h2>
            <p class="text-lead text-muted">Get the full TAMCC Mealhouse experience</p>
        </div>
        
        <div class="grid grid-3">
            <div class="card text-center p-xl">
                <div class="text-green" style="font-size: 3rem;">🚀</div>
                <h3 class="h4 mt-lg">Fast Ordering</h3>
                <p class="text-body text-muted">
                    Save your preferences and order your favorite meals in seconds
                </p>
            </div>
            
            <div class="card text-center p-xl">
                <div class="text-green" style="font-size: 3rem;">💝</div>
                <h3 class="h4 mt-lg">Personalized Experience</h3>
                <p class="text-body text-muted">
                    Get recommendations based on your order history and preferences
                </p>
            </div>
            
            <div class="card text-center p-xl">
                <div class="text-green" style="font-size: 3rem;">🏆</div>
                <h3 class="h4 mt-lg">Rewards Program</h3>
                <p class="text-body text-muted">
                    Earn points with every order and redeem for free meals and discounts
                </p>
            </div>
        </div>
        
        <div class="grid grid-3 mt-xl">
            <div class="card text-center p-xl">
                <div class="text-green" style="font-size: 3rem;">📱</div>
                <h3 class="h4 mt-lg">Order Tracking</h3>
                <p class="text-body text-muted">
                    Real-time updates on your order status and preparation time
                </p>
            </div>
            
            <div class="card text-center p-xl">
                <div class="text-green" style="font-size: 3rem;">💳</div>
                <h3 class="h4 mt-lg">Saved Payment</h3>
                <p class="text-body text-muted">
                    Securely save your payment methods for faster checkout
                </p>
            </div>
            
            <div class="card text-center p-xl">
                <div class="text-green" style="font-size: 3rem;">👥</div>
                <h3 class="h4 mt-lg">Group Orders</h3>
                <p class="text-body text-muted">
                    Easily coordinate group orders with classmates and friends
                </p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section">
    <div class="page-container text-center">
        <div class="card p-2xl" style="background: linear-gradient(135deg, var(--sb-dark-green) 0%, var(--primary) 100%); color: white;">
            <h2 class="h2 mb-lg text-white">Ready to Get Started?</h2>
            <p class="text-lead mb-2xl" style="opacity: 0.9;">
                Join the TAMCC Mealhouse community today and experience campus dining like never before
            </p>
            <a href="#registration-form" class="btn btn-secondary btn-lg">Create Your Account Now</a>
        </div>
    </div>
</section>

<?php
// Include footer at the end
require_once 'footer.php';
?>

<style>
.auth-container {
    background: linear-gradient(135deg, var(--sb-cream) 0%, var(--sb-light-green) 100%);
}

.form-control {
    border: 2px solid var(--sb-border);
    padding: var(--space-lg) var(--space-xl);
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0, 112, 74, 0.1);
}

.form-control.is-invalid {
    border-color: #dc3545;
}

.invalid-feedback {
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: var(--space-sm);
    display: block;
}

.btn-primary {
    background: var(--primary);
    border: none;
    padding: var(--space-lg) var(--space-2xl);
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: var(--sb-dark-green);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 112, 74, 0.3);
}
</style>

<script>
// Simple form enhancement
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const inputs = form.querySelectorAll('input[required]');
    
    // Add real-time validation
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
        
        input.addEventListener('input', function() {
            clearFieldError(this);
        });
    });
    
    function validateField(field) {
        const value = field.value.trim();
        const fieldName = field.name;
        
        if (!value) {
            showFieldError(field, 'This field is required');
            return false;
        }
        
        if (fieldName === 'username') {
            if (!/^[a-zA-Z0-9_]+$/.test(value)) {
                showFieldError(field, 'Username can only contain letters, numbers, and underscores');
                return false;
            }
        }
        
        if (fieldName === 'email') {
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                showFieldError(field, 'Please enter a valid email address');
                return false;
            }
        }
        
        if (fieldName === 'password' && value.length < 6) {
            showFieldError(field, 'Password must be at least 6 characters');
            return false;
        }
        
        if (fieldName === 'confirm_password') {
            const password = form.querySelector('input[name="password"]').value;
            if (value !== password) {
                showFieldError(field, 'Passwords do not match');
                return false;
            }
        }
        
        clearFieldError(field);
        return true;
    }
    
    function showFieldError(field, message) {
        field.classList.add('is-invalid');
        let errorElement = field.parentElement.querySelector('.invalid-feedback');
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'invalid-feedback';
            field.parentElement.appendChild(errorElement);
        }
        errorElement.textContent = message;
    }
    
    function clearFieldError(field) {
        field.classList.remove('is-invalid');
        const errorElement = field.parentElement.querySelector('.invalid-feedback');
        if (errorElement) {
            errorElement.textContent = '';
        }
    }
    
    // Form submission enhancement
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        inputs.forEach(input => {
            if (!validateField(input)) {
                isValid = false;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            // Scroll to first error
            const firstError = form.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    });
});
</script>