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
	if (!PRINT_DB_ERRORS) return '';
	$out = "<strong>Error: <em>{$message}</em> ({$obj->errno})</strong> {$obj->error}";
	pre($out);
	return $out;
}

function handle_account_form(){
	global $finApi;
	echo 'Handle account form';
	$finApi->addAccount(
		trim($_POST['acct_name']),
		trim($_POST['acct_descr']),
		trim($_POST['acct_multi'])
	);
}

function handle_transaction_form(){
	global $finApi;
	echo 'Handle transaction form';
	$finApi->addTrans(
		trim($_POST['trans_date']),
		trim($_POST['trans_descr']),
		trim($_POST['trans_location']),
		trim($_POST['trans_amount']),
		trim($_POST['trans_origin']),
		trim($_POST['trans_destin'])
	);
}

function shorten_str($str,$len=null){
	if(!isset($len))
		$len=22;
	$out = $str;
	if(strlen($str)>=$len)
		$out = substr($str,0,$len-3);
	if (strlen($out) < strlen($str))
		$out .= '...';
	return $out;
}

