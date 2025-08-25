<?php
session_start();
error_reporting(0);
include('include/config.php');
if(strlen($_SESSION['id']==0)) {
 header('location:logout.php');
  } else{

if(isset($_GET['del']))
{
    $uid = $_GET['id'];
    $patient_type = $_GET['type'] ?? 'user-patient';
    
    if($patient_type == 'old-patient') {
        mysqli_query($con, "DELETE FROM tblpatient WHERE id = '$uid'");
        $_SESSION['msg'] = "Old patient deleted successfully!";
    } else {
        mysqli_query($con, "DELETE FROM users WHERE id = '$uid'");
        $_SESSION['msg'] = "User patient deleted successfully!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Admin | Patient Management</title>
		
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
			.filter-btn {
				margin-right: 5px;
				margin-bottom: 5px;
			}
			.filter-btn.active {
				box-shadow: 0 2px 4px rgba(0,0,0,0.2);
			}
			.label {
				font-size: 11px;
				padding: 5px 10px;
				border-radius: 4px;
				font-weight: bold;
				text-transform: uppercase;
				letter-spacing: 0.5px;
			}
			.label-primary {
				background-color: #007bff;
				color: white;
			}
			.label-success {
				background-color: #28a745;
				color: white;
			}
			.label-warning {
				background-color: #ffc107;
				color: #212529;
			}
			.badge-info {
				background-color: #17a2b8;
				color: white;
				padding: 5px 10px;
				border-radius: 4px;
				font-size: 12px;
			}
			.btn-xs {
				margin-right: 3px;
				margin-bottom: 3px;
			}
			.table th {
				background-color: #f8f9fa;
				border-top: 2px solid #dee2e6;
			}
			
			/* Search styling */
			.search-active {
				border-color: #007bff !important;
				box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
			}
			
			#searchInput:focus {
				border-color: #007bff;
				box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
			}
			
			.search-results {
				margin-top: 10px;
				padding: 10px;
				border-radius: 5px;
				border: 1px solid #dee2e6;
			}
			
			.search-results.alert-info {
				background-color: #d1ecf1;
				border-color: #bee5eb;
				color: #0c5460;
			}
			
			.search-results.alert-warning {
				background-color: #fff3cd;
				border-color: #ffeaa7;
				color: #856404;
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
									<h1 class="mainTitle">Admin | Patient Management</h1>
								</div>
								<ol class="breadcrumb">
									<li>
										<span>Admin</span>
									</li>
									<li class="active">
										<span>Patient Management</span>
									</li>
								</ol>
							</div>
						</section>
						<!-- end: PAGE TITLE -->
						
						<!-- start: BASIC EXAMPLE -->
						<div class="container-fluid container-fullw bg-white">
							<div class="row">
								<div class="col-md-12">
									<h5 class="over-title margin-bottom-15">Manage <span class="text-bold">All Patients</span></h5>
									<div class="alert alert-info">
										<i class="fa fa-info-circle"></i> <strong>Note:</strong> This page consolidates both new patients (users table) and old patients (tblpatient table). Use the filters above to view specific patient types.
									</div>
									
									<!-- Search and Filter Section -->
									<div class="row" style="margin-bottom: 20px;">
										<div class="col-md-6">
											<div class="form-group">
												<label for="searchInput">
													<i class="fa fa-search"></i> Search Patients
												</label>
												<input type="text" id="searchInput" class="form-control" placeholder="Search by name, email, or city...">
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label>&nbsp;</label><br>
												<button type="button" class="btn btn-primary filter-btn active" data-filter="all">
													<i class="fa fa-users"></i> All Patients
												</button>
												<button type="button" class="btn btn-info filter-btn" data-filter="user-patient">
													<i class="fa fa-user"></i> User Patients
												</button>
												<button type="button" class="btn btn-warning filter-btn" data-filter="old-patient">
													<i class="fa fa-user-md"></i> Old Patients
												</button>
												<button type="button" id="clearSearch" class="btn btn-secondary" style="margin-left: 10px;">
													<i class="fa fa-times"></i> Clear
												</button>
											</div>
										</div>
									</div>
									
									<!-- Search Results Summary -->
									<div id="searchResults" class="search-results" style="display: none;"></div>
									
									<!-- Visible Count -->
									<div class="text-muted" style="margin-bottom: 15px;">
										Showing <span id="visibleCount">0</span> of <span id="totalCount">0</span> patients
									</div>
									
									<?php
									// Get users data
									$users_sql = mysqli_query($con, "SELECT id, fullName, email, address, city, gender, regDate, updationDate, 'User Patient' as patient_type FROM users ORDER BY fullName");
									
									// Get tblpatient data
									$tblpatient_sql = mysqli_query($con, "SELECT ID as id, PatientName as fullName, PatientEmail as email, PatientAdd as address, PatientAdd as city, PatientGender as gender, CreationDate as regDate, UpdationDate as updationDate, 'Old Patient' as patient_type FROM tblpatient ORDER BY PatientName");
									
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
									
									// Combine and sort all data
									$all_data = array_merge($users_data, $tblpatient_data);
									usort($all_data, function($a, $b) {
										return strcmp($a['fullName'], $b['fullName']);
									});
									
									$total_patients = count($all_data);
									?>
									
									<div class="alert alert-success">
										<i class="fa fa-check-circle"></i> <strong>Total Patients:</strong> <?php echo $total_patients; ?>
									</div>
									
									<?php if($total_patients > 0): ?>
									
									<!-- Patients Table -->
									<div class="table-responsive">
										<table class="table table-hover" id="patientsTable">
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
												foreach($all_data as $row) {
													$patient_type_class = ($row['patient_type'] == 'User Patient') ? 'user-patient' : 'old-patient';
												?>
												<tr class="patient-row" data-type="<?php echo $patient_type_class; ?>">
													<td class="center"><?php echo $cnt; ?>.</td>
													<td class="hidden-xs"><?php echo htmlspecialchars($row['fullName']); ?></td>
													<td><?php echo htmlspecialchars($row['email']); ?></td>
													<td><?php echo htmlspecialchars($row['city']); ?></td>
													<td><?php echo htmlspecialchars($row['gender']); ?></td>
													<td>
														<?php if($row['patient_type'] == 'User Patient'): ?>
															<span class="label label-primary"><?php echo $row['patient_type']; ?></span>
														<?php else: ?>
															<span class="label label-warning"><?php echo $row['patient_type']; ?></span>
														<?php endif; ?>
													</td>
													<td><?php echo htmlspecialchars($row['regDate']); ?></td>
													<td>
														<div class="visible-md visible-lg hidden-sm hidden-xs">
															<a href="view-patient.php?viewid=<?php echo $row['id']; ?>&type=<?php echo $patient_type_class; ?>" class="btn btn-primary btn-xs" title="View Details">
																<i class="fa fa-eye"></i> View
															</a>
															<a href="export-patient-history.php?patient_id=<?php echo $row['id']; ?>" class="btn btn-info btn-xs" title="Export EHR">
																<i class="fa fa-download"></i> Export
															</a>
															<a href="manage-users.php?del=1&id=<?php echo $row['id']; ?>&type=<?php echo $patient_type_class; ?>" class="btn btn-danger btn-xs" title="Delete" onclick="return confirm('Are you sure you want to delete this patient?')">
																<i class="fa fa-trash"></i> Delete
															</a>
														</div>
														<div class="visible-xs visible-sm hidden-md hidden-lg">
															<div class="btn-group" dropdown is-open="status.isopen">
																<button type="button" class="btn btn-primary btn-o btn-sm dropdown-toggle" dropdown-toggle>
																	<i class="fa fa-cog"></i>&nbsp;<span class="caret"></span>
																</button>
																<ul class="dropdown-menu pull-right dropdown-light" role="menu">
																	<li>
																		<a href="view-patient.php?viewid=<?php echo $row['id']; ?>&type=<?php echo $patient_type_class; ?>">
																			<i class="fa fa-eye"></i> View Details
																		</a>
																	</li>
																	<li>
																		<a href="export-patient-history.php?patient_id=<?php echo $row['id']; ?>">
																			<i class="fa fa-download"></i> Export EHR
																		</a>
																	</li>
																	<li>
																		<a href="manage-users.php?del=1&id=<?php echo $row['id']; ?>&type=<?php echo $patient_type_class; ?>" onclick="return confirm('Are you sure you want to delete this patient?')">
																			<i class="fa fa-trash"></i> Delete
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
									
									<!-- No Patients Message -->
									<div class="text-center" style="padding: 50px 20px;">
										<i class="fa fa-users" style="font-size: 48px; color: #dee2e6; margin-bottom: 20px;"></i>
										<h4>No patients found</h4>
										<p class="text-muted">There are no patients in the system yet.</p>
									</div>
									
									<?php endif; ?>
									
								</div>
							</div>
						</div>
						<!-- end: BASIC EXAMPLE -->
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
				
				var $table = $('#patientsTable');
				var $rows = $table.find('.patient-row');
				var totalRows = $rows.length;
				var visibleRows = totalRows;
				
				$('#totalCount').text(totalRows);
				$('#visibleCount').text(visibleRows);
				
				// Filter functionality
				$('.filter-btn').click(function() {
					var filter = $(this).data('filter');
					
					// Update active button
					$('.filter-btn').removeClass('active');
					$(this).addClass('active');
					
					// Apply filter
					if(filter === 'all') {
						$rows.show();
						visibleRows = totalRows;
					} else {
						$rows.hide();
						$rows.filter('[data-type="' + filter + '"]').show();
						visibleRows = $rows.filter('[data-type="' + filter + '"]').length;
					}
					
					$('#visibleCount').text(visibleRows);
					hideSearchResults();
				});
				
				// Search functionality
				$('#searchInput').on('input', function() {
					var searchTerm = $(this).val().toLowerCase();
					
					if(searchTerm === '') {
						// Show all rows based on current filter
						var activeFilter = $('.filter-btn.active').data('filter');
						if(activeFilter === 'all') {
							$rows.show();
							visibleRows = totalRows;
						} else {
							$rows.hide();
							$rows.filter('[data-type="' + activeFilter + '"]').show();
							visibleRows = $rows.filter('[data-type="' + activeFilter + '"]').length;
						}
						hideSearchResults();
					} else {
						// Apply search
						$rows.hide();
						$rows.each(function() {
							var $row = $(this);
							var text = $row.text().toLowerCase();
							if(text.includes(searchTerm)) {
								$row.show();
							}
						});
						visibleRows = $rows.filter(':visible').length;
						showSearchResults(searchTerm, visibleRows);
					}
					
					$('#visibleCount').text(visibleRows);
					$('#searchInput').addClass('search-active');
				});
				
				// Clear search
				$('#clearSearch').click(function() {
					$('#searchInput').val('');
					$('#searchInput').removeClass('search-active');
					
					// Reset to show all rows based on current filter
					var activeFilter = $('.filter-btn.active').data('filter');
					if(activeFilter === 'all') {
						$rows.show();
						visibleRows = totalRows;
					} else {
						$rows.hide();
						$rows.filter('[data-type="' + activeFilter + '"]').show();
						visibleRows = $rows.filter('[data-type="' + activeFilter + '"]').length;
					}
					
					$('#visibleCount').text(visibleRows);
					hideSearchResults();
				});
				
				function showSearchResults(searchTerm, count) {
					if(count > 0) {
						$('#searchResults').removeClass('alert-warning').addClass('alert-info')
							.html('<i class="fa fa-search"></i> Found <strong>' + count + '</strong> patient(s) matching "<strong>' + searchTerm + '</strong>"')
							.show();
					} else {
						$('#searchResults').removeClass('alert-info').addClass('alert-warning')
							.html('<i class="fa fa-exclamation-triangle"></i> No patients found matching "<strong>' + searchTerm + '</strong>"')
							.show();
					}
				}
				
				function hideSearchResults() {
					$('#searchResults').hide();
				}
				
				// Auto-focus search input
				$('#searchInput').focus();
			});
		</script>
	</body>
</html>
<?php } ?>