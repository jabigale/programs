<?php
/*
**navigation
//quote to appointment
//change time
//deleteappt
//get vehicle status
//get display icons
//submit insert new transaction
//convert from a quote/invoice to schedule
//find service bay to displayin
//display day recap
//check and verify tires are in stock
*/

//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Schedule';
$linkpage = 'schedule2.php';
$currenttitle = "Service Schedule";
$schedule = '2';
date_default_timezone_set('America/Chicago');
$currentday = date('Y-m-d');
$currentdate = date('Y-m-d');
$currenthour = date('H');
$currentminute = date('i');

session_start();
date_default_timezone_set('America/Chicago');
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


	if(isset($_GET['id']))
	{
	$transactionid = $_GET['id'];
	}
	else{
	$transactionid = '';
	}
	if(isset($_GET['change']))
{
$change = $_GET['change'];
$displaychange = "&change=1&id=".$transactionid;
}
else{
$change = '0';
$displaychange = '';
}
if(isset($_GET['q']))
{
$invtosched = $_GET['q'];
$invoiceid = $_GET['i'];
$displaychange2 = "&q=1&i=".$invoiceid;
}
else{
$invoiceid = '0';
$invtosched = '0';
$displaychange2 = '';
}
if(isset($_GET['delete']))
{
$delete = $_GET['delete'];
	}
	else{
	$delete = '0';
	}
	if(isset($_GET['accountid']))
	{
	$accountid = $_GET['accountid'];
    $accountinput = "<input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\">";
	}
	else{
	$accountid = '';
    $accountinput = '';
	}
	if(isset($_GET['selectedday']))
	{
	$selectedday = $_GET['selectedday'];
	}
	else{
	$selectedday = date('Y-m-d');
	}
	if(isset($_GET['lc']))
	{
		$lc = $_GET['lc'];
	}
	else{$lc = '0';}
	$selectedyear = date('Y', strtotime($selectedday));
	$selectedmonth = date('m', strtotime($selectedday));
	$displaymonth = date('F', strtotime($selectedday));

	$displayday = date('l F j', strtotime($selectedday));
	$prevday1 = date('Y-m-d', strtotime('-1 day', strtotime($selectedday)));
	$prevday1a = date('D', strtotime('-1 day', strtotime($selectedday)));
	$prevday1b = date('jS', strtotime('-1 day', strtotime($selectedday)));
	$prevday2 = date('Y-m-d', strtotime('-2 day', strtotime($selectedday)));
	$prevday2a = date('D', strtotime('-2 day', strtotime($selectedday)));
	$prevday2b = date('jS', strtotime('-2 day', strtotime($selectedday)));
	$prevday3 = date('Y-m-d', strtotime('-3 day', strtotime($selectedday)));
	$prevday3a = date('D', strtotime('-3 day', strtotime($selectedday)));
	$prevday3b = date('jS', strtotime('-3 day', strtotime($selectedday)));
	
	$nextday1 = date('Y-m-d', strtotime('+1 day', strtotime($selectedday)));
	$nextday1a = date('D', strtotime('+1 day', strtotime($selectedday)));
	$nextday1b = date('jS', strtotime('+1 day', strtotime($selectedday)));
	$nextday2 = date('Y-m-d', strtotime('+2 day', strtotime($selectedday)));
	$nextday2a = date('D', strtotime('+2 day', strtotime($selectedday)));
	$nextday2b = date('jS', strtotime('+2 day', strtotime($selectedday)));
	$nextday3 = date('Y-m-d', strtotime('+3 day', strtotime($selectedday)));
	$nextday3a = date('D', strtotime('+3 day', strtotime($selectedday)));
	$nextday3b = date('jS', strtotime('+3 day', strtotime($selectedday)));
	
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
	}
if($lc > '0')
{
	setcookie($cookie3_name, $cookie3_value, time() - (3600), "/");
	$cookie3_value = $lc;
	setcookie($cookie3_name, $lc, time() + (86400 * 30), "/");
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
		$locschedule = "scheduleloc".$currentlocationid;
		$lischedule = "s".$currentlocationid."line_items";
	}
	if(!isset($_COOKIE[$cookie4_name])) {
		$currentstorename = "None Selected";
	} else {
	    $currentstorename = $_COOKIE[$cookie4_name];
	}
	if($currentid < '1' or $currentlocationid < '1')
{
$header = "Location: index.php";
header($header);
}
	if($delete == '1')
{
$sth1 = $pdocxn->prepare('UPDATE `'.$locschedule.'` SET `voiddate`=:voiddate WHERE `id` = :invoiceid');
$sth1->bindParam(':voiddate',$currentday);
$sth1->bindParam(':invoiceid',$transactionid);
$sth1->execute();
$sth2 = $pdocxn->prepare('DELETE FROM `'.$locschedule.'` WHERE `thread` = :invoiceid');
$sth2->bindParam(':invoiceid',$transactionid);
$sth2->execute();

header('location:schedule2.php?r='.$r.'');
	}
