<?php
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Time clock';
$linkpage = 'timeclock.php';
$totalhours = '0';

session_start();
date_default_timezone_set('America/Chicago');
$currentdate = date('Y-n-j H:i:s');
$currentday = date('Y-n-j');
$day = date('w');
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

if(isset($_POST['e1']))
{
$e1 = $_POST['e1'];
$datetime1 = substr($e1, 10,10);
$datetime2 = substr($e1, 29,10);
$date1 = strtotime ($datetime1);
$date2 = strtotime ($datetime2);
$datetime1display = date('F jS Y',$date1);
$datetime2display = date('F jS Y',$date2);
}
else
{
//$datetime1 = date('Y-m-d', strtotime('-'.$day.' days'));
//$datetime2 = date('Y-m-d', strtotime('+'.(6-$day).' days'));
$datetime1 = date('Y-m-d', strtotime('-'.$day.' days'));
$datetime2 = date('Y-m-d', strtotime('+'.(6-$day).' days'));
$datetime1display = date('F jS Y', strtotime('-'.$day.' days'));
$datetime2display = date('F jS Y', strtotime('+'.(6-$day).' days'));
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
if(isset($_POST['pastpunch']))
{
$userid = $_POST['user'];
$status = $_POST['status'];
$postdate = $_POST['date'];
$location = "1";
$sth2 = $pdocxn->prepare('INSERT INTO `timeclockinfo`(`userid`,`status`,`location`,`datetime`) VALUES (:userid,:status,:location,:currentdate)');
$sth2->bindParam(':userid',$userid);
$sth2->bindParam(':status',$status);
$sth2->bindParam(':location',$location);
$sth2->bindParam(':currentdate',$postdate);
$sth2->execute();
$header = "Location: timeclock.php";
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
<form name="newuser" id="newuser" action="timeclock.php" method="post">
<input type="hidden" name="enterpunch"><table class="searchtable"><tr><th>Select User:</th><td>
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
<tr><th>Select Status:</th><td><div class="styled-select black rounded"><select name="status" onchange="form.submit()">
<option value="5"></option><option value="1">in</option><option value="0">out</option>
</select></div></td></tr>
<tr><td colspan="4"><input type="hidden" name="form" value="1"><input class="smallbutton" type="submit" name="post" value="Submit"></td></tr>
</table></form>
<table>
<tr><td><br /><br /></td></tr>
<tr><td><a href="timeclockedit.php"><button class="smallbutton">Add/Delete Time</button></td></tr>
</table>
</div>
 <div id="left">
<table><tr><td colspan="5" align="center"><b> Showing Hours for: <font color="red"><?php echo $datetime1display; ?> - <?php echo $datetime2display; ?></font></b></td></tr>
<tr><form name="updatetime" id="udpatetime" action="timeclock.php" method="POST"><td>Start Date:</td><td>
<td><input id="e1" name="e1"  onchange="form.submit()"></td>
<!---
	<td><input type="date" name="startdate" value="<?php echo $datetime1; ?>"></td>
<td>End Date:</td><td>
<input type="date" name="enddate" value="<?php echo $datetime2; ?>"></td>
--->
<td><input type="hidden" name="form" value="1"><input type="submit" class="smallbutton" value="update"></form></tr></table>
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
echo "<tr><td><a href=\"timeclocksingle.php?userid=".$userid."&start=".$datetime1."&end=".$datetime2."\">".$username."</a></td><td>".$displaystatus."</td><td>".$displaydatetime."</td><td>".$displaytotalhours."</td></tr>";
}}
?>
</table></div>
</div>
</body></html>