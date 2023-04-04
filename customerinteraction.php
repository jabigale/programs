<?php
/*
**navigation
//general submit form
//edit change mileage
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
//display html - no submit
//display html - submit
//record the inventory transaction in inventory_transactions

**Invoice type ids
//1 Customer Invoice
//2 Vendor Invoice
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
//13 Vendor Adjustment
//14 Return
//15 Vendor Credit
//16 Manually Entry
//17 Customer Credit
//18 Customer Refund
//19 Customer Begining Balance
//20 Vendor Begining Balance
//21 ME Deposti
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
$title = 'Customer Interaction';
$linkpage = 'customerinteraction.php';
$sort = '0';
$quicksearch = '0';
$db = 'realetp3_mtccalendar';
$invoicesubtotal = '0';
$accountid = '0';

session_start();
date_default_timezone_set('America/Chicago');
$currentdate = date('Y-n-j H:i:s');
$currentday = date('Y-n-j');
$currentday2 = date('Y-m-d');
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
$scheduletable = "scheduleloc".$currentlocationid;
$schedulelineitems = "s".$currentlocationid."line_items";
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
if($_POST['submit'] OR $_POST['inventorysubmit'] OR $_POST['editsubmit'] OR $_POST['changevehicle'] OR $_POST['servicesubmit'])
{
//general submit form
if(isset($_POST['invoiceid']))
{
	$invoiceid = $_POST['invoiceid'];
}
if($_POST['new']&&$_POST['new'] == '1')
{
//submit insert new transaction
if($_POST['accountid'])
{
	$accountid = $_POST['accountid'];
}else{
$accountid = '0';
}
$newlinenumber = '1';
$typeid = $_POST['type'];
$sth1 = $pdocxn->prepare('SELECT `id` FROM `customerinteractions` ORDER BY `id` DESC LIMIT 1');
$sth1->execute();
$row1 = $sth1->fetch(PDO::FETCH_ASSOC);
$lastinvid = $row1['id'];
$invoiceid = $lastinvid + '1';

$sth1 = $pdocxn->prepare('SELECT `invoiceid` FROM `customerinteractions` WHERE `invoiceid` > \'1\' AND `location` = :currentlocationid ORDER BY `invoiceid` DESC LIMIT 1');
$sth1->bindParam(':currentlocationid',$currentlocationid);
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
$lastinvnumber = $row1['invoiceid'];
$invoicenumber = $lastinvnumber + '1';
}
if($typeid =='1')
{
$sth2 = $pdocxn->prepare('INSERT INTO `customerinteractions`(`id`,`invoiceid`,`userid`,`type`,`location`,`creationdate`,`invoicedate`,`taxgroup`,`accountid`) VALUES (:id,:invoicenumber,:userid,:typeid,:location,:creationdate,:invoicedate,:taxgroup,:accountid)');
$sth2->bindParam(':invoicenumber',$invoicenumber);
}else{
$sth2 = $pdocxn->prepare('INSERT INTO `customerinteractions`(`id`,`userid`,`type`,`location`,`creationdate`,`invoicedate`,`taxgroup`,`accountid`) VALUES (:id,:userid,:typeid,:location,:creationdate,:invoicedate,:taxgroup,:accountid)');
}
$sth2->bindParam(':id',$invoiceid);
$sth2->bindParam(':userid',$currentid);
$sth2->bindParam(':typeid',$typeid);
$sth2->bindParam(':location',$currentlocationid);
$sth2->bindParam(':creationdate',$currentdate);
$sth2->bindParam(':invoicedate',$currentday);
$sth2->bindParam(':taxgroup',$taxgroup);
$sth2->bindParam(':accountid',$accountid);
$sth2->execute();
}

if(isset($_POST['invoicenumber'])&&$_POST_['invoicenumber']>'1')
{
//serach by invoice number
$invoicenumber = $_POST['invoicenumber'];
$getinv = $pdocxn->prepare('SELECT `id`,`type` FROM `customerinteractions` WHERE `invoiceid` = :invoicenumber AND `location` = :currentlocationid');
$getinv->bindParam(':currentlocationid',$currentlocationid);
$getinv->bindparam(':invoicenumber',$invoicenumber);
$getinv->execute();
while($getinvrow = $getinv->fetch(PDO::FETCH_ASSOC))
{
$invoiceid = $getinvrow['id'];
}
$sth4 = $pdocxn->prepare('SELECT `linenumber` FROM `ci_line_items` WHERE invoiceid = :inv ORDER BY `linenumber` DESC LIMIT 1');
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
$getinv = $pdocxn->prepare('SELECT `id`,`invoiceid`,`type`,`taxgroup`,`invoicedate` FROM `customerinteractions` WHERE `id` = :invoiceid AND `location` = :currentlocationid');
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
$sth4 = $pdocxn->prepare('SELECT `id` FROM line_items WHERE invoiceid = :inv');
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
$sth3 = $pdocxn->prepare('UPDATE `customerinteractions` SET `mileagein`=:mileagein WHERE id = :inv');
$sth3->bindParam(':inv',$invoiceid);
$sth3->bindParam(':mileagein',$mileage);
$sth3->execute();
}
if($_POST['void']&&$_POST['void']=='1')
{
//edit void invoice
$sth4 = $pdocxn->prepare('SELECT `qty`,`partid` FROM `ci_line_items` WHERE `invoiceid` = :inv');
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

$sth1 = $pdocxn->prepare('UPDATE `customerinteractions` SET `voiddate`=:voiddate WHERE `id` = :invoiceid');
$sth1->bindParam(':voiddate',$currentday);
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->execute();
header('location:customerinteraction.php?accountid='.$accountid.'');
}
//submit form general
if($_POST['changedate']&&$_POST['changedate']=='1')
{
//edit change date
$newdate = $_POST['newdate'];
$sth1 = $pdocxn->prepare('UPDATE `customerinteractions` SET `invoicedate`=:newdate WHERE `id` = :invoiceid');
$sth1->bindParam(':newdate',$newdate);
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->execute();
}
if($_POST['changecustomer']&&$_POST['changecustomer'] == '1')
{
//edit change customer
$newaccountid = $_POST['accountid'];
if(isset($_POST['vehicleid']))
{
$newvehicleid = $_POST['vehicleid'];
}
else{
$newvehicleid = '0';
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
if($firstname > '0')
{
$abvname = $firstname." ".$lastname;
}
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
}else{
$abvvehicle = $getvehiclerow['description'];
}
if($abvvehicle < '1')
{
$abvvehicle = '';
}}

$sth1 = $pdocxn->prepare('UPDATE `customerinteractions` SET `accountid`=:accountid,`vehicleid`=:vehicleid,`abvname`=:abvname,`abvvehicle`=:abvvehicle,`taxgroup`=:taxgroup WHERE `id` = :invoiceid');
$sth1->bindParam(':accountid',$newaccountid);
$sth1->bindParam(':vehicleid',$newvehicleid);
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->bindParam(':abvname',$abvname);
$sth1->bindParam(':abvvehicle',$abvvehicle);
$sth1->bindParam(':taxgroup',$taxclass);
$sth1->execute();
}}
if($_POST['changevehicle']&&$_POST['changevehicle']=='1')
{
//edit change vehicle
$vehiclechange=$_POST['vehiclechange'];
$getvehicle = $pdocxn->prepare('SELECT `year`,`make`,`model`,`description` from `vehicles` WHERE `id` = :vehicleid');
$getvehicle->bindParam(':vehicleid',$vehiclechange);
$getvehicle->execute();
while($getvehiclerow = $getvehicle->fetch(PDO::FETCH_ASSOC))
{
$year = $getvehiclerow['year'];
$make = $getvehiclerow['make'];
$description = $getvehiclerow['description'];
if($year = NULL OR $year < '1')
{
$abvvehicle = $description;
}
else{
$abvvehicle = $year." ".$make." ".$model;
}
if($abvvehicle > '1')
{
$abvvehicle = $getvehiclerow['description'];
}
else{
$abvvehicle = "";
}}
$sth1 = $pdocxn->prepare('UPDATE `customerinteractions` SET `vehicleid`=:vehicleid,`abvvehicle`=:abvvehicle WHERE `id` = :invoiceid');
$sth1->bindParam(':vehicleid',$vehiclechange);
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->bindParam(':abvvehicle',$abvvehicle);
$sth1->execute();
}
if($_POST['editvehicle1']&&$_POST['editvehicle1']=='1')
{
//edit vehicle info



$vehicledesc = $_POST['vehiclename'];
$vin = $_POST['vehiclevin'];
$license = $_POST['license'];
$state = $_POST['state'];
$invoiceid = $_POST['invoiceid'];
$vehicleid = $_POST['vehicleid'];


$sth1 = $pdocxn->prepare('UPDATE `customerinteractions` SET `abvvehicle`=:abvvehicle WHERE `id` = :invoiceid');
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->bindParam(':abvvehicle',$vehicledesc);
$sth1->execute();
}
if($_POST['delete']&&$_POST['delete']=='1')
{
//edit delete line
$lineid = $_POST['lineid'];

$sth4 = $pdocxn->prepare('SELECT `qty`,`partid` FROM `ci_line_items` WHERE `id` = :lineid');
$sth4->bindParam(':lineid',$lineid);
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
$deletedlinenumber = $_POST['deletedlinenumber'];
$sth1 = $pdocxn->prepare('DELETE FROM `ci_line_items` WHERE `id` = :lineid');
$sth1->bindParam(':lineid',$lineid);
$sth1->execute();


$sth2 = $pdocxn->prepare('UPDATE `ci_line_items` SET `linenumber` = `linenumber` - 1 WHERE `invoiceid` = :invoiceid AND `linenumber` > :deletedlinenumber');
$sth2->bindParam(':invoiceid',$invoiceid);
$sth2->bindParam(':deletedlinenumber',$deletedlinenumber);
$sth2->execute();


if($articleid = '0' && $typeid = '1')
{
$locationcolumn = "loc".$currentlocationid."_onhand";
$sth1 = $pdocxn->prepare('UPDATE `inventory` SET `'.$locationcolumn.'`=`'.$locationcolumn.'` + :qty WHERE `id` = :articleid');
$sth1->bindParam(':qty',$qty);
$sth1->bindParam(':articleid',$articleid);
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
$sth1 = $pdocxn->prepare('UPDATE `ci_line_items` SET `linenumber`=:currentline WHERE `invoiceid` = :invoiceid AND `linenumber` = :previousline');
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->bindParam(':currentline',$currentline);
$sth1->bindParam(':previousline',$previousline);
$sth1->execute();
$sth2 = $pdocxn->prepare('UPDATE `ci_line_items` SET `linenumber` = :previousline WHERE `id` = :lineid');
$sth2->bindParam(':lineid',$lineid);
$sth2->bindParam(':previousline',$previousline);
$sth2->execute();
}
if($_POST['down']&&$_POST['down'] == '1')
{
$sth1 = $pdocxn->prepare('UPDATE `ci_line_items` SET `linenumber` = :currentline WHERE `invoiceid` = :invoiceid AND `linenumber` = :nextline');
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->bindParam(':currentline',$currentline);
$sth1->bindParam(':nextline',$nextline);
$sth1->execute();
$sth2 = $pdocxn->prepare('UPDATE `ci_line_items` SET `linenumber`=:nextline WHERE `id` = :lineid');
$sth2->bindParam(':lineid',$lineid);
$sth2->bindParam(':nextline',$nextline);
$sth2->execute();
}
}
if($_POST['editsubmit']&&$_POST['editsubmit'] == '1')
{
//edit line item
$invoicenumber = $_POST['invoicenumber'];
$lineid = $_POST['lineid'];
$qty = $_POST['qty'];
$amount = $_POST['price'];
$lineid = $_POST['lineid'];
$comment = $_POST['comment'];
$fet = $_POST['fet'];
$totallineamount = $qty*$amount;
$sth1 = $pdocxn->prepare('UPDATE `ci_line_items` SET `qty`=:qty,`amount`=:amount,`comment`=:comment,`totallineamount`=:totallineamount WHERE `id` = :lineid');
$sth1->bindParam(':totallineamount',$totallineamount);
$sth1->bindParam(':qty',$qty);
$sth1->bindParam(':amount',$amount);
$sth1->bindParam(':lineid',$lineid);
$sth1->bindParam(':comment',$comment);
$sth1->execute();

}

if(isset($_POST['invoicenumber'])&&$_POST_['invoicenumber']>'1')
	{
//search by invoice number
$invoicenumber = $_POST['invoicenumber'];
$getinv = $pdocxn->prepare('SELECT `id`,`type`,`taxgroup`,`invoicedate`,`userid`,`location` FROM `customerinteractions` WHERE `invoiceid` = :invoicenumber AND `location` = :currentlocationid');
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
$sth4 = $pdocxn->prepare('SELECT `id` FROM line_items WHERE invoiceid = :inv');
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
$getinv = $pdocxn->prepare('SELECT `id`,`invoiceid`,`type`,`taxgroup`,`invoicedate` FROM `customerinteractions` WHERE `id` = :invoiceid AND `location` = :currentlocationid');
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
$sth4 = $pdocxn->prepare('SELECT `id` FROM line_items WHERE invoiceid = :inv');
$sth4->bindParam(':inv',$invoiceid);
$sth4->execute();
if(!$sth4) {
$linecount = '0';
}else{
$linecount = $sth4->rowCount();
}
$newlinenumber = $linecount + '1';
	}
//get tax info
$gettax = $pdocxn->prepare('SELECT `id`,`multiply`,`description` FROM `tax_rate` WHERE `id` = :taxgroup');
$gettax->bindparam(':taxgroup',$taxgroup);
$gettax->execute();
while($gettaxraterow = $gettax->fetch(PDO::FETCH_ASSOC))
{
$taxrate = $gettaxraterow['multiply'];
$taxdescription = $gettaxraterow['description'];
}
	if($_POST['copy']&&$_POST['copy'] == '1')
{
//copy a transaction
//$copytype = $_POST['copytype'];
$copytype = '1';
$sth1 = $pdocxn->prepare('SELECT `id`,`location`,`subtotal`,`tax`,`total`,`taxgroup` FROM `customerinteractions` WHERE `id` = :invoiceid LIMIT 1');
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
}
$sth1 = $pdocxn->prepare('SELECT `id` FROM `customerinteractions` ORDER BY `id` DESC LIMIT 1');
$sth1->execute();
$row1 = $sth1->fetch(PDO::FETCH_ASSOC);
$lastinvid = $row1['id'];
$invoiceid = $lastinvid + '1';

$sth1 = $pdocxn->prepare('SELECT `invoiceid` FROM `customerinteractions` WHERE `invoiceid` > \'1\' AND `location` = :currentlocationid ORDER BY `invoiceid` DESC LIMIT 1');
$sth1->bindParam(':currentlocationid',$currentlocationid);
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
$lastinvnumber = $row1['invoiceid'];
$invoicenumber = $lastinvnumber + '1';
}

$sql2 = "INSERT INTO `customerinteractions`(`id`,`invoiceid`,`type`,`location`,`creationdate`,`invoicedate`,`subtotal`,`tax`,`total`,`taxgroup`) VALUES (:id,:invoicenumber,:type,:location,:creationdate,:invoicedate,:subtotal,:tax,:total,:taxgroup)";
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

$sth2 = $pdocxn->prepare('SELECT `linenumber`,`qty`,`amount`,`partid`,`packageid` `serviceid`,`comment`,`fet`,`totallineamount`,`lineitem_typeid`,`lineitem_subtypeid`,`lineitem_saletype`,`hours`,`basecost` FROM `ci_line_items` WHERE `invoiceid` = :invoiceid');
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


$copysql = "INSERT INTO `ci_line_items`(`invoiceid`,`linenumber`,`qty`,`amount`,`partid`,`packageid`,`serviceid`,`comment`,`fet`,`totallineamount`,`lineitem_typeid`,`lineitem_subtypeid`,`lineitem_saletype`,`hours`,`basecost`) VALUES (:invoiceid,:linenumber,:qty,:amount,:partid,:packageid,:serviceid,:comment,:fet,:totallineamount,:lineitem_typeid,:lineitem_subtypeid,:lineitem_saletype,:hours,:basecost)";
$copysql2 = "INSERT INTO `ci_line_items`(`invoiceid`,`linenumber`,`qty`,`amount`,`partid`,`packageid`,`serviceid`,`comment`,`fet`,`totallineamount`,`lineitem_typeid`,`lineitem_subtypeid`,`lineitem_saletype`,`hours`,`basecost`) VALUES ('".$invoiceid."','".$linenumber."','".$qty."','".$amount."','".$partid."','".$packageid."','".$serviceid."','".$comment."','".$fet."','".$totallineamount."','".$lineitem_typeid."','".$lineitem_subtypeid."','".$lineitem_saletype."','".$hours."','".$basecost."')";
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
$sql2 = 'SELECT `id`,`multiply` FROM `tax_rate`WHERE `id` = :taxid';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':taxid',$taxgroup);
$sth2->execute();
$row2 = $sth2->fetch(PDO::FETCH_ASSOC);
$taxmultiply = $row2['multiply'];
$taxamount = $invamount*$taxmultiply;
$sql3 = "INSERT INTO `ci_tax_trans`(`transid`, `taxamount`,`lineid`) VALUES (:invoiceid,:taxamount,:lineid)";
$sth3 = $pdocxn->prepare($sql3);
$sth3->bindParam(':transactionid',$invoiceid);
$sth3->bindParam(':taxamount',$taxamount);
$sth3->bindParam(':lineid',$lastlineid);
$sth3->execute();
}
}

	if($_POST['split']&&$_POST['split'] == '1')
{
//split a transaction
//$copytype = $_POST['copytype'];
$copytype = '1';
$sth1 = $pdocxn->prepare('SELECT `id`,`location`,`subtotal`,`tax`,`total`,`taxgroup` FROM `customerinteractions` WHERE `id` = :invoiceid LIMIT 1');
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
}
$sth1 = $pdocxn->prepare('SELECT `id` FROM `customerinteractions` ORDER BY `id` DESC LIMIT 1');
$sth1->execute();
$row1 = $sth1->fetch(PDO::FETCH_ASSOC);
$lastinvid = $row1['id'];
$invoiceid = $lastinvid + '1';

$sth1 = $pdocxn->prepare('SELECT `invoiceid` FROM `customerinteractions` WHERE `invoiceid` > \'1\' AND `location` = :currentlocationid ORDER BY `invoiceid` DESC LIMIT 1');
$sth1->bindParam(':currentlocationid',$currentlocationid);
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
$lastinvnumber = $row1['invoiceid'];
$invoicenumber = $lastinvnumber + '1';
}

$sql2 = "INSERT INTO `customerinteractions`(`id`,`invoiceid`,`type`,`location`,`creationdate`,`invoicedate`,`subtotal`,`tax`,`total`,`taxgroup`) VALUES (:id,:invoicenumber,:type,:location,:creationdate,:invoicedate,:subtotal,:tax,:total,:taxgroup)";
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
foreach ($splt as $newsplit)
{
$sth2 = $pdocxn->prepare('SELECT `qty`,`amount`,`partid`,`packageid` `serviceid`,`comment`,`fet`,`totallineamount`,`lineitem_typeid`,`lineitem_subtypeid`,`lineitem_saletype`,`hours`,`basecost` FROM `ci_line_items` WHERE `invoiceid` = :invoiceid AND `linenumber` = :linenumber');
$sth2->bindParam(':invoiceid',$oldinvoiceid);
$sth2->bindParam(':linenumber',$linenumber);
$sth2->execute();
while($copyrow = $sth2->fetch(PDO::FETCH_ASSOC))
{
$linenumber = $newlinesplit;
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


$copysql = "INSERT INTO `ci_line_items`(`invoiceid`,`linenumber`,`qty`,`amount`,`partid`,`packageid`,`serviceid`,`comment`,`fet`,`totallineamount`,`lineitem_typeid`,`lineitem_subtypeid`,`lineitem_saletype`,`hours`,`basecost`) VALUES (:invoiceid,:linenumber,:qty,:amount,:partid,:packageid,:serviceid,:comment,:fet,:totallineamount,:lineitem_typeid,:lineitem_subtypeid,:lineitem_saletype,:hours,:basecost)";
$copysql2 = "INSERT INTO `ci_line_items`(`invoiceid`,`linenumber`,`qty`,`amount`,`partid`,`packageid`,`serviceid`,`comment`,`fet`,`totallineamount`,`lineitem_typeid`,`lineitem_subtypeid`,`lineitem_saletype`,`hours`,`basecost`) VALUES ('".$invoiceid."','".$linenumber."','".$qty."','".$amount."','".$partid."','".$packageid."','".$serviceid."','".$comment."','".$fet."','".$totallineamount."','".$lineitem_typeid."','".$lineitem_subtypeid."','".$lineitem_saletype."','".$hours."','".$basecost."')";
$copysth = $pdocxn->prepare($copysql);
$copysth->bindParam(':invoiceid',$invoiceid);
$copysth->bindParam(':linenumber',$newlinenumber);
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
$newlinesplit ++;

$lastlineid = $pdocxn->lastInsertId();
$sql2 = 'SELECT `id`,`multiply` FROM `tax_rate`WHERE `id` = :taxid';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':taxid',$taxgroup);
$sth2->execute();
$row2 = $sth2->fetch(PDO::FETCH_ASSOC);
$taxmultiply = $row2['multiply'];
$taxamount = $invamount*$taxmultiply;
$sql3 = "INSERT INTO `ci_tax_trans`(`transid`, `taxamount`,`lineid`) VALUES (:invoiceid,:taxamount,:lineid)";
$sth3 = $pdocxn->prepare($sql3);
$sth3->bindParam(':transactionid',$invoiceid);
$sth3->bindParam(':taxamount',$taxamount);
$sth3->bindParam(':lineid',$lastlineid);
$sth3->execute();
}
}
}


/*if($_POST['servicesubmit']&&$_POST['servicesubmit'] == '1')
{
$serviceid  = $_POST['serviceid'];
$cost  = $_POST['cost'];
$servicetitle = $_POST['servicetitle'];
$servicenote = $_POST['servicenote'];

$sth3 = $pdocxn->prepare('INSERT INTO `ci_line_items`(`invoiceid`,`amount`,`linenumber`,`comment`,`serviceid`,`totallineamount`,`qty`)VALUES(:invoiceid,:amount,:linenumber,:comment,:serviceid,:totallineamount,\'1\')');
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
$sth3 = $pdocxn->prepare('INSERT INTO `ci_line_items`(`invoiceid`,`amount`,`linenumber`,`comment`,`serviceid`,`totallineamount`)VALUES(:invoiceid,\'0\',:linenumber,:comment,:serviceid,\'0\')');
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
//inventory submit
$partid = $_POST['partid'];
$qty = $_POST['qty'];
if($fet < '.001')
{
	$fet = '0';
}
if($invoiceid < '1')
{
$typeid = $_POST['type'];
$sth1 = $pdocxn->prepare('SELECT `id` FROM `customerinteractions` ORDER BY `id` DESC LIMIT 1');
$sth1->execute();
$row1 = $sth1->fetch(PDO::FETCH_ASSOC);
$lastinvid = $row1['id'];
$invoiceid = $lastinvid + '1';
$sth2 = $pdocxn->prepare('INSERT INTO `customerinteractions`(`id`,`userid`,`type`,`location`,`creationdate`) VALUES (:id,:userid,:type,:location,:creationdate)');
$sth2->bindParam(':id',$invoiceid);
$sth2->bindParam(':userid',$currentid);
$sth2->bindParam(':type',$typeid);
$sth2->bindParam(':location',$currentlocationid);
$sth2->bindParam(':creationdate',$currentdate);
$sth2->execute();
$newlinenumber = '1';
}
if($typeid == '1')
{
$locationcolumn = "loc".$currentlocationid."_onhand";
$sth1 = $pdocxn->prepare('UPDATE `inventory` SET `'.$locationcolumn.'`=`'.$locationcolumn.'` - :qty WHERE `id` = :partid');
$sth1->bindParam(':qty',$qty);
$sth1->bindParam(':partid',$partid);
$sth1->execute();
}
$sql1 = 'SELECT * FROM `inventory` WHERE `id` = :partid';
$sth1 = $pdocxn->prepare($sql1);
$sth1->bindParam(':partid',$partid);
$sth1->execute();
if ($sth1->rowCount() > 0)
{
	while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$articleid = $row1['part_number'];
	$type = $row1['type'];
	$brandid = $row1['manid'];
	$model = $row1['model'];
	$mileage = $row1['warranty'];
	$width = $row1['width'];
	$ratio = $row1['ratio'];
	$rim = $row1['rim'];
	$size = $width."/".$ratio.$rim;
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
		$displayply = "(".$ply." ply)";
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
$query2 = mysqli_query($sql2);
while ($row2 = mysqli_fetch_assoc($query2))
	{
	$brand = $row2['brand'];
	$description = $articleid.", ".$size." ".$brand." ".$model." ".$load_index.$speed." ".$displayply." ply)";
	}
	}}

//record the inventory transaction in inventory_transactions
//$sql11 = 'INSERT INTO `inventory_transactions` (`invoiceid`,`qty`,`amount`,`partid`,`transactiontype`,`location`,`accountid`)VALUES('.$invoiceid.','.$qty.','.$price.','.$partid.','.$typeid.','.$currentlocationid.','.$accountid.')';
//echo $sql11;

$sth5 = $pdocxn->prepare('INSERT INTO `inventory_transactions` (`invoiceid`,`qty`,`amount`,`partid`,`transactiontype`,`location`,`accountid`)VALUES(:invoiceid,:qty,:amount,:partid,:transactiontype,:location,:accountid)');
$sth5->bindParam(':invoiceid',$invoiceid);
$sth5->bindParam(':qty',$qty);
$sth5->bindParam(':amount',$price);
$sth5->bindParam(':partid',$partid);
$sth5->bindParam(':transactiontype',$typeid);
//fkmfkmfkmfkm
$sth5->bindParam(':location',$currentlocationid);
$sth5->bindParam(':accountid',$accountid);
$sth5->execute()or die(print_r($sth5->errorInfo(), true));


$totallineamount = $qty * $price;
$sth3 = $pdocxn->prepare('INSERT INTO `ci_line_items`(`invoiceid`,`qty`,`amount`,`linenumber`,`comment`,`fet`,`totallineamount`,`partid`)VALUES(:invoiceid,:qty,:amount,:linenumber,:comment,:fet,:totallineamount,:partid)');
$sth3->bindParam(':totallineamount',$totallineamount);
$sth3->bindParam(':invoiceid',$invoiceid);
$sth3->bindParam(':qty',$qty);
$sth3->bindParam(':amount',$price);
$sth3->bindParam(':comment',$description);
$sth3->bindParam(':fet',$fet);
$sth3->bindParam(':linenumber',$newlinenumber);
$sth3->bindParam(':partid',$partid);
$sth3->execute();
$newlinenumber ++;

$lastlineid = $pdocxn->lastInsertId();
$sql2 = 'SELECT `id`,`multiply` FROM `tax_rate`WHERE `id` = :taxid';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':taxid',$taxgroup);
$sth2->execute();
$row2 = $sth2->fetch(PDO::FETCH_ASSOC);
$taxmultiply = $row2['multiply'];
$taxamount = $invamount*$taxmultiply;
$sql3 = "INSERT INTO `ci_tax_trans`(`transid`, `taxamount`,`lineid`) VALUES (:invoiceid,:taxamount,:lineid)";
$sth3 = $pdocxn->prepare($sql3);
$sth3->bindParam(':transactionid',$invoiceid);
$sth3->bindParam(':taxamount',$taxamount);
$sth3->bindParam(':lineid',$lastlineid);
$sth3->execute();
//addlinenumber
}
if($_POST['packageid']&&$_POST['packageid'] > '0')
{
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
$totallineamount = $lr1cost * $pkgqty;
$linesth2 = $pdocxn->prepare('INSERT INTO `ci_line_items`(`invoiceid`,`qty`,`amount`,`linenumber`,`comment`,`totallineamount`)VALUES(:invoiceid,:qty,:amount,:linenumber,:comment,:totallineamount)');
$linesth2->bindParam(':invoiceid',$invoiceid);
$linesth2->bindParam(':qty',$pkgqty);
$linesth2->bindParam(':totallineamount',$totallineamount);
$linesth2->bindParam(':amount',$lr1cost);
$linesth2->bindParam(':comment',$packagetitle);
$linesth2->bindParam(':linenumber',$newlinenumber);
$linesth2->execute();
$newlinenumber ++;

$lastlineid = $pdocxn->lastInsertId();
$sql2 = 'SELECT `id`,`multiply` FROM `tax_rate`WHERE `id` = :taxid';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':taxid',$taxgroup);
$sth2->execute();
$row2 = $sth2->fetch(PDO::FETCH_ASSOC);
$taxmultiply = $row2['multiply'];
$taxamount = $invamount*$taxmultiply;
$sql3 = "INSERT INTO `ci_tax_trans`(`transid`, `taxamount`,`lineid`) VALUES (:invoiceid,:taxamount,:lineid)";
$sth3 = $pdocxn->prepare($sql3);
$sth3->bindParam(':transactionid',$invoiceid);
$sth3->bindParam(':taxamount',$taxamount);
$sth3->bindParam(':lineid',$lastlineid);
$sth3->execute();
//addlinenumber
}
}
if($_POST['newcommentform']&&$_POST['newcommentform'] == '1')
{
//submit newcomment
	$qty = $_POST['newqty'];
	$amount = $_POST['newprice'];
	$comment = $_POST['newcomment'];
	$fet = $_POST['newfet'];
	$totallineamount = $qty * $amount;
$sth1 = $pdocxn->prepare('INSERT INTO `ci_line_items`(`invoiceid`,`qty`,`amount`,`linenumber`,`comment`,`fet`,`totallineamount`)VALUES(:invoiceid,:qty,:amount,:linenumber,:comment,:fet,:totallineamount)');
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->bindParam(':totallineamount',$totallineamount);
$sth1->bindParam(':qty',$qty);
$sth1->bindParam(':amount',$amount);
$sth1->bindParam(':linenumber',$newlinenumber);
$sth1->bindParam(':comment',$comment);
$sth1->bindParam(':fet',$fet);
$sth1->execute();
$newlinenumber ++;

$lastlineid = $pdocxn->lastInsertId();
$sql2 = 'SELECT `id`,`multiply` FROM `tax_rate`WHERE `id` = :taxid';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':taxid',$taxgroup);
$sth2->execute();
$row2 = $sth2->fetch(PDO::FETCH_ASSOC);
$taxmultiply = $row2['multiply'];
$taxamount = $invamount*$taxmultiply;
$sql3 = "INSERT INTO `ci_tax_trans`(`transid`, `taxamount`,`lineid`) VALUES (:invoiceid,:taxamount,:lineid)";
$sth3 = $pdocxn->prepare($sql3);
$sth3->bindParam(':transactionid',$invoiceid);
$sth3->bindParam(':taxamount',$taxamount);
$sth3->bindParam(':lineid',$lastlineid);
$sth3->execute();
//addlinenumber
}


$sth3 = $pdocxn->prepare('SELECT * FROM invoice WHERE id = :inv');
$sth3->bindParam(':inv',$invoiceid);
$sth3->execute();
$row3 = $sth3->fetch(PDO::FETCH_ASSOC);
$accountid = $row3['accountid'];
$typeid = $row3['type'];
$userid = $row3['userid'];
$vehicleid = $row3['vehicleid'];
$mileagein = $row3['mileagein'];
$mileageout = $row3['mileageout'];
$location = $row3['location'];
$invoicenumber = $row3['invoiceid'];
$taxgroup = $row3['taxgroup'];
$invoicedate = $row3['invoicedate'];
$invoicedate2 = new DateTime($invoicedate);
$displayinvoicedate = $invoicedate2->format('n/j/Y');
$displayinvoicedate2 = $invoicedate2->format('Y-m-d');

if($accountid > '0')
{
$sth4 = $pdocxn->prepare('SELECT * FROM accounts WHERE accountid = :acct');
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
$daddress = $address." ".$address2." ".$city.", ".$state." ".$zip;
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
if($lastactivedate > '1')
{
$lastactivedate2 = new DateTime($lastactivedate);
$dlastactivedate = $lastactivedate2->format('n/j/Y');
}
else{
	$dlastactivedate = 'N/A';
}
$fullname = $fname." ".$lname;
$dcustomerinfo1 = "<font color=\"blue\">".$fullname."</font><br /><font color=\"blue\">".$phone1."</font>";
//$dcustomerinfo1 = "<font color=\"blue\">".$fullname."</font> <a href=\"\" class=\"no-decoration\"><input type=\"button\" class=\"smallbutton\" value=\"(last active ".$dlastactivedate.")\"></a><br /><font color=\"blue\">".$phone1."</font>";
$dcustomerinfo2 = "<font color=\"blue\">".$daddress."</font>";
if($accountid < '1')
{
$dcustomerinfo = "No customer selected";
}
if($phone1 > '0')
	{
		$dphone1 = "<tr><td class=\"left\">Phone 1: ".$phone1."</td><td>Contact: ".$contact1."</td></tr>";
	}
	else
		{
			$dphone1 = "";
		}
if($phone2 > '0')
	{
		$dphone2 = "<tr><td class=\"left\">Phone 2: ".$phone2."</td><td>Contact: ".$contact2."</td></tr>";
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
if($taxid > '0')
	{
		$taxid = $taxid;
	}
	else
		{
			$taxid = "0";
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
if($vehicleid > '0')
{
$sql5 = 'SELECT * FROM `vehicles` WHERE `id` = :vehicleid';
$sth5 = $pdocxn->prepare($sql5);
$sth5->bindParam(':vehicleid',$vehicleid);
$sth5->execute();
if ($sth5->rowCount() > 0)
{
while($row5 = $sth5->fetch(PDO::FETCH_ASSOC))
	{
	$year = $row5['year'];
	$model = $row5['model'];
	$make = $row5['make'];
	$vin = $row5['vin'];
	$sobmodel = $row5['submodel'];
	$engine = $row5['engine'];
	$license = $row5['license'];
	$vehiclestate = $row5['state'];
	$description = $row5['description'];
if($year > '1')
{
$dvehicleinfo = "<b>".$year." ".$make." ".$model." ".$submodel." ".$engine."</b></td><td href=\"vehicleinfo\">VIN: ".$vin."</td><td href=\"vehicleinfo\">License: ".$license." (".$vehiclestate.")";
$dvehicleinfo1 = "<font color=\"red\"><b>".$year." ".$make." ".$model." ".$submodel." ".$engine."</b><br /><form name=\"updatemileage\" id=\"mileageform\" action=\"customerinteraction.php?accountid=".$accountid."\" method=\"post\">Mileage: <input type=\"textbox\" id=\"mileage\" name=\"mileage1\" value=\"".$mileagein."\" autocomplete=\"off\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"updatemiles\" value=\"1\"><input type=\"submit\" name=\"submit\" class=\"xsmallbutton\" value=\"update miles\"></form></font>";
$dvehicleinfo1a = "<font color=\"red\">VIN: ".$vin." License: ".$license." (".$vehiclestate.")</font>";
$dvehicleinfo2 = "<b>".$year." ".$make." ".$model." ".$submodel." ".$engine."</b>";
$dvehicleinfo2nb = $year." ".$make." ".$model." ".$submodel." ".$engine;
$selectvehicleinfo = "".$year." ".$make." ".$model." ".$submodel." ".$engine."   VIN: ".$vin."  License: ".$license." ";
}
else
{
$dvehicleinfo = "<b>".$description."</b></td><td href=\"vehicleinfo\">VIN: ".$vin."</td><td href=\"vehicleinfo\">License: ".$license." (".$vehiclestate.")";
$dvehicleinfo1 = "<font color=\"red\"><b>".$description."</b><br /><form name=\"updatemileage\" id=\"mileageform\" action=\"customerinteraction.php?accountid=".$accountid."\" method=\"post\">Mileage: <input type=\"textbox\" id=\"mileage\" name=\"mileage1\" value=\"".$mileagein."\" autocomplete=\"off\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"updatemiles\" value=\"1\"><input type=\"submit\" name=\"submit\" class=\"xsmallbutton\" value=\"update miles\"></form></font>";
$dvehicleinfo1a = "<font color=\"red\">VIN: ".$vin." License: ".$license." (".$vehiclestate.")</font>";
$dvehicleinfo2 = "<b>".$description."</b>";
$dvehicleinfo2nb = $description;
$selectvehicleinfo = "".$description."   VIN: ".$vin."   License: ".$license."";
}

${"ip".$tri} = "<div id=\"$tri\"><div class=\"q1\"><table class=\"righttable\"><tr><td colspan=\"2\" class=\"center\"><a href=\"accounthistory.php?accountid=".$accountid."\">Account History</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"editaccounts.php?accountid=".$accountid."\">Edit Info</a></td></tr><tr><td colspan=\"2\" class=\"center\">$fullname</td></tr><tr><td colspan=\"2\" class=\"left\">$address&nbsp;$city,&nbsp;$state&nbsp;$zip</td></tr><tr><td colspan=\"2\">$address2</td></tr>".$dphone1."".$dphone2."".$dphone3."".$dphone4."".$dfax."<tr></table></div><div class=\"q3\"><table class=\"righttable\"><tr><th>Vehicle</th><th>License</th><th>VIN</th></tr><tr><td class=\"left\">$year&nbsp;&nbsp;$make&nbsp;&nbsp;$model</td><td></td><td>$vin</td></tr></table></div></div>";
//${"ip".$tri} = "<div id=\"$tri\"><div class=\"q1\"><table class=\"righttable\"><tr><td colspan=\"2\" class=\"center\">Info&nbsp;&nbsp;&nbsp;<a href=\"vehicles.php?accountid=".$accountid."\">Vehicles</a>&nbsp;&nbsp;&nbsp;<a href=\"editaccounts.php?accountid=".$accountid."\">Edit Info</a></td></tr><tr><td colspan=\"2\" class=\"center\">$fullname</td></tr><tr><td colspan=\"2\" class=\"left\">$address&nbsp;$city,&nbsp;$state&nbsp;$zip</td></tr><tr><td colspan=\"2\">$address2</td></tr>".$dphone1."".$dphone2."".$dphone3."".$dphone4."".$dfax."<tr><td>e-mail:</td><td>$email</td></tr><tr><td>Cedit Limit:</td><td>".$creditlimit."</td></tr><tr><td>Tax ID: ".$taxid."</td><td>Tax Class: ".$dtaxclass."</td></tr><tr><td>Price Class: ".$dpriceclass."</td><td>Require PO: ".$requirepo."</td></tr><tr><td colspan=\"2\">Account Note: ".$comment."</td></tr><tr><td>Last visited:</td><td>".$dlastactivedate."</td></tr></table></div></div>";
$tri ++;
}}}
else{
$dvehicleinfo = "<input type=\"button\" class=\"btn-style\" value=\"Select Vehicle\"></td>";
}
}
else {
${"ip".$tri} = "<div id=\"$tri\"><div class=\"q1\"><table class=\"righttable\"><tr><td colspan=\"2\" class=\"center\"><a href=\"accounthistory.php?accountid=".$accountid."\">Account History</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"editaccounts.php?accountid=".$accountid."\">Edit Info</a></td></tr><tr><td colspan=\"2\" class=\"center\">$fullname</td></tr><tr><td colspan=\"2\" class=\"left\">$address&nbsp;$city,&nbsp;$state&nbsp;$zip</td></tr><tr><td colspan=\"2\">$address2</td></tr>".$dphone1."".$dphone2."".$dphone3."".$dphone4."".$dfax."<tr></table></div><div class=\"q3\"><table class=\"righttable\"><tr><th>Vehicle</th><th>License</th><th>VIN</th></tr><tr><td class=\"left\">$year&nbsp;&nbsp;$make&nbsp;&nbsp;$model</td><td></td><td>$vin</td></tr></table></div></div>";
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
$sql7 = 'SELECT `id` FROM `notes` WHERE `invoiceid` = :invoiceid AND `notetype` = \'1\'';
$sth7 = $pdocxn->prepare($sql7);
$sth7->bindParam(':invoiceid',$invoiceid);
$sth7->execute();
while($row7 = $sth7->fetch(PDO::FETCH_ASSOC))
{
	$noteid = $row7['id'];
}
if($id>'0')
{
$sql7 = 'UPDATE `notes` SET `note`=:note WHERE `invoiceid`=:invoiceid)';
//$sql72 = 'INSERT INTO `notes`(`note`,`invoiceid`,`accountid`,`vehicleid`,`notetype`)VALUES(\''.$note.'\',\''.$invoiceid.'\',\''.$accountid.'\',\''.$vehicleid.'\',\''.$notetype.'\')';
//echo $sql72;
$sth7 = $pdocxn->prepare($sql7);
$sth7->bindParam(':note',$note);
$sth7->bindParam(':invoiceid',$invoiceid);
$sth7->execute();
}else{

$sql7 = 'INSERT INTO `notes`(`note`,`invoiceid`,`accountid`,`vehicleid`,`notetype`)VALUES(:note,:invoiceid,:accountid,:vehicleid,:notetype)';
//$sql72 = 'INSERT INTO `notes`(`note`,`invoiceid`,`accountid`,`vehicleid`,`notetype`)VALUES(\''.$note.'\',\''.$invoiceid.'\',\''.$accountid.'\',\''.$vehicleid.'\',\''.$notetype.'\')';
//echo $sql72;
$sth7 = $pdocxn->prepare($sql7);
$sth7->bindParam(':note',$note);
$sth7->bindParam(':invoiceid',$invoiceid);
$sth7->bindParam(':accountid',$accountid);
$sth7->bindParam(':vehicleid',$vehicleid);
$sth7->bindParam(':notetype',$notetype);
$sth7->execute();
}
}

$note = '';
$sql7 = 'SELECT `note` FROM `notes` WHERE `invoiceid` = :invoiceid AND `notetype` = \'1\'';
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
<title><?php echo $title; ?></title>
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
<script src="scripts/autocomplete.js" type="text/javascript"></script>
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

if($currentlocationid == '1')
{
echo "<div id=\"header\">";
}
else{
echo "<div id=\"header2\">";
}
?>
<table><tr><td>
<form action="customerinteraction.php?accountid=<?php echo $accountid;?>" method="post" name="voidinvoice">
<input type="hidden" name="void" value="1">
<input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>">
<input type="hidden" name="submit" value="1">
<input type="submit" class="cancel" value="Void <?php echo $typename; ?>" onclick="myFunction()"></form>
</td><td>
<a href="customerinteraction.php?accountid=<?php echo $accountid;?>" class="no-decoration">
<input type="button" class="save" value="Save"></a></td>
<td><form action="printcustomerinteraction.php" method="post" name="printinvoice">
<input type="hidden" name="copies" value="2">
<input type="hidden" name="print" value="1">
<input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>">
<input type="hidden" name="submit" value="1">
<input type="submit" class="save" alt="print" value="Print <?php echo $typename; ?>" name="submit"></form></td><td><table cellspacing="0"><tr><td><form action="printcustomerinteraction.php" method="post" name="printinvoice">
<input type="hidden" name="copies" value="1">
<input type="hidden" name="print" value="1">
<input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>">
<input type="hidden" name="submit" value="1">
<input type="submit" class="smallbutton" alt="print" value="1" name="submit"></form></td></tr>
<tr><td>
<form action="printcustomerinteraction.php" method="post" name="printinvoice">
<input type="hidden" name="copies" value="3">
<input type="hidden" name="print" value="1">
<input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>">
<input type="hidden" name="submit" value="1">
<input type="submit" class="smallbutton" alt="print" value="3" name="submit"></form></td></tr></table><td>
<form action="emailcustomerinteraction.php" method="post" name="emailinvoice">
<input type="hidden" name="email" value="1">
<input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>">
<input type="hidden" name="submit" value="1">
<input type="submit" class="save" value="Email <?php echo $typename; ?>"></form></td><td>
<?php
if($typeid == '6')
{}else{
if($typeid == '1')
{
?>
<form action="payments.php" method="post" name="payment">
<input type="hidden" name="invoiceform" value="1">
<input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>">
<input type="hidden" name="accountid" value="<?php echo $accountid; ?>">
<input type="hidden" name="vehicleid" value="<?php echo $vehicleid; ?>">
<input type="hidden" name="invoiceamount" value="<?php echo $invoiceformtotal; ?>">
<input type="submit" class="save" value="Enter Payment"></form>
<?php
}else{
?>
<form action="customerinteraction.php?accountid=<?php echo $accountid; ?>" method="post" name="changetype">
<input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>">
<input type="hidden" name="changetoinvoice" value="1">
<input type="submit" class="save" value="Change to Invoice"></form>
<?php
}
?></td><td>
<form action="customerinteraction.php?accountid=<?php echo $accountid; ?>" method="post" name="copyinvoice">
<input type="hidden" name="copy" value="1">
<input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>">
<input type="hidden" name="accountid" value="<?php echo $accountid; ?>">
<input type="hidden" name="vehicleid" value="<?php echo $vehicleid; ?>">
<input type="hidden" name="submit" value="1">
<input type="submit" class="save" alt="print" value="Copy <?php echo $typename; ?>" name="submit"></form></td>
<?php
}
?>
</tr></table></div>
<div id="selecteduser"><form name="current1" action="customerinteraction.php?accountid=<?php echo $accountid; ?>" method="POST"><table id="floatleft"><tr><td class="currentuser">Current User:</td><td class="currentitem"><div class="styled-select black rounded"><input type="hidden" name="userform" value="1">
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
</select></div><input type="hidden" name="form" value="1"></td></form>
</td><td><?php echo $typename; ?> <b>#<?php if($typeid == '1'){ echo $invoicenumber; }else{ echo $invoiceid; }?></b></td><td href="invoicedate">Date: <b><?php echo $displayinvoicedate; ?></b></td><td>PO Number: <input type="textbox" name="ponumber" value="<?php echo $ponumber; ?>"></td></tr></table></div>
<div id="content">
<div id="left">
<?php
if($accountid > '0')
{
?>
<table width="100%">
	<tr><td href="customerinfo" class="tdleft" width="50%"><?php echo $dcustomerinfo1; ?></td><td href="vehicleinfo" class="tdleft" width="50%"><?php echo $dvehicleinfo1; ?></td></tr>
	<tr><td href="customerinfo" class="tdleft"><?php echo $dcustomerinfo2; ?></td><td href="vehicleinfo" class="tdleft"><?php echo $dvehicleinfo1a; ?></td></tr>
</table>
<?php
}else
{
echo "<table><tr><td><form name=\"changecustomer\" action=\"account.php\" method=\"post\"><input type=\"hidden\" name=\"changecustomer\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"submit\" name=\"submit\" class=\"quotebutton\" value=\"Select Customer\" /></form></td></tr></table>";
}
?>
<table id="highlightTable" class="blueTable">
<thead>
<tr>
<th>Qty</th>
<th>Description</th>
<th>FET</th>
<th>Unit Price</th>
<th>Ext Price</th>
</tr>
</thead>
<tbody>
	<?php
$sbi = '1';
$tri = '1';
$qri = '1';
$sth4 = $pdocxn->prepare('SELECT * FROM `ci_line_items` WHERE `invoiceid` = :inv ORDER BY `linenumber` ASC');
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
if($invqty == '0'){
	$invqty= '';
	$unitamount = '';
$invamount = '';
$unitprice = '';
$dextprice = '';
}else{
$unitamount = $extprice/$invqty;
setlocale(LC_MONETARY,"en_US");
$invamount = money_format('%(#0.2n',$unitamount);
$unitprice = round($unitamount,2);
$dextprice = money_format('%(#0.2n',$extprice);
}
$linenumber = $row4['linenumber'];
$invoicesubtotal = $invoicesubtotal+$extprice;
if($linenumber == '1')
{
	$displayup = "<td>&nbsp;</td>";
}
else {
	$displayup = "<td><form action=\"customerinteraction.php\" method=\"post\" name=\"linemoveform\"><input type=\"hidden\" name=\"currentline\" value=\"".$linenumber."\"><input type=\"hidden\" name=\"up\" value=\"1\"><input type=\"hidden\" name=\"lineid\" value=\"".$lineid."\"><input type=\"hidden\" name=\"linemove\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"submit\" value=\"submit\"><input type=\"image\" src=\"images/icons/up.png\" alt=\"up\" name=\"submit\" width=\"20\"></form></td>";
}
if($linenumber == $linecount)
{
	$displaydown = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
}
else {
	$displaydown = "<form action=\"customerinteraction.php\" method=\"post\" name=\"linvemoveform\"><input type=\"hidden\" name=\"currentline\" value=\"".$linenumber."\"><input type=\"hidden\" name=\"down\" value=\"1\"><input type=\"hidden\" name=\"lineid\" value=\"".$lineid."\"><input type=\"hidden\" name=\"linemove\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"submit\" value=\"submit\"><input type=\"image\" src=\"images/icons/down.png\" alt=\"down\" name=\"submit\" width=\"20\"></form>";
}
$prevlineid = $row4['id'];
	echo "\n<tr href=\"$tri\" id=\"".$qri."row\"><td>$invqty</td><td>$invcomment</td><td>$fet</td><td>$invamount</td><td>$dextprice</td></tr>";
${"ip".$tri} = "\n<div id=\"$tri\"><div class=\"q1\"><form name=\"updatecomment\" method=\"post\" action=\"customerinteraction.php\"><table class=\"righttable\"><tr>";
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
${"ip".$tri} .= "<td colspan=\"3\" class=\"left\">".$invcomment."</td>";
}else{
${"ip".$tri} .= "<td colspan=\"3\" class=\"left\"><textarea class=\"wideinput\" name=\"comment\" autocomplete=\"off\" id=\"".$qri."box\">".$invcomment."</textarea></td>";
}}
if($typeid == '6')
{
${"ip".$tri} .= "</tr><tr><td class=\"left\">Payment Amount: <input type=\"text\" class=\"narrowinput\" name=\"price\" value=\"$unitprice\" autocomplete=\"off\"></td></tr><tr><td class=\"center\" colspan=\"2\"><input type=\"hidden\" name=\"editsubmit\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"lineid\" value=\"".$lineid."\"><input type=\"hidden\" name=\"submit\" value=\"submit\"><input type=\"submit\" class=\"xsmallbutton\" alt=\"down\" value=\"Update\"></form></td><td><form action=\"customerinteraction.php\" method=\"post\" name=\"deletelinenumber\"><input type=\"hidden\" name=\"delete\" value=\"1\"><input type=\"hidden\" name=\"deletedlinenumber\" value=\"".$linenumber."\"><input type=\"hidden\" name=\"lineid\" value=\"".$lineid."\"><input type=\"hidden\" name=\"partid\" value=\"$invpartid\"><input type=\"hidden\" name=\"qty\" value=\"".$invqty."\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"submit\" value=\"submit\"><input type=\"submit\" class=\"cancel-small\" alt=\"down\" value=\"Delete Item\"></form></td></tr></table></form><table width=\"10%\"><tr>".$displayup."<td></td></tr><tr><td>".$displaydown."</td></tr></table></div></div>\n";	
}else{
${"ip".$tri} .= "</tr><tr><td class=\"left\">Unit Price: <input type=\"text\" class=\"narrowinput\" name=\"price\" value=\"$unitprice\" autocomplete=\"off\"></td><td class=\"left\">QTY:<input type=\"number\" class=\"narrowinput\" name=\"qty\" value=\"".$invqty."\" step=\"any\" autocomplete=\"off\"></td><td class=\"center\">FET: <input type=\"text\" class=\"narrowinput\" name=\"newfet\" value=\"$fet\" autocomplete=\"off\"></td></tr><tr><td class=\"center\" colspan=\"2\"><input type=\"hidden\" name=\"editsubmit\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"lineid\" value=\"".$lineid."\"><input type=\"hidden\" name=\"submit\" value=\"submit\"><input type=\"submit\" class=\"xsmallbutton\" alt=\"down\" value=\"Update\"></form></td><td><form action=\"customerinteraction.php\" method=\"post\" name=\"deletelinenumber\"><input type=\"hidden\" name=\"delete\" value=\"1\"><input type=\"hidden\" name=\"deletedlinenumber\" value=\"".$linenumber."\"><input type=\"hidden\" name=\"lineid\" value=\"".$lineid."\"><input type=\"hidden\" name=\"partid\" value=\"$invpartid\"><input type=\"hidden\" name=\"qty\" value=\"".$invqty."\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"submit\" value=\"submit\"><input type=\"submit\" class=\"cancel-small\" alt=\"down\" value=\"Delete Item\"></form></td></tr></table></form><table width=\"10%\"><tr>".$displayup."<td></td></tr><tr><td>".$displaydown."</td></tr></table></div></div>\n";
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
echo "<tr href=\"add\" id=\"additemrow\"><td></td><td><b>Add Item </b><img src=\"images/buttons/add-black.png\" width=\"25\"> </td><td></td><td></td><td></td></tr>";
echo "<tr href=\"note\" id=\"addnoterow\"><td></td><td><b>View/Add Note</b><img src=\"images/buttons/add-black.png\" width=\"25\"> </td><td></td><td></td><td></td></tr>";
$taxtotal = $invoicesubtotal*$taxrate;
$invoicetotal = $invoicesubtotal + $taxtotal;
$invoiceformtotal = round($invoicetotal,2);
$dtaxtotal = money_format('%(#0.2n',$taxtotal);
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
$sql10 = "SELECT `id`,`invoicedate` FROM `customerinteractions` WHERE `id` = :paymentid";
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
$sql11 = "SELECT `id`,`totallineamount`,`lineitem_typeid`,`comment` FROM `ci_line_items` WHERE `invoiceid` = :paymentid";
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
echo "<tr class=\"total\"><td colspan=\"4\" class=\"tdright\">Invoice Balance:&nbsp;&nbsp;&nbsp;&nbsp;</td><td>".$dinvbalance."</td></tr>";
echo "<tr class=\"total\"><td colspan=\"4\" class=\"tdright\">Current Account Balance:&nbsp;&nbsp;&nbsp;&nbsp;</td><td>".$dinvbalance."</td></tr>";
}
?></tbody></table></form></div>
<div class="right">
<?php
$sth1 = $pdocxn->prepare('UPDATE `customerinteractions` SET `subtotal`=:subtotal,`tax`=:tax,`total`=:total WHERE `id` = :invoiceid');
$sth1->bindParam(':subtotal',$invoicesubtotal);
$sth1->bindParam(':tax',$taxtotal);
$sth1->bindParam(':total',$invoicetotal);
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->execute();
while ($tri > 0) {
        echo ${"ip".$tri};
		$tri --;
}
echo "\n<div id=\"customerinfo\"><div class=\"q1\"><table class=\"righttable\"><tr><td class=\"left\">".$fullname."</td><td>";
echo "<form name=\"editcustomer\" action=\"editcutomer.php\" method=\"POST\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\">";
echo "<input type=\"submit\" name=\"submit\" class=\"cancel-small\" value=\"Edit Customer Information\" /></td>";
echo "</form></td></tr><tr><td colspan=\"2\" class=\"left\">".$daddress."</td></tr><tr><td>Phone 1:</td><td>".$phone1."</td></tr><tr><td>Phone 2:</td><td class=\"center\">".$phone2."</td></tr><tr><td colspan=\"2\" class=\"center\"></td></tr></table></div><div class=\"q3\"><form name=\"changecustomer\" action=\"account.php\" method=\"post\"><input type=\"hidden\" name=\"changecustomer\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"submit\" name=\"submit\" class=\"cancel-small\" value=\"Change Customer\" /></form></div></div>";
echo "\n<div id=\"invoicedate\"><div class=\"q1\"><form name=\"newdateform\" action=\"customerinteraction.php\" method=\"post\"><table class=\"righttable\"><tr><td colspan=\"2\" class=\"center\">Change Invoice Date</td></tr><tr><td colspan=\"2\" class=\"center\"><input type=\"date\" name=\"newdate\" value=\"".$displayinvoicedate2."\"></td></tr><tr><td colspan=\"2\" class=\"center\"><input type=\"hidden\" name=\"changedate\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"submit\" class=\"btn-style\" name=\"submit\" value=\"Update Invoice Date\"></td></tr></table></form></div><div class=\"q3\"><form name=\"changecustomer\" action=\"account.php\" method=\"post\"><input type=\"hidden\" name=\"changecustomer\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"submit\" name=\"submit\" class=\"btn-style\" value=\"Change Customer\" /></form></div></div>";

echo "\n<div id=\"vehicleinfo\"><div class=\"q1\"><form name=\"updatevehicle\" method=\"post\" action=\"newinvoiceinfo.php\"><table class=\"righttable\"><tr><td colspan=\"2\" class=\"left\">".$dvehicleinfo2."</td></tr><tr><td class=\"left\" colspan=\"2\">VIN:&nbsp;&nbsp;&nbsp;&nbsp;".$vin."</td></tr><tr><td class=\"left\">License:&nbsp;&nbsp;&nbsp;&nbsp;".$license."</td><td class=\"left\">State:   ".$vehiclestate."</td></tr><tr href=\"editvehicleinfo\" colslpan=\"3\"><td><input type=\"button\" name=\"editvehicle\" class=\"btn-style\" value=\"Edit Vehicle\" /></td></tr><tr><td colspan=\"3\" class=\"center\"></td></tr></table></form><table><tr><td><form name=\"changecustomer\" action=\"customerinteraction.php\" method=\"post\"><input type=\"hidden\" name=\"changecustomer\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"<?php echo $invoiceid;?>\"><input type=\"submit\" name=\"submit\" class=\"btn-style\" value=\"Edit Vehicle\" /></form></td><td><form name=\"addvehicle\" action=\"newvehicle.php\" method=\"post\"><input type=\"hidden\" name=\"newvehicle\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"submit\" name=\"submit\" class=\"btn-style\" value=\"Add Vehicle\" /></form></td></tr><tr><td colspan=\"2\"><div class=\"styled-select2 black rounded\">\n<form name=\"changevehicle\" id=\"form\" action=\"customerinteraction.php\" method=\"post\"><select name=\"vehiclechange\" onchange=\"form.submit()\">";
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
	$sobmodel = $row11['submodel'];
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
echo "\n<div id=\"editvehicleinfo\"><div class=\"q1\"><form name=\"changevehicle\" method=\"post\" action=\"customerinteraction.php\"><table class=\"righttable\"><tr><td class=\"left\">Vehicle Info:</td><td class=\"left\" colspan=\"2\"><input type=\"hidden\" name=\"editvehicle1\" value=\"1\"><input type=\"hidden\" name=\"vehicleid\" value=\"".$vehicleid."\"><input type=\"text\" name=\"vehiclename\" value=\"".$dvehicleinfo2nb."\"></td></tr><tr><td class=\"left\">VIN:</td><td class=\"left\" colspan=\"2\"><input type=\"text\" name=\"vehiclevin\" value=\"".$vin."\"></td></tr><tr><td>License:</td><td><input type=\"text\" name=\"license\" value=\"".$license."\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;State: ".$vehiclestate."</td></tr><tr href=\"vehicleinfo2\" colslpan=\"3\"><td>edit vehicle</td></tr><tr><td colspan=\"3\" class=\"center\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"newcustomerinvoice\" value=\"1\"><input type=\"hidden\" name=\"submit\" value=\"1\"><input type=\"image\" src=\"images/buttons/checkmark.png\" alt=\"addcomment\" name=\"submit\" width=\"25\"></td></tr></table></form></div><div class=\"q3\"><table><tr><td><a href=\"editvehicle.php\">Edit Vehicle</a></td></tr><tr><td></td></tr><tr><td><a href=\"newvehicle.php\">Add New Vehicle</a></td></tr></table></div></div>";

echo "\n<div id=\"add\"><div class=\"q1\"><form name=\"addcomment\" method=\"post\" action=\"customerinteraction.php\"><table class=\"righttable\"><tr><td colspan=\"2\" class=\"left\"><textarea class=\"wideinput\" name=\"newcomment\" placeholder=\"Add Comment\" id=\"invoicecomment\"></textarea><div id=\"auto\"></div></td></tr><tr><td class=\"left\">Unit Price: <input type=\"text\" class=\"narrowinput\" id=\"unitprice\" name=\"newprice\" placeholder=\"0.00\" autocomplete=\"off\"><input type=\"button\" id=\"multiply1\" data-quantity=\"1.5\" value=\"x 1.5\" class=\"smallbutton\"><input type=\"button\" id=\"multiply2\" data-quantity=\"2\" value=\"x 2\" class=\"smallbutton\"></td><td class=\"left\">QTY:<input type=\"number\" class=\"narrowinput\" name=\"newqty\" value=\"1\" step=\"1.00\" ></td></tr><tr><td colspan=\"2\" class=\"center\">FET: <input type=\"text\" class=\"narrowinput\" name=\"newfet\" placeholder=\"0.00\" autocomplete=\"off\"></td></tr><tr><td class=\"center\" colspan=\"2\"><input type=\"hidden\" name=\"newlinenumber\" value=\"$newlinenumber\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"newcommentform\" value=\"1\"><input type=\"hidden\" name=\"submit\" value=\"1\"><input type=\"image\" src=\"images/buttons/checkmark.png\" alt=\"addcomment\" name=\"submit\" width=\"25\"></td></tr></table></form></div></div>";
echo "\n<div id=\"note\"><div class=\"q1\"><form name=\"addnotes\" method=\"post\" action=\"customerinteraction.php\"><table class=\"righttable\"><tr><td colspan=\"2\" class=\"left\"><textarea class=\"wideinput\" rows=\"5\" name=\"notes\" id=\"noteitem\">".$note."</textarea></td></tr><tr><td class=\"center\" colspan=\"2\"><input type=\"hidden\" name=\"notesubmit\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"submit\" value=\"1\"><input type=\"image\" src=\"images/buttons/checkmark.png\" alt=\"addcomment\" name=\"submit\" width=\"25\"></td></tr></table></form></div></div>";
echo "\n<div id=\"total\"><div class=\"q1\"><table class=\"righttable\"><tr><td class=\"center\">Change Tax Class:</td><td><select name=\"newtaxclass\">";
$taxsql = "SELECT `description`,`id` FROM `tax_rate` WHERE `id` = :taxgroup";
$txsth = $pdocxn->prepare($taxsql);
$txsth->bindParam(':taxgroup',$taxgroup);
$txsth->execute();
while($taxrow1 = $txsth->fetch(PDO::FETCH_ASSOC))
{
$taxid = $row1['id'];
$taxname = $taxrow1['description'];
echo "<option value=\"".$taxid."\">".$taxname."</option>";
}
$taxsql = "SELECT `description`,`id` FROM `tax_rate` WHERE `formdisplay` = '1'";
$txsth = $pdocxn->prepare($taxsql);
$txsth->execute();
while($taxrow1 = $txsth->fetch(PDO::FETCH_ASSOC))
{
$taxid = $row1['id'];
$taxname = $taxrow1['description'];
echo "<option value=\"".$taxid."\">".$taxname."</option>";
}
echo "</select></td></tr><tr><td><input type=\"submit\" class=\"smallbutton\" value=\"Change Payment\"></td><td><input type=\"submit\" class=\"cancel-small\" value=\"Void Payment\"></td></tr></table></div>";
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
<div class="printdiv"><table><tr>
<th colspan="2" class="center"><center>Quick Add</center></th></tr><tr><td class="center">
<?php
echo "<form name=\"addtireform\" action=\"inventory.php\" method=\"post\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"submit\" class=\"smallbutton\" name=\"addtire\" value=\"Add Tires\"></form></td><td class=\"center\"><form name=\"addservicea\" action=\"services.php\" method=\"post\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"submit\" name=\"addservice\" class=\"smallbutton\" value=\"Add Service/Package\"></form></td></tr><tr>";
$tpi = '1';
$topsql = "SELECT `id`,`description` FROM `packages` WHERE `active` = '1' ORDER BY `sort` DESC LIMIT 0,12";
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
<form action="customerinteraction.php" method="post" name="additem">
<input type="hidden" name="additem" value="1">
<input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>">
<input type="hidden" name="submit" value="submit">
<input type="hidden" name="packageid" value="<?php echo $packageid; ?>">
<input type="submit" class="smallbutton" alt="print" value="<?php echo $displaytitle; ?>" name="submit"></form>
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
<script type="text/javascript">
$(window).scroll(function(){
    $("#right").css("top",Math.max(80,250-$(this).scrollTop()));
});
</script>
</body>
</html>
<?php
}
else {
//display html - no submit

	$accountid = $_GET['accountid'];
	$sql2 = "SELECT `firstname`,`lastname` FROM `accounts` WHERE `accountid` = :accountid";
	$sth2 = $pdocxn->prepare($sql2);
	$sth2->bindParam(':accountid',$accountid);
	$sth2->execute();
	while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
	{
	$fname = $row2['firstname'];
	$lname = $row2['lastname'];
	$fullname = $fname." ".$lname;
	}
if(isset($_GET['vehicleid']))
{
	$vehicleid = $_GET['vehicleid'];
}else{
	$vehicleid = '0';
}
if(isset($_POST['startdate']))
	{
		$startdate = $_POST['startdate'];
		$enddate = $_POST['enddate'];
		$enddate = date('Y-m-d', strtotime('+1 day', strtotime($enddate)));
	}
else {
	$startdate = date('Y-m-d', strtotime('-6 months', strtotime($currentday)));
	$enddate = date('Y-m-d', strtotime('+1 day', strtotime($currentday)));
}
$appointmentdate = date('Y-m-d', strtotime('-30 day', strtotime($currentday)));
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
<title><?php echo $fullname; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" >
<link rel="stylesheet" type="text/css" href="style/globalstyle.css" >
<link rel="stylesheet" type="text/css" href="style/accountstyle.css" >
<script type="text/javascript" src="scripts/jquery-latest.js"></script>
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
function close_window() {
    window.close();
}
</script>
</head>
<body>
<div id="ciheader">
<div class="headerright"><br /><a href="javascript:close_window();" class="no-decoration"><input type="button" class="save" value="<?php echo $fullname; ?>"></a></div>
<div class="headerleft"><br /><a href="javascript:close_window();" class="no-decoration"><input type="button" class="cancel" value="Cancel/Exit"></a></div>
<div class="headercenter"><a href="inventory.php?accountid=<?php echo $accountid; ?>&ci=1" onmouseover="popup('inventory')"><img src="images/icons/tire-white.png" height="40"></a><a href="schedule.php?r=<?php echo $r; ?>&accountid=<?php echo $accountid; ?>" onmouseover="popup('scheduler')"><img src="images/icons/schedule.png" height="40"></a><a href="customerinteraction-invoice.php?accountid=<?php echo $accountid; ?>" onmouseover="popup('transactions')"><img src="images/icons/phone.png" height="40"></a></div></div>
<div id="selecteduserfullwidth"><form name="current1" action="index.php" method="POST"><table id="floatleft" width="100%"><tr><td class="currentuser">Current User:</td><td class="currentitem"><div class="styled-select black rounded">
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
</select></div><input type="hidden" name="form" value="1"></td></form><form name="update" action="customerinteraction.php?accountid=<?php echo $accountid; ?>" method="POST"><td>Start Date: <input type="date" name="startdate" value="<?php echo $startdate; ?>"></td><td>End Date: <input type="date" name="enddate" value="<?php echo $enddate; ?>"></td><td><input type="hidden" name="submit" value="1"><input type="submit" class="smallbutton" value="Update Search"></td></form></tr></table></div>
        <div id="content">

        	<div id="left"><form name="inventory" action="inventory.php" method="post">
<table id="highlightTable" class="blueTable">
<thead>
<tr>
<th>Date</th>
<th>Type (ID)</th>
<th>Amount</th>
<th>Mileage</th>
</tr>
</thead>
<tbody>
<?php
$tri = '1';

if($vehicleid > '0')
{
$sql1 = 'SELECT `id`,`invoiceid`,`invoicedate`,`type`,`total`,`voiddate`,`mileagein`,`taxgroup` FROM `invoice` WHERE `vehicleid` = :vehicleid AND `invoicedate` > :startdate AND `invoicedate` < :enddate ORDER BY `invoicedate` DESC';
}else{
$sql1 = 'SELECT `id`,`invoiceid`,`invoicedate`,`type`,`total`,`voiddate`,`mileagein`,`taxgroup` FROM `invoice` WHERE `accountid` = :accountid AND `invoicedate` > :startdate AND `invoicedate` < :enddate ORDER BY `invoicedate` DESC';
}
$sth1 = $pdocxn->prepare($sql1);
if($vehicleid > '0')
{
$sth1->bindParam(':vehicleid',$vehicleid);
}
if($accountid > '0')
{
$sth1->bindParam(':accountid',$accountid);
}
$sth1->bindParam(':startdate',$startdate);
$sth1->bindParam(':enddate',$enddate);
$sth1->execute();
	while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$invtax = '0';
	$invsubtotal = '0';
	$invtotal = '0';
	$invoicenumber = $row1['invoiceid'];
	$mileage1 = $row1['mileagein'];
	$mileage = number_format($mileage1);
	$date = $row1['invoicedate'];
	$displaydate = date('n/d/Y', strtotime($date));
	$taxgroup = $row1['taxgroup'];
	$id = $row1['id'];
	$type = $row1['type'];
	$voiddate = $row1['voiddate'];
	if ($voiddate['column'] == NULL)
	{
		$voidedbrand ="";
	}
else
	{
		$voidedbrand ="*VOIDED*";
	}
	if($type == '1')
{
	$displayid = $invoicenumber;
}
else {
	$displayid = $id;
}
$sql2 = 'SELECT `name` FROM `invoice_type` WHERE `id` = :type';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':type',$type);
$sth2->execute();
	while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
	{
	$typename = $row2['name'];
	}

${"ip".$tri} = "<div id=\"$tri\"><div class=\"q1\"><table class=\"righttable\"><tr><td class=\"center\"><form name=\"invoicehistory\" action=\"printcustomerinteraction.php\" method=\"post\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$id."\"><input type=\"submit\" class=\"btn-style\" name=\"submit\" value=\"Print\"></form></td><td class=\"center\"><a href=\"schedule.php?r=".$r."&q=1&i=".$id."&accountid=".$accountid."\" class=\"no-decoration\"><input type=\"button\" class=\"btn-style\" name=\"submit\" value=\"Schedule\"></a></td><td class=\"center\"><a href=\"invoice.php?invoiceid=".$id."\" target=\"_BLANK\"><input type=\"button\" class=\"btn-style\" alt=\"edit transaction\" value=\"Edit ".$typename."\"></form></td></tr></table></div><div class=\"q3\"><table class=\"righttable\"><tr><th>qty</th><th>comment</th><th>unit cost</th><th>total cost</td></tr>\n";
$sql2 = 'SELECT `qty`,`comment`,`amount`,`totallineamount` FROM `line_items` WHERE `invoiceid` = :transactionid ORDER BY `linenumber` ASC';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':transactionid',$id);
$sth2->execute();
if ($sth2->rowCount() > 0)
{
while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
	{
	$qty = $row2['qty'];
	$amount = $row2['amount'];
	$comment = $row2['comment'];
	$totalamount = $row2['totallineamount'];
	$unitcost = $totalamount / $qty;
${"ip".$tri} .= "\n<tr><td class=\"left\">".$qty."</td><td class=\"left\"><b>".$comment."</b></td><td class=\"left\">".$unitcost."</td><td class=\"left\"><b>".$totalamount."</b></td></tr>\n";
$invsubtotal = $invsubtotal + $totalamount;
}
/*
$sql3 = "SELECT SUM(taxamount) as `invtax` FROM `tax_trans` WHERE `transid` = :transactionid";
$sth3 = $pdocxn->prepare($sql3);
$sth3->bindParam(':transactionid',$id);
$sth3->execute();
if ($sth3->rowCount() > 0)
{
while($row3 = $sth3->fetch(PDO::FETCH_ASSOC))
	{
$invtax = $row3['invtax'];
}
}
else{
	$invtax = '0';
}
*/
$gettax = $pdocxn->prepare('SELECT `id`,`multiply`,`description` FROM `tax_rate` WHERE `id` = :taxgroup');
$gettax->bindparam(':taxgroup',$taxgroup);
$gettax->execute();
while($gettaxraterow = $gettax->fetch(PDO::FETCH_ASSOC))
{
$taxrate = $gettaxraterow['multiply'];
$taxdescription = $gettaxraterow['description'];
}

