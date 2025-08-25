<?php
session_start();
//error_reporting(0);
include('include/config.php');
include('include/checklogin.php');
check_login();
if(isset($_POST['submit']))
{
	$fname=$_POST['fname'];
$address=$_POST['address'];
$city=$_POST['city'];
$gender=$_POST['gender'];

// Handle profile picture upload
$profile_picture = null;
if(isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $file_type = $_FILES['profile_picture']['type'];
    $file_size = $_FILES['profile_picture']['size'];
    
    // Validate file type and size (max 5MB)
    if(in_array($file_type, $allowed_types) && $file_size <= 5242880) {
        $file_extension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
        $new_filename = 'patient_' . $_SESSION['id'] . '_' . time() . '.' . $file_extension;
        $upload_path = 'uploads/profile_pictures/patients/' . $new_filename;
        
        if(move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
            $profile_picture = $new_filename;
            
            // Delete old profile picture if exists
            $old_pic_query = mysqli_query($con, "SELECT profile_picture FROM users WHERE id='".$_SESSION['id']."'");
            if($old_pic_data = mysqli_fetch_array($old_pic_query)) {
                if($old_pic_data['profile_picture'] && file_exists('uploads/profile_pictures/patients/' . $old_pic_data['profile_picture'])) {
                    unlink('uploads/profile_pictures/patients/' . $old_pic_data['profile_picture']);
                }
            }
        } else {
            $error_msg = "Error uploading profile picture. Please try again.";
        }
    } else {
        $error_msg = "Invalid file type or size. Please upload JPG, PNG, or GIF files under 5MB.";
    }
}

// Update query with or without profile picture
if($profile_picture) {
    $sql=mysqli_query($con,"Update users set fullName='$fname',address='$address',city='$city',gender='$gender',profile_picture='$profile_picture' where id='".$_SESSION['id']."'");
} else {
    $sql=mysqli_query($con,"Update users set fullName='$fname',address='$address',city='$city',gender='$gender' where id='".$_SESSION['id']."'");
}

if($sql)
{
$msg="Your Profile updated Successfully";
}

}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>User | Edit Profile</title>
		
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
									<h1 class="mainTitle">User | Edit Profile</h1>
																	</div>
								<ol class="breadcrumb">
									<li>
										<span>User </span>
									</li>
									<li class="active">
										<span>Edit Profile</span>
									</li>
								</ol>
							</div>
						</section>
						<!-- end: PAGE TITLE -->
						<!-- start: BASIC EXAMPLE -->
						<div class="container-fluid container-fullw bg-white">
							<div class="row">
								<div class="col-md-12">
<h5 style="color: green; font-size:18px; ">
<?php if($msg) { echo htmlentities($msg);}?> </h5>
<?php if(isset($error_msg)) { ?>
<h5 style="color: red; font-size:18px; "><?php echo htmlentities($error_msg); ?></h5>
<?php } ?>
									<div class="row margin-top-30">
										<div class="col-lg-8 col-md-12">
											<div class="panel panel-white">
												<div class="panel-heading">
													<h5 class="panel-title">Edit Profile</h5>
												</div>
												<div class="panel-body">
									<?php 
