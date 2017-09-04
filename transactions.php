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
			<div id=trans_container>
				<h2>Transactions</h2>
				<table class=spreadsheet >
					<tr>
					<?php foreach ($finApi->getColumns('transactions') as $col): ?>
						<th><pre><?php echo $col->name; ?></pre></th>
					<?php endforeach; ?>
					</tr>
				<?php foreach ($finApi->getTransactions() as $trans): ?>
					<tr>
					<?php foreach ($trans as $col=>$colval): ?>
						<td><pre class="<?php echo $col; ?>"><?php echo $col=='amount'?money_format('$%n',$colval):$colval;?></pre></td>
					<?php endforeach; ?>
					</tr>
				<?php  endforeach; ?>
				</table>
			</div>
			<div id=accts_container>
				<table class=spreadsheet>
				
				</table>
			</div>
		</div>
	</body>
</html>

