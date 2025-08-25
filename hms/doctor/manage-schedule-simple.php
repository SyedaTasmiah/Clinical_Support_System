<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('include/config.php');

// Check if user is logged in
if(!isset($_SESSION['id']) || empty($_SESSION['id'])) {
    echo "<script>alert('Please login first'); window.location.href='index.php';</script>";
    exit();
}

$doctor_id = $_SESSION['id'];
echo "<!-- Debug: Doctor ID = $doctor_id -->";

// Handle schedule updates
if(isset($_POST['update_schedule'])) {
    echo "<!-- Debug: Form submitted -->";
    
    // Simple schedule update - just Monday for testing
    if(isset($_POST['working_monday'])) {
        $start_time = $_POST['start_monday'];
        $end_time = $_POST['end_monday'];
        $duration = $_POST['duration_monday'];
        
        // Delete existing Monday schedule
        $delete_sql = "DELETE FROM doctor_schedule WHERE doctor_id='$doctor_id' AND day_of_week='Monday'";
        mysqli_query($con, $delete_sql);
        
        // Insert new Monday schedule
        $insert_sql = "INSERT INTO doctor_schedule (doctor_id, day_of_week, start_time, end_time, slot_duration) VALUES ('$doctor_id', 'Monday', '$start_time', '$end_time', '$duration')";
        if(mysqli_query($con, $insert_sql)) {
            echo "<script>alert('Monday schedule updated!');</script>";
        } else {
            echo "<script>alert('Error: " . mysqli_error($con) . "');</script>";
        }
    }
}

// Get current schedule
$schedule_query = mysqli_query($con, "SELECT * FROM doctor_schedule WHERE doctor_id='$doctor_id' AND day_of_week='Monday'");
$monday_schedule = mysqli_fetch_array($schedule_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Doctor | Manage Schedule (Simple)</title>
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <style>
        body { padding: 20px; }
        .container { max-width: 800px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Manage Schedule - Simple Version</h2>
        <p>Doctor ID: <?php echo $doctor_id; ?></p>
        
        <form method="post">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4>Monday Schedule</h4>
                </div>
                <div class="panel-body">
                    <label>
                        <input type="checkbox" name="working_monday" value="1" 
                               <?php echo $monday_schedule ? 'checked' : ''; ?>>
                        Working on Monday
                    </label>
                    
                    <div class="row" style="margin-top: 10px;">
                        <div class="col-md-3">
                            <label>Start Time</label>
                            <input type="time" class="form-control" name="start_monday" 
                                   value="<?php echo $monday_schedule ? $monday_schedule['start_time'] : '09:00'; ?>">
                        </div>
                        <div class="col-md-3">
                            <label>End Time</label>
                            <input type="time" class="form-control" name="end_monday" 
                                   value="<?php echo $monday_schedule ? $monday_schedule['end_time'] : '17:00'; ?>">
                        </div>
                        <div class="col-md-3">
                            <label>Duration (minutes)</label>
                            <select class="form-control" name="duration_monday">
                                <option value="15" <?php echo ($monday_schedule && $monday_schedule['slot_duration'] == 15) ? 'selected' : ''; ?>>15 min</option>
                                <option value="30" <?php echo (!$monday_schedule || $monday_schedule['slot_duration'] == 30) ? 'selected' : ''; ?>>30 min</option>
                                <option value="45" <?php echo ($monday_schedule && $monday_schedule['slot_duration'] == 45) ? 'selected' : ''; ?>>45 min</option>
                                <option value="60" <?php echo ($monday_schedule && $monday_schedule['slot_duration'] == 60) ? 'selected' : ''; ?>>60 min</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <button type="submit" name="update_schedule" class="btn btn-primary">Update Schedule</button>
            <a href="view-profile-extended.php" class="btn btn-default">Back to Profile</a>
        </form>
        
        <hr>
        <h3>Current Monday Schedule</h3>
        <?php if($monday_schedule) { ?>
            <p><strong>Working:</strong> Yes</p>
            <p><strong>Hours:</strong> <?php echo date('g:i A', strtotime($monday_schedule['start_time'])); ?> - <?php echo date('g:i A', strtotime($monday_schedule['end_time'])); ?></p>
            <p><strong>Slot Duration:</strong> <?php echo $monday_schedule['slot_duration']; ?> minutes</p>
        <?php } else { ?>
            <p><strong>Working:</strong> No</p>
        <?php } ?>
        
        <hr>
        <p><a href="debug-schedule.php">Debug Information</a></p>
        <p><a href="../setup_schedule_tables.php">Setup Database Tables</a></p>
    </div>
</body>
</html>
