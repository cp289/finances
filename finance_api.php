<?php

class finance_api {
	function __construct () {
		$this->createFinancesDatabase('finances');
	}
	
	public function createFinancesDatabase($dbname){
		global $db;
		
		// create finances and select it
		$db->query("CREATE DATABASE IF NOT EXISTS `$dbname`;");
		$db->select_db('finances');
		
		// create data tables
		$sql = "CREATE TABLE IF NOT EXISTS transactions (
			`id` INT NOT NULL,
			`time_ent` TIMESTAMP NOT NULL,
			`date` DATE NOT NULL,
			`year` SMALLINT NOT NULL,
			`month` SMALLINT NOT NULL,
			`day` SMALLINT NOT NULL,
			`location` VARCHAR(50) NOT NULL,
			`mon_orig` smallint NOT NULL,
			`mon_dest` smallint NOT NULL,
			`type` VARCHAR(50) NOT NULL,
			`amount` DECIMAL(10,2) NOT NULL,
			`categ` VARCHAR(50) NOT NULL,
			`comments` VARCHAR(100) NOT NULL,
			PRIMARY KEY (id)
		);";
		$sql .= "CREATE TABLE IF NOT EXISTS accounts (
			`id` mediumint NOT NULL AUTO_INCREMENT,
			`type` smallint NOT NULL,
			`name` varchar(50) NOT NULL,
			PRIMARY KEY (id)
		);";
		$sql .= "CREATE TABLE IF NOT EXISTS events (
			`id` int NOT NULL AUTO_INCREMENT,
			`name` varchar(50) NOT NULL,
			`date` date NOT NULL,
			`recur` smallint NOT NULL,
			`descr` varchar(150) NOT NULL,
			PRIMARY KEY (id)
		);";
		$db->get_results($sql);
	}

	public function addAccount($name, $type) {
		global $db;
		$db->select_db('finances');
		$out=$db->query("INSERT INTO accounts (type,name) VALUES ('$type','$name');");
		$sql = "SELECT * FROM accounts;";
		$out=$db->get_results($sql);
	}
}

// create global API object
global $fin_api;
$fin_api = new finance_api();
