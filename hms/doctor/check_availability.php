<?php
include('include/config.php');
if(!empty($_POST["emailid"])) {
	$email=$_POST["emailid"];
	$sql =mysqli_query($con,"SELECT docEmail FROM doctors WHERE docEmail='$email'");
	$row=mysqli_num_rows($sql);
	if($row>0) {
		echo "<span style='color:red'> Email already exists .</span>";
		echo "<script>$('#submit').prop('disabled',true);</script>";
	} else {
		echo "<span style='color:green'> Email available for registration .</span>";
		echo "<script>$('#submit').prop('disabled',false);</script>";
	}
}
?>