$taxtotal = $invoicesubtotal*$taxrate;
$invoicetotal = $invoicesubtotal + $taxtotal;
$invoiceformtotal = round($invoicetotal,2);

$dtaxtotal = money_format('%(#0.2n',$taxtotal);
$dsubtotal = money_format('%(#0.2n',$invoicesubtotal);
$dinvoicetotal = money_format('%(#0.2n',$invoicetotal);


$invtotal = $invsubtotal + $invtax;
$displayinvtotal = money_format('%(#0.2n',$invtotal);
${"ip".$tri} .= "\n<tr><td colspan=\"3\" class=\"left\">Subtotal</td><td class=\"left\"><b>".$dsubtotal."</b></td></tr>\n";
${"ip".$tri} .= "\n<tr><td colspan=\"3\" class=\"left\">Tax</td><td class=\"left\"><b>".$dtaxtotal."</b></td></tr>\n";
${"ip".$tri} .= "\n<tr><td colspan=\"3\" class=\"left\"><b>Total</b></td><td class=\"left\"><b>$".$dinvoicetotal."</b></td></tr>\n";

${"ip".$tri} .= "</table></div></div>";
	echo "<tr href=\"".$tri."\"><td><b>".$displaydate."</b></td><td><b>".$voidedbrand." ".$typename."  (#".$displayid.")</b></td><td>$".$displayinvtotal."</td><td>".$mileage."</tr>\n";
