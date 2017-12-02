<?php
include 'db_connect.php';

$id = $course = $time_date = "";
$fail = false;

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["u_id"]) && isset($_POST["course"]) && isset($_POST["time_date"])) {

	//Get values and trim spaces, strip input of html and php tags, escape input
	$id = mysqli_real_escape_string($conn, strip_tags(trim($_POST["u_id"])));
	$course = mysqli_real_escape_string($conn, strip_tags(trim($_POST["course"])));
	$time_date = mysqli_real_escape_string($conn, strip_tags(trim($_POST["time_date"])));

} else {
	$fail = true;
	echo 'fuck';
}

if(strcmp($id, "") === 0 || strcmp($course, "") === 0 || strcmp($time_date, "") === 0){
	$fail = true;
	echo 'fucking shit';
}

if($fail === false){
$sql = $conn->prepare("INSERT INTO REQUEST(USER_ID, TIME_D, PARK_ID) VALUES(?, ?, ?)");
$sql->bind_param("isi", $id, $time_date, $course);

if(!($sql->execute())) echo "<br>" . $sql->error;

//Clear contents from variable
$sql->free_result();
	$sql->close();
} else if($fail === true){
echo "<br>" . "ERROR: CHECK INPUT";
}

mysqli_close($conn);

?>
