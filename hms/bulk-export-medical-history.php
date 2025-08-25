<?php
session_start();
error_reporting(0);
include('include/config.php');

// Check if user is admin
$user_id = isset($_SESSION['id']) ? $_SESSION['id'] : 0;

if(!$user_id) {
    header('location:index.php');
    exit();
}

// Check if user is an admin
$admin_check = mysqli_query($con, "SELECT id FROM admin WHERE id='$user_id'");
$is_admin = mysqli_num_rows($admin_check) > 0;

if(!$is_admin) {
    echo "<script>alert('Access denied. Admin privileges required.'); window.history.back();</script>";
    exit();
}

$patient_ids = isset($_GET['patient_ids']) ? $_GET['patient_ids'] : '';
$format = isset($_GET['format']) ? $_GET['format'] : 'pdf';

if(!$patient_ids) {
    echo "<script>alert('No patients selected for export'); window.history.back();</script>";
    exit();
}

$patient_id_array = explode(',', $patient_ids);
$patient_id_array = array_map('intval', $patient_id_array);
$patient_id_array = array_filter($patient_id_array);

if(empty($patient_id_array)) {
    echo "<script>alert('Invalid patient IDs'); window.history.back();</script>";
    exit();
}

// Create temporary directory for exports
$temp_dir = 'temp_exports/' . uniqid('bulk_export_');
if(!is_dir('temp_exports')) {
    mkdir('temp_exports', 0755, true);
}
mkdir($temp_dir, 0755, true);

$exported_files = [];
$errors = [];

// Process each patient
foreach($patient_id_array as $patient_id) {
    try {
        // Get patient information
        $patient_query = mysqli_query($con, "SELECT * FROM users WHERE id='$patient_id'");
        $patient = mysqli_fetch_array($patient_query);
        
        if(!$patient) {
            $errors[] = "Patient ID $patient_id not found";
            continue;
        }
        
        // Collect medical data for this patient
        $medical_data = collectPatientData($con, $patient_id);
        
        if($format == 'json') {
            $filename = exportPatientJSON($medical_data, $patient, $temp_dir);
        } else {
            $filename = exportPatientPDF($medical_data, $patient, $temp_dir);
        }
        
        if($filename) {
            $exported_files[] = $filename;
        }
        
    } catch (Exception $e) {
        $errors[] = "Error exporting patient $patient_id: " . $e->getMessage();
    }
}

// Create ZIP file if we have exported files
if(!empty($exported_files)) {
    $zip_filename = createBulkExportZip($exported_files, $temp_dir, $format);
    
    if($zip_filename) {
        // Clean up temporary files
        foreach($exported_files as $file) {
            if(file_exists($file)) {
                unlink($file);
            }
        }
        rmdir($temp_dir);
        
        // Download the ZIP file
        $download_name = 'bulk_patient_ehr_export_' . date('Y-m-d_H-i-s') . '.zip';
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $download_name . '"');
        header('Content-Length: ' . filesize($zip_filename));
        readfile($zip_filename);
        unlink($zip_filename);
        exit();
    }
}

// If we get here, there was an error
echo "<script>alert('Export failed. Please try again.'); window.history.back();</script>";

