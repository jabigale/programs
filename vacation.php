<?php
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Vacation';
$linkpage = 'vacation.php';
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
if(isset($_POST['updatesubmit'])) {
	$employeeid = $_POST['employeeid'];
	$startdate = $_POST['startdate'];
	$starthour = $_POST['starthour'];
	$enddate = $_POST['enddate'];
	$endhour = $_POST['endhour'];
	$type = $_POST['type'];
	$adjid = $_POST['adjid'];
	$startdatetime = $startdate.' '.$starthour;
	$enddatetime = $enddate.' '.$endhour;
	echo $startdatetime.'<br />';
	if(empty($employeeid) || empty($startdate) || empty($enddate) || empty($type)) {
	$message = 'All fields are required<br />';
	} else {
	
	$sql1 = 'UPDATE `ptoschedule` SET `employee`=:employee,`startdate`=:startdate,`enddate`=:enddate,`location`=:siteid,`ptotype`=:ptotype,`daterequested`=:daterequested,`approveddate`=:approveddate WHERE `id` = :adjid';

	//$sql1 = 'INSERT INTO `ptoschedule`(`employee`,`startdate`,`enddate`,`location`,`ptotype`,`daterequested`) VALUES ('.$employeeid.','.$startdatetime.','..','..','..','..')';
	
	$sth1 = $pdocxn->prepare($sql1);
	$sth1->bindParam(':employee',$employeeid);
	$sth1->bindParam(':startdate',$startdatetime);
	$sth1->bindParam(':enddate',$enddatetime);
	$sth1->bindParam(':siteid',$currentlocationid);
	$sth1->bindParam(':ptotype',$type);
	$sth1->bindParam(':daterequested',$currentday);
	$sth1->bindParam(':approveddate',$currentday);
	$sth1->bindparam(':adjid',$adjid);
	$sth1->execute();
	$message = 'Vacation was updated<br />';
	}
	}
if(isset($_POST['submit'])) {
$employeeid = $_POST['employeeid'];
$startdate = $_POST['startdate'];
$starthour = $_POST['starthour'];
$enddate = $_POST['enddate'];
$endhour = $_POST['endhour'];
$type = $_POST['type'];
$startdatetime = $startdate.' '.$starthour;
$enddatetime = $enddate.' '.$endhour;
if(empty($employeeid) || empty($startdate) || empty($enddate) || empty($type)) {
$message = 'All fields are required<br />';
} else {

$sql1 = 'INSERT INTO `ptoschedule`(`employee`,`startdate`,`enddate`,`location`,`ptotype`,`daterequested`,`approveddate`) VALUES (:employee,:startdate,:enddate,:siteid,:ptotype,:daterequested,:approveddate)';
//$sql1 = 'INSERT INTO `ptoschedule`(`employee`,`startdate`,`enddate`,`location`,`ptotype`,`daterequested`) VALUES ('.$employeeid.','.$startdatetime.','..','..','..','..')';

$sth1 = $pdocxn->prepare($sql1);
$sth1->bindParam(':employee',$employeeid);
$sth1->bindParam(':startdate',$startdatetime);
$sth1->bindParam(':enddate',$enddatetime);
$sth1->bindParam(':siteid',$currentlocationid);
$sth1->bindParam(':ptotype',$type);
$sth1->bindParam(':daterequested',$currentday);
$sth1->bindParam(':approveddate',$currentday);
$sth1->execute();
$message = 'Vacation was added<br />';
}
}

