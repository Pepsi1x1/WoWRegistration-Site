<?php 
$servername = "MyServer";
$yoursite = "www.example.com";
$administrator = "Admin";
$adminemail = "admin@example.com";
$title = "My Server";
$dbhost = "localhost";
$dbuser = "MyServerSiteUser";
$dbpass = "";
$dbselect = "wcellauthserver";
$dbcselect = "wcellrealmserver";

$minUsernameCharacters = 3;
$maxUsernameCharacters = 20;
$minPasswordCharacters = 3;
$maxPasswordCharacters = 20;
$requiresUniqueEmail = true;
$canRegisterMultipleAccountsPerSession = false;
$conf = array(
"ok" => "<p><a href='index.php'>Congratulations, your account has been successfully created!</a></p><br />",
);
?>