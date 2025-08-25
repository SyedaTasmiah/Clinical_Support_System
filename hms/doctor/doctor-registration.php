<?php
include_once('include/config.php');
if(isset($_POST['submit']))
{
$docspecialization=$_POST['Doctorspecialization'];
$docname=$_POST['docname'];
$docaddress=$_POST['clinicaddress'];
$docfees=$_POST['docfees'];
$doccontactno=$_POST['doccontact'];
$docemail=$_POST['docemail'];
$password=md5($_POST['npass']);
$sql=mysqli_query($con,"insert into doctors(specilization,doctorName,address,docFees,contactno,docEmail,password) values('$docspecialization','$docname','$docaddress','$docfees','$doccontactno','$docemail','$password')");
if($sql)
{
	echo "<script>alert('Doctor Registration Successful. You can login now');</script>";
	echo "<script>window.location.href='index.php'</script>";
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>HMS - Doctor Registration</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Join our medical team as a healthcare professional">
    
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
                    Join our medical team and provide quality patient care through our advanced clinical management system.
                </div>
            </div>
        </div>

        <!-- Right Panel - Form Section -->
        <div class="auth-form-panel">
            <div class="auth-form-container">
                <div class="auth-form-header">
                    <div class="auth-form-logo">H</div>
                    <h1 class="auth-form-title">Join Our Medical Team</h1>
                    <p class="auth-form-subtitle">Register as a healthcare professional</p>
                </div>

                <form class="auth-form" name="registration" id="registration" method="post" onSubmit="return valid();">
                    <div class="form-group">
                        <label for="DoctorSpecialization" class="form-label">
                            <i class="fas fa-stethoscope"></i> Medical Specialization
                        </label>
                        <select name="Doctorspecialization" class="form-input" required="true">
                            <option value="">Choose your medical specialization</option>
<?php $ret=mysqli_query($con,"select * from doctorspecilization");
$count=mysqli_num_rows($ret);
if($count > 0) {
while($row=mysqli_fetch_array($ret))
{
?>
                            <option value="<?php echo htmlentities($row['specilization']);?>">
                                <?php echo htmlentities($row['specilization']);?>
                            </option>
<?php } 
} else {
    // Default specializations if none exist in database
    $default_specializations = array('General Medicine', 'Cardiology', 'Neurology', 'Orthopedics', 'Pediatrics', 'Dermatology', 'Psychiatry', 'Gynecology');
    foreach($default_specializations as $spec) {
?>
                            <option value="<?php echo $spec;?>"><?php echo $spec;?></option>
<?php }
} ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="docname" class="form-label">
                            <i class="fas fa-user-md"></i> Full Name
                        </label>
                        <input type="text" class="form-input" id="docname" name="docname" placeholder="Enter your full name" required>
                    </div>

                    <div class="form-group">
                        <label for="clinicaddress" class="form-label">
                            <i class="fas fa-clinic-medical"></i> Clinic Address
                        </label>
                        <textarea class="form-input" id="clinicaddress" name="clinicaddress" placeholder="Enter your clinic address" rows="3" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="docfees" class="form-label">
                            <i class="fas fa-dollar-sign"></i> Consultation Fees
                        </label>
                        <input type="number" class="form-input" id="docfees" name="docfees" placeholder="Enter consultation fees" required>
                    </div>

                    <div class="form-group">
                        <label for="doccontact" class="form-label">
                            <i class="fas fa-phone"></i> Contact Number
                        </label>
                        <input type="tel" class="form-input" id="doccontact" name="doccontact" placeholder="Enter your contact number" required>
                    </div>

                    <div class="form-group">
                        <label for="docemail" class="form-label">
                            <i class="fas fa-envelope"></i> Email Address
                        </label>
                        <input type="email" class="form-input" id="docemail" name="docemail" placeholder="Enter your email address" required onblur="checkemailAvailability()">
                        <div id="email-availability-status"></div>
                    </div>

                    <div class="form-section">
                        <h4><i class="fas fa-shield-alt"></i> Account Security</h4>
                        <p class="section-description">Create a strong password to secure your account</p>
                        
                        <div class="form-group">
                            <label for="npass" class="form-label">
                                <i class="fas fa-lock"></i> Password
                            </label>
                            <div class="password-input-container">
                                <input type="password" class="form-input" id="npass" name="npass" placeholder="Create a strong password" required>
                                <button type="button" class="password-toggle" id="passwordToggle1">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div id="password-strength-meter"></div>
                        </div>

                        <div class="form-group">
                            <label for="cfpass" class="form-label">
                                <i class="fas fa-lock"></i> Confirm Password
                            </label>
                            <div class="password-input-container">
                                <input type="password" class="form-input" id="cfpass" name="cfpass" placeholder="Confirm your password" required>
                                <button type="button" class="password-toggle" id="passwordToggle2">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div id="password-match-indicator"></div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary" id="registerBtn" name="submit">
                            <span class="btn-text">
                                <i class="fas fa-user-plus"></i> Register as Doctor
                            </span>
                        </button>
                    </div>
                </form>

                <div class="form-footer">
                    <p class="form-footer-text">
                        Already have an account?
                        <a href="index.php" class="form-footer-link">Sign In</a>
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
    
    <script>
        jQuery(document).ready(function() {
            // Password visibility toggles
            $('#passwordToggle1').on('click', function() {
                const passwordField = $('#npass');
                const icon = $(this).find('i');
                
                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    passwordField.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            $('#passwordToggle2').on('click', function() {
                const passwordField = $('#cfpass');
                const icon = $(this).find('i');
                
                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    passwordField.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // Password strength indicator
            $('#npass').on('input', function() {
                const password = $(this).val();
                const strength = calculatePasswordStrength(password);
                const strengthColors = ['#ef4444', '#f59e0b', '#eab308', '#10b981', '#059669'];
                const strengthTexts = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
                
                // Remove existing strength indicators
                $('#password-strength-meter').empty();
                
                if (password.length > 0) {
                    $('#password-strength-meter').html(`
                        <div class="password-strength">
                            <div class="password-strength-bar">
                                <div class="password-strength-fill" style="background: ${strengthColors[strength]}; width: ${(strength + 1) * 20}%;"></div>
                            </div>
                            <div class="password-strength-text">Password Strength: ${strengthTexts[strength]}</div>
                        </div>
                    `);
                }
            });

            // Password match indicator
            $('#cfpass').on('input', function() {
                const password = $('#npass').val();
                const confirmPassword = $(this).val();
                
                // Remove existing match indicators
                $('#password-match-indicator').empty();
                
                if (confirmPassword.length > 0) {
                    if (password === confirmPassword) {
                        $('#password-match-indicator').html(`
                            <div class="password-match match">
                                <i class="fas fa-check-circle"></i> Passwords match
                            </div>
                        `);
                    } else {
                        $('#password-match-indicator').html(`
                            <div class="password-match no-match">
                                <i class="fas fa-times-circle"></i> Passwords do not match
                            </div>
                        `);
                    }
                }
            });

            // Form submission with loading state
            $('#registration').on('submit', function() {
                const btn = $('#registerBtn');
                const btnText = btn.find('.btn-text');
                const originalText = btnText.html();
                
                btn.addClass('loading');
                btnText.html('<i class="fas fa-spinner fa-spin"></i> Registering...');
                
                // Re-enable after 5 seconds if no response
                setTimeout(function() {
                    btn.removeClass('loading');
                    btnText.html(originalText);
                }, 5000);
            });

            // Auto-focus on first field
            $('#docname').focus();
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

        function valid() {
            if(document.registration.npass.value != document.registration.cfpass.value) {
                alert("Password and Confirm Password Field do not match!");
                document.registration.cfpass.focus();
                return false;
            }
            return true;
        }

        function checkemailAvailability() {
            $("#email-availability-status").html('<div class="password-strength-text">Checking availability...</div>');
            jQuery.ajax({
                url: "check_availability.php",
                data:'emailid='+$("#docemail").val(),
                type: "POST",
                success:function(data){
                    $("#email-availability-status").html(data);
                },
                error:function (){
                    $("#email-availability-status").html('<div class="password-strength-text">Error checking availability</div>');
                }
            });
        }
    </script>
</body>
</html>
