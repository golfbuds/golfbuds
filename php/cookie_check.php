<?php
	$msg ="";
	session_start();

	if(isset($_COOKIE["online"]) && isset($_COOKIE["fname"]) && isset($_COOKIE["lname"]) ){
		$_SESSION["fname"] = $_COOKIE["fname"];
		$_SESSION["lname"] = $_COOKIE["lname"];
		$msg = array("online" => "true");
	} else {
		$msg = array("online" => "false");
	}

	header("Content-Type: application/json");
    echo json_encode($msg);
?>
