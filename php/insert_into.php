<?php
include "db_connect.php";
include "secstuff.php";

$first_name = $last_name = $user_name = $user_email = $user_pass = $user_pass_confirm = "";
$fail = false;

//Check if input is from post method and check if null.
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["firstname"]) && isset($_POST["lastname"]) 
		&& isset($_POST["uname"]) && isset($_POST["em"]) && isset($_POST["passW"]) && isset($_POST["passWConfirm"])) {
	
	//Get values and trim spaces, strip input of html and php tags, escape input 	
	$first_name = mysqli_real_escape_string($conn , strip_tags(trim($_POST["firstname"])));
	$last_name = mysqli_real_escape_string($conn, strip_tags(trim($_POST["lastname"])));
	$user_name = mysqli_real_escape_string($conn ,strip_tags(trim($_POST["uname"])));
	//Also sanitize illegal inputs for email
	$user_email = mysqli_real_escape_string($conn, filter_var(strip_tags(trim($_POST["em"])), FILTER_SANITIZE_EMAIL));
	$user_pass = mysqli_real_escape_string($conn, strip_tags(trim($_POST["passW"])));
	$user_pass_confirm = mysqli_real_escape_string($conn, strip_tags(trim($_POST["passWConfirm"])));
	

} else {
	$fail = true;
}

//check if empty string
if(strcmp($first_name, "") === 0 || strcmp($last_name, "") === 0 || strcmp($user_name, "") === 0 
			|| strcmp($user_email, "") === 0 || strcmp($user_pass, "") === 0  || strcmp($user_pass_confirm, "") === 0) { 

	$fail = true; 
}
//check if string contains any spaces in the actually trimmed string
//Exclude passwords
if(strpos($first_name, " ") === true || strpos($last_name, " ") === true || strpos($user_name, " ") === true 
		|| strpos($user_email, " ") === true ) { 

	$fail = true; 
}

//Only letters in names, username can contain numbers
if(preg_match("/^[A-Za-z]+$/", $first_name) === 0) { $fail = true; }
if(preg_match("/^[A-Za-z]+$/", $last_name) === 0) { $fail = true; }
if(preg_match("/^[A-Za-z0-9]+$/", $user_name) === 0) { $fail = true; }

//Validate email format
if(!filter_var($user_email, FILTER_VALIDATE_EMAIL)) { $fail = true; }

//Check to see if password and password confirm matches
if(strcmp($user_pass, $user_pass_confirm) !== 0) { $fail = true; }

//If no errors then try sql else echo error
if($fail === false){
	//Encrypt
	$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length("$user_pass"));
	$user_pass = openssl_encrypt($user_pass, "aes-256-cbc", $key, $options=0, $iv);

    $sql = "INSERT INTO user(first_name, last_name, user_name, user_email, password)
        VALUES('$first_name', '$last_name', '$user_name', '$user_email', '$user_pass')";
    if(!mysqli_query($conn, $sql)) echo "<br>" . mysqli_error($conn); 
} else if($fail === true){
	echo "<br>" . "ERROR: CHECK INPUT";
}

//Close mysql connection
mysqli_close($conn);
?>

<br>
<a href="../index.html"> Return to main page</a>
