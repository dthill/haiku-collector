<?php

session_start();
////////////////////
//Global Variables//
////////////////////


//only used for lacl dev

$server = "localhost";
$port = 3306;
$dbName = "haiku";
$username = "root";
$password = "";



/*
$server = getenv("DB_URI");
$port = getenv("DB_PORT");
$dbName = getenv("DB_NAME");
$username = getenv("DB_USER_NAME");
$password = getenv("DB_PASSWORD");
*/

/////////
//Class//
/////////

class HaikuCollection {
	public function __construct($server, $port, $dbName, $username, $password){
		//sets the limit of reports need to move a Haiku to the deleted page
		$this->deleteCount = 5;
		//mySql connection
		$this->pdo = new PDO("mysql:host=$server;port=$port;dbname=$dbName", $username, $password);
		//create table haikus
		$this->pdo->exec("
			CREATE TABLE IF NOT EXISTS haikus (
			id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			poem VARCHAR(150) NOT NULL,
			date_created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			deleted INT(6) UNSIGNED,
			date_deleted TIMESTAMP DEFAULT 0,
			restore INT(6) UNSIGNED
			);
			");
	}

	//checks if the exact same Haiku exists already if not it adds it to the db
	public function createHaiku($poem){
		$poem = htmlspecialchars($poem, ENT_QUOTES, "UTF-8");
		$stmt1 = $this->pdo->prepare("SELECT COUNT(*) FROM haikus WHERE poem = :poem");
		$stmt1->execute(array(":poem" => $poem));
		$found = $stmt1->fetch()["COUNT(*)"][0];
		if($found == 0){
			$stmt2 = $this->pdo->prepare("INSERT INTO haikus (poem) VALUES (:poem)");
			$stmt2->execute(array(":poem" => $poem));
			return true;
		} else {
			return false;
		}
	}

	//returns all the haikus that have not been deleted in json format
	public function displayHaikus(){
		$stmt = $this->pdo->prepare("SELECT poem, DATE(date_created) FROM haikus WHERE 
			date_deleted = 0 ORDER BY date_created DESC");
		$stmt->execute();
		return json_encode($stmt->fetchAll(PDO::FETCH_NUM));
	}

	//returns all the deleted haikus in json format to be displayed in the deleted section
	//permanently remove Haikus from the db that have been in the deleted section for longer than 30days
	public function displayDeleted(){
		$stmt1 = $this->pdo->prepare("DELETE FROM haikus WHERE (30 - DATEDIFF(NOW(), date_deleted)) <= 0");
		$stmt1->execute();
		$stmt2 = $this->pdo->prepare("SELECT poem, (30 - DATEDIFF(NOW(), date_deleted)) FROM haikus 
			WHERE  date_deleted != 0 ORDER BY (30 - DATEDIFF(NOW(), date_deleted)) ASC");
		$stmt2->execute();
		return json_encode($stmt2->fetchAll(PDO::FETCH_NUM));
	}

	//adds a delete vote to a haiku
	//if the delete vote is higher than the deleteCount set earlier: 
	//date_deleted is set and the haiku will be displayed in the deleted section
	public function deleteHaiku($poem){
		$stmt1 = $this->pdo->prepare("SELECT deleted FROM haikus WHERE poem = :poem");
		$stmt1->execute(array(":poem" => $poem));
		$deleted = $stmt1->fetch(PDO::FETCH_NUM)[0];
		if($deleted < $this->deleteCount - 1){
			$stmt2 = $this->pdo->prepare("UPDATE haikus SET deleted = Coalesce(deleted, 0) + 1 WHERE poem = :poem");
		} else{
			$stmt2 = $this->pdo->prepare("UPDATE haikus SET deleted = Coalesce(deleted, 0) + 1, date_deleted = NOW() WHERE poem = :poem");
		}
		$stmt2->execute(array(":poem" => $poem));
	}

	//this function is only called on Haikus that are in the deleted section
	//if the Haiku receives more restore votes than it has received deleted votes
	//it will be restored and siplayed in the normal haiku section
	function restoreHaiku($poem, $vote){
		if($vote < 0){
			$stmt1 = $this->pdo->prepare("UPDATE haikus SET deleted = Coalesce(deleted, 0) + 1 WHERE poem = :poem");
			$stmt1->execute(array(":poem" => $poem));
		} else {
			$stmt2 = $this->pdo->prepare("SELECT deleted, restore FROM haikus WHERE poem = :poem");
			$stmt2->execute(array(":poem" => $poem));
			$result = $stmt2->fetch(PDO::FETCH_NUM);
			$deleted = $result[0];
			$restore = $result[1];
			if($deleted >= $restore + 1 && $vote > 0){
				$stmt3 = $this->pdo->prepare("UPDATE haikus SET restore = Coalesce(restore, 0) + 1 WHERE poem = :poem");
			} elseif($deleted < $restore + 1  && $vote > 0){
				$stmt3 = $this->pdo->prepare("UPDATE haikus SET restore = NULL, deleted = NULL, date_deleted = 0 WHERE poem = :poem");
			}
			$stmt3->execute(array(":poem" => $poem));
		}
	}
}
//creat db object used by the api php files
$db = new HaikuCollection($server, $port, $dbName, $username, $password);

