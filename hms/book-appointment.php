<?php
session_start();
//error_reporting(0);
include('include/config.php');
include('include/checklogin.php');
check_login();

if(isset($_POST['submit']))
{
$specilization=$_POST['Doctorspecialization'];
$doctorid=$_POST['doctor'];
$userid=$_SESSION['id'];
$fees=$_POST['fees'];
$appdate=$_POST['appdate'];
$time=$_POST['apptime'];
$userstatus=1;
$docstatus=1;

// Check if the selected time slot is available
$conflict_check = mysqli_query($con,"SELECT id FROM appointment WHERE doctorId='$doctorid' AND appointmentDate='$appdate' AND appointmentTime='$time' AND userStatus=1 AND doctorStatus=1");

if(mysqli_num_rows($conflict_check) > 0) {
	echo "<script>alert('This time slot is already booked. Please select another time.');</script>";
} else {
	// Check if doctor is available on this day
	$day_of_week = date('l', strtotime($appdate));
	$schedule_check = mysqli_query($con,"SELECT * FROM doctor_schedule WHERE doctor_id='$doctorid' AND day_of_week='$day_of_week' AND is_active=1");
	
	if(mysqli_num_rows($schedule_check) == 0) {
		echo "<script>alert('Doctor is not available on this day. Please select another date.');</script>";
	} else {
		$schedule = mysqli_fetch_array($schedule_check);
		$start_time = strtotime($schedule['start_time']);
		$end_time = strtotime($schedule['end_time']);
		$appointment_time = strtotime($time);
		
		// Check if appointment time is within doctor's working hours
		if($appointment_time < $start_time || $appointment_time >= $end_time) {
			echo "<script>alert('Selected time is outside doctor working hours. Please select a valid time slot.');</script>";
		} else {
			$query=mysqli_query($con,"insert into appointment(doctorSpecialization,doctorId,userId,consultancyFees,appointmentDate,appointmentTime,userStatus,doctorStatus) values('$specilization','$doctorid','$userid','$fees','$appdate','$time','$userstatus','$docstatus')");
			if($query)
			{
				echo "<script>alert('Your appointment successfully booked');</script>";
			}
		}
	}
}

}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>User  | Book Appointment</title>
	
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
	




	</head>
	<body>
		<div id="app">		
<?php include('include/sidebar.php');?>
			<div class="app-content">
			
						<?php include('include/header.php');?>
					
				<!-- end: TOP NAVBAR -->
				<div class="main-content" >
					<div class="wrap-content container" id="container">
						<!-- start: PAGE TITLE -->
						<section id="page-title">
							<div class="row">
								<div class="col-sm-8">
									<h1 class="mainTitle">User | Book Appointment</h1>
																	</div>
								<ol class="breadcrumb">
									<li>
										<span>User</span>
									</li>
									<li class="active">
										<span>Book Appointment</span>
									</li>
								</ol>
						</section>
						<!-- end: PAGE TITLE -->
						<!-- start: BASIC EXAMPLE -->
						<div class="container-fluid container-fullw bg-white">
							<div class="row">
								<div class="col-md-8">
									
									<div class="row margin-top-30">
										<div class="col-lg-12 col-md-12">
											<div class="panel panel-white">
												<div class="panel-heading">
													<h5 class="panel-title"><i class="fa fa-calendar-plus-o"></i> Book Appointment</h5>
												</div>
												<div class="panel-body">
								<p style="color:red;"><?php echo htmlentities($_SESSION['msg1']);?>
								<?php echo htmlentities($_SESSION['msg1']="");?></p>	
													<form role="form" name="book" method="post" >
														


<div class="form-group">
															<label for="DoctorSpecialization">
																Doctor Specialization
															</label>
<?php 
// Pre-fill data if coming from dashboard
$preselected_spec = isset($_GET['spec']) ? $_GET['spec'] : '';
$preselected_doctor = isset($_GET['doctor']) ? $_GET['doctor'] : '';
$preselected_date = isset($_GET['date']) ? $_GET['date'] : '';
$preselected_time = isset($_GET['time']) ? $_GET['time'] : '';
?>
							<select name="Doctorspecialization" class="form-control" onChange="getdoctor(this.value);" required="required">
																<option value="">Select Specialization</option>
