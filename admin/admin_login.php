<?php
    include ("../session/session_start.php");
    $loginError = isset($_SESSION['login_error']) ? $_SESSION['login_error'] : '';
    unset($_SESSION['login_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- ===== Iconscout CSS ===== -->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">

    <!-- ===== CSS ===== -->
    <link rel="stylesheet" href="../login/index.css">
         
    <title>Admin Login - Agricart</title> 
    <style>
        /* Override for a single-form view */
        .container .forms {
            width: 100%;
            height: auto;
            min-height: 440px;
        }
        .container .form {
            width: 100%;
        }
    </style>
</head>
<body>
    <video autoplay muted id="myVideo">
        <source src="../login/tactor1080_stop.mp4" type="video/mp4">
        Your browser does not support HTML5 video.
    </video>
    
    <div class="container">
        <div class="forms">
            <div class="form login">
                <center><span class="title">Admin Login</span></center>
                
                <?php if ($loginError): ?>
                    <div class="error-message" style="text-align: center; color: #fff; background: rgba(255,0,0,0.3); padding: 10px; border-radius: 5px; margin-top: 15px;">
                        <p><?php echo $loginError; ?></p>
                    </div>
                <?php endif; ?>

                <form action="admin_validation.php" method="POST" autocomplete="off">
                    <div class="input-field">
                        <input type="text" placeholder="Enter Admin Email" name="email" required>
                        <i class="uil uil-envelope icon"></i>
                    </div>
                    <div class="input-field">
                        <input type="password" class="password" placeholder="Enter Admin Password" name="password" required>
                        <i class="uil uil-lock icon"></i>
                        <i class="uil uil-eye-slash showHidePw"></i>
                    </div>

                    <div class="input-field button">
                        <center><button type="submit">Login</button></center>
                    </div>
                </form>

                <div class="login-signup">
                    <span class="text">
                        <a href="../login/login.php" class="text signup-link">Back to Buyer Login</a><br>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Password visibility toggle logic
        const pwShowHide = document.querySelectorAll(".showHidePw"),
              pwFields = document.querySelectorAll(".password");

        pwShowHide.forEach(eyeIcon => {
            eyeIcon.addEventListener("click", () => {
                pwFields.forEach(pwField => {
                    if (pwField.type === "password") {
                        pwField.type = "text";
                        eyeIcon.classList.replace("uil-eye-slash", "uil-eye");
                    } else {
                        pwField.type = "password";
                        eyeIcon.classList.replace("uil-eye", "uil-eye-slash");
                    }
                });
            });
        });
    </script> 
</body>
</html>
