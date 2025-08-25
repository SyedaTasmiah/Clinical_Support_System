<?php
include('hms/include/config.php');

echo "Extending doctor profile with additional fields...\n\n";

// Array of new columns to add
$new_columns = [
    'profile_picture' => "VARCHAR(255) NULL COMMENT 'Profile picture filename'",
    'education' => "TEXT NULL COMMENT 'Educational background'",
    'degrees' => "TEXT NULL COMMENT 'Academic degrees (JSON format)'",
    'certifications' => "TEXT NULL COMMENT 'Professional certifications (JSON format)'",
    'years_of_experience' => "INT(3) NULL COMMENT 'Years of medical experience'",
    'specialization_details' => "TEXT NULL COMMENT 'Detailed specialization information'",
    'languages_spoken' => "TEXT NULL COMMENT 'Languages spoken (JSON format)'",
    'medical_school' => "VARCHAR(255) NULL COMMENT 'Medical school attended'",
    'residency' => "VARCHAR(255) NULL COMMENT 'Residency program'",
    'fellowship' => "VARCHAR(255) NULL COMMENT 'Fellowship program'",
    'board_certifications' => "TEXT NULL COMMENT 'Board certifications (JSON format)'",
    'research_interests' => "TEXT NULL COMMENT 'Research interests and publications'",
    'awards_honors' => "TEXT NULL COMMENT 'Awards and honors (JSON format)'",
    'professional_memberships' => "TEXT NULL COMMENT 'Professional memberships (JSON format)'",
    'bio' => "TEXT NULL COMMENT 'Professional biography'",
    'consultation_fee_range' => "VARCHAR(50) NULL COMMENT 'Fee range for consultations'",
    'availability_note' => "TEXT NULL COMMENT 'Special availability notes'"
];

foreach($new_columns as $column_name => $column_definition) {
    // Check if column already exists
    $check_query = "SHOW COLUMNS FROM doctors LIKE '$column_name'";
    $check_result = mysqli_query($con, $check_query);
    
    if(mysqli_num_rows($check_result) == 0) {
        // Column doesn't exist, add it
        $add_column_query = "ALTER TABLE doctors ADD COLUMN $column_name $column_definition";
        
        if(mysqli_query($con, $add_column_query)) {
            echo "✓ Added column: $column_name\n";
        } else {
            echo "✗ Error adding column $column_name: " . mysqli_error($con) . "\n";
        }
    } else {
        echo "- Column $column_name already exists\n";
    }
}

// Show current table structure
echo "\n" . str_repeat("=", 50) . "\n";
echo "Current doctors table structure:\n";
echo str_repeat("=", 50) . "\n";

$describe_query = "DESCRIBE doctors";
$describe_result = mysqli_query($con, $describe_query);

if($describe_result) {
    while($row = mysqli_fetch_array($describe_result)) {
        $null = $row['Null'] == 'YES' ? 'NULL' : 'NOT NULL';
        $default = $row['Default'] ? " DEFAULT: {$row['Default']}" : '';
        echo sprintf("%-25s %-20s %s%s\n", 
            $row['Field'], 
            $row['Type'], 
            $null,
            $default
        );
    }
} else {
    echo "Error describing table: " . mysqli_error($con) . "\n";
}

mysqli_close($con);
echo "\n✓ Doctor profile extension completed!\n";
?>

