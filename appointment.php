<?php
/*
**navigation
//edit change salesinfo
//general submit form
//edit change mileage
//edit change po number
//edit change invoice date view
//edit void invoice
//edit change date
//edit change customer
//edit change vehicle
//edit vehicle info
//edit delete line
//edit line move
//edit line item
//search by invoice number
//search by transactionid
//get tax info
//submit insert new transaction
//copy a transaction
//split a transaction
//inventory submit
//package/service submit
//submit newcomment
//quick add
//display html - no submit
//get final info from db
//display html - submit
//record the inventory transaction in inventory_transactions
//convert from a schedule to invoice

**Invoice type ids
//1 Customer Invoice
//2 Vendor Invoice (Received Item)
//3 Service Charge
//4 Customer Quote
//5 Order
//6 Customer Payment
//7 Customer Adjustment
//8 Vendor Bill
//9 Vendor Payment
//10 Closing Entry
//11 Customer Work Order
//12 Trial Balance
//13 Vendor Adjustmentinvoice
//14 Return
//15 Vendor Credit
//16 Manually Entry
//17 Customer Credit
//18 Customer Refund
//19 Customer Beginning Balance
//20 Vendor Beginning Balance
//21 ME Deposit
//22 ME Withdrawal
//23 ME Check
//30 Inventory Adjustment (Add)
//31 Inventory Adjustment (Remove)


Default variables
 
$typeid - invoicetype
$invoiceid
$locationid
$currentlocationid
$currentid - userid
$newaccountid
$accountid 
 * 
*/
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Appointment';
$linkpage = 'appointment.php';
$changevehicle = '0';
$lsi = '0';
$sort = '0';
$split = '0';
$quicksearch = '0';
$invoicesubtotal = '0';
$accountid = '0';
$changetax = '0';

session_start();
date_default_timezone_set('America/Chicago');
$currentdate = date('Y-n-j H:i:s');
$currentday = date('Y-n-j');
$currentday2 = date('Y-m-d');
$currentyear = date('Y');
$yearywi = date('Y', strtotime('+1 Year', strtotime($currentyear)));
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

if(isset($_POST['sort']))
{
$sort = $_POST['sort'];
}
if(isset($_POST['invoicedateview']))
{
//edit change invoice date view
$displaydate = $_POST['invoicedateview'];
$displaydate2 = date("n/j/Y", strtotime($displaydate));
if(isset($_POST['invoicetypeview']))
{
$displaytype = $_POST['invoicetypeview'];
}
else {
$displaytype = '1';
}	
}else{
$displaydate = $currentday2;
$displaydate2 = date("n/j/Y", strtotime($displaydate));
if(isset($_POST['invoicetypeview']))
{
$displaytype = $_POST['invoicetypeview'];
}
else {
$displaytype = '1';
}
}
if($_POST['submit'] OR $_POST['inventorysubmit'] OR $_POST['editsubmit'] OR $_POST['changevehicle'] OR $_POST['servicesubmit'] OR $_POST['taxsubmit'] OR $_POST['invoiceid'] OR $_GET['invoiceid'] OR $_GET['scheduleid'] OR $_GET['ninvoice'] OR $_POST['quickadd'] or $_post['new'] or $_GET['confirm'])
{
//general submit form
if(isset($_POST['invoiceid']))
{
	$invoiceid = $_POST['invoiceid'];
}
if(isset($_GET['invoiceid']))
{
	$invoiceid = $_GET['invoiceid'];
}
if(isset($_GET['scheduleid']))
{
	$scheduleid = $_GET['scheduleid'];
}else{
$scheduleid = '0';
}
if(isset($_GET['noprintline']))
{
	$noprintline = $_GET['noprintline'];
	$getprint = $pdocxn->prepare('SELECT `printflag` FROM `'.$invlinetable.'` WHERE `id` = :noprintline');
$getprint->bindparam(':noprintline',$noprintline);
$getprint->execute();
while($getprintrow = $getprint->fetch(PDO::FETCH_ASSOC))
{
$currentprint = $getprintrow['printflag'];
}
if($currentprint > '0')
{
	$newprint = '0';
}else{
	$newprint = '1';
}

	$sth1 = $pdocxn->prepare('UPDATE `'.$invlinetable.'` SET `printflag`=:printflag WHERE id = :noprintline');
$sth1->bindParam(':printflag',$newprint);
$sth1->bindParam(':noprintline',$noprintline);
$sth1->execute();
header('location:appointment.php?invoiceid='.$invoiceid.'');
}

if($_POST['schedule'])
{
	$schedule = $_POST['schedule'];
	$schedulelink = "&schedule=".$schedule;
}else{
	$schedulelink = '';
}
if(isset($_POST['changesales']))
{
//edit change salesinfo
$userid = $_POST['userid'];
if($userid == '0')
{
$header = "Location: selectsalesperson.php?siteid=".$currentlocationid."&id=".$invoiceid."&alert=1";
header($header);
exit();
}
$sth1 = $pdocxn->prepare('UPDATE `'.$invtable.'` SET `userid`=:userid WHERE `id` = :invoiceid');
$sth1->bindParam(':userid',$userid);
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->execute();
}
$gettax = $pdocxn->prepare('SELECT `taxgroup` FROM `'.$invtable.'` WHERE `id` = :invoiceid');
$gettax->bindparam(':invoiceid',$invoiceid);
$gettax->execute();
while($gettaxgroup= $gettax->fetch(PDO::FETCH_ASSOC))
{
$taxgroup = $gettaxgroup['taxgroup'];
}
//get tax info
$gettax = $pdocxn->prepare('SELECT `id`,`multiply`,`description` FROM `tax_rate` WHERE `id` = :taxgroup');
$gettax->bindparam(':taxgroup',$taxgroup);
$gettax->execute();
while($gettaxraterow = $gettax->fetch(PDO::FETCH_ASSOC))
{
$taxmultiply = $gettaxraterow['multiply'];
$taxdescription = $gettaxraterow['description'];
}

if($scheduleid > '1')
{
global $changetax;
$changetax = '1';
$schedullocation = $_GET['loc'];
$locschedule = "scheduleloc".$schedullocation;
$lischedule = "s".$schedullocation."line_items";

	$sth3 = $pdocxn->prepare('SELECT * FROM `'.$locschedule.'` WHERE id = :inv');
$sth3->bindParam(':inv',$scheduleid);
$sth3->execute();
$row3 = $sth3->fetch(PDO::FETCH_ASSOC);

$accountid = $row3['accountid'];
$invuserid = $row3['userid'];
$vehicleid = $row3['vehicleid'];
$mileagein = $row3['mileagein'];
$mileageout = $row3['mileageout'];
$location = $row3['location'];
$siteid = $row3['location'];
$taxgroup = $row3['taxgroup'];
$currentstatus = $row3['status'];
$convertedid = $row3['invoiceid'];

	
if($currentstatus == '8')
{
header('location:account.php?accountid='.$accountid.'');
}else{
//convert from a schedule to invoice

if($convertedid > '0')
{
	header('location:'.$pagelink.'?invoiceid='.$convertedid.'');
}else{
$sth1 = $pdocxn->prepare('SELECT `id` FROM `'.$invtable.'` ORDER BY `id` DESC LIMIT 1');
$sth1->execute();
$row1 = $sth1->fetch(PDO::FETCH_ASSOC);
$lastinvid = $row1['id'];
$invoiceid = $lastinvid + '1';
$sth1 = $pdocxn->prepare('SELECT `invoiceid` FROM `'.$invtable.'` WHERE `invoiceid` > \'1\' AND `location` = :currentlocationid ORDER BY `invoiceid` DESC LIMIT 1');
$sth1->bindParam(':currentlocationid',$location);
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
$lastinvnumber = $row1['invoiceid'];
$invoicenumber = $lastinvnumber + '1';
}
$typeid = '1';
$sth2 = $pdocxn->prepare('INSERT INTO `'.$invtable.'`(`id`,`invoiceid`,`userid`,`type`,`location`,`creationdate`,`invoicedate`,`taxgroup`,`accountid`,`vehicleid`,`mileagein`,`mileageout`) VALUES (:id,:invoicenumber,:userid,:typeid,:location,:creationdate,:invoicedate,:taxgroup,:accountid,:vehicleid,:mileagein,:mileageout)');
$sth2->bindParam(':invoicenumber',$invoicenumber);
$sth2->bindParam(':id',$invoiceid);
$sth2->bindParam(':userid',$currentid);
$sth2->bindParam(':typeid',$typeid);
$sth2->bindParam(':location',$location);
$sth2->bindParam(':creationdate',$currentdate);
$sth2->bindParam(':invoicedate',$currentday);
$sth2->bindParam(':taxgroup',$taxgroup);
$sth2->bindParam(':accountid',$accountid);
$sth2->bindParam(':vehicleid',$vehicleid);
$sth2->bindParam(':mileagein',$mileagein);
$sth2->bindParam(':mileageout',$mileageout);
$sth2->execute();
$invoiceid = $pdocxn->lastInsertId();

$invuserid = $row3['userid'];

$sth4 = $pdocxn->prepare('SELECT * FROM `'.$lischedule.'` WHERE `invoiceid` = :inv ORDER BY `linenumber` ASC');
$sth4->bindParam(':inv',$scheduleid);
$sth4->execute();
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



$copysql = "INSERT INTO `'.$invlinetable.'`(`invoiceid`,`linenumber`,`qty`,`amount`,`partid`,`packageid`,`serviceid`,`comment`,`fet`,`totallineamount`,`lineitem_typeid`,`lineitem_subtypeid`,`lineitem_saletype`,`hours`,`basecost`) VALUES (:invoiceid,:linenumber,:qty,:amount,:partid,:packageid,:serviceid,:comment,:fet,:totallineamount,:lineitem_typeid,:lineitem_subtypeid,:lineitem_saletype,:hours,:basecost)";
$copysql2 = "INSERT INTO `'.$invlinetable.'`(`invoiceid`,`linenumber`,`qty`,`amount`,`partid`,`packageid`,`serviceid`,`comment`,`fet`,`totallineamount`,`lineitem_typeid`,`lineitem_subtypeid`,`lineitem_saletype`,`hours`,`basecost`) VALUES ('".$invoiceid."','".$linenumber."','".$qty."','".$amount."','".$partid."','".$packageid."','".$serviceid."','".$comment."','".$fet."','".$totallineamount."','".$lineitem_typeid."','".$lineitem_subtypeid."','".$lineitem_saletype."','".$hours."','".$basecost."')";
$copysth = $pdocxn->prepare($copysql);
$copysth->bindParam(':invoiceid',$invoiceid);
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
$copysth->execute();

$lastlineid = $pdocxn->lastInsertId();
$taxamount = $invamount*$taxmultiply;
$sql3 = "INSERT INTO `tax_trans_appt`(`transid`,`taxamount`,`lineid`) VALUES (:invoiceid,:taxamount,:lineid)";
$sth3 = $pdocxn->prepare($sql3);
$sth3->bindParam(':invoiceid',$invoiceid);
$sth3->bindParam(':taxamount',$taxamount);
$sth3->bindParam(':lineid',$lastlineid);
$sth3->execute();
}
$newstatus = '8';
$sth1 = $pdocxn->prepare('UPDATE `'.$locschedule.'` SET `status`=:status,`invoiceid`=:invoiceid WHERE `id` = :scheduleid');
$sth1->bindParam(':status',$newstatus);
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->bindParam(':scheduleid',$scheduleid);
$sth1->execute();
}}}

if(isset($_POST['changetoinvoice']))
{
$sth1 = $pdocxn->prepare('SELECT `invoiceid` FROM `'.$invtable.'` WHERE `invoiceid` > \'1\' AND `location` = :currentlocationid ORDER BY `invoiceid` DESC LIMIT 1');
$sth1->bindParam(':currentlocationid',$currentlocationid);
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
$lastinvnumber = $row1['invoiceid'];
$invoicenumber = $lastinvnumber + '1';
}
	$invoiceid = $_POST['invoiceid'];
	$sth1 = $pdocxn->prepare('UPDATE `'.$invtable.'` SET `invoiceid`=:invoicenumber,`type`=\'1\' WHERE `id` = :invoiceid');
	$sth1->bindParam(':invoicenumber',$invoicenumber);
	$sth1->bindParam(':invoiceid',$invoiceid);
	$sth1->execute();
}
 if(isset($_POST['newtaxclass']))
{
global $changetax;
$changetax = '1';
$newtaxgroup = $_POST['newtaxclass'];
$sth1 = $pdocxn->prepare('UPDATE `'.$invtable.'` SET `taxgroup`=:taxgroup WHERE `id` = :invoiceid');
$sth1->bindParam(':taxgroup',$newtaxgroup);
//$sth1->bindParam(':tax',$taxtotal);
//$sth1->bindParam(':total',$invoicetotal);
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->execute();

$sql2 = 'SELECT `id`,`multiply` FROM `tax_rate`WHERE `id` = :taxid';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':taxid',$newtaxgroup);
$sth2->execute();
$row2 = $sth2->fetch(PDO::FETCH_ASSOC);
$taxmultiply = $row2['multiply'];

$sth4 = $pdocxn->prepare('SELECT `id`,`totallineamount` FROM `'.$invlinetable.'` WHERE `invoiceid` = :inv');
$sth4->bindParam(':inv',$invoiceid);
$sth4->execute();
$linecount = $sth4->rowCount();
	while($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
{
$lineid = $row4['id'];
$extprice = $row4['totallineamount'];
	$taxamount = $extprice*$taxmultiply;
	$sql3 = "UPDATE `tax_trans_appt` SET `taxamount` = :taxamount WHERE `lineid` = :lineid";
	$sth3 = $pdocxn->prepare($sql3);
	$sth3->bindParam(':taxamount',$taxamount);
	$sth3->bindParam(':lineid',$lineid);
	$sth3->execute();
	$taxamount = '0';
}
}
if(isset($_GET['ninvoice']))
{$new = '1';}
if(isset($_POST['new']))
{$new = '1';}
if($new == '1')
{
global $changetax;
$changetax = '1';
$schedule = $_POST['schedule'];
//submit insert new transaction
if(isset($_POST['accountid']))
{$accountid = $_POST['accountid'];}
if(isset($_GET['accountid']))
{$accountid = $_GET['accountid'];}
if($accountid > '0')
{
$getname = $pdocxn->prepare('SELECT `firstname`,`lastname`,`taxclass` from `accounts` WHERE `accountid` = :accountid');
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
$taxgroup = $getnamerow['taxclass'];
$abvname = $firstname." ".$lastname;
}}else{
$accountid = '0';
$abvname = '0';
$taxgroup = '0';
}
if(isset($_GET['vehicleid']))
{$vehicleid = $_GET['vehicleid'];}
if(isset($_POST['vehicleid']))
{$vehicleid = $_POST['vehicleid'];}
if($vehicleid >'0')
{
$getvehicle = $pdocxn->prepare('SELECT `year`,`make`,`model`,`description` from `vehicles` WHERE `id` = :vehicleid');
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
	$abvvehicle1a = strval($abvvehicle);
$abvlength = strlen($abvvehicle);
if($abvlength > '18')
{
$abvvehicle2 = substr($abvvehicle1a,0,18);
}else{
	$abvvehicle2 = $abvvehicle;
}
}else{
$abvvehicle = $getvehiclerow['description'];
$abvvehicle1a = strval($abvvehicle);
$abvvehicle2 = substr($abvvehicle1a,0,18);
}
if($abvvehicle < '1')
{
$abvvehicle = '';
}}
}else{
$vehicleid = '0';
$abvvehicle = '';
}
$abvvehicle1a = strval($abvvehicle);
$abvlength = strlen($abvvehicle);
if($abvlength > '18')
{
$abvvehicle2 = substr($abvvehicle1a,0,18);
}else{
	$abvvehicle2 = $abvvehicle;
}


$newlinenumber = '1';
if(isset($_GET['typeid']))
{$typeid = $_GET['typeid'];}
if(isset($_POST['type']))
{$typeid = $_POST['type'];}
$scheduledate = $_POST['appointmentdate'];
$sth1 = $pdocxn->prepare('SELECT `id` FROM `'.$invtable.'` ORDER BY `id` DESC LIMIT 1');
$sth1->execute();
$row1 = $sth1->fetch(PDO::FETCH_ASSOC);
$lastinvid = $row1['id'];
$invoiceid = $lastinvid + '1';
$sth2 = $pdocxn->prepare('INSERT INTO `'.$invtable.'`(`id`,`date`,`userid`,`type`,`location`,`creationdate`,`invoicedate`,`taxgroup`,`accountid`,`vehicleid`,`abvvehicle`,`abvname`,`schedule`) VALUES (:id,:date,:userid,:typeid,:location,:creationdate,:invoicedate,:taxgroup,:accountid,:vehicleid,:abvvehicle,:abvname,:schedule)');
$sth2->bindParam(':id',$invoiceid);
//fkmfkmfkmfkm
$sth2->bindParam(':date',$scheduledate);
$sth2->bindParam(':userid',$currentid);
$sth2->bindParam(':typeid',$typeid);
$sth2->bindParam(':location',$currentlocationid);
$sth2->bindParam(':creationdate',$currentdate);
$sth2->bindParam(':invoicedate',$currentday);
$sth2->bindParam(':taxgroup',$taxgroup);
$sth2->bindParam(':accountid',$accountid);
$sth2->bindParam(':vehicleid',$vehicleid);
$sth2->bindParam(':abvvehicle',$abvvehicle2);
$sth2->bindParam(':abvname',$abvname);
$sth2->bindParam(':schedule',$schedule);
$sth2->execute()or die(print_r($sth2->errorInfo(), true));

