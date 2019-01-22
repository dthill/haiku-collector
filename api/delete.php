<?php 
include "../db.php";

//add a delete vote to a Haiku
if(isset($_POST["poem"])){
	if(isset($_SESSION["delete"][$_POST["poem"]]) && !isset($_SESSION["update"][$_POST["poem"]])){
		echo "false";
	} else {
		$db->deleteHaiku($_POST["poem"]);
		$_SESSION["delete"][$_POST["poem"]] = 1;
		unset($_SESSION["update"][$_POST["poem"]]);
		echo "true";
	}
}