//quote to appointment
if($invtosched == '2')
{
$newdate = $_GET['newtime'];
	//submit insert new transaction

$sth3 = $pdocxn->prepare('SELECT * FROM `invoice` WHERE id = :inv');
$sth3->bindParam(':inv',$invoiceid);
$sth3->execute();
$row3 = $sth3->fetch(PDO::FETCH_ASSOC);

$accountid = $row3['accountid'];
$invuserid = $row3['userid'];
$vehicleid = $row3['vehicleid'];
$mileagein = $row3['mileagein'];
$mileageout = $row3['mileageout'];
$location = $row3['location'];
$taxgroup = $row3['taxgroup'];
$abvname = $row3['abvname'];
$abvvehicle = $row3['abvvehicle'];
//convert from a quote/invoice to schedule
$sth1 = $pdocxn->prepare('SELECT `id` FROM `'.$locschedule.'` ORDER BY `id` DESC LIMIT 1');
$sth1->execute()or die(print_r($sth1->errorInfo(), true));
$row1 = $sth1->fetch(PDO::FETCH_ASSOC);
$lastinvid = $row1['id'];
$scheduleid = $lastinvid + '1';

$typeid = '51';
$dblength = '0';
$tiremark = '0';
$oilmark = '0';
$brakemark = '0';
$shockmark = '0';
$alignmark = '0';
if($abvname < '0')
{
$getname = $pdocxn->prepare('SELECT `firstname`,`lastname`,`fullname` from `accounts` WHERE `accountid` = :accountid');
$getname->bindParam(':accountid',$accountid);
$getname->execute();
while($getnamerow = $getname->fetch(PDO::FETCH_ASSOC))
{
$databasefname = $getnamerow['firstname'];
$firstname = stripslashes($databasefname);
$databaselname = $getnamerow['lastname'];
$lastname = stripslashes($databaselname);
$databasefullname = $getnamerow['fullname'];
$fullname = stripslashes($databasefullname);
if($firstname > '0')
{
$abvname = $firstname." ".$lastname;
}else{
    $abvname = $fullname;
}}
}
if($abvvehicle < '0')
{
$getvehicle = $pdocxn->prepare('SELECT `year`,`make`,`model`,`description`,`cfdescription` from `vehicles` WHERE `id` = :vehicleid');
$getvehicle->bindParam(':vehicleid',$vehicleid);
$getvehicle->execute();
while($getvehiclerow = $getvehicle->fetch(PDO::FETCH_ASSOC))
{
$year = $getvehiclerow['year'];
$make = $getvehiclerow['make'];
$model = $getvehiclerow['model'];
if($year > '0')
{
	$abvvehicle = $year." ".$make." ".$model;
}else{
$abvvehicle = $getvehiclerow['description'];
}
if($abvvehicle < '1')
{
$abvvehicle = $getvehiclerow['cfdescription'];
}
else{
	$abvvehicle = '0';
}}}

$sth4 = $pdocxn->prepare('SELECT `qty`,`comment` FROM `line_items` WHERE `invoiceid` = :inv ORDER BY `linenumber` ASC');
$sth4->bindParam(':inv',$invoiceid);
$sth4->execute()or die(print_r($sth4->errorInfo(), true));
$linecount = $sth4->rowCount();
	while($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
{
$invqty = $row4['qty'];
$databasecomment1 = $row4['comment'];
$databasecomment = strtolower($databasecomment1);
if (strpos($databasecomment,'labor') !== false) {
$dblength = $dblength + $invqty;
}
if($alignmark == '0')
{
if (strpos($databasecomment,'alignment') !== false) {
    $alignmark = '1';
}else{
    $alignmark = '0';
}}
if($brakemark == '0')
{
if (strpos($databasecomment,'brake') !== false) {
    $brakemark = '1';
}else{
    $brakemark = '0';
}}
if($shockmark == '0')
{
if (strpos($databasecomment,'shock') !== false) {
    $shockmark = '1';
}else{
    $shockmark = '0';
}
if($shockmark == '0')
{
if (strpos($databasecomment,'strut') !== false) {
    $shockmark = '1';
}else{
    $shockmark = '0';
}}}
if($oilmark == '0')
{
if (strpos($databasecomment,'oil change') !== false) {
    $oilmark = '1';
}else{
    $oilmark = '0';
}}}
if($dblength < '1')
{
    $length = '1';
}else{
    $length = round($dblength);
}
$sth2 = $pdocxn->prepare('INSERT INTO `'.$locschedule.'`(`id`,`date`,`userid`,`type`,`location`,`creationdate`,`invoicedate`,`taxgroup`,`accountid`,`vehicleid`,`mileagein`,`mileageout`,`length`,`abvname`,`abvvehicle`,`schedule`,`tires`,`lof`,`brakes`,`shocks`,`align`) VALUES (:id,:date,:userid,:typeid,:location,:creationdate,:invoicedate,:taxgroup,:accountid,:vehicleid,:mileagein,:mileageout,:length,:abvname,:abvvehicle,:schedule,:tires,:lof,:brakes,:shocks,:align)');

/*
$sth2echo = "('INSERT INTO `".$locschedule."`(`id`,`date`,`userid`,`type`,`location`,`creationdate`,`invoicedate`,`taxgroup`,`accountid`,`vehicleid`,`mileagein`,`mileageout`,`length`,`abvname`,`abvvehicle`,`schedule`,`tires`,`lof`,`brakes`,`shocks`,`align`) VALUES ($scheduleid,$newdate,$currentid,$typeid,$location,$currentdate,$newdate,$taxgroup,$accountid,$vehicleid,$mileagein,$mileageout,$length,$abvname,$abvvehicle,$schedule,$tires,$oilmark,$brakemark,$shockmark,$alignmark)')";
echo $sth2echo;
*/
$sth2->bindParam(':id',$scheduleid);
$sth2->bindParam(':date',$newdate);
$sth2->bindParam(':userid',$currentid);
$sth2->bindParam(':typeid',$typeid);
$sth2->bindParam(':location',$location);
$sth2->bindParam(':creationdate',$currentdate);
$sth2->bindParam(':invoicedate',$newdate);
$sth2->bindParam(':taxgroup',$taxgroup);
$sth2->bindParam(':accountid',$accountid);
$sth2->bindParam(':vehicleid',$vehicleid);
$sth2->bindParam(':mileagein',$mileagein);
$sth2->bindParam(':mileageout',$mileageout);
$sth2->bindParam(':length',$length);
$sth2->bindParam(':abvname',$abvname);
$sth2->bindParam(':abvvehicle',$abvvehicle);
$sth2->bindParam(':schedule',$schedule);
$sth2->bindParam(':tires',$tires);
$sth2->bindParam(':lof',$oilmark);
$sth2->bindParam(':brakes',$brakemark);
$sth2->bindParam(':shocks',$shockmark);
$sth2->bindParam(':align',$alignmark);
$sth2->execute()or die(print_r($sth2->errorInfo(), true));




$sth4 = $pdocxn->prepare('SELECT * FROM `line_items` WHERE `invoiceid` = :inv ORDER BY `linenumber` ASC');
$sth4->bindParam(':inv',$invoiceid);
$sth4->execute()or die(print_r($sth4->errorInfo(), true));
$linecount = $sth4->rowCount();
	while($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
{
$lineitemtype = $row4['lineitem_typeid'];
$lineid = $row4['id'];
$invqty = $row4['qty'];
$invamount = $row4['amount'];
$invpartid = $row4['partid'];
$invpackageid = $row4['packageid'];
$invserviceid = $row4['serviceid'];
$databasecomment = $row4['comment'];
$fet = $row4['fet'];
$extprice = $row4['totallineamount'];
$linenumber = $row4['linenumber'];
$lineitem_subtypeid = $row4['lineitem_subtypeid'];
$lineitem_saletype = $row4['lineitem_saletype'];



$copysql = 'INSERT INTO `'.$lischedule.'`(`invoiceid`,`linenumber`,`qty`,`amount`,`partid`,`packageid`,`serviceid`,`comment`,`fet`,`totallineamount`,`lineitem_typeid`,`lineitem_subtypeid`,`lineitem_saletype`,`hours`,`basecost`) VALUES (:invoiceid,:linenumber,:qty,:amount,:partid,:packageid,:serviceid,:comment,:fet,:totallineamount,:lineitem_typeid,:lineitem_subtypeid,:lineitem_saletype,:hours,:basecost)';

$copysth = $pdocxn->prepare($copysql);
$copysth->bindParam(':invoiceid',$scheduleid);
$copysth->bindParam(':linenumber',$linenumber);
$copysth->bindParam(':qty',$invqty);
$copysth->bindParam(':amount',$invamount);
$copysth->bindParam(':partid',$invpartid);
$copysth->bindParam(':packageid',$invpackageid);
$copysth->bindParam(':serviceid',$invserviceid);
$copysth->bindParam(':comment',$databasecomment);
$copysth->bindParam(':fet',$fet);
$copysth->bindParam(':totallineamount',$extprice);
$copysth->bindParam(':lineitem_typeid',$lineitemtype);
$copysth->bindParam(':lineitem_subtypeid',$lineitem_subtypeid);
$copysth->bindParam(':lineitem_saletype',$lineitem_saletype);
$copysth->bindParam(':hours',$hours);
$copysth->bindParam(':basecost',$basecost);
$copysth->execute()or die(print_r($copysth->errorInfo(), true));
}



$reallength = '1';
$lengtherror = '0';
$currentinvoicedate = $newdate;
	//see if the following appts are filled
while($length >'1')
{
if($schedule == '1')
{
	$numberappointments = '2';
	$minute = '30';
}else{
	$numberappointments = '4';
	$minute = '60';
}
$searchtime = date('Y-m-d H:i:s', strtotime('+'.$minute.' minute', strtotime($currentinvoicedate)));
$checknoon = date('H',strtotime($searchtime));
if($checknoon == '12')
{
	$searchtime = date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($searchtime)));
}
$sql2 = 'SELECT * FROM `'.$locschedule.'` WHERE `date` = \''.$searchtime.'\' AND `schedule` = \''.$schedule.'\' AND `voiddate` IS NULL';

//$nRows = $pdocxn->query($sql2)->fetchColumn();
$sth2 = $pdocxn->prepare($sql2);
$sth2->execute();
$nrows = $sth2->rowCount();
if ($nrows == $numberappointments)
{
	$lengtherror = '1';
	break;
}
else {
		//fkmfkmfkmfkm
$fillertype = '54';
$sql2 = 'INSERT INTO `'.$locschedule.'` (`date`,`thread`,`type`,`location`,`invoicedate`,`accountid`,`schedule`) VALUES (:date,:thread,:type,:location,:invoicedate,:accountid,:schedule)';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':date',$searchtime);
$sth2->bindParam(':thread',$scheduleid);
$sth2->bindParam(':type',$fillertype);
$sth2->bindParam(':location',$currentlocationid);
$sth2->bindParam(':invoicedate',$newdate);
$sth2->bindParam(':accountid',$accountid);
$sth2->bindParam(':schedule',$schedule);
$sth2->execute()or die(print_r($sth2->errorInfo(), true));
}
	$length --;
	$reallength ++;
	$currentinvoicedate = $searchtime;
}
	//fill them with a thread filler // my only problem is this fills up the database 
	//solution delete the threads that are old -- implement
