<?php
	include "db_connect.php";
	include "secstuff.php";
	
	$username = $password = "";
	$fail = false; //Error reporting

	//Start a session
	session_start();

	//Check if input correctly set
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
	//exclude password
	if(strpos($username, " ") === true)  {
    	$fail = true;
	}

	//Only letters and number in username
	//if(preg_match("/^[A-Za-z0-9]+$/", $username) === 0) { $fail = true; }


	//If no errors
	if($fail === false)	{
		//Encrypt
		$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length("$password"));
		$password = openssl_encrypt($password, "aes-256-cbc", $key, $options=0, $iv);
		//Prepare statments for security
		$sql = $conn->prepare("select * from USER where EMAIL=? and PASSWORD=?");
	    $sql->bind_param("ss", $username, $password);
        $sql->execute();
		$res = $sql->get_result();	

		//Login is correct
		if($res->num_rows == 1) {
			$sql = $conn->prepare("select FNAME, LNAME from USER where EMAIL=?");
			$sql->bind_param("s", $username);
			$sql->execute();
			$sql->bind_result($fname, $lname);

			//Get results and put in session variables
			while($sql->fetch()) {
				$_SESSION["fname"] = $fname;
				$_SESSION["lname"] = "$lname";
			}
			//Clear results from variable
			$sql->free_result();
			$sql->close();

			$report = $report .  "\\nSuccess Login.";	
		} else {
			$report = $report . "\\nError Login.";
		}
		
	} else {
		$report = $report .  "\\nERROR: CHECK INPUT";
	}
	//Put error reporting in session
	$_SESSION["report"] = $report;
	mysqli_close($conn);
?>

<!-- If Successful login then go to profile-->
<script type="text/javascript">
	var i = "<?php echo $report ?>";
	if(i.includes("Success Login.") == true)
		 window.location.href = "../profile.html";
</script>

<!-- If fail then show error -->
<h3> <?php echo $report ?></h3>
<a href="../index.html">Home page</a>



