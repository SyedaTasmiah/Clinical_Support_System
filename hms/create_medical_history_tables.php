<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('include/config.php');

echo "<h2>Creating Medical History Tables...</h2>";

// Create medical_consultations table
$consultation_table = "
CREATE TABLE IF NOT EXISTS medical_consultations (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    patient_id INT(11) NOT NULL,
    doctor_id INT(11) NOT NULL,
    appointment_id INT(11) DEFAULT NULL,
    consultation_date DATE NOT NULL,
    chief_complaint TEXT DEFAULT NULL,
    symptoms TEXT DEFAULT NULL,
    diagnosis TEXT DEFAULT NULL,
    treatment_plan TEXT DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    vital_signs JSON DEFAULT NULL,
    follow_up_required TINYINT(1) DEFAULT 0,
    follow_up_date DATE DEFAULT NULL,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    FOREIGN KEY (appointment_id) REFERENCES appointment(id) ON DELETE SET NULL
)";

if(mysqli_query($con, $consultation_table)) {
    echo "<p style='color: green;'>✓ medical_consultations table created successfully</p>";
} else {
    echo "<p style='color: red;'>✗ Error creating medical_consultations table: " . mysqli_error($con) . "</p>";
}

// Create prescriptions table
$prescriptions_table = "
CREATE TABLE IF NOT EXISTS prescriptions (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    patient_id INT(11) NOT NULL,
    doctor_id INT(11) NOT NULL,
    consultation_id INT(11) DEFAULT NULL,
    medication_name VARCHAR(200) NOT NULL,
    dosage VARCHAR(100) NOT NULL,
    frequency VARCHAR(100) NOT NULL,
    duration VARCHAR(100) NOT NULL,
    instructions TEXT DEFAULT NULL,
    prescribed_date DATE NOT NULL,
    start_date DATE DEFAULT NULL,
    end_date DATE DEFAULT NULL,
    status ENUM('Active', 'Completed', 'Discontinued', 'On Hold') DEFAULT 'Active',
    refills_remaining INT(3) DEFAULT 0,
    pharmacy_notes TEXT DEFAULT NULL,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    FOREIGN KEY (consultation_id) REFERENCES medical_consultations(id) ON DELETE SET NULL
)";

if(mysqli_query($con, $prescriptions_table)) {
    echo "<p style='color: green;'>✓ prescriptions table created successfully</p>";
} else {
    echo "<p style='color: red;'>✗ Error creating prescriptions table: " . mysqli_error($con) . "</p>";
}

// Create patient_allergies table
$allergies_table = "
CREATE TABLE IF NOT EXISTS patient_allergies (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    patient_id INT(11) NOT NULL,
    allergy_name VARCHAR(100) NOT NULL,
    allergy_type ENUM('Food', 'Drug', 'Environmental', 'Other') DEFAULT 'Other',
    severity ENUM('Mild', 'Moderate', 'Severe', 'Life-threatening') DEFAULT 'Mild',
    reaction_description TEXT DEFAULT NULL,
    diagnosed_date DATE DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE
)";

if(mysqli_query($con, $allergies_table)) {
    echo "<p style='color: green;'>✓ patient_allergies table created successfully</p>";
} else {
    echo "<p style='color: red;'>✗ Error creating patient_allergies table: " . mysqli_error($con) . "</p>";
}

// Create patient_conditions table
$conditions_table = "
CREATE TABLE IF NOT EXISTS patient_conditions (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    patient_id INT(11) NOT NULL,
    condition_name VARCHAR(200) NOT NULL,
    condition_type ENUM('Chronic', 'Acute', 'Resolved') DEFAULT 'Chronic',
    diagnosed_date DATE DEFAULT NULL,
    diagnosed_by INT(11) DEFAULT NULL,
    status ENUM('Active', 'Inactive', 'Resolved') DEFAULT 'Active',
    notes TEXT DEFAULT NULL,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (diagnosed_by) REFERENCES doctors(id) ON DELETE SET NULL
)";

if(mysqli_query($con, $conditions_table)) {
    echo "<p style='color: green;'>✓ patient_conditions table created successfully</p>";
} else {
    echo "<p style='color: red;'>✗ Error creating patient_conditions table: " . mysqli_error($con) . "</p>";
}

// Create vital_signs table
$vital_signs_table = "
CREATE TABLE IF NOT EXISTS vital_signs (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    patient_id INT(11) NOT NULL,
    consultation_id INT(11) DEFAULT NULL,
    recorded_date DATETIME NOT NULL,
    systolic_bp INT(3) DEFAULT NULL,
    diastolic_bp INT(3) DEFAULT NULL,
    heart_rate INT(3) DEFAULT NULL,
    temperature DECIMAL(4,1) DEFAULT NULL,
    respiratory_rate INT(3) DEFAULT NULL,
    oxygen_saturation INT(3) DEFAULT NULL,
    weight DECIMAL(5,2) DEFAULT NULL,
    height DECIMAL(5,2) DEFAULT NULL,
    bmi DECIMAL(4,1) DEFAULT NULL,
    recorded_by INT(11) DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (consultation_id) REFERENCES medical_consultations(id) ON DELETE SET NULL,
    FOREIGN KEY (recorded_by) REFERENCES doctors(id) ON DELETE SET NULL
)";

if(mysqli_query($con, $vital_signs_table)) {
    echo "<p style='color: green;'>✓ vital_signs table created successfully</p>";
} else {
    echo "<p style='color: red;'>✗ Error creating vital_signs table: " . mysqli_error($con) . "</p>";
}

echo "<h3>Database tables created successfully!</h3>";
echo "<p>You can now use the medical history management system.</p>";

mysqli_close($con);
?>
