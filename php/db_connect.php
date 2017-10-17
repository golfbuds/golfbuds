<?php

$servername = "athena";
$username = "golfbuds_user";
$password = "golfbuds_db";
$dbname = "golfbuds";
// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "Connected successfully.";

?>