if(isset($_POST['form']))
{
setcookie($cookie1_name, $cookie1_value, time() - (3600), "/");
setcookie($cookie2_name, $cookie2_value, time() - (3600), "/");
setcookie($cookie3_name, $cookie3_value, time() - (3600), "/");
setcookie($cookie4_name, $cookie4_value, time() - (3600), "/");
$userid = $_POST['user'];
$locationid = $_POST['location'];
$sth1 = $pdocxn->prepare('SELECT `username` FROM `employees` WHERE `id` = :userid');
$sth1->bindParam(':userid',$userid);
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
$username = $row1['username'];
}
$sth2 = $pdocxn->prepare('SELECT `storename` FROM `locations` WHERE `id` = :locationid');
$sth2->bindParam(':locationid',$locationid);
$sth2->execute();
while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
{
$storename = $row2['storename'];
}
$cookie1_value = $userid;
$cookie2_value = $username;
$cookie3_value = $locationid;
$cookie4_value = $storename;
$cookie5_value = $_POST['password'];
setcookie($cookie1_name, $cookie1_value, time() + (86400 * 30), "/");
setcookie($cookie2_name, $cookie2_value, time() + (86400 * 30), "/");
setcookie($cookie3_name, $cookie3_value, time() + (86400 * 30), "/");
setcookie($cookie4_name, $cookie4_value, time() + (86400 * 30), "/");
$header = "Location: index.php";
header($header);
}
if(!isset($_COOKIE[$cookie1_name])) {
	$currentid = "0";
} else {
    $currentid = $_COOKIE[$cookie1_name];
}
if(!isset($_COOKIE[$cookie2_name])) {
	$currentusername = "None Selected";
} else {
    $currentusername = $_COOKIE[$cookie2_name];
}
if(!isset($_COOKIE[$cookie3_name])) {
	$currentlocationid = "0";
} else {
    $currentlocationid = $_COOKIE[$cookie3_name];
}
if(!isset($_COOKIE[$cookie4_name])) {
	$currentstorename = "None Selected";
} else {
    $currentstorename = $_COOKIE[$cookie4_name];
}
if(isset($_GET['adjid']))
{
	$adjid = $_GET['adjid'];

	if(isset($_GET['delete']))
	{
		//delete the pto
		$delete = $_GET['delete'];
		if($delete =='1')
		{
		$sql1 = 'DELETE FROM `ptoschedule` WHERE `id` = :adjid';
		$sth1 = $pdocxn->prepare($sql1);
		$sth1->bindParam(':adjid',$adjid);
		$sth1->execute();
		header('location:schedule.php');
	}}
	


	$sql1 = 'SELECT * FROM `ptoschedule` WHERE `id` = :adjid';
	$sth1 = $pdocxn->prepare($sql1);
	$sth1->bindParam(':adjid',$adjid);
	$sth1->execute();
	while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
		$employeeid = $row1['employee'];
		$vacationid = $row1['id'];
		$startdate = $row1['startdate'];
		$enddate = $row1['enddate'];
		$ptotype = $row1['ptotype'];
		$startdate2 = new DateTime($startdate);
		$startdate = $startdate2->format('Y-m-d');
		$starthourvalue = $startdate2->format('H:i');
		$starthourdisplay = $startdate2->format('g:i');
		$enddate2 = new DateTime($enddate);
		$enddate = $enddate2->format('Y-m-d');
		$endhourdisplay = $enddate2->format('g:i');
		$endhourvalue = $enddate2->format('H:i');
	}
