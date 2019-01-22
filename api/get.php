<?php 
include "../db.php";

//display all haikus or all deleted haikus
if(isset($_GET["haikus"])){
	if($_GET["haikus"] === "all"){
		echo $db->displayHaikus();
	} elseif($_GET["haikus"] === "deleted"){
		echo $db->displayDeleted();
	}
}