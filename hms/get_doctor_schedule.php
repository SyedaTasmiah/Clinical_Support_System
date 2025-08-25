<?php
include('include/config.php');

if(isset($_POST["doctor_id"])) {
    $doctor_id = $_POST["doctor_id"];
    
    // Get doctor's schedule
    $schedule_query = mysqli_query($con, "SELECT * FROM doctor_schedule WHERE doctor_id='$doctor_id' AND is_active=1 ORDER BY FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')");
    
    if(mysqli_num_rows($schedule_query) > 0) {
        echo '<div class="schedule-info">';
        while($schedule = mysqli_fetch_array($schedule_query)) {
            $start_time = date('g:i A', strtotime($schedule['start_time']));
            $end_time = date('g:i A', strtotime($schedule['end_time']));
            echo '<div class="schedule-day">';
            echo '<strong>' . $schedule['day_of_week'] . ':</strong> ';
            echo $start_time . ' - ' . $end_time;
            echo ' <small class="text-muted">(' . $schedule['slot_duration'] . ' min slots)</small>';
            echo '</div>';
        }
        echo '</div>';
        
        echo '<div class="alert alert-info" style="margin-top: 10px; padding: 8px;">';
        echo '<small><i class="fa fa-info-circle"></i> Available appointment slots are generated based on these working hours.</small>';
        echo '</div>';
    } else {
        echo '<div class="alert alert-warning" style="padding: 8px;">';
        echo '<small><i class="fa fa-exclamation-triangle"></i> Doctor has not set their schedule yet.</small>';
        echo '</div>';
    }
} else {
    echo '<div class="alert alert-danger" style="padding: 8px;">';
    echo '<small><i class="fa fa-times"></i> Please select a doctor first.</small>';
    echo '</div>';
}
?>
