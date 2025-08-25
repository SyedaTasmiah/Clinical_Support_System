<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['id'])==0) {
    header('location:index.php');
    exit();
}

// Handle bulk export
if(isset($_POST['bulk_export'])) {
    $selected_patients = isset($_POST['selected_patients']) ? $_POST['selected_patients'] : [];
    $export_format = isset($_POST['export_format']) ? $_POST['export_format'] : 'pdf';
    
    if(empty($selected_patients)) {
        $_SESSION['msg'] = "Please select at least one patient to export.";
    } else {
        // Redirect to bulk export handler
        $patient_ids = implode(',', $selected_patients);
        header("location:../bulk-export-medical-history.php?patient_ids=$patient_ids&format=$export_format");
        exit();
    }
}

// Get all patients for selection
$patients_query = mysqli_query($con, "SELECT id, fullName, email, gender, city, regDate FROM users ORDER BY fullName");
$total_patients = mysqli_num_rows($patients_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin | Bulk Export Patient EHR</title>
    
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
        .patient-checkbox {
            margin: 0;
        }
        .export-options {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .patient-row:hover {
            background-color: #f8f9fa;
        }
        .select-all-section {
            background-color: #e3f2fd;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
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
                                <h1 class="mainTitle"><i class="fa fa-download"></i> Bulk Export Patient EHR</h1>
                            </div>
                            <ol class="breadcrumb">
                                <li><span>Admin</span></li>
                                <li><a href="manage-users.php">Manage Users</a></li>
                                <li class="active"><span>Bulk Export EHR</span></li>
                            </ol>
                        </div>
                    </section>

                    <div class="container-fluid container-fullw bg-white">
                        <!-- ADMIN NOTICE -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <i class="fa fa-shield"></i> 
                                    <strong>Bulk Export:</strong> Select multiple patients to export their complete medical records. 
                                    This feature is available for administrative purposes only.
                                </div>
                            </div>
                        </div>

                        <!-- EXPORT OPTIONS -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="export-options">
                                    <form method="post" id="bulkExportForm">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h5><i class="fa fa-file-export"></i> Export Format</h5>
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="export_format" value="pdf" checked>
                                                        <i class="fa fa-file-pdf-o text-danger"></i> PDF Reports (Individual files)
                                                    </label>
                                                </div>
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="export_format" value="json">
                                                        <i class="fa fa-code text-primary"></i> JSON Data (Individual files)
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <h5><i class="fa fa-info-circle"></i> Export Information</h5>
                                                <ul class="list-unstyled">
                                                    <li><i class="fa fa-check text-success"></i> Complete medical history</li>
                                                    <li><i class="fa fa-check text-success"></i> All consultation records</li>
                                                    <li><i class="fa fa-check text-success"></i> Prescriptions and medications</li>
                                                    <li><i class="fa fa-check text-success"></i> Allergies and conditions</li>
                                                    <li><i class="fa fa-check text-success"></i> Vital signs and lab results</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- PATIENT SELECTION -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-white">
                                    <div class="panel-heading">
                                        <h5 class="panel-title">
                                            <i class="fa fa-users"></i> Select Patients for Export 
                                            <span class="badge badge-primary"><?php echo $total_patients; ?> Total Patients</span>
                                        </h5>
                                    </div>
                                    <div class="panel-body">
                                        <!-- SELECT ALL SECTION -->
                                        <div class="select-all-section">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox" id="selectAll">
                                                            <strong>Select All Patients</strong>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 text-right">
                                                    <span id="selectedCount" class="badge badge-info">0 selected</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- PATIENT LIST -->
                                        <div class="table-responsive">
                                            <table class="table table-hover" id="patientsTable">
                                                <thead>
                                                    <tr>
                                                        <th width="50">
                                                            <input type="checkbox" id="headerCheckbox">
                                                        </th>
                                                        <th>Patient Name</th>
                                                        <th>Email</th>
                                                        <th>Gender</th>
                                                        <th>City</th>
                                                        <th>Registration Date</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    mysqli_data_seek($patients_query, 0);
                                                    while($patient = mysqli_fetch_array($patients_query)) { 
                                                    ?>
                                                    <tr class="patient-row">
                                                        <td>
                                                            <input type="checkbox" name="selected_patients[]" 
                                                                   value="<?php echo $patient['id']; ?>" 
                                                                   class="patient-checkbox">
                                                        </td>
                                                        <td>
                                                            <strong><?php echo htmlentities($patient['fullName']); ?></strong>
                                                        </td>
                                                        <td><?php echo htmlentities($patient['email']); ?></td>
                                                        <td><?php echo htmlentities($patient['gender']); ?></td>
                                                        <td><?php echo htmlentities($patient['city']); ?></td>
                                                        <td><?php echo date('M d, Y', strtotime($patient['regDate'])); ?></td>
                                                        <td>
                                                            <a href="export-patient-history.php?patient_id=<?php echo $patient['id']; ?>" 
                                                               class="btn btn-info btn-xs" title="Export Individual">
                                                                <i class="fa fa-download"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- EXPORT BUTTONS -->
                                        <div class="text-center" style="margin-top: 30px;">
                                            <button type="submit" form="bulkExportForm" name="bulk_export" 
                                                    class="btn btn-success btn-lg" id="bulkExportBtn" disabled>
                                                <i class="fa fa-download"></i> Export Selected Patients
                                            </button>
                                            <a href="manage-users.php" class="btn btn-default btn-lg">
                                                <i class="fa fa-arrow-left"></i> Back to User Management
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ADMINISTRATIVE GUIDELINES -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-warning">
                                    <div class="panel-heading">
                                        <h5 class="panel-title"><i class="fa fa-exclamation-triangle"></i> Administrative Guidelines</h5>
                                    </div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6><i class="fa fa-gavel"></i> Legal & Compliance</h6>
                                                <ul>
                                                    <li>Ensure administrative purpose justifies bulk access</li>
                                                    <li>Document the reason for bulk data export</li>
                                                    <li>Follow institutional data governance policies</li>
                                                    <li>Comply with HIPAA and local privacy laws</li>
                                                    <li>Maintain audit trail of bulk data access</li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <h6><i class="fa fa-lock"></i> Security Requirements</h6>
                                                <ul>
                                                    <li>Use secure channels for data transmission</li>
                                                    <li>Encrypt exported files when storing</li>
                                                    <li>Limit access to authorized personnel only</li>
                                                    <li>Securely dispose of files after use</li>
                                                    <li>Report any data breaches immediately</li>
                                                </ul>
                                            </div>
                                        </div>
                                        
                                        <div class="alert alert-danger" style="margin-top: 15px;">
                                            <i class="fa fa-shield"></i> 
                                            <strong>Bulk Export Responsibility:</strong> Bulk export of patient data requires careful consideration 
                                            of privacy and security implications. Ensure this action is necessary and authorized.
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
        $(document).ready(function() {
            // Select all functionality
            $('#selectAll').change(function() {
                $('.patient-checkbox').prop('checked', $(this).is(':checked'));
                updateSelectedCount();
                updateExportButton();
            });
            
            $('#headerCheckbox').change(function() {
                $('.patient-checkbox').prop('checked', $(this).is(':checked'));
                $('#selectAll').prop('checked', $(this).is(':checked'));
                updateSelectedCount();
                updateExportButton();
            });
            
            // Individual checkbox change
            $('.patient-checkbox').change(function() {
                updateSelectedCount();
                updateExportButton();
                updateHeaderCheckbox();
            });
            
            function updateSelectedCount() {
                var selected = $('.patient-checkbox:checked').length;
                $('#selectedCount').text(selected + ' selected');
            }
            
            function updateExportButton() {
                var selected = $('.patient-checkbox:checked').length;
                $('#bulkExportBtn').prop('disabled', selected === 0);
            }
            
            function updateHeaderCheckbox() {
                var total = $('.patient-checkbox').length;
                var selected = $('.patient-checkbox:checked').length;
                
                if(selected === 0) {
                    $('#headerCheckbox').prop('indeterminate', false).prop('checked', false);
                    $('#selectAll').prop('checked', false);
                } else if(selected === total) {
                    $('#headerCheckbox').prop('indeterminate', false).prop('checked', true);
                    $('#selectAll').prop('checked', true);
                } else {
                    $('#headerCheckbox').prop('indeterminate', true);
                    $('#selectAll').prop('checked', false);
                }
            }
            
            // Initialize
            updateSelectedCount();
            updateExportButton();
            
            Main.init();
            FormElements.init();
        });
    </script>
</body>
</html>
