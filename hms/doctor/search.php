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
		<title>Doctor | Search Patients</title>
		
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
			.btn-sm {
				margin-right: 5px;
				margin-bottom: 3px;
			}
			.table th {
				background-color: #f8f9fa;
				border-top: 2px solid #dee2e6;
			}
			.search-form {
				background-color: #f8f9fa;
				padding: 20px;
				border-radius: 5px;
				margin-bottom: 20px;
				border: 1px solid #dee2e6;
			}
			.search-results {
				margin-top: 20px;
			}
			.no-results {
				text-align: center;
				padding: 40px;
				color: #6c757d;
				font-style: italic;
			}
			.badge-info {
				background-color: #17a2b8;
				color: white;
				padding: 5px 10px;
				border-radius: 4px;
				font-size: 12px;
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
<h1 class="mainTitle">Doctor | Search Patients</h1>
</div>
<ol class="breadcrumb">
<li>
<span>Doctor</span>
</li>
<li class="active">
<span>Search Patients</span>
</li>
</ol>
</div>
</section>
<div class="container-fluid container-fullw bg-white">
<div class="row">
<div class="col-md-12">
	<div class="search-form">
		<form role="form" method="post" name="search">
			<div class="form-group">
				<label for="searchdata">
					<strong>Search by Patient Name, Email, or City</strong>
				</label>
				<input type="text" name="searchdata" id="searchdata" class="form-control" value="" placeholder="Enter patient name, email, or city..." required='true'>
			</div>
			<button type="submit" name="search" id="submit" class="btn btn-primary">
				<i class="fa fa-search"></i> Search Patients
			</button>
		</form>
	</div>	
<?php
if(isset($_POST['search']))
{ 

$sdata=$_POST['searchdata'];
  ?>
<div class="search-results">
	<h4 align="center">
		<i class="fa fa-search"></i> Search Results for "<strong><?php echo htmlspecialchars($sdata);?></strong>"
	</h4>

<table class="table table-hover" id="sample-table-1">
<thead>
<tr>
<th class="center">#</th>
<th>Patient Name</th>
<th>Address</th>
<th>City</th>
<th>Gender</th>
<th>Email</th>
<th>Registration Date</th>
<th>Updation Date</th>
<th>Action</th>
</tr>
</thead>
<tbody>
<?php
$docid = $_SESSION['id'];
$sql=mysqli_query($con,"SELECT DISTINCT u.id, u.fullName, u.address, u.city, u.gender, u.email, u.regDate, u.updationDate 
                        FROM users u 
                        INNER JOIN appointment a ON u.id = a.userId 
                        WHERE a.doctorId = '$docid' 
                        AND (u.fullName LIKE '%$sdata%' OR u.email LIKE '%$sdata%' OR u.city LIKE '%$sdata%')
                        ORDER BY u.fullName");
$num=mysqli_num_rows($sql);
echo '<p class="text-muted text-center">Found <span class="badge badge-info">' . $num . '</span> patient(s) matching your search.</p>';
if($num>0){
$cnt=1;
while($row=mysqli_fetch_array($sql))
{
?>
<tr>
<td class="center"><?php echo $cnt;?>.</td>
<td class="hidden-xs"><?php echo $row['fullName'];?></td>
<td><?php echo $row['address'];?></td>
<td><?php echo $row['city'];?></td>
<td><?php echo $row['gender'];?></td>
<td><?php echo $row['email'];?></td>
<td><?php echo $row['regDate'];?></td>
<td><?php echo $row['updationDate'];?>
</td>
<td>

<a href="edit-patient.php?editid=<?php echo $row['id'];?>" class="btn btn-primary btn-sm" target="_self">Edit</a> 
<a href="view-patient.php?viewid=<?php echo $row['id'];?>" class="btn btn-warning btn-sm" target="_self">View Details</a>
<a href="patient-ehr.php?patient_id=<?php echo $row['id'];?>" class="btn btn-success btn-sm" target="_self">
    <i class="fa fa-heartbeat"></i> EHR
</a>
<a href="export-patient-history.php?patient_id=<?php echo $row['id'];?>" class="btn btn-info btn-sm" target="_self">
    <i class="fa fa-download"></i> Export
</a>
</td>
</tr>
<?php 
$cnt=$cnt+1;
} } else { ?>
  <tr>
    <td colspan="8" class="text-center text-muted">
      <i class="fa fa-search fa-2x"></i><br><br>
      No patients found matching your search criteria.<br>
      Try searching by name, email, or city.
    </td>
  </tr>
   
<?php }} ?></tbody>
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
			});
		</script>
		<!-- end: JavaScript Event Handlers for this page -->
		<!-- end: CLIP-TWO JAVASCRIPTS -->
	</body>
</html>
<?php } ?>
