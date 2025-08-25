<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Debug Information</h2>";

// Check session
echo "<h3>Session Check:</h3>";
echo "Session ID exists: " . (isset($_SESSION['id']) ? "Yes (" . $_SESSION['id'] . ")" : "No") . "<br>";
echo "Session dlogin exists: " . (isset($_SESSION['dlogin']) ? "Yes (" . $_SESSION['dlogin'] . ")" : "No") . "<br>";

// Check database connection
include('include/config.php');
echo "<h3>Database Connection:</h3>";
if ($con) {
    echo "✓ Database connected successfully<br>";
    
    // Check if tables exist
    $table_check = mysqli_query($con, "SHOW TABLES LIKE 'doctor_schedule'");
    echo "doctor_schedule table exists: " . (mysqli_num_rows($table_check) > 0 ? "Yes" : "No") . "<br>";
    
    if(mysqli_num_rows($table_check) == 0) {
        echo "<p style='color: red;'>❌ doctor_schedule table does not exist!</p>";
        echo "<p>Please run the setup script first: <a href='../setup_schedule_tables.php'>Setup Tables</a></p>";
    }
    
    // Check doctor exists
    if(isset($_SESSION['id'])) {
        $doctor_check = mysqli_query($con, "SELECT * FROM doctors WHERE id='" . $_SESSION['id'] . "'");
        echo "Doctor found in database: " . (mysqli_num_rows($doctor_check) > 0 ? "Yes" : "No") . "<br>";
    }
    
} else {
    echo "✗ Database connection failed: " . mysqli_connect_error() . "<br>";
}

echo "<h3>File Paths:</h3>";
echo "Current file: " . __FILE__ . "<br>";
echo "Include path: " . get_include_path() . "<br>";

echo "<h3>PHP Info:</h3>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Error reporting: " . error_reporting() . "<br>";

if(isset($_SESSION['id']) && $_SESSION['id']) {
    echo "<p><a href='manage-schedule.php'>Try manage-schedule.php again</a></p>";
} else {
    echo "<p style='color: red;'>You need to be logged in as a doctor first!</p>";
    echo "<p><a href='index.php'>Doctor Login</a></p>";
}
?>
