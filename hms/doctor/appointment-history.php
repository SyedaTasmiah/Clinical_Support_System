<?php
session_start();
error_reporting(0);
include('include/config.php');
if(strlen($_SESSION['id']==0)) {
 header('location:logout.php');
  } else{

if(isset($_GET['cancel']))
		  {
mysqli_query($con,"update appointment set doctorStatus='0' where id ='".$_GET['id']."'");
                  $_SESSION['msg']="Appointment canceled !!";
		  }

// Handle marking consultation as done
if(isset($_GET['done']))
		  {
mysqli_query($con,"update appointment set consultationStatus='1' where id ='".$_GET['id']."'");
                  $_SESSION['msg']="Consultation marked as completed !!";
		  }
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Doctor | Appointment History</title>
		
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
			.btn-xs {
				margin-right: 5px;
				margin-bottom: 3px;
			}
			.label {
				font-size: 11px;
				padding: 5px 10px;
				border-radius: 4px;
				font-weight: bold;
				text-transform: uppercase;
				letter-spacing: 0.5px;
			}
			.label-success {
				background-color: #28a745;
				color: white;
			}
			.label-primary {
				background-color: #007bff;
				color: white;
			}
			.label-warning {
				background-color: #ffc107;
				color: #212529;
			}
			.label-danger {
				background-color: #dc3545;
				color: white;
			}
			.label-default {
				background-color: #6c757d;
				color: white;
			}
			.badge-success {
				background-color: #28a745;
				color: white;
				padding: 4px 8px;
				border-radius: 3px;
				font-size: 10px;
				font-weight: bold;
				margin-right: 8px;
				display: inline-block;
			}
			/* Row highlighting based on status */
			.table tbody tr.treated-patient {
				background-color: #f8fff8;
				border-left: 4px solid #28a745;
			}
			.table tbody tr.upcoming-patient {
				background-color: #f0f8ff;
				border-left: 4px solid #007bff;
			}
			.table tbody tr.pending-treatment {
				background-color: #fffbf0;
				border-left: 4px solid #ffc107;
			}
			.table tbody tr.missing-patient {
				background-color: #fcf8e3;
				border-left: 4px solid #f0ad4e;
			}
			.table tbody tr.cancelled-appointment {
				background-color: #f8f8f8;
				border-left: 4px solid #6c757d;
				opacity: 0.7;
			}
			/* Filter button styling */
			.filter-btn {
				margin-right: 5px;
				margin-bottom: 5px;
			}
			.filter-btn.active {
				box-shadow: 0 2px 4px rgba(0,0,0,0.2);
			}
			/* Action buttons container */
			.action-buttons {
				white-space: nowrap;
			}
			/* Count indicator styling */
			.count-indicator {
				margin-bottom: 15px;
			}
		</style>
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
									<h1 class="mainTitle">Doctor  | Appointment History</h1>
																	</div>
								<ol class="breadcrumb">
									<li>
										<span>Doctor </span>
									</li>
									<li class="active">
										<span>Appointment History</span>
									</li>
								</ol>
							</div>
						</section>
						<!-- end: PAGE TITLE -->
						<!-- start: BASIC EXAMPLE -->
						<div class="container-fluid container-fullw bg-white">
						
						<!-- Filter Section -->
						<div class="row" style="margin-bottom: 20px;">
							<div class="col-md-12">
								<div class="panel panel-white">
									<div class="panel-body">
										<h5><i class="fa fa-filter"></i> Filter Appointments</h5>
										<div class="btn-group" role="group">
											<button type="button" class="btn btn-default filter-btn" data-filter="all">
												<i class="fa fa-list"></i> All Appointments
											</button>
											<button type="button" class="btn btn-primary filter-btn" data-filter="upcoming-patient">
												<i class="fa fa-clock-o"></i> Upcoming Patients
											</button>
																					<button type="button" class="btn btn-warning filter-btn" data-filter="pending-treatment">
											<i class="fa fa-stethoscope"></i> Pending Treatment
										</button>
										<button type="button" class="btn btn-danger filter-btn" data-filter="missing-patient">
											<i class="fa fa-user-times"></i> Missing Patients
										</button>
										<button type="button" class="btn btn-success filter-btn" data-filter="treated-patient">
											<i class="fa fa-check-circle"></i> Treated Patients
										</button>
																					<button type="button" class="btn btn-default filter-btn" data-filter="cancelled-appointment">
											<i class="fa fa-times-circle"></i> Cancelled
										</button>
										

									</div>
								</div>
								</div>
							</div>
						</div>

									<div class="row">
								<div class="col-md-12">
									
									<p style="color:red;"><?php echo htmlentities($_SESSION['msg']);?>
								<?php echo htmlentities($_SESSION['msg']="");?></p>	
									<table class="table table-hover" id="sample-table-1">
										<thead>
											<tr>
												<th class="center">#</th>
												<th class="hidden-xs">Patient  Name</th>
												<th>Specialization</th>
												<th>Consultancy Fee</th>
												<th>Appointment Date / Time </th>
												<th>Appointment Creation Date  </th>
												<th>Current Status</th>
												<th>Action</th>
												
											</tr>
										</thead>
										<tbody>
<?php
$sql=mysqli_query($con,"select users.fullName as fname,appointment.*  from appointment join users on users.id=appointment.userId where appointment.doctorId='".$_SESSION['id']."'");
$cnt=1;
while($row=mysqli_fetch_array($sql))
{
?>


											<tr class="<?php 
												if(($row['userStatus']==1) && ($row['doctorStatus']==1)) {
													if(isset($row['consultationStatus']) && $row['consultationStatus']==1) {
														echo 'treated-patient';
													} else {
														$appointment_datetime = $row['appointmentDate'] . ' ' . $row['appointmentTime'];
														$appointment_timestamp = strtotime($appointment_datetime);
														$current_timestamp = time();
														
														// Check if appointment is more than 1 hour past the scheduled time (missing patient)
														$one_hour_later = $appointment_timestamp + (60 * 60); // Add 1 hour
														
														if($appointment_timestamp > $current_timestamp) {
															echo 'upcoming-patient';
														} else if($current_timestamp > $one_hour_later) {
															echo 'missing-patient';
														} else {
															echo 'pending-treatment';
														}
													}
												} else {
													echo 'cancelled-appointment';
												}
											?>">
												<td class="center"><?php echo $cnt;?>.</td>
												<td class="hidden-xs"><?php echo $row['fname'];?></td>
												<td><?php echo $row['doctorSpecialization'];?></td>
												<td><?php echo $row['consultancyFees'];?></td>
												<td><?php echo $row['appointmentDate'];?> / <?php echo
												 $row['appointmentTime'];?>
												</td>
												<td><?php echo $row['postingDate'];?></td>
												<td>
<?php if(($row['userStatus']==1) && ($row['doctorStatus']==1))  
{
	// Check if consultation is completed
	if(isset($row['consultationStatus']) && $row['consultationStatus']==1) {
		echo "<span class='label label-success'><i class='fa fa-check-circle'></i> TREATED</span>";
	} else {
		// Check if appointment is upcoming or current
		$appointment_datetime = $row['appointmentDate'] . ' ' . $row['appointmentTime'];
		$appointment_timestamp = strtotime($appointment_datetime);
		$current_timestamp = time();
		
		// Check if appointment is more than 1 hour past the scheduled time (missing patient)
		$one_hour_later = $appointment_timestamp + (60 * 60); // Add 1 hour
		
		if($appointment_timestamp > $current_timestamp) {
			echo "<span class='label label-primary'><i class='fa fa-clock-o'></i> UPCOMING</span>";
		} else if($current_timestamp > $one_hour_later) {
			echo "<span class='label label-danger'><i class='fa fa-user-times'></i> MISSING PATIENT</span>";
		} else {
			echo "<span class='label label-warning'><i class='fa fa-stethoscope'></i> PENDING TREATMENT</span>";
		}
	}
}
if(($row['userStatus']==0) && ($row['doctorStatus']==1))  
{
	echo "<span class='label label-default'><i class='fa fa-times-circle'></i> CANCELLED BY PATIENT</span>";
}

if(($row['userStatus']==1) && ($row['doctorStatus']==0))  
{
	echo "<span class='label label-danger'><i class='fa fa-ban'></i> CANCELLED BY DOCTOR</span>";
}



												?></td>
												<td >
												<div class="visible-md visible-lg hidden-sm hidden-xs action-buttons">
							<?php if(($row['userStatus']==1) && ($row['doctorStatus']==1))  
{ 
	// Check if consultation is completed
	if(isset($row['consultationStatus']) && $row['consultationStatus']==1) {
		// If consultation is done, show view history option
		echo "<span class='badge badge-success'>✓ COMPLETED</span>";
		echo "<a href='update-medical-history.php?appointment_id=" . $row['id'] . "&patient_id=" . $row['userId'] . "' class='btn btn-info btn-xs' title='View Medical History'><i class='fa fa-eye'></i> View History</a>";
	} else {
		// Check if appointment time to show appropriate actions
		$appointment_datetime = $row['appointmentDate'] . ' ' . $row['appointmentTime'];
		$appointment_timestamp = strtotime($appointment_datetime);
		$current_timestamp = time();
		
		// Check if appointment is more than 1 hour past the scheduled time (missing patient)
		$one_hour_later = $appointment_timestamp + (60 * 60); // Add 1 hour
		
		if($appointment_timestamp > $current_timestamp) {
			// Upcoming appointment - can cancel or reschedule
			echo "<a href='appointment-history.php?id=" . $row['id'] . "&cancel=update' onClick='return confirm(\"Are you sure you want to cancel this upcoming appointment?\")' class='btn btn-danger btn-xs' title='Cancel Appointment'><i class='fa fa-times'></i> Cancel</a>";
		} else if($current_timestamp > $one_hour_later) {
			// Missing patient - show specific actions
			echo "<a href='appointment-history.php?id=" . $row['id'] . "&cancel=update' onClick='return confirm(\"Are you sure you want to cancel this appointment?\")' class='btn btn-danger btn-xs' title='Cancel'><i class='fa fa-times'></i> Cancel</a>";
			echo "<a href='update-medical-history.php?appointment_id=" . $row['id'] . "&patient_id=" . $row['userId'] . "' class='btn btn-warning btn-xs' title='Mark as No-Show'><i class='fa fa-user-times'></i> Mark No-Show</a>";
		} else {
			// Current/Past appointment - ready for treatment
			echo "<a href='appointment-history.php?id=" . $row['id'] . "&cancel=update' onClick='return confirm(\"Are you sure you want to cancel this appointment?\")' class='btn btn-danger btn-xs' title='Cancel'><i class='fa fa-times'></i> Cancel</a>";
			echo "<a href='update-medical-history.php?appointment_id=" . $row['id'] . "&patient_id=" . $row['userId'] . "' class='btn btn-success btn-xs' title='Update Medical History'><i class='fa fa-plus'></i> Add History</a>";
			echo "<a href='appointment-history.php?id=" . $row['id'] . "&done=update' onClick='return confirm(\"Mark this consultation as treated/completed?\")' class='btn btn-primary btn-xs' title='Mark as Treated'><i class='fa fa-check'></i> Mark Treated</a>";
		}
	}
} else {
	if(($row['userStatus']==0) && ($row['doctorStatus']==1)) {
		echo "<span class='text-muted'>Cancelled by Patient</span>";
	} else if(($row['userStatus']==1) && ($row['doctorStatus']==0)) {
		echo "<span class='text-muted'>Cancelled by Doctor</span>";
	}
} ?>
												</div>
												</td>
											</tr>
											
											<?php 
$cnt=$cnt+1;
											 }?>
											
											
										</tbody>
									</table>
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
			jQuery(document).ready(function() {
				Main.init();
				FormElements.init();
				
				// Filter functionality
				$('.filter-btn').click(function() {
					var filter = $(this).data('filter');
					
					// Update active button
					$('.filter-btn').removeClass('active');
					$(this).addClass('active');
					
					// Show/hide rows based on filter
					if(filter === 'all') {
						$('#sample-table-1 tbody tr').show();
					} else {
						$('#sample-table-1 tbody tr').hide();
						$('#sample-table-1 tbody tr.' + filter).show();
					}
					
					// Update count display
					var visibleRows = $('#sample-table-1 tbody tr:visible').length;
					updateCountDisplay(filter, visibleRows);
				});
				
				// Set default filter to all
				$('.filter-btn[data-filter="all"]').addClass('active');
			});
			
			function updateCountDisplay(filter, count) {
				var filterText = '';
				switch(filter) {
					case 'all': filterText = 'All Appointments'; break;
					case 'upcoming-patient': filterText = 'Upcoming Patients'; break;
					case 'pending-treatment': filterText = 'Pending Treatment'; break;
					case 'missing-patient': filterText = 'Missing Patients'; break;
					case 'treated-patient': filterText = 'Treated Patients'; break;
					case 'cancelled-appointment': filterText = 'Cancelled Appointments'; break;
				}
				
				// Add count indicator if it doesn't exist
				if($('.count-indicator').length === 0) {
					$('table').before('<div class="count-indicator alert alert-info"></div>');
				}
				
				$('.count-indicator').html('<i class="fa fa-info-circle"></i> Showing ' + count + ' ' + filterText.toLowerCase());
			}
		</script>
		<!-- end: JavaScript Event Handlers for this page -->
		<!-- end: CLIP-TWO JAVASCRIPTS -->
	</body>
</html>
<?php } ?>
