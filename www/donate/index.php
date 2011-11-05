<?php
	header("Cache-control: no-cache, must-revalidate\r\n");
	require_once("../../admin/donate_config.php");
	if(isset($_GET['char']))
	{
		$con = mysql_connect(MYSQL_HOST,MYSQL_USER,MYSQL_PASS);
		mysql_select_db(MYSQL_DATA);
		$Name = mysql_real_escape_string($_GET['char']);
		$Realm = mysql_real_escape_string($_GET['realm']);
		$Realm = (int)$Realm+1;
		$res = mysql_query("SELECT sqlhost,sqluser,sqlpass,chardb FROM realms WHERE entry='{$Realm}'");
		$row = mysql_fetch_array($res);
		mysql_close($con);
		$con = mysql_connect($row['sqlhost'],$row['sqluser'],$row['sqlpass']);
		
		mysql_select_db($row['chardb']);
		$res = mysql_query("SELECT EntityLowId FROM characterrecord WHERE name='{$Name}'");
		if(mysql_num_rows($res) == 1)
		{
			$row = mysql_fetch_array($res);
			echo $row['EntityLowId'];
		}
		else
		{
			echo "0";
			die;
		}
		mysql_close($con);
	}
	else
	{
		$con = mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS);
		mysql_select_db(MYSQL_DATA);
		$res = mysql_query("SELECT entry,name FROM realms");
		$REALMS = "{";
		while($row = mysql_fetch_array($res))
		{
			$REALMS .= ((int)$row['entry']-1).":\"".$row['name']."\",";
		}
		$REALMS .= "\"undefined\":0}";
		$res = mysql_query("SELECT entry,name,realm,description,price FROM rewards");
		$REWARDS = "{";
		$DESCRIPTIONS = "";
		while($row = mysql_fetch_array($res))
		{
			$REWARDS .= ((int)$row['entry']-1).":{name:\"".$row['name']."\",realm:".((int)$row['realm']-1).",description:\"".addslashes($row['description'])."\",price:".$row['price']."},";
			$DESCRIPTIONS .= "<div class=\"SlidingPanelsContent\" style=\"padding:2px;\">".$row['description']."</div>";
		}
		$REWARDS .= "\"undefined\":0}";
		$REWARDS = str_replace("\r","\\r",$REWARDS);
		$REWARDS = str_replace("\n","\\n",$REWARDS);
		include("form.php");
		mysql_close($con);
	}
?>

