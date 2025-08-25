<?php
session_start();
error_reporting(0);
include('include/config.php');
if(strlen($_SESSION['id']==0)) {
 header('location:logout.php');
  } else{

if(isset($_POST['submit']))
  {
    
    $vid=$_GET['viewid'];
    $patient_type = $_GET['type'] ?? 'old-patient';
    $bp=$_POST['bp'];
    $bs=$_POST['bs'];
    $weight=$_POST['weight'];
    $temp=$_POST['temp'];
   $pres=$_POST['pres'];
   
 
      $query.=mysqli_query($con, "insert   tblmedicalhistory(PatientID,BloodPressure,BloodSugar,Weight,Temperature,MedicalPres)value('$vid','$bp','$bs','$weight','$temp','$pres')");
    if ($query) {
    echo '<script>alert("Medical history has been added.")</script>';
    echo "<script>window.location.href ='manage-users.php'</script>";
  }
  else
    {
      echo '<script>alert("Something Went Wrong. Please try again")</script>';
    }

  
}

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Admin | View Patient</title>
		
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
			.patient-header {
				background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
				color: white;
				padding: 30px;
				border-radius: 10px;
				margin-bottom: 30px;
				text-align: center;
			}
			.patient-type-badge {
				font-size: 12px;
				padding: 8px 15px;
				border-radius: 20px;
				font-weight: bold;
				text-transform: uppercase;
				display: inline-block;
				margin-top: 10px;
			}
			.patient-type-user {
				background-color: #007bff;
				color: white;
			}
			.patient-type-old {
				background-color: #ffc107;
				color: #212529;
			}
			.details-table {
				border-radius: 8px;
				overflow: hidden;
				box-shadow: 0 2px 10px rgba(0,0,0,0.1);
			}
			.details-table th {
				background-color: #f8f9fa;
				border-top: 2px solid #dee2e6;
				font-weight: 600;
			}
			.medical-history {
				background-color: #f8f9fa;
				padding: 20px;
				border-radius: 8px;
				margin-top: 30px;
			}
			.back-btn {
				margin-bottom: 20px;
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
									<h1 class="mainTitle">Admin | View Patient</h1>
								</div>
								<ol class="breadcrumb">
									<li>
										<span>Admin</span>
									</li>
									<li class="active">
										<span>View Patient</span>
									</li>
								</ol>
							</div>
						</section>
						<!-- end: PAGE TITLE -->
						
						<div class="container-fluid container-fullw bg-white">
							<div class="row">
								<div class="col-md-12">
									<!-- Back Button -->
									<div class="back-btn">
										<a href="patient-search.php" class="btn btn-secondary">
											<i class="fa fa-arrow-left"></i> Back to Search
										</a>
										<a href="manage-users.php" class="btn btn-info" style="margin-left: 10px;">
											<i class="fa fa-users"></i> Manage All Patients
										</a>
									</div>
									
									<?php
									$vid = $_GET['viewid'];
									$patient_type = $_GET['type'] ?? 'old-patient';
									
									// Determine which table to query based on patient type
									if($patient_type == 'user-patient') {
										// Query users table
										$ret = mysqli_query($con, "SELECT * FROM users WHERE id='$vid'");
										$table_name = 'users';
									} else {
										// Query tblpatient table (default)
										$ret = mysqli_query($con, "SELECT * FROM tblpatient WHERE ID='$vid'");
										$table_name = 'tblpatient';
									}
									
									if($ret && mysqli_num_rows($ret) > 0) {
										$row = mysqli_fetch_array($ret);
									?>
									
									<!-- Patient Header -->
									<div class="patient-header">
										<h2><i class="fa fa-user"></i> Patient Details</h2>
										<?php if($patient_type == 'user-patient'): ?>
											<span class="patient-type-badge patient-type-user">User Patient</span>
										<?php else: ?>
											<span class="patient-type-badge patient-type-old">Old Patient</span>
										<?php endif; ?>
									</div>
									
									<!-- Patient Details Table -->
									<div class="table-responsive">
										<table class="table table-bordered details-table">
											<tbody>
												<tr>
													<th width="200">Patient Name</th>
													<td>
														<?php 
														if($patient_type == 'user-patient') {
															echo htmlspecialchars($row['fullName'] ?? 'N/A');
														} else {
															echo htmlspecialchars($row['PatientName'] ?? 'N/A');
														}
														?>
													</td>
													<th width="200">Patient Email</th>
													<td>
														<?php 
														if($patient_type == 'user-patient') {
															echo htmlspecialchars($row['email'] ?? 'N/A');
														} else {
															echo htmlspecialchars($row['PatientEmail'] ?? 'N/A');
														}
														?>
													</td>
												</tr>
												<tr>
													<th>Contact Number</th>
													<td>
														<?php 
														if($patient_type == 'user-patient') {
															echo htmlspecialchars($row['address'] ?? 'N/A');
														} else {
															echo htmlspecialchars($row['PatientContno'] ?? 'N/A');
														}
														?>
													</td>
													<th>Address</th>
													<td>
														<?php 
														if($patient_type == 'user-patient') {
															echo htmlspecialchars($row['city'] ?? 'N/A');
														} else {
															echo htmlspecialchars($row['PatientAdd'] ?? 'N/A');
														}
														?>
													</td>
												</tr>
												<tr>
													<th>Gender</th>
													<td>
														<?php 
														if($patient_type == 'user-patient') {
															echo htmlspecialchars($row['gender'] ?? 'N/A');
														} else {
															echo htmlspecialchars($row['PatientGender'] ?? 'N/A');
														}
														?>
													</td>
													<th>Registration Date</th>
													<td>
														<?php 
														if($patient_type == 'user-patient') {
															echo htmlspecialchars($row['regDate'] ?? 'N/A');
														} else {
															echo htmlspecialchars($row['CreationDate'] ?? 'N/A');
														}
														?>
													</td>
												</tr>
												<?php if($patient_type == 'old-patient'): ?>
												<tr>
													<th>Patient Age</th>
													<td><?php echo htmlspecialchars($row['PatientAge'] ?? 'N/A'); ?></td>
													<th>Medical History</th>
													<td><?php echo htmlspecialchars($row['PatientMedhis'] ?? 'N/A'); ?></td>
												</tr>
												<?php endif; ?>
												<?php if($patient_type == 'user-patient'): ?>
												<tr>
													<th>Last Updated</th>
													<td><?php echo htmlspecialchars($row['updationDate'] ?? 'N/A'); ?></td>
													<th>Address Details</th>
													<td><?php echo htmlspecialchars($row['address'] ?? 'N/A'); ?></td>
												</tr>
												<?php endif; ?>
											</tbody>
										</table>
									</div>
									
									<!-- Medical History Section -->
									<div class="medical-history">
										<h4><i class="fa fa-history"></i> Medical History</h4>
										
										<?php
										// Query medical history based on patient type
										if($patient_type == 'user-patient') {
											$medical_query = mysqli_query($con, "SELECT * FROM tblmedicalhistory WHERE PatientID='$vid'");
										} else {
											$medical_query = mysqli_query($con, "SELECT * FROM tblmedicalhistory WHERE PatientID='$vid'");
										}
										
										if($medical_query && mysqli_num_rows($medical_query) > 0) {
										?>
										<div class="table-responsive">
											<table class="table table-bordered table-striped">
												<thead>
													<tr>
														<th>#</th>
														<th>Blood Pressure</th>
														<th>Weight</th>
														<th>Blood Sugar</th>
														<th>Body Temperature</th>
														<th>Medical Prescription</th>
														<th>Visit Date</th>
													</tr>
												</thead>
												<tbody>
													<?php  
													$cnt = 1;
													while ($medical_row = mysqli_fetch_array($medical_query)) { 
													?>
													<tr>
														<td><?php echo $cnt; ?></td>
														<td><?php echo htmlspecialchars($medical_row['BloodPressure'] ?? 'N/A'); ?></td>
														<td><?php echo htmlspecialchars($medical_row['Weight'] ?? 'N/A'); ?></td>
														<td><?php echo htmlspecialchars($medical_row['BloodSugar'] ?? 'N/A'); ?></td>
														<td><?php echo htmlspecialchars($medical_row['Temperature'] ?? 'N/A'); ?></td>
														<td><?php echo htmlspecialchars($medical_row['MedicalPres'] ?? 'N/A'); ?></td>
														<td><?php echo htmlspecialchars($medical_row['CreationDate'] ?? 'N/A'); ?></td>
													</tr>
													<?php 
													$cnt++;
													} 
													?>
												</tbody>
											</table>
										</div>
										<?php } else { ?>
										<div class="alert alert-info">
											<i class="fa fa-info-circle"></i> No medical history records found for this patient.
										</div>
										<?php } ?>
										
										<!-- Add Medical History Form -->
										<div class="row" style="margin-top: 20px;">
											<div class="col-md-12">
												<h5><i class="fa fa-plus"></i> Add Medical History</h5>
												<form method="post">
													<div class="row">
														<div class="col-md-2">
															<div class="form-group">
																<label>Blood Pressure</label>
																<input type="text" name="bp" class="form-control" placeholder="e.g., 120/80">
															</div>
														</div>
														<div class="col-md-2">
															<div class="form-group">
																<label>Blood Sugar</label>
																<input type="text" name="bs" class="form-control" placeholder="e.g., 100 mg/dL">
															</div>
														</div>
														<div class="col-md-2">
															<div class="form-group">
																<label>Weight</label>
																<input type="text" name="weight" class="form-control" placeholder="e.g., 70 kg">
															</div>
														</div>
														<div class="col-md-2">
															<div class="form-group">
																<label>Temperature</label>
																<input type="text" name="temp" class="form-control" placeholder="e.g., 98.6°F">
															</div>
														</div>
														<div class="col-md-4">
															<div class="form-group">
																<label>Medical Prescription</label>
																<input type="text" name="pres" class="form-control" placeholder="Enter prescription details">
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-md-12">
															<button type="submit" name="submit" class="btn btn-primary">
																<i class="fa fa-save"></i> Add Medical History
															</button>
														</div>
													</div>
												</form>
											</div>
										</div>
									</div>
									
									<?php } else { ?>
									<div class="alert alert-danger">
										<i class="fa fa-exclamation-triangle"></i> <strong>Patient not found!</strong> The requested patient could not be found in the database.
									</div>
									<div class="text-center">
										<a href="patient-search.php" class="btn btn-primary">
											<i class="fa fa-search"></i> Search for Patients
										</a>
									</div>
									<?php } ?>
									
								</div>
							</div>
						</div>
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
		<script src="assets/js/form-elements.js"></script>
		<!-- end: CLIP-TWO JAVASCRIPTS -->
	</body>
</html>
<?php } ?>
