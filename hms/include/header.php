<?php error_reporting(0);?>
<header class="navbar navbar-default navbar-static-top">
					<!-- start: NAVBAR HEADER -->
					<div class="navbar-header">
						<a href="#" class="sidebar-mobile-toggler pull-left hidden-md hidden-lg" class="btn btn-navbar sidebar-toggle" data-toggle-class="app-slide-off" data-toggle-target="#app" data-toggle-click-outside="#sidebar">
							<i class="ti-align-justify"></i>
						</a>
						<a class="navbar-brand" href="#">
							<h2 style="padding-top:20% ">HMS</h2>
						</a>
						<a href="#" class="sidebar-toggler pull-right visible-md visible-lg" data-toggle-class="app-sidebar-closed" data-toggle-target="#app">
							<i class="ti-align-justify"></i>
						</a>
						<a class="pull-right menu-toggler visible-xs-block" id="menu-toggler" data-toggle="collapse" href=".navbar-collapse">
							<span class="sr-only">Toggle navigation</span>
							<i class="ti-view-grid"></i>
						</a>
					</div>
					<!-- end: NAVBAR HEADER -->
					<!-- start: NAVBAR COLLAPSE -->
					<div class="navbar-collapse collapse">
						<ul class="nav navbar-right">
							<!-- start: MESSAGES DROPDOWN -->
								<li  style="padding-top:2% ">
								<h2>Clinical Support System</h2>
							</li>
						
						
							<li class="dropdown current-user">
								<a href class="dropdown-toggle" data-toggle="dropdown">
									<?php 
									$query=mysqli_query($con,"select fullName, profile_picture from users where id='".$_SESSION['id']."'");
									while($row=mysqli_fetch_array($query))
									{
										if($row['profile_picture'] && file_exists('uploads/profile_pictures/patients/' . $row['profile_picture'])) {
											echo '<img src="uploads/profile_pictures/patients/' . htmlentities($row['profile_picture']) . '" style="width: 30px; height: 30px; object-fit: cover; border-radius: 50%;">';
										} else {
											echo '<img src="assets/images/images.jpg" style="width: 30px; height: 30px; object-fit: cover; border-radius: 50%;">';
										}
										echo ' <span class="username">' . htmlentities($row['fullName']) . ' <i class="ti-angle-down"></i></span>';
									}
									?>
								</a>
								<ul class="dropdown-menu dropdown-dark">
									<li>
										<a href="edit-profile.php">
											My Profile
										</a>
									</li>
								
									<li>
										<a href="change-password.php">
											Change Password
										</a>
									</li>
									<li>
										<a href="logout.php">
											Log Out
										</a>
									</li>
								</ul>
							</li>
							<!-- end: USER OPTIONS DROPDOWN -->
						</ul>
						<!-- start: MENU TOGGLER FOR MOBILE DEVICES -->
						<div class="close-handle visible-xs-block menu-toggler" data-toggle="collapse" href=".navbar-collapse">
							<div class="arrow-left"></div>
							<div class="arrow-right"></div>
						</div>
						<!-- end: MENU TOGGLER FOR MOBILE DEVICES -->
					</div>
				
					
					<!-- end: NAVBAR COLLAPSE -->
				</header>