$length = $_POST['length'];
$sth1 = $pdocxn->prepare('UPDATE `'.$locschedule.'` SET `length`=:length WHERE `id` = :invoiceid');
$sth1->bindParam(':length',$reallength);
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->execute();

header('location:schedule2.php?r='.$r.'&selectedday='.$selectedday.'');
}
//change time
	if($change == '2')
{
$newdate = $_GET['newtime'];
$sth1 = $pdocxn->prepare('UPDATE `'.$locschedule.'` SET `date`=:newdate,`schedule`=:schedule,`voiddate` = NULL WHERE `id` = :invoiceid');
$sth1->bindParam(':newdate',$newdate);
$sth1->bindParam(':schedule',$schedule);
$sth1->bindParam(':invoiceid',$transactionid);
$sth1->execute();

$sth2 = $pdocxn->prepare('DELETE FROM `'.$locschedule.'` WHERE `thread` = :inv');
$sth2->bindParam(':inv',$transactionid);
$sth2->execute();
$reallength = '1';
$lengtherror = '0';
$sth3 = $pdocxn->prepare('SELECT `id`,`date`,`invoicedate`,`length`,`accountid` FROM `'.$locschedule.'` WHERE id = :inv');
$sth3->bindParam(':inv',$transactionid);
$sth3->execute();
$row3 = $sth3->fetch(PDO::FETCH_ASSOC);
$currentinvoicedate = $row3['date'];
$appointmentday = $row3['invoicedate'];
$checklength = $row3['length'];
$accountid = $row3['accountid'];
$minute = '30';
	//see if the following appts are filled
while($checklength >'1')
	{
	if($schedule == '1')
	{
		$numberappointments = '2';
		$minute = '30';
	}else{
		$numberappointments = '4';
		$minute = '60';
	}
	$searchtime = date('Y-m-d H:i:s', strtotime('+'.$minute.' minute', strtotime($currentinvoicedate)));
	$checknoon = date('H',strtotime($searchtime));
	if($checknoon == '12')
	{
		$searchtime = date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($searchtime)));
	}
	$sql2 = 'SELECT * FROM `'.$locschedule.'` WHERE `date` = \''.$searchtime.'\' AND `schedule` = \''.$schedule.'\' AND `voiddate` IS NULL';
	//$nRows = $pdocxn->query($sql2)->fetchColumn();

