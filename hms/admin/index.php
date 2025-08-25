<?php
session_start();
error_reporting(0);
include("include/config.php");
if(isset($_POST['submit']))
{
$uname=$_POST['username'];
$upassword=$_POST['password'];

$ret=mysqli_query($con,"SELECT * FROM admin WHERE username='$uname' and password='$upassword'");
$num=mysqli_fetch_array($ret);
if($num>0)
{
$_SESSION['login']=$_POST['username'];
$_SESSION['id']=$num['id'];
header("location:dashboard.php");

}
else
{
$_SESSION['errmsg']="Invalid username or password";

}
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>HMS - Admin Portal</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="HMS Admin Portal - Secure access for hospital administrators and system management">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/modern-auth.css">
</head>
<body class="role-admin">
    <div class="auth-container">
        <!-- Left Panel - Image Section -->
        <div class="auth-image-panel" style="background-image: url('../../assets/images/admin.jpg');">
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
                    <h1 class="auth-form-title">Admin Access</h1>
                    <p class="auth-form-subtitle">Secure administrator portal</p>
                </div>

                <form class="auth-form" method="post" id="adminLoginForm">
                    <?php if(isset($_SESSION['errmsg']) && $_SESSION['errmsg'] != ""): ?>
                    <div class="error-message">
                        <i class="fas fa-shield-alt"></i> <?php echo htmlentities($_SESSION['errmsg']); ?>
                        <?php $_SESSION['errmsg']=""; ?>
                    </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-input" id="username" name="username" placeholder="Enter administrator username" required>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="password-input-container">
                            <input type="password" class="form-input" id="password" name="password" placeholder="Enter administrator password" required>
                            <button type="button" class="password-toggle" id="passwordToggle">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary" id="loginBtn" name="submit">
                            <span class="btn-text">
                                <i class="fas fa-sign-in-alt"></i> Access System
                            </span>
                        </button>
                    </div>
                </form>

                <div class="form-footer">
                    <p class="form-footer-text">
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
            $('#adminLoginForm').on('submit', function() {
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
            
            // Username validation
            $('#username').on('blur', function() {
                const username = $(this).val();
                
                if (username && username.length < 3) {
                    $(this).addClass('error');
                    if (!$(this).next('.invalid-feedback').length) {
                        $(this).after('<div class="invalid-feedback">Username must be at least 3 characters long.</div>');
                    }
                } else {
                    $(this).removeClass('error').addClass('success');
                    $(this).next('.invalid-feedback').remove();
                }
            });
            
            // Enhanced security features
            let loginAttempts = 0;
            const maxAttempts = 5;
            
            $('#adminLoginForm').on('submit', function(e) {
                loginAttempts++;
                
                if (loginAttempts > maxAttempts) {
                    e.preventDefault();
                    alert('Too many login attempts. Please try again later.');
                    return false;
                }
                
                // Store attempt count in session storage
                sessionStorage.setItem('adminLoginAttempts', loginAttempts);
            });
            
            // Check for existing attempts on page load
            const existingAttempts = sessionStorage.getItem('adminLoginAttempts');
            if (existingAttempts) {
                loginAttempts = parseInt(existingAttempts);
            }
            
            // Auto-focus on username field
            $('#username').focus();
            
            // Keyboard shortcuts
            $(document).on('keydown', function(e) {
                // Ctrl+Enter to submit form
                if (e.ctrlKey && e.keyCode === 13) {
                    $('#adminLoginForm').submit();
                }
            });
        });
    </script>
</body>
</html>