<?php
session_start();
include("include/config.php");
error_reporting(0);
if(isset($_POST['submit']))
{
$uname=$_POST['username'];
$dpassword=md5($_POST['password']);	
$ret=mysqli_query($con,"SELECT * FROM doctors WHERE docEmail='$uname' and password='$dpassword'");
$num=mysqli_fetch_array($ret);
if($num>0)
{
$_SESSION['dlogin']=$_POST['username'];
$_SESSION['id']=$num['id'];
$uid=$num['id'];
$uip=$_SERVER['REMOTE_ADDR'];
$status=1;
//Code Logs
$log=mysqli_query($con,"insert into doctorslog(uid,username,userip,status) values('$uid','$uname','$uip','$status')");

header("location:dashboard.php");
}
else
{

$uip=$_SERVER['REMOTE_ADDR'];
$status=0;
mysqli_query($con,"insert into doctorslog(username,userip,status) values('$uname','$uip','$status')");
echo "<script>alert('Invalid username or password');</script>";
echo "<script>window.location.href='index.php'</script>";

}
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>HMS - Doctor Portal</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="HMS Doctor Portal - Secure access for healthcare professionals and patient management">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/modern-auth.css">
</head>
<body class="role-doctor">
    <div class="auth-container">
        <!-- Left Panel - Image Section -->
        <div class="auth-image-panel" style="background-image: url('../../assets/images/doctors.jpg');">
            <div class="auth-image-content">
                <div class="auth-logo">
                    <div class="auth-logo-icon">H</div>
                    <div class="auth-logo-text">HMS Clinical</div>
                </div>
                <div class="auth-tagline">
                    Empowering Healthcare, One Click at a Time: Your Health, Your Records, Your Control.
                </div>
            </div>
        </div>

        <!-- Right Panel - Form Section -->
        <div class="auth-form-panel">
            <div class="auth-form-container">
                <div class="auth-form-header">
                    <div class="auth-form-logo">H</div>
                    <h1 class="auth-form-title">Doctor Portal</h1>
                    <p class="auth-form-subtitle">Access your medical dashboard</p>
                </div>

                <form class="auth-form" method="post" id="doctorLoginForm">
                    <?php if(isset($_SESSION['errmsg']) && $_SESSION['errmsg'] != ""): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-triangle"></i> <?php echo $_SESSION['errmsg']; ?>
                        <?php $_SESSION['errmsg']=""; ?>
                    </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-input" id="email" name="username" placeholder="Enter your doctor email" required>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="password-input-container">
                            <input type="password" class="form-input" id="password" name="password" placeholder="Enter your password" required>
                            <button type="button" class="password-toggle" id="passwordToggle">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-actions">
                        <div class="forgot-password">
                            <a href="forgot-password.php">Forgot Password?</a>
                        </div>
                        <button type="submit" class="btn btn-primary" id="loginBtn" name="submit">
                            <span class="btn-text">
                                <i class="fas fa-stethoscope"></i> Access Portal
                            </span>
                        </button>
                    </div>
                </form>

                <div class="form-footer">
                    <p class="form-footer-text">
                        Need a doctor account? 
                        <a href="doctor-registration.php" class="form-footer-link">Register as Doctor</a>
                    </p>
                    <p class="form-footer-text" style="margin-top: 1rem;">
                        <a href="../../index.php" class="form-footer-link">
                            <i class="fas fa-home"></i> Back to Home Page
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="vendor/modernizr/modernizr.js"></script>
    <script src="vendor/jquery-cookie/jquery.cookie.js"></script>
    <script src="vendor/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="vendor/switchery/switchery.min.js"></script>
    <script src="vendor/jquery-validation/jquery.validate.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/login.js"></script>
    
    <script>
        jQuery(document).ready(function() {
            Main.init();
            Login.init();
            
            // Password visibility toggle
            $('#passwordToggle').on('click', function() {
                const passwordField = $('#password');
                const icon = $(this).find('i');
                
                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    passwordField.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });
            
            // Form submission with loading state
            $('#doctorLoginForm').on('submit', function() {
                const btn = $('#loginBtn');
                const btnText = btn.find('.btn-text');
                const originalText = btnText.html();
                
                btn.addClass('loading');
                btnText.html('<i class="fas fa-spinner fa-spin"></i> Authenticating...');
                
                // Re-enable after 3 seconds if no response
                setTimeout(function() {
                    btn.removeClass('loading');
                    btnText.html(originalText);
                }, 3000);
            });
            
            // Email validation
            $('#email').on('blur', function() {
                const email = $(this).val();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                
                if (email && !emailRegex.test(email)) {
                    $(this).addClass('error');
                    if (!$(this).next('.invalid-feedback').length) {
                        $(this).after('<div class="invalid-feedback">Please enter a valid email address.</div>');
                    }
                } else {
                    $(this).removeClass('error').addClass('success');
                    $(this).next('.invalid-feedback').remove();
                }
            });
            
            // Enhanced security features
            let loginAttempts = 0;
            const maxAttempts = 5;
            
            $('#doctorLoginForm').on('submit', function(e) {
                loginAttempts++;
                
                if (loginAttempts > maxAttempts) {
                    e.preventDefault();
                    alert('Too many login attempts. Please try again later.');
                    return false;
                }
                
                // Store attempt count in session storage
                sessionStorage.setItem('doctorLoginAttempts', loginAttempts);
            });
            
            // Check for existing attempts on page load
            const existingAttempts = sessionStorage.getItem('doctorLoginAttempts');
            if (existingAttempts) {
                loginAttempts = parseInt(existingAttempts);
            }
            
            // Auto-focus on email field
            $('#email').focus();
            
            // Keyboard shortcuts
            $(document).on('keydown', function(e) {
                // Ctrl+Enter to submit form
                if (e.ctrlKey && e.keyCode === 13) {
                    $('#doctorLoginForm').submit();
                }
            });
            
            // Medical-themed welcome message
            const welcomeMessages = [
                "Welcome back, Doctor! Ready to provide excellent care?",
                "Good to see you, Doctor! Your patients are waiting.",
                "Welcome, Doctor! Let's make a difference today.",
                "Hello, Doctor! Time to deliver exceptional healthcare."
            ];
            
            const randomMessage = welcomeMessages[Math.floor(Math.random() * welcomeMessages.length)];
            $('.auth-form-subtitle').text(randomMessage);
        });
    </script>
</body>
</html>