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
			UNIQUE (name),
			PRIMARY KEY (id)
		);";
		$sql .= "CREATE TABLE IF NOT EXISTS tags (
			`id` mediumint NOT NULL AUTO_INCREMENT,
			`name` varchar(50) NOT NULL,
			`descr` varchar(150),
			UNIQUE (name),
			PRIMARY KEY (id)
		);";
		$sql .= "CREATE TABLE IF NOT EXISTS tag_map (
			`id` mediumint NOT NULL AUTO_INCREMENT,
			`trans` mediumint NOT NULL,
			`tag` mediumint NOT NULL,
			PRIMARY KEY (id)
		);";
		$db->get_results($sql);
	}
	
	private function _resetDatabase() {
		global $db;
		$db->get_results( "DROP DATABASE ?", array($this->dbname) );
		$this->_createDatabase();
	}
	
	// Important: escape all parameters (find right method)
	public function addAccount($name, $type, $descr='NULL') {
		global $db;
		$params = array($type, $name, $descr);
		$db->get_results("INSERT INTO accounts (type,name,descr) VALUES (?,?,?);", $params);
	}
	
	public function addTrans($date, $location, $origin, $destin, $amount, $descr) {
		global $db;
		$time_ent = date('Y-m-d H:i:s');
		$params = array( $time_ent,$date,$location,$origin,$destin,$amount,$descr );
		$sql = "INSERT INTO transactions (time_ent,date,location,origin,destin,amount,descr)
			VALUES (?,?,?,?,?,?,?);";
		$db->get_results($sql, $params);
	}
	
	private function _createTag($name, $descr=null) {
		global $db;
		$descr_col = empty($descr) ? '' : ',descr';
		$descr_val = empty($descr) ? '' : ",?";
		$params = empty($descr) ? array($name) : array($name, $descr);
		$db->get_results("INSERT INTO tags (name{$descr_col}) VALUES (?{$descr_val})", $params);
		pre("INSERT INTO tags (name{$descr_col}) VALUES ('$name'{$descr_val})");
	}
	
	private function _createTagMap($trans, $tag_name) {
		global $db;
		
		// tag must exist!
		$params = array($trans, $tag_name);
		$sql = "INSERT INTO tag_map (trans, tag)
			SELECT transactions.id AS trans, tags.id AS tag
			FROM transactions INNER JOIN tags
			ON transactions.id=?
			AND tags.name=?
			LIMIT 1";
		pp($db->get_results($sql, $params));
	}
	
	// more corner cases?
	public function addTag($trans, $name, $descr=null) {
		global $db;
		// create new tag if necessary
		if ( $db->get_results("SELECT COUNT(*) AS count FROM tags WHERE name=?;",array($name))[0]->count < 1 ) {
			$this->_createTag($name,$descr);
		}
		if ( $db->get_results(
				"SELECT COUNT(*) AS count FROM tag_map
				INNER JOIN tags ON tag_map.tag=tags.id
				WHERE name=?",
				array($name)
			)[0]->count < 1 ) {
			$this->_createTagMap($trans, $name);
		}
	}
}

// create global API object
global $finApi;
$finApi = new finance_api();
