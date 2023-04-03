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
//convert from a schedule to invoice

**Invoice type ids
//1 Customer Invoice
//2 Vendor Invoice (Received Item) (order #3)
//3 Service Charge
//4 Customer Quote
//5 Order (order #1)
//6 Customer Payment
//7 Customer Adjustment
//8 Vendor Bill (order #2)
//9 Vendor Payment (order #4)
//10 Closing Entry
//11 Customer Work Order
//12 Trial Balance
//13 Vendor Adjustment
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
$title = 'Vendor Invoice';
$linkpage = 'vendoeinvoice.php';
$changecustomer = '0';

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
$displaytype = '5';
}
}else{
$displaydate = $currentday2;
$displaydate2 = date("n/j/Y", strtotime($displaydate));
if(isset($_POST['invoicetypeview']))
{
$displaytype = $_POST['invoicetypeview'];
}
else {
$displaytype = '5';
}
}
if($_POST['submit'] OR $_POST['inventorysubmit'] OR $_POST['editsubmit'] OR $_POST['servicesubmit'] OR $_GET['invoiceid'])
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
$sth1 = $pdocxn->prepare('SELECT `id` FROM `invoice` ORDER BY `id` DESC LIMIT 1');
$sth1->execute();
$row1 = $sth1->fetch(PDO::FETCH_ASSOC);
$lastinvid = $row1['id'];
$invoiceid = $lastinvid + '1';
if($typeid == '2' OR $typeid == '9')
{
$odertypeid = '5';
$sth2 = $pdocxn->prepare('INSERT INTO `invoice`(`userid`,`type`,`location`,`creationdate`,`invoicedate`,`accountid`) VALUES (:userid,:typeid,:location,:creationdate,:invoicedate,:accountid)');
$sth2->bindParam(':userid',$currentid);
$sth2->bindParam(':typeid',$odertypeid);
$sth2->bindParam(':location',$currentlocationid);
$sth2->bindParam(':creationdate',$currentdate);
$sth2->bindParam(':invoicedate',$currentday);
$sth2->bindParam(':accountid',$accountid);
$sth2->execute();
//get the order trans id
$orderid = $pdocxn->lastInsertId();

$billtypeid = '8';
$sth2 = $pdocxn->prepare('INSERT INTO `invoice`(`userid`,`type`,`location`,`creationdate`,`invoicedate`,`accountid`) VALUES (:userid,:typeid,:location,:creationdate,:invoicedate,:accountid)');
$sth2->bindParam(':userid',$currentid);
$sth2->bindParam(':typeid',$billtypeid);
$sth2->bindParam(':location',$currentlocationid);
$sth2->bindParam(':creationdate',$currentdate);
$sth2->bindParam(':invoicedate',$currentday);
$sth2->bindParam(':accountid',$accountid);
$sth2->execute();
//get the bill trans id
$billid = $pdocxn->lastInsertId();

if($typeid == '9')
{
$receivetypeid = '2';
$sth2 = $pdocxn->prepare('INSERT INTO `invoice`(`userid`,`type`,`location`,`creationdate`,`invoicedate`,`accountid`) VALUES (:userid,:typeid,:location,:creationdate,:invoicedate,:accountid)');
$sth2->bindParam(':userid',$currentid);
$sth2->bindParam(':typeid',$receivetypeid);
$sth2->bindParam(':location',$currentlocationid);
$sth2->bindParam(':creationdate',$currentdate);
$sth2->bindParam(':invoicedate',$currentday);
$sth2->bindParam(':accountid',$accountid);
$sth2->execute();
//get the bill trans id
$receiveid = $pdocxn->lastInsertId();

}
}
//enterinto invoice table
$sth2 = $pdocxn->prepare('INSERT INTO `invoice`(`userid`,`type`,`location`,`creationdate`,`invoicedate`,`accountid`) VALUES (:userid,:typeid,:location,:creationdate,:invoicedate,:accountid)');
$sth2->bindParam(':userid',$currentid);
$sth2->bindParam(':typeid',$typeid);
$sth2->bindParam(':location',$currentlocationid);
$sth2->bindParam(':creationdate',$currentdate);
$sth2->bindParam(':invoicedate',$currentday);
$sth2->bindParam(':accountid',$accountid);
$sth2->execute();
//get the recieve trans id
$invoiceid = $pdocxn->lastInsertId();

if($typeid == '2' OR $typeid == '9')
{
$sql8 = "INSERT INTO `translink` (`transid`) VALUES (:transid)";
$sth8 = $pdocxn->prepare($sql8);
$sth8->bindParam(':transid',$orderid);
$sth8->execute();
$sql8 = "SELECT `linkid` FROM `translink` WHERE `transid` = :transid";
$sth8 = $pdocxn->prepare($sql8);
$sth8->bindParam(':transid',$invoiceid);
$sth8->execute();
while($row8 = $sth8->fetch(PDO::FETCH_ASSOC))
{
	$orderlinkid = $row8['linkid'];
}
$paysql = "INSERT INTO `translink`(`transid`,`linktoid`,`amount`) VALUES (:transid,:linktoid)";
$paysth = $pdocxn->prepare($paysql);
$paysth->bindParam(':transid',$billid);
$paysth->bindParam(':linktoid',$orderlinkid);
$paysth->execute();


$sql8 = "INSERT INTO `translink` (`transid`) VALUES (:transid)";
$sth8 = $pdocxn->prepare($sql8);
$sth8->bindParam(':transid',$billid);
$sth8->execute();
$sql8 = "SELECT `linkid` FROM `translink` WHERE `transid` = :transid";
$sth8 = $pdocxn->prepare($sql8);
$sth8->bindParam(':transid',$invoiceid);
$sth8->execute();
while($row8 = $sth8->fetch(PDO::FETCH_ASSOC))
{
	$billlinkid = $row8['linkid'];
}
$paysql = "INSERT INTO `translink`(`transid`,`linktoid`,`amount`) VALUES (:transid,:linktoid)";
$paysth = $pdocxn->prepare($paysql);
$paysth->bindParam(':transid',$receiveid);
$paysth->bindParam(':linktoid',$billlinkid);
$paysth->execute();

if($typeid == '9')
{
	$sql8 = "INSERT INTO `translink` (`transid`) VALUES (:transid)";
$sth8 = $pdocxn->prepare($sql8);
$sth8->bindParam(':transid',$receiveid);
$sth8->execute();
$sql8 = "SELECT `linkid` FROM `translink` WHERE `transid` = :transid";
$sth8 = $pdocxn->prepare($sql8);
$sth8->bindParam(':transid',$invoiceid);
$sth8->execute();
while($row8 = $sth8->fetch(PDO::FETCH_ASSOC))
{
	$receivelinkid = $row8['linkid'];
}
$paysql = "INSERT INTO `translink`(`transid`,`linktoid`,`amount`) VALUES (:transid,:linktoid)";
$paysth = $pdocxn->prepare($paysql);
$paysth->bindParam(':transid',$receiveid);
$paysth->bindParam(':linktoid',$receivelinkid);
$paysth->execute();
}
}


}


