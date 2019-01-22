<?php 
include "../db.php";

//add a restore or delete vote to a haiku that is in the deleted section
if(isset($_POST["poem"]) && isset($_POST["value"])){
	if(isset($_SESSION["update"][$_POST["poem"]])){
		echo "false";
	} else {
		$db->restoreHaiku($_POST["poem"], $_POST["value"]);
		$_SESSION["update"][$_POST["poem"]] = 1;
		echo "true";
	}
}
