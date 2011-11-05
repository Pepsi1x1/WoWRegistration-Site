<?php include('admin/config.php') ?>
<?php include('pages/header.php') ?>
<?php 
if( isset( $_GET['p']) )
{
$page  = strtolower( substr( $_GET['p'], 0, 8 ) );
if( !file_exists("pages/{$page}.php") )
$page = 'index';
}
else $page = 'news';
include( "pages/{$page}.php" ); 
?>
<?php include('pages/footer.php') ?>