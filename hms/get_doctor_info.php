<?php
include('include/config.php');

if(isset($_POST["doctor_id"])) {
    $doctor_id = $_POST["doctor_id"];
    
    // Get doctor's information
    $doctor_query = mysqli_query($con, "SELECT * FROM doctors WHERE id='$doctor_id'");
    
    if(mysqli_num_rows($doctor_query) > 0) {
        $doctor = mysqli_fetch_array($doctor_query);
        
        echo '<div class="doctor-info">';
        echo '<h6><i class="fa fa-user-md"></i> ' . htmlentities($doctor['doctorName']) . '</h6>';
        echo '<p><strong>Specialization:</strong> ' . htmlentities($doctor['specilization']) . '</p>';
        echo '<p><strong>Consultation Fee:</strong> $' . htmlentities($doctor['docFees']) . '</p>';
        echo '<p><strong>Contact:</strong> ' . htmlentities($doctor['contactno']) . '</p>';
        
        if($doctor['address']) {
            echo '<p><strong>Clinic Address:</strong><br>' . htmlentities($doctor['address']) . '</p>';
        }
        
        // Get appointment count for this doctor
        $appointment_count = mysqli_query($con, "SELECT COUNT(*) as total FROM appointment WHERE doctorId='$doctor_id' AND userStatus=1 AND doctorStatus=1");
        $count_data = mysqli_fetch_array($appointment_count);
        
        echo '<div class="alert alert-info" style="margin-top: 10px; padding: 8px;">';
        echo '<small><i class="fa fa-calendar"></i> Total Active Appointments: ' . $count_data['total'] . '</small>';
        echo '</div>';
        
        echo '</div>';
    } else {
        echo '<p class="text-muted">Doctor information not available.</p>';
    }
} else {
    echo '<p class="text-muted">Please select a doctor first.</p>';
}
?>
