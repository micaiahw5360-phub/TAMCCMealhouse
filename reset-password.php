<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

require_once "config.php";

$new_password = $confirm_password = "";
$new_password_err = $confirm_password_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(empty(trim($_POST["new_password"]))){
        $new_password_err = "Please enter the new password.";     
    } elseif(strlen(trim($_POST["new_password"])) < 6){
        $new_password_err = "Password must have at least 6 characters.";
    } else{
        $new_password = trim($_POST["new_password"]);
    }
    
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm the password.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($new_password_err) && ($new_password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
        
    if(empty($new_password_err) && empty($confirm_password_err)){
        // Use the correct column names: password_hash and user_id
        $sql = "UPDATE users SET password_hash = :password_hash WHERE user_id = :user_id";
        
        if($stmt = $pdo->prepare($sql)){
            // Get user_id from session (check both possible session variable names)
            $user_id = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : (isset($_SESSION["id"]) ? $_SESSION["id"] : null);
            
            if($user_id === null){
                $confirm_password_err = "User session not found. Please log in again.";
            } else {
                $stmt->bindParam(":password_hash", $param_password_hash, PDO::PARAM_STR);
                $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
                
                $param_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                
                if($stmt->execute()){
                    session_destroy();
                    header("location: login.php");
                    exit();
                } else{
                    echo "Oops! Something went wrong. Please try again later.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - TAMCC Mealhouse</title>
    <link rel="stylesheet" href="tamcc-mealhouse-style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    
<?php
require_once 'header.php';
?>
    <!-- Main Content -->
    <main class="page-container">
        <section class="section">
            <div class="card" style="max-width: 500px; margin: 0 auto;">
                <div class="card-header text-center">
                    <h2 class="h3 mb-0">Reset Password</h2>
                    <p class="text-small text-muted mt-sm">Create a new secure password</p>
                </div>
                
                <div class="card-body">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"> 
                        <div class="form-group">
                            <label class="form-label">New Password</label>
                            <input type="password" name="new_password" class="form-control <?php echo (!empty($new_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($new_password); ?>" placeholder="Enter new password">
                            <span class="invalid-feedback"><?php echo $new_password_err; ?></span>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" placeholder="Confirm new password">
                            <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
                        </div>
                        
                        <div class="form-group d-flex gap-md">
                            <button type="submit" class="btn btn-primary w-100">Update Password</button>
                            <a href="welcome.php" class="btn btn-outline">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <?php
require_once 'footer.php';
?>
</body>
</html>