<?php
	include "db_connect.php";
    include "secstuff.php";

    $email = $password = "";
    $fail = false;
    $report = []; //Holds error messages in array.

    //Check if input correctly set
    if(($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["em"]) && isset($_POST["pword"]) )
		&& (strcmp($_POST["em"], "") !== 0 && strcmp($_POST["pword"], "") !== 0)) {

		$email = mysqli_real_escape_string($conn, filter_var(strip_tags(trim($_POST["em"])), FILTER_SANITIZE_EMAIL));
        $password = mysqli_real_escape_string($conn, strip_tags(trim($_POST["pword"])));
    } else {
        $fail = true;
        array_push($report, "All Fields Are Required.");
    }
	//If all fields filled then check inputs
	if($fail === false) {
    	//Check if string contains any spaces in the actually trimmed string
    	//exclude password
    	if(strpos($email, " ") !== false) {
        	$fail = true;
        	array_push($report, "Email Can Not Contain Spaces.");
    	}

    	//Validate email format
    	if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        	$fail = true;
        	array_push($report, "Invalid Email Format.");
    	}
		//If no errors
   		if($fail === false) {
        	//Encrypt
       		$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length("$password"));
        	$password = openssl_encrypt($password, "aes-256-cbc", $key, $options=0, $iv);
        	//Prepare statments for security
        	$sql = $conn->prepare("select * from USER where EMAIL=? and PASSWORD=?");
        	$sql->bind_param("ss", $email, $password);
        	$sql->execute();
       	 	$res = $sql->get_result();
    	    $sql->close();

        	//Login is correct
        	if($res->num_rows === 1) {
    			//Start session
				session_start();

        	    $sql = $conn->prepare("select FNAME, LNAME from USER where EMAIL=?");
        	    $sql->bind_param("s", $email);
           		$sql->execute();
            	$sql->bind_result($fname, $lname);

            	//Get results and put in session variables
				while($sql->fetch()) {
                    $_SESSION["fname"] = $fname;
                    $_SESSION["lname"] = $lname;
                }
			
				//Check if user wants to be remebered.	
				if($_POST["checkbox"]  === "on") { 
					setcookie("online", "true");
					setcookie("fname", $fname);
					setcookie("lname", $lname);
				}

				//clear results from variable
            	$sql->free_result();
    	   		$sql->close();
       	 	} else {
				$fail = true;
            	array_push($report, "Error Login.");
     	   }
    	}
	}

	//Return message to ajax as array encoded to json
	if($fail === true) 
		$msg = array("error" => $report);	
	else
		$msg = array("success" => "success");	

	header("Content-Type: application/json");
	echo json_encode($msg);
?>