$sth2 = $pdocxn->prepare($sql2);
	$sth2->execute();
	$nrows = $sth2->rowCount();
	if ($nrows == $numberappointments)
	{
		$lengtherror = '1';
		break;
	}
	else {
		//fkmfkmfkmfkm
		$fillertype = '54';
$sql2 = 'INSERT INTO `'.$locschedule.'` (`date`,`thread`,`type`,`location`,`invoicedate`,`accountid`,`schedule`) VALUES (:date,:thread,:type,:location,:invoicedate,:accountid,:schedule)';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':date',$searchtime);
$sth2->bindParam(':thread',$transactionid);
$sth2->bindParam(':type',$fillertype);
$sth2->bindParam(':location',$currentlocationid);
$sth2->bindParam(':invoicedate',$appointmentday);
$sth2->bindParam(':accountid',$accountid);
$sth2->bindParam(':schedule',$schedule);
$sth2->execute()or die(print_r($sth2->errorInfo(), true));
	}
	$checklength --;
	$reallength ++;
	$currentinvoicedate = $searchtime;
	}
	//fill them with a thread filler // my only problem is this fills up the database 
	//solution delete the threads that are old -- implement
	
	$length = $_POST['length'];
	$sth1 = $pdocxn->prepare('UPDATE `'.$locschedule.'` SET `length`=:length WHERE `id` = :invoiceid');
$sth1->bindParam(':length',$reallength);
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->execute();
header('location:schedule2.php?r='.$r.'&selectedday='.$selectedday.'');
	}

	?>
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<link rel="shortcut icon" type="image/x-icon" href="images/icons/favicon.ico" />
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" >
	<link rel="stylesheet" type="text/css" href="style/globalstyle.css" >
	<link rel="stylesheet" type="text/css" href="style/schedulestyle.css" >
	<meta http-equiv="cache-control" content="no-cache, must-revalidate, post-check=0, pre-check=0" />
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="-1">
<meta http-equiv="CACHE-CONTROL" content="NO-CACHE">
  <meta http-equiv="refresh" content="10;">
	<title><?php echo $currenttitle; ?></title>
	</head>
	<body>
	<?php
	if($transactionid > '1')
	{
	if($currentlocationid == '1')
	{
	echo "<div id=\"header\">Scheduling appt for ".$customername."</div>";
	}
	else{
	echo "<div id=\"header2\">Scheduling appt for ".$customername."</div>";
	}}
	else{
	if($currentlocationid == '1')
	{
	echo "<div id=\"header\">".$headernavigation."</div>";
	}
	else{
	echo "<div id=\"header2\">".$headernavigation."</div>";
	}}
	?>
	<div id="content">
	<div id="navigation">
	<a href="schedule2.php?r=<?php echo $r; ?>&">Today</a>
