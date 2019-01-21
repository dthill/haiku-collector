<?php 
include "../db.php";

if(isset($_POST["poem"]) && isset($_POST["value"])){
	if(isset($_SESSION["update"][$_POST["poem"]])){
		echo "false";
	} else {
		restoreHaiku($_POST["poem"], $_POST["value"]);
		$_SESSION["update"][$_POST["poem"]] = 1;
		echo "true";
	}
}