//search by transactionid
$getinv = $pdocxn->prepare('SELECT `id`,`type`,`invoicedate`,`userid`,`location`,`accountid`,`subtotal`,`total` FROM `invoice` WHERE `id` = :invoiceid');
$getinv->bindparam(':invoiceid',$invoiceid);
$getinv->execute();
while($getinvrow = $getinv->fetch(PDO::FETCH_ASSOC))
{
$typeid = $getinvrow['type'];
$invoicedate = $getinvrow['invoicedate'];
$accountid = $getinvrow['accountid'];
$invoicedate2 = new DateTime($invoicedate);
$displayinvoicedate = $invoicedate2->format('n/j/Y');
$displayinvoicedate2 = $invoicedate2->format('Y-m-d');
$invoicesalesmanid = $getinvrow['userid'];
$invoicelocationid = $getinvrow['location'];
$subtotal = $getinvrow['subtotal'];
$total = $getinvrow['total'];
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

if($_POST['changetocheck']&&$_POST['changetocheck']=='1')
{
//quickpayment
if($typeid=='5')
{
$sth1 = $pdocxn->prepare('SELECT `id` FROM `invoice` ORDER BY `id` DESC LIMIT 1');
$sth1->execute();
$row1 = $sth1->fetch(PDO::FETCH_ASSOC);
$lastinvid = $row1['id'];
$newinvoiceid = $lastinvid + '1';
	//changetoreceive
$sth2 = $pdocxn->prepare('INSERT INTO `invoice`(`id`,`userid`,`type`,`location`,`creationdate`,`invoicedate`,`accountid`,`subtotal`,`total`) VALUES (:id,:userid,:typeid,:location,:creationdate,:invoicedate,:accountid,:subtotal,:total)');
$sth2->bindParam(':id',$newinvoiceid);
$sth2->bindParam(':userid',$invoicesalesmanid);
$sth2->bindParam(':typeid',$receivetype);
$sth2->bindParam(':location',$invoicelocationid);
$sth2->bindParam(':creationdate',$currentdate);
$sth2->bindParam(':invoicedate',$currentday);
$sth2->bindParam(':accountid',$accountid);
$sth2->bindParam(':subtotal',$subtotal);
$sth2->bindParam(':total',$total);
$sth2->execute()or die(print_r($sth2->errorInfo(), true));




$sth4 = $pdocxn->prepare('SELECT * FROM `line_items` WHERE `invoiceid` = :inv ORDER BY `linenumber` ASC');
$sth4->bindParam(':inv',$invoiceid);
$sth4->execute();
$linecount = $sth4->rowCount();
	while($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
{
$lineitemtype = $row4['lineitem_typeid'];
$lineitemsaletype = $row4['lineitem_saletype'];
$invqty = $row4['qty'];
$invamount = $row4['amount'];
$invpartid = $row4['partid'];
$extprice = $row4['totallineamount'];
$linenumber = $row4['linenumber'];
$databasecomment = $row4['comment'];
$invcomment = stripslashes($databasecomment);
$fet = $row4['fet'];

$sth1 = $pdocxn->prepare('INSERT INTO `line_items`(`invoiceid`,`qty`,`amount`,`linenumber`,`comment`,`fet`,`totallineamount`,`lineitem_typeid`,`lineitem_saletype`)VALUES(:invoiceid,:qty,:amount,:linenumber,:comment,:fet,:totallineamount,:lineitemtype,:lineitemsaletype)');
$sth1->bindParam(':invoiceid',$newinvoiceid);
$sth1->bindParam(':amount',$invamount);
$sth1->bindParam(':qty',$invqty);
$sth1->bindParam(':totallineamount',$extprice);
$sth1->bindParam(':linenumber',$newlinenumber);
$sth1->bindParam(':comment',$invcomment);
$sth1->bindParam(':fet',$fet);
$sth1->bindParam(':lineitemtype',$lineitemtype);
$sth1->bindParam(':lineitemsaletype',$lineitemsaletype);
$sth1->execute()or die(print_r($sth1->errorInfo(), true));
}
$typeid = $receivetype;
$invoiceid = $newinvoiceid;
}

if($typeid=='2')
{
$sth1 = $pdocxn->prepare('SELECT `id` FROM `invoice` ORDER BY `id` DESC LIMIT 1');
$sth1->execute();
$row1 = $sth1->fetch(PDO::FETCH_ASSOC);
$lastinvid = $row1['id'];
$newinvoiceid = $lastinvid + '1';
	//changetobill
$sth2 = $pdocxn->prepare('INSERT INTO `invoice`(`id`,`userid`,`type`,`location`,`creationdate`,`invoicedate`,`accountid`,`subtotal`,`total`) VALUES (:id,:userid,:typeid,:location,:creationdate,:invoicedate,:accountid,:subtotal,:total)');
$sth2->bindParam(':id',$newinvoiceid);
$sth2->bindParam(':userid',$invoicesalesmanid);
$sth2->bindParam(':typeid',$receivetype);
$sth2->bindParam(':location',$invoicelocationid);
$sth2->bindParam(':creationdate',$currentdate);
$sth2->bindParam(':invoicedate',$currentday);
$sth2->bindParam(':accountid',$accountid);
$sth2->bindParam(':subtotal',$subtotal);
$sth2->bindParam(':total',$total);
$sth2->execute()or die(print_r($sth2->errorInfo(), true));



$sth4 = $pdocxn->prepare('SELECT * FROM `line_items` WHERE `invoiceid` = :inv ORDER BY `linenumber` ASC');
$sth4->bindParam(':inv',$invoiceid);
$sth4->execute();
$linecount = $sth4->rowCount();
	while($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
{
$lineitemtype = $row4['lineitem_typeid'];
$lineitemsaletype = $row4['lineitem_saletype'];
$invqty = $row4['qty'];
$invamount = $row4['amount'];
$invpartid = $row4['partid'];
$extprice = $row4['totallineamount'];
$linenumber = $row4['linenumber'];
$databasecomment = $row4['comment'];
$invcomment = stripslashes($databasecomment);

$sth1 = $pdocxn->prepare('INSERT INTO `line_items`(`invoiceid`,`qty`,`amount`,`linenumber`,`comment`,`fet`,`totallineamount`,`lineitem_typeid`,`lineitem_saletype`)VALUES(:invoiceid,:qty,:amount,:linenumber,:comment,:fet,:totallineamount,:lineitemtype,:lineitemsaletype)');
$sth1->bindParam(':invoiceid',$newinvoiceid);
$sth1->bindParam(':amount',$invamount);
$sth1->bindParam(':qty',$invqty);
$sth1->bindParam(':totallineamount',$extprice);
$sth1->bindParam(':linenumber',$newlinenumber);
$sth1->bindParam(':comment',$invcomment);
$sth1->bindParam(':fet',$fet);
$sth1->bindParam(':lineitemtype',$lineitemtype);
$sth1->bindParam(':lineitemsaletype',$lineitemsaletype);
$sth1->execute()or die(print_r($sth1->errorInfo(), true));
}
$typeid = $billtype;
$invoiceid = $newinvoiceid;
}

if($typeid == '8')
{
header('location:printcheck.php?invoiceid='.$invoiceid.'');
}
}
if($_POST['void']&&$_POST['void']=='1')
{
//edit void invoice
$sth4 = $pdocxn->prepare('SELECT `qty`,`partid` FROM `line_items` WHERE `invoiceid` = :inv');
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

$sth1 = $pdocxn->prepare('UPDATE `invoice` SET `voiddate`=:voiddate WHERE `id` = :invoiceid');
$sth1->bindParam(':voiddate',$currentday);
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->execute();
header('location:vendorinvoice.php');
}
//submit form general
if($_POST['changedate']&&$_POST['changedate']=='1')
{
//edit change date
$newdate = $_POST['newdate'];
$sth1 = $pdocxn->prepare('UPDATE `invoice` SET `invoicedate`=:newdate WHERE `id` = :invoiceid');
$sth1->bindParam(':newdate',$newdate);
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->execute();
}
if($_POST['changecustomer']&&$_POST['changecustomer'] == '1')
{
//edit change customer
$newaccountid = $_POST['accountid'];
$getname = $pdocxn->prepare('SELECT `lastname` from `accounts` WHERE `accountid` = :accountid');
$getname->bindParam(':accountid',$newaccountid);
$getname->execute();
while($getnamerow = $getname->fetch(PDO::FETCH_ASSOC))
{
$databaselname = $getnamerow['lastname'];
$lastname = stripslashes($databaselname);
if($firstname > '0')
{
$abvname = $firstname;
}
}
$sth1 = $pdocxn->prepare('UPDATE `invoice` SET `accountid`=:accountid,`abvname`=:abvname WHERE `id` = :invoiceid');
$sth1->bindParam(':accountid',$newaccountid);
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->bindParam(':abvname',$abvname);
$sth1->execute();
}
if($_POST['delete']&&$_POST['delete']=='1')
{
//edit delete line
$lineid = $_POST['lineid'];

$sth4 = $pdocxn->prepare('SELECT `qty`,`partid` FROM `line_items` WHERE `id` = :lineid');
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
$sth1 = $pdocxn->prepare('DELETE FROM `line_items` WHERE `id` = :lineid');
$sth1->bindParam(':lineid',$lineid);
$sth1->execute();


$sth2 = $pdocxn->prepare('UPDATE `line_items` SET `linenumber` = `linenumber` - 1 WHERE `invoiceid` = :invoiceid AND `linenumber` > :deletedlinenumber');
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
$sth1 = $pdocxn->prepare('UPDATE `line_items` SET `linenumber`=:currentline WHERE `invoiceid` = :invoiceid AND `linenumber` = :previousline');
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->bindParam(':currentline',$currentline);
$sth1->bindParam(':previousline',$previousline);
$sth1->execute();
$sth2 = $pdocxn->prepare('UPDATE `line_items` SET `linenumber` = :previousline WHERE `id` = :lineid');
$sth2->bindParam(':lineid',$lineid);
$sth2->bindParam(':previousline',$previousline);
$sth2->execute();
}
if($_POST['down']&&$_POST['down'] == '1')
{
$sth1 = $pdocxn->prepare('UPDATE `line_items` SET `linenumber` = :currentline WHERE `invoiceid` = :invoiceid AND `linenumber` = :nextline');
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
if($_POST['editsubmit']&&$_POST['editsubmit'] == '1')
{
//edit line item
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


//search by transactionid
$getinv = $pdocxn->prepare('SELECT `id`,`invoiceid`,`type`,`invoicedate` FROM `invoice` WHERE `id` = :invoiceid');
$getinv->bindparam(':invoiceid',$invoiceid);
$getinv->execute();
while($getinvrow = $getinv->fetch(PDO::FETCH_ASSOC))
{
$invoicenumber = $getinvrow['invoiceid'];
$typeid = $getinvrow['type'];
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

$sql2 = "INSERT INTO `invoice`(`id`,`invoiceid`,`type`,`location`,`creationdate`,`invoicedate`,`subtotal`,`total`) VALUES (:id,:invoicenumber,:type,:location,:creationdate,:invoicedate,:subtotal,:total)";
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':id',$invoiceid);
$sth2->bindParam(':invoicenumber',$invoicenumber);
$sth2->bindParam(':type',$copytype);
$sth2->bindParam(':location',$oldlocationid);
$sth2->bindParam(':creationdate',$currentdate);
$sth2->bindParam(':invoicedate',$currentdate);
$sth2->bindParam(':subtotal',$oldsubtotal);
$sth2->bindParam(':total',$oldtotal);
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

	if($_POST['split'])
{
$split = $_POST['split'];
if(isset($_POST['split1']))
{
$oldinvoiceid = $_POST['invoiceid'];
//split a transaction
//$copytype = $_POST['copytype'];
/*$copytype = '1';
$sth1 = $pdocxn->prepare('SELECT `id`,`location`,`subtotal`,`total` FROM `invoice` WHERE `id` = :invoiceid LIMIT 1');
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
$oldinvoiceid = $invoiceid;
$oldlocationid = $row1['location'];
$oldsubtotal = $row1['subtotal'];
$oldtotal = $row1['total'];
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

$sql2 = "INSERT INTO `invoice`(`id`,`invoiceid`,`type`,`location`,`creationdate`,`invoicedate`,`subtotal`,`total`) VALUES (:id,:invoicenumber,:type,:location,:creationdate,:invoicedate,:subtotal,:total)";
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':id',$invoiceid);
$sth2->bindParam(':invoicenumber',$invoicenumber);
$sth2->bindParam(':type',$copytype);
$sth2->bindParam(':location',$oldlocationid);
$sth2->bindParam(':creationdate',$currentdate);
$sth2->bindParam(':invoicedate',$currentdate);
$sth2->bindParam(':subtotal',$oldsubtotal);
$sth2->bindParam(':total',$oldtotal);
$sth2->execute();
$newlinesplit = '1';
foreach ($splt as $newsplit)
{
$sth2 = $pdocxn->prepare('SELECT `qty`,`amount`,`partid`,`packageid` `serviceid`,`comment`,`fet`,`totallineamount`,`lineitem_typeid`,`lineitem_subtypeid`,`lineitem_saletype`,`hours`,`basecost` FROM `line_items` WHERE `invoiceid` = :invoiceid AND `linenumber` = :linenumber');
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


$copysql = "INSERT INTO `line_items`(`invoiceid`,`linenumber`,`qty`,`amount`,`partid`,`packageid`,`serviceid`,`comment`,`fet`,`totallineamount`,`lineitem_typeid`,`lineitem_subtypeid`,`lineitem_saletype`,`hours`,`basecost`) VALUES (:invoiceid,:linenumber,:qty,:amount,:partid,:packageid,:serviceid,:comment,:fet,:totallineamount,:lineitem_typeid,:lineitem_subtypeid,:lineitem_saletype,:hours,:basecost)";
$copysql2 = "INSERT INTO `line_items`(`invoiceid`,`linenumber`,`qty`,`amount`,`partid`,`packageid`,`serviceid`,`comment`,`fet`,`totallineamount`,`lineitem_typeid`,`lineitem_subtypeid`,`lineitem_saletype`,`hours`,`basecost`) VALUES ('".$invoiceid."','".$linenumber."','".$qty."','".$amount."','".$partid."','".$packageid."','".$serviceid."','".$comment."','".$fet."','".$totallineamount."','".$lineitem_typeid."','".$lineitem_subtypeid."','".$lineitem_saletype."','".$hours."','".$basecost."')";
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
}
}
 * */
if(isset($_POST['appointmentsubmit']))
{
	//how to make this an appointment??
}
}
}


/*if($_POST['servicesubmit']&&$_POST['servicesubmit'] == '1')
{
$serviceid  = $_POST['serviceid'];
$cost  = $_POST['cost'];
$servicetitle = $_POST['servicetitle'];
$servicenote = $_POST['servicenote'];

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
$sth1 = $pdocxn->prepare('SELECT `id` FROM `invoice` ORDER BY `id` DESC LIMIT 1');
$sth1->execute();
$row1 = $sth1->fetch(PDO::FETCH_ASSOC);
$lastinvid = $row1['id'];
$invoiceid = $lastinvid + '1';
$sth2 = $pdocxn->prepare('INSERT INTO `invoice`(`id`,`userid`,`type`,`location`,`creationdate`) VALUES (:id,:userid,:type,:location,:creationdate)');
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
$query2 = mysql_query($sql2);
while ($row2 = mysql_fetch_assoc($query2))
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
$sth3 = $pdocxn->prepare('INSERT INTO `line_items`(`invoiceid`,`qty`,`amount`,`linenumber`,`comment`,`fet`,`totallineamount`,`partid`)VALUES(:invoiceid,:qty,:amount,:linenumber,:comment,:fet,:totallineamount,:partid)');
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


$sth3 = $pdocxn->prepare('SELECT * FROM `invoice` WHERE id = :inv');
$sth3->bindParam(':inv',$invoiceid);
$sth3->execute();
while($row3 = $sth3->fetch(PDO::FETCH_ASSOC))
{
$accountid = $row3['accountid'];
$typeid = $row3['type'];
$invuserid = $row3['userid'];
$location = $row3['location'];
$invoicenumber = $row3['invoiceid'];
$invoicedate = $row3['invoicedate'];
$invoicedate2 = new DateTime($invoicedate);
$displayinvoicedate = $invoicedate2->format('n/j/Y');
$displayinvoicedate2 = $invoicedate2->format('Y-m-d');
}

$sth4a = $pdocxn->prepare('SELECT `username` FROM `employees` WHERE id = :userid');
$sth4a->bindParam(':userid',$invuserid);
$sth4a->execute();
while($row4a = $sth4a->fetch(PDO::FETCH_ASSOC))
{
$invsalesman = $row4a['username'];
}
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
$priceclass = $row4['priceclass'];
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
$dcustomerinfo1 = "<font color=\"blue\">".$lname."</font><br /><font color=\"blue\">".$phone1."</font>";
//$dcustomerinfo1 = "<font color=\"blue\">".$fullname."</font> <a href=\"\" class=\"no-decoration\"><input type=\"button\" class=\"smallbutton\" value=\"(last active ".$dlastactivedate.")\"></a><br /><font color=\"blue\">".$phone1."</font>";
$dcustomerinfo2 = "<font color=\"blue\">".$daddress."</font>";
if($accountid < '1')
{
$dcustomerinfo = "No Vendor selected";
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
}
else {
${"ip".$tri} = "<div id=\"$tri\"><div class=\"q1\"><table class=\"righttable\"><tr><td colspan=\"2\" class=\"center\"><a href=\"accounthistory.php?accountid=".$accountid."\">Account History</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"editaccounts.php?accountid=".$accountid."\">Edit Info</a></td></tr><tr><td colspan=\"2\" class=\"center\">$fullname</td></tr><tr><td colspan=\"2\" class=\"left\">$address&nbsp;$city,&nbsp;$state&nbsp;$zip</td></tr><tr><td colspan=\"2\">$address2</td></tr>".$dphone1."".$dphone2."".$dphone3."".$dphone4."".$dfax."<tr></table></div><div class=\"q3\"><table class=\"righttable\"></table></div></div>";
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
//$sql72 = 'INSERT INTO `notes`(`note`,`invoiceid`,`accountid`,`notetype`)VALUES(\''.$note.'\',\''.$invoiceid.'\',\''.$accountid.'\',\''.$notetype.'\')';
//echo $sql72;
$sth7 = $pdocxn->prepare($sql7);
$sth7->bindParam(':note',$note);
$sth7->bindParam(':invoiceid',$invoiceid);
$sth7->execute();
}else{

$sql7 = 'INSERT INTO `notes`(`note`,`invoiceid`,`accountid`,`notetype`)VALUES(:note,:invoiceid,:accountid,:notetype)';
//$sql72 = 'INSERT INTO `notes`(`note`,`invoiceid`,`accountid`,`notetype`)VALUES(\''.$note.'\',\''.$invoiceid.'\',\''.$accountid.'\',\''.$notetype.'\')';
//echo $sql72;
$sth7 = $pdocxn->prepare($sql7);
$sth7->bindParam(':note',$note);
$sth7->bindParam(':invoiceid',$invoiceid);
$sth7->bindParam(':accountid',$accountid);
$sth7->bindParam(':notetype',$notetype);
$sth7->execute();
$qtya = '1';
$linenumberna = '1';
$typeida = '52';
$sth1a = $pdocxn->prepare('SELECT `id` FROM `customerinteractions` ORDER BY `id` DESC LIMIT 1');
$sth1a->execute();
$row1a = $sth1a->fetch(PDO::FETCH_ASSOC);
$lastinvida = $row1a['id'];
$invoiceida = $lastinvida + '1';
$sth2a = $pdocxn->prepare('INSERT INTO `customerinteractions`(`id`,`userid`,`type`,`location`,`creationdate`,`invoicedate`,`accountid`) VALUES (:id,:userid,:typeid,:location,:creationdate,:invoicedate,:accountid)');
$sth2a->bindParam(':id',$invoiceida);
$sth2a->bindParam(':userid',$currentid);
$sth2a->bindParam(':typeid',$typeida);
$sth2a->bindParam(':location',$currentlocationid);
$sth2a->bindParam(':creationdate',$currentdate);
$sth2a->bindParam(':invoicedate',$currentday);
$sth2a->bindParam(':accountid',$accountid);
$sth2a->execute();

$copysqla = "INSERT INTO `ci_line_items`(`invoiceid`,`linenumber`,`qty`,`comment`) VALUES (:invoiceid,:linenumber,:qty,:comment)";
$copystha = $pdocxn->prepare($copysqla);
$copystha->bindParam(':invoiceid',$invoiceida);
$copystha->bindParam(':linenumber',$linenumberna);
$copystha->bindParam(':qty',$qtya);
$copystha->bindParam(':comment',$note);
$copystha->execute();

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
<script type="text/javascript" src="http://whatcomputertobuy.com/js/script.js"></script>
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
<form action="vendorinvoice.php" method="post" name="voidinvoice">
<input type="hidden" name="void" value="1">
<input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>">
<input type="hidden" name="submit" value="1">
<input type="submit" class="cancel" value="Void <?php echo $typename; ?>" onclick="myFunction()"></form>
</td><td>
<a href="vendorinvoice.php" class="no-decoration">
<input type="button" class="save" value="Save"></a></td>
<td><form action="printvendorinvoice.php" method="post" name="printinvoice">
<input type="hidden" name="copies" value="1">
<input type="hidden" name="print" value="1">
<input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>">
<input type="hidden" name="submit" value="1">
<input type="submit" class="save" alt="print" value="Print <?php echo $typename; ?>" name="submit"></form></td><td>
<form action="emailvendorinvoice.php" method="post" name="emailinvoice">
<input type="hidden" name="email" value="1">
<input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>">
<input type="hidden" name="submit" value="1">
<input type="submit" class="save" value="Email <?php echo $typename; ?>"></form></td><td>
<?php
if($typeid == '6')
{}else{
if($typeid == '9')
{
?>
<form action="payments.php" method="post" name="payment">
<input type="hidden" name="invoiceform" value="1">
<input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>">
<input type="hidden" name="accountid" value="<?php echo $accountid; ?>">
<input type="hidden" name="invoiceamount" value="<?php echo $invoiceformtotal; ?>">
<input type="submit" class="save" value="Print Check"></form>
<?php
}else{
?>
<form action="vendorinvoice.php" method="post" name="changetype">
<input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>">
<input type="hidden" name="changetocheck" value="1">
<input type="hidden" name="submit" value="1">
<input type="submit" class="save" value="Quick Payment"></form>
<?php
}
?></td><td>
<form action="vendorinvoice.php" method="post" name="copyinvoice">
<input type="hidden" name="copy" value="1">
<input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>">
<input type="hidden" name="accountid" value="<?php echo $accountid; ?>">
<input type="hidden" name="submit" value="1">
<input type="submit" class="save" alt="print" value="Copy <?php echo $typename; ?>" name="submit"></form></td>
<?php
}
?>
</tr></table></div>
<div id="selecteduser"><form name="current1" action="vendorinvoice.php" method="POST"><table id="floatleft"><tr><td class="currentuser">Salesperson:</td><td class="currentitem"><div class="styled-select black rounded"><input type="hidden" name="userform" value="1">
<select name="user" onchange="form.submit()">
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
</div></td><td class="currentstore">Store:</td><td class="currentitem"><div class="styled-select black rounded"><select name="location" onchange="form.submit()"><?php
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
</select></div><input type="hidden" name="form" value="1"></form>
</td><td><?php echo $typename; ?> <b>#<?php if($typeid == '1'){ echo $invoicenumber; }else{ echo $invoiceid; }?></b></td><td href="invoicedate">Date: <b><?php echo $displayinvoicedate; ?></b></td><td>PO Number: <input type="textbox" name="ponumber" value="<?php echo $ponumber; ?>"></td></tr></table></div>
<div id="content">
<div id="left">
<?php
if($accountid > '0')
{
?>
<table width="100%">
	<tr><td href="customerinfo" class="tdleft" width="50%"><?php echo $dcustomerinfo1; ?></td></tr>
	<tr><td href="customerinfo" class="tdleft"><?php echo $dcustomerinfo2; ?></td></tr>
</table>
<?php
}else
{
echo "<table><tr><td><form name=\"changecustomer\" action=\"account.php\" method=\"post\"><input type=\"hidden\" name=\"changecustomer\" value=\"2\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"submit\" name=\"submit\" class=\"quotebutton\" value=\"Select Vendor\" /></form></td></tr></table>";
}
?>
<?php
if($split > '1')
{
echo "<form name=\"splitform\" action=\"vendorinvoice.php\" method=\"POST\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"submit\" value=\"1\"><input type=\"hidden\" name=\"split\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"split1\" value=\"1\">";
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
<?php
if($split > '1')
{
echo "<th>Select Items to Split</th>";
}
?>
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
	$displayup = "<td><form action=\"vendorinvoice.php\" method=\"post\" name=\"linemoveform\"><input type=\"hidden\" name=\"currentline\" value=\"".$linenumber."\"><input type=\"hidden\" name=\"up\" value=\"1\"><input type=\"hidden\" name=\"lineid\" value=\"".$lineid."\"><input type=\"hidden\" name=\"linemove\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"submit\" value=\"submit\"><input type=\"image\" src=\"images/icons/up.png\" alt=\"up\" name=\"submit\" width=\"20\"></form></td>";
}
if($linenumber == $linecount)
{
	$displaydown = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
}
else {
	$displaydown = "<form action=\"vendorinvoice.php\" method=\"post\" name=\"linvemoveform\"><input type=\"hidden\" name=\"currentline\" value=\"".$linenumber."\"><input type=\"hidden\" name=\"down\" value=\"1\"><input type=\"hidden\" name=\"lineid\" value=\"".$lineid."\"><input type=\"hidden\" name=\"linemove\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"submit\" value=\"submit\"><input type=\"image\" src=\"images/icons/down.png\" alt=\"down\" name=\"submit\" width=\"20\"></form>";
}
$prevlineid = $row4['id'];
	echo "\n<tr href=\"$tri\" id=\"".$qri."row\"><td>$invqty</td><td>$invcomment</td><td>$fet</td><td>$invamount</td><td>$dextprice</td>";
	if($split > '1')
	{
		echo "<td><input type=\"checkbox\" name=\"checkedlineid[]\" value=\"".$lineid."\"></td>";
		}
		echo "</tr>";
${"ip".$tri} = "\n<div id=\"$tri\"><div class=\"q1\"><form name=\"updatecomment\" method=\"post\" action=\"vendorinvoice.php\"><table class=\"righttable\"><tr>";
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
${"ip".$tri} .= "</tr><tr><td class=\"left\">Payment Amount: <input type=\"text\" class=\"narrowinput\" name=\"price\" value=\"$unitprice\" autocomplete=\"off\"></td></tr><tr><td class=\"center\" colspan=\"2\"><input type=\"hidden\" name=\"editsubmit\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"lineid\" value=\"".$lineid."\"><input type=\"hidden\" name=\"submit\" value=\"submit\"><input type=\"submit\" class=\"xsmallbutton\" alt=\"down\" value=\"Update\"></form></td><td><form action=\"vendorinvoice.php\" method=\"post\" name=\"deletelinenumber\"><input type=\"hidden\" name=\"delete\" value=\"1\"><input type=\"hidden\" name=\"deletedlinenumber\" value=\"".$linenumber."\"><input type=\"hidden\" name=\"lineid\" value=\"".$lineid."\"><input type=\"hidden\" name=\"partid\" value=\"$invpartid\"><input type=\"hidden\" name=\"qty\" value=\"".$invqty."\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"submit\" value=\"submit\"><input type=\"submit\" class=\"cancel-small\" alt=\"down\" value=\"Delete Item\"></form></td></tr></table></form><table width=\"10%\"><tr>".$displayup."<td></td></tr><tr><td>".$displaydown."</td></tr></table></div></div>\n";	
}else{
${"ip".$tri} .= "</tr><tr><td class=\"left\">Unit Price: <input type=\"text\" class=\"narrowinput\" name=\"price\" value=\"$unitprice\" autocomplete=\"off\"></td><td class=\"left\">QTY:<input type=\"number\" class=\"narrowinput\" name=\"qty\" value=\"".$invqty."\" step=\"any\" autocomplete=\"off\"></td><td class=\"center\">FET: <input type=\"text\" class=\"narrowinput\" name=\"newfet\" value=\"$fet\" autocomplete=\"off\"></td></tr><tr><td class=\"center\" colspan=\"2\"><input type=\"hidden\" name=\"editsubmit\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"lineid\" value=\"".$lineid."\"><input type=\"hidden\" name=\"submit\" value=\"submit\"><input type=\"submit\" class=\"xsmallbutton\" alt=\"down\" value=\"Update\"></form></td><td><form action=\"vendorinvoice.php\" method=\"post\" name=\"deletelinenumber\"><input type=\"hidden\" name=\"delete\" value=\"1\"><input type=\"hidden\" name=\"deletedlinenumber\" value=\"".$linenumber."\"><input type=\"hidden\" name=\"lineid\" value=\"".$lineid."\"><input type=\"hidden\" name=\"partid\" value=\"$invpartid\"><input type=\"hidden\" name=\"qty\" value=\"".$invqty."\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"submit\" value=\"submit\"><input type=\"submit\" class=\"cancel-small\" alt=\"down\" value=\"Delete Item\"></form></td></tr></table></form><table width=\"10%\"><tr>".$displayup."<td></td></tr><tr><td>".$displaydown."</td></tr></table></div></div>\n";
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
$dinvoicetotal = money_format('%(#0.2n',$invoicesubtotal);
if($typeid =='6')
{}else{
echo "<tr href=\"total\"><td colspan=\"4\">Total:</td><td>".$dinvoicetotal."</td></tr>";

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
}
if($split > '1')
{
	echo "<tr><td colspan=\"2\"><input type=\"submit\" name=\"appointmentsubmit\" class=\"quotebutton\" value=\"Add Items to Schedule\" /></td><td colspan=\"2\"><input type=\"submit\" name=\"quotesubmit\" class=\"quotebutton\" value=\"Add Items to Quote\" /></td><td colspan=\"2\"><input type=\"submit\" name=\"invoicesubmit\" class=\"quotebutton\" value=\"Add Items to Invoice\" /></td></tr>";
}
?>
</tbody></table></form></div>
<div class="right">
<?php
$sth1 = $pdocxn->prepare('UPDATE `invoice` SET `subtotal`=:subtotal,`total`=:total WHERE `id` = :invoiceid');
$sth1->bindParam(':subtotal',$invoicesubtotal);
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
echo "</form></td></tr><tr><td colspan=\"2\" class=\"left\">".$daddress."</td></tr><tr><td>Phone 1:</td><td>".$phone1."</td></tr><tr><td>Phone 2:</td><td class=\"center\">".$phone2."</td></tr><tr><td colspan=\"2\" class=\"center\"></td></tr></table></div><div class=\"q3\"><form name=\"changecustomer\" action=\"account.php\" method=\"post\"><input type=\"hidden\" name=\"changecustomer\" value=\"2\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"submit\" name=\"submit\" class=\"cancel-small\" value=\"Change Vendor\" /></form></div></div>";
echo "\n<div id=\"invoicedate\"><div class=\"q1\"><form name=\"newdateform\" action=\"vendorinvoice.php\" method=\"post\"><table class=\"righttable\"><tr><td colspan=\"2\" class=\"center\">Change Invoice Date</td></tr><tr><td colspan=\"2\" class=\"center\"><input type=\"date\" name=\"newdate\" value=\"".$displayinvoicedate2."\"></td></tr><tr><td colspan=\"2\" class=\"center\"><input type=\"hidden\" name=\"changedate\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"submit\" class=\"btn-style\" name=\"submit\" value=\"Update Invoice Date\"></td></tr></table></form></div><div class=\"q3\"><form name=\"changecustomer\" action=\"account.php\" method=\"post\"><input type=\"hidden\" name=\"changecustomer\" value=\"2\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"submit\" name=\"submit\" class=\"btn-style\" value=\"Change Vendor\" /></form></div></div>";


echo "\n<div id=\"add\"><div class=\"q1\"><form name=\"addcomment\" method=\"post\" action=\"vendorinvoice.php\"><table class=\"righttable\"><tr><td colspan=\"2\" class=\"left\"><textarea class=\"wideinput\" name=\"newcomment\" placeholder=\"Add Comment\" id=\"invoicecomment\"></textarea><div id=\"auto\"></div></td></tr><tr><td class=\"left\">Unit Price: <input type=\"text\" class=\"narrowinput\" id=\"unitprice\" name=\"newprice\" placeholder=\"0.00\" autocomplete=\"off\"><input type=\"button\" id=\"multiply1\" data-quantity=\"1.5\" value=\"x 1.5\" class=\"smallbutton\"><input type=\"button\" id=\"multiply2\" data-quantity=\"2\" value=\"x 2\" class=\"smallbutton\"></td><td class=\"left\">QTY:<input type=\"number\" class=\"narrowinput\" name=\"newqty\" value=\"1\" step=\"any\" ></td></tr><tr><td colspan=\"2\" class=\"center\">FET: <input type=\"text\" class=\"narrowinput\" name=\"newfet\" placeholder=\"0.00\" autocomplete=\"off\"></td></tr><tr><td class=\"center\" colspan=\"2\"><input type=\"hidden\" name=\"newlinenumber\" value=\"$newlinenumber\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"newcommentform\" value=\"1\"><input type=\"hidden\" name=\"submit\" value=\"1\"><input type=\"image\" src=\"images/buttons/checkmark.png\" alt=\"addcomment\" name=\"submit\" width=\"25\"></td></tr></table></form></div></div>";
echo "\n<div id=\"note\"><div class=\"q1\"><form name=\"addnotes\" method=\"post\" action=\"vendorinvoice.php\"><table class=\"righttable\"><tr><td colspan=\"2\" class=\"left\"><textarea class=\"wideinput\" rows=\"5\" name=\"notes\" id=\"noteitem\">".$note."</textarea></td></tr><tr><td class=\"center\" colspan=\"2\"><input type=\"hidden\" name=\"notesubmit\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"submit\" value=\"1\"><input type=\"image\" src=\"images/buttons/checkmark.png\" alt=\"addcomment\" name=\"submit\" width=\"25\"></td></tr></table></form></div></div>";
echo "\n<div id=\"total\"><div class=\"q1\"></div>";
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
<td class="center">
<?php
echo "<form name=\"addtireform\" action=\"inventory-receive.php\" method=\"post\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"submit\" class=\"quotebutton\" name=\"addtire\" value=\"Add Tires\"></form></td></tr><tr>";
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
</body>
</html>
<?php


	
	
//fkmfkmfkmfkmfkm	
	
if(isset($_GET['split'])&&$_GET['split']=='1')
{
	
//display html - split no invoiceid
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
<script type="text/javascript" src="http://whatcomputertobuy.com/js/script.js"></script>
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
<div id="selecteduser"><form name="current1" action="vendorinvoice.php" method="POST"><table id="floatleft"><tr><td class="currentuser">Current User:</td><td class="currentitem"><div class="styled-select black rounded"><input type="hidden" name="userform" value="1">
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
<tr>
<td colspan="2"><form name="search" action="vendorinvoice.php" method="post"><input type="hidden" name="submit" value="1"><input type="hidden" name="split" value="1"><input type="text" name="invoicenumber" size="50" placeholder="Enter Transaction ID to Split or select one below" autocomplete="off" autofocus><br /><center><input type="submit" class="smallquotebutton" value="Search"></center></form></td></tr>
</table><br /><table><tr><th class="left">Prev Day</th><th><center><form name="invoiceviewdateform" action="vendorinvoice.php?split=1" method="post"><input type="hidden" name="invoicetypeview" value="<?php echo $displaytype; ?>"><input type="date" name="invoicedateview" onchange="this.form.submit()" value="<?php echo $displaydate; ?>" max="<?php echo $currentday2; ?>"></form></center></th>
<th class="left">
<?php
if($displaytype == '8'){
	$select1text = "Vendor Bill";
}
if($displaytype == '5'){
	$select1text = "Vendor Order";
}
if($displaytype == '9'){
	$select1text = "Vendor Payment";
}
if($displaytype == '2'){
	$select1text = "Received Items";
}
if($displaytype == '14'){
	$select1text = "Returns";
}
if($displaytype == '13'){
	$select1text = "Adjustment";
}
$selecttypeview = "<select name=\"invoicetypeview\" onchange=\"form.submit()\"><option value=\"".$displaytype."\">".$select1text."</option><option value=\"1\">Invoice</option><option value=\"4\">Quotes</option><option value=\"11\">Work Order</option><option value=\"6\">Payment</option><option value=\"18\">Refund</option><option value=\"17\">Credit Invoices</option><option value=\"7\">Adjustment</option>";

if($displaydate == $currentday2)
{
echo "Todays</th><th class=\"left\"><div class=\"styled-select black rounded\"><form name=\"invoiceviewdateform\" action=\"vendorinvoice.php?split=1\" method=\"post\"><input type=\"hidden\" name=\"invoicedateview\" value=\"".$displaydate."\">".$selecttypeview;
}else{
echo $displaydate2." ".$currentstorename."</th><th class=\"left\"><div class=\"styled-select black rounded\"><form name=\"invoiceviewdateform\" action=\"vendorinvoice.php?split=1\" method=\"post\"><input type=\"hidden\" name=\"invoicedateview\" value=\"".$displaydate."\">".$selecttypeview;
}
?>
</select></form></div>
</th></tr>
<tr>
<?php
if($sort == '0')
{
?>
<th><form name="sortform" action="vendorinvoice.php?split=1" method="post"><input type="hidden" name="sort" value="1"><input type="submit" name="sortsubmit" class="btn-style-gray" value="<?php echo $select1text; ?>" /></form></th>
<th><form name="sortform" action="vendorinvoice.php?split=1" method="post"><input type="hidden" name="sort" value="3"><input type="submit" name="sortsubmit" class="btn-style-gray" value="Vendor Name" /></form></th>
<th><form name="sortform" action="vendorinvoice.php?split=1" method="post"><input type="hidden" name="sort" value="5"><input type="submit" name="sortsubmit" class="btn-style-gray" value="Amount" /></form></th></tr>
<?php
}
else
{
if($sort == '1')
{
?>
<th><form name="sortform" action="vendorinvoice.php?split=1" method="post"><input type="hidden" name="sort" value="2"><input type="submit" name="sortsubmit" class="btn-style-dark" value="<?php echo $select1text; ?>" /></form></th>
<?php
}else if($sort == '2')
{
?>
<th><form name="sortform" action="vendorinvoice.php?split=1" method="post"><input type="hidden" name="sort" value="1"><input type="submit" name="sortsubmit" class="btn-style-dark" value="<?php echo $select1text; ?>" /></form></th>
<?php
}else{
?>
<th><form name="sortform" action="vendorinvoice.php?split=1" method="post"><input type="hidden" name="sort" value="1"><input type="submit" name="sortsubmit" class="btn-style-gray" value="<?php echo $select1text; ?>" /></form></th>
<?php
}
if($sort == '3')
{
?>
<th><form name="sortform" action="vendorinvoice.php?split=1" method="post"><input type="hidden" name="sort" value="4"><input type="submit" name="sortsubmit" class="btn-style-dark" value="Vendor Name" /></form></th>
<?php
}else if($sort == '4'){
?>
<th><form name="sortform" action="vendorinvoice.php?split=1" method="post"><input type="hidden" name="sort" value="3"><input type="submit" name="sortsubmit" class="btn-style-dark" value="Vendor Name" /></form></th>
<?php
}else{
?>
<th><form name="sortform" action="vendorinvoice.php?split=1" method="post"><input type="hidden" name="sort" value="3"><input type="submit" name="sortsubmit" class="btn-style-gray" value="Vendor Name" /></form></th>
<?php
}
if($sort == '5')
{
?>
<th><form name="sortform" action="vendorinvoice.php?split=1" method="post"><input type="hidden" name="sort" value="6"><input type="submit" name="sortsubmit" class="btn-style-dark" value="Amount" /></form></th>
<?php
}else if($sort == '6'){
?>
<th><form name="sortform" action="vendorinvoice.php?split=1" method="post"><input type="hidden" name="sort" value="5"><input type="submit" name="sortsubmit" class="btn-style-dark" value="Amount" /></form></th>
<?php
}else{
?>
<th><form name="sortform" action="vendorinvoice.php?split=1" method="post"><input type="hidden" name="sort" value="5"><input type="submit" name="sortsubmit" class="btn-style-gray" value="Amount" /></form></th>
<?php
}
}
$sql4 = "SELECT `id`,`invoiceid`,`subtotal`,`total`,`accountid` FROM `invoice` WHERE `invoicedate` LIKE :displaydate AND `location` = :locationid AND `type` = :displaytype AND `voiddate` IS NULL ";
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
else{
$sql4 .= ' ORDER BY `creationdate` DESC';
}
$sth4 = $pdocxn->prepare($sql4);
$sth4->bindParam(':displaydate',$displaydate);
$sth4->bindParam(':locationid',$currentlocationid);
$sth4->bindParam(':displaytype',$displaytype);
$sth4->execute();
$i = '1';
while($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
{
$invoiceid = $row4['id'];
$invoicenumber = $row4['invoiceid'];
$subtotal = $row4['subtotal'];
$total = $row4['total'];
$customerid = $row4['accountid'];
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
	$fullname = "No Vendor Selected";
}
$fullname = substr($fullname,0,25);
echo "\n<tr id=\"highlight".$i."\"><form name=\"form".$invoiceid."\" id=\"form".$invoiceid."\" action=\"vendorinvoice.php\" method=\"post\"><td><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\" />";
echo "<input type=\"submit\" name=\"submit\" class=\"btn-style\" id=\"".$invoiceid."\" value=\"".$invoiceid."\" />";
echo "</td><td><input type=\"hidden\" name=\"split\" value=\"".$invoiceid."\"><input type=\"submit\" name=\"submit\" class=\"btn-style\" id=\"".$invoicenumber."\" value=\"".$fullname."\" /></td><td><input type=\"submit\" name=\"submit\" class=\"btn-style\" id=\"".$invoicenumber."\" value=\"$".$total."\" /></td></form></tr>";
$i ++;
}
?>
</table>
</div>
<div class="right1">
<table><tr><td><form action="payments.php" method="post" name="payment">
<input type="hidden" name="payment" value="1">
<input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>">
<input type="hidden" name="accountid" value="<?php echo $accountid; ?>">
<input type="hidden" name="submit" value="submit">
<input type="submit" class="smallbutton" alt="print" value="Enter Payment" name="submit"></form></td>
<td>
<form action="vendorinvoice.php" method="post" name="copyinvoice">
<input type="hidden" name="copy" value="1">
<input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>">
<input type="hidden" name="submit" value="submit">
<input type="submit" class="smallbutton" alt="print" value="Copy Invoice" name="submit"></form>
</td></tr>
<tr><td><br />
<a href="vendorinvoice.php?split=1" class="no-decoration">
<input type="button" class="smallbutton" alt="split" value="Split a Transation"></a>
</td>
<td><br />
<a href="account.php?combine=1" class="no-decoration">
<input type="button" class="smallbutton" alt="Combine" value="Combine Transactions"></a>
</td>
</tr>
<tr><td colspan="2"><br />
<a href="searchvendorinvoice.php" class="no-decoration">
<input type="submit" class="smallbutton" alt="search" value="Search Invoice by keyword" name="submit"></a>
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
<script type="text/javascript" src="http://whatcomputertobuy.com/js/script.js"></script>
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
<div id="selecteduser"><form name="current1" action="vendorinvoice.php" method="POST"><table id="floatleft"><tr><td class="currentuser">Current User:</td><td class="currentitem"><div class="styled-select black rounded"><input type="hidden" name="userform" value="1">
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
	<tr><td><a href="vendors.php?invoice=2" class="no-decoration"><input type="button" class="quotebutton" value="Receive Inventory"></a></td><td>
<a href="vendors.php?invoice=9" class="no-decoration"><input type="submit" class="quotebutton" value="Print Check"></a></td>

<td colspan="2"><form name="search" action="vendorinvoice.php" method="post"><input type="hidden" name="submit" value="1"><input type="text" name="invoicenumber" placeholder="Enter Transaction ID" autocomplete="off" autofocus><br /><center><input type="submit" class="smallquotebutton" value="Search"></center></form></td></tr>
</table><br /><table><tr><th class="left">Prev Day</th><th><center><form name="invoiceviewdateform" action="vendorinvoice.php" method="post"><input type="hidden" name="invoicetypeview" value="<?php echo $displaytype; ?>"><input type="date" name="invoicedateview" onchange="this.form.submit()" value="<?php echo $displaydate; ?>" max="<?php echo $currentday2; ?>"></form></center></th>
<th class="left">
<?php
if($displaytype == '8'){
	$select1text = "Vendor Bill";
}
if($displaytype == '5'){
	$select1text = "Vendor Order";
}
if($displaytype == '9'){
	$select1text = "Vendor Payment";
}
if($displaytype == '2'){
	$select1text = "Received Items";
}
if($displaytype == '14'){
	$select1text = "Returns";
}
if($displaytype == '13'){
	$select1text = "Adjustment";
}
$selecttypeview = "<select name=\"invoicetypeview\" onchange=\"form.submit()\"><option value=\"".$displaytype."\">".$select1text."</option>";
//fkmfkmfkmfkm
$sth1 = $pdocxn->prepare('SELECT `id`,`description` FROM `invoice_type` WHERE `accounttype` = \'2\' AND `inactive` = \'0\' ORDER BY `name` ASC');
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
$vendortypeid = $row1['id'];
$vendortypename = $row1['description'];
$selecttypeview .= "<option value=\"".$vendortypeid."\">".$vendortypename."</option>";
}

if($displaydate == $currentday2)
{
echo "Todays</th><th class=\"left\"><div class=\"styled-select black rounded\"><form name=\"invoiceviewdateform\" action=\"vendorinvoice.php\" method=\"post\"><input type=\"hidden\" name=\"invoicedateview\" value=\"".$displaydate."\">".$selecttypeview;
}else{
echo $displaydate2." ".$currentstorename."</th><th class=\"left\"><div class=\"styled-select black rounded\"><form name=\"invoiceviewdateform\" action=\"vendorinvoice.php\" method=\"post\"><input type=\"hidden\" name=\"invoicedateview\" value=\"".$displaydate."\">".$selecttypeview;
}
?>
</select></form></div>
</th></tr>
<tr>
<?php
if($sort == '0')
{
?>
<th><form name="sortform" action="vendorinvoice.php" method="post"><input type="hidden" name="sort" value="1"><input type="submit" name="sortsubmit" class="btn-style-gray" value="<?php echo $select1text; ?>" /></form></th>
<th><form name="sortform" action="vendorinvoice.php" method="post"><input type="hidden" name="sort" value="3"><input type="submit" name="sortsubmit" class="btn-style-gray" value="Vendor Name" /></form></th>
<th><form name="sortform" action="vendorinvoice.php" method="post"><input type="hidden" name="sort" value="5"><input type="submit" name="sortsubmit" class="btn-style-gray" value="Amount" /></form></th>
</tr>
<?php
}
else
{
if($sort == '1')
{
?>
<th><form name="sortform" action="vendorinvoice.php" method="post"><input type="hidden" name="sort" value="2"><input type="submit" name="sortsubmit" class="btn-style-dark" value="<?php echo $select1text; ?>" /></form></th>
<?php
}else if($sort == '2')
{
?>
<th><form name="sortform" action="vendorinvoice.php" method="post"><input type="hidden" name="sort" value="1"><input type="submit" name="sortsubmit" class="btn-style-dark" value="<?php echo $select1text; ?>" /></form></th>
<?php
}else{
?>
<th><form name="sortform" action="vendorinvoice.php" method="post"><input type="hidden" name="sort" value="1"><input type="submit" name="sortsubmit" class="btn-style-gray" value="<?php echo $select1text; ?>" /></form></th>
<?php
}
if($sort == '3')
{
?>
<th><form name="sortform" action="vendorinvoice.php" method="post"><input type="hidden" name="sort" value="4"><input type="submit" name="sortsubmit" class="btn-style-dark" value="Vendor Name" /></form></th>
<?php
}else if($sort == '4'){
?>
<th><form name="sortform" action="vendorinvoice.php" method="post"><input type="hidden" name="sort" value="3"><input type="submit" name="sortsubmit" class="btn-style-dark" value="Vendor Name" /></form></th>
<?php
}else{
?>
<th><form name="sortform" action="vendorinvoice.php" method="post"><input type="hidden" name="sort" value="3"><input type="submit" name="sortsubmit" class="btn-style-gray" value="Vendor Name" /></form></th>
<?php
}
if($sort == '5')
{
?>
<th><form name="sortform" action="vendorinvoice.php" method="post"><input type="hidden" name="sort" value="6"><input type="submit" name="sortsubmit" class="btn-style-dark" value="Amount" /></form></th>
<?php
}else if($sort == '6'){
?>
<th><form name="sortform" action="vendorinvoice.php" method="post"><input type="hidden" name="sort" value="5"><input type="submit" name="sortsubmit" class="btn-style-dark" value="Amount" /></form></th>
<?php
}else{
?>
<th><form name="sortform" action="vendorinvoice.php" method="post"><input type="hidden" name="sort" value="5"><input type="submit" name="sortsubmit" class="btn-style-gray" value="Amount" /></form></th>
<?php
}
}
$sql4 = "SELECT `id`,`invoiceid`,`subtotal`,`total`,`accountid` FROM `invoice` WHERE `invoicedate` LIKE :displaydate AND `location` = :locationid AND `type` = :displaytype AND `voiddate` IS NULL ";
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
else{
$sql4 .= ' ORDER BY `creationdate` DESC';
}
$sth4 = $pdocxn->prepare($sql4);
$sth4->bindParam(':displaydate',$displaydate);
$sth4->bindParam(':locationid',$currentlocationid);
$sth4->bindParam(':displaytype',$displaytype);
$sth4->execute();
$i = '1';
while($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
{
$invoiceid = $row4['id'];
$invoicenumber = $row4['invoiceid'];
$subtotal = $row4['subtotal'];
$total = $row4['total'];
$customerid = $row4['accountid'];
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
	$fullname = "No Vendor Selected";
}

$fullname = substr($fullname,0,25);
echo "\n<tr id=\"highlight".$i."\"><form name=\"form".$invoiceid."\" id=\"form".$invoiceid."\" action=\"vendorinvoice.php\" method=\"post\"><td><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\" />";
echo "<input type=\"submit\" name=\"submit\" class=\"btn-style\" id=\"".$invoiceid."\" value=\"".$invoiceid."\" />";
echo "</td><td><input type=\"submit\" name=\"submit\" class=\"btn-style\" id=\"".$invoicenumber."\" value=\"".$fullname."\" /></td><td><input type=\"submit\" name=\"submit\" class=\"btn-style\" id=\"".$invoicenumber."\" value=\"$".$total."\" /></td></form></tr>";
$i ++;
}
?>
</table>
</div>
<div class="right1">
<table><tr><td>
<a href="vendors.php?invoice=5" class="no-decoration"><input type="button" class="quotebutton" value="Create Vendor Order"></a></td>
<td>
<a href="vendors.php?invoice=8" class="no-decoration"><input type="button" class="quotebutton" value="Create Vendor Bill"></a>
</td></tr>
<tr><td><br />
<a href="vendors.php?invoice=13" class="no-decoration"><input type="button" class="quotebutton" value="Create Vendor Adjustment"></a>
</td>
<td><br />
<a href="vendors.php?invoice=14" class="no-decoration"><input type="button" class="quotebutton" value="Create Vendor RMA"></a>
</td>
</tr>
</table></div>
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