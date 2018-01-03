<?php
include('inc.php');

const UPFILE = 'csvfile';

try {
	// Check for upload errors
	if ( !isset($_FILES[UPFILE]['error']) || is_array($_FILES[UPFILE]['error']) ) {
		throw new RuntimeException('Invalid parameters.');
	}

	switch ($_FILES[UPFILE]['error']) {
		case UPLOAD_ERR_OK:
			break;
		case UPLOAD_ERR_NO_FILE:
			throw new RuntimeException('No file sent.');
		case UPLOAD_ERR_INI_SIZE:
		case UPLOAD_ERR_FORM_SIZE:
			throw new RuntimeException('Exceeded filesize limit.');
		default:
			throw new RuntimeException('An unknown error occured while uploading.');
	}

	// Check filesize
	if ($_FILES[UPFILE]['size'] > 1000000) {
		throw new RuntimeException('Exceeded filesize limit.');
	}

	// Check file MIME type is CSV
	$finfo = new finfo(FILEINFO_MIME_TYPE);
	if (false === $ext = array_search(
		$finfo->file($_FILES[UPFILE]['tmp_name']),
			array( 'csv' => 'text/plain' ), true
	)) {
		throw new RuntimeException('Invalid file format.');
	}

	// Obtain safe unique name from file's binary data
	$fname = sprintf('./uploads/%s.%s', sha1_file($_FILES[UPFILE]['tmp_name']), $ext);
	if (!move_uploaded_file(
		$_FILES[UPFILE]['tmp_name'],
		$fname
	)) {
		throw new RuntimeException('Failed to move uploaded file.');
	}

	$finApi->submitCSV($fname);
	header('Location: ' . DASHBOARD);

} catch ( RuntimeException $e ) {
	echo $e->getMessage();
}

