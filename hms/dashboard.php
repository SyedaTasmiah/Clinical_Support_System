<?php
session_start();
//error_reporting(0);
include('include/config.php');
include('include/checklogin.php');
check_login();

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>User  | Dashboard</title>
		
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
									<h1 class="mainTitle">User | Dashboard</h1>
																	</div>
								<ol class="breadcrumb">
									<li>
										<span>User</span>
									</li>
									<li class="active">
										<span>Dashboard</span>
									</li>
								</ol>
							</div>
						</section>
						<!-- end: PAGE TITLE -->
						<!-- start: BASIC EXAMPLE -->
							<div class="container-fluid container-fullw bg-white">
							<div class="row">
								<div class="col-sm-4">
									<div class="panel panel-white no-radius text-center">
										<div class="panel-body">
											<span class="fa-stack fa-2x"> <i class="fa fa-square fa-stack-2x text-primary"></i> <i class="fa fa-smile-o fa-stack-1x fa-inverse"></i> </span>
											<h2 class="StepTitle">My Profile</h2>
											
											<p class="links cl-effect-1">
												<a href="edit-profile.php">
													Update Profile
												</a>
											</p>
										</div>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="panel panel-white no-radius text-center">
										<div class="panel-body">
											<span class="fa-stack fa-2x"> <i class="fa fa-square fa-stack-2x text-primary"></i> <i class="fa fa-paperclip fa-stack-1x fa-inverse"></i> </span>
											<h2 class="StepTitle">My Appointments</h2>
										
											<p class="cl-effect-1">
												<a href="appointment-history.php">
													View Appointment History
												</a>
											</p>
										</div>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="panel panel-white no-radius text-center">
										<div class="panel-body">
											<span class="fa-stack fa-2x"> <i class="fa fa-square fa-stack-2x text-primary"></i> <i class="fa fa-terminal fa-stack-1x fa-inverse"></i> </span>
											<h2 class="StepTitle"> Book My Appointment</h2>
											
											<p class="links cl-effect-1">
												<a href="book-appointment.php">
													Book Appointment
												</a>
											</p>
										</div>
									</div>
								</div>
							</div>
							
							<!-- EHR SECTION -->
							<div class="row" style="margin-top: 20px;">
								<div class="col-sm-6">
									<div class="panel panel-white no-radius text-center">
										<div class="panel-body">
											<span class="fa-stack fa-2x"> <i class="fa fa-square fa-stack-2x text-success"></i> <i class="fa fa-heartbeat fa-stack-1x fa-inverse"></i> </span>
											<h2 class="StepTitle">My Medical Records</h2>
											
											<p class="links cl-effect-1">
												<a href="ehr-records.php">
													View Complete Health Records
												</a>
											</p>
										</div>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="panel panel-white no-radius text-center">
										<div class="panel-body">
											<span class="fa-stack fa-2x"> <i class="fa fa-square fa-stack-2x text-info"></i> <i class="fa fa-pills fa-stack-1x fa-inverse"></i> </span>
											<h2 class="StepTitle">My Prescriptions</h2>
											
											<p class="links cl-effect-1">
												<a href="view-prescriptions.php">
													View Active Prescriptions
												</a>
											</p>
										</div>
									</div>
								</div>
							</div>
							</div>
							
							<!-- Quick Appointment Booking Widget -->
							<div class="row margin-top-30">
								<div class="col-md-8">
									<div class="panel panel-white">
										<div class="panel-heading">
											<h5 class="panel-title"><i class="fa fa-calendar"></i> Quick Appointment Booking</h5>
										</div>
										<div class="panel-body">
											<form id="quickBookingForm">
												<div class="row">
													<div class="col-md-4">
														<div class="form-group">
															<label>Specialization</label>
															<select class="form-control" id="quick_specialization" onchange="getQuickDoctors(this.value);">
																<option value="">Select Specialization</option>
																<?php 
																$ret=mysqli_query($con,"select * from doctorspecilization");
																while($row=mysqli_fetch_array($ret)) {
																?>
																<option value="<?php echo htmlentities($row['specilization']);?>">
																	<?php echo htmlentities($row['specilization']);?>
																</option>
																<?php } ?>
															</select>
														</div>
													</div>
													<div class="col-md-4">
														<div class="form-group">
															<label>Doctor</label>
															<select class="form-control" id="quick_doctor" onchange="getQuickFee(this.value);">
																<option value="">Select Doctor</option>
															</select>
														</div>
													</div>
													<div class="col-md-4">
														<div class="form-group">
															<label>Date</label>
															<input type="date" class="form-control" id="quick_date" min="<?php echo date('Y-m-d'); ?>" onchange="loadQuickSlots();">
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-md-6">
														<div class="form-group">
															<label>Available Time Slots</label>
															<select class="form-control" id="quick_slots">
																<option value="">Select Date and Doctor First</option>
															</select>
														</div>
													</div>
													<div class="col-md-3">
														<div class="form-group">
															<label>Consultation Fee</label>
															<input type="text" class="form-control" id="quick_fee" readonly>
														</div>
													</div>
													<div class="col-md-3">
														<div class="form-group">
															<label>&nbsp;</label>
															<button type="button" class="btn btn-primary btn-block" onclick="quickBookAppointment()">
																<i class="fa fa-calendar-plus-o"></i> Book Now
															</button>
														</div>
													</div>
												</div>
											</form>
										</div>
									</div>
								</div>
								<div class="col-md-4">
									<div class="panel panel-white">
										<div class="panel-heading">
											<h5 class="panel-title"><i class="fa fa-info-circle"></i> Appointment Info</h5>
										</div>
										<div class="panel-body">
											<div id="appointment_info">
												<p class="text-muted">Select a doctor and date to see available appointment slots.</p>
												<ul class="list-unstyled">
													<li><i class="fa fa-check text-success"></i> Real-time availability</li>
													<li><i class="fa fa-check text-success"></i> Instant confirmation</li>
													<li><i class="fa fa-check text-success"></i> No conflicts guaranteed</li>
												</ul>
											</div>
											<div id="doctor_schedule" style="display: none;">
												<h6>Doctor's Working Hours:</h6>
												<div id="schedule_details"></div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
			
					
					
						
						
					
						<!-- end: SELECT BOXES -->
						
					</div>
				</div>
			</div>
			<!-- start: FOOTER -->
	<?php include('include/footer.php');?>
			<!-- end: FOOTER -->
		
			<!-- start: SETTINGS -->
	<?php include('include/setting.php');?>
			<>
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
			jQuery(document).ready(function() {
				Main.init();
				FormElements.init();
			});
			
			// Quick booking functions
			function getQuickDoctors(specialization) {
				if(specialization) {
					$.ajax({
						type: "POST",
						url: "get_doctor.php",
						data: 'specilizationid=' + specialization,
						success: function(data) {
							$("#quick_doctor").html(data);
							$("#quick_fee").val('');
							$("#quick_slots").html('<option value="">Select Date and Doctor First</option>');
							$("#doctor_schedule").hide();
						}
					});
				} else {
					$("#quick_doctor").html('<option value="">Select Doctor</option>');
					$("#quick_fee").val('');
					$("#quick_slots").html('<option value="">Select Date and Doctor First</option>');
				}
			}
			
			function getQuickFee(doctorId) {
				if(doctorId) {
					$.ajax({
						type: "POST",
						url: "get_doctor.php",
						data: 'doctor=' + doctorId,
						success: function(data) {
							$("#quick_fee").val(data);
							loadQuickSlots();
							loadDoctorSchedule(doctorId);
						}
					});
				} else {
					$("#quick_fee").val('');
					$("#quick_slots").html('<option value="">Select Date and Doctor First</option>');
				}
			}
			
			function loadQuickSlots() {
				var doctorId = $('#quick_doctor').val();
				var appointmentDate = $('#quick_date').val();
				
				if(doctorId && appointmentDate) {
					$.ajax({
						url: 'get_available_slots.php',
						type: 'POST',
						data: {
							doctor_id: doctorId,
							appointment_date: appointmentDate
						},
						success: function(data) {
							$('#quick_slots').html(data);
						},
						error: function() {
							$('#quick_slots').html('<option value="">Error loading slots</option>');
						}
					});
				}
			}
			
			function loadDoctorSchedule(doctorId) {
				if(doctorId) {
					$.ajax({
						url: 'get_doctor_schedule.php',
						type: 'POST',
						data: { doctor_id: doctorId },
						success: function(data) {
							if(data.trim()) {
								$('#schedule_details').html(data);
								$('#doctor_schedule').show();
							}
						},
						error: function() {
							$('#doctor_schedule').hide();
						}
					});
				}
			}
			
			function quickBookAppointment() {
				var specialization = $('#quick_specialization').val();
				var doctorId = $('#quick_doctor').val();
				var date = $('#quick_date').val();
				var time = $('#quick_slots').val();
				var fee = $('#quick_fee').val();
				
				if(!specialization || !doctorId || !date || !time) {
					alert('Please fill all required fields');
					return;
				}
				
				// Redirect to full booking page with pre-filled data
				var url = 'book-appointment.php?spec=' + encodeURIComponent(specialization) + 
						  '&doctor=' + doctorId + '&date=' + date + '&time=' + encodeURIComponent(time);
				window.location.href = url;
			}
		</script>
		<!-- end: JavaScript Event Handlers for this page -->
		<!-- end: CLIP-TWO JAVASCRIPTS -->
	</body>
</html>
