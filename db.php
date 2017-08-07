<?php
/**
 * Database class
 */

class db extends mysqli {
	public function get_results($query) {
		$res = $this->multi_query($query);
		if ( ! $res ) {
			print_r($this->error);
		}
		return gettype($res)!="boolean" ? $res->fetch_all() : false;
	}
}

global $db;
$db = new db($host='localhost', $username="root", $passwd="", $dbname="finances");
