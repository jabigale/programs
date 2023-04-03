<?php
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Accounting';
$linkpage = 'accounting.php';

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
<script type="text/javascript" src="scripts/jquery-1.9.1.min.js"></script>
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
<table><tr><td><a href="transactions.php" onmouseover="popup('Time Clock')" ><img src="images/buttons/index/clock.png" style="border-style: none;" onmouseover="this.src='images/buttons/index/clock2.png'" onmouseout="this.src = 'images/buttons/index/clock.png'" width="100"></a></td><td><a href="reports.php" onmouseover="popup('reports')"><img src="images/buttons/index/reports.png" style="border-style: none;" onmouseover="this.src='images/buttons/index/reports2.png'" onmouseout="this.src='images/buttons/index/reports.png'" width="100"></a></td>
<td><a href="accounting.php" onmouseover="popup('accounting')" ><img src="images/buttons/index/accounting.png" style="border-style: none;" onmouseover="this.src='images/buttons/index/accounting2.png'" onmouseout="this.src='images/buttons/index/accounting.png'" width="100"></a></td><td><a href="settings.php" onmouseover="popup('settings')" ><img src="images/buttons/index/settings.png" style="border-style: none;" onmouseover="this.src='images/buttons/index/settings2.png'" onmouseout="this.src='images/buttons/index/settings.png'" width="100"></a></td></tr></table>

</div>
</body></html>