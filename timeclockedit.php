<?php
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Time clock';
$linkpage = 'timeclockedit.php';
$totalhours = '0';

session_start();
date_default_timezone_set('America/Chicago');
$currentdate = date('Y-n-j H:i:s');
$currentday = date('Y-n-j');
$day = date('w');
$userid = '0';
$editdate = '0';
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

if(isset($_POST['editdate'])&& $_POST['editdate'] > '0')
{
$editdate = $_POST['editdate'];
}
if(isset($_POST['user'])&& $_POST['user'] > '0')
{
$userid = $_POST['user'];
}
if($userid > '0' && $editdate > '0')
{
$gethour = '1';
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
if($gethour == '1')
{
$sth1 = $pdocxn->prepare('SELECT `username` FROM `employees` WHERE `id` = :userid');
$sth1->bindParam(':userid',$userid);
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
$selectedusername = $row1['username'];
}
    ?>
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
    <html>
    <head>
    <title><?php echo $title; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" >
    <link rel="stylesheet" type="text/css" href="style/globalstyle.css" >
    <link rel="stylesheet" type="text/css" href="style/timeclockstyle.css" >
	<script type="text/javascript" src="scripts/jquery-latest.js"></script>
	<script type="text/javascript" src="scripts/script.js"></script>

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
    <div id="left">
<table class="searchtable">
    <tr><th colspan="3"><b> Add/Edit or Delete Time for <font color="red"><?php echo $selectedusername; ?> on Day <?php echo $editdate; ?></font>:</b></th></tr>
</table>
<form name="updatetime" action="confirmtimeclockedit.php" method="POST">
<table>
<tr><td>Current Punch Time:</td><td>Delete Punch:</td><td>Change Time:<br/>(enter time with am/pm<br />like 4:00 pm)</td>
<?php
$editdate1 = $editdate.'%';
$sth3 = $pdocxn->prepare('SELECT * FROM `timeclockinfo` WHERE `userid` = :userid AND `datetime` LIKE :editdate ORDER BY `datetime` ASC ');
$sth3->bindParam(':userid',$userid);
$sth3->bindParam(':editdate',$editdate1);
$sth3->execute();
while($row3 = $sth3->fetch(PDO::FETCH_ASSOC))
{
$shifthours = '0';
$status= $row3['status'];
$shiftdatetime = $row3['datetime'];
$id = $row3['id'];
$stringtime = strtotime($shiftdatetime);
$displaytime = date('g:i a',$stringtime);
$displayday = date('l, n-d',$stringtime);
$day = date('d',$stringtime);
if($status == '1')
{
$displaystatus = "<font color=\"green\">In</font> ";
}
if($status == '0')
{
$displaystatus = "<font color=\"red\">Out</font> ";
}
echo "\n<tr><td>&nbsp;&nbsp;&nbsp;".$displaystatus." ".$displaytime."&nbsp;&nbsp;&nbsp;</td>";
echo "<td><input type=\"checkbox\" name=\"id".$id."\"></td><td><input type=\"textbox\" name=\"newtime-".$id."\" placeholder=\"".$displaytime."\"></td></tr>";
echo "<tr><td><br /><br /></td></tr>";
}
?>
<tr><th colspan="3">Add New Punch to this day:</th></tr>
<tr><td colspan="3">(enter time with am/pm like 4:00 pm)</td></tr>
<tr><td colspan="2"><input type="textbox" name="newtime" placeholder="ex = 8:00 am"></td>
<td><select name="status"><option value="3">Clock In/Out</option><option value="1">In</option><option value="0">Out</option></select></td></tr>
<tr><td colspan="3"><input type="submit" class="save" value="Update Times"></td></tr>
</table></form>
    </div>
    </div>
   <script>
$(":checkbox").change(function() {
		$(this).closest("tr").toggleClass("highlighttce", this.checked);
	});
</script>
    </body></html>
    <?php
}else{
//display no user or date selected
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
<div id="left">
<table>
<form name="newuser" id="newuser" action="timeclockedit.php" method="POST">
<table class="searchtable">
<tr><th colspan="3"><b> Select User below to edit:</b></th></tr>
<tr><th>Select User:</th><td>
<div class="styled-select black rounded">
<select name="user" id="user">
        		<?php
echo "<option value=\"0\"></option>";
$sth1 = $pdocxn->prepare('SELECT * FROM `employees` WHERE `timeclock` = 1 AND `inactive` = 0 ORDER BY `username` ASC');
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
$username = $row1['username'];
$userid1 = $row1['id'];
echo "<option value=\"".$userid1."\">".$username."</option>";
}
?>
</select></div></td>
<?php
if ($editdate > '0' && $userid == '0')
{
    echo "<td><font class=\"warningfontcolorb\">Plese Select User to edit</td>";
}
?>
</tr>
<tr><th>Select Date:</th><td>
<?php
if($editdate > '0')
{
echo    "<input type=\"date\" name=\"editdate\" value=\"".$editedate."\"></td>";

}else{
?>    <input type="date" name="editdate"></td>
<?php
}
if ($userid > '0' && $editdate == '0')
{
    echo "<td><font class=\"warningfontcolorb\">Plese Select a Date</td>";
}
?></tr>
<tr><td colspan="4"><input type="hidden" name="form" value="1"><input class="smallbutton" type="submit" name="post" value="Submit"></td></tr>
</table></form>
</div>
</div>
</body></html>
<?php
}
?>