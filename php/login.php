<?php
	include "db_connect.php";
	
	$username = $password = "";
	$fail = false;

	if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["uname"]) && isset($_POST["pword"]))	{
		$username = mysqli_real_escape_string($conn, strip_tags(trim($_POST["uname"])));
		$password = mysqli_real_escape_string($conn, strip_tags(trim($_POST["pword"])));
	} else {
		$fail = true;
	}

	//check if empty string
	if(strcmp($username, "") === 0 || strcmp($password, "") === 0) {
    	$fail = true;
	}
	//check if string contains any spaces in the actually trimmed string
	if(strpos($username, " ") === true || strpos($password, " ") === true) {
    	$fail = true;
	}


	if($fail === false)	{
		$sql = "select * from user where user_name='$username' and password='$password'";
	
		if(mysqli_num_rows(mysqli_query($conn, $sql)) == 1) {
			echo "<br>" . "Success Login.";	
		} else {
			echo "<br>" . "Error Login.";
		}
		
	} else {
		echo "<br>" . "ERROR: CHECK INPUT";
	}

	mysqli_close($conn);
?>

<br>
<a href="../index.html"> Return to main page</a>
