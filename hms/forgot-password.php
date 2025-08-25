<?php
session_start();
error_reporting(0);
include("include/config.php");
//Checking Details for reset password
if(isset($_POST['submit'])){
$name=$_POST['fullname'];
$email=$_POST['email'];
$query=mysqli_query($con,"select id from  users where fullName='$name' and email='$email'");
$row=mysqli_num_rows($query);
if($row>0){

$_SESSION['name']=$name;
$_SESSION['email']=$email;
header('location:reset-password.php');
} else {
echo "<script>alert('Invalid details. Please try with valid details');</script>";
echo "<script>window.location.href ='forgot-password.php'</script>";


}

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>HMS - Patient Password Recovery</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Recover your patient account password securely">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/modern-auth.css">
</head>
<body class="role-patient">
    <div class="auth-container">
        <!-- Left Panel - Image Section -->
        <div class="auth-image-panel" style="background-image: url('../assets/images/patients.jpg');">
            <div class="auth-image-content">
                <div class="auth-logo">
                    <div class="auth-logo-icon">H</div>
                    <div class="auth-logo-text">HMS Clinical</div>
                </div>
                <div class="auth-tagline">
                    Don't worry! We'll help you recover your account and get back to managing your health records.
                </div>
            </div>
        </div>

        <!-- Right Panel - Form Section -->
        <div class="auth-form-panel">
            <div class="auth-form-container">
                <div class="auth-form-header">
                    <div class="auth-form-logo">H</div>
                    <h1 class="auth-form-title">Password Recovery</h1>
                    <p class="auth-form-subtitle">Let's get you back into your account</p>
                </div>

                <form class="auth-form" method="post" id="recoveryForm">
                    <div class="form-group">
                        <label for="fullname" class="form-label">
                            <i class="fas fa-user"></i> Full Name
                        </label>
                        <input type="text" class="form-input" id="fullname" name="fullname" placeholder="Enter your registered full name" required>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i> Email Address
                        </label>
                        <input type="email" class="form-input" id="email" name="email" placeholder="Enter your registered email" required>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary" id="recoveryBtn" name="submit">
                            <span class="btn-text">
                                <i class="fas fa-key"></i> Reset Password
                            </span>
                        </button>
                    </div>
                </form>

                <div class="form-footer">
                    <p class="form-footer-text">
                        Remember your password?
                        <a href="user-login.php" class="form-footer-link">Sign In</a>
                    </p>
                    <p class="form-footer-text" style="margin-top: 1rem;">
                        <a href="../index.php" class="form-footer-link">
                            <i class="fas fa-home"></i> Back to Home Page
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    
    <script>
        jQuery(document).ready(function() {
            // Form submission with loading state
            $('#recoveryForm').on('submit', function() {
                const btn = $('#recoveryBtn');
                const btnText = btn.find('.btn-text');
                const originalText = btnText.html();
                
                btn.addClass('loading');
                btnText.html('<i class="fas fa-spinner fa-spin"></i> Processing...');
                
                // Re-enable after 5 seconds if no response
                setTimeout(function() {
                    btn.removeClass('loading');
                    btnText.html(originalText);
                }, 5000);
            });

            // Auto-focus on first field
            $('#fullname').focus();
        });
    </script>
</body>
</html>