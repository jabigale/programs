<?php
//include mysql file
include_once('scripts/mysql.php');
include_once('scripts/global.php');
header("Expires: Mon, 01 Jan 2018 05:00:00 GMT");
header("Last-Modified: ".gmdate( 'D, d M Y H:i:s')." GMT");
header("Cache-Control: private, no-store, max-age=0, no-cache, must-revalidate, post-check=0, pre-check=0");
header("Pragma: no-cache");
$message = '';
if(isset($_POST['logout']))
{
session_destroy();
}
if(isset($_POST['loginsubmit'])) {
$username = $_POST['username'];
$password = $_POST['password'];
$linkpage = $_POST['pageid'];
$pagelink = pageidtoname($linkpage);
$hashed_password = password_hash($password,PASSWORD_DEFAULT);
if(empty($username) || empty($password)) {
$message = 'Please enter a username and password';
} else {
$sth1 = $pdocxn->prepare("SELECT `id`,`password` FROM `employees` WHERE `username`=:username ");
$sth1->bindParam(':username',$username);
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
	$storeid = '1';
	$locationname = 'Rapids';
$userid = $row1['id'];
$dbpassword = $row1['password'];
if(password_verify($password, $dbpassword)) {
	//start session
	session_set_cookie_params(86400,"/");
	session_start();
	$_SESSION['login'] = '1';
	$_SESSION['userid'] = $userid;
	$_SESSION['username'] = $username;
	$_SESSION['locationid'] = $storeid;
	$_SESSION['locationname'] = $locationname;
	header('location:'.$pagelink.'');
}
else{
	$message = 'Username or Password was wrong!';
}
}
}
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
echo "<div id=\"header2\">".$headernavigation2."</div>";
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
        	<form name="login" action="login.php" method="POST">
<table><tr><td>
        	<input type="textbox" name="username" placeholder="Username" autofocus>
</td></tr>
<tr><td>
	<input type="password" name="password" placeholder="Password">
</td></tr>
<tr><td>
<input type="hidden" name="loginsubmit" value="1">
	<input type="submit" class="smallquotebutton" value="Login">
</td></tr>
<tr><td>
<font color="red" size="4"><?php
echo $message;
?>
</font>
</td></tr></table>
</form></div>
</body></html>