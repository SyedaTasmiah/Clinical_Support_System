<?php
session_start();
error_reporting(0);
include('include/config.php');
if(strlen($_SESSION['id']==0)) {
 header('location:logout.php');
  } else{

// Handle admin actions
if(isset($_GET['cancel']) && $_GET['cancel'] == 'admin' && isset($_GET['id'])) {
    $appointment_id = mysqli_real_escape_string($con, $_GET['id']);
    
    // Update both user and doctor status to cancelled
    $update_sql = "UPDATE appointment SET userStatus = 0, doctorStatus = 0 WHERE id = '$appointment_id'";
    
    if(mysqli_query($con, $update_sql)) {
        $_SESSION['msg'] = "Appointment cancelled successfully!";
    } else {
        $_SESSION['msg'] = "Error cancelling appointment: " . mysqli_error($con);
    }
    
    // Redirect to refresh the page
    header('location:appointment-history.php');
    exit();
}

// Handle mark as no-show
if(isset($_GET['mark']) && $_GET['mark'] == 'no-show' && isset($_GET['id'])) {
    $appointment_id = mysqli_real_escape_string($con, $_GET['id']);
    
    // Update appointment status to no-show (you might want to add a no_show field to your table)
    $update_sql = "UPDATE appointment SET userStatus = 0, doctorStatus = 0 WHERE id = '$appointment_id'";
    
    if(mysqli_query($con, $update_sql)) {
        $_SESSION['msg'] = "Patient marked as No-Show successfully!";
    } else {
        $_SESSION['msg'] = "Error marking patient as No-Show: " . mysqli_error($con);
    }
    
    // Redirect to refresh the page
    header('location:appointment-history.php');
    exit();
}

// Handle reschedule request
if(isset($_GET['reschedule']) && $_GET['reschedule'] == 'admin' && isset($_GET['id'])) {
    $appointment_id = mysqli_real_escape_string($con, $_GET['id']);
    
    // For now, just redirect to a reschedule page or show a message
    $_SESSION['msg'] = "Reschedule functionality will be implemented in the next update.";
    
    // Redirect to refresh the page
    header('location:appointment-history.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Patients | Appointment History</title>
		
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
				margin: 2px;
			}
			.tooltips {
				position: relative;
				display: inline-block;
			}
			.tooltips[data-tooltip]:hover::after {
				content: attr(data-tooltip);
				position: absolute;
				bottom: 100%;
				left: 50%;
				transform: translateX(-50%);
				background: #333;
				color: white;
				padding: 5px 10px;
				border-radius: 4px;
				font-size: 12px;
				white-space: nowrap;
				z-index: 1000;
			}
			.label {
				font-size: 11px;
				padding: 3px 6px;
				margin: 2px;
			}
			.label-success {
				background-color: #5cb85c;
			}
			.label-danger {
				background-color: #d9534f;
			}
			.label-warning {
				background-color: #f0ad4e;
			}
			.label-default {
				background-color: #777;
			}
			.filter-btn {
				margin-right: 10px;
				margin-bottom: 10px;
			}
			.filter-btn.active {
				background-color: #337ab7;
				border-color: #2e6da4;
				color: white;
			}
			.table tr.active {
				background-color: #dff0d8;
			}
			.table tr.missing {
				background-color: #fcf8e3;
				border-left: 4px solid #f0ad4e;
			}
			.table tr.cancelled {
				background-color: #f2dede;
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
									<h1 class="mainTitle">Patients  | Appointment History</h1>
																	</div>
								<ol class="breadcrumb">
									<li>
										<span>Patients </span>
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
						

									<div class="row">
								<div class="col-md-12">
									
									<p style="color:red;"><?php echo htmlentities($_SESSION['msg']);?>
								<?php echo htmlentities($_SESSION['msg']="");?></p>
								
								<!-- Filter Buttons -->
								<div class="row" style="margin-bottom: 20px;">
									<div class="col-md-12">
										<button type="button" class="btn btn-default filter-btn active" data-filter="all">
											<i class="fa fa-list"></i> All Appointments
										</button>
										<button type="button" class="btn btn-success filter-btn" data-filter="active">
											<i class="fa fa-check"></i> Active
										</button>
										<button type="button" class="btn btn-danger filter-btn" data-filter="missing">
											<i class="fa fa-user-times"></i> Missing Patients
										</button>
										<button type="button" class="btn btn-warning filter-btn" data-filter="cancelled">
											<i class="fa fa-times"></i> Cancelled
										</button>
										
										
									</div>
								</div>
								
								
								
								<table class="table table-hover" id="sample-table-1">
										<thead>
											<tr>
												<th class="center">#</th>
												<th class="hidden-xs">Doctor Name</th>
												<th>Patient Name</th>
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
$sql=mysqli_query($con,"select doctors.doctorName as docname,users.fullName as pname,appointment.*  from appointment join doctors on doctors.id=appointment.doctorId join users on users.id=appointment.userId ");
$cnt=1;

// Debug: Show the first row data
$debug_row = mysqli_fetch_array($sql);
if($debug_row) {
	echo "<!-- First Row Debug: userStatus=" . $debug_row['userStatus'] . ", doctorStatus=" . $debug_row['doctorStatus'] . ", appointmentDate=" . $debug_row['appointmentDate'] . ", appointmentTime=" . $debug_row['appointmentTime'] . " -->";
}

// Reset the result pointer
mysqli_data_seek($sql, 0);

while($row=mysqli_fetch_array($sql))
{
?>

											<?php 
											$appointment_date = $row['appointmentDate'];
											$appointment_time = $row['appointmentTime'];
											
											// Handle different time formats and ensure proper parsing
											if($appointment_date && $appointment_time) {
												// Clean up the time format - remove seconds if present
												$clean_time = $appointment_time;
												if(strlen($clean_time) > 5) {
													$clean_time = substr($clean_time, 0, 5);
												}
												
												// Always combine date and time properly
												$appointment_datetime = strtotime($appointment_date . ' ' . $clean_time);
												
												// Debug: Show the exact string being parsed
												echo "<!-- Debug: Parsing '$appointment_date $clean_time' -> " . date('Y-m-d H:i:s', $appointment_datetime) . " -->";
											} else {
												$appointment_datetime = 0;
											}
											
											$current_datetime = time();
											
											// Calculate missing status first
											$is_missing = (($row['userStatus']==1) && ($row['doctorStatus']==1) && ($appointment_datetime > 0) && ($current_datetime > $appointment_datetime));
											
											// Debug: Add this temporarily to see what's happening
											echo "<!-- Debug: Date: $appointment_date, Time: $appointment_time, DateTime: " . ($appointment_datetime > 0 ? date('Y-m-d H:i:s', $appointment_datetime) : 'Invalid') . ", Current: " . date('Y-m-d H:i:s', $current_datetime) . ", IsMissing: " . ($is_missing ? 'Yes' : 'No') . ", userStatus: " . $row['userStatus'] . ", doctorStatus: " . $row['doctorStatus'] . " -->";
											
											if(($row['userStatus']==1) && ($row['doctorStatus']==1)) {
												if($is_missing) {
													$row_class = 'missing';
												} else {
													$row_class = 'active';
												}
											} else {
												$row_class = 'cancelled';
											}
											?>
											<tr class="<?php echo $row_class; ?>">
												<td class="center"><?php echo $cnt;?>.</td>
												<td class="hidden-xs"><?php echo $row['docname'];?></td>
												<td class="hidden-xs"><?php echo $row['pname'];?></td>
												<td><?php echo $row['doctorSpecialization'];?></td>
												<td><?php echo $row['consultancyFees'];?></td>
												<td><?php echo $row['appointmentDate'];?> / <?php echo
												 $row['appointmentTime'];?>
												</td>
												<td><?php echo $row['postingDate'];?></td>
												<td>
<?php 
$appointment_date = $row['appointmentDate'];
$appointment_time = $row['appointmentTime'];

// Handle different time formats and ensure proper parsing
if($appointment_date && $appointment_time) {
	// Clean up the time format - remove seconds if present
	$clean_time = $appointment_time;
	if(strlen($clean_time) > 5) {
		$clean_time = substr($clean_time, 0, 5);
	}
	
	// Always combine date and time properly
	$appointment_datetime = strtotime($appointment_date . ' ' . $clean_time);
} else {
	$appointment_datetime = 0;
}

$current_datetime = time();

if(($row['userStatus']==1) && ($row['doctorStatus']==1)) {
	// Check if appointment date has passed
	if($appointment_datetime > 0 && $current_datetime > $appointment_datetime) {
		echo "<span class='label label-danger'>Missing Patient</span>";
	} else {
		echo "<span class='label label-success'>Active</span>";
	}
} else if(($row['userStatus']==0) && ($row['doctorStatus']==1)) {
	echo "<span class='label label-warning'>Cancelled by Patient</span>";
} else if(($row['userStatus']==1) && ($row['doctorStatus']==0)) {
	echo "<span class='label label-warning'>Cancelled by Doctor</span>";
} else if(($row['userStatus']==0) && ($row['doctorStatus']==0)) {
	echo "<span class='label label-default'>Cancelled</span>";
}
?>
												</td>
												<td>
												<div class="visible-md visible-lg hidden-sm hidden-xs">
													<?php 
													$appointment_date = $row['appointmentDate'];
													$appointment_time = $row['appointmentTime'];
													
													// Handle different time formats and ensure proper parsing
													if($appointment_date && $appointment_time) {
														// Clean up the time format - remove seconds if present
														$clean_time = $appointment_time;
														if(strlen($clean_time) > 5) {
															$clean_time = substr($clean_time, 0, 5);
														}
														
														// Always combine date and time properly
														$appointment_datetime = strtotime($appointment_date . ' ' . $clean_time);
													} else {
														$appointment_datetime = 0;
													}
													
													$current_datetime = time();
													$is_missing = (($row['userStatus']==1) && ($row['doctorStatus']==1) && ($appointment_datetime > 0) && ($current_datetime > $appointment_datetime));
													
													if(($row['userStatus']==1) && ($row['doctorStatus']==1)) { 
														if($is_missing) { ?>
															<!-- Missing patient - show specific actions -->
															<a href="view-appointment-details.php?id=<?php echo $row['id']; ?>" class="btn btn-info btn-xs tooltips" data-tooltip="View Details">
																<i class="fa fa-eye"></i>
															</a>
															<a href="appointment-history.php?id=<?php echo $row['id']; ?>&mark=no-show" onClick="return confirm('Mark this patient as No-Show?')" class="btn btn-danger btn-xs tooltips" data-tooltip="Mark as No-Show">
																<i class="fa fa-user-times"></i>
															</a>
															<a href="appointment-history.php?id=<?php echo $row['id']; ?>&reschedule=admin" class="btn btn-warning btn-xs tooltips" data-tooltip="Reschedule Appointment">
																<i class="fa fa-calendar-plus-o"></i>
															</a>
														<?php } else { ?>
															<!-- Active appointment - show management options -->
															<a href="view-appointment-details.php?id=<?php echo $row['id']; ?>" class="btn btn-info btn-xs tooltips" data-tooltip="View Details">
																<i class="fa fa-eye"></i>
															</a>
															<a href="appointment-history.php?id=<?php echo $row['id']; ?>&cancel=admin" onClick="return confirm('Are you sure you want to cancel this appointment?')" class="btn btn-warning btn-xs tooltips" data-tooltip="Cancel Appointment">
																<i class="fa fa-times"></i>
															</a>
															<a href="edit-appointment.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-xs tooltips" data-tooltip="Edit Appointment">
																<i class="fa fa-edit"></i>
															</a>
														<?php } ?>
													<?php } else { ?>
														<!-- Cancelled appointment - show view and restore options -->
														<a href="view-appointment-details.php?id=<?php echo $row['id']; ?>" class="btn btn-info btn-xs tooltips" data-tooltip="View Details">
															<i class="fa fa-eye"></i>
														</a>
														<?php if(($row['userStatus']==0) && ($row['doctorStatus']==1)) { ?>
															<span class="label label-danger">Cancelled by Patient</span>
														<?php } else if(($row['userStatus']==1) && ($row['doctorStatus']==0)) { ?>
															<span class="label label-warning">Cancelled by Doctor</span>
														<?php } ?>
													<?php } ?>
												</div>
												<div class="visible-xs visible-sm hidden-md hidden-lg">
													<div class="btn-group">
														<button type="button" class="btn btn-primary btn-o btn-sm dropdown-toggle" data-toggle="dropdown">
															<i class="fa fa-cog"></i>&nbsp;<span class="caret"></span>
														</button>
														<ul class="dropdown-menu pull-right" role="menu">
															<li>
																<a href="view-appointment-details.php?id=<?php echo $row['id']; ?>">
																	<i class="fa fa-eye"></i> View Details
																</a>
															</li>
															<?php 
															$appointment_date = $row['appointmentDate'];
															$appointment_time = $row['appointmentTime'];
															
															// Handle different time formats and ensure proper parsing
															if($appointment_date && $appointment_time) {
																// Clean up the time format - remove seconds if present
																$clean_time = $appointment_time;
																if(strlen($clean_time) > 5) {
																	$clean_time = substr($clean_time, 0, 5);
																}
																
																// Always combine date and time properly
																$appointment_datetime = strtotime($appointment_date . ' ' . $clean_time);
															} else {
																$appointment_datetime = 0;
															}
															
															$current_datetime = time();
															$is_missing = (($row['userStatus']==1) && ($row['doctorStatus']==1) && ($appointment_datetime > 0) && ($current_datetime > $appointment_datetime));
															
															if(($row['userStatus']==1) && ($row['doctorStatus']==1)) { 
																if($is_missing) { ?>
																	<li>
																		<a href="appointment-history.php?id=<?php echo $row['id']; ?>&mark=no-show" onClick="return confirm('Mark this patient as No-Show?')">
																			<i class="fa fa-user-times"></i> Mark as No-Show
																		</a>
																	</li>
																	<li>
																		<a href="appointment-history.php?id=<?php echo $row['id']; ?>&reschedule=admin">
																			<i class="fa fa-calendar-plus-o"></i> Reschedule
																		</a>
																	</li>
																<?php } else { ?>
																	<li>
																		<a href="edit-appointment.php?id=<?php echo $row['id']; ?>">
																			<i class="fa fa-edit"></i> Edit
																		</a>
																	</li>
																	<li>
																		<a href="appointment-history.php?id=<?php echo $row['id']; ?>&cancel=admin" onClick="return confirm('Are you sure you want to cancel this appointment?')">
																			<i class="fa fa-times"></i> Cancel
																		</a>
																	</li>
																<?php } ?>
															<?php } ?>
														</ul>
													</div>
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
				try {
					if (typeof Main !== 'undefined') {
						Main.init();
					}
					
					if (typeof FormElements !== 'undefined') {
						FormElements.init();
					}
				} catch (error) {
					console.log('Some initialization functions not available:', error.message);
				}
				
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
				});
			});
		</script>
		<!-- end: JavaScript Event Handlers for this page -->
		<!-- end: CLIP-TWO JAVASCRIPTS -->
	</body>
</html>
<?php } ?>
