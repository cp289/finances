<?php

// for debugging...
function pp($var, $die=false) {
	print('<pre>');
	print_r($var);
	print('</pre>');
	if ($die) die();
}

function pre($var) {
	print('<pre>');
	print($var);
	print('</pre>');
}

function db_error($obj, $message) {
	$out = "<strong>Error: <em>{$message}</em> ({$obj->errno})</strong> {$obj->error}";
	pre($out);
	return $out;
}