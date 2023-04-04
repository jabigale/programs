<?php
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Quick Quote';
$linkpage = 'quickquote.php';

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

/*
 * 
 //jobcode
1-Steering
2-Brakes
3-Suspension
4-Alternator - Battery
5-A/C - Heating
6-Tune-Up
7-Wheel Bearing
 */
if($linedesc = '%steering%' OR '%rack%' OR '%rod%' or '%pitman%' OR '%drag%' OR'%idler%')
{
	$jobcode = '1';
}
if($linedesc = '%brake%' OR '%caliper%' OR '%pads%' OR '%shoes%' OR '%wheel cylinder%')
{
	$jobcode = '2';
}
if($linedesc = '%shock%' OR '%strut%' OR '%ball joint%' OR '%control arm%' OR '%stabilizer%' OR '%sway%')
{
	$jobcode = '3';
}
if($linedesc = '%alternator%' OR '%battery%' OR '%volt%')
{
	$jobcode = '4';
}
if($linedesc = '%a/c%' OR '%air condition%' OR '%heat%' OR '%evaporator%')
{
	$jobcode = '5';
}
if($linedesc = '%tune up%' OR '%spark%' OR '%tune-up%')
{
	$jobcode = '6';
}
if($linedesc = '%wheel bearing%' OR '%hub%')
{
	$jobcode = '7';
}
$sth1 = $pdocxn->prepare('INSERT INTO `quickquote`(`vehiclecode`,`invoiceid`,`invtype`,`subtotal`,`date`,`vehicledesc`,`jobcode`,`jobdesc`) VALUES (:vehiclecode,:invoicenumber,:invtype,:subtotal,:date,:vehicledesc,:jobcode,:jobdesc)');
$sth1->bindParam(':vehiclecode',$vehiclecode);
$sth1->bindParam(':invoicenumber',$invoicenumber);
$sth1->bindParam(':invtype',$invtype);
$sth1->bindParam(':subtotal',$subtotal);
$sth1->bindParam(':date',$currentdate);
$sth1->bindParam(':vehicledesc',$vehicledesc);
$sth1->bindParam(':jobcode',$jobcode);
$sth1->bindParam(':jobdesc',$jobdesc);
$sth1->execute();
if($vin > '1')
{
$warningnote = "**Remember this can be for a different engine size, according to the vehicle of the selected transaction, the vehicle is a ".$vinyear." ".$vinmodel." ".$vinengine;
}
if($vin < '1')
{
$warningnote = "**Remember this can be for a different engine size, the vehicle from selected transaction does not have the VIN or engine size entered.";
}
?>