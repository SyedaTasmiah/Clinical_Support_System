<?php
session_start();
error_reporting(0);
include('../include/config.php');

// Check if user is admin
if(strlen($_SESSION['id'])==0) {
    header('location:index.php');
    exit();
}

$user_id = $_SESSION['id'];
$patient_id = isset($_GET['patient_id']) ? intval($_GET['patient_id']) : 0;
$format = isset($_GET['format']) ? $_GET['format'] : 'pdf';

if(!$patient_id) {
    echo "<script>alert('Invalid patient ID'); window.history.back();</script>";
    exit();
}

// Get patient information
$patient_query = mysqli_query($con, "SELECT * FROM users WHERE id='$patient_id'");
$patient = mysqli_fetch_array($patient_query);

if(!$patient) {
    echo "<script>alert('Patient not found'); window.history.back();</script>";
    exit();
}

// Collect all medical data
$medical_data = [
    'patient_info' => $patient,
    'consultations' => [],
    'prescriptions' => [],
    'allergies' => [],
    'conditions' => [],
    'vital_signs' => [],
    'lab_results' => []
];

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

// Get lab results (if table exists)
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

// Export based on format
if($format == 'json') {
    // JSON Export
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="admin_medical_history_' . $patient['fullName'] . '_' . date('Y-m-d') . '.json"');
    
    // Clean the data for JSON export
    $json_data = [];
    foreach($medical_data as $key => $value) {
        if(is_array($value)) {
            $json_data[$key] = array_map(function($item) {
                if(is_array($item)) {
                    // Remove numeric indices
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
    
    echo json_encode($json_data, JSON_PRETTY_PRINT);
    exit();
    
} else {
    // PDF Export
    include('../include/simple-pdf.php');
    
    $pdf = new SimplePDF();
    $pdf->addPage();
    
    // Header
    $pdf->setFont('Arial', 'B', 16);
    $pdf->cell(0, 10, 'Medical History Report - ADMIN EXPORT', 0, 1, 'C');
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
    
    // Current Prescriptions
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
    
    // Recent Consultations
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
    $pdf->cell(0, 10, 'Generated on ' . date('F d, Y H:i:s') . ' | Clinical Support System - ADMIN EXPORT', 0, 0, 'C');
    
    $filename = 'admin_medical_history_' . preg_replace('/[^a-zA-Z0-9]/', '_', $patient['fullName']) . '_' . date('Y-m-d') . '.pdf';
    $pdf->output($filename, 'D');
}
?>
