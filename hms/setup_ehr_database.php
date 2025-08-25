<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('include/config.php');

echo "<h2>Setting up EHR Database Tables...</h2>";

// Read the SQL file content
$sql_content = file_get_contents('ehr_database_setup.sql');

if ($sql_content === false) {
    die("Error: Could not read ehr_database_setup.sql file");
}

// Split SQL statements
$statements = array_filter(array_map('trim', explode(';', $sql_content)));

$success_count = 0;
$error_count = 0;

foreach ($statements as $statement) {
    if (empty($statement) || strpos($statement, '--') === 0) {
        continue; // Skip empty statements and comments
    }
    
    if (mysqli_query($con, $statement)) {
        $success_count++;
        
        // Check if it's a CREATE TABLE statement to show table name
        if (stripos($statement, 'CREATE TABLE') !== false) {
            preg_match('/CREATE TABLE.*?`?(\w+)`?/i', $statement, $matches);
            $table_name = isset($matches[1]) ? $matches[1] : 'Unknown';
            echo "<p style='color: green;'>✓ Table '$table_name' created successfully</p>";
        }
        // Check for INSERT statements
        elseif (stripos($statement, 'INSERT') !== false) {
            echo "<p style='color: blue;'>✓ Sample data inserted</p>";
        }
        // Check for INDEX statements
        elseif (stripos($statement, 'CREATE INDEX') !== false) {
            echo "<p style='color: orange;'>✓ Index created</p>";
        }
        // Check for SELECT statements (status messages)
        elseif (stripos($statement, 'SELECT') !== false) {
            $result = mysqli_query($con, $statement);
            if ($result) {
                $row = mysqli_fetch_assoc($result);
                echo "<p style='color: green; font-weight: bold;'>" . $row['Status'] . "</p>";
            }
        }
    } else {
        $error_count++;
        echo "<p style='color: red;'>✗ Error executing statement: " . mysqli_error($con) . "</p>";
        echo "<pre style='color: #666; font-size: 12px;'>" . substr($statement, 0, 200) . "...</pre>";
    }
}

echo "<h3>Setup Summary:</h3>";
echo "<p>Successful operations: <strong style='color: green;'>$success_count</strong></p>";
echo "<p>Errors: <strong style='color: red;'>$error_count</strong></p>";

// Verify tables exist
echo "<h3>Table Verification:</h3>";
$tables = [
    'patient_medical_records',
    'medical_consultations', 
    'patient_allergies',
    'patient_conditions',
    'prescriptions',
    'lab_results',
    'vital_signs'
];

foreach ($tables as $table) {
    $check = mysqli_query($con, "SHOW TABLES LIKE '$table'");
    if (mysqli_num_rows($check) > 0) {
        // Get row count
        $count_result = mysqli_query($con, "SELECT COUNT(*) as count FROM $table");
        $count = mysqli_fetch_assoc($count_result)['count'];
        echo "<p style='color: green;'>✓ $table (Rows: $count)</p>";
    } else {
        echo "<p style='color: red;'>✗ $table (Missing)</p>";
    }
}

echo "<h3>Next Steps:</h3>";
echo "<ul>";
echo "<li>Tables are now created and ready to use</li>";
echo "<li>Sample data has been inserted for testing</li>";
echo "<li>You can now access the EHR system</li>";
echo "<li><a href='patient/ehr-records.php'>Patient EHR View</a> (create this next)</li>";
echo "<li><a href='doctor/patient-ehr.php'>Doctor EHR Interface</a> (create this next)</li>";
echo "</ul>";

mysqli_close($con);
?>
