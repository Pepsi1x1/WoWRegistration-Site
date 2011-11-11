<?php require_once('../admin/config.php') ?>
<?php require_once('pages/header.php') ?>
<?php 
if( isset( $_GET['p']) )
{
$page  = strtolower( substr( $_GET['p'], 0, 8 ) );
if( !file_exists("pages/{$page}.php") )
$page = 'index';
}
else $page = 'news';
require_once( "pages/{$page}.php" ); 
?>
<?php require_once('pages/footer.php') ?>