<?php
if($change == '1')
{
	?>
	<a href="schedule.php?r=<?php echo $r; ?>&selectedday=<?php echo $selectedday; ?>&id=<?php echo $transactionid; ?>&change=1">Tire Schedule</a>
<?php
}else{
?>
<a href="schedule.php?r=<?php echo $r; ?>&selectedday=<?php echo $selectedday; ?>">Tire Schedule</a>
<?php
}
?>
<a href="month.php?m=<?php echo $selectedmonth."&y=".$selectedyear."&id=".$transactionid."&accountid=".$accountid."&change=".$change; ?>"><?php echo $displaymonth; ?></a><a href="deletedappointments.php">Deleted Appointments</a><a href="recentappointments.php">Last 30 Appointments</a></div>
	<table class="gradienttable"><tr>
	<tr><th width="5%"></th><th width="6%"></th><th width="12%"></th><th width="8%"></th><th width="3%"></th><th width="20%"></th><th width="13%"></th><th width="11%"></th><th width="11%"></th><th width="11%"></th></tr>
	
	<tr height="40"><th colspan="2" onclick="document.getElementById('prev3').submit();" class="graystatus"><a href="schedule2.php?r=<?php echo $r; ?>&selectedday=<?php echo $prevday3; ?>&transactionid=<?php echo $transactionid; ?>&accountid=<?php echo $accountid.$displaychange.$displaychange2; ?>"><input type="button" <?php if($currentday == $prevday3){?>class="todaybutton"<?php }else{?>class="calendarbutton"<?php } ?> value="<?php echo $prevday3a; ?>&#13;&#10;<?php echo $prevday3b; ?>"></th>
	<th onclick="document.getElementById('prev2').submit();" class="graystatus"><a href="schedule2.php?r=<?php echo $r; ?>&selectedday=<?php echo $prevday2; ?>&transactionid=<?php echo $transactionid; ?>&accountid=<?php echo $accountid.$displaychange.$displaychange2; ?>"><input type="button" <?php if($currentday == $prevday2){?>class="todaybutton"<?php }else{?>class="calendarbutton"<?php } ?> value="<?php echo $prevday2a; ?>&#13;&#10;<?php echo $prevday2b; ?>"></th>
	<th href="#" onclick="document.getElementById('prev1').submit();" class="graystatus" colspan="2"><a href="schedule2.php?r=<?php echo $r; ?>&selectedday=<?php echo $prevday1; ?>&transactionid=<?php echo $transactionid; ?>&accountid=<?php echo $accountid.$displaychange.$displaychange2; ?>"><input type="button" <?php if($currentday == $prevday1){?>class="todaybutton"<?php }else{?>class="calendarbutton"<?php } ?> value="<?php echo $prevday1a; ?>&#13;&#10;<?php echo $prevday1b; ?>"></th>
	<th colspan="4" class="graystatus"><center><?php if($currentday != $selectedday){?><font size="5" color="red"><?php }else{?><font size="5" color="green"><?php }?><?php echo $displayday; ?></font></center></th>
	<th onclick="document.getElementById('nextday1').submit();" class="graystatus"><a href="schedule2.php?r=<?php echo $r; ?>&selectedday=<?php echo $nextday1; ?>&transactionid=<?php echo $transactionid; ?>&accountid=<?php echo $accountid.$displaychange.$displaychange2; ?>"><input type="hidden" name="selectedday" value="<?php echo $nextday1; ?>"><input type="button" <?php if($currentday == $nextday1){?>class="todaybutton"<?php }else{?>class="calendarbutton"<?php } ?> value="<?php echo $nextday1a; ?>&#13;&#10;<?php echo $nextday1b; ?>"></th>
	<th onclick="document.getElementById('nextday2').submit();" class="graystatus"><a href="schedule2.php?r=<?php echo $r; ?>&selectedday=<?php echo $nextday2; ?>&transactionid=<?php echo $transactionid; ?>&accountid=<?php echo $accountid.$displaychange.$displaychange2; ?>"><input type="hidden" name="selectedday" value="<?php echo $nextday2; ?>"><input type="button" <?php if($currentday == $nextday2){?>class="todaybutton"<?php }else{?>class="calendarbutton"<?php } ?> value="<?php echo $nextday2a; ?>&#13;&#10;<?php echo $nextday2b; ?>"></th>
	<th colspan="2" onclick="document.getElementById('nextday3').submit();" class="graystatus"><a href="schedule2.php?r=<?php echo $r; ?>&selectedday=<?php echo $nextday3; ?>&transactionid=<?php echo $transactionid; ?>&accountid=<?php echo $accountid.$displaychange.$displaychange2; ?>"><input type="hidden" name="selectedday" value="<?php echo $nextday3; ?>"><input type="button" <?php if($currentday == $nextday3){?>class="todaybutton"<?php }else{?>class="calendarbutton"<?php } ?> value="<?php echo $nextday3a; ?>&#13;&#10;<?php echo $nextday3b; ?>"></th>

<tr height="30"><th class="whitestatus"><a href="" class="daysum">GOF:</a><br /><a href="" class="daysum-black">
<?php
$varidate = $selectedday.'%';
$oilsql = 'SELECT SUM(`lof`) AS `oil` FROM `'.$locschedule.'` WHERE `date` LIKE :currentdate';
$oilsth = $pdocxn->prepare($oilsql);
$oilsth->bindParam(':currentdate',$varidate);
$oilsth->execute();
while($oilrow = $oilsth->fetch(PDO::FETCH_ASSOC))
{
	echo $oilrow['oil'];
}
?>
</a></th><th class="whitestatus"><a href="printtires.php" class="daysum">Tires: </a>
<?php
$dailytire = '0';
$checkinvsql = 'SELECT `id` FROM `'.$locschedule.'` WHERE `date` LIKE :currentdate AND `voiddate` IS NULL';
	$checkinvsth = $pdocxn->prepare($checkinvsql);
	$checkinvsth->bindParam(':currentdate',$varidate);
	$checkinvsth->execute();
	while($checkinvrow = $checkinvsth->fetch(PDO::FETCH_ASSOC))
	{
		$checkinvid = $checkinvrow['id'];
$sth4 = $pdocxn->prepare('SELECT `qty` FROM `'.$lischedule.'` WHERE `invoiceid` = :inv AND `partid` > 0');
$sth4->bindParam(':inv',$checkinvid);
$sth4->execute();
	while($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
{
	$schedqty = $row4['qty'];
	$dailytire = $dailytire + $schedqty;
}}
echo '<a href="" class="daysum-black">'.$dailytire.'</a></th>';
?>
<th class="whitestatus" colspan="4"><a href="vacation.php" class="daysum">PTO:</font></a>

<?php
$ptotype = '1';
$showlocs = '0';
if($showlocs == '1')
{
$sql1 = 'SELECT `id`,`employee` FROM `ptoschedule` WHERE `startdate` < :startdate AND `enddate` > :enddate AND `approveddate` IS NOT NULL AND `location` = :siteid AND `ptotype` = :ptotype';
$sth1 = $pdocxn->prepare($sql1);
$sth1->bindParam(':siteid',$currentlocationid);
}else{
$sql1 = 'SELECT `id`,`employee`,`startdate`,`enddate` FROM `ptoschedule` WHERE `startdate` < :startdate AND `enddate` > :enddate AND `approveddate` IS NOT NULL AND `ptotype` = :ptotype';
$sth1 = $pdocxn->prepare($sql1);
}
$sth1->bindParam(':startdate',$nextday1);
$sth1->bindParam(':enddate',$selectedday);
$sth1->bindParam(':ptotype',$ptotype);
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
	$employeeid = $row1['employee'];
	$vacationid = $row1['id'];
	$startdate = $row1['startdate'];
	$enddate = $row1['enddate'];
	$startdate2 = new DateTime($startdate);
	$starthour = $startdate2->format('g:i');
	$enddate2 = new DateTime($enddate);
	$endhour = $enddate2->format('g:i');
	$displaytime = $starthour.'-'.$endhour;
	$sql2 = 'SELECT `username` FROM `employees` WHERE `id` = :employeeid';
	$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':employeeid',$employeeid);
$sth2->execute();
while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
{
	$employeename = $row2['username'];
}
echo '<a href="vacation.php?adjid='.$vacationid.'" class="daysum-red">'.$employeename.' </a><a href="vacation.php?adjid='.$vacationid.'" class="daysum-black">'.$displaytime.'</a>';
}
?>
	</th>
	<th class="whitestatus" colspan="2"><a href="vacation.php" class="daysum">Off:&nbsp;</a>