$header = "Location: selectsalesperson.php?siteid=".$currentlocationid."&id=".$invoiceid;
header($header);
exit();
//go to select salesperson
}
if(isset($_POST['invoicenumber'])&&$_POST_['invoicenumber']>'1')
{
//serach by invoice number
$invoicenumber = $_POST['invoicenumber'];
$getinv = $pdocxn->prepare('SELECT `id`,`type` FROM `'.$invtable.'` WHERE `invoiceid` = :invoicenumber AND `location` = :currentlocationid');
$getinv->bindParam(':currentlocationid',$currentlocationid);
$getinv->bindparam(':invoicenumber',$invoicenumber);
$getinv->execute();
while($getinvrow = $getinv->fetch(PDO::FETCH_ASSOC))
{
$invoiceid = $getinvrow['id'];
}
$sth4 = $pdocxn->prepare('SELECT `linenumber` FROM `'.$invlinetable.'` WHERE `invoiceid` = :inv ORDER BY `linenumber` DESC LIMIT 1');
$sth4->bindParam(':inv',$invoiceid);
$sth4->execute();
if(!$sth4) {
$newlinenumber = '1';
}else{
$linecount = $sth4->rowCount();
}
$newlinenumber = $linecount + '1';
}
	else
	{
//search by transactionid
$getinv = $pdocxn->prepare('SELECT `id`,`invoiceid`,`type`,`taxgroup`,`invoicedate` FROM `'.$invtable.'` WHERE `id` = :invoiceid AND `location` = :currentlocationid');
$getinv->bindParam(':currentlocationid',$currentlocationid);
$getinv->bindparam(':invoiceid',$invoiceid);
$getinv->execute();
while($getinvrow = $getinv->fetch(PDO::FETCH_ASSOC))
{
$invoicenumber = $getinvrow['invoiceid'];
$typeid = $getinvrow['type'];
$taxgroup = $getinvrow['taxgroup'];
$invoicedate = $getinvrow['invoicedate'];
$invoicedate2 = new DateTime($invoicedate);
$displayinvoicedate = $invoicedate2->format('n/j/Y');
$displayinvoicedate2 = $invoicedate2->format('Y-m-d');
$invoicesalesmanid = $_POST['userid'];
$invoicelocationid = $_POST['location'];
}
$sth4 = $pdocxn->prepare('SELECT `id` FROM `'.$invlinetable.'` WHERE `invoiceid` = :inv');
$sth4->bindParam(':inv',$invoiceid);
$sth4->execute();
if(!$sth4) {
$linecount = '0';
}else{
$linecount = $sth4->rowCount();
}
$newlinenumber = $linecount + '1';
}
if(isset($_POST['updatemiles'])&&$_POST['updatemiles']=='1')
{
//edit change mileage
$mileage = $_POST['mileage1'];
$sth3 = $pdocxn->prepare('UPDATE `'.$invtable.'` SET `mileagein`=:mileagein WHERE id = :inv');
$sth3->bindParam(':inv',$invoiceid);
$sth3->bindParam(':mileagein',$mileage);
$sth3->execute();
}
if(isset($_POST['ponumber'])&&$_POST['ponumber']=='1')
{
//edit change ponumber
$ponumber = $_POST['ponumber'];
$sth3 = $pdocxn->prepare('UPDATE `'.$invtable.'` SET `ponumber`=:ponumber WHERE id = :inv');
$sth3->bindParam(':inv',$invoiceid);
$sth3->bindParam(':ponumber',$ponumber);
$sth3->execute();
}
if($_POST['void']&&$_POST['void']=='1')
{
//edit void invoice
/*
$sth4 = $pdocxn->prepare('SELECT `qty`,`partid` FROM `'.$invlinetable.'` WHERE `invoiceid` = :inv');
$sth4->bindParam(':inv',$invoiceid);
$sth4->execute();
while($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
{
$qty = $row4['qty'];
$partid = $row4['partid'];
if($partid > '0')
{
$locationcolumn = "loc".$currentlocationid."_onhand";
$sth1 = $pdocxn->prepare('UPDATE `inventory` SET `'.$locationcolumn.'`=`'.$locationcolumn.'` + :qty WHERE `id` = :partid');
$sth1->bindParam(':qty',$qty);
$sth1->bindParam(':partid',$partid);
$sth1->execute();
}
}
*/
$sth1 = $pdocxn->prepare('UPDATE `'.$invtable.'` SET `voiddate`=:voiddate WHERE `id` = :invoiceid');
$sth1->bindParam(':voiddate',$currentday);
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->execute();

$sth2 = $pdocxn->prepare('DELETE FROM `'.$invtable.'` WHERE `thread` = :inv');
$sth2->bindParam(':inv',$invoiceid);
$sth2->execute();
header('location:'.$pagelink.'');
}
//submit form general
if($_POST['changedate']&&$_POST['changedate']=='1')
{
//edit change date
$newdate = $_POST['newdate'];
$sth1 = $pdocxn->prepare('UPDATE `'.$invtable.'` SET `invoicedate`=:newdate WHERE `id` = :invoiceid');
$sth1->bindParam(':newdate',$newdate);
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->execute();
}
//change length
if($_POST['length'])
{
	$lsi = '1';
$sth1 = $pdocxn->prepare('DELETE FROM `'.$invtable.'` WHERE `thread` = :inv');
$sth1->bindParam(':inv',$invoiceid);
$sth1->execute();

$reallength = '1';
$lengtherror = '0';
$checklength = $_POST['length'];
$schedule = $_POST['schedule'];
$sth3 = $pdocxn->prepare('SELECT * FROM `'.$invtable.'` WHERE id = :inv');
$sth3->bindParam(':inv',$invoiceid);
$sth3->execute();
$row3 = $sth3->fetch(PDO::FETCH_ASSOC);
$currentinvoicedate = $row3['date'];
$appointmentday = $row3['invoicedate'];
	//see if the following appts are filled
while($checklength > '1')
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
	$sql2 = 'SELECT * FROM `'.$invtable.'` WHERE `date` = \''.$searchtime.'\' AND `schedule` = \''.$schedule.'\' AND `voiddate` IS NULL';
	//$nRows = $pdocxn->query($sql2)->fetchColumn();
	$sth2 = $pdocxn->prepare($sql2);
	$sth2->execute();
	$nrows = $sth2->rowCount();
	if ($nrows == $numberappointments)
	{
		header('location:'.$pagelink.'?invoiceid='.$invoiceid.'&schedule='.$schedule.'&le='.$reallength.'');
		exit();
	}
	else {
		$fillertype = '54';
$sql2 = 'INSERT INTO `'.$invtable.'` (`date`,`thread`,`type`,`location`,`invoicedate`,`accountid`,`schedule`) VALUES (:date,:thread,:type,:location,:invoicedate,:accountid,:schedule)';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':date',$searchtime);
$sth2->bindParam(':thread',$invoiceid);
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
	$sth1 = $pdocxn->prepare('UPDATE `'.$invtable.'` SET `length`=:length WHERE `id` = :invoiceid');
$sth1->bindParam(':length',$reallength);
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->execute();
}
if($_POST['newstatus'])
{
$newstatus = $_POST['newstatus'];
$sth1 = $pdocxn->prepare('UPDATE `'.$invtable.'` SET `status`=:status WHERE `id` = :invoiceid');
$sth1->bindParam(':status',$newstatus);
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->execute();
}














	//copy quote from confirm
if(isset($_GET['confirm']))
{
$quoteid = $_GET['i'];
$invoiceid = $_GET['confirmappointment'];
$sth3 = $pdocxn->prepare('SELECT * FROM `invoice` WHERE id = :inv');
$sth3->bindParam(':inv',$quoteid);
$sth3->execute();
$row3 = $sth3->fetch(PDO::FETCH_ASSOC);

$accountid = $row3['accountid'];
$invuserid = $row3['userid'];
$vehicleid = $row3['vehicleid'];
$mileagein = $row3['mileagein'];
$mileageout = $row3['mileageout'];
$location = $row3['location'];
$taxgroup = $row3['taxgroup'];
//convert from a quote/invoice to schedule


    $getname = $pdocxn->prepare('SELECT `firstname`,`lastname`,`fullname`,`taxclass` from `accounts` WHERE `accountid` = :accountid');
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
    $taxclass = $getnamerow['taxclass'];
    
    $abvname = $firstname." ".$lastname;
    
    }
    if($vehicleid > '0')
    {
    $getvehicle = $pdocxn->prepare('SELECT `year`,`make`,`model`,`description` from `vehicles` WHERE `id` = :vehicleid');
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
        $abvvehicle1a = strval($abvvehicle);
    $abvlength = strlen($abvvehicle);
    if($abvlength > '18')
    {
    $abvvehicle2 = substr($abvvehicle1a,0,20);
    }else{
        $abvvehicle2 = $abvvehicle;
    }
    }else{
    $abvvehicle = $getvehiclerow['description'];
    $abvvehicle1a = strval($abvvehicle);
    $abvvehicle2 = substr($abvvehicle1a,0,20);
    }
    if($abvvehicle < '1')
    {
    $abvvehicle = '';
    }}
$sth1 = $pdocxn->prepare('UPDATE `'.$invtable.'` SET `accountid`=:accountid,`vehicleid`=:vehicleid,`abvname`=:abvname,`abvvehicle`=:abvvehicle,`taxgroup`=:taxgroup WHERE `id` = :invoiceid');
$sth1->bindParam(':accountid',$accountid);
$sth1->bindParam(':vehicleid',$vehicleid);
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->bindParam(':abvname',$abvname);
$sth1->bindParam(':abvvehicle',$abvvehicle2);
$sth1->bindParam(':taxgroup',$taxclass);
$sth1->execute();
}else{
	$sth1 = $pdocxn->prepare('UPDATE `'.$invtable.'` SET `accountid`=:accountid,`abvname`=:abvname,`taxgroup`=:taxgroup WHERE `id` = :invoiceid');
	$sth1->bindParam(':accountid',$accountid);
	$sth1->bindParam(':invoiceid',$invoiceid);
	$sth1->bindParam(':abvname',$abvname);
	$sth1->bindParam(':taxgroup',$taxclass);
	$sth1->execute();
}












$sth4 = $pdocxn->prepare('SELECT * FROM `line_items` WHERE `invoiceid` = :inv ORDER BY `linenumber` ASC');
$sth4->bindParam(':inv',$quoteid);
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
$linitem_printflag = $row4['printflag'];



$copysql = 'INSERT INTO `'.$invlinetable.'`(`invoiceid`,`linenumber`,`qty`,`amount`,`partid`,`packageid`,`serviceid`,`comment`,`fet`,`totallineamount`,`lineitem_typeid`,`lineitem_subtypeid`,`lineitem_saletype`,`hours`,`basecost`,`printflag`) VALUES (:invoiceid,:linenumber,:qty,:amount,:partid,:packageid,:serviceid,:comment,:fet,:totallineamount,:lineitem_typeid,:lineitem_subtypeid,:lineitem_saletype,:hours,:basecost,:printflag)';

$copysth = $pdocxn->prepare($copysql);
$copysth->bindParam(':invoiceid',$invoiceid);
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
$copysth->bindparam(':printflag',$printflag);
$copysth->execute()or die(print_r($copysth->errorInfo(), true));

}
}






























































if($_POST['changecustomer'] OR $_GET['changecustomer']){
//edit change customer
if($_POST['changecustomer']){
	$newaccountid = $_POST['accountid'];
}
if($_GET['changecustomer']){
	$newaccountid = $_GET['accountid'];
}
if(isset($_GET['cninvoice']))
{
	$confirm = '1';
}
$newvehicleid = '';
if(isset($_POST['vehicleid']))
{
$newvehicleid = $_POST['vehicleid'];
}
if(isset($_GET['vehicleid']))
{
$newvehicleid = $_GET['vehicleid'];
}
if($confirm == '1')
{}else{
	$sth1 = $pdocxn->prepare('SELECT `id` FROM `invoice` WHERE `type` = \'4\' AND `location` = :currentlocationid AND `accountid` = :accountid AND `voiddate` IS NULL LIMIT 1');
	$sth1->bindParam(':currentlocationid',$currentlocationid);
	$sth1->bindParam(':accountid',$newaccountid);
	$sth1->execute();
	while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$checkid = $row1['id'];
	}

	if($checkid > '1')
	{
	//fkmfkmfkm
		$confirmlocation = 'confirmappointment.php?accountid='.$newaccountid.'&vehicleid='.$newvehicleid.'&invoiceid='.$invoiceid;
		header('location:'.$confirmlocation.'');
		exit();
	}
	}
$getname = $pdocxn->prepare('SELECT `firstname`,`lastname`,`fullname`,`taxclass` from `accounts` WHERE `accountid` = :accountid');
$getname->bindParam(':accountid',$newaccountid);
$getname->execute();
while($getnamerow = $getname->fetch(PDO::FETCH_ASSOC))
{
$databasefname = $getnamerow['firstname'];
$firstname = stripslashes($databasefname);
$databaselname = $getnamerow['lastname'];
$lastname = stripslashes($databaselname);
$databasefullname = $getnamerow['fullname'];
$fullname = stripslashes($databasefullname);
$taxclass = $getnamerow['taxclass'];

$abvname = $firstname." ".$lastname;

}
if($newvehicleid > '0')
{
$getvehicle = $pdocxn->prepare('SELECT `year`,`make`,`model`,`description` from `vehicles` WHERE `id` = :vehicleid');
$getvehicle->bindParam(':vehicleid',$newvehicleid);
$getvehicle->execute();
while($getvehiclerow = $getvehicle->fetch(PDO::FETCH_ASSOC))
{
$year = $getvehiclerow['year'];
$make = $getvehiclerow['make'];
$model = $getvehiclerow['model'];
if($year > '0')
{
	$abvvehicle = $year." ".$make." ".$model;
	$abvvehicle1a = strval($abvvehicle);
$abvlength = strlen($abvvehicle);
if($abvlength > '18')
{
$abvvehicle2 = substr($abvvehicle1a,0,20);
}else{
	$abvvehicle2 = $abvvehicle;
}
}else{
$abvvehicle = $getvehiclerow['description'];
$abvvehicle1a = strval($abvvehicle);
$abvvehicle2 = substr($abvvehicle1a,0,20);
}
if($abvvehicle < '1')
{
$abvvehicle = '';
}}
$sth1 = $pdocxn->prepare('UPDATE `'.$invtable.'` SET `accountid`=:accountid,`vehicleid`=:vehicleid,`abvname`=:abvname,`abvvehicle`=:abvvehicle,`taxgroup`=:taxgroup WHERE `id` = :invoiceid');
$sth1->bindParam(':accountid',$newaccountid);
$sth1->bindParam(':vehicleid',$newvehicleid);
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->bindParam(':abvname',$abvname);
$sth1->bindParam(':abvvehicle',$abvvehicle2);
$sth1->bindParam(':taxgroup',$taxclass);
$sth1->execute();
}else{
	$sth1 = $pdocxn->prepare('UPDATE `'.$invtable.'` SET `accountid`=:accountid,`abvname`=:abvname,`vehicleid`=\'0\',`abvvehicle`=\'0\',`taxgroup`=:taxgroup WHERE `id` = :invoiceid');
	$sth1->bindParam(':accountid',$newaccountid);
	$sth1->bindParam(':invoiceid',$invoiceid);
	$sth1->bindParam(':abvname',$abvname);
	$sth1->bindParam(':taxgroup',$taxclass);
	$sth1->execute();
}
}
if($_POST['vehiclechange']&&$_POST['vehiclechange']>'1')
{
	//fkmfkmfkmfkm
	$newvehicleid = $_POST['vehiclechange'];
	$changevehicle = '1';
}
if($_GET['vehicleid']&&$_GET['vehicleid']>'0')
{
	$newvehicleid = $_GET['vehicleid'];
	$changevehicle = '1';
}
if($changevehicle == '1')
{
//edit change vehicle
$getvehicle = $pdocxn->prepare('SELECT `year`,`make`,`model`,`description` from `vehicles` WHERE `id` = :vehicleid');
$getvehicle->bindParam(':vehicleid',$newvehicleid);
$getvehicle->execute();
while($getvehiclerow = $getvehicle->fetch(PDO::FETCH_ASSOC))
{
$year = $getvehiclerow['year'];
$make = $getvehiclerow['make'];
$model = $getvehiclerow['model'];
$description = $getvehiclerow['description'];
if($year < '1')
{
$abvvehicle = $description;
$abvvehicle1a = strval($abvvehicle);
$abvvehicle2 = substr($abvvehicle1a,0,18);
}
else{
$abvvehicle = $year." ".$make." ".$model;
$abvvehicle1a = strval($abvvehicle);
$abvlength = strlen($abvvehicle);
if($abvlength > '18')
{
$abvvehicle2 = substr($abvvehicle1a,0,18);
}else{
	$abvvehicle2 = $abvvehicle;
}
}
if($abvvehicle < '1')
{
$abvvehicle = '';
}
}
$sth1 = $pdocxn->prepare('UPDATE `'.$invtable.'` SET `vehicleid`=:vehicleid,`abvvehicle`=:abvvehicle WHERE `id` = :invoiceid');
$sth1->bindParam(':vehicleid',$newvehicleid);
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->bindParam(':abvvehicle',$abvvehicle2);
$sth1->execute();
}
if($_POST['editvehicle']&&$_POST['editvehicle']=='1')
{
//edit vehicle info
$vehicledesc = $_POST['vehiclename'];
$vin = $_POST['vehiclevin'];
$license = $_POST['license'];
$state = $_POST['state'];
$invoiceid = $_POST['invoiceid'];
$vehicleid = $_POST['vehicleid'];
$postedyear = $_POST['vehicleyear'];
$postedmake = $_POST['vehiclemake'];
$postedmodel = $_POST['vehiclemodel'];
if($postedyear > '1')
{
$sth1 = $pdocxn->prepare('UPDATE `vehicles` SET `vin`=:vin,`state`=:nstate,`license`=:license,`year`=:nyear,`make`=:make,`model`=:model WHERE `id` = :vehicleid');
$sth1->bindParam(':nyear',$postedyear);
$sth1->bindParam(':make',$postedmake);
$sth1->bindParam(':model',$postedmodel);
}else{
$sth1 = $pdocxn->prepare('UPDATE `vehicles` SET `vin`=:vin,`state`=:nstate,`license`=:license,`description`=:ndescription WHERE `id` = :vehicleid');
$sth1->bindParam(':ndescription',$vehicledesc);
}
$sth1->bindParam(':vin',$vin);
$sth1->bindParam(':nstate',$state);
$sth1->bindParam(':license',$license);
$sth1->bindParam(':vehicleid',$vehicleid);
$sth1->execute();


$sth1 = $pdocxn->prepare('UPDATE `'.$invtable.'` SET `abvvehicle`=:abvvehicle WHERE `id` = :invoiceid');
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->bindParam(':abvvehicle',$vehicledesc);
$sth1->execute();
}
if($_POST['delete']&&$_POST['delete']=='1')
{
//edit delete line
$lineid = $_POST['lineid'];
global $changetax;
$changetax = '1';
$sth4 = $pdocxn->prepare('SELECT `qty`,`partid` FROM `'.$invlinetable.'` WHERE `id` = :lineid');
$sth4->bindParam(':lineid',$lineid);
$sth4->execute();
while($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
{
$qty = $row4['qty'];
$partid = $row4['partid'];


}
$deletedlinenumber = $_POST['deletedlinenumber'];
$sth1 = $pdocxn->prepare('DELETE FROM `'.$invlinetable.'` WHERE `id` = :lineid');
$sth1->bindParam(':lineid',$lineid);
$sth1->execute();

$sql3 = "DELETE FROM `tax_trans_appt` WHERE `lineid` = :lineid";
$sth3 = $pdocxn->prepare($sql3);
$sth3->bindParam(':lineid',$lineid);
$sth3->execute();



$sth2 = $pdocxn->prepare('UPDATE `'.$invlinetable.'` SET `linenumber` = `linenumber` - 1 WHERE `invoiceid` = :invoiceid AND `linenumber` > :deletedlinenumber');
$sth2->bindParam(':invoiceid',$invoiceid);
$sth2->bindParam(':deletedlinenumber',$deletedlinenumber);
$sth2->execute();
if($articleid = '0' && $typeid = '1')
{
	$deletedlinenumber = $_POST['deletedlinenumber'];
$sth1 = $pdocxn->prepare('DELETE FROM `inventory_transactions` WHERE `lineid` = :lineid AND `transactiontype` = \'51\'');
$sth1->bindParam(':lineid',$lineid);
$sth1->execute();
}
}
if($_POST['linemove'] &&$_POST['linemove'] == '1')
{
//edit line move
$currentline = $_POST['currentline'];
$previousline = $currentline - '1';
$nextline = $currentline + '1';
$lineid = $_POST['lineid'];
if($_POST['up'] &&$_POST['up'] == '1')
{
$sth1 = $pdocxn->prepare('UPDATE `'.$invlinetable.'` SET `linenumber`=:currentline WHERE `invoiceid` = :invoiceid AND `linenumber` = :previousline');
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->bindParam(':currentline',$currentline);
$sth1->bindParam(':previousline',$previousline);
$sth1->execute();
$sth2 = $pdocxn->prepare('UPDATE `'.$invlinetable.'` SET `linenumber` = :previousline WHERE `id` = :lineid');
$sth2->bindParam(':lineid',$lineid);
$sth2->bindParam(':previousline',$previousline);
$sth2->execute();
}
if($_POST['down']&&$_POST['down'] == '1')
{
$sth1 = $pdocxn->prepare('UPDATE `'.$invlinetable.'` SET `linenumber` = :currentline WHERE `invoiceid` = :invoiceid AND `linenumber` = :nextline');
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->bindParam(':currentline',$currentline);
$sth1->bindParam(':nextline',$nextline);
$sth1->execute();
$sth2 = $pdocxn->prepare('UPDATE `'.$invlinetable.'` SET `linenumber`=:nextline WHERE `id` = :lineid');
$sth2->bindParam(':lineid',$lineid);
$sth2->bindParam(':nextline',$nextline);
$sth2->execute();
}
}
if($_POST['editsubmit']&&$_POST['editsubmit'] == '1')
{
global $changetax;
$changetax = '1';
//edit line item
$invoicenumber = $_POST['invoicenumber'];
$lineid = $_POST['lineid'];
$qty = $_POST['qty'];
$amount = $_POST['price'];
$comment = $_POST['comment'];
$fet = $_POST['fet'];
$totallineamount = $qty*$amount;
$sth1 = $pdocxn->prepare('UPDATE `'.$invlinetable.'` SET `qty`=:qty,`amount`=:amount,`comment`=:comment,`totallineamount`=:totallineamount WHERE `id` = :lineid');
$sth1->bindParam(':totallineamount',$totallineamount);
$sth1->bindParam(':qty',$qty);
$sth1->bindParam(':amount',$amount);
$sth1->bindParam(':lineid',$lineid);
$sth1->bindParam(':comment',$comment);
$sth1->execute();

$taxamount = $totallineamount*$taxmultiply;
$sql3 = "UPDATE `tax_trans_appt` SET `taxamount`=:taxamount WHERE `lineid` = :lineid";
$sth3 = $pdocxn->prepare($sql3);
$sth3->bindParam(':taxamount',$taxamount);
$sth3->bindParam(':lineid',$lineid);
$sth3->execute();
}

if(isset($_POST['invoicenumber'])&&$_POST_['invoicenumber']>'1')
	{
//search by invoice number
$invoicenumber = $_POST['invoicenumber'];
$getinv = $pdocxn->prepare('SELECT `id`,`type`,`taxgroup`,`invoicedate`,`userid`,`location` FROM `'.$invtable.'` WHERE `invoiceid` = :invoicenumber AND `location` = :currentlocationid');
$getinv->bindParam(':currentlocationid',$currentlocationid);
$getinv->bindparam(':invoicenumber',$invoicenumber);
$getinv->execute();
while($getinvrow = $getinv->fetch(PDO::FETCH_ASSOC))
{
$invoiceid = $getinvrow['id'];
$typeid = $getinvrow['type'];
$taxgroup = $getinvrow['taxgroup'];
$invoicedate = $getinvrow['invoicedate'];
$invoicedate2 = new DateTime($invoicedate);
$displayinvoicedate = $invoicedate2->format('n/j/Y');
$displayinvoicedate2 = $invoicedate2->format('Y-m-d');
$invoicesalesmanid = $_POST['userid'];
$invoicelocationid = $_POST['loaction'];
}
$sth4 = $pdocxn->prepare('SELECT `id` FROM `'.$invlinetable.'` WHERE `invoiceid` = :inv');
$sth4->bindParam(':inv',$invoiceid);
$sth4->execute();
if(!$sth4) {
$linecount = '0';
}else{
$linecount = $sth4->rowCount();
}
$newlinenumber = $linecount + '1';
	}
	else
	{
//search by transactionid
$getinv = $pdocxn->prepare('SELECT `id`,`invoiceid`,`type`,`taxgroup`,`invoicedate` FROM `'.$invtable.'` WHERE `id` = :invoiceid AND `location` = :currentlocationid');
$getinv->bindParam(':currentlocationid',$currentlocationid);
$getinv->bindparam(':invoiceid',$invoiceid);
$getinv->execute();
while($getinvrow = $getinv->fetch(PDO::FETCH_ASSOC))
{
$invoicenumber = $getinvrow['invoiceid'];
$typeid = $getinvrow['type'];
$taxgroup = $getinvrow['taxgroup'];
$invoicedate = $getinvrow['invoicedate'];
$invoicedate2 = new DateTime($invoicedate);
$displayinvoicedate = $invoicedate2->format('n/j/Y');
$displayinvoicedate2 = $invoicedate2->format('Y-m-d');
$invoicesalesmanid = $_POST['userid'];
$invoicelocationid = $_POST['location'];
}
$sth4 = $pdocxn->prepare('SELECT `id` FROM `'.$invlinetable.'` WHERE `invoiceid` = :inv');
$sth4->bindParam(':inv',$invoiceid);
$sth4->execute();
if(!$sth4) {
$linecount = '0';
}else{
$linecount = $sth4->rowCount();
}
$newlinenumber = $linecount + '1';
	}

	if($_POST['copy']&&$_POST['copy'] == '1')
{
global $changetax;
$changetax = '1';
//copy a transaction
//$copytype = '1';
$sth1 = $pdocxn->prepare('SELECT `type`,`id`,`location`,`subtotal`,`tax`,`total`,`taxgroup` FROM `'.$invtable.'` WHERE `id` = :invoiceid LIMIT 1');
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
$oldinvoiceid = $invoiceid;
$oldlocationid = $row1['location'];
$oldsubtotal = $row1['subtotal'];
$oldtax = $row1['tax'];
$oldtotal = $row1['total'];
$oldtaxgroup = $row1['taxgroup'];
$copytype = $row1['type'];
}
$sth1 = $pdocxn->prepare('SELECT `id` FROM `'.$invtable.'` ORDER BY `id` DESC LIMIT 1');
$sth1->execute();
$row1 = $sth1->fetch(PDO::FETCH_ASSOC);
$lastinvid = $row1['id'];
$invoiceid = $lastinvid + '1';
if($copytype == '1')
{
$sth1 = $pdocxn->prepare('SELECT `invoiceid` FROM `'.$invtable.'` WHERE `invoiceid` > \'1\' AND `location` = :currentlocationid ORDER BY `invoiceid` DESC LIMIT 1');
$sth1->bindParam(':currentlocationid',$currentlocationid);
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
$lastinvnumber = $row1['invoiceid'];
$invoicenumber = $lastinvnumber + '1';
}

$sql2 = 'INSERT INTO `'.$invtable.'`(`id`,`invoiceid`,`accountid`,`type`,`location`,`creationdate`,`invoicedate`,`subtotal`,`tax`,`total`,`taxgroup`) VALUES (:id,:invoicenumber,\'0\',:type,:location,:creationdate,:invoicedate,:subtotal,:tax,:total,:taxgroup)';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':invoicenumber',$invoicenumber);
}else{
$sql2 = 'INSERT INTO `'.$invtable.'`(`id`,`accountid`,`type`,`location`,`creationdate`,`invoicedate`,`subtotal`,`tax`,`total`,`taxgroup`) VALUES (:id,\'0\',:type,:location,:creationdate,:invoicedate,:subtotal,:tax,:total,:taxgroup)';
$sth2 = $pdocxn->prepare($sql2);
}
$sth2->bindParam(':id',$invoiceid);
$sth2->bindParam(':type',$copytype);
$sth2->bindParam(':location',$oldlocationid);
$sth2->bindParam(':creationdate',$currentdate);
$sth2->bindParam(':invoicedate',$currentdate);
$sth2->bindParam(':subtotal',$oldsubtotal);
$sth2->bindParam(':tax',$oldtax);
$sth2->bindParam(':total',$oldtotal);
$sth2->bindParam(':taxgroup',$oldtaxgroup);
$sth2->execute()or die(print_r($sth2->errorInfo(), true));

