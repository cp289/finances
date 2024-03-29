<?php

class finance_api {
	
	private $dbname;
	private $accountCols;
	
	public function __construct ( $dbname=DB_NAME ) {
		global $db;
		$this->dbname = $dbname;
		$this->accountCols = array('name','descr');
		$db->select_db($this->dbname);
	}
	
	private function _createDatabase(){
		global $db;
		
		// create finances and select it
		$db->get_results("CREATE DATABASE `{$this->dbname}`");
		$db->select_db($this->dbname);
		
		// create data tables
		$sql = "CREATE TABLE transactions (
			`id` mediumint NOT NULL AUTO_INCREMENT,
			`date` DATE NOT NULL,
			`descr` VARCHAR(150),
			`location` VARCHAR(50) NOT NULL,
			`amount` DECIMAL(10,2) NOT NULL,
			`origin` smallint NOT NULL,
			`destin` smallint NOT NULL,
			PRIMARY KEY (id)
		);";
		$sql .= "CREATE TABLE accounts (
			`id` mediumint NOT NULL AUTO_INCREMENT,
			`order` mediumint DEFAULT 0,
			`name` varchar(50) NOT NULL,
			`descr` varchar(150),
			`multi` smallint DEFAULT 1,
			UNIQUE (name),
			PRIMARY KEY (id)
		);";
		$sql .= "CREATE TABLE events (
			`id` int NOT NULL AUTO_INCREMENT,
			`name` varchar(50) NOT NULL,
			`date` date NOT NULL,
			`recur` smallint NOT NULL,
			`descr` varchar(150),
			UNIQUE (name),
			PRIMARY KEY (id)
		);";
		$sql .= "CREATE TABLE tags (
			`id` mediumint NOT NULL AUTO_INCREMENT,
			`name` varchar(50) NOT NULL,
			`descr` varchar(150),
			UNIQUE (name),
			PRIMARY KEY (id)
		);";
		$sql .= "CREATE TABLE tag_map (
			`id` mediumint NOT NULL AUTO_INCREMENT,
			`trans` mediumint NOT NULL,
			`tag` mediumint NOT NULL,
			PRIMARY KEY (id)
		);";
		$db->get_results($sql);
	}
	
	private function _resetDatabase() {
		global $db;
		$db->get_results( "DROP DATABASE `{$this->dbname}`" );
		$this->_createDatabase();
	}
	
	public function addAccount($name, $descr=null, $multi=1) {
		global $db;
		$params = array($name, $descr, $multi);
		$db->get_results("INSERT INTO accounts (name,descr,multi) VALUES (?,?,?)", $params);
	}
	
	public function deleteAccount($name) {
		global $db;
		$db->get_results("DELETE FROM accounts WHERE name=?",array($name));
	}
	
	public function getAccount($name) {
		global $db;
		return $db->get_result("SELECT * FROM accounts WHERE name=?",array($name));
	}
	
	public function getAccountId($name) {
		global $db;
		return $db->get_result("SELECT id FROM accounts WHERE name=?",array($name))->id;
	}

	public function getAccounts() {
		global $db;
		$cols = implode($this->accountCols, ',');
		return $db->get_results("SELECT $cols FROM accounts ORDER BY `order` ASC");
	}

	public function getAccountBalance($name) {
		global $db;
		$id = $this->getAccountId($name);
		$params = array($id,$id,$id);
		$sql = "SELECT *
			FROM (SELECT SUM(amount) AS neg FROM transactions WHERE origin=?) AS t1,
			(SELECT SUM(amount) AS pos FROM transactions WHERE destin=?) AS t2,
			(SELECT multi FROM accounts WHERE id=?) AS t3";
		$res = $db->get_result($sql,$params);
		$bal = ($res->pos-$res->neg) * $res->multi;
		$bal = isset($bal)? $bal : '0.00';
		return money_format('$%n',$bal);
	}

	public function getAccountColumns() {
		return $this->accountCols;
	}

	public function getColumns($table) {
		global $db;
		return $db->get_results("SELECT COLUMN_NAME AS name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=? AND TABLE_NAME=?",array(DB_NAME,$table));
	}

	private function _parseCSV( $file ) {
		$rawdata = array_map('str_getcsv', file($file));
		$header  = $rawdata[0];
		unset($rawdata[0]);
		$data = array();
		foreach ( $rawdata as $row ) {
			$newrow = array();
			foreach ( $row as $i=>$entry ) {
			 $newrow[$header[$i]] = $entry;
			}
			$data[] = $newrow;
		}
		return $data;
	}

	public function submitCSV($file) {
		$data = $this->_parseCSV($file); // Does not support data with line breaks!
		foreach ( $data as $row ) {
			$this->addTrans($row['date'],$row['descr'],$row['location'],$row['amount'],$row['origin'],$row['destin']);
		}
	}
	
	public function getCSV($data) {
		foreach(array_keys($data[0]) as $col) $cols[] = '"' . $col . '"';
		$header = implode($cols, ',') . "\r\n";
		$body = "";
		foreach ( $data as $row ) {
			$entries = array();
			foreach ( $row as $entry ) $entries[] = '"' . implode(explode('"',$entry),'""') . '"';
			$body .= implode($entries, ',') . "\r\n";
		}
		return $header . $body;
	}

	public function addTrans($date, $descr, $location, $amount, $origin, $destin) { // origin, destin must be strings for the name
		global $db;
		$params = array( $date,$descr,$location,$amount,$origin,$destin );
		$db->get_result("INSERT INTO transactions
			SET date=?,descr=?,location=?,amount=?,
			origin=(SELECT id FROM accounts WHERE name=?),
			destin=(SELECT id FROM accounts WHERE name=?)", $params);
	}
	
	public function deleteTrans($id) {
		global $db;
		$db->get_results("DELETE FROM transactions WHERE id=?",array($id));
	}
	
	public function getTrans($id) {
		global $db;
		if (sizeof($out = $db->get_results("SELECT * FROM transactions WHERE id=?",array($id))) < 1) return false;
		return $out[0];
	}
	
	// add field for account filter
	public function getTransactions($reverse_order=true, $start=0, $limit=4722366482869645213696) {
		global $db;
		$order = $reverse_order ? 'DESC' : 'ASC';
		$sql = "SELECT transactions.id,date,transactions.descr,location,amount,
			ac1.name AS origin, ac2.name AS destin
			FROM transactions
			INNER JOIN accounts AS ac1 ON transactions.origin=ac1.id
			INNER JOIN accounts AS ac2 ON transactions.destin=ac2.id
			ORDER BY date $order,id $order LIMIT ?,?";
		return $db->get_results($sql,array($start,$limit),false);
	}
	
	public function updateTrans($id,$new_date=null,$new_descr=null,$new_location=null,$new_amount=null,$new_origin=null,$new_destin=null) {
		global $db;
		if ( $db->get_result("SELECT count(*) AS count FROM transactions WHERE id=?",array($id))->count < 1 )
			return false;
		$params = array();
		if (isset($new_date)) {
			$params[] = $new_date;
			$sql[] = 'date=?';
		}
		if (isset($new_location)) {
			$params[] = $new_location;
			$sql[] = 'location=?';
		}
		if (isset($new_origin)) {
			$params[] = $new_origin;
			$sql[] = 'origin=?';
		}
		if (isset($new_destin)) {
			$params[] = $new_destin;
			$sql[] = 'destin=?';
		}
		if (isset($new_amount)) {
			$params[] = $new_amount;
			$sql[] = 'amount=?';
		}
		if (isset($new_descr)) {
			$params[] = $new_descr;
			$sql[] = 'descr=?';
		}
		if (sizeof($params)>0) {
			$params[] = $id;
			$new_sql = implode(',', $sql);
			$db->get_results("UPDATE transactions SET $new_sql WHERE id=?", $params);
		}
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
	public function addTags($trans, $name, $descr=null) {
		global $db;
		// create new tag if necessary
		if ( $db->get_result("SELECT COUNT(*) AS count FROM tags WHERE name=?",array($name))->count < 1 ) {
			$this->_createTag($name,$descr);
		}
		// create tag map if it doesn't already exist
		if ( $db->get_result(
				"SELECT COUNT(*) AS count FROM tag_map
				INNER JOIN tags ON tag_map.tag=tags.id
				WHERE name=?",
				array($name)
			)->count < 1 ) {
			$this->_createTagMap($trans, $name);
		}
	}
	
	public function getTag($name) {
		global $db;
		if ( sizeof( $out=$db->get_results("SELECT * FROM tags WHERE name=?", array($name)) )<1 ) return false;
		return $out[0];
	}
	
	public function updateTag($name, $new_name=null, $new_descr=null) {
		global $db;
		if ( $db->get_result("SELECT COUNT(*) AS count FROM tags WHERE name=?",array($name))->count < 1 )
			return false;
		$params = array();
		if (isset($new_name)) {
			$params[] = $new_name;
			$sql[] = 'name=?';
		}
		if (isset($new_descr)) {
			$params[] = $new_descr;
			$sql[] = 'descr=?';
		}
		if (sizeof($params) > 0) {
			$params[] = $name;
			$new_sql = implode(',', $sql);
			$db->get_results("UPDATE tags SET $new_sql WHERE name=?", $params);
		}
	}
	
	public function deleteTag($name) {
		global $db;
		// delete all tag maps
		// try to use just SQL...
		$id = $this->getTag($name)->id;
		$db->get_results(
			"DELETE FROM tag_map WHERE tag=?",
			array($id)
		);
		// delete tag
		$db->get_results("DELETE FROM tags WHERE name=?", array($name));
	}
}

// create global API object
global $finApi;
$finApi = new finance_api();
