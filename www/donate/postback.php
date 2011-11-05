<?php
require("config.php");
set_time_limit(15);

function handlePayment()
{
	$Con = mysql_connect(MYSQL_HOST,MYSQL_USER,MYSQL_PASS);
	mysql_select_db(MYSQL_DATA,$Con);
	
	$Itemcount = (int)$_POST['num_cart_items'];
	$Money = (float)$_POST['mc_gross'];
	
	//Prevent txn_id recycling.
	$res = mysql_query("SELECT COUNT(*) FROM log WHERE txn_id = '{$_POST['txn_id']}'",$Con);
	if(mysql_result($res,0) != 0)
	{
		mysql_query("INSERT INTO log (date,email,txn_id,status,amount,info) VALUES (NOW(),'{$_POST['payer_email']}','{$_POST['txn_id']}','failed','{$_POST['mc_gross']}','This transaction id is a duplicate.')",$Con);
		die;
	}
	
	//Check payment status.
	if($_POST['payment_status'] != "Completed")
	{
		mysql_query("INSERT INTO log (date,email,txn_id,status,amount,info) VALUES (NOW(),'{$_POST['payer_email']}','{$_POST['txn_id']}','failed','{$_POST['mc_gross']}','Payment status was not \"Completed\", but \"{$_POST['payment_status']}\".')",$Con);
		die;
	}
	
	//Get realm info
	$res = mysql_query("SELECT entry,name,sqlhost,sqluser,sqlpass,chardb FROM realms");
	$i = 1;
	while($row = mysql_fetch_array($res))
	{
		$Realms[$i] = $row;
		$i++;
	}
	
	//Get reward info
	$res = mysql_query("SELECT entry,name,realm,price,item1,quantity1,item2,quantity2,item3,quantity3,gold FROM rewards");
	$i = 1;
	while($row = mysql_fetch_array($res))
	{
		$Rewards[$i] = $row;
		$i++;
	}
	
	//Get info of each item.
	for($i = 1;$i<=$Itemcount;$i++)
	{
		$quantity = (int)$_POST['quantity'.$i];
		for($j = 0;$j<$quantity;$j++)
		{
			$info = explode("-",$_POST['item_number'.$i]);
			$Items[$j+$i-1]["id"] = (int)$info[1]; // reward id
			$Items[$j+$i-1]["realm"] = (int)$info[0]; // realm id
			$Items[$j+$i-1]["character"] = (int)$info[2]; // character guid
		}
	}
	
	//Check availability of rewards on realms.
	foreach($Items as $key => $value)
	{
		if((int)$Rewards[$value["id"]]["realm"] != $value["realm"])
		{
			mysql_query("INSERT INTO log (date,email,txn_id,status,amount,info) VALUES (NOW(),'{$_POST['payer_email']}','{$_POST['txn_id']}','failed','{$_POST['mc_gross']}','Requested reward id ".$value['id'].", which is not available on realm ".$value['realm'].".')",$Con);
			die;
		}
	}
	
	//Check total payment amount.
	$sum = 0;
	foreach($Items as $key => $value)
	{
		$sum += (float)$Rewards[$value["id"]]["price"];
	}
	if($sum > $Money)
	{
		$info = "";
		foreach($Items as $key => $value)
		{
			$info .= "Reward: ".$Rewards[$value['id']]['name']." Cost: ".$Rewards[$value['id']]['price']."<br>\n";
		}
		mysql_query("INSERT INTO log (date,email,txn_id,status,amount,info) VALUES (NOW(),'{$_POST['payer_email']}','{$_POST['txn_id']}','failed','{$_POST['mc_gross']}','Cost of rewards ({$sum}) was not paid in full:<br>\n{$info}')",$Con);
		die;
	}
	
	//Begin rewarding items.
	foreach($Items as $key => $value)
	{
		$ccon = mysql_connect($Realms[(int)$value['realm']]['sqlhost'],$Realms[(int)$value['realm']]['sqluser'],$Realms[(int)$value['realm']]['sqlpass'],true);
		mysql_select_db($Realms[(int)$value['realm']]['chardb'],$ccon);
		$REWARDNAME = mysql_real_escape_string($Rewards[$value['id']]['name'],$ccon);
		if((int)$Rewards[$value['id']]['item1'] > 0)
		{
			mysql_query("INSERT INTO mailbox_insert_queue VALUES ('{$value['character']}','{$value['character']}','".MAIL_SUBJECT."','".MAIL_BODY."','61','0','".$Rewards[(int)$value['id']]['item1']."','1')");
		}
		if((int)$Rewards[$value['id']]['item2'] > 0)
		{
			mysql_query("INSERT INTO mailbox_insert_queue VALUES ('{$value['character']}','{$value['character']}','".MAIL_SUBJECT."','".MAIL_BODY."','61','0','".$Rewards[(int)$value['id']]['item2']."','1')");
		}
		if((int)$Rewards[$value['id']]['item3'] > 0)
		{
			mysql_query("INSERT INTO mailbox_insert_queue VALUES ('{$value['character']}','{$value['character']}','".MAIL_SUBJECT."','".MAIL_BODY."','61','0','".$Rewards[(int)$value['id']]['item3']."','1')");
		}
		if((int)$Rewards[$value['id']]['gold'] > 0)
		{
			mysql_query("INSERT INTO mailbox_insert_queue VALUES ('{$value['character']}','{$value['character']}','".MAIL_SUBJECT."','".MAIL_BODY."','61','".$Rewards[$value['id']]['gold']."','0','0')");
		}
		mysql_close($ccon);
	}
	
	//log time
	$Info = "Successful transaction:<br>\n";
	foreach($Items as $key => $value)
	{
		$Info .= mysql_real_escape_string($Rewards[$value['id']]['name'],$Con)."<br>\n";
	}
	mysql_query("INSERT INTO log (date,email,txn_id,status,amount,info) VALUES (NOW(),'{$_POST['payer_email']}','{$_POST['txn_id']}','completed','{$_POST['mc_gross']}','{$Info}')",$Con);
	mysql_close($Con);
}

