<?php
session_start();
error_reporting(0);
include('include/config.php');

if(strlen($_SESSION['id']) == 0) {
    header('location:logout.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Doctor | View Extended Profile</title>
    
    <link href="http://fonts.googleapis.com/css?family=Lato:300,400,400italic,600,700|Raleway:300,400,500,600,700|Crete+Round:400italic" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="vendor/themify-icons/themify-icons.min.css">
    <link href="vendor/animate.css/animate.min.css" rel="stylesheet" media="screen">
    <link href="vendor/perfect-scrollbar/perfect-scrollbar.min.css" rel="stylesheet" media="screen">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/plugins.css">
    <link rel="stylesheet" href="assets/css/themes/theme-1.css" id="skin_color" />
    
    <style>
        .profile-header {
            background: linear-gradient(135deg,rgb(178, 189, 237) 0%,rgb(188, 171, 206) 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 5px solid white;
            object-fit: cover;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .profile-section {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solidrgb(160, 175, 240);
        }
        .profile-section h3 {
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f8f9fa;
        }
        .info-item {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .info-label {
            font-weight: 600;
            color: #495057;
            display: inline-block;
            min-width: 150px;
        }
        .info-value {
            color: #6c757d;
        }
        .tag {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            margin: 3px;
        }
        .tag.certification {
            background-color: #28a745;
        }
        .tag.award {
            background-color: #ffc107;
            color: #333;
        }
        .tag.language {
            background-color: #17a2b8;
        }
        .tag.membership {
            background-color:rgb(168, 150, 202);
        }
        .rating-stars {
            color: #ffc107;
            margin-left: 10px;
        }
        .experience-badge {
            background-color: #28a745;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: bold;
            display: inline-block;
            margin-left: 15px;
        }
        .bio-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #17a2b8;
            font-style: italic;
            line-height: 1.6;
        }
        .action-buttons {
            text-align: center;
            margin-top: 30px;
        }
        .print-btn {
            position: fixed;
            top: 100px;
            right: 20px;
            z-index: 1000;
        }
        @media print {
            .print-btn, .action-buttons { display: none; }
            .profile-section { break-inside: avoid; }
        }
    </style>
</head>

<body>
    <div id="app">        
        <?php include('include/sidebar.php');?>
        <div class="app-content">
            <?php include('include/header.php');?>
            <div class="main-content">
                <div class="wrap-content container" id="container">
                    <!-- PAGE TITLE -->
                    <section id="page-title">
                        <div class="row">
                            <div class="col-sm-8">
                                <h1 class="mainTitle">Doctor | Extended Profile View</h1>
                            </div>
                            <ol class="breadcrumb">
                                <li><span>Doctor</span></li>
                                <li class="active"><span>Profile View</span></li>
                            </ol>
                        </div>
                    </section>

                    <!-- Print Button -->
                    <button class="btn btn-info print-btn" onclick="window.print();">
                        <i class="fa fa-print"></i> Print Profile
                    </button>

                    <div class="container-fluid container-fullw" style="background-color: #f8f9fa; padding: 20px;">
                        <?php 
                        $did = $_SESSION['dlogin'];
                        $sql = mysqli_query($con, "SELECT * FROM doctors WHERE docEmail='$did'");
                        while($data = mysqli_fetch_array($sql)) {
                        ?>
                        
                        <!-- Profile Header -->
                        <div class="profile-header">
                            <div class="row">
                                <div class="col-md-3 text-center">
                                    <?php if($data['profile_picture'] && file_exists('uploads/profile_pictures/doctors/' . $data['profile_picture'])): ?>
                                        <img src="uploads/profile_pictures/doctors/<?php echo htmlentities($data['profile_picture']); ?>" 
                                             alt="Profile Picture" class="profile-picture">
                                    <?php else: ?>
                                        <div class="profile-picture" style="background-color: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center;">
                                            <i class="fa fa-user" style="font-size: 60px; color: white;"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-9">
                                    <h1 style="margin-bottom: 10px;">
                                        Dr. <?php echo htmlentities($data['doctorName']); ?>
                                        <?php if($data['years_of_experience']): ?>
                                            <span class="experience-badge">
                                                <?php echo $data['years_of_experience']; ?> Years Experience
                                            </span>
                                        <?php endif; ?>
                                    </h1>
                                    <h3 style="margin-bottom: 15px; opacity: 0.9;">
                                        <?php echo htmlentities($data['specilization']); ?>
                                    </h3>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><i class="fa fa-envelope"></i> <?php echo htmlentities($data['docEmail']); ?></p>
                                            <p><i class="fa fa-phone"></i> <?php echo htmlentities($data['contactno']); ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <?php if($data['docFees']): ?>
                                                <p><i class=""></i> Consultation Fee: <?php echo htmlentities($data['docFees']); ?></p>
                                            <?php endif; ?>
                                            <?php if($data['consultation_fee_range']): ?>
                                                <p><i class="fa fa-money"></i> Fee Range: <?php echo htmlentities($data['consultation_fee_range']); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <!-- Professional Biography -->
                                <?php if($data['bio']): ?>
                                <div class="profile-section">
                                    <h3><i class="fa fa-user-md"></i> Professional Biography</h3>
                                    <div class="bio-section">
                                        <?php echo nl2br(htmlentities($data['bio'])); ?>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <!-- Specialization Details -->
                                <?php if($data['specialization_details']): ?>
                                <div class="profile-section">
                                    <h3><i class="fa fa-stethoscope"></i> Specialization Details</h3>
                                    <p><?php echo nl2br(htmlentities($data['specialization_details'])); ?></p>
                                </div>
                                <?php endif; ?>

                                <!-- Education & Training -->
                                <div class="profile-section">
                                    <h3><i class="fa fa-graduation-cap"></i> Education & Training</h3>
                                    
                                    <?php if($data['education']): ?>
                                    <div class="info-item">
                                        <span class="info-label">Educational Background:</span>
                                        <div class="info-value"><?php echo nl2br(htmlentities($data['education'])); ?></div>
                                    </div>
                                    <?php endif; ?>

                                    <?php if($data['medical_school']): ?>
                                    <div class="info-item">
                                        <span class="info-label">Medical School:</span>
                                        <span class="info-value"><?php echo htmlentities($data['medical_school']); ?></span>
                                    </div>
                                    <?php endif; ?>

                                    <?php if($data['residency']): ?>
                                    <div class="info-item">
                                        <span class="info-label">Residency:</span>
                                        <span class="info-value"><?php echo htmlentities($data['residency']); ?></span>
                                    </div>
                                    <?php endif; ?>

                                    <?php if($data['fellowship']): ?>
                                    <div class="info-item">
                                        <span class="info-label">Fellowship:</span>
                                        <span class="info-value"><?php echo htmlentities($data['fellowship']); ?></span>
                                    </div>
                                    <?php endif; ?>

                                    <?php 
                                    $degrees = json_decode($data['degrees'] ?? '[]', true);
                                    if(is_array($degrees) && !empty($degrees)): 
                                    ?>
                                    <div class="info-item">
                                        <span class="info-label">Degrees:</span>
                                        <div class="info-value">
                                            <?php foreach($degrees as $degree): ?>
                                                <span class="tag"><?php echo htmlentities($degree); ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Research & Publications -->
                                <?php if($data['research_interests']): ?>
                                <div class="profile-section">
                                    <h3><i class="fa fa-flask"></i> Research & Publications</h3>
                                    <p><?php echo nl2br(htmlentities($data['research_interests'])); ?></p>
                                </div>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-4">
                                <!-- Quick Info -->
                                <div class="profile-section">
                                    <h3><i class="fa fa-info-circle"></i> Quick Information</h3>
                                    
                                    <?php if($data['address']): ?>
                                    <div class="info-item">
                                        <span class="info-label">Clinic Address:</span>
                                        <div class="info-value"><?php echo nl2br(htmlentities($data['address'])); ?></div>
                                    </div>
                                    <?php endif; ?>

                                    <div class="info-item">
                                        <span class="info-label">Member Since:</span>
                                        <span class="info-value"><?php echo date('F Y', strtotime($data['creationDate'])); ?></span>
                                    </div>

                                    <?php if($data['updationDate']): ?>
                                    <div class="info-item">
                                        <span class="info-label">Last Updated:</span>
                                        <span class="info-value"><?php echo date('M d, Y', strtotime($data['updationDate'])); ?></span>
                                    </div>
                                    <?php endif; ?>

                                    <?php if($data['availability_note']): ?>
                                    <div class="info-item">
                                        <span class="info-label">Availability:</span>
                                        <div class="info-value"><?php echo nl2br(htmlentities($data['availability_note'])); ?></div>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Languages -->
                                <?php 
                                $languages = json_decode($data['languages_spoken'] ?? '[]', true);
                                if(is_array($languages) && !empty($languages)): 
                                ?>
                                <div class="profile-section">
                                    <h3><i class="fa fa-globe"></i> Languages Spoken</h3>
                                    <div>
                                        <?php foreach($languages as $language): ?>
                                            <span class="tag language"><?php echo htmlentities($language); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <!-- Certifications -->
                                <?php 
                                $certifications = json_decode($data['certifications'] ?? '[]', true);
                                if(is_array($certifications) && !empty($certifications)): 
                                ?>
                                <div class="profile-section">
                                    <h3><i class="fa fa-certificate"></i> Certifications</h3>
                                    <div>
                                        <?php foreach($certifications as $cert): ?>
                                            <span class="tag certification"><?php echo htmlentities($cert); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <!-- Board Certifications -->
                                <?php 
                                $board_certs = json_decode($data['board_certifications'] ?? '[]', true);
                                if(is_array($board_certs) && !empty($board_certs)): 
                                ?>
                                <div class="profile-section">
                                    <h3><i class="fa fa-shield"></i> Board Certifications</h3>
                                    <div>
                                        <?php foreach($board_certs as $cert): ?>
                                            <span class="tag certification"><?php echo htmlentities($cert); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <!-- Professional Memberships -->
                                <?php 
                                $memberships = json_decode($data['professional_memberships'] ?? '[]', true);
                                if(is_array($memberships) && !empty($memberships)): 
                                ?>
                                <div class="profile-section">
                                    <h3><i class="fa fa-users"></i> Professional Memberships</h3>
                                    <div>
                                        <?php foreach($memberships as $membership): ?>
                                            <span class="tag membership"><?php echo htmlentities($membership); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <!-- Awards & Honors -->
                                <?php 
                                $awards = json_decode($data['awards_honors'] ?? '[]', true);
                                if(is_array($awards) && !empty($awards)): 
                                ?>
                                <div class="profile-section">
                                    <h3><i class="fa fa-trophy"></i> Awards & Honors</h3>
                                    <div>
                                        <?php foreach($awards as $award): ?>
                                            <span class="tag award"><?php echo htmlentities($award); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <a href="edit-profile.php" class="btn btn-primary btn-lg">
                                <i class="fa fa-edit"></i> Edit Profile
                            </a>
                            <a href="dashboard.php" class="btn btn-default btn-lg">
                                <i class="fa fa-dashboard"></i> Dashboard
                            </a>
                            <button onclick="window.print();" class="btn btn-info btn-lg">
                                <i class="fa fa-print"></i> Print Profile
                            </button>
                        </div>

                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        
        <?php include('include/footer.php');?>
        <?php include('include/setting.php');?>
    </div>

    <!-- JAVASCRIPTS -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="vendor/modernizr/modernizr.js"></script>
    <script src="vendor/jquery-cookie/jquery.cookie.js"></script>
    <script src="vendor/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="assets/js/main.js"></script>
    
    <script>
        jQuery(document).ready(function() {
            Main.init();
            
            // Add smooth scrolling for better UX
            $('a[href^="#"]').on('click', function(event) {
                var target = $(this.getAttribute('href'));
                if( target.length ) {
                    event.preventDefault();
                    $('html, body').stop().animate({
                        scrollTop: target.offset().top - 100
                    }, 1000);
                }
            });
        });
    </script>
</body>
</html>
