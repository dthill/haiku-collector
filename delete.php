<?php 
include "db.php";

if(isset($_POST["poem"])){
	if(isset($_SESSION["delete"][$_POST["poem"]]) && !isset($_SESSION["update"][$_POST["poem"]])){
		echo "false";
	} else {
		deleteHaiku($_POST["poem"]);
		$_SESSION["delete"][$_POST["poem"]] = 1;
		unset($_SESSION["update"][$_POST["poem"]]);
		echo "true";
	}
}