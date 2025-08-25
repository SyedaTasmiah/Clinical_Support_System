<?php
include('include/config.php');
error_reporting(0);

$doctor_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$doctor_id) {
    header('Location: doctor-directory.php');
    exit();
}

// Get doctor information
$doctor_query = "SELECT * FROM doctors WHERE id = ?";
$stmt = mysqli_prepare($con, $doctor_query);
mysqli_stmt_bind_param($stmt, "i", $doctor_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$doctor = mysqli_fetch_array($result);

if (!$doctor) {
    header('Location: doctor-directory.php');
    exit();
}

// Get specialization details
$spec_query = "SELECT * FROM doctorspecilization WHERE specilization = ?";
$spec_stmt = mysqli_prepare($con, $spec_query);
mysqli_stmt_bind_param($spec_stmt, "s", $doctor['specilization']);
mysqli_stmt_execute($spec_stmt);
$spec_result = mysqli_stmt_get_result($spec_stmt);
$specialization = mysqli_fetch_array($spec_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dr. <?php echo htmlentities($doctor['doctorName']); ?> - Profile</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 0;
            margin-bottom: 30px;
        }
        
        .profile-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .doctor-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .doctor-name {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .doctor-specialization {
            font-size: 1.3rem;
            opacity: 0.9;
            margin-bottom: 20px;
        }
        
        .section-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 20px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        
        .info-item {
            margin-bottom: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        
        .info-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
        }
        
        .info-value {
            color: #6c757d;
        }
        
        .tag {
            display: inline-block;
            background: #e9ecef;
            color: #495057;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 0.85rem;
            margin-right: 8px;
            margin-bottom: 8px;
        }
        
        .tag.degree {
            background: #d4edda;
            color: #155724;
        }
        
        .tag.certification {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .tag.language {
            background: #fff3cd;
            color: #856404;
        }
        
        .tag.membership {
            background: #f8d7da;
            color: #721c24;
        }
        
        .btn-back {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 10px 25px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .btn-print {
            background: #28a745;
            border: none;
            border-radius: 8px;
            padding: 10px 25px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .btn-print:hover {
            background: #218838;
            color: white;
        }
        
        .contact-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .contact-item {
            margin-bottom: 10px;
        }
        
        .contact-item i {
            width: 20px;
            text-align: center;
            margin-right: 10px;
        }
        
        @media print {
            .no-print {
                display: none !important;
            }
            
            .profile-header {
                background: #667eea !important;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>
    <!-- Profile Header -->
    <div class="profile-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-3 text-center">
                    <img src="<?php echo !empty($doctor['profile_picture']) ? 'doctor/uploads/profile_pictures/doctors/' . $doctor['profile_picture'] : 'doctor/assets/images/media-user.png'; ?>" 
                         alt="Dr. <?php echo htmlentities($doctor['doctorName']); ?>" 
                         class="doctor-avatar">
                </div>
                <div class="col-md-9">
                    <h1 class="doctor-name">Dr. <?php echo htmlentities($doctor['doctorName']); ?></h1>
                    <p class="doctor-specialization">
                        <i class="fa fa-stethoscope"></i> <?php echo htmlentities($doctor['specilization']); ?>
                    </p>
                    
                    <?php if (!empty($doctor['years_of_experience'])): ?>
                        <p><i class="fa fa-clock"></i> <?php echo $doctor['years_of_experience']; ?> years of experience</p>
                    <?php endif; ?>
                    
                    <?php if (!empty($doctor['consultation_fee_range'])): ?>
                        <p><i class="fa fa-money-bill"></i> Consultation Fee: <?php echo htmlentities($doctor['consultation_fee_range']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <!-- Basic Information -->
                <div class="profile-card">
                    <h3 class="section-title"><i class="fa fa-user"></i> Basic Information</h3>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">Full Name</div>
                                <div class="info-value">Dr. <?php echo htmlentities($doctor['doctorName']); ?></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">Specialization</div>
                                <div class="info-value"><?php echo htmlentities($doctor['specilization']); ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">Email</div>
                                <div class="info-value"><?php echo htmlentities($doctor['docEmail']); ?></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">Contact Number</div>
                                <div class="info-value"><?php echo htmlentities($doctor['contactno']); ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Clinic Address</div>
                        <div class="info-value"><?php echo htmlentities($doctor['address']); ?></div>
                    </div>
                </div>

                <!-- Professional Background -->
                <?php if (!empty($doctor['education']) || !empty($doctor['medical_school']) || !empty($doctor['residency'])): ?>
                <div class="profile-card">
                    <h3 class="section-title"><i class="fa fa-graduation-cap"></i> Education & Training</h3>
                    
                    <?php if (!empty($doctor['education'])): ?>
                        <div class="info-item">
                            <div class="info-label">Education</div>
                            <div class="info-value"><?php echo nl2br(htmlentities($doctor['education'])); ?></div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($doctor['medical_school'])): ?>
                        <div class="info-item">
                            <div class="info-label">Medical School</div>
                            <div class="info-value"><?php echo htmlentities($doctor['medical_school']); ?></div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($doctor['residency'])): ?>
                        <div class="info-item">
                            <div class="info-label">Residency</div>
                            <div class="info-value"><?php echo htmlentities($doctor['residency']); ?></div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($doctor['fellowship'])): ?>
                        <div class="info-item">
                            <div class="info-label">Fellowship</div>
                            <div class="info-value"><?php echo htmlentities($doctor['fellowship']); ?></div>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Degrees & Certifications -->
                <div class="profile-card">
                    <h3 class="section-title"><i class="fa fa-certificate"></i> Degrees & Certifications</h3>
                    
                    <?php if (!empty($doctor['degrees'])): ?>
                        <div class="mb-3">
                            <div class="info-label">Degrees</div>
                            <div class="mt-2">
                                <?php 
                                $degrees = json_decode($doctor['degrees'], true);
                                if (is_array($degrees)) {
                                    foreach ($degrees as $degree) {
                                        echo '<span class="tag degree">' . htmlentities($degree) . '</span>';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($doctor['certifications'])): ?>
                        <div class="mb-3">
                            <div class="info-label">Certifications</div>
                            <div class="mt-2">
                                <?php 
                                $certifications = json_decode($doctor['certifications'], true);
                                if (is_array($certifications)) {
                                    foreach ($certifications as $certification) {
                                        echo '<span class="tag certification">' . htmlentities($certification) . '</span>';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($doctor['board_certifications'])): ?>
                        <div class="info-item">
                            <div class="info-label">Board Certifications</div>
                            <div class="info-value"><?php echo nl2br(htmlentities($doctor['board_certifications'])); ?></div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Professional Details -->
                <?php if (!empty($doctor['specialization_details']) || !empty($doctor['research_interests'])): ?>
                <div class="profile-card">
                    <h3 class="section-title"><i class="fa fa-briefcase"></i> Professional Details</h3>
                    
                    <?php if (!empty($doctor['specialization_details'])): ?>
                        <div class="info-item">
                            <div class="info-label">Specialization Details</div>
                            <div class="info-value"><?php echo nl2br(htmlentities($doctor['specialization_details'])); ?></div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($doctor['research_interests'])): ?>
                        <div class="info-item">
                            <div class="info-label">Research Interests</div>
                            <div class="info-value"><?php echo nl2br(htmlentities($doctor['research_interests'])); ?></div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($doctor['awards_honors'])): ?>
                        <div class="info-item">
                            <div class="info-label">Awards & Honors</div>
                            <div class="info-value"><?php echo nl2br(htmlentities($doctor['awards_honors'])); ?></div>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- About -->
                <?php if (!empty($doctor['bio'])): ?>
                <div class="profile-card">
                    <h3 class="section-title"><i class="fa fa-info-circle"></i> About Dr. <?php echo htmlentities($doctor['doctorName']); ?></h3>
                    <p><?php echo nl2br(htmlentities($doctor['bio'])); ?></p>
                </div>
                <?php endif; ?>

                <!-- Languages & Memberships -->
                <div class="profile-card">
                    <h3 class="section-title"><i class="fa fa-globe"></i> Languages & Memberships</h3>
                    
                    <?php if (!empty($doctor['languages_spoken'])): ?>
                        <div class="mb-3">
                            <div class="info-label">Languages Spoken</div>
                            <div class="mt-2">
                                <?php 
                                $languages = json_decode($doctor['languages_spoken'], true);
                                if (is_array($languages)) {
                                    foreach ($languages as $language) {
                                        echo '<span class="tag language">' . htmlentities($language) . '</span>';
                                    }
                                } else {
                                    // If not JSON, treat as comma-separated string
                                    $languages = explode(',', $doctor['languages_spoken']);
                                    foreach ($languages as $language) {
                                        echo '<span class="tag language">' . htmlentities(trim($language)) . '</span>';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($doctor['professional_memberships'])): ?>
                        <div class="mb-3">
                            <div class="info-label">Professional Memberships</div>
                            <div class="mt-2">
                                <?php 
                                $memberships = json_decode($doctor['professional_memberships'], true);
                                if (is_array($memberships)) {
                                    foreach ($memberships as $membership) {
                                        echo '<span class="tag membership">' . htmlentities($membership) . '</span>';
                                    }
                                } else {
                                    echo '<div class="info-value">' . nl2br(htmlentities($doctor['professional_memberships'])) . '</div>';
                                }
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Contact Information -->
                <div class="contact-info">
                    <h4><i class="fa fa-phone"></i> Contact Information</h4>
                    
                    <div class="contact-item">
                        <i class="fa fa-envelope"></i>
                        <a href="mailto:<?php echo htmlentities($doctor['docEmail']); ?>" style="color: white;">
                            <?php echo htmlentities($doctor['docEmail']); ?>
                        </a>
                    </div>
                    
                    <div class="contact-item">
                        <i class="fa fa-phone"></i>
                        <a href="tel:<?php echo htmlentities($doctor['contactno']); ?>" style="color: white;">
                            <?php echo htmlentities($doctor['contactno']); ?>
                        </a>
                    </div>
                    
                    <div class="contact-item">
                        <i class="fa fa-map-marker-alt"></i>
                        <?php echo htmlentities($doctor['address']); ?>
                    </div>
                </div>

                <!-- Quick Info -->
                <div class="profile-card">
                    <h4><i class="fa fa-info"></i> Quick Information</h4>
                    
                    <div class="info-item">
                        <div class="info-label">Consultation Fees</div>
                        <div class="info-value">
                            <?php echo !empty($doctor['docFees']) ? htmlentities($doctor['docFees']) : 'Contact for details'; ?>
                        </div>
                    </div>
                    
                    <?php if (!empty($doctor['consultation_fee_range'])): ?>
                        <div class="info-item">
                            <div class="info-label">Fee Range</div>
                            <div class="info-value"><?php echo htmlentities($doctor['consultation_fee_range']); ?></div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($doctor['availability_note'])): ?>
                        <div class="info-item">
                            <div class="info-label">Availability</div>
                            <div class="info-value"><?php echo nl2br(htmlentities($doctor['availability_note'])); ?></div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Actions -->
                <div class="profile-card no-print">
                    <h4><i class="fa fa-cogs"></i> Actions</h4>
                    
                    <div class="d-grid gap-2">
                        <button onclick="window.print()" class="btn btn-print">
                            <i class="fa fa-print"></i> Print Profile
                        </button>
                        
                        <a href="doctor-directory.php" class="btn-back text-center">
                            <i class="fa fa-arrow-left"></i> Back to Directory
                        </a>
                        
                        <a href="../index.php" class="btn btn-outline-primary">
                            <i class="fa fa-home"></i> Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