function handleInvalidPayment()
{
	$Con = mysql_connect(MYSQL_HOST,MYSQL_USER,MYSQL_PASS);
	mysql_select_db(MYSQL_DATA,$Con);
	$Info = "";
	foreach($_POST as $key => $value)
	{
		$Info .= "{$key} = {$value} <br>\n";
	}
	mysql_query("INSERT INTO log (date,status,info) VALUES (NOW(),'invalid','An invalid request was made. Postdata info:<br>\n{$Info}')",$Con);
	mysql_close($Con);
}

function verifyPayment()
{
	if(sizeof($_POST) == 0)
	{
		//dont bother, maybe an accidental page visit.
		?><h1>Restricted Area</h1>
		<p>You are not permitted to access this page.</p><?php
		return;
	}
	$Postback = "cmd=_notify-validate";
	foreach($_POST as $key => $value)
	{
		$key = urlencode(stripslashes($key));
		$value = urlencode(stripslashes($value));
		$Postback .= "&{$key}={$value}";
	}
	$Sock = fsockopen(PAYPAL_URL,80,$errno,$errstr,5);
	if(!$Sock)
	{
		//handleInvalidPayment();
	}
	$Head .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
	$Head .= "Content-type: application/x-www-form-urlencoded\r\n";
	$Head .= "Content-length: ".strlen($Postback)."\r\n\r\n";
	fputs($Sock,$Head.$Postback,strlen($Head.$Postback));
	while(!feof($Sock))
	{
		$txt = fgets($Sock,1024);
		if(strcmp($txt,"VERIFIED") == 0)
		{
			handlePayment();
		}
		elseif(strcmp($txt,"INVALID") == 0)
		{
			handleInvalidPayment();
		}
	}
	fclose($Sock);
}

verifyPayment();

?>