<?php
session_start();
error_reporting(0);
include('include/config.php');
if(strlen($_SESSION['id'])==0) {
    header('location:logout.php');
    exit();
}

$doctor_id = $_SESSION['id'];
$appointment_id = isset($_GET['appointment_id']) ? $_GET['appointment_id'] : '';
$patient_id = isset($_GET['patient_id']) ? $_GET['patient_id'] : '';

if(!$appointment_id || !$patient_id) {
    header('location:appointment-history.php');
    exit();
}

// Get appointment and patient details
$appointment_query = mysqli_query($con, "
    SELECT a.*, u.fullname as patient_name, u.email as patient_email, u.address, u.city, u.gender 
    FROM appointment a 
    JOIN users u ON a.userId = u.id 
    WHERE a.id='$appointment_id' AND a.doctorId='$doctor_id'
");
$appointment = mysqli_fetch_array($appointment_query);

if(!$appointment) {
    header('location:appointment-history.php');
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
    
    // Build vital signs JSON
    $vital_signs = [];
    if($_POST['systolic_bp']) $vital_signs['systolic_bp'] = $_POST['systolic_bp'];
    if($_POST['diastolic_bp']) $vital_signs['diastolic_bp'] = $_POST['diastolic_bp'];
    if($_POST['heart_rate']) $vital_signs['heart_rate'] = $_POST['heart_rate'];
    if($_POST['temperature']) $vital_signs['temperature'] = $_POST['temperature'];
    if($_POST['respiratory_rate']) $vital_signs['respiratory_rate'] = $_POST['respiratory_rate'];
    if($_POST['oxygen_saturation']) $vital_signs['oxygen_saturation'] = $_POST['oxygen_saturation'];
    if($_POST['weight']) $vital_signs['weight'] = $_POST['weight'];
    if($_POST['height']) $vital_signs['height'] = $_POST['height'];
    
    $vital_signs_json = !empty($vital_signs) ? json_encode($vital_signs) : NULL;
    
    $consultation_sql = "INSERT INTO medical_consultations 
                        (patient_id, doctor_id, appointment_id, consultation_date, chief_complaint, symptoms, diagnosis, treatment_plan, notes, vital_signs, follow_up_required, follow_up_date) 
                        VALUES ('$patient_id', '$doctor_id', '$appointment_id', '$consultation_date', '$chief_complaint', '$symptoms', '$diagnosis', '$treatment_plan', '$notes', " . 
                        ($vital_signs_json ? "'$vital_signs_json'" : "NULL") . ", '$follow_up_required', " . 
                        ($follow_up_date ? "'$follow_up_date'" : "NULL") . ")";
    
    if(mysqli_query($con, $consultation_sql)) {
        $consultation_id = mysqli_insert_id($con);
        
        // Add vital signs to vital_signs table if provided
        if(!empty($vital_signs)) {
            $bmi = null;
            if(isset($vital_signs['weight']) && isset($vital_signs['height'])) {
                $height_m = $vital_signs['height'] / 100;
                $bmi = round($vital_signs['weight'] / ($height_m * $height_m), 1);
            }
            
            $vital_sql = "INSERT INTO vital_signs 
                         (patient_id, consultation_id, recorded_date, systolic_bp, diastolic_bp, heart_rate, temperature, respiratory_rate, oxygen_saturation, weight, height, bmi, recorded_by) 
                         VALUES ('$patient_id', '$consultation_id', NOW(), " .
                         (isset($vital_signs['systolic_bp']) ? "'".$vital_signs['systolic_bp']."'" : "NULL") . ", " .
                         (isset($vital_signs['diastolic_bp']) ? "'".$vital_signs['diastolic_bp']."'" : "NULL") . ", " .
                         (isset($vital_signs['heart_rate']) ? "'".$vital_signs['heart_rate']."'" : "NULL") . ", " .
                         (isset($vital_signs['temperature']) ? "'".$vital_signs['temperature']."'" : "NULL") . ", " .
                         (isset($vital_signs['respiratory_rate']) ? "'".$vital_signs['respiratory_rate']."'" : "NULL") . ", " .
                         (isset($vital_signs['oxygen_saturation']) ? "'".$vital_signs['oxygen_saturation']."'" : "NULL") . ", " .
                         (isset($vital_signs['weight']) ? "'".$vital_signs['weight']."'" : "NULL") . ", " .
                         (isset($vital_signs['height']) ? "'".$vital_signs['height']."'" : "NULL") . ", " .
                         ($bmi ? "'$bmi'" : "NULL") . ", '$doctor_id')";
            mysqli_query($con, $vital_sql);
        }
        
        echo "<script>alert('Medical consultation record added successfully!'); window.location.href='update-medical-history.php?appointment_id=$appointment_id&patient_id=$patient_id';</script>";
    } else {
        echo "<script>alert('Error adding consultation: " . mysqli_error($con) . "');</script>";
    }
}

if(isset($_POST['add_prescriptions'])) {
    $prescribed_date = $_POST['prescribed_date'];
    $start_date = $_POST['start_date'] ?: NULL;
    $general_instructions = mysqli_real_escape_string($con, $_POST['general_instructions']);
    
    $success_count = 0;
    $error_count = 0;
    $medications = [];
    
    // Process multiple medications
    if(isset($_POST['medication_name']) && is_array($_POST['medication_name'])) {
        for($i = 0; $i < count($_POST['medication_name']); $i++) {
            if(!empty($_POST['medication_name'][$i]) && !empty($_POST['dosage'][$i]) && !empty($_POST['frequency'][$i]) && !empty($_POST['duration'][$i])) {
                $medication_name = mysqli_real_escape_string($con, $_POST['medication_name'][$i]);
                $dosage = mysqli_real_escape_string($con, $_POST['dosage'][$i]);
                $frequency = mysqli_real_escape_string($con, $_POST['frequency'][$i]);
                $duration = mysqli_real_escape_string($con, $_POST['duration'][$i]);
                $instructions = mysqli_real_escape_string($con, $_POST['instructions'][$i]);
                $end_date = $_POST['end_date'][$i] ?: NULL;
                $refills = $_POST['refills_remaining'][$i] ?: 0;
                
                $prescription_sql = "INSERT INTO prescriptions 
                                    (patient_id, doctor_id, medication_name, dosage, frequency, duration, instructions, prescribed_date, start_date, end_date, refills_remaining) 
                                    VALUES ('$patient_id', '$doctor_id', '$medication_name', '$dosage', '$frequency', '$duration', '$instructions', '$prescribed_date', " .
                                    ($start_date ? "'$start_date'" : "NULL") . ", " .
                                    ($end_date ? "'$end_date'" : "NULL") . ", '$refills')";
                
                if(mysqli_query($con, $prescription_sql)) {
                    $success_count++;
                    $medications[] = $medication_name;
                } else {
                    $error_count++;
                }
            }
        }
    }
    
    if($success_count > 0) {
        $message = "Successfully added $success_count prescription(s): " . implode(', ', $medications);
        if($error_count > 0) {
            $message .= ". $error_count prescription(s) failed to save.";
        }
        echo "<script>alert('$message'); window.location.href='update-medical-history.php?appointment_id=$appointment_id&patient_id=$patient_id';</script>";
    } else {
        echo "<script>alert('No prescriptions were added. Please check your entries.');</script>";
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
        echo "<script>alert('Allergy record added successfully!'); window.location.href='update-medical-history.php?appointment_id=$appointment_id&patient_id=$patient_id';</script>";
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
        echo "<script>alert('Medical condition added successfully!'); window.location.href='update-medical-history.php?appointment_id=$appointment_id&patient_id=$patient_id';</script>";
    } else {
        echo "<script>alert('Error adding condition: " . mysqli_error($con) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Update Medical History | <?php echo htmlentities($appointment['patient_name']); ?></title>
    
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
        .appointment-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }
        .vital-signs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        .medical-record {
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .allergy-warning {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        .nav-tabs > li.active > a {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }
        .tab-content {
            margin-top: 20px;
        }
        .medication-row {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .medication-header {
            background-color: #007bff;
            color: white;
            padding: 8px 12px;
            margin: -15px -15px 15px -15px;
            border-radius: 4px 4px 0 0;
            font-weight: bold;
        }
        .remove-medication {
            background-color: #dc3545;
            border: none;
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            float: right;
        }
        .add-medication-btn {
            background-color: #28a745;
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
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
                                <h1 class="mainTitle"><i class="fa fa-user-md"></i> Update Medical History</h1>
                            </div>
                            <ol class="breadcrumb">
                                <li><span>Doctor</span></li>
                                <li><a href="appointment-history.php">Appointments</a></li>
                                <li class="active"><span>Medical History</span></li>
                            </ol>
                        </div>
                    </section>

                    <!-- APPOINTMENT INFO -->
                    <div class="container-fluid container-fullw bg-white">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="appointment-info">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h4><i class="fa fa-user"></i> <?php echo htmlentities($appointment['patient_name']); ?></h4>
                                            <p><strong>Email:</strong> <?php echo htmlentities($appointment['patient_email']); ?></p>
                                            <p><strong>Gender:</strong> <?php echo htmlentities($appointment['gender']); ?></p>
                                            <p><strong>Address:</strong> <?php echo htmlentities($appointment['address'] . ', ' . $appointment['city']); ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Appointment Date:</strong> <?php echo date('F d, Y', strtotime($appointment['appointmentDate'])); ?></p>
                                            <p><strong>Appointment Time:</strong> <?php echo htmlentities($appointment['appointmentTime']); ?></p>
                                            <p><strong>Specialization:</strong> <?php echo htmlentities($appointment['doctorSpecialization']); ?></p>
                                            <p><strong>Consultation Fee:</strong> $<?php echo htmlentities($appointment['consultancyFees']); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- EXISTING MEDICAL RECORDS -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-white">
                                    <div class="panel-heading">
                                        <h5 class="panel-title"><i class="fa fa-history"></i> Existing Medical Records</h5>
                                    </div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <!-- Current Allergies -->
                                            <div class="col-md-6">
                                                <h6><i class="fa fa-exclamation-triangle text-danger"></i> Known Allergies</h6>
                                                <?php
                                                $allergies_query = mysqli_query($con, "SELECT * FROM patient_allergies WHERE patient_id='$patient_id' AND is_active=1 ORDER BY severity DESC");
                                                if(mysqli_num_rows($allergies_query) > 0) {
                                                    while($allergy = mysqli_fetch_array($allergies_query)) {
                                                        echo '<div class="allergy-warning">';
                                                        echo '<strong>' . htmlentities($allergy['allergy_name']) . '</strong> (' . $allergy['severity'] . ')';
                                                        if($allergy['reaction_description']) {
                                                            echo '<br><small>' . htmlentities($allergy['reaction_description']) . '</small>';
                                                        }
                                                        echo '</div>';
                                                    }
                                                } else {
                                                    echo '<p class="text-muted">No known allergies recorded</p>';
                                                }
                                                ?>
                                            </div>

                                            <!-- Current Conditions -->
                                            <div class="col-md-6">
                                                <h6><i class="fa fa-heartbeat text-warning"></i> Active Conditions</h6>
                                                <?php
                                                $conditions_query = mysqli_query($con, "SELECT * FROM patient_conditions WHERE patient_id='$patient_id' AND status='Active' ORDER BY diagnosed_date DESC");
                                                if(mysqli_num_rows($conditions_query) > 0) {
                                                    while($condition = mysqli_fetch_array($conditions_query)) {
                                                        echo '<div class="medical-record">';
                                                        echo '<strong>' . htmlentities($condition['condition_name']) . '</strong>';
                                                        if($condition['diagnosed_date']) {
                                                            echo '<br><small>Since: ' . date('M Y', strtotime($condition['diagnosed_date'])) . '</small>';
                                                        }
                                                        echo '</div>';
                                                    }
                                                } else {
                                                    echo '<p class="text-muted">No active conditions recorded</p>';
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- MEDICAL HISTORY TABS -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-white">
                                    <div class="panel-body">
                                        <ul class="nav nav-tabs" role="tablist">
                                            <li role="presentation" class="active">
                                                <a href="#consultation" aria-controls="consultation" role="tab" data-toggle="tab">
                                                    <i class="fa fa-stethoscope"></i> Consultation Notes
                                                </a>
                                            </li>
                                            <li role="presentation">
                                                <a href="#prescription" aria-controls="prescription" role="tab" data-toggle="tab">
                                                    <i class="fa fa-pills"></i> Prescriptions
                                                </a>
                                            </li>
                                            <li role="presentation">
                                                <a href="#allergy" aria-controls="allergy" role="tab" data-toggle="tab">
                                                    <i class="fa fa-exclamation-triangle"></i> Allergies
                                                </a>
                                            </li>
                                            <li role="presentation">
                                                <a href="#condition" aria-controls="condition" role="tab" data-toggle="tab">
                                                    <i class="fa fa-heartbeat"></i> Medical Conditions
                                                </a>
                                            </li>
                                        </ul>

                                        <div class="tab-content">
                                            <!-- CONSULTATION TAB -->
                                            <div role="tabpanel" class="tab-pane active" id="consultation">
                                                <form method="post">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Consultation Date *</label>
                                                                <input type="date" class="form-control" name="consultation_date" value="<?php echo $appointment['appointmentDate']; ?>" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Chief Complaint</label>
                                                                <textarea class="form-control" name="chief_complaint" rows="3" placeholder="Patient's main concern or reason for visit"></textarea>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Symptoms Observed</label>
                                                                <textarea class="form-control" name="symptoms" rows="3" placeholder="List of symptoms observed or reported by patient"></textarea>
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
                                                                <textarea class="form-control" name="notes" rows="3" placeholder="Any additional observations, recommendations, or notes"></textarea>
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
                                                    <h6><i class="fa fa-heartbeat"></i> Vital Signs</h6>
                                                    <div class="vital-signs-grid">
                                                        <div class="form-group">
                                                            <label>Systolic BP (mmHg)</label>
                                                            <input type="number" class="form-control" name="systolic_bp" min="60" max="250" placeholder="120">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Diastolic BP (mmHg)</label>
                                                            <input type="number" class="form-control" name="diastolic_bp" min="40" max="150" placeholder="80">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Heart Rate (bpm)</label>
                                                            <input type="number" class="form-control" name="heart_rate" min="30" max="200" placeholder="72">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Temperature (°C)</label>
                                                            <input type="number" class="form-control" name="temperature" step="0.1" min="30" max="45" placeholder="36.5">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Respiratory Rate</label>
                                                            <input type="number" class="form-control" name="respiratory_rate" min="8" max="40" placeholder="16">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Oxygen Saturation (%)</label>
                                                            <input type="number" class="form-control" name="oxygen_saturation" min="70" max="100" placeholder="98">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Weight (kg)</label>
                                                            <input type="number" class="form-control" name="weight" step="0.1" min="1" max="300" placeholder="70.0">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Height (cm)</label>
                                                            <input type="number" class="form-control" name="height" step="0.1" min="30" max="250" placeholder="175.0">
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <button type="submit" name="add_consultation" class="btn btn-primary btn-lg">
                                                            <i class="fa fa-save"></i> Save Consultation Notes
                                                        </button>
                                                        <a href="appointment-history.php" class="btn btn-default btn-lg">
                                                            <i class="fa fa-arrow-left"></i> Back to Appointments
                                                        </a>
                                                    </div>
                                                </form>
                                            </div>

                                            <!-- PRESCRIPTION TAB -->
                                            <div role="tabpanel" class="tab-pane" id="prescription">
                                                <form method="post" id="prescriptionForm">
                                                    <!-- General Prescription Info -->
                                                    <div class="row" style="margin-bottom: 20px;">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>Prescribed Date *</label>
                                                                <input type="date" class="form-control" name="prescribed_date" value="<?php echo date('Y-m-d'); ?>" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>Start Date</label>
                                                                <input type="date" class="form-control" name="start_date" value="<?php echo date('Y-m-d'); ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label>General Instructions</label>
                                                                <input type="text" class="form-control" name="general_instructions" placeholder="e.g., Take all medications with food">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Add Medication Button -->
                                                    <button type="button" class="add-medication-btn" onclick="addMedicationRow()">
                                                        <i class="fa fa-plus"></i> Add Medication
                                                    </button>

                                                    <!-- Medications Container -->
                                                    <div id="medicationsContainer">
                                                        <!-- Initial medication row -->
                                                        <div class="medication-row" data-medication="0">
                                                            <div class="medication-header">
                                                                Medication #1
                                                                <button type="button" class="remove-medication" onclick="removeMedicationRow(0)" style="display: none;">
                                                                    <i class="fa fa-trash"></i> Remove
                                                                </button>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Medication Name *</label>
                                                                        <input type="text" class="form-control" name="medication_name[]" required placeholder="e.g., Amoxicillin, Ibuprofen">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Dosage *</label>
                                                                        <input type="text" class="form-control" name="dosage[]" required placeholder="e.g., 500mg, 200mg">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Frequency *</label>
                                                                        <select class="form-control" name="frequency[]" required>
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
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label>Duration *</label>
                                                                        <input type="text" class="form-control" name="duration[]" required placeholder="e.g., 7 days, 2 weeks, 1 month">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Special Instructions</label>
                                                                        <textarea class="form-control" name="instructions[]" rows="2" placeholder="e.g., Take with food, Avoid alcohol"></textarea>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>End Date</label>
                                                                                <input type="date" class="form-control" name="end_date[]">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Refills</label>
                                                                                <input type="number" class="form-control" name="refills_remaining[]" min="0" max="12" value="0">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Current Prescriptions -->
                                                    <div style="margin-top: 30px;">
                                                        <h6><i class="fa fa-list"></i> Current Active Prescriptions</h6>
                                                        <?php
                                                        $current_prescriptions = mysqli_query($con, "
                                                            SELECT * FROM prescriptions 
                                                            WHERE patient_id='$patient_id' AND status='Active' 
                                                            ORDER BY prescribed_date DESC 
                                                            LIMIT 5
                                                        ");
                                                        if(mysqli_num_rows($current_prescriptions) > 0) {
                                                            echo '<div class="table-responsive">';
                                                            echo '<table class="table table-striped table-sm">';
                                                            echo '<thead><tr><th>Medication</th><th>Dosage</th><th>Frequency</th><th>Duration</th><th>Prescribed</th></tr></thead>';
                                                            echo '<tbody>';
                                                            while($rx = mysqli_fetch_array($current_prescriptions)) {
                                                                echo '<tr>';
                                                                echo '<td><strong>' . htmlentities($rx['medication_name']) . '</strong></td>';
                                                                echo '<td>' . htmlentities($rx['dosage']) . '</td>';
                                                                echo '<td>' . htmlentities($rx['frequency']) . '</td>';
                                                                echo '<td>' . htmlentities($rx['duration']) . '</td>';
                                                                echo '<td>' . date('M d, Y', strtotime($rx['prescribed_date'])) . '</td>';
                                                                echo '</tr>';
                                                            }
                                                            echo '</tbody></table></div>';
                                                        } else {
                                                            echo '<p class="text-muted">No active prescriptions found.</p>';
                                                        }
                                                        ?>
                                                    </div>

                                                    <div class="form-group" style="margin-top: 30px;">
                                                        <button type="submit" name="add_prescriptions" class="btn btn-success btn-lg">
                                                            <i class="fa fa-save"></i> Save All Prescriptions
                                                        </button>
                                                        <button type="button" class="btn btn-secondary btn-lg" onclick="clearAllMedications()">
                                                            <i class="fa fa-refresh"></i> Clear All
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>

                                            <!-- ALLERGY TAB -->
                                            <div role="tabpanel" class="tab-pane" id="allergy">
                                                <form method="post">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Allergy Name *</label>
                                                                <input type="text" class="form-control" name="allergy_name" required placeholder="e.g., Penicillin, Peanuts, Latex">
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
                                                            <div class="form-group">
                                                                <label>Date Identified</label>
                                                                <input type="date" class="form-control" name="diagnosed_date" value="<?php echo date('Y-m-d'); ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Reaction Description</label>
                                                                <textarea class="form-control" name="reaction_description" rows="6" placeholder="Describe the allergic reaction symptoms (e.g., rash, difficulty breathing, swelling)"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <button type="submit" name="add_allergy" class="btn btn-danger btn-lg">
                                                            <i class="fa fa-exclamation-triangle"></i> Add Allergy Record
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>

                                            <!-- CONDITION TAB -->
                                            <div role="tabpanel" class="tab-pane" id="condition">
                                                <form method="post">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Condition Name *</label>
                                                                <input type="text" class="form-control" name="condition_name" required placeholder="e.g., Hypertension, Type 2 Diabetes, Asthma">
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Condition Type *</label>
                                                                <select class="form-control" name="condition_type" required>
                                                                    <option value="">Select Type</option>
                                                                    <option value="Chronic">Chronic (Long-term)</option>
                                                                    <option value="Acute">Acute (Short-term)</option>
                                                                    <option value="Resolved">Resolved</option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Date Diagnosed</label>
                                                                <input type="date" class="form-control" name="diagnosed_date" value="<?php echo date('Y-m-d'); ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Clinical Notes</label>
                                                                <textarea class="form-control" name="condition_notes" rows="6" placeholder="Additional notes about the condition, treatment response, etc."></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <button type="submit" name="add_condition" class="btn btn-warning btn-lg">
                                                            <i class="fa fa-plus"></i> Add Medical Condition
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
        let medicationCounter = 0;
        
        function addMedicationRow() {
            medicationCounter++;
            const container = document.getElementById('medicationsContainer');
            const newRow = document.createElement('div');
            newRow.className = 'medication-row';
            newRow.setAttribute('data-medication', medicationCounter);
            
            newRow.innerHTML = `
                <div class="medication-header">
                    Medication #${medicationCounter + 1}
                    <button type="button" class="remove-medication" onclick="removeMedicationRow(${medicationCounter})">
                        <i class="fa fa-trash"></i> Remove
                    </button>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Medication Name *</label>
                            <input type="text" class="form-control" name="medication_name[]" required placeholder="e.g., Amoxicillin, Ibuprofen">
                        </div>
                        <div class="form-group">
                            <label>Dosage *</label>
                            <input type="text" class="form-control" name="dosage[]" required placeholder="e.g., 500mg, 200mg">
                        </div>
                        <div class="form-group">
                            <label>Frequency *</label>
                            <select class="form-control" name="frequency[]" required>
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
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Duration *</label>
                            <input type="text" class="form-control" name="duration[]" required placeholder="e.g., 7 days, 2 weeks, 1 month">
                        </div>
                        <div class="form-group">
                            <label>Special Instructions</label>
                            <textarea class="form-control" name="instructions[]" rows="2" placeholder="e.g., Take with food, Avoid alcohol"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>End Date</label>
                                    <input type="date" class="form-control" name="end_date[]">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Refills</label>
                                    <input type="number" class="form-control" name="refills_remaining[]" min="0" max="12" value="0">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            container.appendChild(newRow);
            updateRemoveButtons();
        }
        
        function removeMedicationRow(index) {
            const row = document.querySelector(`[data-medication="${index}"]`);
            if (row) {
                row.remove();
                updateMedicationNumbers();
                updateRemoveButtons();
            }
        }
        
        function updateMedicationNumbers() {
            const rows = document.querySelectorAll('.medication-row');
            rows.forEach((row, index) => {
                const header = row.querySelector('.medication-header');
                const removeBtn = header.querySelector('.remove-medication');
                const medicationNumber = index + 1;
                
                // Update header text
                header.innerHTML = `
                    Medication #${medicationNumber}
                    <button type="button" class="remove-medication" onclick="removeMedicationRow(${row.getAttribute('data-medication')})">
                        <i class="fa fa-trash"></i> Remove
                    </button>
                `;
            });
        }
        
        function updateRemoveButtons() {
            const rows = document.querySelectorAll('.medication-row');
            const removeButtons = document.querySelectorAll('.remove-medication');
            
            // Show/hide remove buttons based on number of rows
            if (rows.length <= 1) {
                removeButtons.forEach(btn => btn.style.display = 'none');
            } else {
                removeButtons.forEach(btn => btn.style.display = 'inline-block');
            }
        }
        
        function clearAllMedications() {
            if (confirm('Are you sure you want to clear all medications? This action cannot be undone.')) {
                const container = document.getElementById('medicationsContainer');
                container.innerHTML = `
                    <div class="medication-row" data-medication="0">
                        <div class="medication-header">
                            Medication #1
                            <button type="button" class="remove-medication" onclick="removeMedicationRow(0)" style="display: none;">
                                <i class="fa fa-trash"></i> Remove
                            </button>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Medication Name *</label>
                                    <input type="text" class="form-control" name="medication_name[]" required placeholder="e.g., Amoxicillin, Ibuprofen">
                                </div>
                                <div class="form-group">
                                    <label>Dosage *</label>
                                    <input type="text" class="form-control" name="dosage[]" required placeholder="e.g., 500mg, 200mg">
                                </div>
                                <div class="form-group">
                                    <label>Frequency *</label>
                                    <select class="form-control" name="frequency[]" required>
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
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Duration *</label>
                                    <input type="text" class="form-control" name="duration[]" required placeholder="e.g., 7 days, 2 weeks, 1 month">
                                </div>
                                <div class="form-group">
                                    <label>Special Instructions</label>
                                    <textarea class="form-control" name="instructions[]" rows="2" placeholder="e.g., Take with food, Avoid alcohol"></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>End Date</label>
                                            <input type="date" class="form-control" name="end_date[]">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Refills</label>
                                            <input type="number" class="form-control" name="refills_remaining[]" min="0" max="12" value="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                medicationCounter = 0;
                updateRemoveButtons();
            }
        }
        
        jQuery(document).ready(function() {
            Main.init();
            FormElements.init();
            
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true
            });
            
            // Initialize remove buttons visibility
            updateRemoveButtons();
        });
    </script>
</body>
</html>
