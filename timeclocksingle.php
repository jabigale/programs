<?php
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Time clock';
$linkpage = 'timeclocksingle.php';
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
$currentdate = date('Y-n-j H:i:s');
$day = date('w');
header("Cache-Control: private, no-store, max-age=0, no-cache, must-revalidate, post-check=0, pre-check=0");
header("Pragma: no-cache");
if(isset($_GET['userid']))
{
$userid = $_GET['userid'];
$sth1 = $pdocxn->prepare('SELECT `username` FROM `employees` WHERE `id` = :userid');
$sth1->bindParam(':userid',$userid);
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
$username = $row1['username'];
}
}
else
{
$userid = '0';
}
if(isset($_GET['start']))
{
$datetime1 = $_GET['start'];
$datetime2 = $_GET['end'];
$date1 = strtotime ($datetime1);
$date2 = strtotime ($datetime2);
$datetime1display = date('F jS Y',$date1);
$datetime2display = date('F jS Y',$date2);
}
else
{
if(isset($_POST['e1']))
{
$e1 = $_POST['e1'];
$datetime1 = substr($e1, 13,10);
$datetime2 = substr($e1, 36,10);
$date1 = strtotime ($datetime1);
$date2 = strtotime ($datetime2);
$datetime1display = date('F jS Y',$date1);
$datetime2display = date('F jS Y',$date2);
}
else
{
$datetime1 = date('Y-m-d', strtotime('-'.$day.' days'));
$datetime2 = date('Y-m-d', strtotime('+'.(6-$day).' days'));
}
}
if(isset($_POST['enterpunch']))
{
$userid = $_POST['user'];
$status = $_POST['status'];
$location = "1";
$sth1 = $pdocxn->prepare('UPDATE `timeclockstatus` SET `status`=:status,`datetime`=:datetime WHERE `userid` = :userid');
$sth1->bindParam(':userid',$userid);
$sth1->bindParam(':status',$status);
$sth1->bindParam(':datetime',$currentdate);
$sth1->execute();

$sth2 = $pdocxn->prepare('INSERT INTO `timeclockinfo`(`userid`,`status`,`location`,`datetime`) VALUES (:userid,:status,:location,:currentdate)');
$sth2->bindParam(':userid',$userid);
$sth2->bindParam(':status',$status);
$sth2->bindParam(':location',$location);
$sth2->bindParam(':currentdate',$currentdate);
$sth2->execute();
$header = "Location: timeclock.php";
header($header);
}
if(isset($_POST['delete']))
{
$delete = $_POST['delete'];
$punchid = $_POST['id'];
if($delete =='1')
{
}
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" >
<link rel="stylesheet" type="text/css" href="style/globalstyle.css" >
<link rel="stylesheet" type="text/css" href="style/timeclockstyle.css" >
<link href="style/jquery-ui.min.css" rel="stylesheet">
    <link href="style/jquery.comiseo.daterangepicker.css" rel="stylesheet">
    <script src="scripts/jquery.min.js"></script>
    <script src="scripts/jquery-ui.js"></script>
    <script src="scripts/moment.min.js"></script>
    <script src="scripts/jquery.comiseo.daterangepicker.js"></script>
    <script>
        $(function() { $("#e1").daterangepicker(); });
    </script>
</head>
<body>
    <div id="header"><?php echo $headernavigation; ?></div>
        <div id="content">
<div id="right">
<form name="newuser" id="newuser" action="timeclock.php" method="post"><table class="searchtable"><tr><th>Select User:</th><td>
<div class="styled-select black rounded">
<input type="hidden" name="enterpunch">
<select name="user" id="user">
        		<?php

echo "<option value=\"0\"></option>";

?>
</select></div></td></tr>
<tr><th>Select Status:</th><td><div class="styled-select black rounded"><select name="status" onchange="form.submit()">
<option value="5"></option><option value="1">in</option><option value="0">out</option>
</select></div></td></tr>
<tr><td colspan="4"><input type="hidden" name="form" value="1"><input type="submit" name="post" value="Submit"></td></tr>
</table></form>
</div>
 <div id="left">
<table><tr><td colspan="5" align="center"><b><?php echo $username; ?></b></td></tr>
<tr><td colspan="5" align="center"><b>Dates: <font color="red"><?php echo $datetime1display; ?> - <?php echo $datetime2display; ?></font></b></td></tr>
<tr><form name="updatetime" id="udpatetime" action="timeclocksingle.php?userid=<?php echo $userid; ?>" method="POST"><td>Change Date:</td><td>
<td><input id="e1" name="e1"  onchange="form.submit()"></td>
<td><input type="hidden" name="form" value="1"></form></tr><tr><td colspan="5"><a href="addtime.php">Add Punch</a></td></tr></table>
<table class="searchtable"><tr>
<?php
$sth3 = $pdocxn->prepare('SELECT * FROM `timeclockinfo` WHERE userid = :userid AND datetime > :datetime1 AND datetime < :datetime2 ORDER BY datetime ASC ');
$sth3->bindParam(':userid',$userid);
$sth3->bindParam(':datetime1',$datetime1);
$sth3->bindParam(':datetime2',$datetime2);
$sth3->execute();
$day1a = '1';
while($row3 = $sth3->fetch(PDO::FETCH_ASSOC))
{
$shifthours = '0';

$status= $row3['status'];
$shiftdatetime = $row3['datetime'];
$id = $row3['id'];
$stringtime = strtotime($shiftdatetime);
$displaytime = date('g:i a',$stringtime);
$displayday = date('l, n-d',$stringtime);
$day1 = $day;
$day = date('d',$stringtime);
if($status == '1')
{
$intime = strtotime($shiftdatetime);
$outtime = '0';
$displaystatus = "<font color=\"green\">In</font> ";
}
if($status == '0')
{
$displaystatus = "<font color=\"red\">Out</font> ";
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
if($day1a == '1')
{
echo "<th>".$displayday."</th></tr><tr>";
}
else
{
if($day1 != $day)
{
 echo "</tr><tr><th>".$displayday."</th></tr><tr>";
}
else
{
}
}
echo "\n<td>".$displaystatus." ".$displaytime."<form name=\"delete\" method=\"post\" id=\"delete\" action=\"timeclock-delete.php\"><input type=\"hidden\" name=\"delete\" value=\"1\"><input type=\"hidden\" name=\"id\" value=\"".$id."\"><input type=\"image\" src=\"images/buttons/delete.png\" width=\"10\" name=\"submit\"></form></td>";

$day1a = '0';
}
?>
</table></div>
</div>
</body></html>