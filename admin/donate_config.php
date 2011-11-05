<?php
//Mysql Information. This is the information for the site's database, NOT your wcell server's.
define("MYSQL_HOST","localhost"); // The mysql host for your webserver.
define("MYSQL_USER","MyServerDonateUser"); // Mysql username
define("MYSQL_PASS",""); // Mysql password
define("MYSQL_DATA","donataions"); // Database the donation tables are in.

// Path information, EXTREMELY IMPORTANT that you do these right or this will not work.
define("SITE_URL","http://www.example.com"); // COMPLETE url to your web site, NO TRAILING SLASH!
// Path to the directory that the donation files are in, beginning with a slash.
// this is relative to the SITE_URL if http://www.example.com is the site
// and the files are in C:/wwwroot/donate
// so from the web we see http://www.example.com/donate
// would mean we use ---> define("SYS_PATH","/donate"); 
define("SYS_PATH","/donate"); // Path to the directory that the donation files are in, beginning with a slash.

// Currency information.
define("CURRENCY_CODE","USD"); // Currency code to be used by PayPal.
define("CURRENCY_CHAR","USD"); // Symbol representing your currency code.

// PayPal information. Use 'www.sandbox.paypal.com' if you wish to test with the sandbox.
define("PAYPAL_URL","www.sandbox.paypal.com"); // Only change this for sandbox testing.
define("PAYPAL_EMAIL","admin@example.com"); // The account that donations will go to.

// Mail information.
define("MAIL_SUBJECT","Your reward"); // Subject of the reward mail.
define("MAIL_BODY","Thank you for your donation! Here is your reward!"); // Mail message.

//Misc
define("ACP_USERNAME","admin"); // Username to access the ACP
define("ACP_PASSWORD","password"); // Password to access the ACP
?>