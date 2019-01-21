<?php

if(getenv("DB_URI") === false){
	$server = "localhost";
	$username = "root";
	$password = "";
} else {
	$server = getenv("DB_URI");
	$username = getenv("DB_USER_NAME");
	$password = getenv("DB_PASSWORD");
}

$
var_dump($server);