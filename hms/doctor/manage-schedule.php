<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('include/config.php');
if(!isset($_SESSION['id']) || empty($_SESSION['id'])) {
 echo "<script>alert('Please login first'); window.location.href='index.php';</script>";
 exit();
  } else{

// Handle schedule updates
if(isset($_POST['update_schedule']))
{
    $doctor_id = $_SESSION['id'];
    
    // Delete existing schedules for this doctor
    $delete_result = mysqli_query($con, "DELETE FROM doctor_schedule WHERE doctor_id='$doctor_id'");
    if (!$delete_result) {
        echo "<script>alert('Error deleting old schedule: " . mysqli_error($con) . "');</script>";
    } else {
        // Days of the week
        $days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
        $success_count = 0;
        
        foreach($days as $day) {
            $is_working = isset($_POST['working_' . strtolower($day)]) ? 1 : 0;
            
            if($is_working) {
                $start_time = mysqli_real_escape_string($con, $_POST['start_' . strtolower($day)]);
                $end_time = mysqli_real_escape_string($con, $_POST['end_' . strtolower($day)]);
                $slot_duration = mysqli_real_escape_string($con, $_POST['duration_' . strtolower($day)]);
                
                if($start_time && $end_time && $slot_duration) {
                    $sql = "INSERT INTO doctor_schedule (doctor_id, day_of_week, start_time, end_time, slot_duration) 
                            VALUES ('$doctor_id', '$day', '$start_time', '$end_time', '$slot_duration')";
                    $result = mysqli_query($con, $sql);
                    if ($result) {
                        $success_count++;
                    } else {
                        echo "<script>alert('Error inserting $day schedule: " . mysqli_error($con) . "');</script>";
                    }
                }
            }
        }
        
        echo "<script>alert('Schedule updated successfully! $success_count working days configured.');</script>";
    }
}

// Get current doctor's schedule
$doctor_id = $_SESSION['id'];

// Check if table exists first
$table_check = mysqli_query($con, "SHOW TABLES LIKE 'doctor_schedule'");
if(mysqli_num_rows($table_check) == 0) {
    echo "<script>alert('Database tables not found. Please contact administrator.'); window.location.href='../setup_schedule_tables.php';</script>";
    exit();
}

$schedule_query = mysqli_query($con, "SELECT * FROM doctor_schedule WHERE doctor_id='$doctor_id'");
if (!$schedule_query) {
    echo "<script>alert('Database error: " . mysqli_error($con) . "');</script>";
    exit();
}

$current_schedule = array();
while($row = mysqli_fetch_array($schedule_query)) {
    $current_schedule[$row['day_of_week']] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Doctor | Manage Schedule</title>
		
		<link href="http://fonts.googleapis.com/css?family=Lato:300,400,400italic,600,700|Raleway:300,400,500,600,700|Crete+Round:400italic" rel="stylesheet" type="text/css" />
		<link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
		<link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
		<link rel="stylesheet" href="vendor/themify-icons/themify-icons.min.css">
		<link href="vendor/animate.css/animate.min.css" rel="stylesheet" media="screen">
		<link href="vendor/perfect-scrollbar/perfect-scrollbar.min.css" rel="stylesheet" media="screen">
		<link href="vendor/switchery/switchery.min.css" rel="stylesheet" media="screen">
		<link href="vendor/bootstrap-touchspin/jquery.bootstrap-touchspin.min.css" rel="stylesheet" media="screen">
		<link href="vendor/select2/select2.min.css" rel="stylesheet" media="screen">
		<link href="vendor/bootstrap-datepicker/bootstrap-datepicker3.standalone.min.css" rel="stylesheet" media="screen">
		<link href="vendor/bootstrap-timepicker/bootstrap-timepicker.min.css" rel="stylesheet" media="screen">
		<link rel="stylesheet" href="assets/css/styles.css">
		<link rel="stylesheet" href="assets/css/plugins.css">
		<link rel="stylesheet" href="assets/css/themes/theme-1.css" id="skin_color" />

		<style>
		/* Fix scrolling and layout issues */
		html, body {
			height: 100%;
			overflow-x: hidden;
		}
		
		#app {
			min-height: 100vh;
		}
		
		.main-content {
			padding-bottom: 50px; /* Add bottom padding for proper scrolling */
		}
		
		.wrap-content {
			min-height: calc(100vh - 120px);
			padding-bottom: 30px;
		}
		
		.schedule-day {
			border: 1px solid #ddd;
			margin-bottom: 15px;
			padding: 15px;
			border-radius: 5px;
		}
		.schedule-day.working {
			background-color: #f9f9f9;
		}
		.day-header {
			font-weight: bold;
			margin-bottom: 10px;
		}
		.time-inputs {
			display: none;
		}
		.working .time-inputs {
			display: block;
		}
		
		/* Ensure proper spacing for the overview section */
		.margin-top-30 {
			margin-top: 30px !important;
		}
		
		/* Fix table responsiveness */
		.table-responsive {
			max-height: 500px;
			overflow-y: auto;
		}
		</style>
	</head>
	<body>
		<div id="app">		
<?php include('include/sidebar.php');?>
			<div class="app-content">
				<?php include('include/header.php');?>
				<div class="main-content" >
					<div class="wrap-content container" id="container">
						<!-- start: PAGE TITLE -->
						<section id="page-title">
							<div class="row">
								<div class="col-sm-8">
									<h1 class="mainTitle">Doctor | Manage Weekly Schedule</h1>
								</div>
								<ol class="breadcrumb">
									<li>
										<span>Doctor</span>
									</li>
									<li class="active">
										<span>Manage Schedule</span>
									</li>
								</ol>
							</div>
						</section>
						<!-- end: PAGE TITLE -->
						
						<div class="container-fluid container-fullw bg-white">
							<div class="row">
								<div class="col-md-12">
									<div class="panel panel-white">
										<div class="panel-heading">
											<h5 class="panel-title">Weekly Schedule Management</h5>
										</div>
										<div class="panel-body">
											<p class="text-info">Set your working hours for each day of the week. Patients will only be able to book appointments during these times.</p>
											
											<form role="form" name="schedule_form" method="post">
												<?php 
												$days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
												foreach($days as $day) {
													$day_lower = strtolower($day);
													$is_working = isset($current_schedule[$day]);
													$start_time = $is_working ? $current_schedule[$day]['start_time'] : '09:00';
													$end_time = $is_working ? $current_schedule[$day]['end_time'] : '17:00';
													$slot_duration = $is_working ? $current_schedule[$day]['slot_duration'] : '30';
												?>
												
												<div class="schedule-day <?php echo $is_working ? 'working' : ''; ?>" id="day_<?php echo $day_lower; ?>">
													<div class="day-header">
														<label class="checkbox-inline">
															<input type="checkbox" name="working_<?php echo $day_lower; ?>" 
																   value="1" <?php echo $is_working ? 'checked' : ''; ?> 
																   onchange="toggleDay('<?php echo $day_lower; ?>')">
															<strong><?php echo $day; ?></strong>
														</label>
													</div>
													
													<div class="time-inputs">
														<div class="row">
															<div class="col-md-3">
																<label>Start Time</label>
																<input type="time" class="form-control" 
																	   name="start_<?php echo $day_lower; ?>" 
																	   value="<?php echo $start_time; ?>">
															</div>
															<div class="col-md-3">
																<label>End Time</label>
																<input type="time" class="form-control" 
																	   name="end_<?php echo $day_lower; ?>" 
																	   value="<?php echo $end_time; ?>">
															</div>
															<div class="col-md-3">
																<label>Slot Duration (minutes)</label>
																<select class="form-control" name="duration_<?php echo $day_lower; ?>">
																	<option value="15" <?php echo $slot_duration == 15 ? 'selected' : ''; ?>>15 minutes</option>
																	<option value="30" <?php echo $slot_duration == 30 ? 'selected' : ''; ?>>30 minutes</option>
																	<option value="45" <?php echo $slot_duration == 45 ? 'selected' : ''; ?>>45 minutes</option>
																	<option value="60" <?php echo $slot_duration == 60 ? 'selected' : ''; ?>>60 minutes</option>
																</select>
															</div>
															<div class="col-md-3">
																<label>&nbsp;</label>
																<div class="form-control-static">
																	<small class="text-muted">Available slots will be created automatically</small>
																</div>
															</div>
														</div>
													</div>
												</div>
												
												<?php } ?>
												
												<div class="form-actions">
													<button type="submit" name="update_schedule" class="btn btn-primary">
														<i class="fa fa-save"></i> Update Schedule
													</button>
													<button type="button" class="btn btn-info" onclick="scrollToOverview()">
														<i class="fa fa-eye"></i> View Schedule Overview
													</button>
													<a href="view-profile-extended.php" class="btn btn-default">
														<i class="fa fa-arrow-left"></i> Back to Profile
													</a>
												</div>
											</form>
										</div>
									</div>
								</div>
							</div>
							
							<!-- Current Schedule Overview -->
							<div class="row margin-top-30" id="schedule-overview">
								<div class="col-md-12">
									<div class="panel panel-white">
										<div class="panel-heading">
											<h5 class="panel-title"><i class="fa fa-calendar"></i> Current Schedule Overview</h5>
										</div>
										<div class="panel-body">
											<div class="table-responsive">
												<table class="table table-striped">
													<thead>
														<tr>
															<th>Day</th>
															<th>Status</th>
															<th>Working Hours</th>
															<th>Slot Duration</th>
															<th>Total Slots</th>
														</tr>
													</thead>
													<tbody>
														<?php foreach($days as $day) { 
															$is_working = isset($current_schedule[$day]);
														?>
														<tr>
															<td><strong><?php echo $day; ?></strong></td>
															<td>
																<?php if($is_working) { ?>
																	<span class="label label-success">Working</span>
																<?php } else { ?>
																	<span class="label label-default">Off</span>
																<?php } ?>
															</td>
															<td>
																<?php if($is_working) { 
																	echo date('g:i A', strtotime($current_schedule[$day]['start_time'])) . ' - ' . 
																		 date('g:i A', strtotime($current_schedule[$day]['end_time']));
																} else {
																	echo '-';
																} ?>
															</td>
															<td>
																<?php echo $is_working ? $current_schedule[$day]['slot_duration'] . ' min' : '-'; ?>
															</td>
															<td>
																<?php 
																if($is_working) {
																	$start = strtotime($current_schedule[$day]['start_time']);
																	$end = strtotime($current_schedule[$day]['end_time']);
																	$duration = $current_schedule[$day]['slot_duration'] * 60; // convert to seconds
																	$total_slots = ($end - $start) / $duration;
																	echo floor($total_slots);
																} else {
																	echo '-';
																}
																?>
															</td>
														</tr>
														<?php } ?>
													</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<!-- Scroll to top button -->
			<button id="scrollToTop" class="btn btn-primary" style="position: fixed; bottom: 20px; right: 20px; z-index: 1000; display: none; border-radius: 50%; width: 50px; height: 50px;">
				<i class="fa fa-arrow-up"></i>
			</button>
			
			<!-- start: FOOTER -->
	<?php include('include/footer.php');?>
			<!-- end: FOOTER -->
		
			<!-- start: SETTINGS -->
	<?php include('include/setting.php');?>
			<!-- end: SETTINGS -->
		</div>
		
		<!-- start: MAIN JAVASCRIPTS -->
		<script src="vendor/jquery/jquery.min.js"></script>
		<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
		<script src="vendor/modernizr/modernizr.js"></script>
		<script src="vendor/jquery-cookie/jquery.cookie.js"></script>
		<script src="vendor/perfect-scrollbar/perfect-scrollbar.min.js"></script>
		<script src="vendor/switchery/switchery.min.js"></script>
		<script src="vendor/bootstrap-timepicker/bootstrap-timepicker.min.js"></script>
		<script src="assets/js/main.js"></script>
		
		<script>
			function toggleDay(day) {
				var checkbox = document.querySelector('input[name="working_' + day + '"]');
				var dayDiv = document.getElementById('day_' + day);
				
				if(checkbox.checked) {
					dayDiv.classList.add('working');
				} else {
					dayDiv.classList.remove('working');
				}
			}
			
			function scrollToOverview() {
				$('html, body').animate({
					scrollTop: $('#schedule-overview').offset().top - 20
				}, 800);
			}
			
			jQuery(document).ready(function() {
				Main.init();
				
				// Initialize time pickers
				$('.time-input').timepicker({
					showMeridian: false,
					defaultTime: false
				});
				
				// Fix scrolling issues
				$(window).resize(function() {
					// Recalculate heights on window resize
					var windowHeight = $(window).height();
					var headerHeight = $('.app-content .main-content').offset().top;
					var availableHeight = windowHeight - headerHeight - 50;
					$('.wrap-content').css('min-height', availableHeight + 'px');
				});
				
				// Trigger resize on load
				$(window).trigger('resize');
				
				// Ensure smooth scrolling to bottom
				$('html, body').css({
					'scroll-behavior': 'smooth'
				});
				
				// Scroll to top button functionality
				$(window).scroll(function() {
					if ($(this).scrollTop() > 100) {
						$('#scrollToTop').fadeIn();
					} else {
						$('#scrollToTop').fadeOut();
					}
				});
				
				$('#scrollToTop').click(function() {
					$('html, body').animate({scrollTop: 0}, 600);
					return false;
				});
				
				// Auto-scroll to bottom after form submission
				<?php if(isset($_POST['update_schedule'])) { ?>
				setTimeout(function() {
					$('html, body').animate({
						scrollTop: $(document).height()
					}, 1000);
				}, 500);
				<?php } ?>
			});
		</script>
	</body>
</html>
<?php } ?>
