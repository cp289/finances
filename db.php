<?php
/**
 * Database class
 */

class db extends mysqli {
	
	// params probably only works for single queries...
	public function get_results($sql, $params=array(), $return_object=true) {
		$queries = explode(';',$sql);
		
		$rows = array();
		foreach ( $queries as $query ) {
			if ( $query == null ) continue;
			//prepare statement
			if ( ! ($stmt = $this->prepare($query)) ) {
				db_error($this,"Prepare failed");
			}
			// Bind parameters
			if ( sizeof($params) > 0 ) {
				$dtypes = "";
				foreach ( $params as $p ) {
					switch( gettype ( $p ) ) {
						case 'string':
							$dtypes .= 's';
							break;
						case 'integer':
							$dtypes .= 'i';
							break;
						case 'double':
							$dtypes .= 'd';
							break;
						default:
							$dtypes .= 'b';
					}
				}
				if ( ! $stmt->bind_param($dtypes, ...$params) ) {
					db_error($stmt,"Bind failed");
				}
			}
			// Execute query and get result set
			if ( ! $stmt->execute() ) {
				db_error($stmt,"Execute failed");
			} else {
				do {
					if ( $res = $stmt->get_result() ) {
						while ( $row = $return_object ? $res->fetch_object() : $res->fetch_row() ) {
							$rows[] = $row;
						}
						$res->close();
					}
				} while ( $stmt->more_results() && $stmt->next_result() );
			}
		}
		return $rows;
	}
}

global $db;
$db = new db(SERVER_DOMAIN, MYSQL_USER, MYSQL_PASS, DB_NAME);