$sth2 = $pdocxn->prepare('SELECT `linenumber`,`qty`,`amount`,`partid`,`packageid` `serviceid`,`comment`,`fet`,`totallineamount`,`lineitem_typeid`,`lineitem_subtypeid`,`lineitem_saletype`,`hours`,`basecost` FROM `'.$invlinetable.'` WHERE `invoiceid` = :invoiceid');
$sth2->bindParam(':invoiceid',$oldinvoiceid);
$sth2->execute();
while($copyrow = $sth2->fetch(PDO::FETCH_ASSOC))
{
$linenumber = $copyrow['linenumber'];
$qty = $copyrow['qty'];
$amount = $copyrow['amount'];
if($amount < '1')
{$amount = '0';}
$partid = $copyrow['partid'];
if($partid < '1')
{$partid = '0';}
$packageid = $copyrow['packageid'];
if($packageid < '1')
{$packageid = '0';}
$serviceid = $copyrow['serviceid'];
if($serviceid < '1')
{$serviceid = '0';}
$databasecomment = $copyrow['comment'];
$comment = stripslashes($databasecomment);
$fet = $copyrow['fet'];
if($fet < '1')
{$fet = '0';}
$totallineamount = $copyrow['totallineamount'];
$lineitem_typeid = $copyrow['lineitem_typeid'];
if($lineitem_typeid < '1')
{$lineitem_typeid = '0';}
$lineitem_subtypeid = $copyrow['lineitem_subtypeid'];
if($lineitem_subtypeid < '1')
{$lineitem_subtypeid = '0';}
$lineitem_saletype = $copyrow['lineitem_saletype'];
if($lineitem_saletype < '1')
{$lineitem_saletype = '0';}
$hours = $copyrow['hours'];
if($hours < '1')
{$hours = '0';}
$basecost = $copyrow['basecost'];


$copysql = 'INSERT INTO `'.$invlinetable.'`(`invoiceid`,`linenumber`,`qty`,`amount`,`partid`,`packageid`,`serviceid`,`comment`,`fet`,`totallineamount`,`lineitem_typeid`,`lineitem_subtypeid`,`lineitem_saletype`,`hours`,`basecost`) VALUES (:invoiceid,:linenumber,:qty,:amount,:partid,:packageid,:serviceid,:comment,:fet,:totallineamount,:lineitem_typeid,:lineitem_subtypeid,:lineitem_saletype,:hours,:basecost)';
$copysql2 = 'INSERT INTO `'.$invlinetable.'`(`invoiceid`,`linenumber`,`qty`,`amount`,`partid`,`packageid`,`serviceid`,`comment`,`fet`,`totallineamount`,`lineitem_typeid`,`lineitem_subtypeid`,`lineitem_saletype`,`hours`,`basecost`) VALUES ('.$invoiceid.','.$linenumber.','.$qty.','.$amount.','.$partid.','.$packageid.','.$serviceid.','.$comment.','.$fet.','.$totallineamount.','.$lineitem_typeid."','".$lineitem_subtypeid."','".$lineitem_saletype."','".$hours."','".$basecost.')';
$copysth = $pdocxn->prepare($copysql);
$copysth->bindParam(':invoiceid',$invoiceid);
$copysth->bindParam(':linenumber',$linenumber);
$copysth->bindParam(':qty',$qty);
$copysth->bindParam(':amount',$amount);
$copysth->bindParam(':partid',$partid);
$copysth->bindParam(':packageid',$packageid);
$copysth->bindParam(':serviceid',$serviceid);
$copysth->bindParam(':comment',$comment);
$copysth->bindParam(':fet',$fet);
$copysth->bindParam(':totallineamount',$totallineamount);
$copysth->bindParam(':lineitem_typeid',$lineitem_typeid);
$copysth->bindParam(':lineitem_subtypeid',$lineitem_subtypeid);
$copysth->bindParam(':lineitem_saletype',$lineitem_saletype);
$copysth->bindParam(':hours',$hours);
$copysth->bindParam(':basecost',$basecost);
$copysth->execute();
$lastlineid = $pdocxn->lastInsertId();
$taxamount = $totallineamount*$taxmultiply;
$sql3 = "INSERT INTO `tax_trans_appt`(`transid`,`taxamount`,`lineid`) VALUES (:invoiceid,:taxamount,:lineid)";
$sth3 = $pdocxn->prepare($sql3);
$sth3->bindParam(':invoiceid',$invoiceid);
$sth3->bindParam(':taxamount',$taxamount);
$sth3->bindParam(':lineid',$lastlineid);
$sth3->execute();
}
}
if(isset($_POST['appointmentsubmit']))
{
	//how to make this an appointment??
}

	if(isset($_GET['split']))
{
$split = $_GET['split'];
}
if($split == '2')
{
$oldinvoiceid = $_POST['invoiceid'];
//split a transaction
//$copytype = $_POST['copytype'];

$copytype = '1';
$sth1 = $pdocxn->prepare('SELECT `id`,`location`,`subtotal`,`tax`,`total`,`taxgroup` FROM `'.$invtable.'` WHERE `id` = :invoiceid LIMIT 1');
$sth1->bindParam(':invoiceid',$oldinvoiceid);
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
$oldlocationid = $row1['location'];
$oldsubtotal = $row1['subtotal'];
$oldtax = $row1['tax'];
$oldtotal = $row1['total'];
$oldtaxgroup = $row1['taxgroup'];
}
$sth1 = $pdocxn->prepare('SELECT `id` FROM `'.$invtable.'` ORDER BY `id` DESC LIMIT 1');
$sth1->execute();
$row1 = $sth1->fetch(PDO::FETCH_ASSOC);
$lastinvid = $row1['id'];
$invoiceid = $lastinvid + '1';

$sth1 = $pdocxn->prepare('SELECT `invoiceid` FROM `'.$invtable.'` WHERE `invoiceid` > \'1\' AND `location` = :currentlocationid ORDER BY `invoiceid` DESC LIMIT 1');
$sth1->bindParam(':currentlocationid',$currentlocationid);
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
$lastinvnumber = $row1['invoiceid'];
$invoicenumber = $lastinvnumber + '1';
}

$sql2 = 'INSERT INTO `'.$invtable.'`(`id`,`invoiceid`,`type`,`location`,`creationdate`,`invoicedate`,`subtotal`,`tax`,`total`,`taxgroup`) VALUES (:id,:invoicenumber,:type,:location,:creationdate,:invoicedate,:subtotal,:tax,:total,:taxgroup)';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':id',$invoiceid);
$sth2->bindParam(':invoicenumber',$invoicenumber);
$sth2->bindParam(':type',$copytype);
$sth2->bindParam(':location',$oldlocationid);
$sth2->bindParam(':creationdate',$currentdate);
$sth2->bindParam(':invoicedate',$currentdate);
$sth2->bindParam(':subtotal',$oldsubtotal);
$sth2->bindParam(':tax',$oldtax);
$sth2->bindParam(':total',$oldtotal);
$sth2->bindParam(':taxgroup',$oldtaxgroup);
$sth2->execute();
$newlinesplit = '1';
foreach($linesplit as $newsplit)
{
$sth2 = $pdocxn->prepare('SELECT `qty`,`amount`,`partid`,`packageid` `serviceid`,`comment`,`fet`,`totallineamount`,`lineitem_typeid`,`lineitem_subtypeid`,`lineitem_saletype`,`hours`,`basecost` FROM `'.$invlinetable.'` WHERE `invoiceid` = :invoiceid AND `linenumber` = :linenumber');
$sth2->bindParam(':invoiceid',$oldinvoiceid);
$sth2->bindParam(':linenumber',$newsplit);
$sth2->execute()or die(print_r($sth2->errorInfo(), true));
while($copyrow = $sth2->fetch(PDO::FETCH_ASSOC))
{
$qty = $copyrow['qty'];
$amount = $copyrow['amount'];
if($amount < '1')
{$amount = '0';}
$partid = $copyrow['partid'];
if($partid < '1')
{$partid = '0';}
$packageid = $copyrow['packageid'];
if($packageid < '1')
{$packageid = '0';}
$serviceid = $copyrow['serviceid'];
if($serviceid < '1')
{$serviceid = '0';}
$databasecomment = $copyrow['comment'];
$comment = stripslashes($databasecomment);
$fet = $copyrow['fet'];
if($fet < '1')
{$fet = '0';}
$totallineamount = $copyrow['totallineamount'];
$lineitem_typeid = $copyrow['lineitem_typeid'];
if($lineitem_typeid < '1')
{$lineitem_typeid = '0';}
$lineitem_subtypeid = $copyrow['lineitem_subtypeid'];
if($lineitem_subtypeid < '1')
{$lineitem_subtypeid = '0';}
$lineitem_saletype = $copyrow['lineitem_saletype'];
if($lineitem_saletype < '1')
{$lineitem_saletype = '0';}
$hours = $copyrow['hours'];
if($hours < '1')
{$hours = '0';}
$basecost = $copyrow['basecost'];


$copysql = "INSERT INTO `'.$invlinetable.'`(`invoiceid`,`linenumber`,`qty`,`amount`,`partid`,`packageid`,`serviceid`,`comment`,`fet`,`totallineamount`,`lineitem_typeid`,`lineitem_subtypeid`,`lineitem_saletype`,`hours`,`basecost`) VALUES (:invoiceid,:linenumber,:qty,:amount,:partid,:packageid,:serviceid,:comment,:fet,:totallineamount,:lineitem_typeid,:lineitem_subtypeid,:lineitem_saletype,:hours,:basecost)";
//$copysql2 = "INSERT INTO `'.$invlinetable.'`(`invoiceid`,`linenumber`,`qty`,`amount`,`partid`,`packageid`,`serviceid`,`comment`,`fet`,`totallineamount`,`lineitem_typeid`,`lineitem_subtypeid`,`lineitem_saletype`,`hours`,`basecost`) VALUES ('".$invoiceid."','".$linenumber."','".$qty."','".$amount."','".$partid."','".$packageid."','".$serviceid."','".$comment."','".$fet."','".$totallineamount."','".$lineitem_typeid."','".$lineitem_subtypeid."','".$lineitem_saletype."','".$hours."','".$basecost."')";
$copysth = $pdocxn->prepare($copysql);
$copysth->bindParam(':invoiceid',$invoiceid);
$copysth->bindParam(':linenumber',$newlinesplit);
$copysth->bindParam(':qty',$qty);
$copysth->bindParam(':amount',$amount);
$copysth->bindParam(':partid',$partid);
$copysth->bindParam(':packageid',$packageid);
$copysth->bindParam(':serviceid',$serviceid);
$copysth->bindParam(':comment',$comment);
$copysth->bindParam(':fet',$fet);
$copysth->bindParam(':totallineamount',$totallineamount);
$copysth->bindParam(':lineitem_typeid',$lineitem_typeid);
$copysth->bindParam(':lineitem_subtypeid',$lineitem_subtypeid);
$copysth->bindParam(':lineitem_saletype',$lineitem_saletype);
$copysth->bindParam(':hours',$hours);
$copysth->bindParam(':basecost',$basecost);
$copysth->execute()or die(print_r($copysth->errorInfo(), true));
$newlinesplit ++;
}
}

if(isset($_POST['quotesubmit']) OR isset($_POST['invoicesubmit']))
{
		//get old values
$sth3 = $pdocxn->prepare('SELECT * FROM `'.$invtable.'` WHERE id = :inv');
$sth3->bindParam(':inv',$oldinvoiceid);
$sth3->execute();
$row3 = $sth3->fetch(PDO::FETCH_ASSOC);
$accountid = $row3['accountid'];
$vehicleid = $row3['vehicleid'];
$invuserid = $row3['userid'];
$vehicleid = $row3['vehicleid'];
$mileagein = $row3['mileagein'];
$location = $row3['location'];
$taxgroup = $row3['taxgroup'];

$sth1 = $pdocxn->prepare('SELECT `id` FROM `'.$invtable.'` ORDER BY `id` DESC LIMIT 1');
$sth1->execute();
$row1 = $sth1->fetch(PDO::FETCH_ASSOC);
$lastinvid = $row1['id'];
$invoiceid = $lastinvid + '1';

$sth1 = $pdocxn->prepare('SELECT `invoiceid` FROM `'.$invtable.'` WHERE `invoiceid` > \'1\' AND `location` = :currentlocationid ORDER BY `invoiceid` DESC LIMIT 1');
$sth1->bindParam(':currentlocationid',$currentlocationid);
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
$lastinvnumber = $row1['invoiceid'];
$invoicenumber = $lastinvnumber + '1';
}
if(isset($_POST['invoicesubmit']))
{
$sth2 = $pdocxn->prepare('INSERT INTO `'.$invtable.'`(`id`,`invoiceid`,`userid`,`type`,`location`,`creationdate`,`invoicedate`,`taxgroup`,`accountid`,`mileagein`,`vehicleid`) VALUES (:id,:invoicenumber,:userid,:typeid,:location,:creationdate,:invoicedate,:taxgroup,:accountid,:mileagein,:vehicleid)');
$sth2->bindParam(':invoicenumber',$invoicenumber);
$typeid = '1';
$sth2->bindParam(':typeid',$typeid);
}else{
$sth2 = $pdocxn->prepare('INSERT INTO `'.$invtable.'`(`id`,`userid`,`type`,`location`,`creationdate`,`invoicedate`,`taxgroup`,`accountid`,`mileagein`,`vehicleid`) VALUES (:id,:userid,:typeid,:location,:creationdate,:invoicedate,:taxgroup,:accountid,:mileagein,:vehicleid)');
$typeid = '4';
$sth2->bindParam(':typeid',$typeid);
}
$sth2->bindParam(':id',$invoiceid);
$sth2->bindParam(':userid',$currentid);
$sth2->bindParam(':location',$location);
$sth2->bindParam(':creationdate',$currentdate);
$sth2->bindParam(':invoicedate',$currentday);
$sth2->bindParam(':taxgroup',$taxgroup);
$sth2->bindParam(':accountid',$accountid);
$sth2->bindParam(':mileagein',$mileagein);
$sth2->bindParam(':vehicleid',$vehicleid);
$sth2->execute();
//for each line update invoiceid
if(isset($_POST['checkedlineid']))
{
foreach($_POST['checkedlineid'] as $checkedlineid) {
    // here you can use $value
$sth2 = $pdocxn->prepare('UPDATE `'.$invlinetable.'` SET `invoiceid` = :invoiceid WHERE `id` = :lineid');
$sth2->bindParam(':invoiceid',$invoiceid);
$sth2->bindParam(':lineid',$checkedlineid);
$sth2->execute();
}

$newinvlinenum = '1';
$sth4 = $pdocxn->prepare('SELECT `id` FROM `'.$invlinetable.'` WHERE `invoiceid` = :invoiceid ORDER BY `linenumber` ASC');
$sth4->bindParam(':invoiceid',$invoiceid);
$sth4->execute();
	while($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
{
$newline = $row4['id'];
$sth2 = $pdocxn->prepare('UPDATE `'.$invlinetable.'` SET `linenumber` = :newinvlinenum WHERE `id` = :newline');
$sth2->bindParam(':newinvlinenum',$newinvlinenum);
$sth2->bindParam(':newline',$newline);
$sth2->execute();
$newinvlinenum ++;
}





$oldinvlinenum = '1';
$sth4 = $pdocxn->prepare('SELECT `id` FROM `'.$invlinetable.'` WHERE `invoiceid` = :oldinv ORDER BY `linenumber` ASC');
$sth4->bindParam(':oldinv',$oldinvoiceid);
$sth4->execute();
	while($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
{
$oldline = $row4['id'];
$sth2 = $pdocxn->prepare('UPDATE `'.$invlinetable.'` SET `linenumber` = :oldinvlinenumer WHERE `id` = :oldline');
$sth2->bindParam(':oldinvlinenumer',$oldinvlinenum);
$sth2->bindParam(':oldline',$oldline);
$sth2->execute();
$oldinvlinenum ++;
}
}
$split = '0';
}
}



