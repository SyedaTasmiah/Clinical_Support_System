<?php
include('include/config.php');

if(isset($_POST["doctor_id"]) && isset($_POST["appointment_date"])) {
    $doctor_id = $_POST["doctor_id"];
    $appointment_date = $_POST["appointment_date"];
    
    // Get the day of week for the selected date
    $day_of_week = date('l', strtotime($appointment_date));
    
    // Get doctor's schedule for this day
    $schedule_query = mysqli_query($con, "SELECT * FROM doctor_schedule WHERE doctor_id='$doctor_id' AND day_of_week='$day_of_week' AND is_active=1");
    
    if(mysqli_num_rows($schedule_query) > 0) {
        $schedule = mysqli_fetch_array($schedule_query);
        
        $start_time = $schedule['start_time'];
        $end_time = $schedule['end_time'];
        $slot_duration = $schedule['slot_duration'];
        
        // Generate time slots
        $slots = array();
        $current_time = strtotime($start_time);
        $end_timestamp = strtotime($end_time);
        
        while($current_time < $end_timestamp) {
            $slot_start = date('H:i:s', $current_time);
            $slot_end = date('H:i:s', $current_time + ($slot_duration * 60));
            
            // Check if this slot is already booked
            $booking_check = mysqli_query($con, "SELECT id FROM appointment WHERE doctorId='$doctor_id' AND appointmentDate='$appointment_date' AND appointmentTime='".date('H:i:s', $current_time)."' AND userStatus=1 AND doctorStatus=1");
            
            if(mysqli_num_rows($booking_check) == 0) {
                // Slot is available
                $slots[] = array(
                    'time' => $slot_start,
                    'display' => date('g:i A', $current_time) . ' - ' . date('g:i A', $current_time + ($slot_duration * 60)),
                    'available' => true
                );
            }
            
            $current_time += ($slot_duration * 60);
        }
        
        if(count($slots) > 0) {
            echo '<option value="">Select Time Slot</option>';
            foreach($slots as $slot) {
                echo '<option value="' . $slot['time'] . '">' . $slot['display'] . '</option>';
            }
        } else {
            echo '<option value="">No available slots for this date</option>';
        }
        
    } else {
        echo '<option value="">Doctor not available on ' . $day_of_week . '</option>';
    }
} else {
    echo '<option value="">Please select a doctor and date first</option>';
}
?>
