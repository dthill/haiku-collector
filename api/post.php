<?php
include "../db.php";

//create new haiku posted to this file
if(isset($_POST["poem"])){
	if($db->createHaiku($_POST["poem"])){
		echo "success";
	} else {
		echo "failure";
	}
}

