<?php
session_start();
error_reporting(0);
include('include/config.php');
include('include/checklogin.php');
check_login();

$patient_id = $_SESSION['id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Prescriptions | Patient Portal</title>
    
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
        .prescription-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .prescription-header {
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .status-active { 
            color: #28a745; 
            font-weight: bold; 
            background-color: #d4edda;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
        }
        .status-completed { 
            color: #6c757d; 
            background-color: #f8f9fa;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
        }
        .status-discontinued { 
            color: #dc3545; 
            background-color: #f8d7da;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
        }
        .status-on-hold { 
            color: #856404; 
            background-color: #fff3cd;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
        }
        .doctor-info {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .filter-tabs {
            margin-bottom: 20px;
        }
        .filter-tabs .nav-tabs > li.active > a {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }
        .medication-icon {
            font-size: 24px;
            color: #007bff;
            margin-right: 10px;
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
                                <h1 class="mainTitle"><i class="fa fa-pills"></i> My Prescriptions</h1>
                            </div>
                            <ol class="breadcrumb">
                                <li><span>Patient</span></li>
                                <li><span>EHR</span></li>
                                <li class="active"><span>Prescriptions</span></li>
                            </ol>
                        </div>
                    </section>

                    <div class="container-fluid container-fullw bg-white">
                        <!-- FILTER TABS -->
                        <div class="filter-tabs">
                            <ul class="nav nav-tabs" role="tablist">
                                <li role="presentation" class="active">
                                    <a href="#all-prescriptions" aria-controls="all-prescriptions" role="tab" data-toggle="tab">
                                        <i class="fa fa-list"></i> All Prescriptions
                                    </a>
                                </li>
                                <li role="presentation">
                                    <a href="#active-prescriptions" aria-controls="active-prescriptions" role="tab" data-toggle="tab">
                                        <i class="fa fa-check-circle"></i> Active
                                    </a>
                                </li>
                                <li role="presentation">
                                    <a href="#completed-prescriptions" aria-controls="completed-prescriptions" role="tab" data-toggle="tab">
                                        <i class="fa fa-archive"></i> Completed
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <!-- ALL PRESCRIPTIONS -->
                                <div role="tabpanel" class="tab-pane active" id="all-prescriptions">
                                    <div style="margin-top: 20px;">
                                        <?php
                                        $all_prescriptions = mysqli_query($con, "
                                            SELECT p.*, d.doctorName, d.specilization 
                                            FROM prescriptions p 
                                            LEFT JOIN doctors d ON p.doctor_id = d.id 
                                            WHERE p.patient_id='$patient_id' 
                                            ORDER BY p.prescribed_date DESC
                                        ");
                                        
                                        if(mysqli_num_rows($all_prescriptions) > 0) {
                                            while($prescription = mysqli_fetch_array($all_prescriptions)) {
                                                $status_class = 'status-' . strtolower(str_replace(' ', '-', $prescription['status']));
                                        ?>
                                        <div class="prescription-card">
                                            <div class="prescription-header">
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <h5>
                                                            <i class="fa fa-pill medication-icon"></i>
                                                            <?php echo htmlentities($prescription['medication_name']); ?>
                                                        </h5>
                                                        <div class="doctor-info">
                                                            <strong>Prescribed by:</strong> 
                                                            <?php echo $prescription['doctorName'] ? 'Dr. ' . htmlentities($prescription['doctorName']) : 'Unknown Doctor'; ?>
                                                            <?php if($prescription['specilization']) { ?>
                                                            <br><small class="text-muted"><?php echo htmlentities($prescription['specilization']); ?></small>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 text-right">
                                                        <span class="<?php echo $status_class; ?>">
                                                            <?php echo $prescription['status']; ?>
                                                        </span>
                                                        <br><small class="text-muted">Prescribed: <?php echo date('M d, Y', strtotime($prescription['prescribed_date'])); ?></small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p><strong><i class="fa fa-tint"></i> Dosage:</strong> <?php echo htmlentities($prescription['dosage']); ?></p>
                                                    <p><strong><i class="fa fa-clock-o"></i> Frequency:</strong> <?php echo htmlentities($prescription['frequency']); ?></p>
                                                    <p><strong><i class="fa fa-calendar"></i> Duration:</strong> <?php echo htmlentities($prescription['duration']); ?></p>
                                                    <?php if($prescription['instructions']) { ?>
                                                    <p><strong><i class="fa fa-info-circle"></i> Instructions:</strong> <?php echo htmlentities($prescription['instructions']); ?></p>
                                                    <?php } ?>
                                                </div>
                                                <div class="col-md-6">
                                                    <?php if($prescription['start_date']) { ?>
                                                    <p><strong>Start Date:</strong> <?php echo date('M d, Y', strtotime($prescription['start_date'])); ?></p>
                                                    <?php } ?>
                                                    <?php if($prescription['end_date']) { ?>
                                                    <p><strong>End Date:</strong> <?php echo date('M d, Y', strtotime($prescription['end_date'])); ?></p>
                                                    <?php } ?>
                                                    <?php if($prescription['refills_remaining'] > 0) { ?>
                                                    <p><strong><i class="fa fa-refresh"></i> Refills Remaining:</strong> 
                                                        <span class="label label-info"><?php echo $prescription['refills_remaining']; ?></span>
                                                    </p>
                                                    <?php } ?>
                                                    <?php if($prescription['pharmacy_notes']) { ?>
                                                    <p><strong>Pharmacy Notes:</strong> <?php echo htmlentities($prescription['pharmacy_notes']); ?></p>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php 
                                            }
                                        } else {
                                            echo '<div class="alert alert-info"><i class="fa fa-info-circle"></i> No prescriptions found. Visit your doctor to get prescriptions.</div>';
                                        }
                                        ?>
                                    </div>
                                </div>

                                <!-- ACTIVE PRESCRIPTIONS -->
                                <div role="tabpanel" class="tab-pane" id="active-prescriptions">
                                    <div style="margin-top: 20px;">
                                        <?php
                                        $active_prescriptions = mysqli_query($con, "
                                            SELECT p.*, d.doctorName, d.specilization 
                                            FROM prescriptions p 
                                            LEFT JOIN doctors d ON p.doctor_id = d.id 
                                            WHERE p.patient_id='$patient_id' AND p.status='Active' 
                                            ORDER BY p.prescribed_date DESC
                                        ");
                                        
                                        if(mysqli_num_rows($active_prescriptions) > 0) {
                                            echo '<div class="alert alert-success"><i class="fa fa-check-circle"></i> <strong>Important:</strong> Please take these medications as prescribed by your doctor.</div>';
                                            
                                            while($prescription = mysqli_fetch_array($active_prescriptions)) {
                                        ?>
                                        <div class="prescription-card" style="border-left: 4px solid #28a745;">
                                            <div class="prescription-header">
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <h5>
                                                            <i class="fa fa-pill medication-icon"></i>
                                                            <?php echo htmlentities($prescription['medication_name']); ?>
                                                        </h5>
                                                        <div class="doctor-info">
                                                            <strong>Prescribed by:</strong> 
                                                            <?php echo $prescription['doctorName'] ? 'Dr. ' . htmlentities($prescription['doctorName']) : 'Unknown Doctor'; ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 text-right">
                                                        <span class="status-active">ACTIVE</span>
                                                        <br><small class="text-muted">Since: <?php echo date('M d, Y', strtotime($prescription['prescribed_date'])); ?></small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p><strong><i class="fa fa-tint"></i> Dosage:</strong> <?php echo htmlentities($prescription['dosage']); ?></p>
                                                    <p><strong><i class="fa fa-clock-o"></i> Frequency:</strong> <?php echo htmlentities($prescription['frequency']); ?></p>
                                                    <p><strong><i class="fa fa-calendar"></i> Duration:</strong> <?php echo htmlentities($prescription['duration']); ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <?php if($prescription['instructions']) { ?>
                                                    <div class="alert alert-warning" style="padding: 8px;">
                                                        <strong><i class="fa fa-exclamation-triangle"></i> Instructions:</strong><br>
                                                        <?php echo htmlentities($prescription['instructions']); ?>
                                                    </div>
                                                    <?php } ?>
                                                    <?php if($prescription['refills_remaining'] > 0) { ?>
                                                    <p><strong><i class="fa fa-refresh"></i> Refills Available:</strong> 
                                                        <span class="label label-success"><?php echo $prescription['refills_remaining']; ?></span>
                                                    </p>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php 
                                            }
                                        } else {
                                            echo '<div class="alert alert-info"><i class="fa fa-info-circle"></i> No active prescriptions found.</div>';
                                        }
                                        ?>
                                    </div>
                                </div>

                                <!-- COMPLETED PRESCRIPTIONS -->
                                <div role="tabpanel" class="tab-pane" id="completed-prescriptions">
                                    <div style="margin-top: 20px;">
                                        <?php
                                        $completed_prescriptions = mysqli_query($con, "
                                            SELECT p.*, d.doctorName, d.specilization 
                                            FROM prescriptions p 
                                            LEFT JOIN doctors d ON p.doctor_id = d.id 
                                            WHERE p.patient_id='$patient_id' AND p.status IN ('Completed', 'Discontinued') 
                                            ORDER BY p.prescribed_date DESC
                                        ");
                                        
                                        if(mysqli_num_rows($completed_prescriptions) > 0) {
                                            while($prescription = mysqli_fetch_array($completed_prescriptions)) {
                                                $status_class = 'status-' . strtolower(str_replace(' ', '-', $prescription['status']));
                                        ?>
                                        <div class="prescription-card" style="opacity: 0.8;">
                                            <div class="prescription-header">
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <h5>
                                                            <i class="fa fa-pill medication-icon" style="color: #6c757d;"></i>
                                                            <?php echo htmlentities($prescription['medication_name']); ?>
                                                        </h5>
                                                        <div class="doctor-info">
                                                            <strong>Prescribed by:</strong> 
                                                            <?php echo $prescription['doctorName'] ? 'Dr. ' . htmlentities($prescription['doctorName']) : 'Unknown Doctor'; ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 text-right">
                                                        <span class="<?php echo $status_class; ?>">
                                                            <?php echo $prescription['status']; ?>
                                                        </span>
                                                        <br><small class="text-muted">Prescribed: <?php echo date('M d, Y', strtotime($prescription['prescribed_date'])); ?></small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <p><strong>Dosage:</strong> <?php echo htmlentities($prescription['dosage']); ?> - 
                                                       <strong>Frequency:</strong> <?php echo htmlentities($prescription['frequency']); ?> - 
                                                       <strong>Duration:</strong> <?php echo htmlentities($prescription['duration']); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                        <?php 
                                            }
                                        } else {
                                            echo '<div class="alert alert-info"><i class="fa fa-info-circle"></i> No completed prescriptions found.</div>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PRESCRIPTION SUMMARY -->
                        <div class="row" style="margin-top: 30px;">
                            <div class="col-md-12">
                                <div class="panel panel-white">
                                    <div class="panel-heading">
                                        <h5 class="panel-title"><i class="fa fa-chart-pie"></i> Prescription Summary</h5>
                                    </div>
                                    <div class="panel-body">
                                        <?php
                                        $summary_query = mysqli_query($con, "
                                            SELECT 
                                                status,
                                                COUNT(*) as count 
                                            FROM prescriptions 
                                            WHERE patient_id = '$patient_id' 
                                            GROUP BY status
                                        ");
                                        
                                        $total = 0;
                                        $summary = [];
                                        while($stat = mysqli_fetch_array($summary_query)) {
                                            $summary[$stat['status']] = $stat['count'];
                                            $total += $stat['count'];
                                        }
                                        ?>
                                        <div class="row text-center">
                                            <div class="col-md-3">
                                                <h4 class="text-primary"><?php echo $total; ?></h4>
                                                <p>Total Prescriptions</p>
                                            </div>
                                            <div class="col-md-3">
                                                <h4 class="text-success"><?php echo isset($summary['Active']) ? $summary['Active'] : 0; ?></h4>
                                                <p>Active</p>
                                            </div>
                                            <div class="col-md-3">
                                                <h4 class="text-secondary"><?php echo isset($summary['Completed']) ? $summary['Completed'] : 0; ?></h4>
                                                <p>Completed</p>
                                            </div>
                                            <div class="col-md-3">
                                                <h4 class="text-danger"><?php echo isset($summary['Discontinued']) ? $summary['Discontinued'] : 0; ?></h4>
                                                <p>Discontinued</p>
                                            </div>
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
        jQuery(document).ready(function() {
            Main.init();
            FormElements.init();
        });
    </script>
</body>
</html>