?>
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
	<html>
	<head>
	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-117796361-1"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());
	
	  gtag('config', 'UA-117796361-1');
	</script>
	<title><?php echo $title; ?></title>
	<link rel="shortcut icon" type="image/x-icon" href="images/icons/favicon.ico" />
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
	<div id="selecteduser"><form name="current1" action="index.php" method="POST"><table id="floatleft"><tr><td class="currentuser">Current User:</td><td class="currentitem"><div class="styled-select black rounded">
	<select name="user" onchange="form.submit()">
					<?php
					if($currentid > '0')
					{
						echo "<option value=\"$currentid\">$currentusername</option>";
					}
	else {
	echo "<option value=\"0\"></option>";
	}
	$sth1 = $pdocxn->prepare('SELECT * FROM `employees` WHERE sales = 1 AND inactive = 0 ORDER BY username ASC');
	$sth1->execute();
	while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$username = $row1['username'];
	$userid = $row1['id'];
	echo "<option value=\"".$userid."\">".$username."</option>";
	}
	?>
				</select>
	</div></td><td class="currentstore">Current Store:</td><td class="currentitem"><div class="styled-select black rounded"><select name="location" onchange="form.submit()"><?php
	if($currentid > '0')
					{
					echo "<option value=\"$currentlocationid\">$currentstorename</option>";
					}
	else {
	echo "<option value=\"0\"></option>";
	}
	$sth2 = $pdocxn->prepare('SELECT `storename`,`id`,`storenum` FROM `locations` ORDER BY storename ASC');
	$sth2->execute();
	while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
	{
	$storename = $row2['storename'];
	$locationid = $row2['id'];
	$storenum = $row2['storenum'];
	if($storenum == '0')
	{
		echo "<option value=\"".$locationid."\">".$storename."</option>";
	}
	else {
		echo "<option value=\"".$locationid."\">".$storename." ".$storenum."</option>";
	}
	}
	?>
	</select></div><input type="hidden" name="form" value="1"></td></tr></table></form></div>
			<div id="content">
			<font color="red" size="4"><?php
	echo $message;
	?>
	</font>
			Enter Vacation/Sick Day
				<form name="login" action="vacation.php" method="POST">
	<table><tr><td>
	<form name="newuser" id="newuser" action="timeclock.php" method="post">
	<input type="hidden" name="enterpunch"><table class="searchtable"><tr><th>Select Employee:</th><td>
	<div class="styled-select black rounded">
	<select name="employeeid">
					<?php
	$sth1 = $pdocxn->prepare('SELECT * FROM `employees` WHERE `id` = :empid');
	$sth1->bindParam(':empid',$employeeid);
	$sth1->execute();
	while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$username = $row1['username'];
	$userid = $row1['id'];
	echo "<option value=\"".$userid."\">".$username."</option>";
	}

	$sth1 = $pdocxn->prepare('SELECT * FROM `employees` WHERE inactive = 0 ORDER BY `username` ASC');
	$sth1->execute();
	while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$username = $row1['username'];
	$userid = $row1['id'];
	echo "<option value=\"".$userid."\">".$username."</option>";
	}
	?>
	</select></div>
	
	</td></tr>
	<tr><th>
	Start Date:</th><td>
		<input type="date" name="startdate" value="<?php echo $startdate; ?>">
	</td><td>
	<div class="styled-select black rounded"><select name="starthour">
	<option value="<?php echo $starthourvalue; ?>"><?php echo $starthourdisplay; ?></option>
	<?php
	$sth1 = $pdocxn->prepare('SELECT `displaytime`,`hour`,`minute` FROM `shophours` WHERE `inactive` = 0 ORDER BY `id` ASC');
	$sth1->execute();
	while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$displaytime = $row1['displaytime'];
	$hour = $row1['hour'];
	$minute = $row1['minute'];
	if($hour < '10')
	{
		$hour = '0'.$hour;
	}
	if($minute < '10')
	{
		$minute = '0'.$minute;
	}
	$hm = $hour.':'.$minute.':00';
	echo "<option value=\"".$hm."\">".$displaytime."</option>";
	
	}
	?>
	</select>
	</div></tr>
	<tr><th>
	End Date:</th><td>
		<input type="date" name="enddate" value="<?php echo $enddate; ?>">
	</td><td>
	<div class="styled-select black rounded"><select name="endhour">
	<option value="<?php echo $endhourvalue; ?>"><?php echo $endhourdisplay; ?></option>
	<?php
	$sth1 = $pdocxn->prepare('SELECT `displaytime`,`hour`,`minute` FROM `shophours` WHERE `inactive` = 0 ORDER BY `id` ASC');
	$sth1->execute();
	while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$displaytime = $row1['displaytime'];
	$hour = $row1['hour'];
	$minute = $row1['minute'];
	if($hour < '10')
	{
		$hour = '0'.$hour;
	}
	if($minute < '10')
	{
		$minute = '0'.$minute;
	}
	$hm = $hour.':'.$minute.':00';
	echo "<option value=\"".$hm."\">".$displaytime."</option>";
	}
	?>
	</select>
	</div></tr>
	<tr><th>
	Type of PTO:</th><td>
	<div class="styled-select black rounded"><select name="type">
	<option value="1">PTO</option>
	<option value="2">Sick</option>
	<option value="3">Off</option>
	</select>
	</div></tr>
	<tr><td>
	<a href="vacation.php?adjid=<?php echo $adjid; ?>&delete=1">
		<input type="button" class="cancel" value="Delete"></a>
	</td>
		<td>
	<input type="hidden" name="updatesubmit" value="1"><input type="hidden" name="adjid" value="<?php echo $adjid; ?>">
		<input type="submit" class="smallquotebutton" value="Submit">
	</td></tr>
	<tr><td>
	</td></tr></table>
	</form></div>
	</body></html>
