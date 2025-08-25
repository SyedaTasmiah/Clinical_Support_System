<?php
session_start();
error_reporting(0);
include('include/config.php');
if(strlen($_SESSION['id'])==0) {
 header('location:logout.php');
  } else{

$doctor_id = $_SESSION['id'];
$patient_id = isset($_GET['patient_id']) ? $_GET['patient_id'] : '';

if(!$patient_id) {
    header('location:manage-patient.php');
    exit();
}

// Handle form submissions
if(isset($_POST['add_consultation'])) {
    $chief_complaint = mysqli_real_escape_string($con, $_POST['chief_complaint']);
    $symptoms = mysqli_real_escape_string($con, $_POST['symptoms']);
    $diagnosis = mysqli_real_escape_string($con, $_POST['diagnosis']);
    $treatment_plan = mysqli_real_escape_string($con, $_POST['treatment_plan']);
    $notes = mysqli_real_escape_string($con, $_POST['notes']);
    $consultation_date = $_POST['consultation_date'];
    $follow_up_required = isset($_POST['follow_up_required']) ? 1 : 0;
    $follow_up_date = $_POST['follow_up_date'] ?: NULL;
    
    // Vital signs JSON
    $vital_signs = json_encode([
        'systolic_bp' => $_POST['systolic_bp'] ?: null,
        'diastolic_bp' => $_POST['diastolic_bp'] ?: null,
        'heart_rate' => $_POST['heart_rate'] ?: null,
        'temperature' => $_POST['temperature'] ?: null,
        'respiratory_rate' => $_POST['respiratory_rate'] ?: null,
        'oxygen_saturation' => $_POST['oxygen_saturation'] ?: null,
        'weight' => $_POST['weight'] ?: null,
        'height' => $_POST['height'] ?: null
    ]);
    
    $consultation_sql = "INSERT INTO medical_consultations 
                        (patient_id, doctor_id, consultation_date, chief_complaint, symptoms, diagnosis, treatment_plan, notes, vital_signs, follow_up_required, follow_up_date) 
                        VALUES ('$patient_id', '$doctor_id', '$consultation_date', '$chief_complaint', '$symptoms', '$diagnosis', '$treatment_plan', '$notes', '$vital_signs', '$follow_up_required', " . ($follow_up_date ? "'$follow_up_date'" : "NULL") . ")";
    
    if(mysqli_query($con, $consultation_sql)) {
        $consultation_id = mysqli_insert_id($con);
        
        // Add vital signs to vital_signs table if provided
        if($_POST['systolic_bp'] || $_POST['heart_rate'] || $_POST['temperature'] || $_POST['weight']) {
            $bmi = null;
            if($_POST['weight'] && $_POST['height']) {
                $height_m = $_POST['height'] / 100;
                $bmi = round($_POST['weight'] / ($height_m * $height_m), 1);
            }
            
            $vital_sql = "INSERT INTO vital_signs 
                         (patient_id, consultation_id, recorded_date, systolic_bp, diastolic_bp, heart_rate, temperature, respiratory_rate, oxygen_saturation, weight, height, bmi, recorded_by) 
                         VALUES ('$patient_id', '$consultation_id', NOW(), " .
                         ($_POST['systolic_bp'] ? "'".$_POST['systolic_bp']."'" : "NULL") . ", " .
                         ($_POST['diastolic_bp'] ? "'".$_POST['diastolic_bp']."'" : "NULL") . ", " .
                         ($_POST['heart_rate'] ? "'".$_POST['heart_rate']."'" : "NULL") . ", " .
                         ($_POST['temperature'] ? "'".$_POST['temperature']."'" : "NULL") . ", " .
                         ($_POST['respiratory_rate'] ? "'".$_POST['respiratory_rate']."'" : "NULL") . ", " .
                         ($_POST['oxygen_saturation'] ? "'".$_POST['oxygen_saturation']."'" : "NULL") . ", " .
                         ($_POST['weight'] ? "'".$_POST['weight']."'" : "NULL") . ", " .
                         ($_POST['height'] ? "'".$_POST['height']."'" : "NULL") . ", " .
                         ($bmi ? "'$bmi'" : "NULL") . ", '$doctor_id')";
            mysqli_query($con, $vital_sql);
        }
        
        echo "<script>alert('Consultation record added successfully!');</script>";
    } else {
        echo "<script>alert('Error adding consultation: " . mysqli_error($con) . "');</script>";
    }
}

if(isset($_POST['add_prescription'])) {
    $medication_name = mysqli_real_escape_string($con, $_POST['medication_name']);
    $dosage = mysqli_real_escape_string($con, $_POST['dosage']);
    $frequency = mysqli_real_escape_string($con, $_POST['frequency']);
    $duration = mysqli_real_escape_string($con, $_POST['duration']);
    $instructions = mysqli_real_escape_string($con, $_POST['instructions']);
    $prescribed_date = $_POST['prescribed_date'];
    $start_date = $_POST['start_date'] ?: NULL;
    $end_date = $_POST['end_date'] ?: NULL;
    $refills = $_POST['refills_remaining'] ?: 0;
    
    $prescription_sql = "INSERT INTO prescriptions 
                        (patient_id, doctor_id, medication_name, dosage, frequency, duration, instructions, prescribed_date, start_date, end_date, refills_remaining) 
                        VALUES ('$patient_id', '$doctor_id', '$medication_name', '$dosage', '$frequency', '$duration', '$instructions', '$prescribed_date', " .
                        ($start_date ? "'$start_date'" : "NULL") . ", " .
                        ($end_date ? "'$end_date'" : "NULL") . ", '$refills')";
    
    if(mysqli_query($con, $prescription_sql)) {
        echo "<script>alert('Prescription added successfully!');</script>";
    } else {
        echo "<script>alert('Error adding prescription: " . mysqli_error($con) . "');</script>";
    }
}

if(isset($_POST['add_allergy'])) {
    $allergy_name = mysqli_real_escape_string($con, $_POST['allergy_name']);
    $allergy_type = $_POST['allergy_type'];
    $severity = $_POST['severity'];
    $reaction_description = mysqli_real_escape_string($con, $_POST['reaction_description']);
    $diagnosed_date = $_POST['diagnosed_date'] ?: NULL;
    
    $allergy_sql = "INSERT INTO patient_allergies 
                   (patient_id, allergy_name, allergy_type, severity, reaction_description, diagnosed_date) 
                   VALUES ('$patient_id', '$allergy_name', '$allergy_type', '$severity', '$reaction_description', " .
                   ($diagnosed_date ? "'$diagnosed_date'" : "NULL") . ")";
    
    if(mysqli_query($con, $allergy_sql)) {
        echo "<script>alert('Allergy record added successfully!');</script>";
    } else {
        echo "<script>alert('Error adding allergy: " . mysqli_error($con) . "');</script>";
    }
}

if(isset($_POST['add_condition'])) {
    $condition_name = mysqli_real_escape_string($con, $_POST['condition_name']);
    $condition_type = $_POST['condition_type'];
    $diagnosed_date = $_POST['diagnosed_date'] ?: NULL;
    $notes = mysqli_real_escape_string($con, $_POST['condition_notes']);
    
    $condition_sql = "INSERT INTO patient_conditions 
                     (patient_id, condition_name, condition_type, diagnosed_date, diagnosed_by, notes) 
                     VALUES ('$patient_id', '$condition_name', '$condition_type', " .
                     ($diagnosed_date ? "'$diagnosed_date'" : "NULL") . ", '$doctor_id', '$notes')";
    
    if(mysqli_query($con, $condition_sql)) {
        echo "<script>alert('Medical condition added successfully!');</script>";
    } else {
        echo "<script>alert('Error adding condition: " . mysqli_error($con) . "');</script>";
    }
}

// Get patient information
$patient_query = mysqli_query($con, "SELECT * FROM users WHERE id='$patient_id'");
$patient = mysqli_fetch_array($patient_query);

if(!$patient) {
    header('location:manage-patient.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Patient EHR | <?php echo htmlentities($patient['fullname']); ?></title>
    
    <link href="http://fonts.googleapis.com/css?family=Lato:300,400,400italic,600,700|Raleway:300,400,500,600,700|Crete+Round:400italic" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="vendor/themify-icons/themify-icons.min.css">
    <link href="vendor/animate.css/animate.min.css" rel="stylesheet" media="screen">
    <link href="vendor/perfect-scrollbar/perfect-scrollbar.min.css" rel="stylesheet" media="screen">
    <link href="vendor/switchery/switchery.min.css" rel="stylesheet" media="screen">
    <link href="vendor/bootstrap-datepicker/bootstrap-datepicker3.standalone.min.css" rel="stylesheet" media="screen">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/plugins.css">
    <link rel="stylesheet" href="assets/css/themes/theme-1.css" id="skin_color" />
    
    <style>
        .ehr-tabs .nav-tabs > li.active > a {
            background-color: #337ab7;
            color: white;
        }
        .vital-signs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        .allergy-alert {
            background-color: #f2dede;
            border: 1px solid #ebccd1;
            color: #a94442;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        .condition-item {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        .prescription-item {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 10px;
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
                                <h1 class="mainTitle"><i class="fa fa-user-md"></i> Patient EHR: <?php echo htmlentities($patient['fullname']); ?></h1>
                            </div>
                            <ol class="breadcrumb">
                                <li><span>Doctor</span></li>
                                <li><a href="manage-patient.php">Patients</a></li>
                                <li class="active"><span>EHR</span></li>
                            </ol>
                        </div>
                    </section>

                    <!-- PATIENT INFO HEADER -->
                    <div class="container-fluid container-fullw bg-white">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-white">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <h4><i class="fa fa-user"></i> <?php echo htmlentities($patient['fullname']); ?></h4>
                                                <p><strong>Email:</strong> <?php echo htmlentities($patient['email']); ?> | 
                                                   <strong>Gender:</strong> <?php echo htmlentities($patient['gender']); ?> | 
                                                   <strong>Address:</strong> <?php echo htmlentities($patient['address'] . ', ' . $patient['city']); ?></p>
                                            </div>
                                            <div class="col-md-4 text-right">
                                                <a href="manage-patient.php" class="btn btn-default">
                                                    <i class="fa fa-arrow-left"></i> Back to Patients
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- EHR TABS -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-white ehr-tabs">
                                    <div class="panel-body">
                                        <ul class="nav nav-tabs" role="tablist">
                                            <li role="presentation" class="active">
                                                <a href="#overview" aria-controls="overview" role="tab" data-toggle="tab">
                                                    <i class="fa fa-dashboard"></i> Overview
                                                </a>
                                            </li>
                                            <li role="presentation">
                                                <a href="#consultation" aria-controls="consultation" role="tab" data-toggle="tab">
                                                    <i class="fa fa-stethoscope"></i> New Consultation
                                                </a>
                                            </li>
                                            <li role="presentation">
                                                <a href="#prescription" aria-controls="prescription" role="tab" data-toggle="tab">
                                                    <i class="fa fa-pills"></i> Add Prescription
                                                </a>
                                            </li>
                                            <li role="presentation">
                                                <a href="#allergy" aria-controls="allergy" role="tab" data-toggle="tab">
                                                    <i class="fa fa-exclamation-triangle"></i> Add Allergy
                                                </a>
                                            </li>
                                            <li role="presentation">
                                                <a href="#condition" aria-controls="condition" role="tab" data-toggle="tab">
                                                    <i class="fa fa-heartbeat"></i> Add Condition
                                                </a>
                                            </li>
                                        </ul>

                                        <div class="tab-content">
                                            <!-- OVERVIEW TAB -->
                                            <div role="tabpanel" class="tab-pane active" id="overview">
                                                <div class="row" style="margin-top: 20px;">
                                                    <!-- Current Allergies -->
                                                    <div class="col-md-6">
                                                        <h5><i class="fa fa-exclamation-triangle text-danger"></i> Active Allergies</h5>
                                                        <?php
                                                        $allergies_query = mysqli_query($con, "SELECT * FROM patient_allergies WHERE patient_id='$patient_id' AND is_active=1 ORDER BY severity DESC");
                                                        if(mysqli_num_rows($allergies_query) > 0) {
                                                            while($allergy = mysqli_fetch_array($allergies_query)) {
                                                                echo '<div class="allergy-alert">';
                                                                echo '<strong>' . htmlentities($allergy['allergy_name']) . '</strong> (' . $allergy['severity'] . ')';
                                                                if($allergy['reaction_description']) {
                                                                    echo '<br><small>' . htmlentities($allergy['reaction_description']) . '</small>';
                                                                }
                                                                echo '</div>';
                                                            }
                                                        } else {
                                                            echo '<p class="text-muted">No known allergies</p>';
                                                        }
                                                        ?>
                                                    </div>

                                                    <!-- Current Conditions -->
                                                    <div class="col-md-6">
                                                        <h5><i class="fa fa-heartbeat text-warning"></i> Active Conditions</h5>
                                                        <?php
                                                        $conditions_query = mysqli_query($con, "SELECT * FROM patient_conditions WHERE patient_id='$patient_id' AND status='Active' ORDER BY diagnosed_date DESC");
                                                        if(mysqli_num_rows($conditions_query) > 0) {
                                                            while($condition = mysqli_fetch_array($conditions_query)) {
                                                                echo '<div class="condition-item">';
                                                                echo '<strong>' . htmlentities($condition['condition_name']) . '</strong>';
                                                                if($condition['diagnosed_date']) {
                                                                    echo '<br><small>Since: ' . date('M Y', strtotime($condition['diagnosed_date'])) . '</small>';
                                                                }
                                                                echo '</div>';
                                                            }
                                                        } else {
                                                            echo '<p class="text-muted">No active conditions</p>';
                                                        }
                                                        ?>
                                                    </div>
                                                </div>

                                                <div class="row" style="margin-top: 20px;">
                                                    <!-- Current Prescriptions -->
                                                    <div class="col-md-6">
                                                        <h5><i class="fa fa-pills text-success"></i> Active Prescriptions</h5>
                                                        <?php
                                                        $prescriptions_query = mysqli_query($con, "SELECT * FROM prescriptions WHERE patient_id='$patient_id' AND status='Active' ORDER BY prescribed_date DESC LIMIT 5");
                                                        if(mysqli_num_rows($prescriptions_query) > 0) {
                                                            while($prescription = mysqli_fetch_array($prescriptions_query)) {
                                                                echo '<div class="prescription-item">';
                                                                echo '<strong>' . htmlentities($prescription['medication_name']) . '</strong><br>';
                                                                echo htmlentities($prescription['dosage']) . ' - ' . htmlentities($prescription['frequency']);
                                                                echo '</div>';
                                                            }
                                                        } else {
                                                            echo '<p class="text-muted">No active prescriptions</p>';
                                                        }
                                                        ?>
                                                    </div>

                                                    <!-- Recent Consultations -->
                                                    <div class="col-md-6">
                                                        <h5><i class="fa fa-calendar text-info"></i> Recent Consultations</h5>
                                                        <?php
                                                        $recent_consultations = mysqli_query($con, "
                                                            SELECT mc.*, d.doctorName 
                                                            FROM medical_consultations mc 
                                                            LEFT JOIN doctors d ON mc.doctor_id = d.id 
                                                            WHERE mc.patient_id='$patient_id' 
                                                            ORDER BY mc.consultation_date DESC 
                                                            LIMIT 3
                                                        ");
                                                        if(mysqli_num_rows($recent_consultations) > 0) {
                                                            while($consultation = mysqli_fetch_array($recent_consultations)) {
                                                                echo '<div style="border-left: 3px solid #337ab7; padding-left: 10px; margin-bottom: 10px;">';
                                                                echo '<strong>' . date('M d, Y', strtotime($consultation['consultation_date'])) . '</strong>';
                                                                if($consultation['doctorName']) {
                                                                    echo ' - Dr. ' . htmlentities($consultation['doctorName']);
                                                                }
                                                                if($consultation['diagnosis']) {
                                                                    echo '<br><small>' . htmlentities($consultation['diagnosis']) . '</small>';
                                                                }
                                                                echo '</div>';
                                                            }
                                                        } else {
                                                            echo '<p class="text-muted">No recent consultations</p>';
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- NEW CONSULTATION TAB -->
                                            <div role="tabpanel" class="tab-pane" id="consultation">
                                                <form method="post" style="margin-top: 20px;">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Consultation Date</label>
                                                                <input type="date" class="form-control" name="consultation_date" value="<?php echo date('Y-m-d'); ?>" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Chief Complaint</label>
                                                                <textarea class="form-control" name="chief_complaint" rows="3" placeholder="Patient's main concern or reason for visit"></textarea>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Symptoms</label>
                                                                <textarea class="form-control" name="symptoms" rows="3" placeholder="List of symptoms observed or reported"></textarea>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Diagnosis</label>
                                                                <textarea class="form-control" name="diagnosis" rows="3" placeholder="Medical diagnosis or assessment"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Treatment Plan</label>
                                                                <textarea class="form-control" name="treatment_plan" rows="3" placeholder="Recommended treatment approach"></textarea>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Additional Notes</label>
                                                                <textarea class="form-control" name="notes" rows="3" placeholder="Any additional observations or notes"></textarea>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>
                                                                    <input type="checkbox" name="follow_up_required" value="1"> Follow-up Required
                                                                </label>
                                                                <input type="date" class="form-control" name="follow_up_date" placeholder="Follow-up date">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Vital Signs -->
                                                    <h5><i class="fa fa-heartbeat"></i> Vital Signs</h5>
                                                    <div class="vital-signs-grid">
                                                        <div class="form-group">
                                                            <label>Systolic BP (mmHg)</label>
                                                            <input type="number" class="form-control" name="systolic_bp" min="60" max="250">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Diastolic BP (mmHg)</label>
                                                            <input type="number" class="form-control" name="diastolic_bp" min="40" max="150">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Heart Rate (bpm)</label>
                                                            <input type="number" class="form-control" name="heart_rate" min="30" max="200">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Temperature (°C)</label>
                                                            <input type="number" class="form-control" name="temperature" step="0.1" min="30" max="45">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Respiratory Rate</label>
                                                            <input type="number" class="form-control" name="respiratory_rate" min="8" max="40">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Oxygen Saturation (%)</label>
                                                            <input type="number" class="form-control" name="oxygen_saturation" min="70" max="100">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Weight (kg)</label>
                                                            <input type="number" class="form-control" name="weight" step="0.1" min="1" max="300">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Height (cm)</label>
                                                            <input type="number" class="form-control" name="height" step="0.1" min="30" max="250">
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <button type="submit" name="add_consultation" class="btn btn-primary">
                                                            <i class="fa fa-save"></i> Save Consultation
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>

                                            <!-- ADD PRESCRIPTION TAB -->
                                            <div role="tabpanel" class="tab-pane" id="prescription">
                                                <form method="post" style="margin-top: 20px;">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Medication Name *</label>
                                                                <input type="text" class="form-control" name="medication_name" required placeholder="e.g., Amoxicillin">
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Dosage *</label>
                                                                <input type="text" class="form-control" name="dosage" required placeholder="e.g., 500mg">
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Frequency *</label>
                                                                <select class="form-control" name="frequency" required>
                                                                    <option value="">Select Frequency</option>
                                                                    <option value="Once daily">Once daily</option>
                                                                    <option value="Twice daily">Twice daily</option>
                                                                    <option value="Three times daily">Three times daily</option>
                                                                    <option value="Four times daily">Four times daily</option>
                                                                    <option value="Every 4 hours">Every 4 hours</option>
                                                                    <option value="Every 6 hours">Every 6 hours</option>
                                                                    <option value="Every 8 hours">Every 8 hours</option>
                                                                    <option value="Every 12 hours">Every 12 hours</option>
                                                                    <option value="As needed">As needed (PRN)</option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Duration *</label>
                                                                <input type="text" class="form-control" name="duration" required placeholder="e.g., 7 days, 2 weeks">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Special Instructions</label>
                                                                <textarea class="form-control" name="instructions" rows="3" placeholder="e.g., Take with food, Avoid alcohol"></textarea>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Prescribed Date</label>
                                                                <input type="date" class="form-control" name="prescribed_date" value="<?php echo date('Y-m-d'); ?>" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Start Date</label>
                                                                <input type="date" class="form-control" name="start_date">
                                                            </div>
                                                            <div class="form-group">
                                                                <label>End Date</label>
                                                                <input type="date" class="form-control" name="end_date">
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Refills Remaining</label>
                                                                <input type="number" class="form-control" name="refills_remaining" min="0" max="12" value="0">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <button type="submit" name="add_prescription" class="btn btn-success">
                                                            <i class="fa fa-plus"></i> Add Prescription
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>

                                            <!-- ADD ALLERGY TAB -->
                                            <div role="tabpanel" class="tab-pane" id="allergy">
                                                <form method="post" style="margin-top: 20px;">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Allergy Name *</label>
                                                                <input type="text" class="form-control" name="allergy_name" required placeholder="e.g., Penicillin, Peanuts">
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Allergy Type *</label>
                                                                <select class="form-control" name="allergy_type" required>
                                                                    <option value="">Select Type</option>
                                                                    <option value="Drug">Drug/Medication</option>
                                                                    <option value="Food">Food</option>
                                                                    <option value="Environmental">Environmental</option>
                                                                    <option value="Other">Other</option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Severity *</label>
                                                                <select class="form-control" name="severity" required>
                                                                    <option value="">Select Severity</option>
                                                                    <option value="Mild">Mild</option>
                                                                    <option value="Moderate">Moderate</option>
                                                                    <option value="Severe">Severe</option>
                                                                    <option value="Life-threatening">Life-threatening</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Reaction Description</label>
                                                                <textarea class="form-control" name="reaction_description" rows="4" placeholder="Describe the allergic reaction symptoms"></textarea>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Date Diagnosed</label>
                                                                <input type="date" class="form-control" name="diagnosed_date">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <button type="submit" name="add_allergy" class="btn btn-danger">
                                                            <i class="fa fa-exclamation-triangle"></i> Add Allergy
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>

                                            <!-- ADD CONDITION TAB -->
                                            <div role="tabpanel" class="tab-pane" id="condition">
                                                <form method="post" style="margin-top: 20px;">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Condition Name *</label>
                                                                <input type="text" class="form-control" name="condition_name" required placeholder="e.g., Hypertension, Type 2 Diabetes">
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Condition Type *</label>
                                                                <select class="form-control" name="condition_type" required>
                                                                    <option value="">Select Type</option>
                                                                    <option value="Chronic">Chronic</option>
                                                                    <option value="Acute">Acute</option>
                                                                    <option value="Resolved">Resolved</option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Date Diagnosed</label>
                                                                <input type="date" class="form-control" name="diagnosed_date">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Notes</label>
                                                                <textarea class="form-control" name="condition_notes" rows="6" placeholder="Additional notes about the condition"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <button type="submit" name="add_condition" class="btn btn-warning">
                                                            <i class="fa fa-plus"></i> Add Condition
                                                        </button>
                                                    </div>
                                                </form>
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
    <script src="vendor/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/form-elements.js"></script>
    <script>
        jQuery(document).ready(function() {
            Main.init();
            FormElements.init();
            
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true
            });
        });
    </script>
</body>
</html>
<?php } ?>
