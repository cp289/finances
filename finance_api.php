<?php

class finance_api {
	
	private $dbname;
	
	public function __construct ( $dbname='finances' ) {
		$this->dbname = $dbname;
		$this->_createDatabase();
		
		global $db;
		$db->select_db($dbname);
	}
	
	private function _createDatabase(){
		global $db;
		
		// create finances and select it
		$db->get_results("CREATE DATABASE IF NOT EXISTS `{$this->dbname}`;");
		
		// create data tables
		$sql = "CREATE TABLE IF NOT EXISTS transactions (
			`id` mediumint NOT NULL AUTO_INCREMENT,
			`time_ent` datetime NOT NULL,
			`date` DATE NOT NULL,
			`location` VARCHAR(50) NOT NULL,
			`origin` smallint NOT NULL,
			`destin` smallint NOT NULL,
			`amount` DECIMAL(10,2) NOT NULL,
			`descr` VARCHAR(150),
			PRIMARY KEY (id)
		);";
		$sql .= "CREATE TABLE IF NOT EXISTS accounts (
			`id` mediumint NOT NULL AUTO_INCREMENT,
			`type` smallint NOT NULL,
			`name` varchar(50) NOT NULL,
			`descr` varchar(150),
			PRIMARY KEY (id)
		);";
		$sql .= "CREATE TABLE IF NOT EXISTS events (
			`id` int NOT NULL AUTO_INCREMENT,
			`name` varchar(50) NOT NULL,
			`date` date NOT NULL,
			`recur` smallint NOT NULL,
			`descr` varchar(150),
			PRIMARY KEY (id)
		);";
		$sql .= "CREATE TABLE IF NOT EXISTS tags (
			`id` mediumint NOT NULL AUTO_INCREMENT,
			`name` varchar(50) NOT NULL,
			`descr` varchar(150),
			PRIMARY KEY (id)
		);";
		$sql .= "CREATE TABLE IF NOT EXISTS tag_map (
			`id` mediumint NOT NULL AUTO_INCREMENT,
			`trans` mediumint NOT NULL,
			`tag` mediumint NOT NULL,
			PRIMARY KEY (id)
		);";
		//$sql .= "CREATE TABLE IF NOT EXISTS tag_map ("
		$db->get_results($sql);
	}
	
	private function _resetDatabase() {
		global $db;
		$db->get_results( "DROP DATABASE $dbname" );
		$this->_createDatabase();
	}
	
	// Important: escape all parameters (find right method)
	public function addAccount($name, $type, $descr='NULL') {
		global $db;
		$db->get_results("INSERT INTO accounts (type,name,descr) VALUES ('$type','$name','$descr');");
	}
	
	public function addTrans($date, $location, $origin, $destin, $amount, $descr) {
		global $db;
		$time_ent = date('Y-m-d H:i:s');
		$params = array( $time_ent,$date,$location,$origin,$destin,$amount,$descr );
		$sql = "INSERT INTO transactions (time_ent,date,location,origin,destin,amount,descr)
			VALUES (?,?,?,?,?,?,?);";
		$db->get_results($sql, $params);
	}
}

// create global API object
global $finApi;
$finApi = new finance_api();
