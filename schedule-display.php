<?php
//displayschedule

//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Schedule';
$linkpage = 'schedule-display.php';
$currentday = date('Y-m-j');

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
if(isset($_GET['transactionid']))
{
$transactionid = $_GET['transactionid'];
}
else{
$transactionid = '';
}
if(isset($_GET['accountid']))
{
$accountid = $_GET['accountid'];
}
else{
$accountid = '';
}
if(isset($_POST['selectedday']))
{
$selectedday = $_POST['selectedday'];
}
else{
$selectedday = date('Y-m-j');
}
$displayday = date('l F j', strtotime($selectedday));
$currenthour = "8:00";
$prevday1 = date('Y-m-j', strtotime('-1 day', strtotime($selectedday)));
$prevday1a = date('l', strtotime('-1 day', strtotime($selectedday)));
$prevday1b = date('j', strtotime('-1 day', strtotime($selectedday)));
$prevday2 = date('Y-m-j', strtotime('-2 day', strtotime($selectedday)));
$prevday2a = date('l', strtotime('-2 day', strtotime($selectedday)));
$prevday2b = date('j', strtotime('-2 day', strtotime($selectedday)));
$prevday3 = date('Y-m-j', strtotime('-3 day', strtotime($selectedday)));
$prevday3a = date('l', strtotime('-3 day', strtotime($selectedday)));
$prevday3b = date('j', strtotime('-3 day', strtotime($selectedday)));

$nextday1 = date('Y-m-j', strtotime('+1 day', strtotime($selectedday)));
$nextday1a = date('l', strtotime('+1 day', strtotime($selectedday)));
$nextday1b = date('j', strtotime('+1 day', strtotime($selectedday)));
$nextday2 = date('Y-m-j', strtotime('+2 day', strtotime($selectedday)));
$nextday2a = date('l', strtotime('+2 day', strtotime($selectedday)));
$nextday2b = date('j', strtotime('+2 day', strtotime($selectedday)));
$nextday3 = date('Y-m-j', strtotime('+3 day', strtotime($selectedday)));
$nextday3a = date('l', strtotime('+3 day', strtotime($selectedday)));
$nextday3b = date('j', strtotime('+3 day', strtotime($selectedday)));

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
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="shortcut icon" type="image/x-icon" href="images/icons/favicon.ico" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" >
<link rel="stylesheet" type="text/css" href="style/globalstyle.css" >
<link rel="stylesheet" type="text/css" href="style/schedulestyle.css" >
<title><?php echo $title; ?></title>
<link rel="stylesheet" href="http://66.170.64.56/plover/css/autocomplete.css" type="text/css" media="screen">
<script type="text/javascript">
function hoverByClass(classname,colorover,colorout="transparent"){
	var elms=document.getElementsByClassName(classname);
	for(var i=0;i<elms.length;i++){
		elms[i].onmouseover = function(){
			for(var k=0;k<elms.length;k++){
				elms[k].style.backgroundColor=colorover;
			}
		};
		elms[i].onmouseout = function(){
			for(var k=0;k<elms.length;k++){
				elms[k].style.backgroundColor=colorout;
			}
		};
	}
}
 </script>
 <script type="text/javascript" src="scripts/highlight.js"></script>
 <script type="text/javascript">
  var myHilitor; // global variable
  document.addEventListener("DOMContentLoaded", function() {
    myHilitor = new Hilitor("content");
    myHilitor.apply("");
  }, false);
</script>
</head>
<body>
<?php
if($transactionid > '1')
{
if($currentlocationid == '1')
{
echo "<div id=\"header\">Scheduling appt for</div>";
}
else{
echo "<div id=\"header2\">Scheduling appt for</div>";
}}
else{
if($currentlocationid == '1')
{
echo "<div id=\"header\">".$headernavigation."</div>";
}
else{
echo "<div id=\"header2\">".$headernavigation."</div>";
}}
//displayschedule
?>
<div id="content">
<div id="dayrecap">
<b>Today:</b><br /><a href="printtires.php"><b>Tires:</b></a> 2 <br />GOF: 3 <br /><br /><a href="vacation.php"><font color="blue">Vacation:</font></a><br /><a href="vacation.php"><font color="blue">Sick:</font></a><br /><a href=""><font color="red">Jack</font></a>
<br /><a href="vacation.php">8:00-5:00</a><br /><br /><br /><a href="deletedappointments.php"><font color="blue">Show Deleted Appts</font></a></div>

