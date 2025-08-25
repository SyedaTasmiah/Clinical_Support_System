<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('include/config.php');

if(strlen($_SESSION['id'])==0) {
    header('location:logout.php');
    exit();
}

$msg = '';
$message_type = 'info';

if(isset($_POST['import'])) {
    if(isset($_FILES['json_file']) && $_FILES['json_file']['error'] == 0) {
        $file = $_FILES['json_file'];
        $file_name = $file['name'];
        $file_tmp = $file['tmp_name'];
        $file_size = $file['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Check file extension
        if($file_ext != 'json') {
            $msg = 'Only JSON files are allowed!';
            $message_type = 'danger';
        } elseif($file_size > 5000000) { // 5MB limit
            $msg = 'File size must be less than 5MB!';
            $message_type = 'danger';
        } else {
            // Read JSON file
            $json_content = file_get_contents($file_tmp);
            $data = json_decode($json_content, true);

            if($data === null) {
                $msg = 'Invalid JSON file!';
                $message_type = 'danger';
            } else {
                // Ensure required tables exist
                $required_tables = [
                    'patient_allergies' => "CREATE TABLE IF NOT EXISTS patient_allergies (id INT(11) AUTO_INCREMENT PRIMARY KEY, patient_id INT(11) NOT NULL, allergy_name VARCHAR(100) NOT NULL, severity VARCHAR(50) DEFAULT 'Mild', reaction_description TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE)",
                    'patient_conditions' => "CREATE TABLE IF NOT EXISTS patient_conditions (id INT(11) AUTO_INCREMENT PRIMARY KEY, patient_id INT(11) NOT NULL, condition_name VARCHAR(200) NOT NULL, status VARCHAR(50) DEFAULT 'Active', diagnosed_date DATE, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE)",
                    'prescriptions' => "CREATE TABLE IF NOT EXISTS prescriptions (id INT(11) AUTO_INCREMENT PRIMARY KEY, patient_id INT(11) NOT NULL, medication_name VARCHAR(200) NOT NULL, dosage VARCHAR(100), frequency VARCHAR(100), duration VARCHAR(100), status VARCHAR(50) DEFAULT 'Active', prescribed_date DATE, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE)",
                    'vital_signs' => "CREATE TABLE IF NOT EXISTS vital_signs (id INT(11) AUTO_INCREMENT PRIMARY KEY, patient_id INT(11) NOT NULL, blood_pressure VARCHAR(50), heart_rate VARCHAR(50), temperature VARCHAR(50), weight VARCHAR(50), height VARCHAR(50), recorded_date DATE, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE)",
                    'lab_results' => "CREATE TABLE IF NOT EXISTS lab_results (id INT(11) AUTO_INCREMENT PRIMARY KEY, patient_id INT(11) NOT NULL, test_name VARCHAR(200) NOT NULL, test_result TEXT, test_date DATE, notes TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE)",
                    'medical_consultations' => "CREATE TABLE IF NOT EXISTS medical_consultations (id INT(11) AUTO_INCREMENT PRIMARY KEY, patient_id INT(11) NOT NULL, consultation_date DATE, chief_complaint TEXT, diagnosis TEXT, treatment_plan TEXT, notes TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE)"
                ];

                // Create missing tables
                foreach($required_tables as $table_name => $create_sql) {
                    $table_exists = mysqli_query($con, "SHOW TABLES LIKE '$table_name'");
                    if(mysqli_num_rows($table_exists) == 0) {
                        if(!mysqli_query($con, $create_sql)) {
                            $msg = "Failed to create required table: $table_name - " . mysqli_error($con);
                            $message_type = 'danger';
                            break;
                        }
                    }
                }

                if(empty($msg)) {
                    // Start transaction
                    mysqli_begin_transaction($con);
                    
                    try {
                        $imported_count = 0;
                        $errors = [];

                        // Handle single patient or array of patients
                        $patients = is_array($data) && isset($data[0]) ? $data : [$data];

                        foreach($patients as $patientData) {
                            // Extract patient info
                            $patient_info = isset($patientData['patient_info']) ? $patientData['patient_info'] : $patientData;
                            
                            // Generate unique email if not provided
                            if(empty($patient_info['email'])) {
                                $patient_info['email'] = 'patient_' . uniqid() . '@hospital.com';
                            }

                            // Check if email already exists
                            $check_email = mysqli_query($con, "SELECT id FROM users WHERE email = '" . mysqli_real_escape_string($con, $patient_info['email']) . "'");
                            if(mysqli_num_rows($check_email) > 0) {
                                $errors[] = "Email {$patient_info['email']} already exists";
                                continue;
                            }

                            // Insert into users table
                            $insert_query = "INSERT INTO users (fullName, email, password, address, city, gender, regDate, updationDate) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())";
                            
                            $stmt = mysqli_prepare($con, $insert_query);
                            if($stmt) {
                                $hashed_password = md5('password123'); // Default password
                                mysqli_stmt_bind_param($stmt, 'ssssss', 
                                    $patient_info['fullName'],
                                    $patient_info['email'],
                                    $hashed_password,
                                    $patient_info['address'],
                                    $patient_info['city'],
                                    $patient_info['gender']
                                );
                                
                                if(mysqli_stmt_execute($stmt)) {
                                    $patient_id = mysqli_insert_id($con);
                                    $imported_count++;
                                    
                                    // Import allergies if present
                                    if(isset($patientData['allergies']) && is_array($patientData['allergies'])) {
                                        foreach($patientData['allergies'] as $allergy) {
                                            $allergy_query = "INSERT INTO patient_allergies (patient_id, allergy_name, severity, reaction_description, created_at) VALUES (?, ?, ?, ?, NOW())";
                                            $allergy_stmt = mysqli_prepare($con, $allergy_query);
                                            if($allergy_stmt) {
                                                mysqli_stmt_bind_param($allergy_stmt, 'isss', 
                                                    $patient_id,
                                                    $allergy['allergy_name'],
                                                    $allergy['severity'],
                                                    $allergy['reaction_description']
                                                );
                                                mysqli_stmt_execute($allergy_stmt);
                                                mysqli_stmt_close($allergy_stmt);
                                            }
                                        }
                                    }

                                    // Import conditions if present
                                    if(isset($patientData['conditions']) && is_array($patientData['conditions'])) {
                                        foreach($patientData['conditions'] as $condition) {
                                            $condition_query = "INSERT INTO patient_conditions (patient_id, condition_name, status, diagnosed_date, created_at) VALUES (?, ?, ?, ?, NOW())";
                                            $condition_stmt = mysqli_prepare($con, $condition_query);
                                            if($condition_stmt) {
                                                mysqli_stmt_bind_param($condition_stmt, 'isss', 
                                                    $patient_id,
                                                    $condition['condition_name'],
                                                    $condition['status'],
                                                    $condition['diagnosed_date']
                                                );
                                                mysqli_stmt_execute($condition_stmt);
                                                mysqli_stmt_close($condition_stmt);
                                            }
                                        }
                                    }

                                    // Import prescriptions if present
                                    if(isset($patientData['prescriptions']) && is_array($patientData['prescriptions'])) {
                                        foreach($patientData['prescriptions'] as $prescription) {
                                            $prescription_query = "INSERT INTO prescriptions (patient_id, medication_name, dosage, frequency, duration, status, prescribed_date, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
                                            $prescription_stmt = mysqli_prepare($con, $prescription_query);
                                            if($prescription_stmt) {
                                                mysqli_stmt_bind_param($prescription_stmt, 'issssss', 
                                                    $patient_id,
                                                    $prescription['medication_name'],
                                                    $prescription['dosage'],
                                                    $prescription['frequency'],
                                                    $prescription['duration'],
                                                    $prescription['status'],
                                                    $prescription['prescribed_date']
                                                );
                                                mysqli_stmt_execute($prescription_stmt);
                                                mysqli_stmt_close($prescription_stmt);
                                            }
                                        }
                                    }

                                    // Import vital signs if present
                                    if(isset($patientData['vital_signs']) && is_array($patientData['vital_signs'])) {
                                        foreach($patientData['vital_signs'] as $vital) {
                                            $vital_query = "INSERT INTO vital_signs (patient_id, blood_pressure, heart_rate, temperature, weight, height, recorded_date, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
                                            $vital_stmt = mysqli_prepare($con, $vital_query);
                                            if($vital_stmt) {
                                                mysqli_stmt_bind_param($vital_stmt, 'issssss', 
                                                    $patient_id,
                                                    $vital['blood_pressure'],
                                                    $vital['heart_rate'],
                                                    $vital['temperature'],
                                                    $vital['weight'],
                                                    $vital['height'],
                                                    $vital['recorded_date']
                                                );
                                                mysqli_stmt_execute($vital_stmt);
                                                mysqli_stmt_close($vital_stmt);
                                            }
                                        }
                                    }

                                    // Import lab results if present
                                    if(isset($patientData['lab_results']) && is_array($patientData['lab_results'])) {
                                        foreach($patientData['lab_results'] as $lab) {
                                            $lab_query = "INSERT INTO lab_results (patient_id, test_name, test_result, test_date, notes, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
                                            $lab_stmt = mysqli_prepare($con, $lab_query);
                                            if($lab_stmt) {
                                                mysqli_stmt_bind_param($lab_stmt, 'issss', 
                                                    $patient_id,
                                                    $lab['test_name'],
                                                    $lab['test_result'],
                                                    $lab['test_date'],
                                                    $lab['notes']
                                                );
                                                mysqli_stmt_execute($lab_stmt);
                                                mysqli_stmt_close($lab_stmt);
                                            }
                                        }
                                    }

                                    // Import consultations if present
                                    if(isset($patientData['consultations']) && is_array($patientData['consultations'])) {
                                        foreach($patientData['consultations'] as $consultation) {
                                            $consultation_query = "INSERT INTO medical_consultations (patient_id, consultation_date, chief_complaint, diagnosis, treatment_plan, notes, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())";
                                            $consultation_stmt = mysqli_prepare($con, $consultation_query);
                                            if($consultation_stmt) {
                                                mysqli_stmt_bind_param($consultation_stmt, 'isssss', 
                                                    $patient_id,
                                                    $consultation['consultation_date'],
                                                    $consultation['chief_complaint'],
                                                    $consultation['diagnosis'],
                                                    $consultation['treatment_plan'],
                                                    $consultation['notes']
                                                );
                                                mysqli_stmt_execute($consultation_stmt);
                                                mysqli_stmt_close($consultation_stmt);
                                            }
                                        }
                                    }

                                } else {
                                    $errors[] = "Failed to import {$patient_info['fullName']}: " . mysqli_error($con);
                                }
                                mysqli_stmt_close($stmt);
                            } else {
                                $errors[] = "Failed to prepare statement for {$patient_info['fullName']}";
                            }
                        }

                        if($imported_count > 0) {
                            mysqli_commit($con);
                            $msg = "Successfully imported {$imported_count} patient(s) with complete medical records!";
                            $message_type = 'success';
                            
                            if(!empty($errors)) {
                                $msg .= " Some errors occurred: " . implode(', ', $errors);
                                $message_type = 'warning';
                            }
                        } else {
                            mysqli_rollback($con);
                            $msg = "No patients were imported. Errors: " . implode(', ', $errors);
                            $message_type = 'danger';
                        }

                    } catch(Exception $e) {
                        mysqli_rollback($con);
                        $msg = "Import failed: " . $e->getMessage();
                        $message_type = 'danger';
                    }
                }
            }
        }
    } else {
        $msg = 'Please select a file to import!';
        $message_type = 'danger';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin | Import Patient Data</title>
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    
    <style>
        .import-container {
            background-color: #f8f9fa;
            padding: 30px;
            border-radius: 8px;
            border: 2px dashed #dee2e6;
            text-align: center;
            margin-bottom: 30px;
        }
        .import-container.dragover {
            border-color: #007bff;
            background-color: #e3f2fd;
        }
        .file-info {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
            border: 1px solid #dee2e6;
        }
        .json-preview {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            max-height: 300px;
            overflow-y: auto;
            font-family: 'Courier New', monospace;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div id="app">
        <?php include('include/header.php');?>
        
        <div class="main-container">
            <?php include('include/sidebar.php');?>
            
            <div class="main-content">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-white">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <i class="fa fa-upload"></i> Import Patient Data from JSON
                                    </h4>
                                </div>
                                <div class="panel-body">
                                    <!-- Alert Messages -->
                                    <?php if(!empty($msg)): ?>
                                    <div class="alert alert-<?php echo $message_type; ?> alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                                        <?php echo $msg; ?>
                                    </div>
                                    <?php endif; ?>

                                    <!-- Instructions -->
                                    <div class="alert alert-info">
                                        <h5><i class="fa fa-info-circle"></i> Import Instructions</h5>
                                        <p>Upload a JSON file containing patient data. The system will automatically create required tables if they don't exist.</p>
                                        <a href="sample-patient-import.json" class="btn btn-info" download>
                                            <i class="fa fa-download"></i> Download Sample JSON
                                        </a>
                                    </div>

                                    <!-- Import Form -->
                                    <div class="import-container" id="importContainer">
                                        <form role="form" method="post" enctype="multipart/form-data" id="importForm">
                                            <div class="form-group">
                                                <label for="json_file" style="font-size: 18px; font-weight: bold; cursor: pointer;">
                                                    <i class="fa fa-cloud-upload fa-2x"></i><br><br>
                                                    Choose JSON File or Drag & Drop Here<br>
                                                    <small class="text-muted">Click here to browse files</small>
                                                </label>
                                                <input type="file" name="json_file" id="json_file" class="form-control" 
                                                       accept=".json" style="opacity: 0; position: absolute; z-index: -1;" required>
                                                <div class="file-info" id="fileInfo" style="display: none;">
                                                    <h6><i class="fa fa-file"></i> Selected File</h6>
                                                    <p id="fileName"></p>
                                                    <p id="fileSize"></p>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <button type="submit" name="import" class="btn btn-primary" id="importBtn" disabled>
                                                    <i class="fa fa-upload"></i> Import Patients
                                                </button>
                                                <button type="button" class="btn btn-success" id="previewBtn" disabled>
                                                    <i class="fa fa-eye"></i> Preview Data
                                                </button>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- JSON Preview -->
                                    <div id="jsonPreview" style="display: none;">
                                        <h6><i class="fa fa-code"></i> JSON Preview</h6>
                                        <div class="json-preview" id="jsonContent"></div>
                                    </div>

                                    <!-- Import Summary -->
                                    <div id="importSummary" style="display: none;">
                                        <h6><i class="fa fa-list"></i> Import Summary</h6>
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Section</th>
                                                        <th>Fields</th>
                                                        <th>Required</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><strong>Patient Info</strong></td>
                                                        <td>fullName, email, gender, address, city</td>
                                                        <td><span class="label label-success">Yes</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Allergies</strong></td>
                                                        <td>allergy_name, severity, reaction_description</td>
                                                        <td><span class="label label-warning">Optional</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Medical Conditions</strong></td>
                                                        <td>condition_name, status, diagnosed_date</td>
                                                        <td><span class="label label-warning">Optional</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Prescriptions</strong></td>
                                                        <td>medication_name, dosage, frequency, duration, status, prescribed_date</td>
                                                        <td><span class="label label-warning">Optional</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Vital Signs</strong></td>
                                                        <td>blood_pressure, heart_rate, temperature, weight, height, recorded_date</td>
                                                        <td><span class="label label-warning">Optional</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Lab Results</strong></td>
                                                        <td>test_name, test_result, test_date, notes</td>
                                                        <td><span class="label label-warning">Optional</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Consultations</strong></td>
                                                        <td>consultation_date, chief_complaint, diagnosis, treatment_plan, notes</td>
                                                        <td><span class="label label-warning">Optional</span></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
        jQuery(document).ready(function() {
            // Make label clickable
            $('label[for="json_file"]').click(function() {
                $('#json_file').click();
            });
            
            // File selection handling
            $('#json_file').change(function() {
                const file = this.files[0];
                
                if (file) {
                    $('#fileName').text('Name: ' + file.name);
                    $('#fileSize').text('Size: ' + (file.size / 1024).toFixed(2) + ' KB');
                    $('#fileInfo').show();
                    
                    // Enable buttons
                    $('#importBtn').prop('disabled', false);
                    $('#previewBtn').prop('disabled', false);
                    
                    // Show success message
                    $('.file-info').addClass('alert alert-success');
                }
            });
        
            // Drag and drop functionality
            const importContainer = document.getElementById('importContainer');
            const fileInput = document.getElementById('json_file');
            
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                importContainer.addEventListener(eventName, preventDefaults, false);
            });
            
            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            ['dragenter', 'dragover'].forEach(eventName => {
                importContainer.addEventListener(eventName, highlight, false);
            });
            
            ['dragleave', 'drop'].forEach(eventName => {
                importContainer.addEventListener(eventName, unhighlight, false);
            });
            
            function highlight(e) {
                importContainer.classList.add('dragover');
            }
            
            function unhighlight(e) {
                importContainer.classList.remove('dragover');
            }
            
            importContainer.addEventListener('drop', handleDrop, false);
            
            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                
                if (files.length > 0) {
                    fileInput.files = files;
                    $('#json_file').trigger('change');
                }
            }
            
            // Preview functionality
            $('#previewBtn').on('click', function(e) {
                e.preventDefault();
                const file = document.getElementById('json_file').files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        try {
                            const data = JSON.parse(e.target.result);
                            $('#jsonContent').html(JSON.stringify(data, null, 2));
                            $('#jsonPreview').show();
                            $('#importSummary').show();
                        } catch (error) {
                            console.error('JSON parse error:', error);
                            alert('Invalid JSON file!');
                        }
                    };
                    reader.readAsText(file);
                } else {
                    alert('Please select a file first!');
                }
            });
            
            // Import functionality
            $('#importBtn').on('click', function(e) {
                e.preventDefault();
                const file = document.getElementById('json_file').files[0];
                if (file) {
                    $('#importForm').submit();
                } else {
                    alert('Please select a file first!');
                }
            });
        });
    </script>
</body>
</html>
