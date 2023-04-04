<?php
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'New Sidewall';
$linkpage = 'newsidewall.php';
$changecustomer = '0';

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
if(isset($_POST['submit']))
{
$code = $_POST['code'];
$description = $_POST['description'];

$sth1 = $pdocxn->prepare('INSERT INTO `inventory-sidewall` (`description`,`code`) VALUES (:description,:code)');
$sth1->bindParam(':description',$description);
$sth1->bindParam(':code',$code);
$sth1->execute();
header('Location: settings-loadrange.php');

}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" >
<link rel="stylesheet" type="text/css" href="style/globalstyle.css" >
<link rel="stylesheet" type="text/css" href="style/settingsstyle.css" >
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
        <div id="content">
<table><tr><td><a href="settings-store.php">Store Customization</a></td><td><a href="settings-employee.php">Employee Settings</a></td>
<td><a href="settings-inventory.php">Inventory Settings</a></td><td><a href="settings-site.php">Site Customization</a></td></tr>
<form name="newsidewall" action="newsidewall.php" method="post">
        	<table class="searchtable"><tr><th colspan="2">Add New Sidewall</th></tr></table><table class="searchtable">
<tr><td>Sidewall Code</td><td><input type="textbox" name="code" placeholder="sidewall code"</td></tr>
<tr><td>Sidewall Description</td><td><textarea name="description" placeholder="sidewall description"></textarea></td></tr>
<tr><td></td><td><input type="hidden" name="submit" value="submit"><input type="image" src="images/buttons/submit.png" alt="updateservice" name="submit"></td></tr>
       	</table></form>

<br /><br />
<table><tr><th>Current Sidewall Options:</th></tr></table><table>
        			<?php
        			$sql2 = "SELECT * FROM `inventory_sidewall` WHERE `inactive`='0' ORDER BY `description` ASC";
        			$tb2 = '1';
					$query2 = mysql_query($sql2);
	while ($row2 = mysql_fetch_assoc($query2))
	{
	$description = $row2['description'];
	$code = $row2['code'];
	$typeid = $row2['id'];
	if($tb2 % '7')
	{
				echo "<td><a href=\"service-sidewall.php?id=".$typeid."\">".$code."</a></td>\n";
	}
	else {
		echo "</tr><tr><td><a href=\"service-sidewall.php?id=".$typeid."\">".$code."</a></td>\n";
	}
	$tb2 ++;
	}
?>
</table>

        </form>
</div>
</body>
</html>