<div id="reminders"><a href="repairs.php"><img src="images/dropoffschedule.png"></a><br /><br /><a href="addreminder.php"><font color="blue">Add Reminder:</font></a><br /><a href="reminders.php"><font color="red"><b>Jared</b></font>
<br /><font color="black">Reminder</font></a><br/><br /><a href="addreminder.php"><font color="blue">Add Reminder:</font></a></div>

<div id="navigation">
<a href="schedule.php">Today</a><a href="schedule-blank.php">Service Schedule</a><a href="month.php?m=5&y=2018?transactionid=<?php echo $transactionid; ?>&accountid=<?php echo $accountid; ?>">May</a><a href="deletedschedule.php">Deleted Appointments</a><a href="schedule-blank.php">Shop Location 2</a><a href="tools.php">Tools</a></div>


<table class="gradienttable"><tr><th width="5%"></th><th width="5%"></th><th width="10%"></th><th width="10%"></th><th width="25%"></th><th width="15%"></th><th width="10%"></th><th width="10%"></th><th width="10%"></th><th width="10%"></th></tr>

<tr height="40"><th width="55" colspan="2" onclick="document.getElementById('prev3').submit();"><div style="height:100%;width:100%"><form name="changeday" id="prev3" action="schedule.php?transactionid=<?php echo $transactionid; ?>&accountid=<?php echo $accountid; ?>" method="POST"><input type="hidden" name="selectedday" value="<?php echo $prevday3; ?>"><button type="submit" class="btn-link"><?php echo $prevday3a; ?></button><br/><button type="submit" class="btn-link-big"><?php echo $prevday3b; ?></button></form></div></th>

<th width="55" onclick="document.getElementById('prev2').submit();"><?php if($currentday == $prevday2){?><div class="lightgreenstatus"><?php }else{?><div class="graystatus"><?php } ?><form name="changeday" id="prev2" action="schedule.php?transactionid=<?php echo $transactionid; ?>&accountid=<?php echo $accountid; ?>" method="POST"><input type="hidden" name="selectedday" value="<?php echo $prevday2; ?>"><button type="submit" class="btn-link"><?php echo $prevday2a; ?></button><br/><button type="submit" class="btn-link-big"><?php echo $prevday2b; ?></button></form></div></th>


<th width="55" href="#" onclick="document.getElementById('prev1').submit();"><div style="height:100%;width:100%"><form name="changeday" id="prev1" action="schedule.php?transactionid=<?php echo $transactionid; ?>&accountid=<?php echo $accountid; ?>" method="POST"><input type="hidden" name="selectedday" value="<?php echo $prevday1; ?>"><div style="height:100%;width:100%"><button type="submit" class="btn-link"><?php echo $prevday1a; ?></button><br/><button type="submit" class="btn-link-big"><?php echo $prevday1b; ?></button></form></div></th>
<th colspan="2"><center><?php if($currentday != $selectedday){?><font size="5" color="red"><?php }else{?><font size="5" color="green"><?php }?><?php echo $displayday; ?></font></center></th>

