<?php
session_start();
error_reporting(0);
include('include/config.php');
if(strlen($_SESSION['id'])==0) {
    header('location:logout.php');
    exit();
}

$doctor_id = $_SESSION['id'];
$patient_id = isset($_GET['patient_id']) ? intval($_GET['patient_id']) : 0;

if(!$patient_id) {
    header('location:manage-patient.php');
    exit();
}

// Get patient information and verify doctor has access
$patient_query = mysqli_query($con, "
    SELECT u.*, COUNT(mc.id) as consultation_count
    FROM users u
    LEFT JOIN medical_consultations mc ON u.id = mc.patient_id AND mc.doctor_id = '$doctor_id'
    WHERE u.id = '$patient_id'
    GROUP BY u.id
");

$patient = mysqli_fetch_array($patient_query);

if(!$patient || $patient['consultation_count'] == 0) {
    echo "<script>alert('You do not have access to this patient\\'s records'); window.location.href='manage-patient.php';</script>";
    exit();
}

// Get summary statistics
$stats = [];

$allergies_count = mysqli_query($con, "SELECT COUNT(*) as count FROM patient_allergies WHERE patient_id='$patient_id' AND is_active=1");
$stats['allergies'] = mysqli_fetch_array($allergies_count)['count'];

$conditions_count = mysqli_query($con, "SELECT COUNT(*) as count FROM patient_conditions WHERE patient_id='$patient_id' AND status='Active'");
$stats['conditions'] = mysqli_fetch_array($conditions_count)['count'];

$prescriptions_count = mysqli_query($con, "SELECT COUNT(*) as count FROM prescriptions WHERE patient_id='$patient_id'");
$stats['prescriptions'] = mysqli_fetch_array($prescriptions_count)['count'];

$consultations_count = mysqli_query($con, "SELECT COUNT(*) as count FROM medical_consultations WHERE patient_id='$patient_id' AND doctor_id='$doctor_id'");
$stats['consultations'] = mysqli_fetch_array($consultations_count)['count'];

$vitals_count = mysqli_query($con, "SELECT COUNT(*) as count FROM vital_signs WHERE patient_id='$patient_id'");
$stats['vitals'] = mysqli_fetch_array($vitals_count)['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Export Patient Medical History</title>
    
    <link href="http://fonts.googleapis.com/css?family=Lato:300,400,400italic,600,700|Raleway:300,400,500,600,700|Crete+Round:400italic" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="vendor/themify-icons/themify-icons.min.css">
    <link href="vendor/animate.css/animate.min.css" rel="stylesheet" media="screen">
    <link href="vendor/perfect-scrollbar/perfect-scrollbar.min.css" rel="stylesheet" media="screen">
    <link href="vendor/switchery/switchery.min.css" rel="stylesheet" media="screen">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/plugins.css">
    <link rel="stylesheet" href="assets/css/themes/theme-1.css" id="skin_color" />
    
    <style>
        .export-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .format-option {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }
        .format-option:hover {
            border-color: #007bff;
            background-color: #f8f9fa;
        }
        .format-option.selected {
            border-color: #007bff;
            background-color: #e3f2fd;
        }
        .format-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .stat-item {
            text-align: center;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #007bff;
        }
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }
        .stat-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
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
                                <h1 class="mainTitle"><i class="fa fa-download"></i> Export Medical History</h1>
                            </div>
                            <ol class="breadcrumb">
                                <li><span>Doctor</span></li>
                                <li><a href="manage-patient.php">Patients</a></li>
                                <li class="active"><span>Export History</span></li>
                            </ol>
                        </div>
                    </section>

                    <div class="container-fluid container-fullw bg-white">
                        <!-- PATIENT INFO -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="export-card">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h4><i class="fa fa-user"></i> <?php echo htmlentities($patient['fullname']); ?></h4>
                                            <p><strong>Email:</strong> <?php echo htmlentities($patient['email']); ?></p>
                                            <p><strong>Gender:</strong> <?php echo htmlentities($patient['gender']); ?></p>
                                            <p><strong>Address:</strong> <?php echo htmlentities($patient['address'] . ', ' . $patient['city']); ?></p>
                                            <p><strong>Registration Date:</strong> <?php echo date('F d, Y', strtotime($patient['regDate'])); ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <h5><i class="fa fa-bar-chart"></i> Medical Data Summary</h5>
                                            <div class="stats-grid">
                                                <div class="stat-item">
                                                    <div class="stat-number"><?php echo $stats['consultations']; ?></div>
                                                    <div class="stat-label">Consultations</div>
                                                </div>
                                                <div class="stat-item">
                                                    <div class="stat-number"><?php echo $stats['prescriptions']; ?></div>
                                                    <div class="stat-label">Prescriptions</div>
                                                </div>
                                                <div class="stat-item">
                                                    <div class="stat-number"><?php echo $stats['allergies']; ?></div>
                                                    <div class="stat-label">Allergies</div>
                                                </div>
                                                <div class="stat-item">
                                                    <div class="stat-number"><?php echo $stats['conditions']; ?></div>
                                                    <div class="stat-label">Conditions</div>
                                                </div>
                                                <div class="stat-item">
                                                    <div class="stat-number"><?php echo $stats['vitals']; ?></div>
                                                    <div class="stat-label">Vital Records</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- EXPORT FORMAT SELECTION -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-white">
                                    <div class="panel-heading">
                                        <h5 class="panel-title"><i class="fa fa-file-export"></i> Choose Export Format</h5>
                                    </div>
                                    <div class="panel-body">
                                        <form id="exportForm">
                                            <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
                                            <input type="hidden" name="format" id="selectedFormat" value="">
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="format-option" data-format="pdf" onclick="selectFormat('pdf')">
                                                        <div class="format-icon">
                                                            <i class="fa fa-file-pdf-o text-danger"></i>
                                                        </div>
                                                        <h4>PDF Report</h4>
                                                        <p class="text-muted">
                                                            Professional medical report suitable for printing and sharing with other healthcare providers.
                                                            Includes formatted layout with patient information, medical history, and consultation notes.
                                                        </p>
                                                        <div class="features">
                                                            <small><i class="fa fa-check text-success"></i> Professional formatting</small><br>
                                                            <small><i class="fa fa-check text-success"></i> Print-ready layout</small><br>
                                                            <small><i class="fa fa-check text-success"></i> Comprehensive summary</small><br>
                                                            <small><i class="fa fa-check text-success"></i> Secure and portable</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <div class="format-option" data-format="json" onclick="selectFormat('json')">
                                                        <div class="format-icon">
                                                            <i class="fa fa-code text-primary"></i>
                                                        </div>
                                                        <h4>JSON Data</h4>
                                                        <p class="text-muted">
                                                            Structured data format suitable for integration with other systems, 
                                                            data analysis, or backup purposes. Contains all medical data in machine-readable format.
                                                        </p>
                                                        <div class="features">
                                                            <small><i class="fa fa-check text-success"></i> Machine-readable</small><br>
                                                            <small><i class="fa fa-check text-success"></i> Complete data export</small><br>
                                                            <small><i class="fa fa-check text-success"></i> System integration ready</small><br>
                                                            <small><i class="fa fa-check text-success"></i> Structured format</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="text-center" style="margin-top: 30px;">
                                                <button type="button" id="exportBtn" class="btn btn-success btn-lg" disabled onclick="exportData()">
                                                    <i class="fa fa-download"></i> Export Medical History
                                                </button>
                                                <a href="manage-patient.php" class="btn btn-default btn-lg">
                                                    <i class="fa fa-arrow-left"></i> Back to Patients
                                                </a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- EXPORT INFORMATION -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-info">
                                    <div class="panel-heading">
                                        <h5 class="panel-title"><i class="fa fa-info-circle"></i> Export Information</h5>
                                    </div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6><i class="fa fa-shield"></i> Data Privacy & Security</h6>
                                                <ul>
                                                    <li>Exported data contains sensitive medical information</li>
                                                    <li>Ensure secure storage and transmission</li>
                                                    <li>Follow HIPAA and local privacy regulations</li>
                                                    <li>Delete exported files after use when appropriate</li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <h6><i class="fa fa-file"></i> Export Content</h6>
                                                <ul>
                                                    <li>Patient demographics and contact information</li>
                                                    <li>Medical consultations and diagnosis history</li>
                                                    <li>Current and past prescriptions</li>
                                                    <li>Known allergies and medical conditions</li>
                                                    <li>Vital signs and lab results (if available)</li>
                                                </ul>
                                            </div>
                                        </div>
                                        
                                        <div class="alert alert-warning" style="margin-top: 15px;">
                                            <i class="fa fa-exclamation-triangle"></i> 
                                            <strong>Important:</strong> This export contains confidential medical information. 
                                            Handle according to your institution's privacy policies and applicable healthcare regulations.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- FOOTER -->
        <?php include('include/footer.php');?>
        
        <!-- SETTINGS -->
        <?php include('include/setting.php');?>
    </div>

    <!-- JAVASCRIPTS -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="vendor/modernizr/modernizr.js"></script>
    <script src="vendor/jquery-cookie/jquery.cookie.js"></script>
    <script src="vendor/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="vendor/switchery/switchery.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/form-elements.js"></script>
    <script>
        function selectFormat(format) {
            // Remove selected class from all options
            $('.format-option').removeClass('selected');
            
            // Add selected class to clicked option
            $('.format-option[data-format="' + format + '"]').addClass('selected');
            
            // Set the format value
            $('#selectedFormat').val(format);
            
            // Enable export button
            $('#exportBtn').prop('disabled', false);
        }
        
        function exportData() {
            var format = $('#selectedFormat').val();
            var patientId = <?php echo $patient_id; ?>;
            
            if(!format) {
                alert('Please select an export format.');
                return;
            }
            
            // Show loading state
            $('#exportBtn').html('<i class="fa fa-spinner fa-spin"></i> Generating Export...').prop('disabled', true);
            
            // Create export URL
            var exportUrl = '../export-medical-history.php?patient_id=' + patientId + '&format=' + format;
            
            // For PDF, open in new window; for JSON, trigger download
            if(format === 'pdf') {
                window.open(exportUrl, '_self');
            } else {
                window.location.href = exportUrl;
            }
            
            // Reset button after a delay
            setTimeout(function() {
                $('#exportBtn').html('<i class="fa fa-download"></i> Export Medical History').prop('disabled', false);
            }, 2000);
        }
        
        jQuery(document).ready(function() {
            Main.init();
            FormElements.init();
        });
    </script>
</body>
</html>
