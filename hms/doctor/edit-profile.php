<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('include/config.php');

// Check if doctor is logged in
if(!isset($_SESSION['id']) || strlen($_SESSION['id']) == 0) {
    header('location:logout.php');
    exit();
}

$doctor_id = $_SESSION['id'];

// Handle form submission
if(isset($_POST['submit'])) {
    // Sanitize input data
    $doctorName = mysqli_real_escape_string($con, $_POST['docname']);
    $contactno = mysqli_real_escape_string($con, $_POST['doccontact']);
    $address = mysqli_real_escape_string($con, $_POST['clinicaddress']);
    $specilization = mysqli_real_escape_string($con, $_POST['Doctorspecialization']);
    $docFees = mysqli_real_escape_string($con, $_POST['docfees']);
    $years_of_experience = mysqli_real_escape_string($con, $_POST['years_of_experience']);
    $consultation_fee_range = mysqli_real_escape_string($con, $_POST['consultation_fee_range']);
    $education = mysqli_real_escape_string($con, $_POST['education']);
    $medical_school = mysqli_real_escape_string($con, $_POST['medical_school']);
    $residency = mysqli_real_escape_string($con, $_POST['residency']);
    $fellowship = mysqli_real_escape_string($con, $_POST['fellowship']);
    $degrees = mysqli_real_escape_string($con, $_POST['degrees']);
    $specialization_details = mysqli_real_escape_string($con, $_POST['specialization_details']);
    $languages_spoken = mysqli_real_escape_string($con, $_POST['languages_spoken']);
    $certifications = mysqli_real_escape_string($con, $_POST['certifications']);
    $board_certifications = mysqli_real_escape_string($con, $_POST['board_certifications']);
    $research_interests = mysqli_real_escape_string($con, $_POST['research_interests']);
    $awards_honors = mysqli_real_escape_string($con, $_POST['awards_honors']);
    $professional_memberships = mysqli_real_escape_string($con, $_POST['professional_memberships']);
    $bio = mysqli_real_escape_string($con, $_POST['bio']);
    $availability_note = mysqli_real_escape_string($con, $_POST['availability_note']);

    // Handle profile picture upload
    $profile_picture = '';
    if(isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if(in_array($_FILES['profile_picture']['type'], $allowed_types) && $_FILES['profile_picture']['size'] <= $max_size) {
            $upload_dir = 'uploads/profile_pictures/doctors/';
            if(!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_extension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
            $filename = 'doctor_' . $doctor_id . '_' . time() . '.' . $file_extension;
            $upload_path = $upload_dir . $filename;
            
            if(move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
                // Delete old profile picture if exists
                $old_picture_query = "SELECT profile_picture FROM doctors WHERE id = ?";
                $stmt = mysqli_prepare($con, $old_picture_query);
                mysqli_stmt_bind_param($stmt, "i", $doctor_id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                if($row = mysqli_fetch_assoc($result)) {
                    if($row['profile_picture'] && file_exists($upload_dir . $row['profile_picture'])) {
                        unlink($upload_dir . $row['profile_picture']);
                    }
                }
                $profile_picture = $filename;
            }
        }
    }
    
    // Update query
    $update_query = "UPDATE doctors SET 
        doctorName = ?, 
        contactno = ?, 
        address = ?, 
        specilization = ?, 
        docFees = ?, 
        years_of_experience = ?, 
        consultation_fee_range = ?, 
        education = ?, 
        medical_school = ?, 
        residency = ?, 
        fellowship = ?, 
        degrees = ?, 
        specialization_details = ?, 
        languages_spoken = ?, 
        certifications = ?, 
        board_certifications = ?, 
        research_interests = ?, 
        awards_honors = ?, 
        professional_memberships = ?, 
        bio = ?, 
        availability_note = ?, 
        updationDate = CURRENT_TIMESTAMP";
    
    if($profile_picture) {
        $update_query .= ", profile_picture = ?";
    }
    
    $update_query .= " WHERE id = ?";
    
    $stmt = mysqli_prepare($con, $update_query);
    
    if($profile_picture) {
        mysqli_stmt_bind_param($stmt, "ssssssssssssssssssssssi", 
            $doctorName, $contactno, $address, $specilization, $docFees, 
            $years_of_experience, $consultation_fee_range, $education, 
            $medical_school, $residency, $fellowship, $degrees, 
            $specialization_details, $languages_spoken, $certifications, 
            $board_certifications, $research_interests, $awards_honors, 
            $professional_memberships, $bio, $availability_note, 
            $profile_picture, $doctor_id);
    } else {
        mysqli_stmt_bind_param($stmt, "sssssssssssssssssssssi", 
            $doctorName, $contactno, $address, $specilization, $docFees, 
            $years_of_experience, $consultation_fee_range, $education, 
            $medical_school, $residency, $fellowship, $degrees, 
            $specialization_details, $languages_spoken, $certifications, 
            $board_certifications, $research_interests, $awards_honors, 
            $professional_memberships, $bio, $availability_note, $doctor_id);
    }
    
    if(mysqli_stmt_execute($stmt)) {
        $success_message = "Profile updated successfully!";
        // Redirect to refresh the page
        header('Location: edit-profile.php?updated=1');
        exit();
    } else {
        $error_message = "Error updating profile: " . mysqli_error($con);
    }
}

// Fetch current doctor data
$query = "SELECT * FROM doctors WHERE id = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $doctor_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Doctor | Edit Profile</title>
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>

<body>
    <div id="app">        
        <?php include('include/sidebar.php');?>
        <div class="app-content">
            <?php include('include/header.php');?>
            <div class="main-content">
                <div class="wrap-content container" id="container">
                    <section id="page-title">
                        <div class="row">
                            <div class="col-sm-8">
                                <h1 class="mainTitle">
                                    <i class="fa fa-user-md"></i> Edit Profile
                                </h1>
                            </div>
                        </div>
                    </section>

                    <div class="container-fluid container-fullw bg-white">
                        <!-- Success Message -->
                        <?php if(isset($_GET['updated']) && $_GET['updated'] == '1'): ?>
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <strong>Success!</strong> Your profile has been updated successfully.
                        </div>
                        <?php endif; ?>

                        <!-- Error Message -->
                        <?php if(isset($error_message)): ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <strong>Error!</strong> <?php echo $error_message; ?>
                        </div>
                        <?php endif; ?>

                        <form method="post" enctype="multipart/form-data" onSubmit="return validateForm();">
                            <div class="panel panel-white">
                                <div class="panel-heading">
                                    <h4 class="panel-title">Basic Information</h4>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="docname">Full Name <span class="text-danger">*</span></label>
                                                <input type="text" id="docname" name="docname" class="form-control" 
                                                       value="<?php echo isset($data['doctorName']) ? htmlentities($data['doctorName']) : ''; ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="doccontact">Contact Number <span class="text-danger">*</span></label>
                                                <input type="text" id="doccontact" name="doccontact" class="form-control" 
                                                       value="<?php echo isset($data['contactno']) ? htmlentities($data['contactno']) : ''; ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="Doctorspecialization">Specialization <span class="text-danger">*</span></label>
                                                <select id="Doctorspecialization" name="Doctorspecialization" class="form-control" required>
                                                    <option value="">Select Specialization</option>
                                                    <option value="Cardiology" <?php echo (isset($data['specilization']) && $data['specilization'] == 'Cardiology') ? 'selected' : ''; ?>>Cardiology</option>
                                                    <option value="Dermatology" <?php echo (isset($data['specilization']) && $data['specilization'] == 'Dermatology') ? 'selected' : ''; ?>>Dermatology</option>
                                                    <option value="Endocrinology" <?php echo (isset($data['specilization']) && $data['specilization'] == 'Endocrinology') ? 'selected' : ''; ?>>Endocrinology</option>
                                                    <option value="Gastroenterology" <?php echo (isset($data['specilization']) && $data['specilization'] == 'Gastroenterology') ? 'selected' : ''; ?>>Gastroenterology</option>
                                                    <option value="General Medicine" <?php echo (isset($data['specilization']) && $data['specilization'] == 'General Medicine') ? 'selected' : ''; ?>>General Medicine</option>
                                                    <option value="Neurology" <?php echo (isset($data['specilization']) && $data['specilization'] == 'Neurology') ? 'selected' : ''; ?>>Neurology</option>
                                                    <option value="Oncology" <?php echo (isset($data['specilization']) && $data['specilization'] == 'Oncology') ? 'selected' : ''; ?>>Oncology</option>
                                                    <option value="Orthopedics" <?php echo (isset($data['specilization']) && $data['specilization'] == 'Orthopedics') ? 'selected' : ''; ?>>Orthopedics</option>
                                                    <option value="Pediatrics" <?php echo (isset($data['specilization']) && $data['specilization'] == 'Pediatrics') ? 'selected' : ''; ?>>Pediatrics</option>
                                                    <option value="Psychiatry" <?php echo (isset($data['specilization']) && $data['specilization'] == 'Psychiatry') ? 'selected' : ''; ?>>Psychiatry</option>
                                                    <option value="Radiology" <?php echo (isset($data['specilization']) && $data['specilization'] == 'Radiology') ? 'selected' : ''; ?>>Radiology</option>
                                                    <option value="Surgery" <?php echo (isset($data['specilization']) && $data['specilization'] == 'Surgery') ? 'selected' : ''; ?>>Surgery</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="docfees">Consultation Fee <span class="text-danger">*</span></label>
                                                <input type="number" id="docfees" name="docfees" class="form-control" 
                                                       value="<?php echo isset($data['docFees']) ? htmlentities($data['docFees']) : ''; ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="clinicaddress">Clinic Address <span class="text-danger">*</span></label>
                                        <textarea id="clinicaddress" name="clinicaddress" class="form-control" rows="3" required><?php echo isset($data['address']) ? htmlentities($data['address']) : ''; ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Profile Picture Section -->
                            <div class="panel panel-white">
                                <div class="panel-heading">
                                    <h4 class="panel-title"><i class="fa fa-camera"></i> Profile Picture</h4>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-4 text-center">
                                            <?php if(isset($data['profile_picture']) && $data['profile_picture'] && file_exists('uploads/profile_pictures/doctors/' . $data['profile_picture'])): ?>
                                                <div class="current-profile-picture">
                                                    <h5>Current Picture</h5>
                                                    <img src="uploads/profile_pictures/doctors/<?php echo htmlentities($data['profile_picture']); ?>" 
                                                         alt="Profile Picture" 
                                                         class="img-thumbnail" 
                                                         style="width: 200px; height: 200px; object-fit: cover; border-radius: 10px;">
                                                </div>
                                            <?php else: ?>
                                                <div class="no-profile-picture">
                                                    <div style="width: 200px; height: 200px; background-color: #f8f9fa; border: 2px dashed #dee2e6; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                                        <i class="fa fa-user" style="font-size: 80px; color: #6c757d;"></i>
                                                    </div>
                                                    <p class="text-muted" style="margin-top: 10px;">No profile picture</p>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label for="profile_picture">Upload New Profile Picture</label>
                                                <input type="file" name="profile_picture" id="profile_picture" class="form-control" accept="image/*" onchange="previewImage(this);">
                                                <small class="help-block">
                                                    <i class="fa fa-info-circle"></i> 
                                                    Upload JPG, PNG, or GIF files. Maximum size: 5MB. Recommended: Square image (400x400px or larger).
                                                </small>
                                                <div id="imagePreview" style="margin-top: 15px; display: none;">
                                                    <h6>Preview:</h6>
                                                    <img id="preview" src="" alt="Preview" class="img-thumbnail" style="width: 150px; height: 150px; object-fit: cover; border-radius: 10px;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Experience & Fees Section -->
                            <div class="panel panel-white">
                                <div class="panel-heading">
                                    <h4 class="panel-title"><i class="fa fa-briefcase"></i> Experience & Fees</h4>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="years_of_experience">Years of Experience</label>
                                                <input type="number" id="years_of_experience" name="years_of_experience" class="form-control" min="0" max="50" 
                                                       value="<?php echo isset($data['years_of_experience']) ? htmlentities($data['years_of_experience']) : ''; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="consultation_fee_range">Fee Range</label>
                                                <select id="consultation_fee_range" name="consultation_fee_range" class="form-control">
                                                    <option value="">Select Range</option>
                                                    <option value="Under 500" <?php echo (isset($data['consultation_fee_range']) && $data['consultation_fee_range'] == 'Under 500') ? 'selected' : ''; ?>>Under 500</option>
                                                    <option value="500 - 1000" <?php echo (isset($data['consultation_fee_range']) && $data['consultation_fee_range'] == '500 - 1000') ? 'selected' : ''; ?>>500 - 1000</option>
                                                    <option value="1000 - 2000" <?php echo (isset($data['consultation_fee_range']) && $data['consultation_fee_range'] == '1000 - 2000') ? 'selected' : ''; ?>>1000 - 2000</option>
                                                    <option value="Above 2000" <?php echo (isset($data['consultation_fee_range']) && $data['consultation_fee_range'] == 'Above 2000') ? 'selected' : ''; ?>>Above 2000</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Education & Training Section -->
                            <div class="panel panel-white">
                                <div class="panel-heading">
                                    <h4 class="panel-title"><i class="fa fa-graduation-cap"></i> Education & Training</h4>
                                </div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label for="education">Educational Background</label>
                                        <textarea id="education" name="education" class="form-control" rows="4" 
                                                  placeholder="Describe your educational background, qualifications, and training..."><?php echo isset($data['education']) ? htmlentities($data['education']) : ''; ?></textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="medical_school">Medical School</label>
                                                <input type="text" id="medical_school" name="medical_school" class="form-control" 
                                                       value="<?php echo isset($data['medical_school']) ? htmlentities($data['medical_school']) : ''; ?>" 
                                                       placeholder="Name of medical school">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="residency">Residency Program</label>
                                                <input type="text" id="residency" name="residency" class="form-control" 
                                                       value="<?php echo isset($data['residency']) ? htmlentities($data['residency']) : ''; ?>" 
                                                       placeholder="Residency program and institution">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="fellowship">Fellowship Program</label>
                                        <input type="text" id="fellowship" name="fellowship" class="form-control" 
                                               value="<?php echo isset($data['fellowship']) ? htmlentities($data['fellowship']) : ''; ?>" 
                                               placeholder="Fellowship program and institution">
                                    </div>
                                    <div class="form-group">
                                        <label for="degrees">Degrees & Qualifications</label>
                                        <input type="text" id="degrees" name="degrees" class="form-control" 
                                               value="<?php 
                                               $degrees = isset($data['degrees']) ? json_decode($data['degrees'], true) : [];
                                               echo htmlentities(is_array($degrees) ? implode(', ', $degrees) : (isset($data['degrees']) ? htmlentities($data['degrees']) : ''));
                                               ?>" 
                                               placeholder="MBBS, MD, MS, PhD (comma separated)">
                                        <small class="help-block">Separate multiple degrees with commas</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Specialization Details Section -->
                            <div class="panel panel-white">
                                <div class="panel-heading">
                                    <h4 class="panel-title"><i class="fa fa-stethoscope"></i> Specialization Details</h4>
                                </div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label for="specialization_details">Specialization Details</label>
                                        <textarea id="specialization_details" name="specialization_details" class="form-control" rows="3" 
                                                  placeholder="Describe your areas of expertise, subspecialties, and special interests..."><?php echo isset($data['specialization_details']) ? htmlentities($data['specialization_details']) : ''; ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="languages_spoken">Languages Spoken</label>
                                        <input type="text" id="languages_spoken" name="languages_spoken" class="form-control" 
                                               value="<?php 
                                               $languages = isset($data['languages_spoken']) ? json_decode($data['languages_spoken'], true) : [];
                                               echo htmlentities(is_array($languages) ? implode(', ', $languages) : (isset($data['languages_spoken']) ? htmlentities($data['languages_spoken']) : ''));
                                               ?>" 
                                               placeholder="English, Bangla (comma separated)">
                                        <small class="help-block">Separate multiple languages with commas</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Certifications & Memberships Section -->
                            <div class="panel panel-white">
                                <div class="panel-heading">
                                    <h4 class="panel-title"><i class="fa fa-certificate"></i> Certifications & Memberships</h4>
                                </div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label for="certifications">Professional Certifications</label>
                                        <input type="text" id="certifications" name="certifications" class="form-control" 
                                               value="<?php 
                                               $certifications = isset($data['certifications']) ? json_decode($data['certifications'], true) : [];
                                               echo htmlentities(is_array($certifications) ? implode(', ', $certifications) : (isset($data['certifications']) ? htmlentities($data['certifications']) : ''));
                                               ?>" 
                                               placeholder="CPR Certified, ACLS, BLS (comma separated)">
                                        <small class="help-block">Separate multiple certifications with commas</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="board_certifications">Board Certifications</label>
                                        <input type="text" id="board_certifications" name="board_certifications" class="form-control" 
                                               value="<?php 
                                               $board_certs = isset($data['board_certifications']) ? json_decode($data['board_certifications'], true) : [];
                                               echo htmlentities(is_array($board_certs) ? implode(', ', $board_certs) : (isset($data['board_certifications']) ? htmlentities($data['board_certifications']) : ''));
                                               ?>" 
                                               placeholder="American Board of Internal Medicine (comma separated)">
                                        <small class="help-block">Separate multiple board certifications with commas</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="professional_memberships">Professional Memberships</label>
                                        <input type="text" id="professional_memberships" name="professional_memberships" class="form-control" 
                                               value="<?php 
                                               $memberships = isset($data['professional_memberships']) ? json_decode($data['professional_memberships'], true) : [];
                                               echo htmlentities(is_array($memberships) ? implode(', ', $memberships) : (isset($data['professional_memberships']) ? htmlentities($data['professional_memberships']) : ''));
                                               ?>" 
                                               placeholder="IMA, AMA, Local Medical Association (comma separated)">
                                        <small class="help-block">Separate multiple memberships with commas</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Research & Achievements Section -->
                            <div class="panel panel-white">
                                <div class="panel-heading">
                                    <h4 class="panel-title"><i class="fa fa-trophy"></i> Research & Achievements</h4>
                                </div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label for="research_interests">Research Interests & Publications</label>
                                        <textarea id="research_interests" name="research_interests" class="form-control" rows="4" 
                                                  placeholder="Describe your research interests, publications, and ongoing studies..."><?php echo isset($data['research_interests']) ? htmlentities($data['research_interests']) : ''; ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="awards_honors">Awards & Honors</label>
                                        <input type="text" id="awards_honors" name="awards_honors" class="form-control" 
                                               value="<?php 
                                               $awards = isset($data['awards_honors']) ? json_decode($data['awards_honors'], true) : [];
                                               echo htmlentities(is_array($awards) ? implode(', ', $awards) : (isset($data['awards_honors']) ? htmlentities($data['awards_honors']) : ''));
                                               ?>" 
                                               placeholder="Best Doctor Award 2023, Research Excellence (comma separated)">
                                        <small class="help-block">Separate multiple awards with commas</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Professional Information Section -->
                            <div class="panel panel-white">
                                <div class="panel-heading">
                                    <h4 class="panel-title"><i class="fa fa-user-md"></i> Professional Information</h4>
                                </div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label for="bio">Professional Biography</label>
                                        <textarea id="bio" name="bio" class="form-control" rows="6" 
                                                  placeholder="Write a professional biography that patients and colleagues can read. Include your approach to medicine, philosophy, and what makes you unique as a healthcare provider..."><?php echo isset($data['bio']) ? htmlentities($data['bio']) : ''; ?></textarea>
                                        <small class="help-block">This will be displayed to patients when they view your profile</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="availability_note">Availability Notes</label>
                                        <textarea id="availability_note" name="availability_note" class="form-control" rows="3" 
                                                  placeholder="Special notes about your availability, emergency contact information, or scheduling preferences..."><?php echo isset($data['availability_note']) ? htmlentities($data['availability_note']) : ''; ?></textarea>
                                    </div>
                                    
                                    <div class="text-center" style="margin: 20px 0;">
                                        <button type="submit" name="submit" class="btn btn-primary btn-lg">
                                            <i class="fa fa-save"></i> Update Profile
                                        </button>
                                        <a href="dashboard.php" class="btn btn-default btn-lg" style="margin-left: 10px;">
                                            <i class="fa fa-arrow-left"></i> Back to Dashboard
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <?php include('include/footer.php');?>
        <?php include('include/setting.php');?>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/main.js"></script>
    
    <script>
        // Image preview function
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#preview').attr('src', e.target.result);
                    $('#imagePreview').show();
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Form validation
        function validateForm() {
            var isValid = true;
            
            // Check required fields
            var requiredFields = ['docname', 'doccontact', 'Doctorspecialization', 'docfees', 'clinicaddress'];
            requiredFields.forEach(function(fieldId) {
                var field = document.getElementById(fieldId);
                if (!field.value.trim()) {
                    field.style.borderColor = '#dc3545';
                    isValid = false;
                } else {
                    field.style.borderColor = '#ced4da';
                }
            });
            
            if (!isValid) {
                alert('Please fill in all required fields marked with *');
                return false;
            }
            
            return true;
        }

        // Auto-hide success/error messages after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    </script>
</body>
</html>
