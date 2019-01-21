<?php 
include "../db.php";

if(isset($_GET["haikus"])){
	if($_GET["haikus"] === "all"){
		echo displayHaikus();
	} elseif($_GET["haikus"] === "deleted"){
		echo displayDeleted();
	}
}