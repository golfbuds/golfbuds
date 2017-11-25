<?php
    include "db_connect.php";

	$golfbudEmail = "team14csc190@gmail.com";
    $fail = false;
    $report = [];
	
	//Check if input correctly set
    if( ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["name"]) && isset($_POST["email"])
		&& isset($_POST["subject"]) && isset($_POST["message"]) )
        && (strcmp($_POST["name"], "") !== 0 && strcmp($_POST["email"], "") !== 0 
	    && strcmp($_POST["subject"], "") !== 0 && strcmp($_POST["message"], "") !== 0) ) {

        $name = mysqli_real_escape_string($conn, strip_tags(trim($_POST["name"])));
        $email = mysqli_real_escape_string($conn, filter_var(strip_tags(trim($_POST["email"])), FILTER_SANITIZE_EMAIL));
		$subject = mysqli_real_escape_string($conn, strip_tags(trim($_POST["subject"])));
        $message = mysqli_real_escape_string($conn, strip_tags(trim($_POST["message"])));
    } else {
        $fail = true;
        array_push($report, "All Fields Are Required.");
    }

	if($fail === false) {
    	//check if string contains any spaces in the actually trimmed string
    	if(strpos($email, " ") !== false) {

      		array_push($report, "Email Can Not Contain Spaces.");
       		$fail = true;
    	}
    	//Only letters.
	    if((preg_match("/^[A-Za-z\s]+$/", $name) === 0) ) {
    	    array_push($report, "Name Can Only Contain Letters.");
    	    $fail = true;
    	}
    	//Validate email format
    	if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        	array_push($report, "Invalid Email Format.");
        	$fail = true;
    	}

		if($fail === false) {
			$headers = "From: " . $golfbudEmail . "\r\n";
			mail($golfbudEmail, ("GolfBuds Contact Us Message" . " - " . $subject),
				 ($message . "\n\nSender Name: " . $name . "\nSender Email: " . $email) , $headers);
			array_push($report, "Message Sent!");
		}
	}
	$msg = array("msg" => $report);
    header("Content-Type: application/json");
    echo json_encode($msg);

?>
