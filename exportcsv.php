<?php
include('inc.php');

header('Content-type: text/csv');
header('Content-disposition: attachment;filename=finances.csv');

// Fetch data from database
echo $finApi->getCSV($finApi->getTransactions(false));