$tri ++;
}
else {
${"ip".$tri} .= "</table></div></div>";
$tri ++;
}
}
echo "<tr><td colspan=\"4\" bgcolor=\"gray\"><center><b>Customer Interactions/Recommendations Below</b></center></td></tr>\n";
if($vehicleid > '0')
{
$sql1 = 'SELECT `id`,`invoicedate`,`type`,`total`,`voiddate`,`taxgroup`,`abvvehicle` FROM `customerinteractions` WHERE `vehicleid` = :vehicleid AND `invoicedate` > :startdate AND `invoicedate` < :enddate ORDER BY `invoicedate` DESC';
}else{
$sql1 = 'SELECT `id`,`invoicedate`,`type`,`total`,`voiddate`,`taxgroup`,`abvvehicle` FROM `customerinteractions` WHERE `accountid` = :accountid AND `invoicedate` > :startdate AND `invoicedate` < :enddate ORDER BY `invoicedate` DESC';
}
$sth1 = $pdocxn->prepare($sql1);
if($vehicleid > '0')
{
$sth1->bindParam(':vehicleid',$vehicleid);
}
if($accountid > '0')
{
$sth1->bindParam(':accountid',$accountid);
}
$sth1->bindParam(':startdate',$startdate);
$sth1->bindParam(':enddate',$enddate);
$sth1->execute();
	while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$invtax = '0';
	$invsubtotal = '0';
	$invtotal = '0';
	$date = $row1['invoicedate'];
	$displaydate = date('n/d/Y', strtotime($date));
	$taxgroup = $row1['taxgroup'];
	$id = $row1['id'];
	$type = $row1['type'];
	$voiddate = $row1['voiddate'];
	$abvvehicle = $row1['abvvehicle'];
	if ($voiddate['column'] == NULL)
	{
		$voidedbrand ="";
	}
