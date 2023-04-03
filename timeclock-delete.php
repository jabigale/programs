<?php
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Time clock';
$linkpage = 'timeclock-delete.php';
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


if(isset($_GET['userid']))
{
$userid = $_GET['userid'];
}
else
{
$userid = '0';
}
if(isset($_POST['form']))
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
header('Location: timeclock.php');
}
if(isset($_POST['delete']))
{
$delete = $_POST['delete'];
$punchid = $_POST['id'];
if($delete =='1')
{
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" >
<link rel="stylesheet" type="text/css" href="style/timeclockstyle.css" >

</head>
<body>
    <div id="header"><?php echo $headernavigation; ?></div>
        <div id="content">
 <div id="left">
<?php
$sth3 = $pdocxn->prepare('SELECT * FROM `timeclockinfo` WHERE id = :id ');
$sth3->bindParam(':id',$punchid);
$sth3->execute();
$day1a = '1';
while($row3 = $sth3->fetch(PDO::FETCH_ASSOC))
{
$shiftdatetime = $row3['datetime'];
$status = $row3['status'];
$userid = $row3['userid'];
if($status == '1')
{
$displaystatus = "<font color=\"green\">In</font> ";
}
if($status == '0')
{
$displaystatus = "<font color=\"red\">Out</font> ";
}
$stringtime = strtotime($shiftdatetime);
$displaytime = date('g:i a',$stringtime);
$displayday = date('l, n-d',$stringtime);

$sth1 = $pdocxn->prepare('SELECT `username` FROM `employees` WHERE id = :userid');
$sth1->bindParam(':userid',$userid);
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
$username = $row1['username'];
}

 echo "<form name=\"confirmdeleteform\" method=\"post\" id=\"confirmdelete\" action=\"timeclock-delete.php\"><table><tr><th>Are you sure you would like to delete this time?</th></tr><tr><th>".$displaystatus." ".$displaytime." ".$username."</th></tr><tr>";
}
echo "\n<td><input type=\"hidden\" name=\"confirmdelete\" value=\"1\"><input type=\"hidden\" name=\"id\" value=\"".$punchid."\"><input type=\"submit\" class=\"smallbutton\" alt=\"yes\" value=\"Yes\" name=\"submit\">";

$day1a = '0';

?>
</td></tr></form></table></div>
</div>
</body></html>
<?php
}}
if(isset($_POST['confirmdelete']))
{
$punchid = $_POST['id'];
$sql4 = "DELETE FROM `timeclockinfo` WHERE `id` = :punchid";
$sth4 = $pdocxn->prepare($sql4);
$sth4->bindParam(':punchid',$punchid);
$sth4->execute();
header('Location:timeclock.php');
}
?>