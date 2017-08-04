<?php

// function to initially create financial database
function createFinancialDatabase(){
	$conn = new DBConnection('root','');
	
	$conn->createDB("finances");
	$conn->selectDB("finances");
	
	$sql = "CREATE TABLE log (
				id INT NOT NULL,
				time_ent TIMESTAMP NOT NULL,
				date DATE NOT NULL,
				year SMALLINT NOT NULL,
				month SMALLINT NOT NULL,
				day SMALLINT NOT NULL,
				location VARCHAR(50) NOT NULL,
				mon_orig VARCHAR(50) NOT NULL,
				mon_dest VARCHAR(50) NOT NULL,
				type VARCHAR(50) NOT NULL,
				amount DECIMAL(10,2) NOT NULL,
				categ VARCHAR(50) NOT NULL,
				comments VARCHAR(100) NOT NULL,
			PRIMARY KEY (id))";
}

function addAccount($name, $type) {
	$db = new mysqli($host='localhost', $username="root", $passwd="", $dbname="finances");
	$db->select_db('finances');
	//$out=$db->query("INSERT INTO accounts (type,name) VALUES ('$type','$name');");
	$sql = "SELECT * FROM accounts;";
	$out=$db->query($sql);
	$out=$db->fetch_all($sql);
	print_r($out);
	die();
}