$sql=mysqli_query($con,"select * from users where id='".$_SESSION['id']."'");
while($data=mysqli_fetch_array($sql))
{
?>
<h4><?php echo htmlentities($data['fullName']);?>'s Profile</h4>
<p><b>Profile Reg. Date: </b><?php echo htmlentities($data['regDate']);?></p>
<?php if($data['updationDate']){?>
<p><b>Profile Last Updation Date: </b><?php echo htmlentities($data['updationDate']);?></p>
<?php } ?>
<hr />													<form role="form" name="edit" method="post" enctype="multipart/form-data">
														
														<!-- Current Profile Picture Display -->
														<div class="form-group text-center" style="margin-bottom: 30px;">
															<?php if($data['profile_picture'] && file_exists('uploads/profile_pictures/patients/' . $data['profile_picture'])): ?>
																<div class="current-profile-picture" style="margin-bottom: 15px;">
																	<h5>Current Profile Picture</h5>
																	<img src="uploads/profile_pictures/patients/<?php echo htmlentities($data['profile_picture']); ?>" 
																		 alt="Current Profile Picture" 
																		 class="img-thumbnail" 
																		 style="width: 150px; height: 150px; object-fit: cover; border-radius: 75px;">
																</div>
															<?php else: ?>
																<div class="no-profile-picture" style="margin-bottom: 15px;">
																	<div style="width: 150px; height: 150px; background-color: #f8f9fa; border: 2px dashed #dee2e6; border-radius: 75px; display: inline-flex; align-items: center; justify-content: center; margin: 0 auto;">
																		<i class="fa fa-user" style="font-size: 60px; color: #6c757d;"></i>
																	</div>
																	<p class="text-muted" style="margin-top: 10px;">No profile picture uploaded</p>
																</div>
															<?php endif; ?>
														</div>

														<!-- Profile Picture Upload -->
														<div class="form-group">
															<label for="profile_picture">
																<i class="fa fa-camera"></i> Profile Picture
															</label>
															<input type="file" name="profile_picture" id="profile_picture" class="form-control" accept="image/*" onchange="previewImage(this);">
															<small class="help-block">
																<i class="fa fa-info-circle"></i> 
																Upload JPG, PNG, or GIF files. Maximum size: 5MB. Recommended: Square image (400x400px or larger).
															</small>
															<!-- Image Preview -->
															<div id="imagePreview" style="margin-top: 15px; display: none;">
																<h6>Preview:</h6>
																<img id="preview" src="" alt="Preview" class="img-thumbnail" style="width: 120px; height: 120px; object-fit: cover; border-radius: 60px;">
															</div>
														</div>
													

<div class="form-group">
															<label for="fname">
																 User Name
															</label>
	<input type="text" name="fname" class="form-control" value="<?php echo htmlentities($data['fullName']);?>" >
														</div>


<div class="form-group">
															<label for="address">
																 Address
															</label>
					<textarea name="address" class="form-control"><?php echo htmlentities($data['address']);?></textarea>
														</div>
<div class="form-group">
															<label for="city">
																 City
															</label>
		<input type="text" name="city" class="form-control" required="required"  value="<?php echo htmlentities($data['city']);?>" >
														</div>
	
<div class="form-group">
									<label for="gender">
																Gender
															</label>

<select name="gender" class="form-control" required="required" >
<option value="<?php echo htmlentities($data['gender']);?>"><?php echo htmlentities($data['gender']);?></option>
<option value="male">Male</option>	
<option value="female">Female</option>	
<option value="other">Other</option>	
</select>

														</div>

<div class="form-group">
									<label for="fess">
																 User Email
															</label>
					<input type="email" name="uemail" class="form-control"  readonly="readonly"  value="<?php echo htmlentities($data['email']);?>">
					<a href="change-emaild.php">Update your email id</a>
														</div>



														
														
														
														
														<button type="submit" name="submit" class="btn btn-o btn-primary">
															Update
														</button>
													</form>
													<?php } ?>
												</div>
											</div>
										</div>
											
											</div>
										</div>
									<div class="col-lg-12 col-md-12">
											<div class="panel panel-white">
												
												
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
			function previewImage(input) {
				if (input.files && input.files[0]) {
					var reader = new FileReader();
					var file = input.files[0];
					
					// Validate file size (5MB)
					if (file.size > 5242880) {
						alert('File size too large. Please select an image under 5MB.');
						input.value = '';
						document.getElementById('imagePreview').style.display = 'none';
						return;
					}
					
					// Validate file type
					var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
					if (!allowedTypes.includes(file.type)) {
						alert('Invalid file type. Please select JPG, PNG, or GIF files only.');
						input.value = '';
						document.getElementById('imagePreview').style.display = 'none';
						return;
					}
					
					reader.onload = function(e) {
						document.getElementById('preview').src = e.target.result;
						document.getElementById('imagePreview').style.display = 'block';
					}
					
					reader.readAsDataURL(input.files[0]);
				} else {
					document.getElementById('imagePreview').style.display = 'none';
				}
			}
			
			jQuery(document).ready(function() {
				Main.init();
				FormElements.init();
			});
		</script>
		<!-- end: JavaScript Event Handlers for this page -->
		<!-- end: CLIP-TWO JAVASCRIPTS -->
	</body>
</html>
