<?php
include('inc.php');

$form = $_POST['form'];

if ( isset($form) ) {
	echo "$form form recieved.";
	switch($form){
		case 'account': handle_account_form(); break;
		case 'transaction': handle_transaction_form(); break;
	}
}

?><!Doctype html>
<html>
	<head>
		<title>Finances Dashboard</title>
		<link rel="stylesheet" href="styles/main.css">
		<script type="text/javascript" src="js/main.js"></script>
	</head>
	<body onload="setup();">
		<div id=main_container>
			<div class=section id=transactions>
				<h3>Transactions</h3>
				<hr/><br/>
				<h4>New Transaction</h4>
				<form action="uploadcsv.php" enctype="multipart/form-data" method=POST>
					<input type="file" name="csvfile" title="Ensure the CSV file has the appropriate headers!"/>
					<input type="submit" value="Upload CSV"/>
				</form>
				<br/>
				<form action="" method=POST>
					<input type=hidden name=form value=transaction />
					<table style="margin-left:auto;margin-right:auto;">
						<tr>
							<td class=aright><label>Date:</label></td>
							<td class=aleft><input name=trans_date id=trans_date type=text /></td>
							<td class=aright><label>Description:</label></td>
							<td class=aleft><input name=trans_descr type=text class=large /></td>
						</tr>
						<tr>
							<td class=aright><label>Location:</label></td>
							<td class=aleft><input name=trans_location type=text /></td>
							<td class=aright><label>Amount:</label></td>
							<td class=aleft><input name=trans_amount type=text class=small /></td>
						</tr>
						<tr>
							<td class=aright><label>From:</label></td>
							<td class=aleft><select name=trans_origin>
								<option value="">-</option>
								<?php foreach ($finApi->getAccounts() as $acct):?>
								<option value="<?php echo $acct->name; ?>"><?php echo $acct->name; ?></option>
								<?php endforeach; ?>
							</select></td>
							<td class=aright><label>To:</label></td>
							<td class=aleft><select name=trans_destin>
								<option value="">-</option>
								<?php foreach ($finApi->getAccounts() as $acct):?>
								<option value="<?php echo $acct->name; ?>"><?php echo $acct->name; ?></option>
								<?php endforeach; ?>
							</select></td>
						</tr>
						<tr><td></td><td></td><td><input type=submit value=Add class=hstretch /></td><td></td>
					</table>
				</form>
				<br/>
				<h4>All Transactions</h4>
				<button onClick="window.open('exportcsv.php');">Export CSV</button>
				<br/>
				<table class=spreadsheet>
					<tr>
					  <?php foreach ($finApi->getColumns('transactions') as $col): ?>
						<th><pre><?php echo $col->name; ?></pre></th>
					  <?php endforeach; ?>
					</tr>
				  <?php foreach ($finApi->getTransactions() as $trans): ?>
					<tr>
					  <?php foreach ($trans as $col=>$colval):
							$outval=($col=='amount'?money_format('$%n',$colval):$colval);
							$short=shorten_str($outval);?>
						<td class="<?php echo $col; ?>" title="<?php echo $outval;?>"><pre><?php echo $short;?></pre></td>
					  <?php endforeach; ?>
					</tr>
				  <?php endforeach; ?>
				</table>
			</div>
			<div class=section id=accounts>
				<h3>Accounts</h3>
				<hr/><br/>
				<h4>New Account</h4>
				<br/>
				<form action="" method=POST>
					<input type=hidden name=form value=account />
					<table style="margin-left:auto;margin-right:auto">
						<tr>
							<td class=aright><label>Name:</label></td>
							<td><input type=text name=acct_name /></td>
							<td><label>Type:</label></td>
							<td><select name=acct_multi><option value=1>Debit</option><option value=-1>Credit</option></select></td>
						</tr>
						<tr>
							<td class=aright><label>Description:</label></td>
							<td colspan=3><textarea name=acct_descr id=account_descr style="width:100%"></textarea></td>
						</tr>
						<tr>
							<td></td><td></td><td></td><td><input type=submit value=Submit /></td>
					</table>
				</form>
				<br/>
				<h4>All Accounts</h4>
				<table class=spreadsheet>
				<tr>
				<?php foreach ( $finApi->getAccountColumns() as $col ):?>
					<th><?php echo $col;?></th>
				<?php endforeach;?>
					<th>Balance</th>
				</tr>
				<?php foreach ( $finApi->getAccounts() as $acct ):?>
					<tr><?php foreach ( $acct as $col=>$val ):?>
						<td><?php echo $val;?></td>
					<?php endforeach; ?>
					<td class=balance><?php echo $finApi->getAccountBalance($acct->name);?></td>
					</tr>
				<?php endforeach; ?>
				</table>
			</div>
		</div>
	</body>
</html>