<th width="55" onclick="document.getElementById('nextday1').submit();"><div style="height:100%;width:100%"><form name="changeday" id="nextday1" action="schedule.php?transactionid=<?php echo $transactionid; ?>&accountid=<?php echo $accountid; ?>" method="POST"><input type="hidden" name="selectedday" value="<?php echo $nextday1; ?>"><button type="submit" class="btn-link"><?php echo $nextday1a; ?></button><br/><button type="submit" class="btn-link-big"><?php echo $nextday1b; ?></button></form></div></th>
<th width="55" onclick="document.getElementById('nextday2').submit();"><div style="height:100%;width:100%"><form name="changeday" id="nextday2" action="schedule.php?transactionid=<?php echo $transactionid; ?>&accountid=<?php echo $accountid; ?>" method="POST"><input type="hidden" name="selectedday" value="<?php echo $nextday2; ?>"><button type="submit" class="btn-link"><?php echo $nextday2a; ?></button><br/><button type="submit" class="btn-link-big"><?php echo $nextday2b; ?></button></form></div></th>
<th width="55" onclick="document.getElementById('nextday3').submit();"><div style="height:100%;width:100%"><form name="changeday" id="nextday3" action="schedule.php?transactionid=<?php echo $transactionid; ?>&accountid=<?php echo $accountid; ?>" method="POST"><input type="hidden" name="selectedday" value="<?php echo $nextday3; ?>"><button type="submit" class="btn-link"><?php echo $nextday3a; ?></button><br/><button type="submit" class="btn-link-big"><?php echo $nextday3b; ?></button></form></div></th>



<tr height="31"><th><?php echo $currenthour;?></th>
<td colspan="4"><form name="newappt" action="newappt.php" method="POST"><input type="hidden" name="time" value="<?php echo $selectedday." ".$currenthour; ?>"><input type="hidden" name="bay" value="1"><input type="hidden" name="transactionid" value="<?php echo $transactionid; ?>"><input type="submit" class="smallbutton" name="newappt" value="New Appointment"></form></td>

<td colspan="4"><form name="newappt" action="edit.php" method="POST"><input type="hidden" name="time" value="2018-04-17 8:00"><input type="submit" class="smallbutton" name="newappt" value="New Appointment"></form></td>
</tr>

<tr height="31"><th>8:30</th>
<th colspan="4"><div class="graystatus"><form name="editappt" action="edit.php" method="POST"><input type="hidden" name="time" value="2018-04-17 8:00"><input type="submit" class="btn-style" name="editappt" value="John Smith 2010 Ford F-150 715-888-8888">&nbsp;<img src="images/icons/schedule/oil_icon.png" width="25"></form></div></th>
<td colspan="4"><form name="newappt" action="newappt.php" method="POST"><input type="hidden" name="time" value="<?php echo $selectedday." ".$currenthour; ?>"><input type="hidden" name="bay" value="1"><input type="hidden" name="transactionid" value="<?php echo $transactionid; ?>"><input type="submit" class="smallbutton" name="newappt" value="New Appointment"></form></td>
</tr>

<tr height="31"><th>9:00</th>
<td colspan="4"><form name="newappt" action="newappt.php" method="POST"><input type="hidden" name="time" value="<?php echo $selectedday." ".$currenthour; ?>"><input type="hidden" name="bay" value="1"><input type="hidden" name="transactionid" value="<?php echo $transactionid; ?>"><input type="submit" class="smallbutton" name="newappt" value="New Appointment"></form></td>
<td colspan="4"><form name="newappt" action="newappt.php" method="POST"><input type="hidden" name="time" value="<?php echo $selectedday." ".$currenthour; ?>"><input type="hidden" name="bay" value="1"><input type="hidden" name="transactionid" value="<?php echo $transactionid; ?>"><input type="submit" class="smallbutton" name="newappt" value="New Appointment"></form></td>
</tr>

<tr height="31"><th>9:30</th>


<th colspan="4"><div class="yellowstatus"><form name="editappt" action="edit" method="POST"><input type="hidden" name="time" value="2018-04-17 8:00"><input type="submit" class="btn-style" name="editappt" value="Jordan Smith 2013 Mitsubishi Lancer 715-888-888">&nbsp;&nbsp;<img src="images/icons/schedule/tire_icon.png" width="25"></form></div></th>

<td colspan="4"><form name="newappt" action="newappt.php" method="POST"><input type="hidden" name="time" value="<?php echo $selectedday." ".$currenthour; ?>"><input type="hidden" name="bay" value="1"><input type="hidden" name="transactionid" value="<?php echo $transactionid; ?>"><input type="submit" class="smallbutton" name="newappt" value="New Appointment"></form></td>
</tr>

<tr height="31"><th>10:00</th>


