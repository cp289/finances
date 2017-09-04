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

function handle_account_form(){
	global $finApi;
	echo 'Handle account form';
	$finApi->addAccount($_POST['acct_name'],$_POST['acct_descr'],$_POST['acct_multi']);
}

function handle_transaction_form(){
	global $finApi;
	echo 'Handle transaction form';
	$finApi->addTrans($_POST['trans_date'],$_POST['trans_descr'],$_POST['trans_location'],$_POST['trans_amount'],$_POST['trans_origin'],$_POST['trans_destin']);
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
				<br/>
				<form action="" method=POST>
					<input type=hidden name=form value=transaction />
					<label>Date:</label>
					<input name=trans_date id=trans_date type=text />
					<label>Description:</label>
					<input name=trans_descr type=text />
					<br/>
					<label>Location:</label>
					<input name=trans_location type=text />
					<label>Amount:</label>
					<input name=trans_amount type=text class=number />
					<br/>
					<label>From:</label>
					<select name=trans_origin>
						<option value="">-</option>
						<?php foreach ($finApi->getAccounts() as $acct):?>
						<option value=<?php echo $acct->id; ?>><?php echo $acct->name; ?></option>
						<?php endforeach; ?>
					</select>
					<label>To:</label>
					<select name=trans_destin>
						<option value="">-</option>
						<?php foreach ($finApi->getAccounts() as $acct):?>
						<option value=<?php echo $acct->id; ?>><?php echo $acct->name; ?></option>
						<?php endforeach; ?>
					</select>
					<input type=submit value=Add />
				</form>
				<br/>
				<h4>All Transactions</h4>
				<br/>
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
			<div class=section id=accounts>
				<h3>Accounts</h3>
				<hr/><br/>
				<h4>New Account</h4>
				<br/>
				<form action="" method=POST>
					<input type=hidden name=form value=account />
					<label>Name:</label><input type=text name=acct_name />
					<label>Type:</label>
					<select name=acct_multi><option value=1>Debit</option><option value=-1>Credit</option></select>
					<br/>
					<label>Description:</label>
					<textarea name=acct_descr id=account_descr></textarea>
					<input type=submit value=Submit />
					<br/>
				</form>
				<br/>
				<h4>All accounts</h4>
				<table class=spreadsheet>
				<?php foreach ( $finApi->getAccounts() as $acct ): ?>
					<tr><?php foreach ( $acct as $col=>$val ): ?>
						<td><?php echo $val;?></td>
					<?php endforeach; ?></tr>
				<?php endforeach; ?>
				</table>
			</div>
		</div>
	</body>
</html>
