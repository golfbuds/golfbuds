<?php
    include "db_connect.php";
	
	$fail = false;
	$report = [];

	if(($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["em"])) && ( strcmp($_POST["em"], "") !== 0 )){
        $email = mysqli_real_escape_string($conn, filter_var(strip_tags(trim($_POST["em"])), FILTER_SANITIZE_EMAIL));
    } else {
        $fail = true;
        array_push($report, "All fields are required.");
    }

	if($fail === false) {
    	//check if string contains any spaces in the actually trimmed string
    	//exclude password
    	if(strpos($email, " ") !== false)  {
    	    $fail = true;
    	    array_push($report, "Email can not contain spaces.");
    	}

    	//Validate email format
    	if(!filter_var($email, FILTER_VALIDATE_EMAIL)) { $fail = true; }


		if($fail === false) { 
        	//Prepare statments for security
        	$sql = $conn->prepare("select * from USER where EMAIL=?");
        	$sql->bind_param("s", $email);
        	$sql->execute();
        	$res = $sql->get_result();
           	$sql->free_result();
        	$sql->close();

        	//Login is correct
        	if($res->num_rows == 1) {
				//Start session
				session_start();
				$_SESSION["email"] = $email;
				$_SESSION["start"] = time();
				$_SESSION["expire"] = $_SESSION["start"] + (15 * 60);
				$msg = '<a href="https://athena.ecs.csus.edu/~trana/golfbuds/passwordReset.html"> Heres the link to reset password </a>';
       	 		$msg = wordwrap($msg, 70);
        		$headers = 'Content-type: text/html; charset=utf-8' . "\r\n";
				$headers .= "From: golfbuds@golfbuds.com" . "\r\n";
        		mail($email, "Reset password link.", $msg, $headers);

            	array_push($report,  "Reset link sent.");
        	} else {
           		$fail = true;
				array_push($report, "Email doesn't exist.");
        	}
	
		}
	}
    $msg = array("msg" => $report);

  	header("Content-Type: application/json");
    echo json_encode($msg);
?>

