<?php
session_start();
error_reporting(0);
include('include/config.php');
if(strlen($_SESSION['id']==0)) {
 header('location:logout.php');
  } else{

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Admin | Patient Search</title>
		
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
		<link href="vendor/bootstrap-timepicker/bootstrap-timepicker.min.js"></script>
		<link rel="stylesheet" href="assets/css/styles.css">
		<link rel="stylesheet" href="assets/css/plugins.css">
		<link rel="stylesheet" href="assets/css/themes/theme-1.css" id="skin_color" />
		
		<style>
			.search-container {
				background-color: #f8f9fa;
				padding: 30px;
				border-radius: 8px;
				border: 2px solid #dee2e6;
				margin-bottom: 30px;
			}
			.search-input {
				border-radius: 25px;
				padding: 12px 20px;
				font-size: 16px;
				border: 2px solid #dee2e6;
				transition: all 0.3s ease;
			}
			.search-input:focus {
				border-color: #007bff;
				box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
			}
			.search-btn {
				border-radius: 25px;
				padding: 12px 30px;
				font-size: 16px;
				font-weight: bold;
			}
			.patient-card {
				border: 1px solid #dee2e6;
				border-radius: 8px;
				padding: 20px;
				margin-bottom: 20px;
				transition: all 0.3s ease;
			}
			.patient-card:hover {
				box-shadow: 0 4px 8px rgba(0,0,0,0.1);
				transform: translateY(-2px);
			}
			.patient-type-badge {
				font-size: 11px;
				padding: 5px 10px;
				border-radius: 15px;
				font-weight: bold;
				text-transform: uppercase;
			}
			.patient-type-user {
				background-color: #007bff;
				color: white;
			}
			.patient-type-old {
				background-color: #ffc107;
				color: #212529;
			}
			.search-stats {
				background-color: #e9ecef;
				padding: 15px;
				border-radius: 8px;
				margin-bottom: 20px;
			}
			.no-results {
				text-align: center;
				padding: 50px 20px;
				color: #6c757d;
			}
			.no-results i {
				font-size: 48px;
				margin-bottom: 20px;
				color: #dee2e6;
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
									<h1 class="mainTitle">Admin | Patient Search</h1>
								</div>
								<ol class="breadcrumb">
									<li>
										<span>Admin</span>
									</li>
									<li class="active">
										<span>Patient Search</span>
									</li>
								</ol>
							</div>
						</section>
						<!-- end: PAGE TITLE -->
						
						<div class="container-fluid container-fullw bg-white">
							<div class="row">
								<div class="col-md-12">
									<h5 class="over-title margin-bottom-15">Search <span class="text-bold">Patients</span></h5>
									
									<div class="alert alert-info">
										<i class="fa fa-info-circle"></i> <strong>Search Tip:</strong> This search covers both new patients (users table) and old patients (tblpatient table). Search by name, email, contact number, or city.
									</div>
									
									<!-- Search Form -->
									<div class="search-container">
										<form role="form" method="post" name="search" id="searchForm">
											<div class="row">
												<div class="col-md-8">
													<div class="form-group">
														<label for="searchdata">
															<i class="fa fa-search"></i> Search Patients
														</label>
														<input type="text" name="searchdata" id="searchdata" class="form-control search-input" 
															   placeholder="Enter patient name, email, contact number, or city..." 
															   value="<?php echo isset($_POST['searchdata']) ? htmlspecialchars($_POST['searchdata']) : ''; ?>" required>
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label>&nbsp;</label><br>
														<button type="submit" name="search" id="submit" class="btn btn-primary search-btn">
															<i class="fa fa-search"></i> Search
														</button>
														<button type="button" id="clearSearch" class="btn btn-secondary search-btn" style="margin-left: 10px;">
															<i class="fa fa-times"></i> Clear
														</button>
													</div>
												</div>
											</div>
										</form>
									</div>
									
									<?php
									if(isset($_POST['search']))
									{ 
										$sdata = trim($_POST['searchdata']);
										if(!empty($sdata)) {
									?>
									
									<!-- Search Results Header -->
									<div class="search-stats">
										<h4><i class="fa fa-search"></i> Search Results for "<strong><?php echo htmlspecialchars($sdata); ?></strong>"</h4>
									</div>
									
									<?php
									// Search in users table (new patients)
									$users_sql = mysqli_query($con, "SELECT id, fullName, email, address, city, gender, regDate, updationDate, 'User Patient' as patient_type FROM users 
																	WHERE fullName LIKE '%" . mysqli_real_escape_string($con, $sdata) . "%' 
																	OR email LIKE '%" . mysqli_real_escape_string($con, $sdata) . "%' 
																	OR address LIKE '%" . mysqli_real_escape_string($con, $sdata) . "%' 
																	OR city LIKE '%" . mysqli_real_escape_string($con, $sdata) . "%'");
									
									// Search in tblpatient table (old patients)
									$tblpatient_sql = mysqli_query($con, "SELECT id, PatientName as fullName, PatientEmail as email, PatientAdd as address, PatientAdd as city, PatientGender as gender, CreationDate as regDate, UpdationDate as updationDate, 'Old Patient' as patient_type FROM tblpatient 
																			WHERE PatientName LIKE '%" . mysqli_real_escape_string($con, $sdata) . "%' 
																			OR PatientContno LIKE '%" . mysqli_real_escape_string($con, $sdata) . "%' 
																			OR PatientAdd LIKE '%" . mysqli_real_escape_string($con, $sdata) . "%'");
									
									$users_data = [];
									$tblpatient_data = [];
									
									if($users_sql) {
										while($user_row = mysqli_fetch_array($users_sql)) {
											$users_data[] = $user_row;
										}
									}
									
									if($tblpatient_sql) {
										while($tblpatient_row = mysqli_fetch_array($tblpatient_sql)) {
											$tblpatient_data[] = $tblpatient_row;
										}
									}
									
									// Combine and sort all results
									$all_results = array_merge($users_data, $tblpatient_data);
									usort($all_results, function($a, $b) {
										return strcmp($a['fullName'], $b['fullName']);
									});
									
									$total_results = count($all_results);
									?>
									
									<!-- Results Summary -->
									<div class="alert alert-success">
										<i class="fa fa-check-circle"></i> <strong>Found <?php echo $total_results; ?> patient(s)</strong>
										(<?php echo count($users_data); ?> from users table, <?php echo count($tblpatient_data); ?> from tblpatient table)
									</div>
									
									<?php if($total_results > 0): ?>
									
									<!-- Results Table -->
									<div class="table-responsive">
										<table class="table table-hover" id="sample-table-1">
											<thead>
												<tr>
													<th class="center">#</th>
													<th>Patient Name</th>
													<th>Email/Contact</th>
													<th>Address/City</th>
													<th>Gender</th>
													<th>Patient Type</th>
													<th>Registration Date</th>
													<th>Action</th>
												</tr>
											</thead>
											<tbody>
												<?php
												$cnt = 1;
												foreach($all_results as $row) {
												?>
												<tr>
													<td class="center"><?php echo $cnt; ?>.</td>
													<td class="hidden-xs"><?php echo htmlspecialchars($row['fullName']); ?></td>
													<td><?php echo htmlspecialchars($row['email']); ?></td>
													<td><?php echo htmlspecialchars($row['city']); ?></td>
													<td><?php echo htmlspecialchars($row['gender']); ?></td>
													<td>
														<?php if($row['patient_type'] == 'User Patient'): ?>
															<span class="patient-type-badge patient-type-user"><?php echo $row['patient_type']; ?></span>
														<?php else: ?>
															<span class="patient-type-badge patient-type-old"><?php echo $row['patient_type']; ?></span>
														<?php endif; ?>
													</td>
													<td><?php echo htmlspecialchars($row['regDate']); ?></td>
													<td>
														<div class="visible-md visible-lg hidden-sm hidden-xs">
															<a href="view-patient.php?viewid=<?php echo $row['id']; ?>&type=<?php echo strtolower(str_replace(' ', '-', $row['patient_type'])); ?>" class="btn btn-primary btn-xs" title="View Details">
																<i class="fa fa-eye"></i> View
															</a>
															<a href="export-patient-history.php?patient_id=<?php echo $row['id']; ?>" class="btn btn-info btn-xs" title="Export EHR">
																<i class="fa fa-download"></i> Export
															</a>
														</div>
														<div class="visible-xs visible-sm hidden-md hidden-lg">
															<div class="btn-group" dropdown is-open="status.isopen">
																<button type="button" class="btn btn-primary btn-o btn-sm dropdown-toggle" dropdown-toggle>
																	<i class="fa fa-cog"></i>&nbsp;<span class="caret"></span>
																</button>
																<ul class="dropdown-menu pull-right dropdown-light" role="menu">
																	<li>
																		<a href="view-patient.php?viewid=<?php echo $row['id']; ?>&type=<?php echo strtolower(str_replace(' ', '-', $row['patient_type'])); ?>">
																			<i class="fa fa-eye"></i> View Details
																		</a>
																	</li>
																	<li>
																		<a href="export-patient-history.php?patient_id=<?php echo $row['id']; ?>">
																			<i class="fa fa-download"></i> Export EHR
																		</a>
																	</li>
																</ul>
															</div>
														</div>
													</td>
												</tr>
												<?php 
												$cnt++;
												} 
												?>
											</tbody>
										</table>
									</div>
									
									<?php else: ?>
									
									<!-- No Results Message -->
									<div class="no-results">
										<i class="fa fa-search"></i>
										<h4>No patients found</h4>
										<p>No patients match your search criteria "<strong><?php echo htmlspecialchars($sdata); ?></strong>"</p>
										<p>Try searching with different keywords or check the spelling.</p>
									</div>
									
									<?php endif; ?>
									
									<?php
										} else {
											echo '<div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> Please enter a search term.</div>';
										}
									} 
									?>
									
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
		
		<script>
			jQuery(document).ready(function() {
				Main.init();
				FormElements.init();
				
				// Clear search functionality
				$('#clearSearch').click(function() {
					$('#searchdata').val('');
					$('#searchForm').submit();
				});
				
				// Auto-submit on Enter key
				$('#searchdata').keypress(function(e) {
					if(e.which == 13) { // Enter key
						$('#searchForm').submit();
					}
				});
				
				// Focus search input on page load
				$('#searchdata').focus();
			});
		</script>
	</body>
</html>
<?php } ?>
