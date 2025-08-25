<?php
include_once('include/config.php');
if(isset($_POST['submit']))
{
$fname=$_POST['full_name'];
$address=$_POST['address'];
$city=$_POST['city'];
$gender=$_POST['gender'];
$email=$_POST['email'];
$password=md5($_POST['password']);
$query=mysqli_query($con,"insert into users(fullname,address,city,gender,email,password) values('$fname','$address','$city','$gender','$email','$password')");
if($query)
{
	echo "<script>alert('Successfully Registered. You can login now');</script>";
	//header('location:user-login.php');
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>HMS - Patient Registration</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Join HMS Clinical Management System - Create your patient account for comprehensive healthcare management">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/modern-auth.css">
    
    <style>
        /* Radio Button Styles */
        .radio-group {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }
        
        .radio-option {
            display: flex;
            align-items: center;
            cursor: pointer;
            padding: 10px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .radio-option:hover {
            border-color: #007bff;
            background-color: #f8f9ff;
        }
        
        .radio-option input[type="radio"] {
            margin-right: 10px;
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        
        .radio-option input[type="radio"]:checked + .radio-custom + .radio-label {
            color: #007bff;
            font-weight: 600;
        }
        
        .radio-option input[type="radio"]:checked {
            accent-color: #007bff;
        }
        
        .radio-option.selected {
            border-color: #007bff;
            background-color: #e3f2fd;
        }
        
        .radio-label {
            font-size: 16px;
            color: #495057;
            cursor: pointer;
        }
        
        .required {
            color: #dc3545;
        }
    </style>
    
    <script type="text/javascript">
    function valid()
    {
     if(document.registration.password.value!= document.registration.password_again.value)
    {
    alert("Password and Confirm Password Field do not match  !!");
    document.registration.password_again.focus();
    return false;
    }
    return true;
    }
    </script>
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
                    <h1 class="auth-form-title">Create Account</h1>
                    <p class="auth-form-subtitle">Join our healthcare community</p>
                </div>

                <form name="registration" id="registration" method="post" onSubmit="return valid();" class="auth-form" autocomplete="on">
                    <div class="form-group">
                        <label for="fullName" class="form-label">Full Name</label>
                        <input type="text" class="form-input" id="fullName" name="full_name" placeholder="Enter your full name" autocomplete="name" required>
                    </div>

                    <div class="form-group">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-input" id="address" name="address" placeholder="Enter your address" autocomplete="street-address" required>
                    </div>

                    <div class="form-group">
                        <label for="city" class="form-label">City</label>
                        <input type="text" class="form-input" id="city" name="city" placeholder="Enter your city" autocomplete="address-level2" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Gender <span class="required">*</span></label>
                        <div class="radio-group">
                            <label class="radio-option">
                                <input type="radio" name="gender" value="female" required autocomplete="sex">
                                <span class="radio-custom"></span>
                                <span class="radio-label">Female</span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="gender" value="male" required checked autocomplete="sex">
                                <span class="radio-custom"></span>
                                <span class="radio-label">Male</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-section">
                        <h4><i class="fas fa-shield-alt"></i> Account Security</h4>
                        <p class="section-description">Create secure credentials for your account access</p>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-input" id="email" name="email" onBlur="userAvailability()" placeholder="Enter your email address" autocomplete="email" required>
                        <span id="user-availability-status1"></span>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="password-input-container">
                            <input type="password" class="form-input" id="password" name="password" placeholder="Create a secure password" autocomplete="new-password" required>
                            <button type="button" class="password-toggle" id="passwordToggle">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div id="password-strength-meter"></div>
                    </div>

                    <div class="form-group">
                        <label for="password_again" class="form-label">Confirm Password</label>
                        <div class="password-input-container">
                            <input type="password" class="form-input" id="password_again" name="password_again" placeholder="Confirm your password" autocomplete="new-password" required>
                            <button type="button" class="password-toggle" id="confirmPasswordToggle">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div id="password-match-indicator"></div>
                    </div>

                    <div class="checkbox-option">
                        <input type="checkbox" id="agree" value="agree" checked="true" readonly="true">
                        <span class="checkbox-custom"></span>
                        <label for="agree">
                            I agree to the <a href="#" class="terms-link">Terms of Service</a> and <a href="#" class="privacy-link">Privacy Policy</a>
                        </label>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary" id="submit" name="submit">
                            <span class="btn-text">
                                <i class="fas fa-user-plus"></i> Create Account
                            </span>
                        </button>
                    </div>
                </form>

                <div class="form-footer">
                    <p class="form-footer-text">
                        Already have an account? 
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
    <script src="vendor/modernizr/modernizr.js"></script>
    <script src="vendor/jquery-cookie/jquery.cookie.js"></script>
    <script src="vendor/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="vendor/switchery/switchery.min.js"></script>
    <script src="vendor/jquery-validation/jquery.validate.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/login.js"></script>
    
    <script>
    function userAvailability() {
        $("#loaderIcon").show();
        jQuery.ajax({
            url: "check_availability.php",
            data:'email='+$("#email").val(),
            type: "POST",
            success:function(data){
                $("#user-availability-status1").html(data);
                $("#loaderIcon").hide();
            },
            error:function (){}
        });
    }
    
    jQuery(document).ready(function() {
        Main.init();
        Login.init();
        
        // Password visibility toggles
        $('#passwordToggle, #confirmPasswordToggle').on('click', function() {
            const passwordField = $(this).siblings('input[type="password"], input[type="text"]');
            const icon = $(this).find('i');
            
            if (passwordField.attr('type') === 'password') {
                passwordField.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                passwordField.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });
        
        // Password strength meter
        $('#password').on('input', function() {
            const password = $(this).val();
            const strength = calculatePasswordStrength(password);
            const strengthColors = ['#ef4444', '#f59e0b', '#eab308', '#10b981', '#059669'];
            const strengthWidths = ['20%', '40%', '60%', '80%', '100%'];
            
            $('#password-strength-meter').html(`
                <div class="password-strength">
                    <div class="password-strength-bar">
                        <div class="password-strength-fill" style="background: ${strengthColors[strength]}; width: ${strengthWidths[strength]};"></div>
                    </div>
                    <div class="password-strength-text">Password Strength: ${strengthTexts[strength]}</div>
                </div>
            `);
        });
        
        // Password match indicator
        $('#password_again').on('input', function() {
            const password = $('#password').val();
            const confirmPassword = $(this).val();
            const indicator = $('#password-match-indicator');
            
            if (confirmPassword.length === 0) {
                indicator.removeClass('match no-match').html('');
            } else if (password === confirmPassword) {
                indicator.removeClass('no-match').addClass('match').html('<i class="fas fa-check-circle"></i> Passwords match');
            } else {
                indicator.removeClass('match').addClass('no-match').html('<i class="fas fa-times-circle"></i> Passwords do not match');
            }
        });
        
        // Form submission with loading state
        $('#registration').on('submit', function() {
            const btn = $('#submit');
            const btnText = btn.find('.btn-text');
            const originalText = btnText.html();
            
            btn.addClass('loading');
            btnText.html('<i class="fas fa-spinner fa-spin"></i> Creating Account...');
            
            // Re-enable after 5 seconds if no response
            setTimeout(function() {
                btn.removeClass('loading');
                btnText.html(originalText);
            }, 5000);
        });
        
        // Real-time validation
        $('#fullName').on('blur', function() {
            const name = $(this).val();
            if (name.length < 2) {
                $(this).addClass('error');
                if (!$(this).next('.invalid-feedback').length) {
                    $(this).after('<div class="invalid-feedback">Name must be at least 2 characters long.</div>');
                }
            } else {
                $(this).removeClass('error').addClass('success');
                $(this).next('.invalid-feedback').remove();
            }
        });
        
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
        
        function calculatePasswordStrength(password) {
            let score = 0;
            
            if (password.length >= 8) score++;
            if (/[a-z]/.test(password)) score++;
            if (/[A-Z]/.test(password)) score++;
            if (/[0-9]/.test(password)) score++;
            if (/[^A-Za-z0-9]/.test(password)) score++;
            
            return Math.min(score - 1, 4);
        }
        
        const strengthTexts = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
        
        // Enhanced form validation
        $('#registration').on('submit', function(e) {
            const password = $('#password').val();
            const confirmPassword = $('#password_again').val();
            const email = $('#email').val();
            const name = $('#fullName').val();
            
            let isValid = true;
            
            // Clear previous errors
            $('.invalid-feedback').remove();
            $('.form-input').removeClass('error');
            
            // Name validation
            if (name.length < 2) {
                $('#fullName').addClass('error').after('<div class="invalid-feedback">Name must be at least 2 characters long.</div>');
                isValid = false;
            }
            
            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                $('#email').addClass('error').after('<div class="invalid-feedback">Please enter a valid email address.</div>');
                isValid = false;
            }
            
            // Password validation
            if (password.length < 8) {
                $('#password').addClass('error').after('<div class="invalid-feedback">Password must be at least 8 characters long.</div>');
                isValid = false;
            }
            
            // Password match validation
            if (password !== confirmPassword) {
                $('#password_again').addClass('error').after('<div class="invalid-feedback">Passwords do not match.</div>');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: $('.error').first().offset().top - 100
                }, 500);
            }
        });
        
        // Auto-focus on first field
        $('#fullName').focus();
        
        // Radio button enhancement
        $('input[name="gender"]').on('change', function() {
            $('.radio-option').removeClass('selected');
            $(this).closest('.radio-option').addClass('selected');
        });
        
        // Initialize with selected gender
        $('input[name="gender"]:checked').closest('.radio-option').addClass('selected');
    });
    </script>
</body>
</html>