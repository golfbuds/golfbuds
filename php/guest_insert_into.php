<?php
include "db_connect.php";

$first_name = $last_name = $user_email;
$fail = false; //Error reporting
$report = [];

//Check if input is from post method and check if null.
if(($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["firstname"]) && isset($_POST["lastname"]) 
		 && isset($_POST["em"])) 
		 && (strcmp($_POST["firstname"], "") !== 0 && strcmp($_POST["lastname"], "") !== 0 && strcmp($_POST["em"], "") !== 0)){
	
	//Get values and trim spaces, strip input of html and php tags, escape input 	
	$first_name = mysqli_real_escape_string($conn , strip_tags(trim($_POST["firstname"])));
	$last_name = mysqli_real_escape_string($conn, strip_tags(trim($_POST["lastname"])));
	//Also sanitize illegal inputs for email
	$user_email = mysqli_real_escape_string($conn, filter_var(strip_tags(trim($_POST["em"])), FILTER_SANITIZE_EMAIL));	
} else {
	$fail = true; 
	array_push($report, "All Fields Must Be Filled.");
}

if($fail === false) {
	//check if string contains any spaces in the actually trimmed string
	//Exclude passwords
	if(strpos($first_name, " ") !== false || strpos($last_name, " ") !== false || strpos($user_email, " ") !== false ) { 
		$fail = true; 
		array_push($report, "First Name, Last Name And Email Can Not Contain Spaces.");
	}

	//Only letters in names
	if(preg_match("/^[A-Za-z]+$/", $first_name) === 0 && preg_match("/^[A-Za-z]+$/", $last_name) === 0 ) { 
		$fail = true;
		array_push($report, "First And Last Name Must Only Contain Letters.");
	}

	//Validate email format
	if(!filter_var($user_email, FILTER_VALIDATE_EMAIL)) { 
		$fail = true; 
		array_push($report, "Invalid Email Format");
	}

	//If no errors then try sql else echo error
	if($fail === false){
		//Prepare statements
    	$sql = $conn->prepare("INSERT INTO USER(FNAME, LNAME, EMAIL) VALUES(?, ?, ?)");
		$sql->bind_param("sss", $first_name, $last_name, $user_email);
		if(!($sql->execute())) {
			$fail = true;
			if(strpos($sql->error, "user_email"))
     		   	array_push($report, "Email Already Exists.");
		}
			
		 //Keep guest logged in
         setcookie("online", "true");
         setcookie("fname", $first_name);
         setcookie("lname", $last_name);

		//Clear contents from variable
		$sql->free_result();
    	$sql->close();
	} 
}


if($fail == true){
    $msg = array("error" => $report);
} else {
    $msg = array("success" => "success");
}
header("Content-Type: application/json");
echo json_encode($msg);

?>

