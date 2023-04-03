<?php
//submit form general
//submit insert new transaction
//copy a transaction
//delete line
//add service
//add inventory
//submit newcomment
//submit edit line item

//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Schedule-Hour';
$linkpage = 'hour.php';
$changecustomer = '0';

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
if(isset($_POST['sort']))
{
$sort = $_POST['sort'];
}
if(isset($_POST['invoicedateview']))
{
$displaydate = $_POST['invoicedateview'];
$displaydate2 = date("n/j/Y", strtotime($displaydate));
}else{
$displaydate = $currentday2;
$displaydate2 = date("n/j/Y", strtotime($displaydate));

}
if($_POST['submit'] OR $_POST['inventorysubmit'] OR $_POST['editsubmit'] OR $_POST['changevehicle'] OR $_POST['servicesubmit'])
{

//submit form general
if($_POST['changedate']&&$_POST['changedate']=='1')
{
$invoiceid = $_POST['invoiceid'];
$newdate = $_POST['newdate'];
$sth1 = $pdocxn->prepare('UPDATE `invoice` SET `invoicedate`=:newdate WHERE `id` = :invoiceid');
$sth1->bindParam(':newdate',$newdate);
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->execute();
}

if(isset($_POST['invoicenumber'])&&$_POST_['invoicenumber']>'1')
	{
	$invoicenumber = $_POST['invoicenumber'];
$getinv = $pdocxn->prepare('SELECT `id`,`type`,`taxgroup`,`invoicedate`,`userid`,`location` FROM `invoice` WHERE `invoiceid` = :invoicenumber AND `location` = :currentlocationid');
$getinv->bindParam(':currentlocationid',$currentlocationid);
$getinv->bindparam(':invoicenumber',$invoicenumber);
$getinv->execute();
while($getinvrow = $getinv->fetch(PDO::FETCH_ASSOC))
{
$invoiceid = $getinvrow['id'];
$invtype = $getinvrow['type'];
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
	else if(isset($_POST['invoiceid']))
	{
	$invoiceid = $_POST['invoiceid'];
$getinv = $pdocxn->prepare('SELECT `id`,`invoiceid`,`type`,`taxgroup`,`invoicedate` FROM `invoice` WHERE `id` = :invoiceid AND `location` = :currentlocationid');
$getinv->bindParam(':currentlocationid',$currentlocationid);
$getinv->bindparam(':invoiceid',$invoiceid);
$getinv->execute();
while($getinvrow = $getinv->fetch(PDO::FETCH_ASSOC))
{
$invoicenumber = $getinvrow['invoiceid'];
$invtype = $getinvrow['type'];
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
else {
	$invoiceid = '0';
}
if($_POST['void']&&$_POST['void']=='1')
{
$invoiceid = $_POST['invoiceid'];

$sth4 = $pdocxn->prepare('SELECT `qty`,`partid` FROM `line_items` WHERE `invoiceid` = :inv');
$sth4->bindParam(':inv',$invoiceid);
$sth4->execute();
while($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
{
$qty = $row4['qty'];
$partid = $row4['partid'];
if($partid > '0')
{
$locationcolumn = "loc".$inventorylocationid."_onhand";
$sth1 = $pdocxn->prepare('UPDATE `inventory` SET `'.$locationid.'`=`'.$locationid.'` + :qty WHERE `id` = :partid');
$sth1->bindParam(':qty',$qty);
$sth1->bindParam(':partid',$partid);
$sth1->execute();
}
}

$sth1 = $pdocxn->prepare('UPDATE `invoice` SET `voiddate`=:voiddate WHERE `id` = :invoiceid');
$sth1->bindParam(':voiddate',$currentday);
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->execute();
header('location:invoice.php');
}
$gettax = $pdocxn->prepare('SELECT `id`,`multiply`,`description` FROM `tax_rate` WHERE `id` = :taxgroup');
$gettax->bindparam(':taxgroup',$taxgroup);
$gettax->execute();
while($gettaxraterow = $gettax->fetch(PDO::FETCH_ASSOC))
{
$taxrate = $gettaxraterow['multiply'];
$taxdescription = $gettaxraterow['description'];
}
	if($_POST['new']&&$_POST['new'] == '1')
{
//submit insert new transaction
$type = $_POST['type'];
$sth1 = $pdocxn->prepare('SELECT `id` FROM `invoice` ORDER BY `id` DESC LIMIT 1');
$sth1->execute();
$row1 = $sth1->fetch(PDO::FETCH_ASSOC);
$lastinvid = $row1['id'];
$invoiceid = $lastinvid + '1';

$sth1 = $pdocxn->prepare('SELECT `invoiceid` FROM `invoice` WHERE `invoiceid` > \'1\' AND `location` = :currentlocationid ORDER BY `invoiceid` DESC LIMIT 1');
$sth1->bindParam(':currentlocationid',$currentlocationid);
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
$lastinvnumber = $row1['invoiceid'];
$invoicenumber = $lastinvnumber + '1';
}
if($type=='1')
{
$sth2 = $pdocxn->prepare('INSERT INTO `invoice`(`id`,`invoiceid`,`userid`,`type`,`location`,`creationdate`,`invoicedate`,`taxgroup`) VALUES (:id,:invoicenumber,:userid,:type,:location,:creationdate,:invoicedate,:taxgroup)');
$sth2->bindParam(':id',$invoiceid);
$sth2->bindParam(':invoicenumber',$invoicenumber);
$sth2->bindParam(':userid',$currentid);
$sth2->bindParam(':type',$type);
$sth2->bindParam(':location',$currentlocationid);
$sth2->bindParam(':creationdate',$currentdate);
$sth2->bindParam(':invoicedate',$currentday);
$sth2->bindParam(':taxgroup',$taxgroup);
$sth2->execute();
}else{
	$sth2 = $pdocxn->prepare('INSERT INTO `invoice`(`id`,`userid`,`type`,`location`,`creationdate`,`invoicedate`,`taxgroup`) VALUES (:id,:userid,:type,:location,:creationdate,:invoicedate,:taxgroup)');
$sth2->bindParam(':id',$invoiceid);
$sth2->bindParam(':userid',$currentid);
$sth2->bindParam(':type',$type);
$sth2->bindParam(':location',$currentlocationid);
$sth2->bindParam(':creationdate',$currentdate);
$sth2->bindParam(':invoicedate',$currentday);
$sth2->bindParam(':taxgroup',$taxgroup);
$sth2->execute();
}
}

	if($_POST['copy']&&$_POST['copy'] == '1')
{
//$copytype = $_POST['copytype'];
$copytype = '1';
//copy a transaction
$sth1 = $pdocxn->prepare('SELECT `id`,`location`,`subtotal`,`tax`,`total`,`taxgroup` FROM `invoice` WHERE `id` = :invoiceid LIMIT 1');
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
$sth1 = $pdocxn->prepare('SELECT `id` FROM `invoice` ORDER BY `id` DESC LIMIT 1');
$sth1->execute();
$row1 = $sth1->fetch(PDO::FETCH_ASSOC);
$lastinvid = $row1['id'];
$invoiceid = $lastinvid + '1';

$sth1 = $pdocxn->prepare('SELECT `invoiceid` FROM `invoice` WHERE `invoiceid` > \'1\' AND `location` = :currentlocationid ORDER BY `invoiceid` DESC LIMIT 1');
$sth1->bindParam(':currentlocationid',$currentlocationid);
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
$lastinvnumber = $row1['invoiceid'];
$invoicenumber = $lastinvnumber + '1';
}

$sql2 = "INSERT INTO `invoice`(`id`,`invoiceid`,`type`,`location`,`creationdate`,`invoicedate`,`subtotal`,`tax`,`total`,`taxgroup`) VALUES (:id,:invoicenumber,:type,:location,:creationdate,:invoicedate,:subtotal,:tax,:total,:taxgroup)";
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

$sth2 = $pdocxn->prepare('SELECT `linenumber`,`qty`,`amount`,`partid`,`packageid` `serviceid`,`comment`,`fet`,`totallineamount`,`lineitem_typeid`,`lineitem_subtypeid`,`lineitem_saletype`,`hours`,`basecost` FROM `line_items` WHERE `invoiceid` = :invoiceid');
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
$comment = $copyrow['comment'];
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


$copysql = "INSERT INTO `line_items`(`invoiceid`,`linenumber`,`qty`,`amount`,`partid`,`packageid`,`serviceid`,`comment`,`fet`,`totallineamount`,`lineitem_typeid`,`lineitem_subtypeid`,`lineitem_saletype`,`hours`,`basecost`) VALUES (:invoiceid,:linenumber,:qty,:amount,:partid,:packageid,:serviceid,:comment,:fet,:totallineamount,:lineitem_typeid,:lineitem_subtypeid,:lineitem_saletype,:hours,:basecost)";
$copysql2 = "INSERT INTO `line_items`(`invoiceid`,`linenumber`,`qty`,`amount`,`partid`,`packageid`,`serviceid`,`comment`,`fet`,`totallineamount`,`lineitem_typeid`,`lineitem_subtypeid`,`lineitem_saletype`,`hours`,`basecost`) VALUES ('".$invoiceid."','".$linenumber."','".$qty."','".$amount."','".$partid."','".$packageid."','".$serviceid."','".$comment."','".$fet."','".$totallineamount."','".$lineitem_typeid."','".$lineitem_subtypeid."','".$lineitem_saletype."','".$hours."','".$basecost."')";
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
}
}
//delete line
if($_POST['delete']&&$_POST['delete']=='1')
{
$lineid = $_POST['lineid'];
$invoiceid = $_POST['invoiceid'];
$sth1 = $pdocxn->prepare('DELETE FROM `line_items` WHERE `id` = :lineid');
$sth1->bindParam(':lineid',$lineid);
$sth1->execute();
if($articleid = '0' && $invtype = '1')
{
$locationcolumn = "loc".$currentlocationid."_onhand";
$sth1 = $pdocxn->prepare('UPDATE `inventory` SET `'.$locationid.'`=`'.$locationid.'` + :qty WHERE `id` = :articleid');
$sth1->bindParam(':qty',$qty);
$sth1->bindParam(':articleid',$articleid);
$sth1->execute();
}
}
if($_POST['changecustomer']&&$_POST['changecustomer'] == '1')
{
$newaccountid = $_POST['accountid'];
if(isset($_POST['vehicleid']))
{
$newvehicleid = $_POST['vehicleid'];
}
else{
$newvehicleid = '0';
}
$getname = $pdocxn->prepare('SELECT `firstname`,`lastname`,`fullname`,`taxclass` from `accounts` WHERE `acctid` = :accountid');
$getname->bindParam(':accountid',$newaccountid);
$getname->execute();
while($getnamerow = $getname->fetch(PDO::FETCH_ASSOC))
{
$fullname = $getnamerow['fullname'];
$firstname = $getnamerow['firstname'];
$lastname = $getnamerow['lastname'];
$taxclass = $getnamerow['taxclass'];
if($firstname > '0')
{
$abvname = $firstname." ".$lastname;
}
}
if($newvehicleid > '0')
{
$getvehicle = $pdocxn->prepare('SELECT `year`,`make`,`model`,`description`,`cfdescription` from `vehicles` WHERE `id` = :vehicleid');
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
$abvvehicle = $getvehiclerow['cfdescription'];
}}

$sth1 = $pdocxn->prepare('UPDATE `invoice` SET `accountid`=:accountid,`vehicleid`=:vehicleid,`abvname`=:abvname,`abvvehicle`=:abvvehicle,`taxgroup`=:taxgroup WHERE `id` = :invoiceid');
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
$vehiclechange=$_POST['vehiclechange'];
$getvehicle = $pdocxn->prepare('SELECT `year`,`make`,`model`,`cfdescription` from `vehicles` WHERE `id` = :vehicleid');
$getvehicle->bindParam(':vehicleid',$vehiclechange);
$getvehicle->execute();
while($getvehiclerow = $getvehicle->fetch(PDO::FETCH_ASSOC))
{
$year = $getvehiclerow['year'];
$make = $getvehiclerow['make'];
$description = $getvehiclerow['cfdescription'];
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
$sth1 = $pdocxn->prepare('UPDATE `invoice` SET `vehicleid`=:vehicleid,`abvvehicle`=:abvvehicle WHERE `id` = :invoiceid');
$sth1->bindParam(':vehicleid',$vehiclechange);
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->bindParam(':abvvehicle',$abvvehicle);
$sth1->execute();
}
if($_POST['linemove'] &&$_POST['linemove'] == '1')
{
$currentline = $_POST['currentline'];
$previousline = $currentline - '1';
$nextline = $currentline + '1';
$lineid = $_POST['lineid'];
if($_POST['up'] &&$_POST['up'] == '1')
{
$sth1 = $pdocxn->prepare('UPDATE `line_items` SET `linenumber`=:currentline WHERE `invoiceid` = :invoiceid AND `linenumber` = :previousline');
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->bindParam(':currentline',$currentline);
$sth1->bindParam(':previousline',$previousline);
$sth1->execute();
$sth2 = $pdocxn->prepare('UPDATE `line_items` SET `linenumber`=:previousline WHERE `id` = :lineid');
$sth2->bindParam(':lineid',$lineid);
$sth2->bindParam(':previousline',$previousline);
$sth2->execute();
}
if($_POST['down']&&$_POST['down'] == '1')
{
$sth1 = $pdocxn->prepare('UPDATE `line_items` SET `linenumber`=:currentline WHERE `invoiceid` = :invoiceid AND `linenumber` = :nextline');
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->bindParam(':currentline',$currentline);
$sth1->bindParam(':nextline',$nextline);
$sth1->execute();
$sth2 = $pdocxn->prepare('UPDATE `line_items` SET `linenumber`=:nextline WHERE `id` = :lineid');
$sth2->bindParam(':lineid',$lineid);
$sth2->bindParam(':nextline',$nextline);
$sth2->execute();
}
}
//add service
if($_POST['servicesubmit']&&$_POST['servicesubmit'] == '1')
{
$serviceid  = $_POST['serviceid'];
$cost  = $_POST['cost'];
$servicetitle = $_POST['servicetitle'];
$servicenote = $_POST['servicenote'];
$invoiceid = $_POST['invoiceid'];

$sth3 = $pdocxn->prepare('INSERT INTO `line_items`(`invoiceid`,`amount`,`linenumber`,`comment`,`serviceid`,`totallineamount`,`qty`)VALUES(:invoiceid,:amount,:linenumber,:comment,:serviceid,:totallineamount,\'1\')');
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
$sth3 = $pdocxn->prepare('INSERT INTO `line_items`(`invoiceid`,`amount`,`linenumber`,`comment`,`serviceid`,`totallineamount`)VALUES(:invoiceid,\'0\',:linenumber,:comment,:serviceid,\'0\')');
$sth3->bindParam(':invoiceid',$invoiceid);
$sth3->bindParam(':serviceid',$serviceid);
$sth3->bindParam(':comment',$servicenote);
$sth3->bindParam(':linenumber',$newlinenumber);
$sth3->execute();
$newlinenumber ++;
//addlinenumber
}
}
//add inventory
if($_POST['inventorysubmit']&&$_POST['inventorysubmit'] == '1')
{
$articleid = $_POST['articleid'];
$qty = $_POST['qty'];
$type = $_POST['type'];
$description = $_POST['desc'];
$price = $_POST['price'];
$fet = $_POST['fet'];
if($fet < '.001')
{
	$fet = '0';
}
if($invtype = '1')
{
$locationcolumn = "loc".$currentlocationid."_onhand";
$sth1 = $pdocxn->prepare('UPDATE `inventory` SET `'.$locationid.'`=`'.$locationid.'`-:qty WHERE `id` = :articleid');
$sth1->bindParam(':qty',$qty);
$sth1->bindParam(':articleid',$articleid);
$sth1->execute();
}
if($invoiceid < '1')
{
$sth1 = $pdocxn->prepare('SELECT `id` FROM `invoice` ORDER BY `id` DESC LIMIT 1');
$sth1->execute();
$row1 = $sth1->fetch(PDO::FETCH_ASSOC);
$lastinvid = $row1['id'];
$invoiceid = $lastinvid + '1';
$sth2 = $pdocxn->prepare('INSERT INTO `invoice`(`id`,`userid`,`type`,`location`,`creationdate`) VALUES (:id,:userid,:type,:location,:creationdate)');
$sth2->bindParam(':id',$invoiceid);
$sth2->bindParam(':userid',$currentid);
$sth2->bindParam(':type',$type);
$sth2->bindParam(':location',$currentlocationid);
$sth2->bindParam(':creationdate',$currentdate);
$sth2->execute();
$newlinenumber = '1';
}
$totallineamount = $qty * $price;
$sth3 = $pdocxn->prepare('INSERT INTO `line_items`(`invoiceid`,`qty`,`amount`,`linenumber`,`comment`,`fet`,`totallineamount`)VALUES(:invoiceid,:qty,:amount,:linenumber,:comment,:fet,:totallineamount)');
$sth3->bindParam(':totallineamount',$totallineamount);
$sth3->bindParam(':invoiceid',$invoiceid);
$sth3->bindParam(':qty',$qty);
$sth3->bindParam(':amount',$price);
$sth3->bindParam(':comment',$description);
$sth3->bindParam(':fet',$fet);
$sth3->bindParam(':linenumber',$newlinenumber);
$sth3->execute();
$newlinenumber ++;
//addlinenumber
}
if($_POST['packageid']&&$_POST['packageid'] > '0')
{
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
$linesth2 = $pdocxn->prepare('INSERT INTO `line_items`(`invoiceid`,`qty`,`amount`,`linenumber`,`comment`,`totallineamount`)VALUES(:invoiceid,:qty,:amount,:linenumber,:comment,:totallineamount)');
$linesth2->bindParam(':invoiceid',$invoiceid);
$linesth2->bindParam(':qty',$pkgqty);
$linesth2->bindParam(':totallineamount',$totallineamount);
$linesth2->bindParam(':amount',$lr1cost);
$linesth2->bindParam(':comment',$packagetitle);
$linesth2->bindParam(':linenumber',$newlinenumber);
$linesth2->execute();
$newlinenumber ++;
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
$sth1 = $pdocxn->prepare('INSERT INTO `line_items`(`invoiceid`,`qty`,`amount`,`linenumber`,`comment`,`fet`,`totallineamount`)VALUES(:invoiceid,:qty,:amount,:linenumber,:comment,:fet,:totallineamount)');
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->bindParam(':totallineamount',$totallineamount);
$sth1->bindParam(':qty',$qty);
$sth1->bindParam(':amount',$amount);
$sth1->bindParam(':linenumber',$newlinenumber);
$sth1->bindParam(':comment',$comment);
$sth1->bindParam(':fet',$fet);
$sth1->execute();
$newlinenumber ++;
//addlinenumber
}
if($_POST['editsubmit']&&$_POST['editsubmit'] == '1')
{
//submit edit line item
$invoiceid = $_POST['invoiceid'];
$invoicenumber = $_POST['invoicenumber'];
$lineid = $_POST['lineid'];
$qty = $_POST['qty'];
$amount = $_POST['price'];
$lineid = $_POST['lineid'];
$comment = $_POST['comment'];
$fet = $_POST['fet'];
$totallineamount = $qty*$amount;
$sth1 = $pdocxn->prepare('UPDATE `line_items` SET `qty`=:qty,`amount`=:amount,`comment`=:comment,`totallineamount`=:totallineamount WHERE `id` = :lineid');
$sth1->bindParam(':totallineamount',$totallineamount);
$sth1->bindParam(':qty',$qty);
$sth1->bindParam(':amount',$amount);
$sth1->bindParam(':lineid',$lineid);
$sth1->bindParam(':comment',$comment);
$sth1->execute();
}

$sth3 = $pdocxn->prepare('SELECT * FROM invoice WHERE id = :inv');
$sth3->bindParam(':inv',$invoiceid);
$sth3->execute();
$row3 = $sth3->fetch(PDO::FETCH_ASSOC);
$accountid = $row3['accountid'];
$type = $row3['type'];
$userid = $row3['userid'];
$vehicleid = $row3['vehicleid'];
$mileagein = $row3['mileagein'];
$mileageout = $row3['mileageout'];
$location = $row3['location'];
if($accountid > '0')
{
$sth4 = $pdocxn->prepare('SELECT * FROM accounts WHERE acctid = :acct');
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
$fullname = $fname." ".$lname;
$dcustomerinfo1 = "<font color=\"blue\">".$fullname."<br />".$phone1."</font>";
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
$dlastactivedate = "12/20/2017";
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
	$description = $row5['cfdescription'];
if($year > '1')
{
$dvehicleinfo = "<b>".$year." ".$make." ".$model." ".$submodel." ".$engine."</b></td><td href=\"vehicleinfo\">VIN: ".$vin."</td><td href=\"vehicleinfo\">License: ".$license." (".$vehiclestate.")";
$dvehicleinfo1 = "<font color=\"red\"><b>".$year." ".$make." ".$model." ".$submodel." ".$engine."</b><br />Mileage: <input type=\"textbox\" name=\"mileage1\" value=\"".$mileagein."\"></font>";
$dvehicleinfo1a = "<font color=\"red\">VIN: ".$vin." License: ".$license." (".$vehiclestate.")</font>";
$dvehicleinfo2 = "<b>".$year." ".$make." ".$model." ".$submodel." ".$engine."</b>";
$dvehicleinfo2nb = $year." ".$make." ".$model." ".$submodel." ".$engine;
$selectvehicleinfo = "".$year." ".$make." ".$model." ".$submodel." ".$engine."   VIN: ".$vin."  License: ".$license." ";
}
else
{
$dvehicleinfo = "<b>".$description."</b></td><td href=\"vehicleinfo\">VIN: ".$vin."</td><td href=\"vehicleinfo\">License: ".$license." (".$vehiclestate.")";
$dvehicleinfo1 = "<font color=\"red\"><b>".$description."</b><br />Mileage: <input type=\"textbox\" name=\"mileage1\" value=\"".$mileagein."\"></font>";
$dvehicleinfo1a = "<font color=\"red\">VIN: ".$vin." License: ".$license." (".$vehiclestate.")</font>";
$dvehicleinfo2 = "<b>".$description."</b>";
$dvehicleinfo2nb = $description;
$selectvehicleinfo = "".$description."   VIN: ".$vin."   License: ".$license."";
}

${"ip".$tri} = "<div id=\"$tri\"><div class=\"q1\"><table class=\"righttable\"><tr><td colspan=\"2\" class=\"center\"><a href=\"accounthistory.php?acctid=".$acctid."\">Account History</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"editaccounts.php?acctid=".$acctid."\">Edit Info</a></td></tr><tr><td colspan=\"2\" class=\"center\">$fullname</td></tr><tr><td colspan=\"2\" class=\"left\">$address&nbsp;$city,&nbsp;$state&nbsp;$zip</td></tr><tr><td colspan=\"2\">$address2</td></tr>".$dphone1."".$dphone2."".$dphone3."".$dphone4."".$dfax."<tr></table></div><div class=\"q3\"><table class=\"righttable\"><tr><th>Vehicle</th><th>License</th><th>VIN</th></tr><tr><td class=\"left\">$year&nbsp;&nbsp;$make&nbsp;&nbsp;$model</td><td></td><td>$vin</td></tr></table></div></div>";
$tri ++;
}}}
else{
$dvehicleinfo = "<input type=\"button\" class=\"btn-style\" value=\"Select Vehicle\"></td>";
}
}
else {
${"ip".$tri} = "<div id=\"$tri\"><div class=\"q1\"><table class=\"righttable\"><tr><td colspan=\"2\" class=\"center\"><a href=\"accounthistory.php?acctid=".$acctid."\">Account History</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"editaccounts.php?acctid=".$acctid."\">Edit Info</a></td></tr><tr><td colspan=\"2\" class=\"center\">$fullname</td></tr><tr><td colspan=\"2\" class=\"left\">$address&nbsp;$city,&nbsp;$state&nbsp;$zip</td></tr><tr><td colspan=\"2\">$address2</td></tr>".$dphone1."".$dphone2."".$dphone3."".$dphone4."".$dfax."<tr></table></div><div class=\"q3\"><table class=\"righttable\"><tr><th>Vehicle</th><th>License</th><th>VIN</th></tr><tr><td class=\"left\">$year&nbsp;&nbsp;$make&nbsp;&nbsp;$model</td><td></td><td>$vin</td></tr></table></div></div>";
}
$sql6 = 'SELECT `id`,`name` FROM `invoice_type` WHERE `id` = :type';
$sth6 = $pdocxn->prepare($sql6);
$sth6->bindParam(':type',$invtype);
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
$sth7 = $pdocxn->prepare($sql7);
$sth7->bindParam(':note',$note);
$sth7->bindParam(':invoiceid',$invoiceid);
$sth7->execute();
}else{

$sql7 = 'INSERT INTO `notes`(`note`,`invoiceid`,`accountid`,`vehicleid`,`notetype`)VALUES(:note,:invoiceid,:accountid,:vehicleid,:notetype)';
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
	$note = $row7['note'];
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
</head>
<body>
<div id="header"><table><tr><td>
<form action="invoice.php" method="post" name="voidinvoice">
<input type="hidden" name="void" value="1">
<input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>">
<input type="hidden" name="submit" value="1">
<input type="submit" class="cancel" value="Void Invoice"></form>
</td>
<td>
<form action="invoice.php" method="post" name="voidinvoice">
<input type="submit" class="save" value="Save"></form></td>

<td><form action="printinvoice.php" method="post" name="printinvoice">
<input type="hidden" name="copies" value="2">
<input type="hidden" name="print" value="1">
<input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>">
<input type="hidden" name="submit" value="1">
<input type="submit" class="save" alt="print" value="Print Invoice" name="submit"></form></td><td><table cellspacing="0"><tr><td><form action="printinvoice.php" method="post" name="printinvoice">
<input type="hidden" name="copies" value="1">
<input type="hidden" name="print" value="1">
<input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>">
<input type="hidden" name="submit" value="1">
<input type="submit" class="smallbutton" alt="print" value="1" name="submit"></form></td></tr><tr><td><form action="printinvoice.php" method="post" name="printinvoice">
<input type="hidden" name="copies" value="3">
<input type="hidden" name="print" value="1">
<input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>">
<input type="hidden" name="submit" value="1">
<input type="submit" class="smallbutton" alt="print" value="3" name="submit"></form></td></tr></table><td>

<form action="emailinvoice.php" method="post" name="emailinvoice">
<input type="hidden" name="email" value="1">
<input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>">
<input type="hidden" name="submit" value="1">
<input type="button" class="save" value="Email Invoice"></form></td><td>
<?php
if($type == '1')
{
?>
<form action="payments.php" method="post" name="payment">
<input type="hidden" name="invoiceform" value="1">
<input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>">
<input type="hidden" name="accountid" value="<?php echo $accountid; ?>">
<input type="hidden" name="vehicleid" value="<?php echo $vehicleid; ?>">
<input type="hidden" name="invoiceamount" value="<?php echo $invoiceformtotal; ?>">
<input type="hidden" name="submit" value="1"><input type="button" class="save" value="Enter Payment">
<?php
}else{
?>
<input type="button" class="save" value="Change to Invoice">
<?php
}
?></form></td><td>
<form action="invoice.php" method="post" name="copyinvoice">
<input type="hidden" name="copy" value="1">
<input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>">
<input type="hidden" name="accountid" value="<?php echo $accountid; ?>">
<input type="hidden" name="vehicleid" value="<?php echo $vehicleid; ?>">
<input type="hidden" name="submit" value="1">
<input type="submit" class="save" alt="print" value="Copy Invoice" name="submit"></form></td></tr></table></div>
<div id="selecteduser"><form name="current1" action="invoice.php" method="POST"><table id="floatleft"><tr><td class="currentuser">Invoice Salesman:</td><td class="currentitem"><div class="styled-select black rounded"><input type="hidden" name="userform" value="1">
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
</div></td><td class="currentstore">Invoice Store:</td><td class="currentitem"><div class="styled-select black rounded"><select name="location" onchange="form.submit()"><?php
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
</select></div><input type="hidden" name="form" value="1"></td></tr></table></form>
</td><td><?php echo $typename; ?> <b>#<?php echo $invoicenumber; ?></b></td><td href="invoicedate"><br />Date: <?php echo $displayinvoicedate; ?></td></tr></table></div>
<div id="content">
<div id="left">
<?php
if($accountid > '0')
{
?>
<table>
	<tr><td href="customerinfo" class="tdleft" width="50%"><?php echo $dcustomerinfo1; ?></td><td href="vehicleinfo" class="tdright" width="50%"><?php echo $dvehicleinfo1; ?></td></tr>
	<tr><td href="customerinfo" class="tdleft"><?php echo $dcustomerinfo2; ?></td><td href="vehicleinfo" class="tdright"><?php echo $dvehicleinfo1a; ?></td></tr>
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
$sth4 = $pdocxn->prepare('SELECT * FROM `line_items` WHERE `invoiceid` = :inv ORDER BY `linenumber` ASC');
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
$invcomment = $row4['comment'];
$fet = $row4['fet'];
if($fet == '0')
{
	$fet = '';
}
$extprice = $row4['totallineamount'];
$unitamount = $extprice/$invqty;
setlocale(LC_MONETARY,"en_US");
$invamount = money_format('%(#0.2n',$unitamount);
$unitprice = round($unitamount,2);
$dextprice = money_format('%(#0.2n',$extprice);
$linenumber = $row4['linenumber'];
$invoicesubtotal = $invoicesubtotal+$extprice;
if($linenumber == '1')
{
	$displayup = "<td>&nbsp;</td>";
}
else {
	$displayup = "<td><form action=\"invoice.php\" method=\"post\" name=\"linemoveform\"><input type=\"hidden\" name=\"currentline\" value=\"".$linenumber."\"><input type=\"hidden\" name=\"up\" value=\"1\"><input type=\"hidden\" name=\"lineid\" value=\"".$lineid."\"><input type=\"hidden\" name=\"linemove\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"submit\" value=\"submit\"><input type=\"image\" src=\"images/icons/up.png\" alt=\"up\" name=\"submit\" width=\"20\"></form></td>";
}
if($linenumber == $linecount)
{
	$displaydown = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
}
else {
	$displaydown = "<form action=\"invoice.php\" method=\"post\" name=\"linvemoveform\"><input type=\"hidden\" name=\"currentline\" value=\"".$linenumber."\"><input type=\"hidden\" name=\"down\" value=\"1\"><input type=\"hidden\" name=\"lineid\" value=\"".$lineid."\"><input type=\"hidden\" name=\"linemove\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"submit\" value=\"submit\"><input type=\"image\" src=\"images/icons/down.png\" alt=\"down\" name=\"submit\" width=\"20\"></form>";
}
$prevlineid = $row4['id'];
	echo "\n<tr href=\"$tri\" id=\"".$qri."row\"><td>$invqty</td><td>$invcomment</td><td>$fet</td><td>$invamount</td><td>$dextprice</td></tr>";
${"ip".$tri} = "\n<div id=\"$tri\"><div class=\"q1\"><form name=\"updatecomment\" method=\"post\" action=\"invoice.php\"><table class=\"righttable\"><tr>";
if($lineitemtype == '1')
{
${"ip".$tri} .= "<td colspan=\"3\" class=\"left\">".$invcomment."</td>";	
}else{
${"ip".$tri} .= "<td colspan=\"3\" class=\"left\"><textarea class=\"wideinput\" name=\"comment\" autocomplete=\"off\" id=\"".$qri."box\">".$invcomment."</textarea></td>";
}
${"ip".$tri} .= "</tr><tr><td class=\"left\">Unit Price: <input type=\"number\" class=\"narrowinput\" name=\"price\" value=\"$unitprice\" autocomplete=\"off\"></td><td class=\"left\">QTY:<input type=\"number\" class=\"narrowinput\" name=\"qty\" value=\"".$invqty."\" step=\"1.00\" autocomplete=\"off\"></td><td class=\"center\">FET: <input type=\"text\" class=\"narrowinput\" name=\"newfet\" value=\"$fet\" autocomplete=\"off\"></td></tr><tr><td class=\"center\" colspan=\"2\"><input type=\"hidden\" name=\"editsubmit\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"lineid\" value=\"".$lineid."\"><input type=\"image\" src=\"images/buttons/checkmark.png\" width=\"25\" name=\"submit\"></form></td><td><form action=\"invoice.php\" method=\"post\" name=\"deletelineitem\"><input type=\"hidden\" name=\"delete\" value=\"1\"><input type=\"hidden\" name=\"lineid\" value=\"".$lineid."\"><input type=\"hidden\" name=\"partid\" value=\"$invpartid\"><input type=\"hidden\" name=\"qty\" value=\"".$invqty."\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"submit\" value=\"submit\"><input type=\"submit\" class=\"cancel-small\" alt=\"down\" value=\"Delete Item\"></form></td></tr></table></form><table width=\"10%\"><tr>".$displayup."<td></td></tr><tr><td>".$displaydown."</td></tr></table></div></div>\n";
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
$sql10 = "SELECT `id`,`invoicedate` FROM `invoice` WHERE `id` = :paymentid";
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
$sql11 = "SELECT `id`,`totallineamount`,`lineitem_typeid`,`comment` FROM `line_items` WHERE `invoiceid` = :paymentid";
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
?></tbody></table></form></div>
<div class="right">
<?php
$sth1 = $pdocxn->prepare('UPDATE `invoice` SET `subtotal`=:subtotal,`tax`=:tax,`total`=:total WHERE `id` = :invoiceid');
$sth1->bindParam(':subtotal',$invoicesubtotal);
$sth1->bindParam(':tax',$taxtotal);
$sth1->bindParam(':total',$invoicetotal);
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->execute();

while ($tri > 0) {
        echo ${"ip".$tri};
		$tri --;
}
echo "\n<div id=\"customerinfo\"><div class=\"q1\"><table class=\"righttable\"><tr><td colspan=\"2\" class=\"left\">".$fullname."</td></tr><tr><td colspan=\"2\" class=\"left\">".$daddress."</td></tr><tr><td>Phone 1:</td><td>".$phone1."</td></tr><tr><td>Phone 2:</td><td class=\"center\">".$phone2."</td></tr><tr><td colspan=\"2\" class=\"center\"></td></tr></table></div><div class=\"q3\"><form name=\"changecustomer\" action=\"account.php\" method=\"post\"><input type=\"hidden\" name=\"changecustomer\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"submit\" name=\"submit\" class=\"cancel-small\" value=\"Change Customer\" /></form></div></div>";

echo "\n<div id=\"invoicedate\"><div class=\"q1\"><form name=\"newdateform\" action=\"invoice.php\" method=\"post\"><table class=\"righttable\"><tr><td colspan=\"2\" class=\"center\">Change Invoice Date</td></tr><tr><td colspan=\"2\" class=\"center\"><input type=\"date\" name=\"newdate\" value=\"".$displayinvoicedate2."\"></td></tr><tr><td colspan=\"2\" class=\"center\"><input type=\"hidden\" name=\"changedate\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"submit\" class=\"btn-style\" name=\"submit\" value=\"Update Invoice Date\"></td></tr><td colspan=\"2\" class=\"center\"></td></tr></table></form></div><div class=\"q3\"><form name=\"changecustomer\" action=\"account.php\" method=\"post\"><input type=\"hidden\" name=\"changecustomer\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"submit\" name=\"submit\" class=\"btn-style\" value=\"Change Customer\" /></form></div></div>";

echo "\n<div id=\"vehicleinfo\"><div class=\"q1\"><form name=\"updatevehicle\" method=\"post\" action=\"newinvoiceinfo.php\"><table class=\"righttable\"><tr><td colspan=\"3\" class=\"left\">".$dvehicleinfo2."</td></tr><tr><td class=\"left\">VIN:</td><td colspan=\"2\">".$vin."</tr><tr><td>License:</td><td>".$license."</td><td>State:   ".$vehiclestate."</td></tr><tr href=\"editvehicleinfo\" colslpan=\"3\"><td>edit vehicle</td></tr><tr><td colspan=\"3\" class=\"center\"></td></tr></table></form><table><tr><td><form name=\"changecustomer\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"changecustomer\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"<?php echo $invoiceid;?>\"><input type=\"submit\" name=\"submit\" class=\"btn-style\" value=\"Change Vehicle\" /></form></td><td><form name=\"changecustomer\" action=\"newvehicle.php\" method=\"post\"><input type=\"hidden\" name=\"newvehicle\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"<?php echo $invoiceid;?>\"><input type=\"submit\" name=\"submit\" class=\"btn-style\" value=\"Add Vehicle\" /></form></td></tr><tr><td colspan=\"2\"><div class=\"styled-select2 black rounded\">\n<form name=\"changevehicle\" id=\"form\" action=\"invoice.php\" method=\"post\"><select name=\"vehiclechange\" onchange=\"form.submit()\">";
if($vehicleid> '0')
	{
		echo "<option value=\"$vehicleid\">".$dvehicleinfo2nb."</option>";
	}
else {
echo "<option value=\"0\"></option>";
}

$sql11 = 'SELECT * FROM `vehicles` WHERE `accountid` = :accountid AND inactive = \'0\' ORDER BY `cfdescription` DESC';
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
	$description = $row11['cfdescription'];
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
echo "\n<div id=\"editvehicleinfo\"><div class=\"q1\"><form name=\"changevehicle\" method=\"post\" action=\"invoice.php\"><table class=\"righttable\"><tr><td colspan=\"3\" class=\"left\">Vehicle Info: <input type=\"text\" name=\"vehiclename\" value=\"".$dvehicleinfo2nb."\"></td></tr><tr><td colspan=\"3\" class=\"left\">VIN:<input type=\"text\" name=\"vehiclevin\" value=\"".$vin."\"></td></tr><tr><td>License:</td><td>".$license."</td><td>State:   ".$vehiclestate."</td></tr><tr href=\"vehicleinfo2\" colslpan=\"3\"><td>edit vehicle</td></tr><tr><td colspan=\"3\" class=\"center\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"newcustomerinvoice\" value=\"1\"><input type=\"hidden\" name=\"submit\" value=\"1\"><input type=\"image\" src=\"images/buttons/checkmark.png\" alt=\"addcomment\" name=\"submit\" width=\"25\"></td></tr></table></form></div><div class=\"q3\"><table><tr><td><a href=\"editvehicle.php\">Edit Vehicle</a></td></tr><tr><td></td></tr><tr><td><a href=\"newvehicle.php\">Add New Vehicle</a></td></tr></table></div></div>";

echo "\n<div id=\"add\"><div class=\"q1\"><form name=\"addcomment\" method=\"post\" action=\"invoice.php\"><table class=\"righttable\"><tr><td colspan=\"2\" class=\"left\"><textarea class=\"wideinput\" name=\"newcomment\" placeholder=\"Add Comment\" id=\"invoicecomment\"></textarea><div id=\"auto\"</td></tr><tr><td class=\"left\">Unit Price: <input type=\"text\" class=\"narrowinput\" name=\"newprice\" placeholder=\"0.00\" autocomplete=\"off\"></td><td class=\"left\">QTY:<input type=\"number\" class=\"narrowinput\" name=\"newqty\" value=\"1\" step=\"1.00\" ></td></tr><tr><td colspan=\"2\" class=\"center\">FET: <input type=\"text\" class=\"narrowinput\" name=\"newfet\" placeholder=\"0.00\" autocomplete=\"off\"></td></tr><tr><td class=\"center\" colspan=\"2\"><input type=\"hidden\" name=\"newlinenumber\" value=\"$newlinenumber\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"newcommentform\" value=\"1\"><input type=\"hidden\" name=\"submit\" value=\"1\"><input type=\"image\" src=\"images/buttons/checkmark.png\" alt=\"addcomment\" name=\"submit\" width=\"25\"></td></tr></table></form></div></div>";
echo "\n<div id=\"note\"><div class=\"q1\"><form name=\"addnotes\" method=\"post\" action=\"invoice.php\"><table class=\"righttable\"><tr><td colspan=\"2\" class=\"left\"><textarea class=\"wideinput\" rows=\"5\" name=\"notes\" id=\"noteitem\">".$note."</textarea></td></tr><tr><td class=\"center\" colspan=\"2\"><input type=\"hidden\" name=\"notesubmit\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"submit\" value=\"1\"><input type=\"image\" src=\"images/buttons/checkmark.png\" alt=\"addcomment\" name=\"submit\" width=\"25\"></td></tr></table></form></div></div>";
echo "\n<div id=\"total\"><div class=\"q1\"><table class=\"righttable\"><tr><td colspan=\"2\" class=\"left\"><tr href=\"total\"><td colspan=\"4\">Subtotal:</td><td>".$dsubtotal."</td></tr><tr href=\"total\"><td colspan=\"4\">Sales Tax:</td><td>".$dtaxtotal."</td></tr><tr href=\"total\"><td colspan=\"4\"><b>Total:</b></td><td><b>".$dinvoicetotal."</b></td></tr><tr><td colspan=\"4\" class=\"center\">Change Tax Class:</td><td><select name=\"newtaxclass\">";
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
echo "</select></td></tr><tr><td>Void Payment</table></div>";

?>
</div></div><div class="printdiv"><table><tr>
<th colspan="2" class="center">
<center>Quick Add</center>
</th></tr><tr>
<td>
<?php
echo "<form name=\"addtireform\" action=\"inventory.php\" method=\"post\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"submit\" class=\"smallbutton\" name=\"addtire\" value=\"Add Tires\"></form></td><td><form name=\"addservicea\" action=\"services.php\" method=\"post\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"submit\" name=\"addservice\" class=\"smallbutton\" value=\"Add Service/Package\"></form></td></tr><tr>";
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
<form action="invoice.php" method="post" name="additem">
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
<?php
while ($qri > 0) {
echo "\n<script type=\"text/javascript\">\n$(\"#".$qri."row\").click(function() {\n$(\"#".$qri."box\" ).focus();\n});\n</script>";
$qri --;
}
?>
</body>
</html>

<?php
}
else {
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
    $("#highlight").hover(function(){
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
<div id="selecteduser"><form name="current1" action="invoice.php" method="POST"><table id="floatleft"><tr><td class="currentuser">Current User:</td><td class="currentitem"><div class="styled-select black rounded"><input type="hidden" name="userform" value="1">
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
        	<div id="left1">
<table>
	<tr><td><form name="quote" action="invoice.php" method="post"><input type="hidden" name="new" value="1"><input type="hidden" name="submit" value="1"><input type="hidden" name="type" value="4"><input type="submit" class="quotebutton" value="Create Quote"></form></td><td>
<form name="quote" action="invoice.php" method="post"><input type="hidden" name="new" value="1"><input type="hidden" name="submit" value="1"><input type="hidden" name="type" value="1"><input type="submit" class="quotebutton" value="Create Invoice"></form></td>

<td colspan="2"><form name="search" action="invoice.php" method="post"><input type="hidden" name="submit" value="1"><input type="text" name="invoicenumber" placeholder="Enter Transaction ID" autocomplete="off" autofocus><br /><center><input type="submit" class="smallquotebutton" value="Search"></center></form></td></tr>
</table><br /><table><tr><th class="left">Prev Day</th><th><center><form name="invoiceviewdateform" action="invoice3.php" method="post"><input type="date" name="invoicedateview" onchange="this.form.submit()" value="<?php echo $displaydate; ?>" max="<?php echo $currentday2; ?>"></form></center></th>
<th class="left">
<?php
if($displaydate == $currentday2)
{
echo "Todays Invoices";
}else{
echo $displaydate2." Invoices For ".$currentstorename;
}
?>
</th></tr></table>
<table id="dailytable"><thead>
<tr>
<th id="invrow"><input type="button" name="sortsubmit" class="btn-style-gray" value="Invoice" /></th>
<th id="namerow">Customer Name</th>
<th id="amountrow"><input type="button" name="sortsubmit" class="btn-style-gray" value="Amount" /></th>
<th id="vehiclerow"><input type="button" name="sortsubmit" class="btn-style-gray" value="Vehicle" /></th></tr></thead>
<tbody>
<?php

$sql4 = "SELECT `id`,`invoiceid`,`subtotal`,`tax`,`total`,`accountid`,`vehicleid` FROM `invoice` WHERE `invoicedate` LIKE :displaydate AND `location` = :locationid AND `type` = '1' AND `voiddate` IS NULL ORDER BY `creationdate` DESC";


$sth4 = $pdocxn->prepare($sql4);
$sth4->bindParam(':displaydate',$displaydate);
$sth4->bindParam(':locationid',$currentlocationid);
$sth4->execute();
while($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
{
$invoiceid = $row4['id'];
$invoicenumber = $row4['invoiceid'];
$subtotal = $row4['subtotal'];
$tax = $row4['tax'];
$total = $row4['total'];
$customerid = $row4['accountid'];
$vehicleid = $row4['vehicleid'];
if($total < '1')
{
$total = '0';
}
if($customerid > '0')
{
$sth5 = $pdocxn->prepare('SELECT `firstname`,`lastname` FROM `accounts` WHERE acctid = \''.$customerid.'\'');
$sth5->execute();
while($row5 = $sth5->fetch(PDO::FETCH_ASSOC))
{
$firstname = $row5['firstname'];
$lastname = $row5['lastname'];
$fullname = $firstname." ".$lastname;
}}else{
	$fullname = "No Customer Selected";
}
$sth6 = $pdocxn->prepare('SELECT `year`,`make`,`model`,`cfdescription`,`description` FROM `vehicles` WHERE id = \''.$vehicleid.'\'');
$sth6->execute();
if($vehicleid < '1')
{
$displayvehicle = "No Vehicle Selected";
}
else
{
while($row6 = $sth6->fetch(PDO::FETCH_ASSOC))
{
$year = $row6['year'];
$make = $row6['make'];
$model = $row6['model'];
$description1 = $row6['cfdescription'];
$description2 = $row6['description'];
if($year > '0')
{
$displayvehicle = $year." ".$make." ".$model;
}
else if($description1 > '1')
{
$displayvehicle = $description1; 
}
else{
$displayvehicle = $description2;
}
}
}
        
echo "\n<tr id=\"highlight\"><form name=\"form".$invoiceid."\" id=\"form".$invoiceid."\" action=\"invoice.php\" method=\"post\"><td><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\" /><input type=\"hidden\" name=\"invoicenumber\" value=\"".$invoicenumber."\" /><input type=\"submit\" name=\"submit\" class=\"btn-style\" id=\"".$invoicenumber."\" value=\"".$invoicenumber."\" /></td><td><input type=\"submit\" name=\"submit\" class=\"btn-style\" id=\"".$invoicenumber."\" value=\"".$fullname."\" /></td><td><input type=\"submit\" name=\"submit\" class=\"btn-style\" id=\"".$invoicenumber."\" value=\"".$total."\" /></td><td><input type=\"submit\" name=\"submit\" class=\"btn-style\" id=\"".$invoicenumber."\" value=\"".$displayvehicle."\" /></td></form></tr>";}
?>
</tbody>
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
<form action="invoice.php" method="post" name="copyinvoice">
<input type="hidden" name="copy" value="1">
<input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>">
<input type="hidden" name="submit" value="submit">
<input type="submit" class="smallbutton" alt="print" value="Copy Invoice" name="submit"></form>
</td></tr>
<tr><td><br />
<form action="printinvoice.php" method="post" name="printinvoice">
<input type="hidden" name="copies" value="2">
<input type="hidden" name="print" value="1">
<input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>">
<input type="hidden" name="submit" value="submit">
<input type="submit" class="smallbutton" alt="print" value="Print Invoice" name="submit"></form>
</td>
<td><br />
<form action="printinvoice.php" method="post" name="printinvoice">
<input type="hidden" name="copies" value="1">
<input type="hidden" name="print" value="1">
<input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>">
<input type="hidden" name="submit" value="submit">
<input type="submit" class="smallbutton" alt="print" value="1" name="submit"></form>
</td>
</tr>
<tr><td><br />
<form action="splitinvoice.php" method="post" name="printinvoice">
<input type="hidden" name="copies" value="2">
<input type="hidden" name="print" value="1">
<input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>">
<input type="hidden" name="submit" value="submit">
<input type="submit" class="smallbutton" alt="print" value="Split a Transation" name="submit"></form>
</td>
<td><br />
<form action="printinvoice.php" method="post" name="printinvoice">
<input type="hidden" name="copies" value="1">
<input type="hidden" name="print" value="1">
<input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>">
<input type="hidden" name="submit" value="submit">
<input type="submit" class="smallbutton" alt="print" value="Combine Transactions" name="submit"></form>
</td>
</tr>
<tr><td colspan="2"><br />
<a href="searchinvoice.php" class="no-decoration">
<input type="submit" class="smallbutton" alt="search" value="Search Invoice by keyword" name="submit"></a>
</td>
</tr></table></div>
</div>
<script type="text/javascript">
$("table tr").click(function(){
    $($('#' + $(this).attr("href"))).siblings().hide().end().fadeIn();
});
</script>
<script type="text/javascript">
function sortTable(f,n){
	var rows = $('#dailytable tbody  tr').get();
	rows.sort(function(a, b) {
		var A = getVal(a);
		var B = getVal(b);
		if(A < B) {
			return -1*f;
		}
		if(A > B) {
			return 1*f;
		}
		return 0;
	});
	function getVal(elm){
		var v = $(elm).children('td').eq(n).text().toUpperCase();
		if($.isNumeric(v)){
			v = parseInt(v,10);
		}
		return v;
	}

	$.each(rows, function(index, row) {
		$('#dailytable').children('tbody').append(row);
	});
}
var f_invrow = 1;
var f_namerow = 1;
var f_amountrow = 1;
var f_vehiclerow = 1;
$("#invrow").click(function(){
    f_invrow *= -1;
    var n = $(this).prevAll().length;
    sortTable(f_invrow,n);
});
$("#namerow").click(function(){
    f_namerow *= -1;
    var n = $(this).prevAll().length;
    sortTable(f_namerow,n);
});
$("#amountrow").click(function(){
    f_amountrow *= -1;
    var n = $(this).prevAll().length;
    sortTable(f_amountrow,n);
});
$("#vehiclerow").click(function(){
    f_vehiclerow *= -1;
    var n = $(this).prevAll().length;
    sortTable(f_vehiclerow,n);
});
</script>
</body>
</html>
<?php
}
?>