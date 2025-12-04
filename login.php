<?php
session_start();

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: welcome.php");
    exit;
}

require_once "config.php";

$username = $password = "";
$username_err = $password_err = $login_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(empty(trim($_POST["username"] ?? ""))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"] ?? "");
    }
    
    if(empty(trim($_POST["password"] ?? ""))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"] ?? "");
    }
    
    if(empty($username_err) && empty($password_err)){
        $sql = "SELECT user_id, username, password_hash FROM users WHERE username = :username";
        
        if($stmt = $pdo->prepare($sql)){
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $username;
            
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    if($row = $stmt->fetch()){
                        $id = $row["user_id"];
                        $username = $row["username"];
                        $hashed_password = $row["password_hash"];
                        if(password_verify($password, $hashed_password)){
                            session_start();
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            header("location: welcome.php");
                            exit();
                        } else{
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else{
                    $login_err = "Invalid username or password.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
            unset($stmt);
        }
    }
    // Don't unset $pdo here - it might be needed by included files
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TAMCC Mealhouse</title>
    <link rel="stylesheet" href="tamcc-mealhouse-style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h2 class="h3 mb-0">Welcome Back</h2>
                <p class="text-small text-muted mt-sm">Sign in to your TAMCC Mealhouse account</p>
            </div>
            
            <div class="auth-body">
                <?php 
                if(!empty($login_err)){
                    echo '<div class="alert alert-danger">' . $login_err . '</div>';
                }        
                ?>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($username); ?>" placeholder="Enter your username">
                        <span class="invalid-feedback"><?php echo $username_err; ?></span>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" placeholder="Enter your password">
                        <span class="invalid-feedback"><?php echo $password_err; ?></span>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary w-100 btn-lg">Sign In</button>
                    </div>
                </form>
            </div>
            
            <div class="auth-footer">
                <p class="text-small text-muted mb-0">
                    Don't have an account? 
                    <a href="register.php" class="text-primary">Sign up now</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>