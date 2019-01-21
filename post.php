<?php
include "db.php";

if(isset($_POST["poem"])){
	if(createHaiku($_POST["poem"])){
		echo "success";
	} else {
		echo "failure";
	}
}

