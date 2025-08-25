<?php
/**
 * Extended Doctor Profile Testing Script
 * This script tests all the extended profile functionality
 */

include('hms/include/config.php');

echo "<h2>Extended Doctor Profile Testing</h2>\n";
echo "<pre>\n";

// Test 1: Check if all new columns exist in the doctors table
echo "=== Test 1: Database Schema Verification ===\n";
$required_columns = [
    'profile_picture',
    'education',
    'degrees',
    'certifications',
    'years_of_experience',
    'specialization_details',
    'languages_spoken',
    'medical_school',
    'residency',
    'fellowship',
    'board_certifications',
    'research_interests',
    'awards_honors',
    'professional_memberships',
    'bio',
    'consultation_fee_range',
    'availability_note'
];

$existing_columns = [];
$result = mysqli_query($con, "DESCRIBE doctors");
while($row = mysqli_fetch_array($result)) {
    $existing_columns[] = $row['Field'];
}

$missing_columns = array_diff($required_columns, $existing_columns);
$extra_columns = array_intersect($required_columns, $existing_columns);

echo "Required columns: " . count($required_columns) . "\n";
echo "Existing columns: " . count($extra_columns) . "\n";
echo "Missing columns: " . count($missing_columns) . "\n";

if(empty($missing_columns)) {
    echo "✓ All required columns exist in the database\n";
} else {
    echo "✗ Missing columns: " . implode(', ', $missing_columns) . "\n";
}

// Test 2: Check if files exist
echo "\n=== Test 2: File Existence Verification ===\n";
$required_files = [
    'hms/doctor/edit-profile-extended.php',
    'hms/doctor/view-profile-extended.php',
    'hms/doctor/assets/js/profile-validation.js'
];

foreach($required_files as $file) {
    if(file_exists($file)) {
        echo "✓ $file exists\n";
    } else {
        echo "✗ $file is missing\n";
    }
}

// Test 3: Check upload directory
echo "\n=== Test 3: Upload Directory Verification ===\n";
$upload_dir = 'hms/doctor/uploads/profile_pictures/doctors/';
if(is_dir($upload_dir)) {
    echo "✓ Upload directory exists: $upload_dir\n";
    if(is_writable($upload_dir)) {
        echo "✓ Upload directory is writable\n";
    } else {
        echo "✗ Upload directory is not writable\n";
    }
} else {
    echo "✗ Upload directory does not exist: $upload_dir\n";
    echo "  Creating directory...\n";
    if(mkdir($upload_dir, 0755, true)) {
        echo "✓ Upload directory created successfully\n";
    } else {
        echo "✗ Failed to create upload directory\n";
    }
}

// Test 4: Test JSON field functionality
echo "\n=== Test 4: JSON Field Functionality Test ===\n";
$test_data = [
    'degrees' => ['MBBS', 'MD', 'PhD'],
    'certifications' => ['CPR Certified', 'ACLS', 'BLS'],
    'languages_spoken' => ['English', 'Hindi', 'Tamil']
];

foreach($test_data as $field => $data) {
    $json_encoded = json_encode($data);
    $json_decoded = json_decode($json_encoded, true);
    
    if($data === $json_decoded) {
        echo "✓ JSON encoding/decoding works for $field\n";
    } else {
        echo "✗ JSON encoding/decoding failed for $field\n";
    }
}

// Test 5: Sample data insertion test (optional)
echo "\n=== Test 5: Sample Data Test ===\n";
$test_doctor_id = 999; // Use a test ID that doesn't exist

// Check if test doctor exists
$check_query = "SELECT id FROM doctors WHERE id = $test_doctor_id";
$check_result = mysqli_query($con, $check_query);

