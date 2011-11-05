<?php
	require("../config.php");
	
	if(!isset($_SERVER['PHP_AUTH_USER']))
	{
		header('WWW-Authenticate: Basic realm="Restricted Area"');
    	header('HTTP/1.0 401 Unauthorized');
		die;
	}
	if($_SERVER['PHP_AUTH_USER'] != ACP_USERNAME || $_SERVER['PHP_AUTH_PW'] != ACP_PASSWORD)
	{
		header('HTTP/1.0 401 Unauthorized');
		die;
	}
	
	$Con = mysql_connect(MYSQL_HOST,MYSQL_USER,MYSQL_PASS);
	mysql_select_db(MYSQL_DATA);
	if($_GET['type'] != "invalid")
		$Info = "AND email LIKE '%{$_GET['email']}%'";
	$Res = mysql_query("SELECT email,txn_id,amount,date,info FROM log WHERE status='{$_GET['type']}' {$Info} ORDER BY date DESC LIMIT 100 ");
	$White = false;
	while($row = mysql_fetch_array($Res))
	{
		if($White)
		{
			$OUTPUT .= "
				<tr>
					<td> {$row['email']}</td>
					<td> {$row['txn_id']}</td>
					<td> {$row['date']}</td>
					<td> ".CURRENCY_CHAR."{$row['amount']}</td>
					<td> {$row['info']}</td>
				</tr>
				";
			$White = false;
		}
		else
		{
			$OUTPUT .= "
				<tr class='gray'>
					<td> {$row['email']}</td>
					<td> {$row['txn_id']}</td>
					<td> {$row['date']}</td>
					<td> ".CURRENCY_CHAR."{$row['amount']}</td>
					<td> {$row['info']}</td>
				</tr>
				";
			$White = true;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Administration Panel</title>

<style type="text/css">
	.infoTable
	{
		border-left:#000000 solid 1px;
		font-size:10pt;
	}
	
	.infoTable td
	{
		border-right:#000000 solid 1px;
		vertical-align:top;
		/*border-bottom:#000000 solid 1px;*/
	}
	
	.infoTable .truncator
	{
		height:20px;
		overflow:auto;
	}
	
	.gray
	{
		/*border-bottom:#000000 solid 1px;*/
		background:#EAEAEA;
	}
</style>
</head>

<body>
<h1>Donation Logs</h1>
<p>You can find donation logs here. Completed entries have had no problems.
Failed entries could be an invalid character or tampered form information.
Invalid entries have been reported as nonexistant from PayPal and should be investigated.</p>

<form method="get" action="">
	<table width="400" border="0" cellspacing="2px">
		<tr>
			<td>Logs:</td>
			<td><label>
			<input type="radio" name="type" value="completed" checked="checked" />
			Completed</label></td>
			<td><label>
			<input type="radio" name="type" value="failed" />
			Failed</label></td>
			<td><label>
			<input type="radio" name="type" value="invalid" />
			Invalid</label></td>
		</tr>
		<tr>
			<td>Email:</td>
			<td><input type="text" name="email" size="16" /></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td><input type="submit" value="Search" /></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
	</table>
</form>
<br />
<table class="infoTable" width="700" border="0" cellspacing="0px" cellpadding="2px">
	<tr>
		<td>PayPal Email</td>
		<td>Transaction Id</td>
		<td>Date</td>
		<td>Paid</td>
		<td>Information</td>
	</tr>
	<?php echo $OUTPUT; ?>
</table>

</body>
</html>