<?php
$ptotype = '3';
$showlocs = '0';

if($showlocs == '1')
{
$sql1 = 'SELECT `id`,`employee` FROM `ptoschedule` WHERE `startdate` < :startdate AND `enddate` > :enddate AND `approveddate` IS NOT NULL AND `location` = :siteid AND `ptotype` = :ptotype';
$sth1 = $pdocxn->prepare($sql1);
$sth1->bindParam(':siteid',$currentlocationid);
}else{
$sql1 = 'SELECT `id`,`employee`,`startdate`,`enddate` FROM `ptoschedule` WHERE `startdate` < :startdate AND `enddate` > :enddate AND `approveddate` IS NOT NULL AND `ptotype` = :ptotype';
$sth1 = $pdocxn->prepare($sql1);
}
$sth1->bindParam(':startdate',$nextday1);
$sth1->bindParam(':enddate',$selectedday);
$sth1->bindParam(':ptotype',$ptotype);
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
	$employeeid = $row1['employee'];
	$vacationid = $row1['id'];
	$startdate = $row1['startdate'];
	$enddate = $row1['enddate'];
	$startdate2 = new DateTime($startdate);
	$starthour = $startdate2->format('g:i');
	$enddate2 = new DateTime($enddate);
	$endhour = $enddate2->format('g:i');
	$displaytime = $starthour.'-'.$endhour;
	$sql2 = 'SELECT `username` FROM `employees` WHERE `id` = :employeeid';
	$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':employeeid',$employeeid);
$sth2->execute();
while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
{
	$employeename = $row2['username'];
}
echo '<a href="vacation.php?adjid='.$vacationid.'" class="daysum-red">'.$employeename.' </a><a href="vacation.php?adjid='.$vacationid.'" class="daysum-black">'.$displaytime.'</a>';
}
?>
	</th>
	<th class="whitestatus" colspan="4"><a href="vacation.php" class="daysum">Sick:&nbsp;</a>
<?php
$ptotype = '2';
$showlocs = '0';

if($showlocs == '1')
{
$sql1 = 'SELECT `id`,`employee` FROM `ptoschedule` WHERE `startdate` < :startdate AND `enddate` > :enddate AND `approveddate` IS NOT NULL AND `location` = :siteid AND `ptotype` = :ptotype';
$sth1 = $pdocxn->prepare($sql1);
$sth1->bindParam(':siteid',$currentlocationid);
}else{
$sql1 = 'SELECT `id`,`employee`,`startdate`,`enddate` FROM `ptoschedule` WHERE `startdate` < :startdate AND `enddate` > :enddate AND `approveddate` IS NOT NULL AND `ptotype` = :ptotype';
$sth1 = $pdocxn->prepare($sql1);
}
$sth1->bindParam(':startdate',$nextday1);
$sth1->bindParam(':enddate',$selectedday);
$sth1->bindParam(':ptotype',$ptotype);
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
	$employeeid = $row1['employee'];
	$vacationid = $row1['id'];
	$startdate = $row1['startdate'];
	$enddate = $row1['enddate'];
	$startdate2 = new DateTime($startdate);
	$starthour = $startdate2->format('g:i');
	$enddate2 = new DateTime($enddate);
	$endhour = $enddate2->format('g:i');
	$displaytime = $starthour.'-'.$endhour;
	$sql2 = 'SELECT `username` FROM `employees` WHERE `id` = :employeeid';
	$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':employeeid',$employeeid);
