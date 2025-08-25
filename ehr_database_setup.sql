-- Electronic Health Records (EHR) Database Setup

-- Create patient_medical_records table for comprehensive patient records
CREATE TABLE IF NOT EXISTS patient_medical_records (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    patient_id INT(11) NOT NULL,
    blood_type VARCHAR(5) DEFAULT NULL,
    height DECIMAL(5,2) DEFAULT NULL COMMENT 'Height in cm',
    weight DECIMAL(5,2) DEFAULT NULL COMMENT 'Weight in kg',
    emergency_contact_name VARCHAR(100) DEFAULT NULL,
    emergency_contact_phone VARCHAR(20) DEFAULT NULL,
    insurance_provider VARCHAR(100) DEFAULT NULL,
    insurance_number VARCHAR(50) DEFAULT NULL,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_patient (patient_id)
);

-- Create medical_consultations table for consultation history
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
    vital_signs JSON DEFAULT NULL COMMENT 'BP, HR, Temperature, etc.',
    follow_up_required TINYINT(1) DEFAULT 0,
    follow_up_date DATE DEFAULT NULL,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    FOREIGN KEY (appointment_id) REFERENCES appointment(id) ON DELETE SET NULL
);

-- Create patient_allergies table
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
);

-- Create patient_conditions table for chronic/ongoing conditions
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
);

-- Create prescriptions table
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
);

-- Create lab_results table for test results
CREATE TABLE IF NOT EXISTS lab_results (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    patient_id INT(11) NOT NULL,
    doctor_id INT(11) NOT NULL,
    consultation_id INT(11) DEFAULT NULL,
    test_name VARCHAR(200) NOT NULL,
    test_type VARCHAR(100) DEFAULT NULL,
    test_date DATE NOT NULL,
    results TEXT DEFAULT NULL,
    reference_range VARCHAR(100) DEFAULT NULL,
    status ENUM('Normal', 'Abnormal', 'Critical', 'Pending') DEFAULT 'Pending',
    notes TEXT DEFAULT NULL,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    FOREIGN KEY (consultation_id) REFERENCES medical_consultations(id) ON DELETE SET NULL
);

-- Create vital_signs table for tracking vital signs over time
CREATE TABLE IF NOT EXISTS vital_signs (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    patient_id INT(11) NOT NULL,
    consultation_id INT(11) DEFAULT NULL,
    recorded_date DATETIME NOT NULL,
    systolic_bp INT(3) DEFAULT NULL,
    diastolic_bp INT(3) DEFAULT NULL,
    heart_rate INT(3) DEFAULT NULL,
    temperature DECIMAL(4,1) DEFAULT NULL COMMENT 'Temperature in Celsius',
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
);

-- Insert sample data for testing
INSERT IGNORE INTO patient_medical_records (patient_id, blood_type, height, weight, emergency_contact_name, emergency_contact_phone) VALUES
(1, 'A+', 175.5, 70.2, 'John Emergency', '+1234567890'),
(2, 'O-', 162.0, 55.8, 'Jane Emergency', '+0987654321');

-- Insert sample allergies
INSERT IGNORE INTO patient_allergies (patient_id, allergy_name, allergy_type, severity, reaction_description) VALUES
(1, 'Penicillin', 'Drug', 'Severe', 'Causes severe rash and breathing difficulties'),
(1, 'Peanuts', 'Food', 'Life-threatening', 'Anaphylactic shock'),
(2, 'Dust Mites', 'Environmental', 'Moderate', 'Causes sneezing and watery eyes');

-- Insert sample conditions
INSERT IGNORE INTO patient_conditions (patient_id, condition_name, condition_type, diagnosed_date, status) VALUES
(1, 'Hypertension', 'Chronic', '2023-01-15', 'Active'),
(1, 'Type 2 Diabetes', 'Chronic', '2022-08-20', 'Active'),
(2, 'Asthma', 'Chronic', '2021-05-10', 'Active');

-- Create indexes for better performance
CREATE INDEX idx_patient_consultations ON medical_consultations(patient_id, consultation_date);
CREATE INDEX idx_patient_allergies ON patient_allergies(patient_id, is_active);
CREATE INDEX idx_patient_conditions ON patient_conditions(patient_id, status);
CREATE INDEX idx_prescriptions_patient ON prescriptions(patient_id, status);
CREATE INDEX idx_vital_signs_patient ON vital_signs(patient_id, recorded_date);

-- Success message
SELECT 'EHR Database tables created successfully!' as Status;
