<?php

include("inc.php");

if(isset($_POST['month'])&&!empty($_POST['month'])){
	echo $_POST['month'];
}

?><!DOCTYPE html>
<html>
	<head>
		<link type="text/css" rel=stylesheet href="styles/main.css" />
		<script type="text/javascript" src="js/main.js"></script>
	</head>
	<body onLoad="setup();">
		<div id="main_container">
			<h1>Financial Records</h1>
			<div id=addTrans>
				<button onClick="newTrans()" id="ntbutton">New Transaction</button>
				<div style="display:none;" id="ntform">
					<h2>Enter a new transaction...</h2>
					<form method="POST" action="index.php">
						<table>
							<tbody>
								<tr>
									<th>Date</th>
									<th>Amount</th>
								</tr>
								<tr>
									<td>
										<input type="number" name="month" id="m" min="1" max="12" class="snum" onclick="autoFocus(0);"/> / 
										<input type="number" name="day" id="d" min="1" max="31" class="snum" onclick="autoFocus(1);"/> / 
										<input type="number" name="year" id="y" class="snum" onclick="autoFocus(2);"/>
									</td>
									<td>
										$ <input type="text" name="amount" id="amount"/>
									</td>
								</tr>
								<tr>
									<td><input type="submit" value="Submit"/></td>
									<td><input type="button" onClick="cancelNewTrans();" value="Cancel"/></td>
								</tr>
							</tbody>
						</table>
					</form>
				</div>
			</div>
			<div id=statistics>
				<h2>Statistics</h2>
				<p>Transaction Stats.</p>
			</div>
			
			<table>
				<tbody>
					<th colspan=2>Recent Transaction History</th>
					<tr><td>A</td><td>B</td></tr>
				</tbody>
			</table>
		</div>
	</body>
</html>