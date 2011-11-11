<?php  
$timer = microtime(true);
session_start();
if( isset($_SESSION['t'] ) )
{
$template = $_SESSION['t'];
}
else
{
$template = 'default';
}
if( isset($_GET['temp']) )
{
$template = $_GET['temp'];
if( file_exists("css/style-{$template}.css") && strlen($template) < 10 )
{
$_SESSION['t'] = $template;
}
else
{
$template = 'default';
}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $title ?></title>
<?php echo "<link href='css/style-{$template}.css' type='text/css' rel='stylesheet' />"; ?>
<?php echo "<link href='css/extra.css' type='text/css' rel='stylesheet' />"; ?>
<link href="../images/icon/favicon.ico" rel="icon" type="image/x-icon" />
</head>
<body>
<div id="header">
<div class="themerow">
<a href="?temp=default" style="color: #0CF;"><div class="inline">1</div></a>
<a href="?temp=ragnaros" style="color: #FC0"><div class="inline">2</div></a>
<a href="?temp=akama" style="color: #96F"><div class="inline">3</div></a>
<a href="?temp=illidan" style="color: #6F0"><div class="inline">4</div></a>
<a href="?temp=deathwing" style="color: #F03"><div class="inline">5</div></a>
<a href="?temp=ulduar" style="color:#FFF"><div class="inline">6</div></a>
</div>
</div>
<div id="navigation"><?php include('navigation.php') ?></div>
<div id="preloaded-images">
   <img src="../images/arthas.jpg" width="1" height="1" alt="Image 01" />
   <img src="../images/ragnaros.jpg" width="1" height="1" alt="Image 02" />
   <img src="../images/akama.jpg" width="1" height="1" alt="Image 03" />
   <img src="../images/illidan.jpg" width="1" height="1" alt="Image 04" />
   <img src="../images/deathwing.jpg" width="1" height="1" alt="Image 05" />
   <img src="../images/uludar.jpg" width="1" height="1" alt="Image 06" />
</div>