<th colspan="4"><div class="orangestatus"><form name="editappt" action="edit" method="POST"><input type="hidden" name="time" value="2018-04-17 8:00"><input type="submit" class="btn-style" name="editappt" value="Jennifer Olsen 2010 Chevrolet Captiva 715-888-8888">&nbsp;<img src="images/icons/schedule/brake_icon.png" width="25"></form></div></th>

<td colspan="4"><form name="newappt" action="newappt.php" method="POST"><input type="hidden" name="time" value="<?php echo $selectedday." ".$currenthour; ?>"><input type="hidden" name="bay" value="1"><input type="hidden" name="transactionid" value="<?php echo $transactionid; ?>"><input type="submit" class="smallbutton" name="newappt" value="New Appointment"></form></td>
</tr>

<tr height="31"><th>10:30</th>


<td colspan="4"><form name="newappt" action="newappt.php" method="POST"><input type="hidden" name="time" value="<?php echo $selectedday." ".$currenthour; ?>"><input type="hidden" name="bay" value="1"><input type="hidden" name="transactionid" value="<?php echo $transactionid; ?>"><input type="submit" class="smallbutton" name="newappt" value="New Appointment"></form></td>

<td colspan="4"><form name="newappt" action="newappt.php" method="POST"><input type="hidden" name="time" value="<?php echo $selectedday." ".$currenthour; ?>"><input type="hidden" name="bay" value="1"><input type="hidden" name="transactionid" value="<?php echo $transactionid; ?>"><input type="submit" class="smallbutton" name="newappt" value="New Appointment"></form></td>
</tr>

<tr height="31"><th>11:00</th>


<td colspan="4"><form name="newappt" action="newappt.php" method="POST"><input type="hidden" name="time" value="<?php echo $selectedday." ".$currenthour; ?>"><input type="hidden" name="bay" value="1"><input type="hidden" name="transactionid" value="<?php echo $transactionid; ?>"><input type="submit" class="smallbutton" name="newappt" value="New Appointment"></form></td>

<td colspan="4"><form name="newappt" action="newappt.php" method="POST"><input type="hidden" name="time" value="<?php echo $selectedday." ".$currenthour; ?>"><input type="hidden" name="bay" value="1"><input type="hidden" name="transactionid" value="<?php echo $transactionid; ?>"><input type="submit" class="smallbutton" name="newappt" value="New Appointment"></form></td>
</tr>

<tr height="31"><th>11:30</th>


<td colspan="4"><form name="newappt" action="newappt.php" method="POST"><input type="hidden" name="time" value="<?php echo $selectedday." ".$currenthour; ?>"><input type="hidden" name="bay" value="1"><input type="hidden" name="transactionid" value="<?php echo $transactionid; ?>"><input type="submit" class="smallbutton" name="newappt" value="New Appointment"></form></td>

<td colspan="4"><form name="newappt" action="newappt.php" method="POST"><input type="hidden" name="time" value="<?php echo $selectedday." ".$currenthour; ?>"><input type="hidden" name="bay" value="1"><input type="hidden" name="transactionid" value="<?php echo $transactionid; ?>"><input type="submit" class="smallbutton" name="newappt" value="New Appointment"></form></td>
</tr>

<tr height="31"><th>12:30</th>


<td colspan="4"><form name="newappt" action="newappt.php" method="POST"><input type="hidden" name="time" value="<?php echo $selectedday." ".$currenthour; ?>"><input type="hidden" name="bay" value="1"><input type="hidden" name="transactionid" value="<?php echo $transactionid; ?>"><input type="submit" class="smallbutton" name="newappt" value="New Appointment"></form></td>

<td colspan="4"><form name="newappt" action="newappt.php" method="POST"><input type="hidden" name="time" value="<?php echo $selectedday." ".$currenthour; ?>"><input type="hidden" name="bay" value="1"><input type="hidden" name="transactionid" value="<?php echo $transactionid; ?>"><input type="submit" class="smallbutton" name="newappt" value="New Appointment"></form></td>
</tr>

<tr height="31"><th>1:00</th>


