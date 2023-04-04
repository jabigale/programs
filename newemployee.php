<?php
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Add Employee';
$linkpage = 'newemployee.php';

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
$name = $_POST['name'];
$username = $_POST['username'];
$hiredate = $_POST['hiredate'];
$birthday = $_POST['birthday'];
$position = $_POST['position'];
$sales = $_POST['sales'];
$timeclock = $_POST['timeclock'];


$sth1 = $pdocxn->prepare('INSERT INTO `employees` (`name`,`username`,`birthday`,`hiredate`,`position`,`sales`,`timeclock`) VALUES (:name,:username,:birthday,:hiredate,:position,:sales,:timeclock)');
$sth1->bindParam(':name',$name);
$sth1->bindParam(':username',$username);
$sth1->bindParam(':birthday',$birthday);
$sth1->bindParam(':hiredate',$hiredate);
$sth1->bindParam(':position',$position);
$sth1->bindParam(':sales',$sales);
$sth1->bindParam(':timeclock',$timeclock);
$sth1->execute();
header('Location: settings-employee.php');

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

<form name="newemployee" action="newemployee.php" method="post">
        	<table class="searchtable"><tr><th>Add New Employee</th></tr>
<tr><td>Employee Name</td><td><input type="textbox" name="name" placeholder="full name"</td></tr>
<tr><td>Username</td><td><input type="textbox" name="username" placeholder="username"></td></tr>
<tr><td>Hire Date</td><td><input type="date" name="hiredate"></td></tr>
<tr><td>Birthday</td><td><input type="date" name="birthday"></td></tr>
<tr><td>Sales</td><td><select name="sales"><option value=""></option><option value="1">Yes</option><option value="0">No</option></select></td></tr>
<tr><td>Position</td><td><select name="position"><option value=""></option><option value=Accountant">Accountant</option><option value="Manager">Manager</option><option value="Mechanic">Mechanic</option><option value="Salesman">Salesman</option></select></td></tr>
<tr><td>Time Clock</td><td><select name="timeclock"><option value=""></option><option value="1">Yes</option><option value="0">No</option></select></td></tr>
<tr><td><input type="hidden" name="submit" value="submit"><input type="image" src="images/buttons/submit.png" alt="updateservice" name="submit"></td></tr>
       	</table>
        </form>
</div>
</body>
</html>