/*if($_POST['servicesubmit']&&$_POST['servicesubmit'] == '1')
{
$serviceid  = $_POST['serviceid'];
$cost  = $_POST['cost'];
$servicetitle = $_POST['servicetitle'];
$servicenote = $_POST['servicenote'];

$sth3 = $pdocxn->prepare('INSERT INTO `'.$invlinetable.'`(`invoiceid`,`amount`,`linenumber`,`comment`,`serviceid`,`totallineamount`,`qty`)VALUES(:invoiceid,:amount,:linenumber,:comment,:serviceid,:totallineamount,\'1\')');
$sth3->bindParam(':totallineamount',$cost);
$sth3->bindParam(':invoiceid',$invoiceid);
$sth3->bindParam(':serviceid',$serviceid);
$sth3->bindParam(':amount',$cost);
$sth3->bindParam(':comment',$servicetitle);
$sth3->bindParam(':linenumber',$newlinenumber);
$sth3->execute();
$newlinenumber ++;
//addlinenumber
if($servicenote > '0')
{
$sth3 = $pdocxn->prepare('INSERT INTO `'.$invlinetable.'`(`invoiceid`,`amount`,`linenumber`,`comment`,`serviceid`,`totallineamount`)VALUES(:invoiceid,\'0\',:linenumber,:comment,:serviceid,\'0\')');
$sth3->bindParam(':invoiceid',$invoiceid);
$sth3->bindParam(':serviceid',$serviceid);
$sth3->bindParam(':comment',$servicenote);
$sth3->bindParam(':linenumber',$newlinenumber);
$sth3->execute();
$newlinenumber ++;
//addlinenumber
}
}
 * 
 */
if($_POST['inventorysubmit']&&$_POST['inventorysubmit'] == '1')
{
if(isset($scheduleid))
{
	$invoiceid = $_POST['scheduleid'];
}
global $changetax;
$changetax = '1';
//inventory submit
$partid = $_POST['partid'];
$getlinesql = 'SELECT `linenumber` FROM `'.$invlinetable.'` WHERE `invoiceid` = :invoiceid ORDER BY `linenumber` DESC LIMIT 1';
$getlinesth = $pdocxn->prepare($getlinesql);
$getlinesth->bindParam(':invoiceid',$invoiceid);
$getlinesth->execute();
if ($getlinesth->rowCount() > 0)
{
	while($getlinerow = $getlinesth->fetch(PDO::FETCH_ASSOC))
	{
	$oldmaxline = $getlinerow['linenumber'];
	$newlinenumber = $oldmaxline + '1';
	}
}else{
	$newlinenumber = '1';
}
$qty = $_POST['qty'];
if($fet < '.001')
{
	$fet = '0';
}
if($invoiceid < '1')
{
	if(isset($_POST['accountid']))
	{
		$accountid = $_POST['accountid'];
	}else{
		$accountid = '0';
	}
$typeid = $_POST['type'];
$sth1 = $pdocxn->prepare('SELECT `id` FROM `'.$invtable.'` ORDER BY `id` DESC LIMIT 1');
$sth1->execute();
$row1 = $sth1->fetch(PDO::FETCH_ASSOC);
$lastinvid = $row1['id'];
$invoiceid = $lastinvid + '1';
$sth2 = $pdocxn->prepare('INSERT INTO `'.$invtable.'`(`id`,`userid`,`type`,`location`,`creationdate`,`accountid`) VALUES (:id,:userid,:type,:location,:creationdate,:accountid)');
$sth2->bindParam(':id',$invoiceid);
$sth2->bindParam(':userid',$currentid);
$sth2->bindParam(':type',$typeid);
$sth2->bindParam(':location',$currentlocationid);
$sth2->bindParam(':creationdate',$currentdate);
$sth2->bindParam(':accountid',$accountid);
$sth2->execute();
}

$sql1 = 'SELECT * FROM `inventory` WHERE `id` = :partid';
$sth1 = $pdocxn->prepare($sql1);
$sth1->bindParam(':partid',$partid);
$sth1->execute();
if ($sth1->rowCount() > 0)
{
	while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$inventorysubtypeid = $row1['subtypeid'];
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
	if($ply > '1')
	{
		$displayply = "".$ply." ply";
	}
	$sql2 = "SELECT `id`,`price1` FROM `inventory_price` WHERE `partid` = :partid AND `siteid` = :siteid";
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':partid',$partid);
$sth2->bindParam(':siteid',$currentlocationid);
$sth2->execute();
while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
{
$price = $row2['price1'];
}
$sql2= "SELECT `brand` FROM `tire_manufacturers` WHERE `id` = '$brandid'";
$query2 = mysqli_query($sqlicxn,$sql2);
while ($row2 = mysqli_fetch_assoc($query2))
	{
	$brand = $row2['brand'];
	$description = $articleid.", ".$size." ".$brand." ".$model." ".$load_index.$speed." ".$displayply;
	}
	}}

//record the inventory transaction in inventory_transactions
//$sql11 = 'INSERT INTO `inventory_transactions` (`invoiceid`,`qty`,`amount`,`partid`,`transactiontype`,`location`,`accountid`)VALUES('.$invoiceid.','.$qty.','.$price.','.$partid.','.$typeid.','.$currentlocationid.','.$accountid.')';
//echo $sql11;
$appttype = '51';
$sth5 = $pdocxn->prepare('INSERT INTO `inventory_transactions` (`invoiceid`,`qty`,`amount`,`partid`,`transactiontype`,`location`,`accountid`)VALUES(:invoiceid,:qty,:amount,:partid,:transactiontype,:location,:accountid)');
$sth5->bindParam(':invoiceid',$invoiceid);
$sth5->bindParam(':qty',$qty);
$sth5->bindParam(':amount',$price);
$sth5->bindParam(':partid',$partid);
$sth5->bindParam(':transactiontype',$appttype);
$sth5->bindParam(':location',$currentlocationid);
$sth5->bindParam(':accountid',$accountid);
$sth5->execute()or die(print_r($sth5->errorInfo(), true));

$lineitem_typeid = '1';
$lineitem_saletype = '1';

$totallineamount = $qty * $price;
$sth3 = $pdocxn->prepare('INSERT INTO `'.$invlinetable.'`(`invoiceid`,`qty`,`amount`,`linenumber`,`comment`,`fet`,`totallineamount`,`partid`,`lineitem_typeid`,`lineitem_subtypeid`,`lineitem_saletype`)VALUES(:invoiceid,:qty,:amount,:linenumber,:comment,:fet,:totallineamount,:partid,:lineitem_typeid,:lineitem_subtypeid,:lineitem_saletype)');
$sth3->bindParam(':totallineamount',$totallineamount);
$sth3->bindParam(':invoiceid',$invoiceid);
$sth3->bindParam(':qty',$qty);
$sth3->bindParam(':amount',$price);
$sth3->bindParam(':comment',$description);
$sth3->bindParam(':fet',$fet);
$sth3->bindParam(':linenumber',$newlinenumber);
$sth3->bindParam(':partid',$partid);
$sth3->bindParam(':lineitem_typeid',$lineitem_typeid);
$sth3->bindParam(':lineitem_subtypeid',$inventorysubtypeid);
$sth3->bindParam(':lineitem_saletype',$lineitem_saletype);
$sth3->execute();
$newlinenumber ++;

