<?php
//verify user is admin
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Settings';
$linkpage = 'settings-inventory.php';

session_start();
date_default_timezone_set('America/Chicago');
$currentdate = date('Y-n-j H:i:s');
header("Expires: Mon, 01 Jan 2018 05:00:00 GMT");
header("Last-Modified: ".gmdate( 'D, d M Y H:i:s')." GMT");
header("Cache-Control: private, no-store, max-age=0, no-cache, must-revalidate, post-check=0, pre-check=0");
header("Pragma: no-cache");
if(session_status() === PHP_SESSION_ACTIVE && $_SESSION['login'] == '1')
{
	$currentid = $_SESSION[$session1_name];
	$currentusername = $_SESSION[$session2_name];
	$currentlocationid = $_SESSION[$session3_name];
	$currentstorename = $_SESSION[$session4_name];
}
else{
	$pagelink = pagenametoid($linkpage);
	$header = 'Location: login.php?refpage='.$pagenum.'';
	header($header);
}
//verify user is admin
$sth1 = $pdocxn->prepare('SELECT `admin` FROM `employees` WHERE `userid` = :userid');
$sth1->bindParam(':userid',$currentid);
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
	$adminstatus = $row1['admin'];
}
if($adminstatus != '1')
(
    header('location:settings.php');
)

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" >
<link rel="stylesheet" type="text/css" href="style/globalstyle.css" >
<link rel="stylesheet" type="text/css" href="style/indexstyle.css" >
</head>
<body>
<?php
if($currentlocationid == '1')
{
echo "<div id=\"header\">".$headernavigation."</div>";
}
else{
echo "<div id=\"header2\">".$headernavigation."</div>";
}
?>
        <div id="content"><br/><br/><br/><br/>
<table><tr><td><a href="settings-store.php">Store Customization</a></td><td><a href="settings-employee.php">Employee Settings</a></td>
<td><a href="settings-inventory.php">Inventory Settings</a></td><td><a href="settings-site.php">Site Customization</a></td></tr></table>

<form name="newuser" action="index.php" method="post">
        	<table><tr><th><center>Add or Edit Inventory Settings</center></th></tr></table>
<table><tr><td><a href="service-types.php">Inventory Types</a><td><a href="service-loadrange.php">Load Range</a></td><td><a href="service-manufacturers.php">Manufacturers</a></td><td><a href="service-services.php">Services</a></td><td><a href="service-sidewall.php">Sidewall Descriptions</a></td><td><a href="service-tires.php">Tires</a></td><td><a href="service-tireprice.php">Tire Price Matrix</a></td></tr>
        	</table>
        </form>
</div>
</body>
</html>
