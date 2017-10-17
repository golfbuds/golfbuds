<?php
#include "db_connect";
$command = "SELECT * FROM tablename WHERE course_id = 1,2,3,4";

$result = mysql_query($command);

$thisArray = array();

$index = 0;

$totalQ = 0;
$qArray = array();

//Populate thisArray with users from the request table

while($row = mysql_fetch_assoc($result)){
	
	$thisArray[$index] = $row;
	$index++;
	
}
//While number of people in group is less than 4, the loop will search for people whose selected playtimes
//are the same. Once there has been a match, the algo will place them in a seperate q for tracking and then
//remove those users from the request table to ensure double checks will not happen.

while(count($totalQ) != 4){
	for($x = 0; $x<count($thisArray); $x++){
	
		if($thisArray[$x].time == $thisArray[$x+1].time){

			$qArray[$x]   = $thisArray[$x];
			$qArray[$x+1] = $thisArray[$x];
			$x++;
			$sql = "DELETE FROM tablename WHERE id=$x AND id=$x+1";
			if(mysqli_query($conn, $sql)){
				echo "Done";
			}else{
					echo "Not Done" . mysqli_error($conn);
			}
		}
		if(count($totalQ == 4)){
			break;
		}	
	
		if($x == count($thisArray)-1){
			$x=0;
		}
	}

}
//This will send an email confirming the grouping of players once the total q is full.

$msg = "Your Group has been created successfully.\n Your time is for 'time' at 'course'.\n You will be playing with xyz. Enjoy your round!";
$msg = wordwrap($msg,70);
	mail($qArray[0].mail, "Group Created", $msg);
	mail($qArray[1].mail, "Group Created", $msg);
	mail($qArray[2].mail, "Group Created", $msg);
	mail($qArray[3].mail, "Group Created", $msg);

?>