$lastlineid = $pdocxn->lastInsertId();
$taxamount = $totallineamount*$taxmultiply;
$sql3 = "INSERT INTO `tax_trans_appt`(`transid`,`taxamount`,`lineid`) VALUES (:invoiceid,:taxamount,:lineid)";
$sth3 = $pdocxn->prepare($sql3);
$sth3->bindParam(':invoiceid',$invoiceid);
$sth3->bindParam(':taxamount',$taxamount);
$sth3->bindParam(':lineid',$lastlineid);
$sth3->execute();
//addlinenumber
}
if($_POST['packageid']&&$_POST['packageid'] > '0')
{
global $changetax;
$changetax = '1';
//package/service submit
$packageid = $_POST['packageid'];
$sth4 = $pdocxn->prepare('SELECT * FROM `packages` WHERE `id` = :packageid');
$sth4->bindParam(':packageid',$packageid);
$sth4->execute();
while($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
{
$packagetitle = $row4['description'];
$tpkcost1 = $row4['price1'];
$tpkcost2 = $row4['price2'];
$tpkcost3 = $row4['price3'];
$tpkcost4 = $row4['price4'];
$tpkcost5 = $row4['price5'];
$tpkcost6 = $row4['price6'];
$oil = $row4['oil'];
$align = $row4['align'];
$brakes = $row4['brakes'];
$tire = $row4['tire'];
if($pkcost == '1')
{
$lr1cost = $tpkcost1;
}
if($pkcost == '2')
{
$lr1cost = $tpkcost2;
}
if($pkcost == '3')
{
$lr1cost = $tpkcost3;
}
if($pkcost == '4')
{
$lr1cost = $tpkcost4;
}
if($pkcost == '5')
{
$lr1cost = $tpkcost5;
}
if($pkcost == '6')
{
$lr1cost = $tpkcost6;
}

$lineitem_subtypeid = '1';
$lineitem_saletype = '3';
$lineitem_typeid = '4';
/*$linesth2 = $pdocxn->prepare('INSERT INTO `'.$invlinetable.'`(`invoiceid`,`qty`,`amount`,`linenumber`,`comment`,`totallineamount`,`lineitem_typeid`,`lineitem_subtypeid`,`lineitem_saletype`)VALUES(:invoiceid,\'1\',:amount,:linenumber,:comment,:totallineamount,:lineitem_typeid,:lineitem_subtypeid,:lineitem_saletype)');
$linesth2->bindParam(':invoiceid',$invoiceid);
$linesth2->bindParam(':totallineamount',$lr1cost);
$linesth2->bindParam(':amount',$lr1cost);
$linesth2->bindParam(':comment',$packagetitle);
$linesth2->bindParam(':linenumber',$newlinenumber);
$linesth2->bindParam(':lineitem_typeid',$lineitem_typeid);
$linesth2->bindParam(':lineitem_subtypeid',$lineitem_subtypeid);
$linesth2->bindParam(':lineitem_saletype',$lineitem_saletype);
$linesth2->execute();

$lastlineid = $pdocxn->lastInsertId();
$taxamount = $totallineamount*$taxmultiply;
$sql3 = "INSERT INTO `tax_trans_appt`(`transid`,`taxamount`,`lineid`) VALUES (:invoiceid,:taxamount,:lineid)";
$sth3 = $pdocxn->prepare($sql3);
$sth3->bindParam(':invoiceid',$invoiceid);
$sth3->bindParam(':taxamount',$taxamount);
$sth3->bindParam(':lineid',$lastlineid);
$sth3->execute();
$newlinenumber ++;
*/
//addlinenumber


//ttt

$oil = $row4['oil'];
if($oil > '0')
{
$sql7 = 'UPDATE `'.$invtable.'` SET `lof`=:oil WHERE `id`=:invoiceid';
$sth7 = $pdocxn->prepare($sql7);
$sth7->bindParam(':oil',$oil);
$sth7->bindParam(':invoiceid',$invoiceid);
$sth7->execute()or die(print_r($sth7->errorInfo(), true));
}
$align = $row4['align'];
if($align > '0')
{
	$sql7 = 'UPDATE `'.$invtable.'` SET `align`=:align WHERE `id`=:invoiceid';
	$sth7 = $pdocxn->prepare($sql7);
	$sth7->bindParam(':align',$align);
	$sth7->bindParam(':invoiceid',$invoiceid);
	$sth7->execute()or die(print_r($sth7->errorInfo(), true));
}
$brakes = $row4['brakes'];
if($brakes > '0')
{
	$sql7 = 'UPDATE `'.$invtable.'` SET `brakes`=:brakes WHERE `id`=:invoiceid';
	$sth7 = $pdocxn->prepare($sql7);
	$sth7->bindParam(':brakes',$brakes);
	$sth7->bindParam(':invoiceid',$invoiceid);
	$sth7->execute()or die(print_r($sth7->errorInfo(), true));
}
$tire = $row4['tire'];
if($tire > '0')
{
	$sql7 = 'UPDATE `'.$invtable.'` SET `tires`=:tire WHERE `id`=:invoiceid';
	$sth7 = $pdocxn->prepare($sql7);
	$sth7->bindParam(':tire',$tire);
	$sth7->bindParam(':invoiceid',$invoiceid);
	$sth7->execute()or die(print_r($sth7->errorInfo(), true));
}

}
$linesth1 = $pdocxn->prepare('SELECT * FROM `package_items` WHERE `packageid` = :packageid ORDER BY `linenumber` ASC');
$linesth1->bindParam(':packageid',$packageid);
$linesth1->execute();
while($linerow1 = $linesth1->fetch(PDO::FETCH_ASSOC))
{
$packagetitle = $linerow1['description'];
$lr1cost1 = $linerow1['price1'];
$lr1cost2 = $linerow1['price2'];
$lr1cost3 = $linerow1['price3'];
$lr1cost4 = $linerow1['price4'];
$lr1cost5 = $linerow1['price5'];
$lr1cost6 = $linerow1['price6'];
$note = $linerow1['note'];
$pkgqty = $linerow1['qty'];
$printflag = $linerow1['printflag'];
$pkcost = '1';
if($pkcost == '1')
{
$lr1cost = $lr1cost1;
}
if($pkcost == '2')
{
$lr1cost = $lr1cost2;
}
if($pkcost == '3')
{
$lr1cost = $lr1cost3;
}
if($pkcost == '4')
{
$lr1cost = $lr1cost4;
}
if($pkcost == '5')
{
$lr1cost = $lr1cost5;
}
if($pkcost == '6')
{
$lr1cost = $lr1cost6;
}
$lineitem_subtypeid = '1';
$lineitem_saletype = '3';
$lineitem_typeid = '4';
$totallineamount = $lr1cost * $pkgqty;
//$linesth2 = $pdocxn->prepare('INSERT INTO `'.$invlinetable.'`(`invoiceid`,`qty`,`amount`,`linenumber`,`comment`,`totallineamount`,`lineitem_typeid`,`lineitem_subtypeid`,`lineitem_saletype`)VALUES(:invoiceid,:qty,:amount,:linenumber,:comment,:totallineamount,:lineitem_typeid,:lineitem_subtypeid,:lineitem_saletype)');
$linesth2 = $pdocxn->prepare('INSERT INTO `'.$invlinetable.'`(`invoiceid`,`qty`,`amount`,`linenumber`,`comment`,`totallineamount`,`lineitem_typeid`,`lineitem_subtypeid`,`lineitem_saletype`,`printflag`)VALUES(:invoiceid,:qty,:amount,:linenumber,:comment,:totallineamount,:lineitem_typeid,:lineitem_subtypeid,:lineitem_saletype,:printflag)');
$linesth2->bindParam(':invoiceid',$invoiceid);
$linesth2->bindParam(':qty',$pkgqty);
$linesth2->bindParam(':totallineamount',$totallineamount);
$linesth2->bindParam(':amount',$lr1cost);
$linesth2->bindParam(':comment',$packagetitle);
$linesth2->bindParam(':linenumber',$newlinenumber);
$linesth2->bindParam(':lineitem_typeid',$lineitem_typeid);
$linesth2->bindParam(':lineitem_subtypeid',$lineitem_subtypeid);
$linesth2->bindParam(':lineitem_saletype',$lineitem_saletype);
$linesth2->bindParam(':printflag',$printflag);
$linesth2->execute();

$lastlineid = $pdocxn->lastInsertId();
$taxamount = $totallineamount*$taxmultiply;
$sql3 = "INSERT INTO `tax_trans_appt`(`transid`,`taxamount`,`lineid`) VALUES (:invoiceid,:taxamount,:lineid)";
$sth3 = $pdocxn->prepare($sql3);
$sth3->bindParam(':invoiceid',$invoiceid);
$sth3->bindParam(':taxamount',$taxamount);
$sth3->bindParam(':lineid',$lastlineid);
$sth3->execute();
$newlinenumber ++;
//addlinenumber
}
}













if($_POST['servicesubmit']&&$_POST['servicesubmit'] > '0')
{
global $changetax;
$changetax = '1';
//package/service submit
$serviceid = $_POST['serviceid'];
$sth4 = $pdocxn->prepare('SELECT * FROM `services` WHERE `id` = :serviceid');
$sth4->bindParam(':serviceid',$serviceid);
$sth4->execute();
while($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
{
$packagetitle = $row4['description'];
$tpkcost1 = $row4['price1'];
$tpkcost2 = $row4['price2'];
$tpkcost3 = $row4['price3'];
$tpkcost4 = $row4['price4'];
$tpkcost5 = $row4['price5'];
$tpkcost6 = $row4['price6'];
}
$linesth1 = $pdocxn->prepare('SELECT * FROM `package_items` WHERE `packageid` = :packageid ORDER BY `linenumber` ASC');
$linesth1->bindParam(':packageid',$packageid);
$linesth1->execute();
while($linerow1 = $linesth1->fetch(PDO::FETCH_ASSOC))
{
$packagetitle = $linerow1['description'];
$lr1cost1 = $linerow1['price1'];
$lr1cost2 = $linerow1['price2'];
$lr1cost3 = $linerow1['price3'];
$lr1cost4 = $linerow1['price4'];
$lr1cost5 = $linerow1['price5'];
$lr1cost6 = $linerow1['price6'];
$note = $linerow1['note'];
$pkgqty = $linerow1['qty'];
$pkcost = '1';
if($pkcost == '1')
{
$lr1cost = $lr1cost1;
}
if($pkcost == '2')
{
$lr1cost = $lr1cost2;
}
if($pkcost == '3')
{
$lr1cost = $lr1cost3;
}
if($pkcost == '4')
{
$lr1cost = $lr1cost4;
}
if($pkcost == '5')
{
$lr1cost = $lr1cost5;
}
if($pkcost == '6')
{
$lr1cost = $lr1cost6;
}
$lineitem_subtypeid = '1';
$lineitem_saletype = '3';
$lineitem_typeid = '4';
$totallineamount = $lr1cost * $pkgqty;
$linesth2 = $pdocxn->prepare('INSERT INTO `'.$invlinetable.'`(`invoiceid`,`qty`,`amount`,`linenumber`,`comment`,`totallineamount`,`lineitem_typeid`,`lineitem_subtypeid`,`lineitem_saletype`)VALUES(:invoiceid,:qty,:amount,:linenumber,:comment,:totallineamount,:lineitem_typeid,:lineitem_subtypeid,:lineitem_saletype)');
$linesth2->bindParam(':invoiceid',$invoiceid);
$linesth2->bindParam(':qty',$pkgqty);
$linesth2->bindParam(':totallineamount',$totallineamount);
$linesth2->bindParam(':amount',$lr1cost);
$linesth2->bindParam(':comment',$packagetitle);
$linesth2->bindParam(':linenumber',$newlinenumber);
$linesth2->bindParam(':lineitem_typeid',$lineitem_typeid);
$linesth2->bindParam(':lineitem_subtypeid',$lineitem_subtypeid);
$linesth2->bindParam(':lineitem_saletype',$lineitem_saletype);
$linesth2->execute();

$lastlineid = $pdocxn->lastInsertId();
$taxamount = $totallineamount*$taxmultiply;
$sql3 = "INSERT INTO `tax_trans_appt`(`transid`,`taxamount`,`lineid`) VALUES (:invoiceid,:taxamount,:lineid)";
$sth3 = $pdocxn->prepare($sql3);
$sth3->bindParam(':invoiceid',$invoiceid);
$sth3->bindParam(':taxamount',$taxamount);
$sth3->bindParam(':lineid',$lastlineid);
$sth3->execute();
$newlinenumber ++;
//addlinenumber
}
}

if($_POST['newcommentform']&&$_POST['newcommentform'] == '1')
{
global $changetax;
$changetax = '1';
//submit newcomment
	$qty = $_POST['newqty'];
	$amount = $_POST['newprice'];
	$comment = $_POST['newcomment'];
	$fet = $_POST['newfet'];
	if($fet < '.01')
	{
		$fet = '0.00';
	}
	$lineitem_typeid = '5';
	$lineitem_subtypeid = '1';
	$lineitem_saletype = '3';
	$totallineamount = $qty * $amount;
$sth1 = $pdocxn->prepare('INSERT INTO `'.$invlinetable.'`(`invoiceid`,`qty`,`amount`,`linenumber`,`comment`,`fet`,`totallineamount`,`lineitem_typeid`,`lineitem_subtypeid`,`lineitem_saletype`)VALUES(:invoiceid,:qty,:amount,:linenumber,:comment,:fet,:totallineamount,:lineitem_typeid,:lineitem_subtypeid,:lineitem_saletype)');
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->bindParam(':totallineamount',$totallineamount);
$sth1->bindParam(':qty',$qty);
$sth1->bindParam(':amount',$amount);
$sth1->bindParam(':linenumber',$newlinenumber);
$sth1->bindParam(':comment',$comment);
$sth1->bindParam(':fet',$fet);
$sth1->bindParam(':lineitem_typeid',$lineitem_typeid);
$sth1->bindParam(':lineitem_subtypeid',$lineitem_subtypeid);
$sth1->bindParam(':lineitem_saletype',$lineitem_saletype);
$sth1->execute() or die(print_r($sth1->errorInfo(), true));
$newlinenumber ++;
$lastlineid = $pdocxn->lastInsertId();
$taxamount = $totallineamount*$taxmultiply;
$sql3 = "INSERT INTO `tax_trans_appt`(`transid`,`taxamount`,`lineid`) VALUES (:invoiceid,:taxamount,:lineid)";
$sth3 = $pdocxn->prepare($sql3);
$sth3->bindParam(':invoiceid',$invoiceid);
$sth3->bindParam(':taxamount',$taxamount);
$sth3->bindParam(':lineid',$lastlineid);
$sth3->execute();
//addlinenumber
}
//quick add



if($_POST['quickadd']&&$_POST['quickadd'] == '1')
{
$invoiceid = $_POST['invoiceid'];
$qty = '1';
if(!empty($_POST['qa'])){
		// Loop to store and display values of individual checked checkbox.
foreach($_POST['qa'] as $qaid){
$qapricepost = 'qa'.$qaid.'price';
if(isset($_POST[$qapricepost]))
			{
$qaprice = $_POST[$qapricepost];
			}else{$qaprice = '0';
			}
$qapartpost = 'qa'.$qaid.'part';
if(isset($_POST[$qapartpost]))
			{
$qapart = $_POST[$qapartpost];
			}else{$qapart = '';
			}
$fet = '0.00';
$lineitem_typeid = '5';
$lineitem_subtypeid = '1';
$lineitem_saletype = '3';
$totallineamount = $qty * $qaprice;

			$sth2 = $pdocxn->prepare('SELECT `comment` FROM `quickadd` WHERE `id` = :qaid');
$sth2->bindParam(':qaid',$qaid);
$sth2->execute();
while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
{
$qacomment = $row2['comment'];
echo "<td><label for=\"".$optionid."\" >".$optiontitle.": <input type=\"checkbox\" id=\"".$optionid."\" name=\"qa[]\" value=\"".$optionid."\" ></label></td>";

			$sth1 = $pdocxn->prepare('INSERT INTO `'.$invlinetable.'`(`invoiceid`,`qty`,`amount`,`linenumber`,`comment`,`fet`,`totallineamount`,`lineitem_typeid`,`lineitem_subtypeid`,`lineitem_saletype`)VALUES(:invoiceid,:qty,:amount,:linenumber,:comment,:fet,:totallineamount,:lineitem_typeid,:lineitem_subtypeid,:lineitem_saletype)');
			$sth1->bindParam(':invoiceid',$invoiceid);
			$sth1->bindParam(':totallineamount',$totallineamount);
			$sth1->bindParam(':qty',$qty);
			$sth1->bindParam(':amount',$qaprice);
			$sth1->bindParam(':linenumber',$newlinenumber);
			$sth1->bindParam(':comment',$qacomment);
			$sth1->bindParam(':fet',$fet);
			$sth1->bindParam(':lineitem_typeid',$lineitem_typeid);
			$sth1->bindParam(':lineitem_subtypeid',$lineitem_subtypeid);
			$sth1->bindParam(':lineitem_saletype',$lineitem_saletype);
			$sth1->execute() or die(print_r($sth1->errorInfo(), true));
			$newlinenumber ++;

			$lastlineid = $pdocxn->lastInsertId();
			$taxamount = $totallineamount*$taxmultiply;
			$sql3 = "INSERT INTO `tax_trans_appt`(`transid`,`taxamount`,`lineid`) VALUES (:invoiceid,:taxamount,:lineid)";
			$sth3 = $pdocxn->prepare($sql3);
			$sth3->bindParam(':invoiceid',$invoiceid);
			$sth3->bindParam(':taxamount',$taxamount);
			$sth3->bindParam(':lineid',$lastlineid);
			$sth3->execute() or die(print_r($sth3->errorInfo(), true));
}
		}
		}
}
if(!isset($_GET['invoiceid']))
{
	header('location:'.$pagelink.'?invoiceid='.$invoiceid.'');
}
//get final info from db
$sth3 = $pdocxn->prepare('SELECT * FROM `'.$invtable.'` WHERE id = :inv');
$sth3->bindParam(':inv',$invoiceid);
$sth3->execute();
$row3 = $sth3->fetch(PDO::FETCH_ASSOC);
$schedule = $row3['schedule'];
$accountid = $row3['accountid'];
$typeid = $row3['type'];
$invuserid = $row3['userid'];
$vehicleid = $row3['vehicleid'];
$mileagein = $row3['mileagein'];
$mileageout = $row3['mileageout'];
$location = $row3['location'];
$invoicenumber = $row3['invoiceid'];
$taxgroup = $row3['taxgroup'];
$invoicedate = $row3['date'];
$creationdate = $row3['creationdate'];
$convertedid = $row3['invoiceid'];
$creationdate2 = new DateTime($creationdate);
$displaycreationdate = $creationdate2->format('l, M j g:i');
$invoicedate2 = new DateTime($invoicedate);
$displayinvoicedate = $invoicedate2->format('n/j/Y');
$displayinvoicedate2 = $invoicedate2->format('Y-m-d');
$displayinvoicedatetime = $invoicedate2->format('l, M j g:i');
$schedulelink = "&schedule=".$schedule;


$currentlength = $row3['length'];
$currentstatus = $row3['status'];
$schedule = $row3['schedule'];
if($currentlength>'0')
{
	if($schedule == '1')
{
	if($currentlength == '1')
	{
		$displaylength = '1/2 Hour';
	}
	if($currentlength == '2')
	{
		$displaylength = '1 Hour';
	}
	if($currentlength == '3')
	{
		$displaylength = '1.5 Hour';
	}
	if($currentlength == '4')
	{
		$displaylength = '2 Hours';
	}
	if($currentlength == '5')
	{
		$displaylength = '2.5 Hours';
	}
	if($currentlength == '6')
	{
		$displaylength = '3 Hours';
	}
	if($currentlength == '7')
	{
		$displaylength = '3.5 Hours';
	}
	if($currentlength == '8')
	{
		$displaylength = '4 Hours';
	}
	if($currentlength == '9')
	{
		$displaylength = '4.5 Hours';
	}
	if($currentlength == '10')
	{
		$displaylength = '5 Hours';
	}
	if($currentlength == '11')
	{
		$displaylength = '5.5 Hours';
	}
	if($currentlength == '12')
	{
		$displaylength = '6 Hours';
	}
	if($currentlength == '13')
	{
		$displaylength = '6.5 Hours';
	}
	if($currentlength == '14')
	{
		$displaylength = '7 Hours';
	}
	if($currentlength == '15')
	{
		$displaylength = '7.5 Hours';
	}
	if($currentlength == '16')
	{
		$displaylength = '8 Hours';
	}
	if($currentlength == '17')
	{
		$displaylength = '8.5 Hours';
	}
	if($currentlength == '18')
	{
		$displaylength = '9 Hours';
	}
	if($currentlength == '19')
	{
		$displaylength = '9.5 Hours';
	}
	if($currentlength == '20')
	{
		$displaylength = '10 Hours';
	}
}
else{
	if($currentlength == '1')
	{
		$displaylength = '1 Hour';
	}
	if($currentlength == '2')
	{
		$displaylength = '2 Hours';
	}
	if($currentlength == '3')
	{
		$displaylength = '3 Hours';
	}
	if($currentlength == '4')
	{
		$displaylength = '4 Hours';
	}
	if($currentlength == '5')
	{
		$displaylength = '5 Hours';
	}
	if($currentlength == '6')
	{
		$displaylength = '6 Hours';
	}
	if($currentlength == '7')
	{
		$displaylength = '7 Hours';
	}
	if($currentlength == '8')
	{
		$displaylength = '8 Hours';
	}
	if($currentlength == '9')
	{
		$displaylength = '9 Hours';
	}
	if($currentlength == '10')
	{
		$displaylength = '10 Hours';
	}
}
}
else {
	$currentlength = '1';
	$displaylength = "Select Length";
}
if($currentstatus >'0')
{
		if($currentstatus == '1')
		{
			$displaystatus = 'Appointment Created';
		}
		if($currentstatus == '2')
		{
			$displaystatus = 'Vehicle is Here';
		}
		if($currentstatus == '3')
		{
			$displaystatus = 'Needs Attention';
		}
		if($currentstatus == '4')
		{
			$displaystatus = 'Parts Ordered';
		}
		if($currentstatus == '5')
		{
			$displaystatus = 'Vehicle will be dropped off';
		}
		if($currentstatus == '6')
		{
			$displaystatus = 'Waiting on Customer';
		}
		if($currentstatus == '7')
		{
			$displaystatus = 'Vehicle Needs a Test Drive';
		}
		if($currentstatus == '8')
		{
			$displaystatus = 'Feeling Blue';
		}
		if($currentstatus == '10')
		{
			$displaystatus = 'Vehicle is Completed';
        }
}
else {
	$currentstatus = '1';
	$displaystatus = "Appointment Created";
}
$sth4 = $pdocxn->prepare('SELECT SUM(`totallineamount`) AS `invsubtotal` FROM `'.$invlinetable.'` WHERE `invoiceid` = :invoiceid');
$sth4->bindParam(':invoiceid',$invoiceid);
$sth4->execute();
$linecount = $sth4->rowCount();
	while($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
{
$invoicesubtotal = $row4['invsubtotal'];
}
$sth4a = $pdocxn->prepare('SELECT `username` FROM `employees` WHERE id = :userid');
$sth4a->bindParam(':userid',$invuserid);
$sth4a->execute();
while($row4a = $sth4a->fetch(PDO::FETCH_ASSOC))
{
$invsalesman = $row4a['username'];
}

$sth4a = $pdocxn->prepare('SELECT `storename` FROM `locations` WHERE id = :locationid');
$sth4a->bindParam(':locationid',$location);
$sth4a->execute();
while($row4a = $sth4a->fetch(PDO::FETCH_ASSOC))
{
$invstorename = $row4a['storename'];
}

if($accountid > '0')
{
$sth4 = $pdocxn->prepare('SELECT * FROM `accounts` WHERE `accountid` = :acct');
$sth4->bindParam(':acct',$accountid);
$sth4->execute();
while($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
{
$fname = $row4['firstname'];
$lname = $row4['lastname'];
$address = $row4['address'];
$address2 = $row4['address2'];
$city = $row4['city'];
$state = $row4['state'];
$zip = $row4['zip'];
$phone1 = $row4['phone1'];
$phone2 = $row4['phone2'];
$phone3 = $row4['phone3'];
$phone4 = $row4['phone4'];
$contact1 = $row4['contact1'];
$contact2 = $row4['contact2'];
$contact3 = $row4['contact3'];
$contact4 = $row4['contact4'];
$daddress = $address." ".$address2;
$dcsz = $city.", ".$state." ".$zip;
$fax = $row4['fax'];
$email = $row4['email'];
$creditlimit = $row4['creditlimit'];
$taxid = $row4['taxid'];
$priceclass = $row4['priceclass'];
$taxclass = $row4['taxclass'];
$nationalaccount = $row4['nationalaccount'];
$requirepo = $row4['requirepo'];
$accounttype = $row4['accounttype'];
$flag = $row4['flag'];
$comment = $row4['comment'];
$insertdate = $row4['insertdate'];
$lastactivedate = $row4['lastactivedate'];

$sth4 = $pdocxn->prepare('SELECT `store1`,`store2`,`store3`,`store4`,`store5`,`store6`,`store7`,`store8`,`store9`,`store10` FROM `accountbalance` WHERE `accountid` = :acct');
$sth4->bindParam(':acct',$accountid);
$sth4->execute();
while($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
{
$store1 = $row4['store1'];
$store2 = $row4['store2'];
$store3 = $row4['store3'];
$store4 = $row4['store4'];
$store5 = $row4['store5'];
$store6 = $row4['store6'];
$store7 = $row4['store7'];
$store8 = $row4['store8'];
$store9 = $row4['store9'];
$store10 = $row4['store10'];
$currentstorebalance = ${'store'.$currentlocationid};
$currentaccountbalance = $store1+$store2+$store3+$store4+$store5+$store6+$store7+$store8+$store9+$store10;
}
if($lastactivedate > '1')
{
$lastactivedate2 = new DateTime($lastactivedate);
$dlastactivedate = $lastactivedate2->format('n/j/Y');
}
else{
	$dlastactivedate = 'N/A';
}
$fullname = $fname." ".$lname;
$dcustomerinfo1 = "<font color=\"blue\"><b>".$fullname."</b></font>";
$dcsz2 = "<font color=\"blue\">".$dcsz."</font>";
//$dcustomerinfo1 = "<font color=\"blue\">".$fullname."</font> <a href=\"\" class=\"no-decoration\"><input type=\"button\" class=\"smallbutton\" value=\"(last active ".$dlastactivedate.")\"></a><br /><font color=\"blue\">".$phone1."</font>";
$dcustomerinfo2 = "<font color=\"blue\">".$daddress."</font>";
if($accountid < '1')
{
$dcustomerinfo = "No customer selected";
}
if($phone1 > '0')
	{
if($contact1 > '0')
{
$dcontact1 = "(".$contact1.")";
}else{
$dcontact1 = '';
}
		$dphone1 = "<font color=\"blue\">Phone 1: ".$phone1."&nbsp;".$dcontact1."</font>";
	}
	else
		{
			$dphone1 = "";
		}
if($phone2 > '0')
	{
if($contact2 > '0')
{
$dcontact2 = "(".$contact2.")";
}else{
$dcontact2 = '';
}
		$dphone2 = "<font color = \"blue\">Phone 2: ".$phone2."&nbsp;".$dcontact2."</font>";
	}
	else
		{
			$dphone2 = "";
		}
if($phone3 > '0')
	{
		$dphone3 = "<tr><td class=\"left\">Phone 3: ".$phone3."</td><td>Contact: ".$contact3."</td></tr>";
	}
	else
		{
			$dphone3 = "";
		}
if($phone4 > '0')
	{
		$dphone4 = "<tr><td class=\"left\">Phone 4: ".$phone4."</td><td>Contact: ".$contact4."</td></tr>";
	}
	else
		{
			$dphone4 = "";
		}
if($fax > '0')
	{
		$dfax = "<tr><td colspan=\"2\" class=\"left\">Fax: ".$fax."</td></tr>";
	}
	else
		{
			$dfax = "";
		}
if($creditlimit > '0')
	{
		$creditlimit = $creditlimit;
	}
	else
		{
			$creditlimit = "0";
		}
if($requirepo == '1')
	{
		$requirepo = "Yes";
	}
	else
		{
			$requirepo = "No";
		}
if($priceclass == '1')
	{
		$dpriceclass = "Consumer";
	}
	else
		{
			$dpriceclass = "Resale";
		}
}
$sql5 = 'SELECT * FROM `vehicles` WHERE `id` = :vehicleid';
$sth5 = $pdocxn->prepare($sql5);
$sth5->bindParam(':vehicleid',$vehicleid);
$sth5->execute();
if ($sth5->rowCount() > 0)
{
while($row5 = $sth5->fetch(PDO::FETCH_ASSOC))
	{
	$currentvehicleyear = $row5['year'];
	$currentvehiclemodel = $row5['model'];
	$currentvehiclemake = $row5['make'];
	$year = $row5['year'];
	$model = $row5['model'];
	$make = $row5['make'];
	$currentvin1 = $row5['vin'];
	$vinlen = strlen($currentvin1);
	if($vinlen = '16')
	{
		$v2 = substr($currentvin1,9,8);
        $v1 = substr($currentvin1,0,9);
		$currentvin = $v1."<u><b>".$v2."</b></u>";
	}else{
		$currentvin = $currentvin1;
	}
	$sobmodel = $row5['submodel'];
	$engine = $row5['engine'];
	$license = $row5['license'];
	$vehiclestate = $row5['state'];
	$currentlicense = $license;
	$currentstate = $vehiclestate;
	$description = $row5['description'];
if($year > '1')
{
$dvehicleinfo = "<b>".$year." ".$make." ".$model." ".$submodel." ".$engine."</b></td><td href=\"vehicleinfo\">VIN: ".$currentvin."</td><td href=\"vehicleinfo\">License: ".$license." (".$vehiclestate.")";
$dvehicleinfo1 = "<font color=\"red\">".$year." ".$make." ".$model." ".$submodel." ".$engine."</font>";
$dlicensestate = "<font color=\"red\">License: ".$license." (".$vehiclestate.")</font>";
$dvehicleinfo2 = "<b>".$year." ".$make." ".$model." ".$submodel." ".$engine."</b>";
$dvehicleinfo2nb = $year." ".$make." ".$model." ".$submodel." ".$engine;
$selectvehicleinfo = "".$year." ".$make." ".$model." ".$submodel." ".$engine."   VIN: ".$currentvin."  License: ".$license." ";
}
else
{
$dvehicleinfo = "".$description."</td><td href=\"vehicleinfo\">VIN: ".$currentvin."</td><td href=\"vehicleinfo\">License: ".$license." (".$vehiclestate.")";
$dvehicleinfo1 = "<font color=\"red\">".$description."</font>";
$dvehicleinfo1a = "<font color=\"red\">VIN: ".$currentvin."</font>";
$dlicensestate = "<font color=\"red\">License: ".$license." (".$vehiclestate.")</font>";
$dvehicleinfo2 = "<b>".$description."</b>";
$dvehicleinfo2nb = $description;
$selectvehicleinfo = "".$description."   VIN: ".$currentvin."   License: ".$license."";
}
$dmileage = "<font color=\"red\"><form name=\"updatemileage\" id=\"mileageform\" action=\"".$pagelink."\" method=\"post\">Mileage: <input type=\"textbox\" id=\"mileage\" name=\"mileage1\" value=\"".$mileagein."\" autocomplete=\"off\" size=\"5\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"updatemiles\" value=\"1\"><input type=\"submit\" name=\"submit\" class=\"xsmallbutton\" value=\"update miles\"></form></font>";
${"ip".$tri} = "<div id=\"$tri\"><div class=\"q1\"><table class=\"righttable\"><tr><td colspan=\"2\" class=\"center\"><a href=\"accounthistory.php?accountid=".$accountid."\">Account History</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"editaccounts.php?accountid=".$accountid."\">Edit Info</a></td></tr><tr><td colspan=\"2\" class=\"center\">$fullname</td></tr><tr><td colspan=\"2\" class=\"left\">$address&nbsp;$city,&nbsp;$state&nbsp;$zip</td></tr><tr><td colspan=\"2\">$address2</td></tr>".$dphone1."".$dphone2."".$dphone3."".$dphone4."".$dfax."<tr></table></div><div class=\"q3\"><table class=\"righttable\"><tr><th>Vehicle</th><th>License</th><th>VIN</th></tr><tr><td class=\"left\">$year&nbsp;&nbsp;$make&nbsp;&nbsp;$model</td><td></td><td>$currentvin</td></tr></table></div></div>";
//${"ip".$tri} = "<div id=\"$tri\"><div class=\"q1\"><table class=\"righttable\"><tr><td colspan=\"2\" class=\"center\">Info&nbsp;&nbsp;&nbsp;<a href=\"vehicles.php?accountid=".$accountid."\">Vehicles</a>&nbsp;&nbsp;&nbsp;<a href=\"editaccounts.php?accountid=".$accountid."\">Edit Info</a></td></tr><tr><td colspan=\"2\" class=\"center\">$fullname</td></tr><tr><td colspan=\"2\" class=\"left\">$address&nbsp;$city,&nbsp;$state&nbsp;$zip</td></tr><tr><td colspan=\"2\">$address2</td></tr>".$dphone1."".$dphone2."".$dphone3."".$dphone4."".$dfax."<tr><td>e-mail:</td><td>$email</td></tr><tr><td>Cedit Limit:</td><td>".$creditlimit."</td></tr><tr><td>Tax ID: ".$taxid."</td><td>Tax Class: ".$dtaxclass."</td></tr><tr><td>Price Class: ".$dpriceclass."</td><td>Require PO: ".$requirepo."</td></tr><tr><td colspan=\"2\">Account Note: ".$comment."</td></tr><tr><td>Last visited:</td><td>".$dlastactivedate."</td></tr></table></div></div>";
$tri ++;
}}
else{
$dvehicleinfo = "<input type=\"button\" class=\"btn-style\" value=\"Select Vehicle\"></td>";
$dvehicleinfo1 = "<input type=\"button\" class=\"save\" value=\"Select Vehicle\">";
}
}
else {
${"ip".$tri} = "<div id=\"$tri\"><div class=\"q1\"><table class=\"righttable\"><tr><td colspan=\"2\" class=\"center\"><a href=\"accounthistory.php?accountid=".$accountid."\">Account History</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"editaccounts.php?accountid=".$accountid."\">Edit Info</a></td></tr><tr><td colspan=\"2\" class=\"center\">$fullname</td></tr><tr><td colspan=\"2\" class=\"left\">$address&nbsp;$city,&nbsp;$state&nbsp;$zip</td></tr><tr><td colspan=\"2\">$address2</td></tr>".$dphone1."".$dphone2."".$dphone3."".$dphone4."".$dfax."<tr></table></div><div class=\"q3\"><table class=\"righttable\"><tr><th>Vehicle</th><th>License</th><th>VIN</th></tr><tr><td class=\"left\">$year&nbsp;&nbsp;$make&nbsp;&nbsp;$model</td><td></td><td>$currentvin</td></tr></table></div></div>";
}
$sql6 = 'SELECT `id`,`name` FROM `invoice_type` WHERE `id` = :typeid';
$sth6 = $pdocxn->prepare($sql6);
$sth6->bindParam(':typeid',$typeid);
$sth6->execute();
while($row6 = $sth6->fetch(PDO::FETCH_ASSOC))
{
	$typename = $row6['name'];
}
if($_POST['notesubmit'])
{
$note = $_POST['notes'];
$notetype = '1';
$sql7 = 'SELECT `id` FROM `notes-appt` WHERE `invoiceid` = :invoiceid AND `notetype` = \'1\'';
$sth7 = $pdocxn->prepare($sql7);
$sth7->bindParam(':invoiceid',$invoiceid);
$sth7->execute()or die(print_r($sth7->errorInfo(), true));
while($row7 = $sth7->fetch(PDO::FETCH_ASSOC))
{
	$noteid = $row7['id'];
}
if($id>'0')
{
$sql7 = 'UPDATE `notes-appt` SET `note`=:note WHERE `invoiceid`=:invoiceid)';
//$sql72 = 'INSERT INTO `notes`(`note`,`invoiceid`,`accountid`,`vehicleid`,`notetype`)VALUES(\''.$note.'\',\''.$invoiceid.'\',\''.$accountid.'\',\''.$vehicleid.'\',\''.$notetype.'\')';
//echo $sql72;
$sth7 = $pdocxn->prepare($sql7);
$sth7->bindParam(':note',$note);
$sth7->bindParam(':invoiceid',$invoiceid);
$sth7->execute()or die(print_r($sth7->errorInfo(), true));
}else{

$sql7 = 'INSERT INTO `notes-appt`(`note`,`invoiceid`,`accountid`,`vehicleid`,`notetype`)VALUES(:note,:invoiceid,:accountid,:vehicleid,:notetype)';
//$sql72 = 'INSERT INTO `notes`(`note`,`invoiceid`,`accountid`,`vehicleid`,`notetype`)VALUES(\''.$note.'\',\''.$invoiceid.'\',\''.$accountid.'\',\''.$vehicleid.'\',\''.$notetype.'\')';
//echo $sql72;
$sth7 = $pdocxn->prepare($sql7);
$sth7->bindParam(':note',$note);
$sth7->bindParam(':invoiceid',$invoiceid);
$sth7->bindParam(':accountid',$accountid);
$sth7->bindParam(':vehicleid',$vehicleid);
$sth7->bindParam(':notetype',$notetype);
$sth7->execute()or die(print_r($sth7->errorInfo(), true));

$qtya = '1';
$linenumberna = '1';
$typeida = '52';
$sth1a = $pdocxn->prepare('SELECT `id` FROM `customerinteractions` ORDER BY `id` DESC LIMIT 1');
$sth1a->execute();
$row1a = $sth1a->fetch(PDO::FETCH_ASSOC);
$lastinvida = $row1a['id'];
$invoiceida = $lastinvida + '1';
$sth2a = $pdocxn->prepare('INSERT INTO `customerinteractions`(`id`,`userid`,`type`,`location`,`creationdate`,`invoicedate`,`taxgroup`,`accountid`) VALUES (:id,:userid,:typeid,:location,:creationdate,:invoicedate,:taxgroup,:accountid)');
$sth2a->bindParam(':id',$invoiceida);
$sth2a->bindParam(':userid',$currentid);
$sth2a->bindParam(':typeid',$typeida);
$sth2a->bindParam(':location',$currentlocationid);
$sth2a->bindParam(':creationdate',$currentdate);
$sth2a->bindParam(':invoicedate',$currentday);
$sth2a->bindParam(':taxgroup',$taxgroup);
$sth2a->bindParam(':accountid',$accountid);
$sth2a->execute()or die(print_r($sth2a->errorInfo(), true));

$copysqla = "INSERT INTO `ci_line_items`(`invoiceid`,`linenumber`,`qty`,`comment`) VALUES (:invoiceid,:linenumber,:qty,:comment)";
$copystha = $pdocxn->prepare($copysqla);
$copystha->bindParam(':invoiceid',$invoiceida);
$copystha->bindParam(':linenumber',$linenumberna);
$copystha->bindParam(':qty',$qtya);
$copystha->bindParam(':comment',$note);
$copystha->execute()or die(print_r($copystha->errorInfo(), true));

}
}

$note = '';
$sql7 = 'SELECT `note` FROM `notes-appt` WHERE `invoiceid` = :invoiceid AND `notetype` = \'1\'';
$sth7 = $pdocxn->prepare($sql7);
$sth7->bindParam(':invoiceid',$invoiceid);
$sth7->execute();
while($row7 = $sth7->fetch(PDO::FETCH_ASSOC))
{
$databasenote = $row7['note'];
$note = stripslashes($databasenote);
}
//display html - submit
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
<title><?php echo $fullname.' - Appointment'; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" >
<meta http-equiv="cache-control" content="no-cache, must-revalidate, post-check=0, pre-check=0" />
  <meta http-equiv="cache-control" content="max-age=0" />
  <meta http-equiv="expires" content="0" />
  <meta http-equiv="expires" content="Tue, 01 Jan 2018 1:00:00 GMT" />
  <meta http-equiv="pragma" content="no-cache" />
<link rel="stylesheet" type="text/css" href="style/globalstyle.css" >
<link rel="stylesheet" type="text/css" href="style/invoicestyle2.css" >
<link rel="stylesheet" type="text/css" href="style/autocomplete.css" >
<script type="text/javascript" src="scripts/jquery-latest.js"></script>
<script src="http://code.jquery.com/jquery-1.10.2.js"></script>
  <script src="http://code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" >
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script type="text/javascript" src="scripts/script.js"></script>
<script src="scripts/autocomplete.js" type="text/javascript"></script>
  <script src="scripts/jquery-ui.js"></script>
<script type="text/javascript">
<!--  to hide script contents from old browsers
$(function(){
setAutoComplete("invoicecomment", "results", "invoicecomment-autocomplete.php?part=");
});
//--></script>
<script type="text/javascript">
$(document).ready(function(){
  $("#multiply1").click(function(){
    $("#unitprice").val(function(i,origText){
num = origText * "1.5";
    return num.toFixed(2); 
    });
  });
});
</script>
<script type="text/javascript">
$(document).ready(function(){
  $("#multiply2").click(function(){
    $("#unitprice").val(function(i,origText){
num = origText * "2";
    return num.toFixed(2);
    });
  });
});
</script>
</head>
<body>
<?php
if($location == '1')
{
echo "<div id=\"header\">";
}
else{
echo "<div id=\"header2\">";
}
?>
<table><tr><td>
<form action="appointment.php" method="post" name="voidinvoice">
<input type="hidden" name="void" value="1">
<input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>">
<input type="hidden" name="submit" value="1">
<input type="submit" class="cancel" value="Void <?php echo $typename; ?>" onclick="myFunction()"></form>
</td><td>
<?php
/*if($schedule == '1')
{
?>
<a href="schedule.php?r=<?php echo $r; ?>&selectedday=<?php echo $displayinvoicedate2; ?>" class="no-decoration">
<?php
}else{
?>
<a href="schedule2.php?r=<?php echo $r; ?>&selectedday=<?php echo $displayinvoicedate2; ?>" class="no-decoration">
<?php
}
*/
?>&nbsp;
<input type="button" class="save" value="Save" onclick="self.close()"></td>
<td>&nbsp;
<!--<a href="printappointment.php?invoiceid=<?php echo $invoiceid; ?>&siteid=<?php echo $currentlocationid; ?>">
<input type="button" class="save" alt="print" value="Print <?php echo $typename; ?>" name="submit"></a>-->
<input type="button" class="save" alt="print" value="Print" name="submit" onclick="window.open('printappointment.php?invoiceid=<?php echo $invoiceid; ?>&siteid=<?php echo $currentlocationid; ?>');self.close()">
&nbsp;</td>
<td>
<form action="emailappointment.php" method="post" name="emailinvoice">
<input type="hidden" name="email" value="1">
<input type="hidden" name="scheduleid" value="<?php echo $invoiceid; ?>">
<input type="hidden" name="submit" value="1">
<input type="submit" class="save" value="Email"></form></td>
<?php
if($convertedid > '0')
{
?>
<td>&nbsp;
<input type="button" class="save" value="Linked to Invoice"onclick="window.open('invoice.php?invoiceid=<?php echo $convertedid; ?>');self.close();">&nbsp;</td>
<?php
}else{
?>
<td>&nbsp;
<input type="button" class="save" value="Change to Invoice" onclick="window.open('invoice.php?scheduleid=<?php echo $invoiceid; ?>&loc=<?php echo $location; ?>');self.close();">&nbsp;</td>
<?php
}
?>
<td>
<form action="appointment.php" method="post" name="copyinvoice">
<input type="hidden" name="copy" value="1">
<input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>">
<input type="hidden" name="accountid" value="<?php echo $accountid; ?>">
<input type="hidden" name="vehicleid" value="<?php echo $vehicleid; ?>">
<input type="hidden" name="submit" value="1">
<input type="submit" class="save" alt="print" value="Copy <?php echo $typename; ?>" name="submit"></form></td>
<td>
<a href="schedule.php?change=1&id=<?php echo $invoiceid; ?>&acctid=<?php echo $accountid; ?>" class="no-decoration">
<input type="button" class="save" alt="print" value="Change Appointment Time" name="submit"></a></td>
</tr></table></div>
<div id="selecteduserfullwidth">
<form name="changesales" action="<?php echo $pagelink; ?>" method="POST">
<table id="floatleft"><tr>
<td class="currentuser">Salesperson:</td>
<td class="currentitem"><div class="styled-select black rounded">
<select name="userid" onchange="form.submit()">
        		<?php
        		 if($invuserid > '0')
        		{
        			echo "<option value=\"$invuserid\">$invsalesman</option>";
        		}
else {
echo "<option value=\"0\">Select Salesperson</option>";
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
</div>

<td class="currentstore">Store:</td><td class="currentitem">
<div class="styled-select black rounded"><select name="locationid" onchange="form.submit()"><?php
if($currentid > '0')
        		{
				echo "<option value=\"$location\">$invstorename</option>";
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
</select></div>
<input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>"><input type="hidden" name="changesales" value="1"></form>
</td><td>Date: <b><?php echo $displayinvoicedatetime; ?></b></td>
<td>Tax:</td><td>

<?php
echo "<form name=\"taxclassform\" action=\"".$pagelink."\" method=\"POST\">";
echo "<input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\">";
echo "<input type=\"hidden\" name=\"taxsubmit\" value=\"1\">";
echo "<select name=\"newtaxclass\" onchange=\"form.submit()\">";
$taxsql = "SELECT `description`,`id` FROM `tax_rate` WHERE `id` = :taxgroup";
$txsth = $pdocxn->prepare($taxsql);
$txsth->bindParam(':taxgroup',$taxgroup);
$txsth->execute();
while($taxrow1 = $txsth->fetch(PDO::FETCH_ASSOC))
{
$taxid = $taxrow1['id'];
$taxname = $taxrow1['description'];
echo "<option value=\"".$taxid."\">".$taxname."</option>";
}
$taxsql = "SELECT `description`,`id` FROM `tax_rate` WHERE `formdisplay` = '1'";
$txsth = $pdocxn->prepare($taxsql);
$txsth->execute();
while($taxrow1 = $txsth->fetch(PDO::FETCH_ASSOC))
{
$taxid = $taxrow1['id'];
$taxname = $taxrow1['description'];
echo "<option value=\"".$taxid."\">".$taxname."</option>";
}
echo "</select></form>";
?>
</td>
<?php
	if($schedule == '1')
	{
?>
<td>Appt Length</td><td><form name="lengthform" id="lengthform" action="appointment.php" method="POST"><input type="hidden" name="lengthsubmit" value="lengthsubmit"><input type="hidden" name="schedule" value="<?php echo $schedule; ?>"><input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>"><select name="length" onchange="form.submit()"><option value="<?php echo $currentlength; ?>"><?php echo $displaylength; ?></option><option value="1">1/2 Hour</option><option value="2">1 Hour</option><option value="3">1.5 Hours</option><option value="4">2 Hours</option><option value="5">2.5 Hours</option><option value="6">3 Hours</option><option value="7">3.5 Hours</option><option value="8">4 Hours</option><option value="9">4.5 Hours</option><option value="10">5 Hours</option><option value="11">5.5 Hours</option><option value="12">6 Hours</option><option value="13">6.5 Hours</option><option value="14">7 Hours</option><option value="15">7.5 Hours</option><option value="16">8 Hours</option><option value="17">8.5 Hours</option><option value="18">9 Hours</option><option value="19">9.5 Hours</option><option value="20">10 Hours</option></select></form></td>
<?php
}else{
	?>
<td>Appt Length</td><td><form name="lengthform" id="lengthform" action="appointment.php" method="POST"><input type="hidden" name="lengthsubmit" value="lengthsubmit"><input type="hidden" name="schedule" value="<?php echo $schedule; ?>"><input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>"><select name="length" onchange="form.submit()"><option value="<?php echo $currentlength; ?>"><?php echo $displaylength; ?></option><option value="1">1 Hour</option><option value="2">2 Hours</option><option value="3">3 Hours</option><option value="4">4 Hours</option><option value="5">5 Hours</option><option value="6">6 Hours</option><option value="7">7 Hours</option><option value="8">8 Hours</option><option value="9">9 Hours</option><option value="10">10 Hours</option></select></form></td>
<?php
}
?>
	<td>Appt Status</td><td><form name="statusform" id="statusform" action="appointment.php" method="POST"><input type="hidden" name="statussubmit" value="statussubmit"><input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>"><select name="newstatus" onchange="form.submit()"><option value="<?php echo $currentstatus; ?>"><?php echo $displaystatus; ?></option><option value="1">Appointment Created</option><option value="2">Vehicle is Here</option><option value="3">Needs Attention</option><option value="4">Parts Ordered</option><option value="5">Vehicle will be dropped off</option><option value="6">Waiting on Customer</option>/option><option value="7">Vehicle Needs a Test Drive</option><option value="8">Feeling Blue</option><option value="10">Vehicle is Completed</option></select></form></td>
	</tr></table>
</div>
<div id="content">
	<?php
if(isset($_GET['le']))
{
	$reallength = $_GET['le'];
	if($schedule == '1')
	{
		$reallength = $reallength * .5;
	}
	echo '<p class="warningfont">There was an error extending the length of the Appointment. The appointment was extened to '.$reallength.' hours.</p>';
}

if($convertedid > '0')
{
	echo '<table><tr><td><p class="warningfont"> This appointment is linked to an invoice already</p></td><td><button class="smallquotebutton" value="go to invoice" onclick="window.open(\'invoice.php?invoiceid='.$convertedid.'\');self.close();">go to invoice</button></td></tr></table>';
}
?>
<div id="left">
<?php
if($accountid > '0')
{
?>
<table width="100%">
	<tr><td href="customerinfo" class="tdleft" width="50%"><?php echo $dcustomerinfo1; ?></td><td href="vehicleinfo" class="tdleft" width="50%"><?php echo $dvehicleinfo1; ?></td></tr>
	<tr><td href="customerinfo" class="tdleft" width="50%"><?php echo $dphone1; ?><br/><?php echo $dphone2; ?></td><td href="vehicleinfo" class="tdleft" width="50%"><?php echo $dmileage; ?></td></tr>

	<tr><td href="customerinfo" class="tdleft"><?php echo $dcustomerinfo2; ?></td><td href="vehicleinfo" class="tdleft"><?php echo $dvehicleinfo1a; ?></td></tr>

<tr><td href="customerinfo" class="tdleft"><?php echo $dcsz2; ?></td><td href="vehicleinfo" class="tdleft"><?php echo $dlicensestate; ?></td></tr>


</table>
<?php
}else
{
echo "<table><tr><td><a href=\"account.php?changecustomer=1&invoiceid=".$invoiceid.$schedulelink."\" class=\"no-decoration\"><input type=\"button\" alt=\"Select Customer\" class=\"quotebutton\" value=\"Select Customer\" /></a></td></tr></table>";
}
?>
<?php
if($split == '1')
{
echo "<form name=\"splitform\" action=\"".$pagelink."\" method=\"POST\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"submit\" value=\"1\"><input type=\"hidden\" name=\"split\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"split1\" value=\"1\">";
}
?>
<table id="highlightTable" class="blueTable">
<thead>
<tr>
<?php
if($split == '1')
{
echo "<th>Split</th>";
}
?>
<th width="38">Qty</th>
<th>Description</th>
<th width="40">FET</th>
<th width="55">Unit Price</th>
<th width="58">Ext Price</th>

</tr>
</thead>
</tbody><tr><td colspan="5">

<ul class="list-unstyled" id="page_list">
	<?php
		if($split == '1')
		{
			echo "<form name=\"splittransaction\" action=\"".$pagelink."\" method=\"POST\">";
			}

$sbi = '1';
$tri = '1';
$qri = '1';
$sth4 = $pdocxn->prepare('SELECT * FROM `'.$invlinetable.'` WHERE `invoiceid` = :inv ORDER BY `linenumber` ASC');
$sth4->bindParam(':inv',$invoiceid);
$sth4->execute();
$linecount = $sth4->rowCount();
	while($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
{
$lineitemtype = $row4['lineitem_typeid'];
$lineid = $row4['id'];
$invqty = $row4['qty'];
$invamount = $row4['amount'];
$invpartid = $row4['partid'];
$printflag = $row4['printflag'];
if($invpartid < '1')
{
$invpartid = '0';
}
$invpackageid = $row4['packageid'];
$invserviceid = $row4['serviceid'];
$databasecomment = $row4['comment'];
$invcomment = stripslashes($databasecomment);
$fet = $row4['fet'];
if($fet == '0')
{
	$fet = '';
}
$extprice = $row4['totallineamount'];
$unitamount = $extprice/$invqty;
if($invqty == '0'){
	$invqty= '';
	$unitamount = '';
$invamount = '';
$unitprice = '';
$dextprice = '';
}else{
setlocale(LC_MONETARY,"en_US");
$invamount = money_format('%(#0.2n',$unitamount);
$unitprice = round($unitamount,2);
$dextprice = money_format('%(#0.2n',$extprice);
}
$linenumber = $row4['linenumber'];
/*if($linenumber == '1')
{
	$displayup = "<td>&nbsp;</td>";
}
else {
	$displayup = "<td><form action=\"".$pagelink."\" method=\"post\" name=\"linemoveform\"><input type=\"hidden\" name=\"currentline\" value=\"".$linenumber."\"><input type=\"hidden\" name=\"up\" value=\"1\"><input type=\"hidden\" name=\"lineid\" value=\"".$lineid."\"><input type=\"hidden\" name=\"linemove\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"submit\" value=\"submit\"><input type=\"image\" src=\"images/icons/up.png\" alt=\"up\" name=\"submit\" width=\"20\" border=\"none\"></form></td>";
}
if($linenumber == $linecount)
{
	$displaydown = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
}
else {
	$displaydown = "<form action=\"".$pagelink."\" method=\"post\" name=\"linvemoveform\"><input type=\"hidden\" name=\"currentline\" value=\"".$linenumber."\"><input type=\"hidden\" name=\"down\" value=\"1\"><input type=\"hidden\" name=\"lineid\" value=\"".$lineid."\"><input type=\"hidden\" name=\"linemove\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"submit\" value=\"submit\"><input type=\"image\" src=\"images/icons/down.png\" alt=\"down\" name=\"submit\" width=\"20\"></form>";
}

$prevlineid = $row4['id'];
	echo "\n<tr href=\"$tri\" id=\"".$qri."row\">";
	if($split == '1')
	{
		echo "<td><input type=\"checkbox\" name=\"linesplit[]\" value=\"".$lineid."\"></td>";
		}
		echo "<td>$invqty</td><td>$invcomment</td><td>$fet</td><td>$invamount</td><td>$dextprice</td></tr>";
		*/
if($printflag > '0')
{
	echo '<li id="'.$lineid.'"><table class="invtable"><tr href="'.$tri.'" id="'.$qri.'row"><td width="35">'.$invqty.'</td><td>'.$invcomment.'</td><td width="40">'.$fet.'</td><td width="55">'.$invamount.'</td><td width="55">'.$dextprice.'</td></tr></table></li>';
}else{
		echo '<li id="'.$lineid.'"><table class="noprintinvtable"><tr href="'.$tri.'" id="'.$qri.'row"><td width="35">'.$invqty.'</td><td>'.$invcomment.'</td><td width="40">'.$fet.'</td><td width="55">'.$invamount.'</td><td width="55">'.$dextprice.'</td></tr></table></li>';
}
${"ip".$tri} = "\n<div id=\"$tri\"><div class=\"q1\"><form name=\"updatecomment\" method=\"post\" action=\"".$pagelink."\"><table class=\"righttable\"><tr>";
if($typeid == '6')
{
${"ip".$tri} .= "<td colspan=\"3\" class=\"left\"><select name=\"newpaymentid\"><option value=\"0\">".$invcomment."</option>";
$sql8 = "SELECT `id`,`name` FROM `payment_type` ORDER BY `name` ASC";
$sth8 = $pdocxn->prepare($sql8);
$sth8->execute();
while($row8 = $sth8->fetch(PDO::FETCH_ASSOC))
{
	$paymentid = $row8['id'];
	$paymentname = $row8['name'];
	${"ip".$tri} .= "<option value=\"".$paymentid."\">".$paymentname."</option>";
}
${"ip".$tri} .= "</select></td><td><input type=\"textbox\" name=\"checknumber\" placeholder=\"check number\"";

}else{
if($lineitemtype == '1')
{
${"ip".$tri} .= "<td colspan=\"3\" class=\"center\"><b>".$invcomment."</b><input type=\"hidden\" name=\"comment\" value=\"".$invcomment."\"></td>";
}else{
${"ip".$tri} .= "<td colspan=\"3\" class=\"left\"><textarea class=\"wideinput\" name=\"comment\" autocomplete=\"off\" id=\"".$qri."box\">".$invcomment."</textarea></td>";
}}
if($typeid == '6')
{
${"ip".$tri} .= "</tr><tr><td class=\"left\">Payment Amount: <input type=\"text\" class=\"narrowinput\" name=\"price\" value=\"$unitprice\" autocomplete=\"off\"></td></tr><tr><td class=\"center\"><input type=\"hidden\" name=\"editsubmit\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"lineid\" value=\"".$lineid."\"><input type=\"hidden\" name=\"submit\" value=\"submit\"><input type=\"submit\" class=\"smallbutton\" alt=\"down\" value=\"Update\"></form></td><td><a href=\"appointment.php?invoiceid=".$invoiceid."&noprintline=".$lineid."\"><input type=\"button\" class=\"xsmallbutton\" value=\"noprint\"></a></td><td><form action=\"".$pagelink."\" method=\"post\" name=\"deletelinenumber\"><input type=\"hidden\" name=\"delete\" value=\"1\"><input type=\"hidden\" name=\"deletedlinenumber\" value=\"".$linenumber."\"><input type=\"hidden\" name=\"lineid\" value=\"".$lineid."\"><input type=\"hidden\" name=\"partid\" value=\"$invpartid\"><input type=\"hidden\" name=\"qty\" value=\"".$invqty."\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"submit\" value=\"submit\"><input type=\"submit\" class=\"cancel-small\" alt=\"down\" value=\"Delete Item\"></form></td></tr></table></form></div></div>\n";	
}else{
${"ip".$tri} .= "</tr><tr><td class=\"left\">Unit Price: <input type=\"text\" class=\"narrowinput\" name=\"price\" value=\"$unitprice\" autocomplete=\"off\"></td><td class=\"left\">QTY:<input type=\"number\" class=\"narrowinput\" name=\"qty\" value=\"".$invqty."\" step=\"any\" autocomplete=\"off\"></td><td class=\"center\">FET: <input type=\"text\" class=\"narrowinput\" name=\"newfet\" value=\"$fet\" autocomplete=\"off\"></td></tr><tr><td class=\"center\"><input type=\"hidden\" name=\"editsubmit\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"lineid\" value=\"".$lineid."\"><input type=\"hidden\" name=\"submit\" value=\"submit\"><input type=\"submit\" class=\"xsmallbutton\" alt=\"down\" value=\"Update\"></form></td><td><a href=\"appointment.php?invoiceid=".$invoiceid."&noprintline=".$lineid."\"><input type=\"button\" class=\"xsmallbutton\" value=\"noprint\"></a></td><td><form action=\"".$pagelink."\" method=\"post\" name=\"deletelinenumber\"><input type=\"hidden\" name=\"delete\" value=\"1\"><input type=\"hidden\" name=\"deletedlinenumber\" value=\"".$linenumber."\"><input type=\"hidden\" name=\"lineid\" value=\"".$lineid."\"><input type=\"hidden\" name=\"partid\" value=\"$invpartid\"><input type=\"hidden\" name=\"qty\" value=\"".$invqty."\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"submit\" value=\"submit\"><input type=\"submit\" class=\"cancel-small\" alt=\"down\" value=\"Delete Item\"></form></td></tr></table></form><table width=\"10%\"><tr>".$displayup."<td></td></tr><tr><td>".$displaydown."</td></tr></table></div></div>\n";
}
$tri ++;
$qri ++;
	}
if($linenumber < '1')
{
	$newlinenumber = '1';
}
else
{
	$newlinenumber = $linenumber + 1;
}
echo "</ul></td></tr>";
echo "<tr href=\"add\" id=\"additemrow\"><td></td><td><b>Add Item </b><img src=\"images/buttons/add-black.png\" width=\"25\"> </td><td></td><td></td><td></td></tr>";
//echo "<tr href=\"note\" id=\"addnoterow\"><td></td><td><b>Diagnostic Note (does not carry over to invoice</b><img src=\"images/buttons/add-black.png\" width=\"25\"> </td><td></td><td></td><td></td></tr>";

if(isset($_POST['newtaxclass']))
{
global $changetax;
$changetax = '1';
$newtaxgroup = $_POST['newtaxclass'];

$sth1 = $pdocxn->prepare('UPDATE `'.$invtable.'` SET `taxgroup`=:taxgroup WHERE `id` = :invoiceid');
$sth1->bindParam(':taxgroup',$newtaxgroup);
//$sth1->bindParam(':tax',$taxtotal);
//$sth1->bindParam(':total',$invoicetotal);
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->execute();
}
/*
$sql3 = "SELECT SUM(`taxamount`) AS `taxtotal` FROM `tax_trans_appt` WHERE `transid` = :invoiceid";
$sth3 = $pdocxn->prepare($sql3);
$sth3->bindParam(':invoiceid',$invoiceid);
$sth3->execute();
while($row3 = $sth3->fetch(PDO::FETCH_ASSOC))
{
	$taxamount = $row3['taxtotal'];
}
*/
$taxamount = $invoicesubtotal * $taxmultiply;
$invoicetotal = $invoicesubtotal + $taxamount;
$invoiceformtotal = round($invoicetotal,2);
$dtaxtotal = money_format('%(#0.2n',$taxamount);
$dsubtotal = money_format('%(#0.2n',$invoicesubtotal);
$dinvoicetotal = money_format('%(#0.2n',$invoicetotal);
if($typeid =='6')
{}else{
echo "<tr href=\"total\"><td colspan=\"4\">Subtotal:</td><td>".$dsubtotal."</td></tr>";
echo "<tr href=\"total\"><td colspan=\"4\">Sales Tax:&nbsp;&nbsp;&nbsp;&nbsp;".$taxdescription."</td><td>".$dtaxtotal."</td></tr>";
echo "<tr href=\"total\"><td colspan=\"4\"><b>Total:</b></td><td><b>".$dinvoicetotal."</b></td></tr>";

$sql8 = "SELECT `linkid` FROM `translink` WHERE `transid` = :transid";
$sth8 = $pdocxn->prepare($sql8);
$sth8->bindParam(':transid',$invoiceid);
$sth8->execute();
while($row8 = $sth8->fetch(PDO::FETCH_ASSOC))
{
	$linkid = $row8['linkid'];
}
$sql9 = "SELECT `linkid`,`transid` FROM `translink` WHERE `linktoid` = :linkid";
$sth9 = $pdocxn->prepare($sql9);
$sth9->bindParam(':linkid',$linkid);
$sth9->execute();
while($row9 = $sth9->fetch(PDO::FETCH_ASSOC))
{
	$paymentid = $row9['transid'];
}
$sql10 = "SELECT `id`,`invoicedate` FROM `'.$invtable.'` WHERE `id` = :paymentid";
$sth10 = $pdocxn->prepare($sql10);
$sth10->bindParam(':paymentid',$paymentid);
$sth10->execute();
while($row10 = $sth10->fetch(PDO::FETCH_ASSOC))
{
$paymentdate = $row10['invoicedate'];
$paymentdate2 = new DateTime($paymentdate);
$displaypaymentdate = $paymentdate2->format('n/j/Y');
}
$payrow = '1';
$sql11 = "SELECT `id`,`totallineamount`,`lineitem_typeid`,`comment` FROM `'.$invlinetable.'` WHERE `invoiceid` = :paymentid";
$sth11 = $pdocxn->prepare($sql11);
$sth11->bindParam(':paymentid',$paymentid);
$sth11->execute();
while($row11 = $sth11->fetch(PDO::FETCH_ASSOC))
{
 $paymentamount = $row11['totallineamount'];
 $paymentdesc = $row11['comment'];
 $paymenttypeid = $row11['lineitem_typeid'];
if($paymenttypeid == '9')
{
 $paymentdesc = "Check #: ".$paymentdesc;
}
$sql12 = "SELECT `id`,`description` FROM `lineitem_type` WHERE `id` = :paymenttypeid";
$sth12 = $pdocxn->prepare($sql12);
$sth12->bindParam(':paymenttypeid',$paymenttypeid);
$sth12->execute();
while($row12 = $sth12->fetch(PDO::FETCH_ASSOC))
{
 $paymenttype = $row12['description'];
}
if($payrow == '1')
{
echo "<tr class=\"total\"><td colspan=\"4\" class=\"tdright\">Payment Method&nbsp;&nbsp;&nbsp;&nbsp;".$displaypaymentdate." ".$paymentdesc."</td><td>".$paymentamount."</td></tr>";
}else{
echo "<tr class=\"total\"><td colspan=\"4\" class=\"tdright\">".$displaypaymentdate." ".$paymentdesc."</td><td>".$paymentamount."</td></tr>";
}
$payrow ++;
}
$invbalance = $invoicetotal - $paymentamount;
$dinvbalance = money_format('%(#0.2n',$invbalance);

if($typeid == '1')
{
echo "<tr class=\"total\"><td colspan=\"4\" class=\"tdright\">Invoice Balance:&nbsp;&nbsp;&nbsp;&nbsp;</td><td>".$dinvbalance."</td></tr>";
}
echo "<tr class=\"total\"><td colspan=\"2\" class=\"tdleft\">Current Account Balance from All Stores:&nbsp;&nbsp;&nbsp;&nbsp;$currentaccountbalance</td><td colspan=\"2\" class=\"tdright\">Account Balance at ".$currentstorename.":<td>".$currentstorebalance."</td></tr>";
if($split == '1')
{
echo "<tr class=\"split\"><td colspan=\"2\" class=\"tdleft\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"split\" value=\"3\"><input type=\"submit\" name=\"quotesplit\" value=\"Add items to Quote\"></td><td colspan=\"3\"><input type=\"submit\" name=\"invoicesplit\" value=\"Add items to Invoice\"></form></td></tr>";
}
}

?>
</tbody></table></form>Appoinment Created on <?php echo $displaycreationdate; ?></div>
<div class="right">
<?php
$sth1 = $pdocxn->prepare('UPDATE `'.$invtable.'` SET `subtotal`=:subtotal,`tax`=:tax,`total`=:total WHERE `id` = :invoiceid');
$sth1->bindParam(':subtotal',$invoicesubtotal);
$sth1->bindParam(':tax',$taxamount);
$sth1->bindParam(':total',$invoicetotal);
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->execute();
while ($tri > 0) {
        echo ${"ip".$tri};
		$tri --;
}
echo "\n<div id=\"customerinfo\"><div class=\"q1\"><table class=\"righttable\"><tr><td class=\"left\">".$fullname."</td><td>";
echo "<a href=\"editaccount.php?accountid=".$accountid."&appointmentid=".$invoiceid."\">";
echo "<input type=\"button\" alt=\"edit customer information\" class=\"cancel-small\" value=\"Edit Customer Information\" /></a></td>";
echo "</tr><tr><td colspan=\"2\" class=\"left\">".$daddress."</td></tr><tr><td>Phone 1:</td><td>".$phone1."</td></tr><tr><td>Phone 2:</td><td class=\"center\">".$phone2."</td></tr><tr><td colspan=\"2\" class=\"center\"></td></tr><tr><td class=\"center\"><a href=\"account.php?changecustomer=1&invoiceid=".$invoiceid."&schedule=".$schedule."\" class=\"no-decoration\"><input type=\"button\" alt=\"Change Customer\" class=\"cancel-small\" value=\"Change Customer\" /></a></td><td class=\"center\"><a href=\"accounthistory.php?accountid=".$accountid."\" class=\"no-decoration\" target=\"_BLANK\"><input type=\"button\" alt=\"Change Customer\" class=\"xsmallbutton\" value=\"Account History\" /></a></td></tr></table></div></div>";
echo "\n<div id=\"invoicedate\"><div class=\"q1\"><form name=\"newdateform\" action=\"".$pagelink."\" method=\"post\"><table class=\"righttable\"><tr><td colspan=\"2\" class=\"center\">Change Appointment Date</td></tr><tr><td colspan=\"2\" class=\"center\"><input type=\"date\" name=\"newdate\" value=\"".$displayinvoicedate2."\"></td></tr><tr><td colspan=\"2\" class=\"center\"><input type=\"hidden\" name=\"changedate\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"submit\" class=\"btn-style\" name=\"submit\" value=\"Update Invoice Date\"></td></tr></table></form></div></div>";
echo "\n<div id=\"vehicleinfo\"><div class=\"q1\"><table class=\"righttable\"><tr><td colspan=\"2\" class=\"left\">".$dvehicleinfo2."</td></tr><tr><td class=\"left\" colspan=\"2\">VIN:&nbsp;&nbsp;&nbsp;&nbsp;".$currentvin."</td></tr><tr><td class=\"left\">License:&nbsp;&nbsp;&nbsp;&nbsp;".$license."</td><td class=\"left\">State:   ".$vehiclestate."</td></tr><tr href=\"editvehicleinfo\" colslpan=\"2\"><td><input type=\"button\" name=\"editvehicle\" class=\"btn-style\" value=\"Edit Vehicle\" /></td><td><form name=\"addvehicle\" action=\"vehicles.php\" method=\"post\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"hidden\" name=\"appointmentid\" value=\"".$invoiceid."\"><input type=\"submit\" name=\"addvehicle\" class=\"btn-style\" value=\"Add Vehicle\" /></form></td></tr><tr><td colspan=\"3\" class=\"center\"></td></tr></table></form><table><tr><td colspan=\"2\"><div class=\"styled-select2 black rounded\">\n<form name=\"changevehicle\" id=\"form\" action=\"".$pagelink."\" method=\"post\"><select name=\"vehiclechange\" onchange=\"form.submit()\">";
if($vehicleid> '0')
	{
		echo "<option value=\"$vehicleid\">".$dvehicleinfo2nb."</option>";
	}
else {
echo "<option value=\"0\"></option>";
}

$sql11 = 'SELECT * FROM `vehicles` WHERE `accountid` = :accountid AND active = \'1\' ORDER BY `description` DESC';
$sth11 = $pdocxn->prepare($sql11);
$sth11->bindParam(':accountid',$accountid);
$sth11->execute();
if ($sth11->rowCount() > 0)
{
while($row11 = $sth11->fetch(PDO::FETCH_ASSOC))
	{
	$newvehicleid = $row11['id'];
	$year = $row11['year'];
	$model = $row11['model'];
	$make = $row11['make'];
	$vin = $row11['vin'];
	$submodel = $row11['submodel'];
	$engine = $row11['engine'];
	$license = $row11['license'];
	$vehiclestate = $row11['state'];
	$description = $row11['description'];
if($year > '1')
{
$dvehicleinfo = "".$year." ".$make." ".$model." ".$submodel." ".$engine."   VIN: ".$vin."  License: ".$license." ";
}
else
{
$dvehicleinfo = "".$description."   VIN: ".$vin."   License: ".$license."";
}

echo "\n<option value=\"".$newvehicleid."\">".$dvehicleinfo."</option>";
}
}
echo "</select><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"changevehicle\" value=\"1\"><input type=\"hidden\" name=\"form\" value=\"1\"></form></td></tr></table>";

echo "</div></div>";
if($currentvehicleyear > '1')
{
	echo "\n<div id=\"editvehicleinfo\"><div class=\"q1\"><form name=\"changevehicle\" method=\"post\" action=\"".$pagelink2."\"><table class=\"righttable\"><tr><td class=\"left\"><select name=\"vehicleyear\"><option value=\"".$currentvehicleyear."\">".$currentvehicleyear."</option>";
while($yearywi > $dbyear)
{
echo "<option value=\"".$yearywi."\">".$yearywi."</option>";
$yearywi --;	
}
echo "
</select></td><td class=\"left\"><input type=\"hidden\" name=\"editvehicle1\" value=\"1\"><input type=\"hidden\" name=\"vehicleid\" value=\"".$vehicleid."\"><input type=\"text\" name=\"vehiclemake\" value=\"".$currentvehiclemake."\"></td><td><input type=\"text\" name=\"vehiclemodel\" value=\"".$currentvehiclemodel."\"></td></tr><tr><td class=\"left\">VIN:</td><td class=\"left\" colspan=\"2\"><input type=\"text\" name=\"vehiclevin\" value=\"".$currentvin1."\"></td></tr><tr><td class=\"left\">License:</td><td class=\"left\"><input type=\"text\" name=\"license\" value=\"".$currentlicense."\" size=\"6\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;State:<input type=\"text\" name=\"state\" size=\"1\" value=\"".$currentstate."\"></td></tr><tr><td colspan=\"3\" class=\"center\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"editvehicle\" value=\"1\"><input type=\"hidden\" name=\"submit\" value=\"1\"><input type=\"image\" src=\"images/buttons/checkmark.png\" alt=\"editvehicle\" name=\"submit\" width=\"25\"></td></tr></table></form></div></div>";

}else{
echo "\n<div id=\"editvehicleinfo\"><div class=\"q1\"><form name=\"changevehicle\" method=\"post\" action=\"".$pagelink2."\"><table class=\"righttable\"><tr><td class=\"left\">Vehicle Info:</td><td class=\"left\" colspan=\"2\"><input type=\"hidden\" name=\"editvehicle1\" value=\"1\"><input type=\"hidden\" name=\"vehicleid\" value=\"".$vehicleid."\"><input type=\"text\" name=\"vehiclename\" value=\"".$dvehicleinfo2nb."\"></td></tr><tr><td class=\"left\">VIN:</td><td class=\"left\" colspan=\"2\"><input type=\"text\" name=\"vehiclevin\" value=\"".$currentvin1."\"></td></tr><tr><td class=\"left\">License:</td><td class=\"left\"><input type=\"text\" name=\"license\" value=\"".$currentlicense."\" size=\"6\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;State:<input type=\"text\" name=\"state\" size=\"1\" value=\"".$currentstate."\"></td></tr><tr><td colspan=\"3\" class=\"center\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"editvehicle\" value=\"1\"><input type=\"hidden\" name=\"submit\" value=\"1\"><input type=\"image\" src=\"images/buttons/checkmark.png\" alt=\"editvehicle\" name=\"submit\" width=\"25\"></td></tr></table></form></div></div>";
}
echo "\n<div id=\"add\"><div class=\"q1\"><form name=\"addcomment\" method=\"post\" action=\"".$pagelink."\" onsubmit=\"return makeSearch()\"><table class=\"righttable\"><tr><td colspan=\"3\" class=\"left\"><textarea class=\"wideinput\" name=\"newcomment\" placeholder=\"Add Comment\" id=\"invoicecomment\"></textarea><div id=\"auto\"></div></td></tr><tr><td class=\"left\">Unit Price: <input type=\"text\" class=\"narrowinput\" id=\"unitprice\" name=\"newprice\" placeholder=\"0.00\" autocomplete=\"off\"></td><td class=\"center\">QTY:<input type=\"number\" class=\"narrowinput\" name=\"newqty\" value=\"1\" step=\"any\" ></td><td>FET: <input type=\"text\" class=\"narrowinput\" name=\"newfet\" placeholder=\"0.00\" autocomplete=\"off\"></td></tr><tr><td class=\"center\"><input type=\"button\" id=\"multiply1\" data-quantity=\"1.5\" value=\"x 1.5\" class=\"xsmallbutton\"><input type=\"button\" id=\"multiply2\" data-quantity=\"2\" value=\"x 2\" class=\"xsmallbutton\"></td><td class=\"center\" colspan=\"2\"><input type=\"hidden\" name=\"newlinenumber\" value=\"$newlinenumber\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"newcommentform\" value=\"1\"><input type=\"hidden\" name=\"submit\" value=\"1\"><input type=\"submit\" name=\"submit\" id=\"Add Item\" value=\"Add\" class=\"smallbutton\"></td></tr></table></form></div></div>";
echo "\n<div id=\"note\"><div class=\"q1\"><form name=\"addnotes\" method=\"post\" action=\"".$pagelink."\"><table class=\"righttable\"><tr><td colspan=\"2\" class=\"left\"><textarea class=\"wideinput\" rows=\"5\" name=\"notes\" id=\"noteitem\">".$note."</textarea></td></tr><tr><td class=\"center\" colspan=\"2\"><input type=\"hidden\" name=\"notesubmit\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"submit\" value=\"1\"><input type=\"submit\" name=\"submit\" id=\"Add Note\" value=\"Save Note\" class=\"smallbutton\"></td></tr></table></form></div></div>";
echo "\n<div id=\"total\"><div class=\"q1\"><table class=\"righttable\"><tr><td><input type=\"submit\" class=\"smallbutton\" value=\"Change Payment\"></td><td><input type=\"submit\" class=\"cancel-small\" value=\"Void Payment\"></td></tr></table></div>";
?>
</div></div>
<?php
if($typeid =='6')
{
?>
<div class="printdiv"></div>
<?php
}else{
?>
<div class="printdiv"><table class="righttable2"><tr>
<th colspan="2" class="center"><center>Quick Add</center></th></tr><tr><td class="center">
<?php
echo "<a href=\"inventory.php?scheduleid=".$invoiceid."\"><input type=\"button\" class=\"xsmallbutton\" alt=\"Add Tire\" value=\"Add Tires\"></a></td><td class=\"center\"><a href=\"services-appointment.php?invoiceid=".$invoiceid."\"><input type=\"button\" apt=\"add service\" class=\"xsmallbutton\" value=\"Add Service/Package\"></a></td></tr><tr>";
$tpi = '1';
$topsql = "SELECT `id`,`description` FROM `packages` WHERE `active` = '1' ORDER BY `sortorder` DESC LIMIT 0,12";
$topsth = $pdocxn->prepare($topsql);
$topsth->execute();
while($toprow = $topsth->fetch(PDO::FETCH_ASSOC))
{
$packageid = $toprow['id'];
$packagetitle = $toprow['description'];
$displaytitle = substr($packagetitle, 0,30);
if($tpi % '2')
{
echo "</tr><tr>";
}
?>
<td class="center">
<form action="<?php echo $pagelink; ?>" method="post" name="additem">
<input type="hidden" name="additem" value="1">
<input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>">
<input type="hidden" name="submit" value="submit">
<input type="hidden" name="packageid" value="<?php echo $packageid; ?>">
<input type="submit" class="xsmallbutton" alt="print" value="<?php echo $displaytitle; ?>" name="submit"></form>
</td>
<?php
$tpi ++;
}
?>
</tr></table>
</div>
<?php
}
?>
</div>
<script type="text/javascript">
$("table tr").click(function(){
    $($('#' + $(this).attr("href"))).siblings().hide().end().fadeIn();
});
</script>
<script type="text/javascript">
$("table td").click(function(){
    $($('#' + $(this).attr("href"))).siblings().hide().end().fadeIn();
});
</script>
<script type="text/javascript">
$( "#additemrow" ).click(function() {
  $( "#additem" ).focus();
});
</script>
<script type="text/javascript">
$( "#addnoterow" ).click(function() {
  $( "#noteitem" ).focus();
});
</script>
<script type="text/javascript">
$("#newmileage").blur(function() {
  $("#mileageform").submit();
});
</script>
<?php
while ($qri > 0) {
echo "\n<script type=\"text/javascript\">\n$(\"#".$qri."row\").click(function() {\n$(\"#".$qri."box\" ).focus();\n});\n</script>";
$qri --;
}
?><script type="text/javascript">
function multiplyBy(){
num1 = document.getElementById("unitprice").value;
num2 = '1.5';
document.getElementById("unitprice2").value = num1*num2;
});
</script>

<script>
$(document).ready(function(){
 $( "#page_list" ).sortable({
  placeholder : "ui-state-highlight",
  update  : function(event, ui)
  {
   var line_id_array = new Array();
   $('#page_list li').each(function(){
    line_id_array.push($(this).attr("id"));
   });
   $.ajax({
    url:"scripts/sorttablescriptschedule.php?r=24567&siteid=1",
    method:"POST",
    data:{line_id_array:line_id_array},
   });
  }
 });
});
</script>
<script type="text/javascript">
$(document).ready(function(){
        $("#invoicecomment").on("change", function(){ 
             $.ajax({
                 method: "POST",
                 url: "invoiceautoprice.php",
                 data: {autocomment: $("#invoicecomment").val(),
                 },
                 success: function(response){
                        $("#unitprice").val(response);
                 },
              });
         });
    });
</script>
</body>
</html>
<?php
}

else{
//display html - no submit
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
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" >
<meta http-equiv="cache-control" content="no-cache, must-revalidate, post-check=0, pre-check=0" />
  <meta http-equiv="cache-control" content="max-age=0" />
  <meta http-equiv="expires" content="0" />
  <meta http-equiv="expires" content="Tue, 01 Jan 2018 1:00:00 GMT" />
  <meta http-equiv="pragma" content="no-cache" />
<link rel="stylesheet" type="text/css" href="style/globalstyle.css" >
<link rel="stylesheet" type="text/css" href="style/invoicestyle.css" >
<script type="text/javascript" src="scripts/jquery-latest.js"></script>
<script type="text/javascript" src="scripts/script.js"></script>
<script type="text/javascript">
function highlight_row() {
    var table = document.getElementById('highlightTable');
    var cells = table.getElementsByTagName('td');
    for (var i = 0; i < cells.length; i++) {
        // Take each cell
        var cell = cells[i];
        // do something on onclick event for cell
        cell.onclick = function () {
            // Get the row id where the cell exists
            var rowId = this.parentNode.rowIndex;
            var rowsNotSelected = table.getElementsByTagName('tr');
            for (var row = 0; row < rowsNotSelected.length; row++) {
                rowsNotSelected[row].style.backgroundColor = "";
                rowsNotSelected[row].classList.remove('selected');
            }
            var rowSelected = table.getElementsByTagName('tr')[rowId];
            rowSelected.style.backgroundColor = "#347DD5";
            rowSelected.className += " selected";
        }
    }
} //end of function
window.onload = highlight_row;
</script>
<script type="text/javascript">
$(document).ready(function() {
    loadPopupManual();
});
function loadPopupManual() {
    $('#load-div').fadeIn("slow"); 
    $.get('scripts/selecteduser.php', function(data) {
        $('#load-div').html(data);
    });
}
    </script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
$(document).ready(function(){
    $("#highlight1").hover(function(){
        $(this).css("background-color", "lightblue");
        }, function(){
        $(this).css("background-color", "white");
    });
});
</script>
<script>
$(document).ready(function(){
    $("#highlight2").hover(function(){
        $(this).css("background-color", "lightblue");
        }, function(){
        $(this).css("background-color", "white");
    });
});
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
<div id="selecteduser"><form name="current1" action="<?php echo $pagelink; ?>" method="POST"><table id="floatleft"><tr><td class="currentuser">Current User:</td><td class="currentitem"><div class="styled-select black rounded"><input type="hidden" name="userform" value="1">
<select name="user" onchange="form.submit()">
        		<?php
        		if($currentid > '0')
        		{
        			echo "<option value=\"$currentid\">$currentusername</option>";
        		}
else {
echo "<option value=\"0\">Select Salesperson</option>";
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
        	<div id="left1">
<table>
	<tr><td><a href="account.php?invoice=4" class="no-decoration" target="_BLANK"><input type="button" class="quotebutton" value="Create Quote"></a></td><td>
<a href="account.php?invoice=1" class="no-decoration" target="_BLANK"><input type="button" class="quotebutton" value="Create Invoice"></a></td>

<td colspan="2"><form name="search" action="<?php echo $pagelink; ?>" method="post"><input type="hidden" name="submit" value="1"><input type="text" name="invoicenumber" placeholder="Enter Transaction ID" autocomplete="off" autofocus><br /><center><input type="submit" class="smallquotebutton" value="Search"></center></form></td></tr>
</table><br /><table><tr><th class="left">Prev Day</th><th><center><form name="invoiceviewdateform" action="<?php echo $pagelink; ?>" method="post"><input type="hidden" name="invoicetypeview" value="<?php echo $displaytype; ?>"><input type="date" name="invoicedateview" onchange="this.form.submit()" value="<?php echo $displaydate; ?>" max="<?php echo $currentday2; ?>"></form></center></th>
<th class="left">
<?php
if($displaytype == '1'){
	$select1text = "Invoices";
}
if($displaytype == '17'){
	$select1text = "Credit";
}
if($displaytype == '18'){
	$select1text = "Refunds";
}
if($displaytype == '4'){
	$select1text = "Quotes";
}
if($displaytype == '11'){
	$select1text = "Work Order";
}
if($displaytype == '6'){
	$select1text = "Payments";
}
if($displaytype == '7'){
	$select1text = "Adjustments";
}
$selecttypeview = "<select name=\"invoicetypeview\" onchange=\"form.submit()\"><option value=\"".$displaytype."\">".$select1text."</option><option value=\"1\">Invoice</option><option value=\"4\">Quotes</option><option value=\"11\">Work Order</option><option value=\"6\">Payment</option><option value=\"18\">Refund</option><option value=\"17\">Credit Invoices</option><option value=\"7\">Adjustment</option>";

if($displaydate == $currentday2)
{
echo "Todays ".$currentstorename."</th><th class=\"left\"><div class=\"styled-select black rounded\"><form name=\"invoiceviewdateform\" action=\"".$pagelink."\" method=\"post\"><input type=\"hidden\" name=\"invoicedateview\" value=\"".$displaydate."\">".$selecttypeview;
}else{
echo $displaydate2." ".$currentstorename."</th><th class=\"left\"><div class=\"styled-select black rounded\"><form name=\"invoiceviewdateform\" action=\"".$pagelink."\" method=\"post\"><input type=\"hidden\" name=\"invoicedateview\" value=\"".$displaydate."\">".$selecttypeview;
}
?>
</select></form></div>
</th></tr>
<tr>
<?php
if($sort == '0')
{
?>
<th><form name="sortform" action="<?php echo $pagelink; ?>" method="post"><input type="hidden" name="sort" value="1"><input type="submit" name="sortsubmit" class="btn-style-gray" value="<?php echo $select1text; ?>" /></form></th>
<th><form name="sortform" action="<?php echo $pagelink; ?>" method="post"><input type="hidden" name="sort" value="3"><input type="submit" name="sortsubmit" class="btn-style-gray" value="Customer Name" /></form></th>
<th><form name="sortform" action="<?php echo $pagelink; ?>" method="post"><input type="hidden" name="sort" value="5"><input type="submit" name="sortsubmit" class="btn-style-gray" value="Amount" /></form></th>
<th><form name="sortform" action="<?php echo $pagelink; ?>" method="post"><input type="hidden" name="sort" value="7"><input type="submit" name="sortsubmit" class="btn-style-gray" value="Vehicle" /></form></th></tr>
<?php
}
else
{
if($sort == '1')
{
?>
<th><form name="sortform" action="<?php echo $pagelink; ?>" method="post"><input type="hidden" name="sort" value="2"><input type="submit" name="sortsubmit" class="btn-style-dark" value="<?php echo $select1text; ?>" /></form></th>
<?php
}else if($sort == '2')
{
?>
<th><form name="sortform" action="<?php echo $pagelink; ?>" method="post"><input type="hidden" name="sort" value="1"><input type="submit" name="sortsubmit" class="btn-style-dark" value="<?php echo $select1text; ?>" /></form></th>
<?php
}else{
?>
<th><form name="sortform" action="<?php echo $pagelink; ?>" method="post"><input type="hidden" name="sort" value="1"><input type="submit" name="sortsubmit" class="btn-style-gray" value="<?php echo $select1text; ?>" /></form></th>
<?php
}
if($sort == '3')
{
?>
<th><form name="sortform" action="<?php echo $pagelink; ?>" method="post"><input type="hidden" name="sort" value="4"><input type="submit" name="sortsubmit" class="btn-style-dark" value="Customer Name" /></form></th>
<?php
}else if($sort == '4'){
?>
<th><form name="sortform" action="<?php echo $pagelink; ?>" method="post"><input type="hidden" name="sort" value="3"><input type="submit" name="sortsubmit" class="btn-style-dark" value="Customer Name" /></form></th>
<?php
}else{
?>
<th><form name="sortform" action="<?php echo $pagelink; ?>" method="post"><input type="hidden" name="sort" value="3"><input type="submit" name="sortsubmit" class="btn-style-gray" value="Customer Name" /></form></th>
<?php
}
if($sort == '5')
{
?>
<th><form name="sortform" action="<?php echo $pagelink; ?>" method="post"><input type="hidden" name="sort" value="6"><input type="submit" name="sortsubmit" class="btn-style-dark" value="Amount" /></form></th>
<?php
}else if($sort == '6'){
?>
<th><form name="sortform" action="<?php echo $pagelink; ?>" method="post"><input type="hidden" name="sort" value="5"><input type="submit" name="sortsubmit" class="btn-style-dark" value="Amount" /></form></th>
<?php
}else{
?>
<th><form name="sortform" action="<?php echo $pagelink; ?>" method="post"><input type="hidden" name="sort" value="5"><input type="submit" name="sortsubmit" class="btn-style-gray" value="Amount" /></form></th>
<?php
}
if($sort == '7')
{
?>
<th><form name="sortform" action="<?php echo $pagelink; ?>" method="post"><input type="hidden" name="sort" value="8"><input type="submit" name="sortsubmit" class="btn-style-dark" value="Vehicle" /></form></th>
<?php
}else if($sort == '8'){
?>
<th><form name="sortform" action="<?php echo $pagelink; ?>" method="post"><input type="hidden" name="sort" value="7"><input type="submit" name="sortsubmit" class="btn-style-dark" value="Vehicle" /></form></th>
<?php
}else{
?>
<th><form name="sortform" action="<?php echo $pagelink; ?>" method="post"><input type="hidden" name="sort" value="7"><input type="submit" name="sortsubmit" class="btn-style-gray" value="Vehicle" /></form></th>
<?php
}
}
$sql4 = 'SELECT `id`,`invoiceid`,`subtotal`,`tax`,`total`,`accountid`,`vehicleid` FROM `'.$invtable.'` WHERE `invoicedate` LIKE :displaydate AND `location` = :locationid AND `type` = :displaytype AND `voiddate` IS NULL ';

if($sort == '1')
{
$sql4 .= ' ORDER BY `id` ASC';
}
else if($sort == '2')
{
$sql4 .= ' ORDER BY `id` DESC';
}
else if($sort == '3')
{
$sql4 .= ' ORDER BY `abvname` ASC';
}
else if($sort == '4')
{
$sql4 .= ' ORDER BY `abvname` DESC';
}
else if($sort == '5')
{
$sql4 .= ' ORDER BY `total` ASC';
}
else if($sort == '6')
{
$sql4 .= ' ORDER BY `total` DESC';
}
else if($sort == '7')
{
$sql4 .= ' ORDER BY `abvvehicle` ASC';
}
else if($sort == '8')
{
$sql4 .= ' ORDER BY `abvvehicle` DESC';
}
else{
$sql4 .= ' ORDER BY `creationdate` DESC';
}
$sth4 = $pdocxn->prepare($sql4);
$displaydate2 = $displaydate."%";
$sth4->bindParam(':displaydate',$displaydate2);
$sth4->bindParam(':locationid',$currentlocationid);
$sth4->bindParam(':displaytype',$displaytype);
$sth4->execute()or die(print_r($sth4->errorInfo(), true));
$i = '1';
while($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
{
$invoiceid = $row4['id'];
$invoicenumber = $row4['invoiceid'];
$subtotal = $row4['subtotal'];
$tax = $row4['tax'];
$total = $row4['total'];
$customerid = $row4['accountid'];
$vehicleid = $row4['vehicleid'];
if($customerid > '0')
{
$sth5 = $pdocxn->prepare('SELECT `firstname`,`lastname` FROM `accounts` WHERE accountid = \''.$customerid.'\'');
$sth5->execute();
while($row5 = $sth5->fetch(PDO::FETCH_ASSOC))
{
$databasefname = $row5['firstname'];
$firstname = stripslashes($databasefname);
$databaselname = $row5['lastname'];
$lastname = stripslashes($databaselname);
$fullname = $firstname." ".$lastname;
}}else{
	$fullname = "No Customer Selected";
}
$sth6 = $pdocxn->prepare('SELECT `year`,`make`,`model`,`description` FROM `vehicles` WHERE id = \''.$vehicleid.'\'');
$sth6->execute();
if ($sth6->rowCount() > 0)
{
while($row6 = $sth6->fetch(PDO::FETCH_ASSOC))
{
$year = $row6['year'];
$make = $row6['make'];
$model = $row6['model'];
$description1 = $row6['description'];
if($year > '0')
{
$displayvehicle = $year." ".$make." ".$model;
}
else{
$displayvehicle = $description1;
}
}
$displayvehicle = substr($displayvehicle,0,25);
}else
{
$displayvehicle = "No Vehicle Selected";
}

$fullname = substr($fullname,0,25);
echo "\n<tr id=\"highlight".$i."\"><td><a href=\"".$pagelink."?invoiceid=".$invoiceid."\" class=\"no-decoration\"target=\"_BLANK\">";
if($displaytype == '1'){
echo "<input type=\"button\" name=\"button\" class=\"btn-style\" id=\"".$invoicenumber."\" value=\"".$invoicenumber."\" /></a>";
}else{
echo "<input type=\"button\" name=\"button\" class=\"btn-style\" id=\"".$invoiceid."\" value=\"".$invoiceid."\" /></a>";
}
echo "</td><td><a href=\"".$pagelink."?invoiceid=".$invoiceid."\" class=\"no-decoration\"target=\"_BLANK\"><input type=\"button\" class=\"btn-style\" value=\"".$fullname."\" /></a></td><td><a href=\"".$pagelink."?invoiceid=".$invoiceid."\" class=\"no-decoration\"target=\"_BLANK\"><input type=\"button\" class=\"btn-style\" value=\"$".$total."\" /></a></td><td><a href=\"".$pagelink."?invoiceid=".$invoiceid."\" class=\"no-decoration\"target=\"_BLANK\"><input type=\"button\" class=\"btn-style\" value=\"".$displayvehicle."\" /></a></td></tr>";
$i ++;
$vehicleid = '0';
}
?>
</table>
</div>
<div class="right1">
<table><tr><td><form action="payments.php" method="post" name="payment">
<input type="hidden" name="payment" value="1">
<input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>">
<input type="hidden" name="accountid" value="<?php echo $accountid; ?>">
<input type="hidden" name="vehicleid" value="<?php echo $vehicleid; ?>">
<input type="hidden" name="submit" value="submit">
<input type="submit" class="smallbutton" alt="print" value="Enter Payment" name="submit"></form></td>
<td>
<form action="<?php echo $pagelink; ?>" method="post" name="copyinvoice">
<input type="hidden" name="copy" value="1">
<input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>">
<input type="hidden" name="submit" value="submit">
<input type="submit" class="smallbutton" alt="print" value="Copy Invoice" name="submit"></form>
</td></tr>
<tr><td><br />
<a href="<?php echo $pagelink; ?>?split=1" class="no-decoration">
<input type="button" class="smallbutton" alt="split" value="Split a Transation"></a>
</td>
<td><br />
<a href="account.php?combine=1" class="no-decoration">
<input type="button" class="smallbutton" alt="Combine" value="Combine Transactions"></a>
</td>
</tr>
<tr><td colspan="2"><br />
<a href="searchinvoice.php" class="no-decoration">
<input type="submit" class="smallbutton" alt="search" value="Search Invoice by keyword" name="submit"></a>
</td>
</tr>
<tr><td colspan="2"><br />
<a href="<?php echo $pagelink; ?>?completed=1" class="no-decoration">
<input type="submit" class="smallbutton" alt="search" value="Unsaved Invoices/Quotes" name="submit"></a>
</td>
</tr></table></div>
</div>
<script type="text/javascript">
$("table tr").click(function(){
    $($('#' + $(this).attr("href"))).siblings().hide().end().fadeIn();
});
</script>
</body>
</html>
<?php
}
?>