<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('include/config.php');

echo "<h2>Adding Profile Picture Fields to Database...</h2>";

// Add profile_picture field to doctors table
$add_doctor_picture = "ALTER TABLE doctors ADD COLUMN profile_picture VARCHAR(255) DEFAULT NULL";
if(mysqli_query($con, $add_doctor_picture)) {
    echo "<p style='color: green;'>✓ Added profile_picture field to doctors table</p>";
} else {
    if(mysqli_errno($con) == 1060) { // Column already exists
        echo "<p style='color: orange;'>⚠ profile_picture field already exists in doctors table</p>";
    } else {
        echo "<p style='color: red;'>✗ Error adding profile_picture to doctors table: " . mysqli_error($con) . "</p>";
    }
}

// Add profile_picture field to users table
$add_user_picture = "ALTER TABLE users ADD COLUMN profile_picture VARCHAR(255) DEFAULT NULL";
if(mysqli_query($con, $add_user_picture)) {
    echo "<p style='color: green;'>✓ Added profile_picture field to users table</p>";
} else {
    if(mysqli_errno($con) == 1060) { // Column already exists
        echo "<p style='color: orange;'>⚠ profile_picture field already exists in users table</p>";
    } else {
        echo "<p style='color: red;'>✗ Error adding profile_picture to users table: " . mysqli_error($con) . "</p>";
    }
}

echo "<h3>Database schema updated successfully!</h3>";
echo "<p>Profile picture fields have been added to both doctors and users tables.</p>";

mysqli_close($con);
?>
