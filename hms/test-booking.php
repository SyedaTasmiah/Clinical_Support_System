<?php
session_start();
include('include/config.php');

// Set a test session for testing purposes
if(!isset($_SESSION['id'])) {
    $_SESSION['id'] = 1; // Test user ID
    $_SESSION['login'] = 'test@test.com';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Test Booking System</title>
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
    <script src="vendor/jquery/jquery.min.js"></script>
    <style>
        body { padding: 20px; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Booking System Test Page</h2>
        
        <div class="test-section">
            <h4>1. Test Specialization Dropdown</h4>
            <select class="form-control" id="test_specialization" onchange="testGetDoctor(this.value);">
                <option value="">Select Specialization</option>
                <?php 
                $ret = mysqli_query($con, "SELECT * FROM doctorspecilization");
                if($ret) {
                    while($row = mysqli_fetch_array($ret)) {
                        echo '<option value="' . htmlentities($row['specilization']) . '">' . htmlentities($row['specilization']) . '</option>';
                    }
                } else {
                    echo '<option value="">Database Error: ' . mysqli_error($con) . '</option>';
                }
                ?>
            </select>
        </div>
        
        <div class="test-section">
            <h4>2. Test Doctor Dropdown</h4>
            <select class="form-control" id="test_doctor" onchange="testGetFee(this.value);">
                <option value="">Select Doctor</option>
            </select>
        </div>
        
        <div class="test-section">
            <h4>3. Test Consultation Fee</h4>
            <input type="text" class="form-control" id="test_fees" readonly placeholder="Select a doctor to see fees">
        </div>
        
        <div class="test-section">
            <h4>4. Test Available Slots</h4>
            <input type="date" class="form-control" id="test_date" onchange="testGetSlots();" style="margin-bottom: 10px;">
            <select class="form-control" id="test_slots">
                <option value="">Select Date and Doctor First</option>
            </select>
        </div>
        
        <div class="test-section">
            <h4>5. Database Connection Test</h4>
            <p>Connection Status: 
                <?php 
                if($con) {
                    echo '<span class="text-success">✓ Connected</span>';
                    echo '<br>Database: ' . mysqli_get_server_info($con);
                } else {
                    echo '<span class="text-danger">✗ Failed: ' . mysqli_connect_error() . '</span>';
                }
                ?>
            </p>
        </div>
        
        <div class="test-section">
            <h4>6. Table Existence Check</h4>
            <?php
            $tables = ['doctorspecilization', 'doctors', 'doctor_schedule', 'appointment'];
            foreach($tables as $table) {
                $check = mysqli_query($con, "SHOW TABLES LIKE '$table'");
                if(mysqli_num_rows($check) > 0) {
                    echo '<p><span class="text-success">✓ ' . $table . '</span></p>';
                } else {
                    echo '<p><span class="text-danger">✗ ' . $table . ' (missing)</span></p>';
                }
            }
            ?>
        </div>
    </div>
    
    <script>
        function testGetDoctor(val) {
            console.log('Testing getdoctor with value:', val);
            if(val) {
                $.ajax({
                    type: "POST",
                    url: "get_doctor.php",
                    data: 'specilizationid=' + val,
                    success: function(data) {
                        console.log('Doctor data received:', data);
                        $("#test_doctor").html(data);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading doctors:', error);
                        $("#test_doctor").html('<option value="">Error loading doctors</option>');
                    }
                });
            } else {
                $("#test_doctor").html('<option value="">Select Doctor</option>');
            }
        }
        
        function testGetFee(val) {
            console.log('Testing getfee with value:', val);
            if(val) {
                $.ajax({
                    type: "POST",
                    url: "get_doctor.php",
                    data: 'doctor=' + val,
                    success: function(data) {
                        console.log('Fee data received:', data);
                        $("#test_fees").val(data);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading fee:', error);
                        $("#test_fees").val('Error loading fee');
                    }
                });
            } else {
                $("#test_fees").val('');
            }
        }
        
        function testGetSlots() {
            var doctorId = $('#test_doctor').val();
            var appointmentDate = $('#test_date').val();
            
            console.log('Testing slots with doctor:', doctorId, 'date:', appointmentDate);
            
            if(doctorId && appointmentDate) {
                $.ajax({
                    url: 'get_available_slots.php',
                    type: 'POST',
                    data: {
                        doctor_id: doctorId,
                        appointment_date: appointmentDate
                    },
                    success: function(data) {
                        console.log('Slots data received:', data);
                        $('#test_slots').html(data);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading slots:', error);
                        $('#test_slots').html('<option value="">Error loading slots</option>');
                    }
                });
            } else {
                $('#test_slots').html('<option value="">Select Date and Doctor First</option>');
            }
        }
        
        // Bind events
        $('#test_doctor').change(function() {
            testGetFee(this.value);
        });
        
        $('#test_date').change(testGetSlots);
    </script>
</body>
</html>