$sth2->execute();
while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
{
	$employeename = $row2['username'];
}
echo '<a href="vacation.php?adjid='.$vacationid.'" class="daysum-red">'.$employeename.' </a><a href="vacation.php?adjid='.$vacationid.'" class="daysum-black">'.$displaytime.'</a>';
}
?>
	</th><tr>
	
	<?php
	$sql1 = 'SELECT * FROM `schedule2hours` WHERE `inactive` = \'0\' ORDER BY `sortorder` ASC';
	$sth1 = $pdocxn->prepare($sql1);
	$sth1->execute();
	$tr = '1';
	$rn = '0';
	$bay1 = '0';
	$bay2 = '0';
	$bay3 = '0';
	$bay4 = '0';
	while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$displaytime = $row1['displaytime'];
	$hour = $row1['hour'];
	$minute = $row1['minute'];
	if($hour > '10')
	{
		$hour = '0'.$hour;
	}
	if($minute == '0')
	{
		$minute = '00';
	}
	if($currentday != $selectedday)
	{
		echo "</tr><tr height=\"70\"><th class=\"graystatus\">".$displaytime."</th>";
	}else{
	if($currenthour == $hour)
    {
            echo "</tr><tr height=\"70\"><th class=\"redstatus\">".$displaytime."</th>";
    }else{
    echo "</tr><tr height=\"70\"><th class=\"graystatus\">".$displaytime."</th>";
    }}
	//echo "</tr><tr height=\"70\"><th class=\"graystatus\">".$displaytime."</th>";
	$searchtime1a = date('Y-m-d H:i:s', strtotime('+'.$hour.' hour', strtotime($selectedday)));
	$searchtime = date('Y-m-d H:i:s', strtotime('+'.$minute.' minute', strtotime($searchtime1a)));
	//$currentdatetime = $year."-".$month."-".$day." ".$hour.":".$minute.":00";
	$sql2a = 'SELECT `id` FROM `'.$locschedule.'` WHERE `date` = \''.$searchtime.'\' AND `schedule` = \''.$schedule.'\' AND `voiddate` IS NULL';
	//$nRows = $pdocxn->query($sql2)->fetchColumn();
	$sth2a = $pdocxn->prepare($sql2a);
	$sth2a->execute();
	$newrows = $sth2a->rowCount();
	if($newrows == '4')
	{
		$newrows2 = '0';
	}else if($newrows == '3')
	{
		$newrows2 = '1';
	}else if($newrows == '2')
	{
		$newrows2 = '2';
	}else if($newrows == '1')
	{
		$newrows2 = '3';
	}else if($newrows == '0')
	{
		$newrows2 = '4';
	}
	$sql2 = 'SELECT `id`,`length`,`status`,`returntime`,`abvvehicle`,`abvname`,`phone`,`tires`,`lof`,`brakes`,`shocks`,`align`,`engine`,`baynumber` FROM `'.$locschedule.'` WHERE `date` = \''.$searchtime.'\' AND `schedule` = \''.$schedule.'\' AND `thread` IS NULL AND `voiddate` IS NULL';
	//$nRows = $pdocxn->query($sql2)->fetchColumn();
	$sth2 = $pdocxn->prepare($sql2);
	$sth2->execute();
	
	if ($newrows > 0)
	{
	while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
	{
		$scheduleid = $row2['id'];
		$length = $row2['length'];
		$baynumber = $row2['baynumber'];

		$currentstatus = $row2['status'];
		$return = $row2['returntime'];
		$abvname = $row2['abvname'];
		$abvvehicle = $row2['abvvehicle'];
		$tires = $row2['tires'];
		$lof = $row2['lof'];
		$brakes = $row2['brakes'];
		$shocks = $row2['shocks'];
		$align = $row2['align'];
		$engine = $row2['engine'];
		$baynumber = $row2['baynumber'];
		
		//get the icons
	if($brakes == '1')
	{
		$brakeicon = "<img src=\"images/icons/schedule/brake_icon.png\" width=\"25\">";
	}else{$brakeicon = '';}
	if($tires >= '1')
	{	$tireicon = "<img src=\"images/icons/schedule/tire_icon.png\" width=\"25\">";
	}else{$tireicon = '';}
	if($lof == '1')
	{	$oilicon = "<img src=\"images/icons/schedule/oil_icon.png\" width=\"25\">";
	}else{$oilicon = '';}
	if($shocks == '1')
	{	$shockicon = "<img src=\"images/icons/schedule/shock_icon.png\" width=\"25\">";
	}else{$shockicon = '';}
	if($align == '1')
	{	$alignicon = "<img src=\"images/icons/schedule/align_icon.png\" width=\"25\">";
	}else{$alignicon = '';}
	if($engine == '1')
	{	$engineicon = "<img src=\"images/icons/schedule/engine_icon.png\" width=\"25\">";
	}else{$engineicon = '';}
		$displayicons = $tireicon.$brakeicon.$oilicon.$shockicon.$alignicon.$engineicon;
	// get the status
		if($currentstatus == '1')
		{
			$statuscolor = 'graystatus';
		}
		if($currentstatus == '2')
		{
			$statuscolor = 'greenstatus';
		}
		if($currentstatus == '3')
		{
			$statuscolor = 'redstatus';
		}
		if($currentstatus == '4')
		{
			$statuscolor = 'orangestatus';
		}
		if($currentstatus == '5')
		{
			$statuscolor = 'lightgreenstatus';
		}
		if($currentstatus == '6')
		{
			$statuscolor = 'yellowstatus';
		}
		if($currentstatus == '7')
		{
			$statuscolor = 'whitestatus';
		}
		if($currentstatus == '8')
		{
			$statuscolor = 'bluestatus';
		}
		if($currentstatus == '10')
		{
			$statuscolor = 'blackstatus';
        }
        if($length > '2')
        {
            $displayinfo = "<br />".$abvname."<br /><br />".$abvvehicle."<br />&nbsp;";
        }else{
            $displayinfo = $abvname."<br />".$abvvehicle;
        }


    if($currentstatus > '10')
{
	echo "<a href=\"invoice.php?scheduleid=".$scheduleid."&loc=".$currentlocationid."\"><img src=\"images/icons/scheduledollar.png\" width=\"25\"></a>";
}else{
echo "<th colspan=\"3\" rowspan=\"$length\" class=\"".$statuscolor."\"><a href=\"appointment.php?invoiceid=".$scheduleid."&schedule=".$schedule."\" target=\"_BLANK\"><button class=\"btn-style\">".$displayinfo."</button><br />".$displayicons."</a><a href=\"schedule2.php?r=".$r."&selectedday=".$selectedday."&id=".$scheduleid."&change=1\">&nbsp;&nbsp;&nbsp;<img src=\"images/icons/schedulechange.png\" width=\"25\"></a><a href=\"schedule2.php?r=".$r."&id=".$scheduleid."&delete=1&locid=1\" onclick=\"return confirm('Delete this Appointment?')\">&nbsp;<img src=\"images/icons/scheduledelete.png\" width=\"25\"></a></th>";
}}}
while($newrows2 > '0')
{
if($change == '1' OR $invtosched == '1')
{
	if($invtosched == '1')
	{
?>
<td colspan="3"><a href="schedule2.php?r=<?php echo $r; ?>&newtime=<?php echo $searchtime; ?>&selectedday=<?php echo $selectedday?>&i=<?php echo $invoiceid; ?>&q=2" class="no-decoration"><input type="button" class="smallbutton" name="submit" value="Select"></a></td>
<?php	
}else{
?>
<td colspan="3"><a href="schedule2.php?r=<?php echo $r; ?>&newtime=<?php echo $searchtime; ?>&selectedday=<?php echo $selectedday?>&id=<?php echo $transactionid; ?>&change=2" class="no-decoration"><input type="button" class="smallbutton" name="submit" value="Select"></a></td>
<?php	
}}
else {
?>
<td colspan="3"><form name="newappt" action="appointment.php" method="POST" target="_BLANK"><input type="hidden" name="appointmentdate" value="<?php echo $searchtime; ?>"><input type="hidden" name="schedule" value="<?php echo $schedule; ?>"><input type="hidden" name="new" value="1"><?php echo $accountinput; ?><input type="submit" class="smallbutton" name="submit" value="New Appointment"></form></td>
	<?php
}
$newrows2 --;
}}
	?>
	</tr></table> <br />
	<table id="highlightTable" class="blueTable">
