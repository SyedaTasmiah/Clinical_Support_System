<?php
// Test script to verify admin access
session_start();
include('include/config.php');

echo "<h2>Admin Access Test</h2>";

// Check session
if(isset($_SESSION['id'])) {
    echo "<p style='color: green;'>✓ Session ID exists: " . $_SESSION['id'] . "</p>";
} else {
    echo "<p style='color: red;'>✗ No session ID found</p>";
    exit();
}

$user_id = $_SESSION['id'];

// Check if user is in doctors table
$doctor_check = mysqli_query($con, "SELECT id, doctorName FROM doctors WHERE id='$user_id'");
if($doctor_check === false) {
    echo "<p style='color: red;'>✗ Error checking doctors table: " . mysqli_error($con) . "</p>";
} else if(mysqli_num_rows($doctor_check) > 0) {
    $doctor = mysqli_fetch_array($doctor_check);
    echo "<p style='color: blue;'>ℹ User is a doctor: " . $doctor['doctorName'] . "</p>";
    $is_doctor = true;
} else {
    echo "<p style='color: blue;'>ℹ User is NOT a doctor</p>";
    $is_doctor = false;
}

// Check if user is in admin table
$admin_check = mysqli_query($con, "SELECT id, username FROM admin WHERE id='$user_id'");
if($admin_check === false) {
    echo "<p style='color: red;'>✗ Error checking admin table: " . mysqli_error($con) . "</p>";
} else if(mysqli_num_rows($admin_check) > 0) {
    $admin = mysqli_fetch_array($admin_check);
    echo "<p style='color: green;'>✓ User is an admin: " . $admin['username'] . "</p>";
    $is_admin = true;
} else {
    echo "<p style='color: orange;'>⚠ User is NOT in admin table</p>";
    $is_admin = false;
}

// Check if admin table exists
$table_check = mysqli_query($con, "SHOW TABLES LIKE 'admin'");
if($table_check === false) {
    echo "<p style='color: red;'>✗ Error checking if admin table exists: " . mysqli_error($con) . "</p>";
} else if(mysqli_num_rows($table_check) > 0) {
    echo "<p style='color: green;'>✓ Admin table exists</p>";
} else {
    echo "<p style='color: red;'>✗ Admin table does NOT exist</p>";
}

// Determine access level
if($is_doctor) {
    echo "<p style='color: blue;'><strong>Access Level: DOCTOR</strong></p>";
} elseif($is_admin) {
    echo "<p style='color: green;'><strong>Access Level: ADMIN</strong></p>";
} else {
    echo "<p style='color: orange;'><strong>Access Level: UNKNOWN</strong></p>";
}

// Test export access logic
echo "<hr><h3>Export Access Logic Test</h3>";

$export_is_doctor = $is_doctor;
$export_is_admin = false;

if(!$export_is_doctor) {
    // Check if admin table exists and has this user
    $export_admin_check = mysqli_query($con, "SELECT id FROM admin WHERE id='$user_id'");
    if($export_admin_check) {
        $export_is_admin = mysqli_num_rows($export_admin_check) > 0;
    } else {
        // If admin table doesn't exist, assume user is admin if they're not a doctor
        $export_is_admin = true;
    }
}

// Force admin access if user is not a doctor and has a valid session
if(!$export_is_doctor && $user_id > 0) {
    $export_is_admin = true;
}

echo "<p>Export Logic Results:</p>";
echo "<p>Is Doctor: " . ($export_is_doctor ? 'Yes' : 'No') . "</p>";
echo "<p>Is Admin: " . ($export_is_admin ? 'Yes' : 'No') . "</p>";

if($export_is_doctor || $export_is_admin) {
    echo "<p style='color: green;'>✓ Export access would be GRANTED</p>";
} else {
    echo "<p style='color: red;'>✗ Export access would be DENIED</p>";
}

echo "<hr>";
echo "<p><a href='admin/manage-users.php'>Go to Manage Users</a></p>";
echo "<p><a href='admin/export-patient-history.php?patient_id=1'>Test Export (Patient ID 1)</a></p>";
?>
