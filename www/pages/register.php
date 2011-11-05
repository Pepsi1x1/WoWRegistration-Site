<?php
if (!isset ($_COOKIE[ini_get('session.name')]))
{
    session_start();
}

if (!isset($_SESSION['initiated']))
{
    session_regenerate_id();
    $_SESSION['initiated'] = true;
}

if (isset($_SESSION['HTTP_USER_AGENT']))
{
    if ($_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT']))
    {
        /* Prompt for password */
        //exit;
    }
}
else
{
    $_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
}

function reg()
{		 
	if($_SERVER['REQUEST_METHOD'] == "POST")
	{
		//Validate recieved data!
		switch ($_POST['form'])
		{
			case 'registration':
				$allowed = array();
				$allowed[] = 'token';
				$allowed[] = 'form';
				$allowed[] = 'login';
				$allowed[] = 'password';
				$allowed[] = 'email';

				$sent = array_keys($_POST);
				if ($allowed != $sent)
				{
					return "We're awfully sorry but something went wrong with the form, how retarded!";
				}
		}
		

		if (!isset($_SESSION['token']) || $_POST['token'] != $_SESSION['token'])
		{
			return "We're awfully sorry but something went wrong with the session, how retarded!";
		}

		//Set up an array to store the data we have recieved
		//once it has been verified as sane
		$clean = array();
		$clean['email'] = '';
		$clean['login'] = '';
		$clean['password'] = '';
		$clean['ip'] = 0;
		
		include "/admin/config.php";
		//If we are here all our data has been validated! We're good to go.
		$connect = mysql_connect($dbhost,$dbuser,$dbpass) or die(mysql_error());
		mysql_select_db($dbselect,$connect) or die(mysql_error()); 
		
		if( is_str($_POST['login']) && strlen($_POST['login']) >= $minUsernameCharacters && strlen($_POST['login']) <= $maxUsernameCharacters)
			$clean['login'] = scrubSqlBoundInput($_POST['login']);
		else
			return "<div style='background-color:red; color:white; font-weight:bold;'>Error: Username must be at least ".$minUsernameCharacters." and less than ".$maxUsernameCharacters." characters!</div>".form(setFormToken());
		if( is_str($_POST['password']) && strlen($_POST['password']) >= $minPasswordCharacters && strlen($_POST['password']) <= $maxPasswordCharacters )
			$clean['password'] = scrubSqlBoundInput($_POST['password']);
		else
			return "<div style='background-color:red; color:white; font-weight:bold;'>Error: Password must be at least ".$minPasswordCharacters." and less than ".$maxPasswordCharacters." characters!</div>".form(setFormToken());
		if( validateEmail($_POST['email']) )
			$clean['email'] = scrubSqlBoundInput($_POST['email']);
		else
			return "<div style='background-color:red; color:white; font-weight:bold;'>Error: Invalid E-mail!</div>".form(setFormToken());

		$Users_IP_address = VisitorIP();
		if (!validateIpAddress($Users_IP_address))
			return "We're awfully sorry but something went wrong with getting your ip address, how retarded!";
		$clean['ip'] = GetIPv4AddressBytes($Users_IP_address);
		
		if(isset($_SESSION['reg']) && !$canRegisterMultipleAccountsPerSession)
			return "<div style='background-color:red; color:white; font-weight:bold;'>Error: You have already registered this account!</div>".form(setFormToken());
			
		if($requiresUniqueEmail && !isEmailUnique($clean['email']))
			return "<div style='background-color:red; color:white; font-weight:bold;'>Error: An account already exists with this E-mail!</div>".form(setFormToken());

		$sha1pass = sha1(strtoupper($clean['login']).":".strtoupper($clean['password']), true);
		$sql = "SELECT MAX(AccountId) as maxAccId FROM account";
		$maxAccId = 0;
		$result = mysql_query($sql);
		if($result)
		{
			$row = mysql_fetch_array($result);
			$maxAccId=$row['maxAccId'] + 1;
		}
		$sql = "INSERT INTO account(`AccountId`, `Created`, `Name`, `Password`, `EmailAddress`, `ClientId`, `RoleGroupName`, `IsActive`, `LastIp`) VALUES ('$maxAccId', NOW(), UPPER('".$clean['login']."'), '$sha1pass', '".$clean['email']."', 2, 'Player', 1, '".$clean['ip']."');";
		if(mysql_query($sql))
		{
			mysql_close($connect);   
			$_SESSION['reg'] = true;
			return "<div style='font-weight:bold;'>".$conf["ok"]."</div>";
		}
		
		switch(mysql_errno())
		{
			case 1062 :
			return "<div style='background-color:red; color:white; font-weight:bold;'>This account is already registered!</div> ".form(setFormToken());
			break;
		}
		return "MySql Error : ".mysql_errno()."<br />".mysql_error()."<br />".form(setFormToken());
	}
	else
	{

		return form(setFormToken());
	}
}

function setFormToken()
{
		$token = md5(uniqid(rand(), true));
		$_SESSION['token'] = $token;
		return $token;
}
		
function is_str($str)
{
	return preg_match("#^[A-Za-z0-9]+$#",$str);
}

function validateEmail($mail)
{
	$email_pattern = '/^[^@\s<&>]+@([-a-z0-9]+\.)+[a-z]{2,}$/i';
	if(preg_match($email_pattern,$mail));
		return true;
	return false;
}

function scrubSqlBoundInput($str)
{
	if(!get_magic_quotes_gpc())
	{
		return function_exists("mysql_real_escape_string") ? mysql_real_escape_string($str) : mysql_escape_string($str);
	}
	else
		return $str;
}

function isEmailUnique($mail)
{
	$c = mysql_query("SELECT EmailAddress FROM account WHERE EmailAddress = '$mail';");
	if(mysql_num_rows($c) == 0)
		return true;
}

function strToHex($string)
{
    $hex='0x';
    for ($i=0; $i < strlen($string); $i++)
    {
        $hex .= dechex(ord($string[$i]));
    }
    return $hex;
}

function hexToStr($hex)
{
    $string='';
    for ($i=0; $i < strlen($hex)-1; $i+=2)
    {
        $string .= chr(hexdec($hex[$i].$hex[$i+1]));
    }
    return $string;
}

function visitorIP()
{ 
    if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else
		$ip = $_SERVER['REMOTE_ADDR'];
 
    return trim($ip);
}

function validateIpAddress($ip)
{
	if (preg_match("/^((1?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(1?\d{1,2}|2[0-4]\d|25[0-5])$/",$ip,$ipcheck))
			return true;
			
	return false;
}

function GetIPv4AddressBytes($ip)
{
	if ( empty($ip) )
		return 0;
	
	// First thing to do, split the IP into its quads
	$ip = explode('.', $ip);
	
	// Now we convert it to an unsigned int
	$ipb = sprintf('%c', $ip[0] );
	$ipb .= sprintf('%c', $ip[1] );
	$ipb .= sprintf('%c', $ip[2] );
	$ipb .= sprintf('%c', $ip[3] );
	
	return $ipb;
}

function form($token)
{
	global $minUsernameCharacters, $minPasswordCharacters;
	return '
	<form action="'.htmlentities($_SERVER['PHP_SELF']).'?p=register" method="post">
	<input type="hidden" name="token" value="'.$token.'" />
	<input type="hidden" name="form" value="registration" />
	<table>
	<tr><td>Username : </td><td><input name="login" value="" type="text" /> (Minimum '.intval($minUsernameCharacters).' characters)</td></tr> 
	<tr><td>Password : </td><td><input name="password" value="" type="password" /> (Minimum '.intval($minPasswordCharacters).' characters)</td></tr>
	<tr><td>E-mail : </td><td><input name="email" value="" type="text" /></td></tr>
	<tr><td colspan="2" align="center"><input type="submit" /></td></tr>
	</table>
	</form>';
}
?>
<div id="content">
<center><h1>Account Registration</h1></center><br />
<p>
<center><?php echo reg(); ?></center>
</p>
</div>