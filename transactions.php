<?php

include('inc.php');

$finApi->addTrans('2017-10-11','new purchase','lmoa',37.2,2,1);

?><!Doctype html>
<html>
	<head>
		<title>Transaction History</title>
		<link rel=stylesheet href="styles/main.css" />
	</head>
	<body>
		<div id=main_container>
			<div>
				<h2>Testing</h2>
				<?php pp($finApi->getAccountBalance(1));?>
			</div>
			<div id=trans_container>
				<h2>Transactions</h2>
				<table class=spreadsheet >
					<tr>
					<?php foreach ($db->get_results("SELECT COLUMN_NAME AS name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=? AND TABLE_NAME='transactions'",array(DB_NAME),false) as $col): ?>
						<th><pre><?php echo $col['name']; ?></pre></th>
					<?php endforeach; ?>
					</tr>
				<?php foreach ($test = $finApi->getTransactions() as $trans): ?>
					<tr>
					<?php foreach ($trans as $col=>$colval): ?>
						<td><pre><?php echo $col=='amount'?money_format('%',$colval):$colval;?></pre></td>
					<?php endforeach; ?>
					</tr>
				<?php  endforeach; ?>
				</table>
			</div>
			<?php pp($test); ?>
			<div id=accts_container>
				<table class=spreadsheet>
				
				</table>
			</div>
		</div>
	</body>
</html>