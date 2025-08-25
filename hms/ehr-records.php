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
    <title>My Medical Records | EHR</title>
    
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
        .ehr-section {
            margin-bottom: 30px;
        }
        .allergy-item {
            border-left: 4px solid #d9534f;
            padding: 10px;
            margin-bottom: 10px;
            background-color: #f9f9f9;
        }
        .condition-item {
            border-left: 4px solid #f0ad4e;
            padding: 10px;
            margin-bottom: 10px;
            background-color: #f9f9f9;
        }
        .prescription-item {
            border-left: 4px solid #5cb85c;
            padding: 10px;
            margin-bottom: 10px;
            background-color: #f9f9f9;
        }
        .consultation-item {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .vital-signs-chart {
            height: 300px;
        }
        .severity-mild { color: #5cb85c; }
        .severity-moderate { color: #f0ad4e; }
        .severity-severe { color: #d9534f; }
        .severity-life-threatening { color: #d9534f; font-weight: bold; }
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
                                <h1 class="mainTitle"><i class="fa fa-heartbeat"></i> My Medical Records</h1>
                            </div>
                            <ol class="breadcrumb">
                                <li><span>Patient</span></li>
                                <li class="active"><span>Medical Records</span></li>
                            </ol>
                        </div>
                    </section>

                    <!-- BASIC PATIENT INFO -->
                    <div class="container-fluid container-fullw bg-white">
                        <?php
                        // Get patient basic info
                        $patient_query = mysqli_query($con, "SELECT * FROM users WHERE id='$patient_id'");
                        $patient = mysqli_fetch_array($patient_query);
                        
                        // Get patient medical record
                        $record_query = mysqli_query($con, "SELECT * FROM patient_medical_records WHERE patient_id='$patient_id'");
                        $medical_record = mysqli_fetch_array($record_query);
                        ?>
                        
                        <div class="row ehr-section">
                            <div class="col-md-12">
                                <div class="panel panel-white">
                                    <div class="panel-heading">
                                        <h5 class="panel-title"><i class="fa fa-user"></i> Patient Information</h5>
                                    </div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Name:</strong> <?php echo htmlentities($patient['fullname']); ?></p>
                                                <p><strong>Email:</strong> <?php echo htmlentities($patient['email']); ?></p>
                                                <p><strong>Address:</strong> <?php echo htmlentities($patient['address']); ?></p>
                                                <p><strong>City:</strong> <?php echo htmlentities($patient['city']); ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <?php if($medical_record) { ?>
                                                <p><strong>Blood Type:</strong> <?php echo htmlentities($medical_record['blood_type'] ?: 'Not recorded'); ?></p>
                                                <p><strong>Height:</strong> <?php echo $medical_record['height'] ? $medical_record['height'] . ' cm' : 'Not recorded'; ?></p>
                                                <p><strong>Weight:</strong> <?php echo $medical_record['weight'] ? $medical_record['weight'] . ' kg' : 'Not recorded'; ?></p>
                                                <p><strong>Emergency Contact:</strong> <?php echo htmlentities($medical_record['emergency_contact_name'] ?: 'Not provided'); ?></p>
                                                <?php } else { ?>
                                                <p class="text-muted">Complete medical record not available. Please contact your healthcare provider.</p>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ALLERGIES SECTION -->
                        <div class="row ehr-section">
                            <div class="col-md-12">
                                <div class="panel panel-white">
                                    <div class="panel-heading">
                                        <h5 class="panel-title"><i class="fa fa-exclamation-triangle text-danger"></i> Allergies & Adverse Reactions</h5>
                                    </div>
                                    <div class="panel-body">
                                        <?php
                                        $allergies_query = mysqli_query($con, "SELECT * FROM patient_allergies WHERE patient_id='$patient_id' AND is_active=1 ORDER BY severity DESC, allergy_name");
                                        if(mysqli_num_rows($allergies_query) > 0) {
                                            while($allergy = mysqli_fetch_array($allergies_query)) {
                                        ?>
                                        <div class="allergy-item">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <h6><strong><?php echo htmlentities($allergy['allergy_name']); ?></strong> 
                                                        <span class="label label-default"><?php echo $allergy['allergy_type']; ?></span>
                                                    </h6>
                                                    <p><?php echo htmlentities($allergy['reaction_description']); ?></p>
                                                </div>
                                                <div class="col-md-4 text-right">
                                                    <span class="severity-<?php echo strtolower(str_replace('-', '', $allergy['severity'])); ?>">
                                                        <strong><?php echo $allergy['severity']; ?></strong>
                                                    </span>
                                                    <?php if($allergy['diagnosed_date']) { ?>
                                                    <br><small class="text-muted">Since: <?php echo date('M Y', strtotime($allergy['diagnosed_date'])); ?></small>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php 
                                            }
                                        } else {
                                            echo '<p class="text-muted">No known allergies recorded.</p>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CONDITIONS SECTION -->
                        <div class="row ehr-section">
                            <div class="col-md-12">
                                <div class="panel panel-white">
                                    <div class="panel-heading">
                                        <h5 class="panel-title"><i class="fa fa-stethoscope"></i> Medical Conditions</h5>
                                    </div>
                                    <div class="panel-body">
                                        <?php
                                        $conditions_query = mysqli_query($con, "
                                            SELECT pc.*, d.doctorName 
                                            FROM patient_conditions pc 
                                            LEFT JOIN doctors d ON pc.diagnosed_by = d.id 
                                            WHERE pc.patient_id='$patient_id' AND pc.status='Active' 
                                            ORDER BY pc.diagnosed_date DESC
                                        ");
                                        if(mysqli_num_rows($conditions_query) > 0) {
                                            while($condition = mysqli_fetch_array($conditions_query)) {
                                        ?>
                                        <div class="condition-item">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <h6><strong><?php echo htmlentities($condition['condition_name']); ?></strong>
                                                        <span class="label label-warning"><?php echo $condition['condition_type']; ?></span>
                                                    </h6>
                                                    <?php if($condition['notes']) { ?>
                                                    <p><?php echo htmlentities($condition['notes']); ?></p>
                                                    <?php } ?>
                                                </div>
                                                <div class="col-md-4 text-right">
                                                    <?php if($condition['diagnosed_date']) { ?>
                                                    <p><strong>Diagnosed:</strong> <?php echo date('M d, Y', strtotime($condition['diagnosed_date'])); ?></p>
                                                    <?php } ?>
                                                    <?php if($condition['doctorName']) { ?>
                                                    <small class="text-muted">By: Dr. <?php echo htmlentities($condition['doctorName']); ?></small>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php 
                                            }
                                        } else {
                                            echo '<p class="text-muted">No active medical conditions recorded.</p>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CURRENT PRESCRIPTIONS -->
                        <div class="row ehr-section">
                            <div class="col-md-12">
                                <div class="panel panel-white">
                                    <div class="panel-heading">
                                        <h5 class="panel-title"><i class="fa fa-pills"></i> Current Prescriptions</h5>
                                    </div>
                                    <div class="panel-body">
                                        <?php
                                        $prescriptions_query = mysqli_query($con, "
                                            SELECT p.*, d.doctorName 
                                            FROM prescriptions p 
                                            LEFT JOIN doctors d ON p.doctor_id = d.id 
                                            WHERE p.patient_id='$patient_id' AND p.status='Active' 
                                            ORDER BY p.prescribed_date DESC
                                        ");
                                        if(mysqli_num_rows($prescriptions_query) > 0) {
                                            while($prescription = mysqli_fetch_array($prescriptions_query)) {
                                        ?>
                                        <div class="prescription-item">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <h6><strong><?php echo htmlentities($prescription['medication_name']); ?></strong></h6>
                                                    <p><strong>Dosage:</strong> <?php echo htmlentities($prescription['dosage']); ?> - 
                                                       <strong>Frequency:</strong> <?php echo htmlentities($prescription['frequency']); ?></p>
                                                    <p><strong>Duration:</strong> <?php echo htmlentities($prescription['duration']); ?></p>
                                                    <?php if($prescription['instructions']) { ?>
                                                    <p><strong>Instructions:</strong> <?php echo htmlentities($prescription['instructions']); ?></p>
                                                    <?php } ?>
                                                </div>
                                                <div class="col-md-4 text-right">
                                                    <p><strong>Prescribed:</strong> <?php echo date('M d, Y', strtotime($prescription['prescribed_date'])); ?></p>
                                                    <?php if($prescription['doctorName']) { ?>
                                                    <small class="text-muted">By: Dr. <?php echo htmlentities($prescription['doctorName']); ?></small>
                                                    <?php } ?>
                                                    <?php if($prescription['refills_remaining'] > 0) { ?>
                                                    <br><small class="text-info">Refills: <?php echo $prescription['refills_remaining']; ?></small>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php 
                                            }
                                        } else {
                                            echo '<p class="text-muted">No active prescriptions.</p>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- RECENT CONSULTATIONS -->
                        <div class="row ehr-section">
                            <div class="col-md-12">
                                <div class="panel panel-white">
                                    <div class="panel-heading">
                                        <h5 class="panel-title"><i class="fa fa-calendar"></i> Recent Consultations</h5>
                                    </div>
                                    <div class="panel-body">
                                        <?php
                                        $consultations_query = mysqli_query($con, "
                                            SELECT mc.*, d.doctorName, d.specilization 
                                            FROM medical_consultations mc 
                                            LEFT JOIN doctors d ON mc.doctor_id = d.id 
                                            WHERE mc.patient_id='$patient_id' 
                                            ORDER BY mc.consultation_date DESC 
                                            LIMIT 5
                                        ");
                                        if(mysqli_num_rows($consultations_query) > 0) {
                                            while($consultation = mysqli_fetch_array($consultations_query)) {
                                        ?>
                                        <div class="consultation-item">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <h6><strong><?php echo date('F d, Y', strtotime($consultation['consultation_date'])); ?></strong></h6>
                                                    <?php if($consultation['chief_complaint']) { ?>
                                                    <p><strong>Chief Complaint:</strong> <?php echo htmlentities($consultation['chief_complaint']); ?></p>
                                                    <?php } ?>
                                                    <?php if($consultation['diagnosis']) { ?>
                                                    <p><strong>Diagnosis:</strong> <?php echo htmlentities($consultation['diagnosis']); ?></p>
                                                    <?php } ?>
                                                    <?php if($consultation['treatment_plan']) { ?>
                                                    <p><strong>Treatment Plan:</strong> <?php echo htmlentities($consultation['treatment_plan']); ?></p>
                                                    <?php } ?>
                                                </div>
                                                <div class="col-md-4 text-right">
                                                    <?php if($consultation['doctorName']) { ?>
                                                    <p><strong>Dr. <?php echo htmlentities($consultation['doctorName']); ?></strong></p>
                                                    <small class="text-muted"><?php echo htmlentities($consultation['specilization']); ?></small>
                                                    <?php } ?>
                                                    <?php if($consultation['follow_up_required'] && $consultation['follow_up_date']) { ?>
                                                    <br><small class="text-warning">Follow-up: <?php echo date('M d, Y', strtotime($consultation['follow_up_date'])); ?></small>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php 
                                            }
                                        } else {
                                            echo '<p class="text-muted">No consultation records available.</p>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- VITAL SIGNS HISTORY -->
                        <div class="row ehr-section">
                            <div class="col-md-12">
                                <div class="panel panel-white">
                                    <div class="panel-heading">
                                        <h5 class="panel-title"><i class="fa fa-heartbeat"></i> Recent Vital Signs</h5>
                                    </div>
                                    <div class="panel-body">
                                        <?php
                                        $vitals_query = mysqli_query($con, "
                                            SELECT * FROM vital_signs 
                                            WHERE patient_id='$patient_id' 
                                            ORDER BY recorded_date DESC 
                                            LIMIT 5
                                        ");
                                        if(mysqli_num_rows($vitals_query) > 0) {
                                        ?>
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Blood Pressure</th>
                                                        <th>Heart Rate</th>
                                                        <th>Temperature</th>
                                                        <th>Weight</th>
                                                        <th>BMI</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php while($vital = mysqli_fetch_array($vitals_query)) { ?>
                                                    <tr>
                                                        <td><?php echo date('M d, Y', strtotime($vital['recorded_date'])); ?></td>
                                                        <td>
                                                            <?php if($vital['systolic_bp'] && $vital['diastolic_bp']) { ?>
                                                                <?php echo $vital['systolic_bp']; ?>/<?php echo $vital['diastolic_bp']; ?> mmHg
                                                            <?php } else { ?>
                                                                -
                                                            <?php } ?>
                                                        </td>
                                                        <td><?php echo $vital['heart_rate'] ? $vital['heart_rate'] . ' bpm' : '-'; ?></td>
                                                        <td><?php echo $vital['temperature'] ? $vital['temperature'] . '°C' : '-'; ?></td>
                                                        <td><?php echo $vital['weight'] ? $vital['weight'] . ' kg' : '-'; ?></td>
                                                        <td><?php echo $vital['bmi'] ? $vital['bmi'] : '-'; ?></td>
                                                    </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <?php 
                                        } else {
                                            echo '<p class="text-muted">No vital signs recorded.</p>';
                                        }
                                        ?>
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
