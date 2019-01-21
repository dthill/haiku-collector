<?php

$server = getenv("DB_URI");
$port = getenv("DB_PORT");
$dbName = getenv("DB_NAME");
$username = getenv("DB_USER_NAME");
$password = getenv("DB_PASSWORD");
$deleteCount = 5;

session_start();

$pdo = new PDO("mysql:host=$server;port=$port;dbname=$dbName", $username, $password);

$pdo->exec("
	CREATE TABLE IF NOT EXISTS haikus (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		poem VARCHAR(150) NOT NULL,
		date_created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		deleted INT(6) UNSIGNED,
		date_deleted TIMESTAMP DEFAULT 0,
		restore INT(6) UNSIGNED
		);
	");

function createHaiku($poem){
	$pdo = $GLOBALS["pdo"];
	$poem = htmlspecialchars($poem, ENT_QUOTES, "UTF-8");
	$stmt1 = $pdo->prepare("SELECT COUNT(*) FROM haikus WHERE poem = :poem");
	$stmt1->execute(array(":poem" => $poem));
	$found = $stmt1->fetch()["COUNT(*)"][0];
	if($found == 0){
		$stmt2 = $pdo->prepare("INSERT INTO haikus (poem) VALUES (:poem)");
		$stmt2->execute(array(":poem" => $poem));
		return true;
	} else {
		return false;
	}
}

function displayHaikus(){
	$pdo = $GLOBALS["pdo"];
	$stmt = $pdo->prepare("SELECT poem, DATE(date_created) FROM haikus WHERE 
	date_deleted = 0 ORDER BY date_created DESC");
	$stmt->execute();
	return json_encode($stmt->fetchAll(PDO::FETCH_NUM));
}

function deleteHaiku($poem){
	$pdo = $GLOBALS["pdo"];
	$deleteCount = $GLOBALS["deleteCount"];
	$stmt1 = $pdo->prepare("SELECT deleted FROM haikus WHERE poem = :poem");
	$stmt1->execute(array(":poem" => $poem));
	$deleted = $stmt1->fetch(PDO::FETCH_NUM)[0];
	if($deleted < $deleteCount - 1){
		$stmt2 = $pdo->prepare("UPDATE haikus SET deleted = Coalesce(deleted, 0) + 1 WHERE poem = :poem");
	} else{
		$stmt2 = $pdo->prepare("UPDATE haikus SET deleted = Coalesce(deleted, 0) + 1, date_deleted = NOW() WHERE poem = :poem");
	}
	$stmt2->execute(array(":poem" => $poem));
} 

function restoreHaiku($poem, $vote){
	$pdo = $GLOBALS["pdo"];
	if($vote < 0){
		$stmt1 = $pdo->prepare("UPDATE haikus SET deleted = Coalesce(deleted, 0) + 1 WHERE poem = :poem");
		$stmt1->execute(array(":poem" => $poem));
	} else {
		$stmt2 = $pdo->prepare("SELECT deleted, restore FROM haikus WHERE poem = :poem");
		$stmt2->execute(array(":poem" => $poem));
		$result = $stmt2->fetch(PDO::FETCH_NUM);
		$deleted = $result[0];
		$restore = $result[1];
		if($deleted >= $restore + 1 && $vote > 0){
			$stmt3 = $pdo->prepare("UPDATE haikus SET restore = Coalesce(restore, 0) + 1 WHERE poem = :poem");
		} elseif($deleted < $restore + 1  && $vote > 0){
			$stmt3 = $pdo->prepare("UPDATE haikus SET restore = NULL, deleted = NULL, date_deleted = 0 WHERE poem = :poem");
		}
		$stmt3->execute(array(":poem" => $poem));
	}
}

function displayDeleted(){
	$pdo = $GLOBALS["pdo"];
	$stmt1 = $pdo->prepare("DELETE FROM haikus WHERE (30 - DATEDIFF(NOW(), date_deleted)) <= 0");
	$stmt1->execute();
	$stmt2 = $pdo->prepare("SELECT poem, (30 - DATEDIFF(NOW(), date_deleted)) FROM haikus 
		WHERE  date_deleted != 0 ORDER BY (30 - DATEDIFF(NOW(), date_deleted)) ASC");
	$stmt2->execute();
	return json_encode($stmt2->fetchAll(PDO::FETCH_NUM));
}

//var_dump(displayDeleted());
//createHaiku("Hello5");
//deleteHaiku("Hello1");
//restoreHaiku("Hello4",1);