else
	{
		$voidedbrand ="*VOIDED*";
	}
$sql2 = 'SELECT `name` FROM `invoice_type` WHERE `id` = :type';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':type',$type);
$sth2->execute();
	while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
	{
	$typename = $row2['name'];
	}

${"ip".$tri} = "<div id=\"$tri\"><div class=\"q1\"><table class=\"righttable\"><tr><td class=\"center\"><form name=\"invoicehistory\" action=\"printcustomerinteraction.php?accountid=".$accountid."\" method=\"post\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$id."\"><input type=\"submit\" class=\"btn-style\" name=\"submit\" value=\"Print\"></form></td><td class=\"center\"><a href=\"schedule.php?r=".$r."&q=1&i=".$invoiceid."&accountid=".$accountid."\" class=\"no-decoration\"><input type=\"button\" class=\"btn-style\" name=\"submit\" value=\"Schedule\"></a></td><td class=\"center\"><form name=\"invoicehistory\" action=\"customerinteraction-invoice.php?accountid=".$accountid."\" method=\"post\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$id."\"><input type=\"submit\" class=\"btn-style\" name=\"submit\" value=\"Edit ".$typename."\"></form></td></tr></table></div><div class=\"q3\"><table class=\"righttable\"><tr><th>qty</th><th>comment</th><th>unit cost</th><th>total cost</td></tr>\n";
$sql2 = 'SELECT `qty`,`comment`,`amount`,`totallineamount` FROM `ci_line_items` WHERE `invoiceid` = :transactionid ORDER BY `linenumber` ASC';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':transactionid',$id);
$sth2->execute();
if ($sth2->rowCount() > 0)
{
while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
	{
	$qty = $row2['qty'];
	$amount = $row2['amount'];
	$comment = $row2['comment'];
	$totalamount = $row2['totallineamount'];
	$unitcost = $totalamount / $qty;
${"ip".$tri} .= "\n<tr><td class=\"left\">".$qty."</td><td class=\"left\"><b>".$comment."</b></td><td class=\"left\">".$unitcost."</td><td class=\"left\"><b>".$totalamount."</b></td></tr>\n";
$invsubtotal = $invsubtotal + $totalamount;
}
$sql3 = "SELECT SUM(taxamount) as `invtax` FROM `tax_trans` WHERE `transid` = :transactionid";
$sth3 = $pdocxn->prepare($sql3);
$sth3->bindParam(':transactionid',$id);
$sth3->execute();
if ($sth3->rowCount() > 0)
{
while($row3 = $sth3->fetch(PDO::FETCH_ASSOC))
	{
$invtax = $row3['invtax'];
}
}
else{
	$invtax = '0';
}
$invtotal = $invsubtotal + $invtax;
$displayinvtotal = money_format('%(#0.2n',$invtotal);

${"ip".$tri} .= "</table></div></div>";
	echo "<tr href=\"$tri\"><td><b>$displaydate</b></td><td><b>$voidedbrand $typename </b></td><td>$abvvehicle</td><td>$mileage</td></tr>\n";
$tri ++;
}
else {
${"ip".$tri} .= "</table></div></div>";
$tri ++;
}}



















