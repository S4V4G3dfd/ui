<?php

require_once "config.php";
 

$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";
 

if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } else{
        
        $sql = "SELECT id FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            
            $param_username = trim($_POST["username"]);
            
            
            if(mysqli_stmt_execute($stmt)){
                
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            
            mysqli_stmt_close($stmt);
        }
    }
 
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
   
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
    
   
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
        
        
        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            
            mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_password);
     
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); 
        
            if(mysqli_stmt_execute($stmt)){
              
                header("location: login.php");
            } else{
                echo "Something went wrong. Please try again later.";
            }

       
            mysqli_stmt_close($stmt);
        }
    }

    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Shakuni-Login Page</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="login/1.png">
  </head>
  <body>
<div class="loginbox">
<div class="leftbox">
<h1>Sign Up</h1>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
<input type="text" name="username" class="inputbox" placeholder="Username" value="<?php echo $username; ?>">
<span class="help-block"><?php echo $username_err; ?></span>
<input type="password" class="inputbox" placeholder="Password" name="password" value="<?php echo $password; ?>">
<span class="help-block"><?php echo $password_err; ?></span>
<div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
<input type="password" class="inputbox" placeholder="Confirm Password" name="confirm_password" value="<?php echo $confirm_password; ?>">
                <span class="help-block"><?php echo $confirm_password_err; ?></span>
<input type="submit" class="submitbtn" value="Sign Up">
</div>
<p>Already have an account? <a href="login.php">Login here</a>.</p>
</form>
</div>

<div class="rightbox">
  <span class="right-title">Sign Up With<br>Social Network</span>
  <button class="social facebook">Login With Facebook</button>
    <button class="social twitter">Login With Twitter</button>
      <button class="social google">Login With Google+</button>


</div>

<div class="or">
  OR
</div>
</div>
  </body>
</html>