<td colspan="4"><form name="newappt" action="newappt.php" method="POST"><input type="hidden" name="time" value="<?php echo $selectedday." ".$currenthour; ?>"><input type="hidden" name="bay" value="1"><input type="hidden" name="transactionid" value="<?php echo $transactionid; ?>"><input type="submit" class="smallbutton" name="newappt" value="New Appointment"></form></td>

<td colspan="4"><form name="newappt" action="newappt.php" method="POST"><input type="hidden" name="time" value="<?php echo $selectedday." ".$currenthour; ?>"><input type="hidden" name="bay" value="1"><input type="hidden" name="transactionid" value="<?php echo $transactionid; ?>"><input type="submit" class="smallbutton" name="newappt" value="New Appointment"></form></td>
</tr>

<tr height="31"><th>1:30</th>


<td colspan="4"><form name="newappt" action="newappt.php" method="POST"><input type="hidden" name="time" value="<?php echo $selectedday." ".$currenthour; ?>"><input type="hidden" name="bay" value="1"><input type="hidden" name="transactionid" value="<?php echo $transactionid; ?>"><input type="submit" class="smallbutton" name="newappt" value="New Appointment"></form></td>

<td colspan="4"><form name="newappt" action="newappt.php" method="POST"><input type="hidden" name="time" value="<?php echo $selectedday." ".$currenthour; ?>"><input type="hidden" name="bay" value="1"><input type="hidden" name="transactionid" value="<?php echo $transactionid; ?>"><input type="submit" class="smallbutton" name="newappt" value="New Appointment"></form></td>
</tr>

<tr height="31"><th>2:00</th>


<td colspan="4"><form name="newappt" action="newappt.php" method="POST"><input type="hidden" name="time" value="<?php echo $selectedday." ".$currenthour; ?>"><input type="hidden" name="bay" value="1"><input type="hidden" name="transactionid" value="<?php echo $transactionid; ?>"><input type="submit" class="smallbutton" name="newappt" value="New Appointment"></form></td>

<td colspan="4"><form name="newappt" action="newappt.php" method="POST"><input type="hidden" name="time" value="<?php echo $selectedday." ".$currenthour; ?>"><input type="hidden" name="bay" value="1"><input type="hidden" name="transactionid" value="<?php echo $transactionid; ?>"><input type="submit" class="smallbutton" name="newappt" value="New Appointment"></form></td>
</tr>

<tr height="31"><th>2:30</th>


<td colspan="4"><form name="newappt" action="newappt.php" method="POST"><input type="hidden" name="time" value="<?php echo $selectedday." ".$currenthour; ?>"><input type="hidden" name="bay" value="1"><input type="hidden" name="transactionid" value="<?php echo $transactionid; ?>"><input type="submit" class="smallbutton" name="newappt" value="New Appointment"></form></td>

<td colspan="4"><form name="newappt" action="newappt.php" method="POST"><input type="hidden" name="time" value="<?php echo $selectedday." ".$currenthour; ?>"><input type="hidden" name="bay" value="1"><input type="hidden" name="transactionid" value="<?php echo $transactionid; ?>"><input type="submit" class="smallbutton" name="newappt" value="New Appointment"></form></td>
</tr>

<tr height="31"><th>3:00</th>


<td colspan="4"><form name="newappt" action="newappt.php" method="POST"><input type="hidden" name="time" value="<?php echo $selectedday." ".$currenthour; ?>"><input type="hidden" name="bay" value="1"><input type="hidden" name="transactionid" value="<?php echo $transactionid; ?>"><input type="submit" class="smallbutton" name="newappt" value="New Appointment"></form></td>

<td colspan="4"><form name="newappt" action="newappt.php" method="POST"><input type="hidden" name="time" value="<?php echo $selectedday." ".$currenthour; ?>"><input type="hidden" name="bay" value="1"><input type="hidden" name="transactionid" value="<?php echo $transactionid; ?>"><input type="submit" class="smallbutton" name="newappt" value="New Appointment"></form></td>
</tr>

<tr height="31"><th>3:30</th>


