<?php
function parsestats($dbhost, $dbuser, $dbpass,$dbcselect) 
{
	mysql_connect($dbhost, $dbuser, $dbpass);
	mysql_select_db($dbcselect);
	mysql_query("SET NAMES 'utf8';");
	$query = mysql_query("SELECT Name, Race, ClassId, Gender, Level, Map FROM `characterrecord` WHERE IsOnline = 1;");
	if($query)
	{
		$results = mysql_num_rows($query);
	}
	else
	{
		$results = 0;
		echo "Error in query: ".mysql_error();
	}

	if($results > 0)
	{
		if($results == 1)
			$info = "There is 1 player online!";
		else
			$info = "There are {$results} players online!";
	}
	else
	{
		$info = "There are no players online :( Get on now!";
	}

	
	$info .= "<table>";
	$info .= "<tr><td>Name</td><td>Level</td><td>Gender</td><td>Race</td><td>Class</td></tr>";
	$gender = 'Male';
	$race = "";
	$class = "";
	while($row = mysql_fetch_array($query))
	{
		
		if( $row['Gender'] == '1')
			$gender = 'Female';
		else
			$gender = 'Male';
			
		switch ($row['Race'])
		{
			case '1':
				$race= "<img src=\"images/races/human.png\" width='18' height='18' \">";
				break;
			case '2':
				$race= "<img src=\"images/races/orc.png\"\" width='18' height='18' \">";
				break;
			case '3':
				$race= "<img src=\"images/races/dwarf.png\" width='18' height='18' \">";
				break;
			case '4':
				$race= "<img src=\"images/races/ne.png\" width='18' height='18' \">";
				break;
			case '5':
				$race= "<img src=\"images/races/undead.png\" width='18' height='18' \">";
				break;
			case '6':
				$race= "<img src=\"images/races/tauren.png\" width='18' height='18' \">";
				break;
			case '7':
				$race= "<img src=\"images/races/gnome.png\" width='18' height='18' \">";
				break;
			case '8':
				$race= "<img src=\"images/races/troll.png\" width='18' height='18' \">";
				break;
			case '10':
				$race= "<img src=\"images/races/be.png\" width='18' height='18' \">";
				break;
			case '11':
				$race= "<img src=\"images/races/human.png\" width='18' height='18' \">";
				break;
		}
		
		switch ($row['ClassId'])
		{
			case '1':
				$class= "<img src=\"images/classes/warrior.png\" width='18' height='18' \">";
				break;
			case '2':
				$class= "<img src=\"images/classes/paladin.png\" width='18' height='18' \">";
				break;
			case '3': 
				$class= "<img src=\"images/classes/hunter.png\" width='18' height='18' \">";
				break;
			case '4':
				$class= "<img src=\"images/classes/rogue.png\" width='18' height='18' \">";
				break;
			case '5':
				$class= "<img src=\"images/classes/priest.png\" width='18' height='18' \">";
				break;
			case '6':
				$class= "<img src=\"images/classes/dk.png\" width='18' height='18' \">";
				break;
			case '7':
				$class= "<img src=\"images/classes/sham.png\" width='18' height='18' \">";
				break;
			case '8':
				$class= "<img src=\"images/classes/mage.png\" width='18' height='18' \">";
				break;
			case '9':
				$class= "<img src=\"images/classes/warlock.png\" width='18' height='18' \">";
				break;
			case '11':
				$class= "<img src=\"images/classes/druid.png\" width='18' height='18' \">";
				break;
		}
		
		$info .= "<tr><td>{$row['Name']}</td><td>{$row['Level']}</td><td>{$gender}</td><td>{$race}</td><td>{$class}</td></tr>";
	}
	
	$info .= "</table>";
	return $info;
}
?>
<div id="content">
<center><h1>Realm stats</h1><br />
<p><?php echo "<a href='#'>$servername</a>" . " <br /> " . parsestats($dbhost, $dbuser, $dbpass,$dbcselect); ?></p>
</center>
</div>