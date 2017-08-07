<?php
/**
 * Database class
 */

class db extends mysqli {
	public function get_results($query) {
		$res = $this->query($query);
		while ( $res && $row = $res->fetch_assoc() ) {
			$out[] = $row;
		}
		return isset($out) ? $out : false;
	}
}