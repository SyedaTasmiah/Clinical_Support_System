<?php session_start();
error_reporting(0);
include("include/config.php");
if(isset($_POST['submit']))
{
$puname=$_POST['username'];	
$ppwd=md5($_POST['password']);
$ret=mysqli_query($con,"SELECT * FROM users WHERE email='$puname' and password='$ppwd'");
$num=mysqli_fetch_array($ret);
if($num>0)
{
$_SESSION['login']=$_POST['username'];
$_SESSION['id']=$num['id'];
$pid=$num['id'];
$host=$_SERVER['HTTP_HOST'];
$uip=$_SERVER['REMOTE_ADDR'];
$status=1;
// For stroing log if user login successfull
$log=mysqli_query($con,"insert into userlog(uid,username,userip,status) values('$pid','$puname','$uip','$status')");
header("location:dashboard.php");
}
else
{
// For stroing log if user login unsuccessfull
$_SESSION['login']=$_POST['username'];	
$uip=$_SERVER['REMOTE_ADDR'];
$status=0;
mysqli_query($con,"insert into userlog(username,userip,status) values('$puname','$uip','$status')");

echo "<script>alert('Invalid username or password');</script>";
echo "<script>window.location.href='user-login.php'</script>";
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>HMS - Patient Login</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Secure patient login portal for HMS Clinical Management System">
    
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
                    Empowering Healthcare, One Click at a Time: Your Health, Your Records, Your Control.
                </div>
            </div>
        </div>

        <!-- Right Panel - Form Section -->
        <div class="auth-form-panel">
            <div class="auth-form-container">
                <div class="auth-form-header">
                    <div class="auth-form-logo">H</div>
                    <h1 class="auth-form-title">Welcome Back</h1>
                    <p class="auth-form-subtitle">Log in to your patient account</p>
                </div>

                <form class="auth-form" method="post" id="loginForm">
                    <?php if(isset($_SESSION['errmsg']) && $_SESSION['errmsg'] != ""): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-triangle"></i> <?php echo $_SESSION['errmsg']; ?>
                        <?php $_SESSION['errmsg']=""; ?>
                    </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-input" id="email" name="username" placeholder="Enter your email address" required>
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
                                <i class="fas fa-sign-in-alt"></i> Sign In
                            </span>
                        </button>
                    </div>
                </form>

                <div class="form-footer">
                    <p class="form-footer-text">
                        Don't have an account yet? 
                        <a href="registration.php" class="form-footer-link">Sign Up</a>
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
    <script src="vendor/modernizr/modernizr.js"></script>
    <script src="vendor/jquery-cookie/jquery.cookie.js"></script>
    <script src="vendor/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="vendor/switchery/switchery.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/login.js"></script>
    
    <script>
        jQuery(document).ready(function() {
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
            $('#loginForm').on('submit', function() {
                const btn = $('#loginBtn');
                const btnText = btn.find('.btn-text');
                const originalText = btnText.html();
                
                btn.addClass('loading');
                btnText.html('<i class="fas fa-spinner fa-spin"></i> Signing In...');
                
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
            
            // Password strength indicator
            $('#password').on('input', function() {
                const password = $(this).val();
                const strength = calculatePasswordStrength(password);
                const strengthColors = ['#ef4444', '#f59e0b', '#eab308', '#10b981', '#059669'];
                const strengthTexts = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
                
                // Remove existing strength indicators
                $('.password-strength').remove();
                
                if (password.length > 0) {
                    $(this).parent().after(`
                        <div class="password-strength">
                            <div class="password-strength-bar">
                                <div class="password-strength-fill" style="background: ${strengthColors[strength]}; width: ${(strength + 1) * 20}%;"></div>
                            </div>
                            <div class="password-strength-text">Password Strength: ${strengthTexts[strength]}</div>
                        </div>
                    `);
                }
            });
            
            function calculatePasswordStrength(password) {
                let score = 0;
                
                if (password.length >= 8) score++;
                if (/[a-z]/.test(password)) score++;
                if (/[A-Z]/.test(password)) score++;
                if (/[0-9]/.test(password)) score++;
                if (/[^A-Za-z0-9]/.test(password)) score++;
                
                return Math.min(score - 1, 4);
            }
            
            // Auto-focus on email field
            $('#email').focus();
        });
    </script>
</body>
</html>