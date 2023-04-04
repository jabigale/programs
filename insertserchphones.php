<?php
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Search Phone';
$linkpage = 'insertsearchphones.php';

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

	//Zero Values Prior
	$firstname = '';
	$lastname='';
	$phone = '';


$sql1 = 'SELECT * FROM `accounts`';
$sth1 = $pdocxn->prepare($sql1);
$sth1->execute();
	while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$acctid = $row1['acctid'];
	$fname = $row1['firstname'];
	$lname = $row1['lastname'];
	$fullname = $fname." ".$lname;
	$phone1 = $row1['phone1'];
	$phone2 = $row1['phone2'];
	$phone3 = $row1['phone3'];
	$phone4 = $row1['phone4'];

$sphone1 = ereg_replace('[^0-9]', '', $phone1);
$sphone2 = ereg_replace('[^0-9]', '', $phone2);
$sphone3 = ereg_replace('[^0-9]', '', $phone3);
$sphone4 = ereg_replace('[^0-9]', '', $phone4);

$sql2 = 'UPDATE `accounts` SET `sphone1`=:sphone1, `sphone2`=:sphone2, `sphone3`=:sphone3, `sphone4`=:sphone4, `fullname`=:fullname WHERE `acctid` = :acctid';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':acctid',$acctid);
$sth2->bindParam(':sphone1',$sphone1);
$sth2->bindParam(':sphone2',$sphone2);
$sth2->bindParam(':sphone3',$sphone3);
$sth2->bindParam(':sphone4',$sphone4);
$sth2->bindParam(':fullname',$fullname);
$sth2->execute();

$fname = '';
$lname='';
$fullname = '';
$phone1 = '';
$phone2 = '';
$phone3 = '';
$phone4 = '';
$sphone1 = '';
$sphone2 = '';
$sphone3 = '';
$sphone4 = '';
}
?>
Done
