<?php
	include "db_connect.php";
	
	$username = $password = "";
	
	$username = $_POST["uname"];
	$password = $_POST["pword"];
	
		
	$sql = "select * from user where user_email='$username' and password='$password'";
	
	if(mysqli_num_rows(mysqli_query($conn, $sql)) > 0) {
		echo " Success Login.";	
	} else {
		echo " Error Login.";
	}
?>

<a href="../index.html"> Return to main page</a>