<thead>
<tr><th colspan="4">Inventory Checklist - Tires going on Today:</th></tr>
<tr><th>Appointment</th>
<th>Tire</th>
<th>Appointment QTY</th>
<th>QTY on hand</th></tr>
</thead>
<tbody>
	<?php
	$checkinvsql = 'SELECT `id`,`date`,`status` FROM `'.$locschedule.'` WHERE `date` LIKE :currentdate AND `voiddate` IS NULL';
	$checkinvsth = $pdocxn->prepare($checkinvsql);
	$checkinvsth->bindParam(':currentdate',$varidate);
	$checkinvsth->execute();
	while($checkinvrow = $checkinvsth->fetch(PDO::FETCH_ASSOC))
	{
		$checkinvid = $checkinvrow['id'];
		$dbdate = $checkinvrow['date'];
		$currentcheckstatus = $checkinvrow['status'];
		$invoicedate2 = new DateTime($dbdate);
		$displayinvoicedatetime = $invoicedate2->format('l, M j g:i');

$sth4 = $pdocxn->prepare('SELECT `qty`,`partid` FROM `'.$lischedule.'` WHERE `invoiceid` = :inv');
$sth4->bindParam(':inv',$checkinvid);
$sth4->execute();
	while($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
{
$checkpartid = $row4['partid'];
$schedqty = $row4['qty'];
if($checkpartid > '1')
{
	$sth1 = $pdocxn->prepare('SELECT * FROM `inventory` WHERE `id` = :cpartid LIMIT 1'); 
	//$sth1 = $pdocxn->prepare('SELECT `loc1_onhand`,`loc2_onhand`,`loc3_onhand`,`loc4_onhand`,`loc5_onhand`,`loc6_onhand`,`loc7_onhand`,`loc8_onhand`,`loc9_onhand`,`loc10_onhand` FROM `inventory` WHERE `id` = :cpartid LIMIT 1');
	$sth1->bindParam(':cpartid',$checkpartid);
	$sth1->execute();
		while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
		$articleid = $row1['part_number'];
		$type = $row1['type'];
		$recordinventory = $row1['record'];
		$brandid = $row1['manid'];
		$model = $row1['model'];
		$mileage = $row1['warranty'];
		$width = $row1['width'];
		$ratio = $row1['ratio'];
		$rim = $row1['rim'];
		$size = $width."/".$ratio." ".$rim;
		$partnumber = $row1['part_number'];
		$sw = $row1['sidewall'];
		$utgq = $row1['treadwear'];
		$fet = $row1['fet'];
		$load_index = $row1['load_index'];
		$speed = $row1['speed'];
		$ply = $row1['ply'];
		$loc1onhand = $row1['loc1_onhand'];
		$loc2onhand = $row1['loc2_onhand'];
		$loc3onhand = $row1['loc3_onhand'];
		$loc4onhand = $row1['loc4_onhand'];
		$loc5onhand = $row1['loc5_onhand'];
		$loc6onhand = $row1['loc6_onhand'];
		$loc7onhand = $row1['loc7_onhand'];
		$loc8onhand = $row1['loc8_onhand'];
		$loc9onhand = $row1['loc9_onhand'];
		$loc10onhand = $row1['loc10_onhand'];
		if($currentlocationid=='1')
		{
			$dloconhand = $row1['loc1_onhand'];
		}
		if($currentlocationid=='2')
		{
			$dloconhand = $row1['loc2_onhand'];
		}
		if($currentlocationid=='3')
		{
			$dloconhand = $row1['loc3_onhand'];
		}
		if($currentlocationid=='4')
		{
			$dloconhand = $row1['loc4_onhand'];
		}
		if($currentlocationid=='5')
		{
			$dloconhand = $row1['loc5_onhand'];
		}
		if($currentlocationid=='6')
		{
			$dloconhand = $row1['loc6_onhand'];
		}
		if($currentlocationid=='7')
		{
			$dloconhand = $row1['loc7_onhand'];
		}
		if($currentlocationid=='8')
		{
			$dloconhand = $row1['loc8_onhand'];
		}
		if($currentlocationid=='9')
		{
			$dloconhand = $row1['loc9_onhand'];
		}
		if($currentlocationid=='10')
		{
			$dloconhand = $row1['loc10_onhand'];
		}
		$sql2= "SELECT `brand` FROM `tire_manufacturers` WHERE `id` = '$brandid'";
$query2 = mysqli_query($sqlicxn,$sql2);
while ($row2 = mysqli_fetch_assoc($query2))
	{
	$brand = $row2['brand'];
	$description = $articleid.", ".$size." ".$brand." ".$model." ".$load_index.$speed." ".$displayply;
	}
	if($currentcheckstatus == '10')
	{
		echo '<tr><td class="blackstatus"><a href="appointment.php?invoiceid='.$checkinvid.'">'.$displayinvoicedatetime.'</a></td><td class="blackstatus"><a href="appointment.php?invoiceid='.$checkinvid.'">'.$description.'</a></td><td class="blackstatus">'.$schedqty.'</td><td class="blackstatus">'.$dloconhand.'</td></tr>';
	}else{
	if($dloconhand < $schedqty)
	{
		echo '<tr><td class="redstatus"><a href="appointment.php?invoiceid='.$checkinvid.'">'.$displayinvoicedatetime.'</a></td><td class="redstatus"><a href="appointment.php?invoiceid='.$checkinvid.'">'.$description.'</a></td><td class="redstatus">'.$schedqty.'</td><td class="redstatus">'.$dloconhand.'</td></tr>';
	}else{
		echo '<tr><td><a href="appointment.php?invoiceid='.$checkinvid.'">'.$displayinvoicedatetime.'</a></td><td><a href="appointment.php?invoiceid='.$checkinvid.'">'.$description.'</a></td><td>'.$schedqty.'</td><td>'.$dloconhand.'</td></tr>';
	}}}}}}
		?>
	</table>
	</body></html>