<?php $ret=mysqli_query($con,"select * from doctorspecilization");
while($row=mysqli_fetch_array($ret))
{
?>
																<option value="<?php echo htmlentities($row['specilization']);?>" <?php echo ($preselected_spec == $row['specilization']) ? 'selected' : ''; ?>>
																	<?php echo htmlentities($row['specilization']);?>
																</option>
																<?php } ?>
																
															</select>
														</div>




														<div class="form-group">
															<label for="doctor">
																Doctors
															</label>
						<select name="doctor" class="form-control" id="doctor" onChange="getfee(this.value);" required="required">
						<option value="">Select Doctor</option>
						</select>
														</div>





														<div class="form-group">
															<label for="consultancyfees">
																Consultancy Fees
															</label>
					<input type="text" name="fees" class="form-control" id="fees" readonly placeholder="Select a doctor to see fees">
														</div>
														
<div class="form-group">
															<label for="AppointmentDate">
																Date
															</label>
<input class="form-control datepicker" name="appdate"  required="required" data-date-format="yyyy-mm-dd" value="<?php echo htmlentities($preselected_date); ?>">
	
														</div>
														
<div class="form-group">
															<label for="Appointmenttime">
														
														Available Time Slots
													
															</label>
			<select class="form-control" name="apptime" id="available_slots" required="required">
				<option value="">Select Date and Doctor First</option>
			</select>
			<small class="text-muted">Only available time slots are shown</small>
														</div>														
														
														<button type="submit" name="submit" class="btn btn-o btn-primary">
															Submit
														</button>
													</form>
												</div>
											</div>
										</div>
									</div>
								</div>
								
								<!-- Doctor Information Panel -->
								<div class="col-md-4">
									<div class="panel panel-white">
										<div class="panel-heading">
											<h5 class="panel-title"><i class="fa fa-info-circle"></i> Doctor Information</h5>
										</div>
										<div class="panel-body">
											<div id="doctor_info">
												<p class="text-muted">Select a doctor to see their information and schedule.</p>
											</div>
											<div id="selected_doctor_schedule" style="display: none;">
												<h6><i class="fa fa-clock-o"></i> Working Hours:</h6>
												<div id="doctor_schedule_info"></div>
											</div>
										</div>
									</div>
									
									<div class="panel panel-white">
										<div class="panel-heading">
											<h5 class="panel-title"><i class="fa fa-lightbulb-o"></i> Booking Tips</h5>
										</div>
										<div class="panel-body">
											<ul class="list-unstyled">
												<li><i class="fa fa-check text-success"></i> Only available time slots are shown</li>
												<li><i class="fa fa-check text-success"></i> Appointments are confirmed instantly</li>
												<li><i class="fa fa-check text-success"></i> No double-booking possible</li>
												<li><i class="fa fa-check text-success"></i> You can cancel up to 24 hours before</li>
											</ul>
										</div>
									</div>
								</div>
								
							</div>
							
						<!-- end: BASIC EXAMPLE -->
			
					
					
						
						
					
						<!-- end: SELECT BOXES -->
						
					</div>
				</div>
			</div>
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
		<!-- end: MAIN JAVASCRIPTS -->
		<!-- start: JAVASCRIPTS REQUIRED FOR THIS PAGE ONLY -->
		<script src="vendor/maskedinput/jquery.maskedinput.min.js"></script>
		<script src="vendor/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js"></script>
		<script src="vendor/autosize/autosize.min.js"></script>
		<script src="vendor/selectFx/classie.js"></script>
		<script src="vendor/selectFx/selectFx.js"></script>
		<script src="vendor/select2/select2.min.js"></script>
		<script src="vendor/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
		<script src="vendor/bootstrap-timepicker/bootstrap-timepicker.min.js"></script>
		<!-- end: JAVASCRIPTS REQUIRED FOR THIS PAGE ONLY -->
		<!-- start: CLIP-TWO JAVASCRIPTS -->
		<script src="assets/js/main.js"></script>
		<!-- start: JavaScript Event Handlers for this page -->
		<script src="assets/js/form-elements.js"></script>
		<script>
			// Define functions first
			function getdoctor(val) {
				if(val) {
					$.ajax({
						type: "POST",
						url: "get_doctor.php",
						data: 'specilizationid=' + val,
						success: function(data) {
							$("#doctor").html(data);
							$("#fees").val('');
							$('#available_slots').html('<option value="">Select Date and Doctor First</option>');
							$('#doctor_info').html('<p class="text-muted">Select a doctor to see their information and schedule.</p>');
							$('#selected_doctor_schedule').hide();
						},
						error: function() {
							$("#doctor").html('<option value="">Error loading doctors</option>');
						}
					});
				} else {
					$("#doctor").html('<option value="">Select Doctor</option>');
					$("#fees").val('');
					$('#available_slots').html('<option value="">Select Date and Doctor First</option>');
				}
			}
			
			function getfee(val) {
				if(val) {
					$.ajax({
						type: "POST",
						url: "get_doctor.php",
						data: 'doctor=' + val,
						success: function(data) {
							$("#fees").val(data);
							loadAvailableSlots();
							
							// Load doctor schedule information
							$.ajax({
								url: 'get_doctor_schedule.php',
								type: 'POST',
								data: { doctor_id: val },
								success: function(scheduleData) {
									if(scheduleData.trim()) {
										$('#doctor_schedule_info').html(scheduleData);
										$('#selected_doctor_schedule').show();
									}
								},
								error: function() {
									$('#selected_doctor_schedule').hide();
								}
							});
							
							// Load doctor basic info
							$.ajax({
								url: 'get_doctor_info.php',
								type: 'POST',
								data: { doctor_id: val },
								success: function(infoData) {
									if(infoData.trim()) {
										$('#doctor_info').html(infoData);
									}
								}
							});
						},
						error: function() {
							$("#fees").val('Error loading fee');
						}
					});
				} else {
					$("#fees").val('');
					$('#available_slots').html('<option value="">Select Date and Doctor First</option>');
				}
			}
			
			// Load available slots function
			function loadAvailableSlots() {
				var doctorId = $('#doctor').val();
				var appointmentDate = $('input[name="appdate"]').val();
				
				if(doctorId && appointmentDate) {
					$.ajax({
						url: 'get_available_slots.php',
						type: 'POST',
						data: {
							doctor_id: doctorId,
							appointment_date: appointmentDate
						},
						success: function(data) {
							$('#available_slots').html(data);
						},
						error: function() {
							$('#available_slots').html('<option value="">Error loading slots</option>');
						}
					});
				} else {
					$('#available_slots').html('<option value="">Select Date and Doctor First</option>');
				}
			}
			
			jQuery(document).ready(function() {
				Main.init();
				FormElements.init();
				
				// Initialize datepicker
				$('.datepicker').datepicker({
					format: 'yyyy-mm-dd',
					startDate: '0d'
				});
				
				// Bind events
				$('#doctor').change(function() {
					getfee(this.value);
				});
				$('input[name="appdate"]').change(loadAvailableSlots);
				
				// Auto-load data if coming from dashboard
				<?php if($preselected_spec) { ?>
					setTimeout(function() {
						getdoctor('<?php echo $preselected_spec; ?>');
					}, 500);
				<?php } ?>
				
				// Auto-select doctor and load slots after a delay
				<?php if($preselected_doctor) { ?>
					setTimeout(function() {
						$('#doctor').val('<?php echo $preselected_doctor; ?>');
						getfee('<?php echo $preselected_doctor; ?>');
						setTimeout(loadAvailableSlots, 1000);
					}, 2000);
				<?php } ?>
			});
		</script>
		<!-- end: JavaScript Event Handlers for this page -->
		<!-- end: CLIP-TWO JAVASCRIPTS -->
	</body>
</html>
