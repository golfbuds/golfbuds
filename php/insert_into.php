<?php
include "db_connect.php";

$first_name = $last_name = $user_name = $user_email = $user_pass = "";

//if($_SERVER["REQUEST_METHOD"] == "POST"){
$first_name = $_POST["firstname"];
$last_name = $_POST["lastname"];
$user_name = $_POST["uname"];
$user_email = $_POST["em"];
$user_pass = $_POST["passW"];



$sql = "INSERT INTO user(first_name, last_name, user_email, password)
		VALUES('$first_name', '$last_name', '$user_email', '$user_pass')";
$result = $conn->query($sql) or die($conn->error);

echo "registered";
?>

<a href="../index.html"> Return to main page</a>
