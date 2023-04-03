<?php
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Time clock';
$linkpage = 'timeclockadd.php';
$totalhours = '0';

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


$userid = $_POST['userid'];

if(isset($_POST['form']))
{
$entereddate = $_POST['date'];
echo $entereddate;
$location = "1";
/*
$sth2 = $pdocxn->prepare('INSERT INTO `timeclockinfo`(`userid`,`status`,`location`,`datetime`) VALUES (:userid,:status,:location,:entereddate)');
$sth2->bindParam(':userid',$userid);
$sth2->bindParam(':status',$status);
$sth2->bindParam(':location',$location);
$sth2->bindParam(':entereddate',$entereddate);
$sth2->execute();
$header = "Location: timeclock.php";
header($header);
*/
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" >
<link rel="stylesheet" type="text/css" href="style/globalstyle.css" >
<link rel="stylesheet" type="text/css" href="style/timeclockstyle.css" >
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
<div id="right">
<form name="newuser" id="newuser" action="timeclockadd.php" method="post"><table class="searchtable"><tr><th>Select User:</th><td>
<div class="styled-select black rounded">
<select name="user" id="user">
        		<?php

echo "<option value=\"0\"></option>";

$sth1 = $pdocxn->prepare('SELECT * FROM `employees` WHERE timeclock = 1 AND inactive = 0 ORDER BY username ASC');
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
$username = $row1['username'];
$userid = $row1['id'];
echo "<option value=\"".$userid."\">".$username."</option>";
}
?>
</select></div></td></tr>
<tr><th>Date:</th><td><input id="date" type="date" name="date" onchange="form.submit()"></td></tr>
<tr><td colspan="4"><input type="hidden" name="form" value="1"><input type="submit" name="post" value="Submit"></td></tr>
</table></form>
</div>
 <div id="left">
<table class="searchtable"><tr><th>User:</th><th>Status:</th><th>Date Time:</th><th>Regular Hours:</th><th>Overtime:</th><td>
<?php
$sth1 = $pdocxn->prepare('SELECT * FROM `employees` WHERE timeclock = 1 AND inactive = 0 ORDER BY username ASC');
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
$totalhours = '0';
$shifthours = '0';
$outtime = '0';
$intime = '0';
$username = $row1['username'];
$userid = $row1['id'];
$sth2 = $pdocxn->prepare('SELECT * FROM `timeclockstatus` WHERE userid = :userid');
$sth2->bindParam(':userid',$userid);
$sth2->execute();

while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
{
$totalhours = '0';
$shifthours2 = '0';
$outtime = '0';
$intime = '0';
$displaytotalhours = '0.00';

$status= $row2['status'];
$datetime = $row2['datetime'];
$displaydatetime = date('D F jS g:i a',strtotime($datetime));
if($status =='1')
{
$displaystatus = "<font color=\"green\">in</font>";
}
else
{
$displaystatus = "<font color=\"red\">out</font>";
}

$sth3 = $pdocxn->prepare('SELECT * FROM `timeclockinfo` WHERE userid = :userid AND datetime > :datetime1 AND datetime < :datetime2 ORDER BY datetime ASC ');
$sth3->bindParam(':userid',$userid);
$sth3->bindParam(':datetime1',$datetime1);
$sth3->bindParam(':datetime2',$datetime2);
$sth3->execute();

while($row3 = $sth3->fetch(PDO::FETCH_ASSOC))
{
$shifthours = '0';

$status= $row3['status'];
$shiftdatetime = $row3['datetime'];
if($status == '1')
{
$intime = strtotime($shiftdatetime);
$outtime = '0';
}
if($status == '0')
{
$outtime = strtotime($shiftdatetime);
}
if($outtime > '0')
{
$shifthours = $outtime - $intime;
$shifthours2 = $shifthours/'3600';
$displayshifthours = number_format((float)$shifthours2, 2, '.', '');
$totalhours = $totalhours + $shifthours2;
$displaytotalhours = number_format((float)$totalhours, 2, '.', '');
}
}
echo "<tr><td>".$username."</td><td>".$displaystatus."</td><td>".$displaydatetime."</td><td>".$displaytotalhours."</td></tr>";
}}
?>
</table></div>
</div>
</body></html>