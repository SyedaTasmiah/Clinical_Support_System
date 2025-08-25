<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('include/config.php');

echo "<h2>Setting up Doctor Schedule Tables...</h2>";

// Create doctor_schedule table
$sql1 = "CREATE TABLE IF NOT EXISTS doctor_schedule (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT(11) NOT NULL,
    day_of_week ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday') NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    slot_duration INT(11) DEFAULT 30,
    is_active TINYINT(1) DEFAULT 1,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_doctor_day (doctor_id, day_of_week)
)";

if (mysqli_query($con, $sql1)) {
    echo "<p style='color: green;'>✓ doctor_schedule table created successfully</p>";
} else {
    echo "<p style='color: red;'>✗ Error creating doctor_schedule table: " . mysqli_error($con) . "</p>";
}

// Create appointment_slots table (optional)
$sql2 = "CREATE TABLE IF NOT EXISTS appointment_slots (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT(11) NOT NULL,
    appointment_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    is_booked TINYINT(1) DEFAULT 0,
    appointment_id INT(11) NULL,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_doctor_datetime (doctor_id, appointment_date, start_time)
)";

if (mysqli_query($con, $sql2)) {
    echo "<p style='color: green;'>✓ appointment_slots table created successfully</p>";
} else {
    echo "<p style='color: red;'>✗ Error creating appointment_slots table: " . mysqli_error($con) . "</p>";
}

// Check if tables exist
$check1 = mysqli_query($con, "SHOW TABLES LIKE 'doctor_schedule'");
$check2 = mysqli_query($con, "SHOW TABLES LIKE 'appointment_slots'");

echo "<h3>Table Status:</h3>";
echo "<p>doctor_schedule table exists: " . (mysqli_num_rows($check1) > 0 ? "Yes" : "No") . "</p>";
echo "<p>appointment_slots table exists: " . (mysqli_num_rows($check2) > 0 ? "Yes" : "No") . "</p>";

echo "<h3>Sample Data Insertion:</h3>";
// Insert sample schedule for testing (if doctor with ID 1 exists)
$doctor_check = mysqli_query($con, "SELECT id FROM doctors LIMIT 1");
if(mysqli_num_rows($doctor_check) > 0) {
    $doctor = mysqli_fetch_array($doctor_check);
    $doctor_id = $doctor['id'];
    
    // Insert sample schedule
    $sample_sql = "INSERT IGNORE INTO doctor_schedule (doctor_id, day_of_week, start_time, end_time, slot_duration) VALUES
    ('$doctor_id', 'Monday', '09:00:00', '17:00:00', 30),
    ('$doctor_id', 'Tuesday', '09:00:00', '17:00:00', 30),
    ('$doctor_id', 'Wednesday', '09:00:00', '17:00:00', 30),
    ('$doctor_id', 'Thursday', '09:00:00', '17:00:00', 30),
    ('$doctor_id', 'Friday', '09:00:00', '17:00:00', 30)";
    
    if (mysqli_query($con, $sample_sql)) {
        echo "<p style='color: green;'>✓ Sample schedule data inserted for doctor ID: $doctor_id</p>";
    } else {
        echo "<p style='color: orange;'>Sample data may already exist or error: " . mysqli_error($con) . "</p>";
    }
} else {
    echo "<p style='color: orange;'>No doctors found in database to create sample schedule</p>";
}

echo "<h3>Next Steps:</h3>";
echo "<p>1. Tables are now created</p>";
echo "<p>2. Try accessing <a href='doctor/manage-schedule.php'>manage-schedule.php</a> again</p>";
echo "<p>3. Make sure you're logged in as a doctor</p>";

mysqli_close($con);
?>
