<?php
// Must be at the very top
session_start();

// Initialize session variables
$_SESSION["user"] = "";
$_SESSION["usertype"] = "";

// Set timezone
date_default_timezone_set('Asia/Kolkata');
$_SESSION["date"] = date('Y-m-d');

// Include database connection
include("connection.php"); // Make sure connection.php creates $database as mysqli object

// Initialize error message
$error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = $_POST['useremail'];
    $password = $_POST['userpassword'];

    // Check if email exists
    $result = $database->query("SELECT * FROM webuser WHERE email='$email'");
    
    if ($result && $result->num_rows == 1) {
        $utype = $result->fetch_assoc()['usertype'];

        if ($utype == 'p') {
            $checker = $database->query("SELECT * FROM patient WHERE pemail='$email' AND ppassword='$password'");
            if ($checker && $checker->num_rows == 1) {
                $_SESSION['user'] = $email;
                $_SESSION['usertype'] = 'p';
                header('Location: patient/index.php');
                exit();
            } else {
                $error = "Wrong credentials: Invalid email or password";
            }

        } elseif ($utype == 'a') {
            $checker = $database->query("SELECT * FROM admin WHERE aemail='$email' AND apassword='$password'");
            if ($checker && $checker->num_rows == 1) {
                $_SESSION['user'] = $email;
                $_SESSION['usertype'] = 'a';
                header('Location: admin/index.php');
                exit();
            } else {
                $error = "Wrong credentials: Invalid email or password";
            }

        } elseif ($utype == 'd') {
            $checker = $database->query("SELECT * FROM doctor WHERE docemail='$email' AND docpassword='$password'");
            if ($checker && $checker->num_rows == 1) {
                $_SESSION['user'] = $email;
                $_SESSION['usertype'] = 'd';
                header('Location: doctor/index.php');
                exit();
            } else {
                $error = "Wrong credentials: Invalid email or password";
            }
        }

    } else {
        $error = "No account found for this email";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/animations.css">  
    <link rel="stylesheet" href="css/main.css">  
    <link rel="stylesheet" href="css/login.css">
    <title>Login</title>
</head>
<body>
    <center>
    <div class="container">
        <table border="0" style="width: 60%;">
            <tr>
                <td><p class="header-text">Welcome Back!</p></td>
            </tr>
            <tr>
                <td><p class="sub-text">Login with your details to continue</p></td>
            </tr>
            <tr>
                <form action="" method="POST">
                    <td class="label-td">
                        <label for="useremail" class="form-label">Email: </label>
                        <input type="email" name="useremail" class="input-text" placeholder="Email Address" required>
                    </td>
            </tr>
            <tr>
                <td class="label-td">
                    <label for="userpassword" class="form-label">Password: </label>
                    <input type="password" name="userpassword" class="input-text" placeholder="Password" required>
                </td>
            </tr>
            <tr>
                <td>
                    <br>
                    <label style="color:red;text-align:center;"><?php echo $error; ?></label>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="submit" value="Login" class="login-btn btn-primary btn">
                </td>
            </tr>
            <tr>
                <td>
                    <br>
                    <label class="sub-text">Don't have an account? </label>
                    <a href="signup.php" class="hover-link1 non-style-link">Sign Up</a>
                    <br><br><br>
                </td>
            </tr>
                </form>
        </table>
    </div>
    </center>
</body>
</html>