<td colspan="4"><form name="newappt" action="newappt.php" method="POST"><input type="hidden" name="time" value="<?php echo $selectedday." ".$currenthour; ?>"><input type="hidden" name="bay" value="1"><input type="hidden" name="transactionid" value="<?php echo $transactionid; ?>"><input type="submit" class="smallbutton" name="newappt" value="New Appointment"></form></td>

<td colspan="4"><form name="newappt" action="newappt.php" method="POST"><input type="hidden" name="time" value="<?php echo $selectedday." ".$currenthour; ?>"><input type="hidden" name="bay" value="1"><input type="hidden" name="transactionid" value="<?php echo $transactionid; ?>"><input type="submit" class="smallbutton" name="newappt" value="New Appointment"></form></td>
</tr>

<tr height="31"><th>4:00</th>


<td colspan="4"><form name="newappt" action="newappt.php" method="POST"><input type="hidden" name="time" value="<?php echo $selectedday." ".$currenthour; ?>"><input type="hidden" name="bay" value="1"><input type="hidden" name="transactionid" value="<?php echo $transactionid; ?>"><input type="submit" class="smallbutton" name="newappt" value="New Appointment"></form></td>

<td colspan="4"><form name="newappt" action="newappt.php" method="POST"><input type="hidden" name="time" value="<?php echo $selectedday." ".$currenthour; ?>"><input type="hidden" name="bay" value="1"><input type="hidden" name="transactionid" value="<?php echo $transactionid; ?>"><input type="submit" class="smallbutton" name="newappt" value="New Appointment"></form></td>
</tr>

<tr height="31"><th>4:30</th>


<td colspan="4"><form name="newappt" action="newappt.php" method="POST"><input type="hidden" name="time" value="<?php echo $selectedday." ".$currenthour; ?>"><input type="hidden" name="bay" value="1"><input type="hidden" name="transactionid" value="<?php echo $transactionid; ?>"><input type="submit" class="smallbutton" name="newappt" value="New Appointment"></form></td>

<td colspan="4"><form name="newappt" action="newappt.php" method="POST"><input type="hidden" name="time" value="<?php echo $selectedday." ".$currenthour; ?>"><input type="hidden" name="bay" value="1"><input type="hidden" name="transactionid" value="<?php echo $transactionid; ?>"><input type="submit" class="smallbutton" name="newappt" value="New Appointment"></form></td>
</tr>
</table> <br />
<script type="text/javascript">
hoverByClass("0811","#4fe2fc");
hoverByClass("0812","#4fe2fc");
hoverByClass("0813","#4fe2fc");
hoverByClass("0814","#4fe2fc");
hoverByClass("0911","#4fe2fc");
hoverByClass("0912","#4fe2fc");
hoverByClass("0913","#4fe2fc");
hoverByClass("0914","#4fe2fc");
hoverByClass("1011","#4fe2fc");
hoverByClass("1012","#4fe2fc");
hoverByClass("1013","#4fe2fc");
hoverByClass("1014","#4fe2fc");
hoverByClass("1111","#4fe2fc");
hoverByClass("1112","#4fe2fc");
hoverByClass("1113","#4fe2fc");
hoverByClass("1114","#4fe2fc");
hoverByClass("1211","#4fe2fc");
hoverByClass("1212","#4fe2fc");
hoverByClass("1213","#4fe2fc");
hoverByClass("1214","#4fe2fc");
hoverByClass("1311","#4fe2fc");
hoverByClass("1312","#4fe2fc");
hoverByClass("1313","#4fe2fc");
hoverByClass("1314","#4fe2fc");
hoverByClass("1411","#4fe2fc");
hoverByClass("1412","#4fe2fc");
hoverByClass("1413","#4fe2fc");
hoverByClass("1414","#4fe2fc");
hoverByClass("1511","#4fe2fc");
hoverByClass("1512","#4fe2fc");
hoverByClass("1513","#4fe2fc");
hoverByClass("1514","#4fe2fc");
hoverByClass("1611","#4fe2fc");
hoverByClass("1612","#4fe2fc");
</script>
</body></html>