if(mysqli_num_rows($check_result) == 0) {
    echo "Creating test doctor record...\n";
    
    $insert_query = "INSERT INTO doctors (id, specilization, doctorName, address, docFees, contactno, docEmail, password, education, degrees, certifications, years_of_experience) VALUES (
        $test_doctor_id,
        'Test Specialization',
        'Test Doctor',
        'Test Address',
        '500',
        '1234567890',
        'test@doctor.com',
        MD5('testpass'),
        'Test education background',
        '" . json_encode(['MBBS', 'MD']) . "',
        '" . json_encode(['CPR', 'ACLS']) . "',
        5
    )";
    
    if(mysqli_query($con, $insert_query)) {
        echo "✓ Test doctor record created\n";
        
        // Test update functionality
        $update_query = "UPDATE doctors SET 
            bio = 'Test professional biography',
            languages_spoken = '" . json_encode(['English', 'Hindi']) . "',
            medical_school = 'Test Medical School',
            specialization_details = 'Test specialization details'
            WHERE id = $test_doctor_id";
        
        if(mysqli_query($con, $update_query)) {
            echo "✓ Test doctor record updated successfully\n";
        } else {
            echo "✗ Failed to update test doctor record: " . mysqli_error($con) . "\n";
        }
        
        // Clean up test data
        mysqli_query($con, "DELETE FROM doctors WHERE id = $test_doctor_id");
        echo "✓ Test data cleaned up\n";
        
    } else {
        echo "✗ Failed to create test doctor record: " . mysqli_error($con) . "\n";
    }
} else {
    echo "⚠ Skipping sample data test (doctor ID $test_doctor_id already exists)\n";
}

// Test 6: Configuration verification
echo "\n=== Test 6: Configuration Verification ===\n";

// Check PHP configuration
$upload_max_filesize = ini_get('upload_max_filesize');
$post_max_size = ini_get('post_max_size');
$max_execution_time = ini_get('max_execution_time');

echo "PHP upload_max_filesize: $upload_max_filesize\n";
echo "PHP post_max_size: $post_max_size\n";
echo "PHP max_execution_time: $max_execution_time seconds\n";

// Convert to bytes for comparison
function convertToBytes($value) {
    $unit = strtolower(substr($value, -1));
    $value = (int)$value;
    switch($unit) {
        case 'g': $value *= 1024;
        case 'm': $value *= 1024;
        case 'k': $value *= 1024;
    }
    return $value;
}

$upload_bytes = convertToBytes($upload_max_filesize);
$post_bytes = convertToBytes($post_max_size);
$required_bytes = 5 * 1024 * 1024; // 5MB

if($upload_bytes >= $required_bytes && $post_bytes >= $required_bytes) {
    echo "✓ PHP configuration supports 5MB file uploads\n";
} else {
    echo "⚠ PHP configuration may not support 5MB file uploads\n";
    echo "  Consider increasing upload_max_filesize and post_max_size\n";
}

// Test 7: Database connection and basic queries
echo "\n=== Test 7: Database Functionality ===\n";

// Test basic select
$test_query = "SELECT COUNT(*) as count FROM doctors";
$test_result = mysqli_query($con, $test_query);
if($test_result) {
    $row = mysqli_fetch_array($test_result);
    echo "✓ Database connection working (found {$row['count']} doctors)\n";
} else {
    echo "✗ Database query failed: " . mysqli_error($con) . "\n";
}

// Test specialization table
$spec_query = "SELECT COUNT(*) as count FROM doctorspecilization";
$spec_result = mysqli_query($con, $spec_query);
if($spec_result) {
    $row = mysqli_fetch_array($spec_result);
    echo "✓ Specialization table accessible (found {$row['count']} specializations)\n";
} else {
    echo "⚠ Specialization table may not exist or be accessible\n";
}

echo "\n=== Testing Summary ===\n";
echo "Extended Doctor Profile Testing Completed!\n";
echo "Please review the results above and address any issues marked with ✗ or ⚠\n";
echo "\nNext Steps:\n";
echo "1. Access the doctor dashboard at: hms/doctor/dashboard.php\n";
echo "2. Click on 'Update Extended Profile' to test the form\n";
echo "3. Click on 'View Extended Profile' to see the display page\n";
echo "4. Test file upload functionality with various image formats\n";
echo "5. Test form validation by submitting invalid data\n";

echo "\n";
echo "</pre>";

mysqli_close($con);
?>