echo "<tr><td colspan=\"4\" bgcolor=\"gray\"><center><b>Upcoming Appointments Below</b></center></td></tr>\n";
if($vehicleid > '0')
{
$sql1 = 'SELECT `id`,`date`,`type`,`total`,`voiddate`,`abvvehicle`,`invoicedate`,`taxgroup` FROM `'.$scheduletable.'` WHERE `vehicleid` = :vehicleid AND `invoicedate` > :startdate ORDER BY `invoicedate` ASC';
}else{
$sql1 = 'SELECT `id`,`date`,`type`,`total`,`voiddate`,`abvvehicle`,`invoicedate`,`taxgroup` FROM `'.$scheduletable.'` WHERE `accountid` = :accountid AND `invoicedate` > :startdate ORDER BY `invoicedate` ASC';
}
$sth1 = $pdocxn->prepare($sql1);
if($vehicleid > '0')
{
$sth1->bindParam(':vehicleid',$vehicleid);
}
else
{
$sth1->bindParam(':accountid',$accountid);
}
$sth1->bindParam(':startdate',$appointmentdate);
$sth1->execute()or die(print_r($sth1->errorInfo(), true));
	while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$invtax = '0';
	$invsubtotal = '0';
	$invtotal = '0';
	$date = $row1['date'];
	$invoicedate = $row1['invoicedate'];
	$displaydate = date('D - n/d/Y g:i a', strtotime($date));
	$date1 = new DateTime();
	$date2 = new DateTime($date);
	if($date1 > $date2)
	{
		$prevapt1 = "<font color=\"red\">";
		$prevapt2 = "</font>";
	}else {
	$prevapt1 = '';
	$prevapt2 = '';
}
	$taxgroup = $row1['taxgroup'];
	$id = $row1['id'];
	$type = $row1['type'];
	$voiddate = $row1['voiddate'];
	$abvvehicle = $row1['abvvehicle'];
	if ($voiddate['column'] == NULL)
	{
		$voidedbrand ="";
	}