<?php














































}else{
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-117796361-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-117796361-1');
</script>
<title><?php echo $title; ?></title>
<link rel="shortcut icon" type="image/x-icon" href="images/icons/favicon.ico" />
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
<div id="selecteduser"><form name="current1" action="index.php" method="POST"><table id="floatleft"><tr><td class="currentuser">Current User:</td><td class="currentitem"><div class="styled-select black rounded">
<select name="user" onchange="form.submit()">
        		<?php
        		if($currentid > '0')
        		{
        			echo "<option value=\"$currentid\">$currentusername</option>";
        		}
else {
echo "<option value=\"0\"></option>";
}
$sth1 = $pdocxn->prepare('SELECT * FROM `employees` WHERE sales = 1 AND inactive = 0 ORDER BY username ASC');
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
$username = $row1['username'];
$userid = $row1['id'];
echo "<option value=\"".$userid."\">".$username."</option>";
}
?>
        	</select>
</div></td><td class="currentstore">Current Store:</td><td class="currentitem"><div class="styled-select black rounded"><select name="location" onchange="form.submit()"><?php
if($currentid > '0')
        		{
				echo "<option value=\"$currentlocationid\">$currentstorename</option>";
        		}
else {
echo "<option value=\"0\"></option>";
}
$sth2 = $pdocxn->prepare('SELECT `storename`,`id`,`storenum` FROM `locations` ORDER BY storename ASC');
$sth2->execute();
while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
{
$storename = $row2['storename'];
$locationid = $row2['id'];
$storenum = $row2['storenum'];
if($storenum == '0')
{
	echo "<option value=\"".$locationid."\">".$storename."</option>";
}
else {
	echo "<option value=\"".$locationid."\">".$storename." ".$storenum."</option>";
}
}
?>
</select></div><input type="hidden" name="form" value="1"></td></tr></table></form></div>
        <div id="content">
		<font color="red" size="4"><?php
echo $message;
?>
</font>
        Enter Vacation/Sick Day
        	<form name="login" action="vacation.php" method="POST">
<table><tr><td>
<form name="newuser" id="newuser" action="timeclock.php" method="post">
<input type="hidden" name="enterpunch"><table class="searchtable"><tr><th>Select Employee:</th><td>
<div class="styled-select black rounded">
<select name="employeeid">
        		<?php
echo "<option value=\"0\"></option>";
$sth1 = $pdocxn->prepare('SELECT * FROM `employees` WHERE inactive = 0 ORDER BY username ASC');
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
$username = $row1['username'];
$userid = $row1['id'];
echo "<option value=\"".$userid."\">".$username."</option>";
}
?>
</select></div>

</td></tr>
<tr><th>
Start Date:</th><td>
	<input type="date" name="startdate" placeholder="Start Date">
</td><td>
<div class="styled-select black rounded"><select name="starthour">
<?php
$sth1 = $pdocxn->prepare('SELECT `displaytime`,`hour`,`minute` FROM `shophours` WHERE `inactive` = 0 ORDER BY `id` ASC');
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
$displaytime = $row1['displaytime'];
$hour = $row1['hour'];
$minute = $row1['minute'];
if($hour < '10')
{
	$hour = '0'.$hour;
}
if($minute < '10')
{
	$minute = '0'.$minute;
}
$hm = $hour.':'.$minute.':00';
echo "<option value=\"".$hm."\">".$displaytime."</option>";

}
?>
</select>
</div></tr>
<tr><th>
End Date:</th><td>
	<input type="date" name="enddate" placeholder="2020-04-03">
</td><td>
<div class="styled-select black rounded"><select name="endhour">
<option value="18:00:00">6:00pm</option>
<?php
$sth1 = $pdocxn->prepare('SELECT `displaytime`,`hour`,`minute` FROM `shophours` WHERE `inactive` = 0 ORDER BY `id` ASC');
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
$displaytime = $row1['displaytime'];
$hour = $row1['hour'];
$minute = $row1['minute'];
if($hour < '10')
{
	$hour = '0'.$hour;
}
if($minute < '10')
{
	$minute = '0'.$minute;
}
$hm = $hour.':'.$minute.':00';
echo "<option value=\"".$hm."\">".$displaytime."</option>";
}
?>
</select>
</div></tr>
<tr><th>
Type of PTO:</th><td>
<div class="styled-select black rounded"><select name="type">
<option value="1">PTO</option>
<option value="2">Sick</option>
<option value="3">Off</option>
</select>
</div></tr>
<tr><td>
<input type="hidden" name="submit" value="1">
	<input type="submit" class="smallquotebutton" value="Submit">
</td></tr>
<tr><td>
</td></tr></table>
</form></div>
</body></html>
<?php
}
?>