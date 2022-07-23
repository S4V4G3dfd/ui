<?php
session_start();
 
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: welcome.php");
    exit;
}
 
require_once "config.php";
 
$username = $password = "";
$username_err = $password_err = "";
 
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    if(empty($username_err) && empty($password_err)){
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            $param_username = $username;
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            session_start();
                            
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                            
                            header("location: welcome.php");
                        } else{
                            $password_err = "The password you entered was not valid.";
                        }
                    }
                } else{
                    $username_err = "No account found with that username.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
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
     <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
<input type="text" class="inputbox" placeholder="Username" name="username" value="<?php echo $username; ?>">
<span class="help-block"><?php echo $username_err; ?></span>
            </div> 
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
<input type="password" class="inputbox" placeholder="Password" name="password">
 <span class="help-block"><?php echo $password_err; ?></span>
            </div>
<input type="submit" class="submitbtn" value="LogIn">


  <p>Don't have an account? <a href="index.php">Sign up now</a>.</p>
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