// Function to collect patient medical data
function collectPatientData($con, $patient_id) {
    $medical_data = [
        'patient_info' => null,
        'consultations' => [],
        'prescriptions' => [],
        'allergies' => [],
        'conditions' => [],
        'vital_signs' => [],
        'lab_results' => []
    ];
    
    // Get patient info
    $patient_query = mysqli_query($con, "SELECT * FROM users WHERE id='$patient_id'");
    $medical_data['patient_info'] = mysqli_fetch_array($patient_query);
    
    // Get consultations
    $consultations_query = mysqli_query($con, "
        SELECT mc.*, d.doctorName, d.specilization 
        FROM medical_consultations mc 
        LEFT JOIN doctors d ON mc.doctor_id = d.id 
        WHERE mc.patient_id='$patient_id' 
        ORDER BY mc.consultation_date DESC
    ");
    while($consultation = mysqli_fetch_array($consultations_query)) {
        $medical_data['consultations'][] = $consultation;
    }
    
    // Get prescriptions
    $prescriptions_query = mysqli_query($con, "
        SELECT p.*, d.doctorName 
        FROM prescriptions p 
        LEFT JOIN doctors d ON p.doctor_id = d.id 
        WHERE p.patient_id='$patient_id' 
        ORDER BY p.prescribed_date DESC
    ");
    while($prescription = mysqli_fetch_array($prescriptions_query)) {
        $medical_data['prescriptions'][] = $prescription;
    }
    
    // Get allergies
    $allergies_query = mysqli_query($con, "
        SELECT * FROM patient_allergies 
        WHERE patient_id='$patient_id' 
        ORDER BY severity DESC, allergy_name
    ");
    while($allergy = mysqli_fetch_array($allergies_query)) {
        $medical_data['allergies'][] = $allergy;
    }
    
    // Get conditions
    $conditions_query = mysqli_query($con, "
        SELECT pc.*, d.doctorName 
        FROM patient_conditions pc 
        LEFT JOIN doctors d ON pc.diagnosed_by = d.id 
        WHERE pc.patient_id='$patient_id' 
        ORDER BY pc.diagnosed_date DESC
    ");
    while($condition = mysqli_fetch_array($conditions_query)) {
        $medical_data['conditions'][] = $condition;
    }
    
    // Get vital signs
    $vitals_query = mysqli_query($con, "
        SELECT vs.*, d.doctorName 
        FROM vital_signs vs 
        LEFT JOIN doctors d ON vs.recorded_by = d.id 
        WHERE vs.patient_id='$patient_id' 
        ORDER BY vs.recorded_date DESC
    ");
    while($vital = mysqli_fetch_array($vitals_query)) {
        $medical_data['vital_signs'][] = $vital;
    }
    
    // Get lab results
    $lab_query = mysqli_query($con, "
        SELECT lr.*, d.doctorName 
        FROM lab_results lr 
        LEFT JOIN doctors d ON lr.doctor_id = d.id 
        WHERE lr.patient_id='$patient_id' 
        ORDER BY lr.test_date DESC
    ");
    if($lab_query) {
        while($lab = mysqli_fetch_array($lab_query)) {
            $medical_data['lab_results'][] = $lab;
        }
    }
    
    return $medical_data;
}

// Function to export patient data as JSON
function exportPatientJSON($medical_data, $patient, $temp_dir) {
    $filename = $temp_dir . '/medical_history_' . preg_replace('/[^a-zA-Z0-9]/', '_', $patient['fullName']) . '_' . date('Y-m-d') . '.json';
    
    // Clean the data for JSON export
    $json_data = [];
    foreach($medical_data as $key => $value) {
        if(is_array($value)) {
            $json_data[$key] = array_map(function($item) {
                if(is_array($item)) {
                    $clean_item = [];
                    foreach($item as $k => $v) {
                        if(!is_numeric($k)) {
                            $clean_item[$k] = $v;
                        }
                    }
                    return $clean_item;
                }
                return $item;
            }, $value);
        } else {
            $json_data[$key] = $value;
        }
    }
    
    $json_content = json_encode($json_data, JSON_PRETTY_PRINT);
    if(file_put_contents($filename, $json_content)) {
        return $filename;
    }
    return false;
}

// Function to export patient data as PDF
function exportPatientPDF($medical_data, $patient, $temp_dir) {
    $filename = $temp_dir . '/medical_history_' . preg_replace('/[^a-zA-Z0-9]/', '_', $patient['fullName']) . '_' . date('Y-m-d') . '.pdf';
    
    // Include PDF library
    include('include/simple-pdf.php');
    
    $pdf = new SimplePDF();
    $pdf->addPage();
    
    // Header
    $pdf->setFont('Arial', 'B', 16);
    $pdf->cell(0, 10, 'Medical History Report', 0, 1, 'C');
    $pdf->ln(5);
    
    // Patient Information
    $pdf->setFont('Arial', 'B', 14);
    $pdf->cell(0, 8, 'Patient Information', 0, 1);
    $pdf->setFont('Arial', '', 12);
    $pdf->cell(0, 6, 'Name: ' . $patient['fullName'], 0, 1);
    $pdf->cell(0, 6, 'Email: ' . $patient['email'], 0, 1);
    $pdf->cell(0, 6, 'Gender: ' . $patient['gender'], 0, 1);
    $pdf->cell(0, 6, 'Address: ' . $patient['address'] . ', ' . $patient['city'], 0, 1);
    $pdf->cell(0, 6, 'Registration Date: ' . date('F d, Y', strtotime($patient['regDate'])), 0, 1);
    $pdf->ln(5);
    
    // Allergies
    if(!empty($medical_data['allergies'])) {
        $pdf->setFont('Arial', 'B', 14);
        $pdf->cell(0, 8, 'Allergies', 0, 1);
        $pdf->setFont('Arial', '', 12);
        foreach($medical_data['allergies'] as $allergy) {
            $pdf->cell(0, 6, '• ' . $allergy['allergy_name'] . ' (' . $allergy['severity'] . ')', 0, 1);
            if($allergy['reaction_description']) {
                $pdf->cell(10, 6, '', 0, 0);
                $pdf->multiCell(0, 6, 'Reaction: ' . $allergy['reaction_description']);
            }
        }
        $pdf->ln(3);
    }
    
    // Medical Conditions
    if(!empty($medical_data['conditions'])) {
        $pdf->setFont('Arial', 'B', 14);
        $pdf->cell(0, 8, 'Medical Conditions', 0, 1);
        $pdf->setFont('Arial', '', 12);
        foreach($medical_data['conditions'] as $condition) {
            $pdf->cell(0, 6, '• ' . $condition['condition_name'] . ' (' . $condition['status'] . ')', 0, 1);
            if($condition['diagnosed_date']) {
                $pdf->cell(10, 6, '', 0, 0);
                $pdf->cell(0, 6, 'Diagnosed: ' . date('F d, Y', strtotime($condition['diagnosed_date'])), 0, 1);
            }
        }
        $pdf->ln(3);
    }
    
    // Prescriptions
    if(!empty($medical_data['prescriptions'])) {
        $pdf->setFont('Arial', 'B', 14);
        $pdf->cell(0, 8, 'Prescriptions', 0, 1);
        $pdf->setFont('Arial', '', 12);
        foreach(array_slice($medical_data['prescriptions'], 0, 10) as $prescription) {
            $pdf->cell(0, 6, '• ' . $prescription['medication_name'] . ' - ' . $prescription['dosage'], 0, 1);
            $pdf->cell(10, 6, '', 0, 0);
            $pdf->cell(0, 6, 'Frequency: ' . $prescription['frequency'] . ', Duration: ' . $prescription['duration'], 0, 1);
            $pdf->cell(10, 6, '', 0, 0);
            $pdf->cell(0, 6, 'Status: ' . $prescription['status'] . ', Prescribed: ' . date('M d, Y', strtotime($prescription['prescribed_date'])), 0, 1);
            $pdf->ln(2);
        }
    }
    
    // Consultations
    if(!empty($medical_data['consultations'])) {
        $pdf->addPage();
        $pdf->setFont('Arial', 'B', 14);
        $pdf->cell(0, 8, 'Recent Consultations', 0, 1);
        $pdf->setFont('Arial', '', 12);
        foreach(array_slice($medical_data['consultations'], 0, 5) as $consultation) {
            $pdf->setFont('Arial', 'B', 12);
            $pdf->cell(0, 6, date('F d, Y', strtotime($consultation['consultation_date'])) . ' - Dr. ' . $consultation['doctorName'], 0, 1);
            $pdf->setFont('Arial', '', 12);
            if($consultation['chief_complaint']) {
                $pdf->multiCell(0, 6, 'Chief Complaint: ' . $consultation['chief_complaint']);
            }
            if($consultation['diagnosis']) {
                $pdf->multiCell(0, 6, 'Diagnosis: ' . $consultation['diagnosis']);
            }
            if($consultation['treatment_plan']) {
                $pdf->multiCell(0, 6, 'Treatment: ' . $consultation['treatment_plan']);
            }
            $pdf->ln(3);
        }
    }
    
    // Footer
    $pdf->setY(-15);
    $pdf->setFont('Arial', 'I', 8);
    $pdf->cell(0, 10, 'Generated on ' . date('F d, Y H:i:s') . ' | Clinical Support System - Bulk Export', 0, 0, 'C');
    
    // Save to file
    $pdf_content = $pdf->output('', '', true);
    if(file_put_contents($filename, $pdf_content)) {
        return $filename;
    }
    return false;
}

// Function to create ZIP file
function createBulkExportZip($files, $temp_dir, $format) {
    $zip_filename = $temp_dir . '/bulk_export.zip';
    
    $zip = new ZipArchive();
    if($zip->open($zip_filename, ZipArchive::CREATE) !== TRUE) {
        return false;
    }
    
    foreach($files as $file) {
        if(file_exists($file)) {
            $zip->addFile($file, basename($file));
        }
    }
    
    $zip->close();
    
    if(file_exists($zip_filename)) {
        return $zip_filename;
    }
    return false;
}
?>
