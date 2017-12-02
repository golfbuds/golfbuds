<?php
include 'db_connect.php';

$id = $course = $res_date = $res_time = "";
$fail = false;

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["u_id"]) && isset($_POST["course"]) && isset($_POST["res_date"]) && isset($_POST["res_time"])) {

	//Get values and trim spaces, strip input of html and php tags, escape input
	$id = mysqli_real_escape_string($conn, strip_tags(trim($_POST["u_id"])));
	$course = mysqli_real_escape_string($conn, strip_tags(trim($_POST["course"])));
	$res_date = mysqli_real_escape_string($conn, strip_tags(trim($_POST["res_date"])));
	$res_time = mysqli_real_escape_string($conn, strip_tags(trim($_POST["res_time"])));

} else {
	$fail = true;
}

if(strcmp($id, "") === 0 || strcmp($course, "") === 0 || strcmp($res_date, "") === 0 || strcmp($res_time, "") === 0){
	$fail = true;
}

if($fail === false){
$sql = $conn->prepare("INSERT INTO REQUEST(USER_ID, RES_DATE, RES_TIME, PARK_ID) VALUES(?, ?, ?, ?)");
$sql->bind_param("isi", $id, $res_date, $res_time, $course);

if(!($sql->execute())) echo "<br>" . $sql->error;

//Clear contents from variable
$sql->free_result();
	$sql->close();
} else if($fail === true){
echo "<br>" . "ERROR: CHECK INPUT";
}

mysqli_close($conn);

?>