else
	{
		$voidedbrand ="*VOIDED*";
	}
${"ip".$tri} = "<div id=\"$tri\"><div class=\"q1\"><table class=\"righttable\"><tr><td class=\"center\"><form name=\"invoicehistory\" action=\"printcustomerinteraction.php?accountid=".$accountid."\" method=\"post\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$id."\"><input type=\"submit\" class=\"btn-style\" name=\"submit\" value=\"Print\"></form></td><td class=\"center\"><a href=\"schedule.php?r=".$r."&q=1&i=".$invoiceid."&accountid=".$accountid."\" class=\"no-decoration\"><input type=\"button\" class=\"btn-style\" name=\"submit\" value=\"Schedule\"></a></td><td class=\"center\"><a href=\"appointment.php?invoiceid=".$id."\" class=\"no-decoration\"><input type=\"button\" class=\"btn-style\" name=\"submit\" value=\"Edit Appointment\"></td></tr></table></div><div class=\"q3\"><table class=\"righttable\"><tr><th>qty</th><th>comment</th><th>unit cost</th><th>total cost</td></tr>\n";
$sql2 = 'SELECT `qty`,`comment`,`amount`,`totallineamount` FROM `'.$schedulelineitems.'` WHERE `invoiceid` = :transactionid ORDER BY `linenumber` ASC';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':transactionid',$id);
$sth2->execute();
if ($sth2->rowCount() > 0)
{
while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
	{
	$qty = $row2['qty'];
	$amount = $row2['amount'];
	$comment = $row2['comment'];
	$totalamount = $row2['totallineamount'];
	$unitcost = $totalamount / $qty;
${"ip".$tri} .= "\n<tr><td class=\"left\">".$qty."</td><td class=\"left\"><b>".$comment."</b></td><td class=\"left\">".$unitcost."</td><td class=\"left\"><b>".$totalamount."</b></td></tr>\n";
$invsubtotal = $invsubtotal + $totalamount;
}

$invtotal = $invsubtotal;
$displayinvtotal = money_format('%(#0.2n',$invtotal);

${"ip".$tri} .= "</table></div></div>";
	echo "<tr href=\"".$tri."\"><td>".$id."</td><td><b>".$prevapt1.$displaydate." ".$voidedbrand.$prevapt2."</b></td><td><b>".$abvvehicle."</b></td><td>".$displayinvtotal."</td></tr>\n";
$tri ++;
}
else {
${"ip".$tri} .= "</table></div></div>";
$tri ++;
}}



























?></tbody></table>
    </form></div><div class="right">
<?php
while ($tri > 0) {
        echo ${"ip".$tri};
		$tri --;
}
?>
</div></div>
<script type="text/javascript">
$("table tr").click(function(){
    $($('#' + $(this).attr("href"))).siblings().hide().end().fadeIn();
});
</script>
<script type="text/javascript">
$(window).scroll(function(){
    $(".right").css("top",Math.max(88,150-$(this).scrollTop()));
});
</script>
</body>
</html>
<?php
}
?>
