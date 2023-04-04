<?php
/*
**navigation
//general submit form
//edit void invoice
//edit change date
//edit change customer
//edit delete line
//edit line move
//edit line item
//get tax info
//submit insert new transactions
//inventory submit
//quick add
//display html - no submit
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
$title = 'Receive Inventory';
$linkpage = 'recinv.php';
$changecustomer = '0';

$invtable = 'invoice';
$invlinetable ='line_items';
$typeid = '2';
$sort = '0';
$split = '0';
$currentdate = date('Y-n-j H:i:s');
$currentday = date('Y-n-j');
$currentday2 = date('Y-m-d');
$quicksearch = '0';
$invoicesubtotal = '0';
$accountid = '0';
$changetax = '0';

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


if($_POST['submit'] OR $_GET['vendor'] OR $_GET['new'] OR $_GET['accountid'] OR $_GET['rec'] OR $_GET['invoiceid'] OR $_POST['invoiceid'] OR $_POST['void'])
{

//general submit form
if($_GET['invoiceid'])
{ $invoiceid = $_GET['invoiceid'];}
if($_POST['invoiceid'])
{ $invoiceid = $_POST['invoiceid'];}

if(isset($_GET['new']))
{$new = '1';}
if(isset($_GET['rec'])&& $_GET['rec']=='1')
{$new = '1';}
if($new == '1')
{
$typeid = '2';
global $changetax;
$changetax = '1';
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
if($firstname > '0')
{
$abvname = $firstname." ".$lastname;
}}}else{
$accountid = '0';
$abvname = '0';
$taxgroup = '7';
}

$newlinenumber = '1';
$sth1 = $pdocxn->prepare('SELECT `id` FROM `'.$invtable.'` ORDER BY `id` DESC LIMIT 1');
$sth1->execute();
$row1 = $sth1->fetch(PDO::FETCH_ASSOC);
$lastinvid = $row1['id'];
$invoiceid = $lastinvid + '1';

$sth2 = $pdocxn->prepare('INSERT INTO `'.$invtable.'`(`id`,`userid`,`type`,`location`,`creationdate`,`invoicedate`,`taxgroup`,`accountid`,`abvname`,`vendor_invoice`) VALUES (:id,:userid,:typeid,:location,:creationdate,:invoicedate,:taxgroup,:accountid,:abvname,\'1\')');
$sth2->bindParam(':id',$invoiceid);
$sth2->bindParam(':userid',$currentid);
$sth2->bindParam(':typeid',$typeid);
$sth2->bindParam(':location',$currentlocationid);
$sth2->bindParam(':creationdate',$currentdate);
$sth2->bindParam(':invoicedate',$currentday);
$sth2->bindParam(':taxgroup',$taxgroup);
$sth2->bindParam(':accountid',$accountid);
$sth2->bindParam(':abvname',$abvname);
$sth2->execute()or die(print_r($sth2->errorInfo(), true));
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




if($_POST['void']&&$_POST['void']=='1')
{
//edit void invoice
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
$sth1 = $pdocxn->prepare('UPDATE `inventory` SET `'.$locationcolumn.'`=`'.$locationcolumn.'` - :qty WHERE `id` = :partid');
$sth1->bindParam(':qty',$qty);
$sth1->bindParam(':partid',$partid);
$sth1->execute();
}
}
$delinv = $pdocxn->prepare('DELETE FROM  `inventory_transactions` WHERE `invoiceid` = :invoiceid');
$delinv->bindParam(':invoiceid',$invoiceid);
$delinv->execute();

$sth1 = $pdocxn->prepare('UPDATE `'.$invtable.'` SET `voiddate`=:voiddate WHERE `id` = :invoiceid');
$sth1->bindParam(':voiddate',$currentday);
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->execute();

header('location:'.$linkpage.'');
exit();
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
if($_POST['changecustomer'] OR $_GET['changecustomer']){
	global $changetax;
$changetax = '1';
//edit change customer
if($_POST['changecustomer']){
	$newaccountid = $_POST['accountid'];
}
if($_GET['changecustomer']){
	$newaccountid = $_GET['accountid'];
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
	$sth1 = $pdocxn->prepare('UPDATE `'.$invtable.'` SET `accountid`=:accountid,`abvname`=:abvname,`vehicleid`=\'0\',`abvvehicle`=\'0\',`taxgroup`=:taxgroup WHERE `id` = :invoiceid');
	$sth1->bindParam(':accountid',$newaccountid);
	$sth1->bindParam(':invoiceid',$invoiceid);
	$sth1->bindParam(':abvname',$abvname);
	$sth1->bindParam(':taxgroup',$taxclass);
	$sth1->execute()or die(print_r($sth1->errorInfo(), true));
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

if($partid > '0')
{
$locationcolumn = "loc".$currentlocationid."_onhand";
$sth1 = $pdocxn->prepare('UPDATE `inventory` SET `'.$locationcolumn.'`=`'.$locationcolumn.'` - :qty WHERE `id` = :partid');
$sth1->bindParam(':qty',$qty);
$sth1->bindParam(':partid',$partid);
$sth1->execute();
}
}
$deletedlinenumber = $_POST['deletedlinenumber'];
$sth1 = $pdocxn->prepare('DELETE FROM `'.$invlinetable.'` WHERE `id` = :lineid');
$sth1->bindParam(':lineid',$lineid);
$sth1->execute();

$sth2 = $pdocxn->prepare('UPDATE `'.$invlinetable.'` SET `linenumber` = `linenumber` - 1 WHERE `invoiceid` = :invoiceid AND `linenumber` > :deletedlinenumber');
$sth2->bindParam(':invoiceid',$invoiceid);
$sth2->bindParam(':deletedlinenumber',$deletedlinenumber);
$sth2->execute();
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
$sth1 = $pdocxn->prepare('UPDATE `'.$invlinetable.'` SET `qty`=:qty,`amount`=:amount,`totallineamount`=:totallineamount WHERE `id` = :lineid');
$sth1->bindParam(':totallineamount',$totallineamount);
$sth1->bindParam(':qty',$qty);
$sth1->bindParam(':amount',$amount);
$sth1->bindParam(':lineid',$lineid);
$sth1->execute();

}

if($_POST['inventorysubmit']&&$_POST['inventorysubmit'] == '1')
{
$typeid = '2';
//inventory submit
$partid = $_POST['partid'];
$qty = $_POST['qty'];
$price = $_POST['price'];
$fet = $_POST['fet'];
$record = '1';
if(isset($_POST['accountid']))
{
	$accountid = $_POST['accountid'];
}else{
	$accountid = '0';
}
$sth1 = $pdocxn->prepare('UPDATE `inventory_price` SET `lastcost`=:lastcost,`baseamount`=:baseamount WHERE `partid` = :partid');
$sth1->bindParam(':lastcost',$price);
$sth1->bindParam(':baseamount',$price);
$sth1->bindParam(':partid',$partid);
$sth1->execute();

if($invoiceid < '1')
{
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
$newlinenumber = '1';
}else{

	$actsql1 = 'SELECT `accountid` FROM `'.$invtable.'` WHERE `id` = :invoiceid';
	$actsth1 = $pdocxn->prepare($actsql1);
	$actsth1->bindParam(':invoiceid',$invoiceid);
	$actsth1->execute();
		while($actrow1 = $actsth1->fetch(PDO::FETCH_ASSOC))
		{
		$accountid = $actrow1['accountid'];
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

$locationcolumn = "loc".$currentlocationid."_onhand";
$sth1 = $pdocxn->prepare('UPDATE `inventory` SET `'.$locationcolumn.'`=`'.$locationcolumn.'` + :qty WHERE `id` = :partid');
$sth1->bindParam(':qty',$qty);
$sth1->bindParam(':partid',$partid);
$sth1->execute();

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
		$displayply = "(".$ply." ply)";
	}

$sql2= "SELECT `brand` FROM `tire_manufacturers` WHERE `id` = '$brandid'";
$query2 = mysqli_query($sqlicxn,$sql2);
while ($row2 = mysqli_fetch_assoc($query2))
	{
	$brand = $row2['brand'];
	$description = $articleid.", ".$size." ".$brand." ".$model." ".$load_index.$speed." ".$displayply;
	}
	}}


$lineitem_typeid = '1';
$lineitem_saletype = '9';

$totallineamount = $qty * $price;
$sth3 = $pdocxn->prepare('INSERT INTO `line_items`(`invoiceid`,`qty`,`amount`,`linenumber`,`comment`,`fet`,`totallineamount`,`partid`,`lineitem_typeid`,`lineitem_subtypeid`,`lineitem_saletype`)VALUES(:invoiceid,:qty,:amount,:linenumber,:comment,:fet,:totallineamount,:partid,:lineitem_typeid,:lineitem_subtypeid,:lineitem_saletype)');
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
$lastinsertid1 = $pdocxn->lastInsertId();
//lastinsertid

$sth5 = $pdocxn->prepare('INSERT INTO `inventory_transactions` (`invoiceid`,`qty`,`amount`,`partid`,`transactiontype`,`location`,`accountid`,`record`,`lineid`)VALUES(:invoiceid,:qty,:amount,:partid,:transactiontype,:siteid,:accountid,:record,:lineid)');
$sth5->bindParam(':invoiceid',$invoiceid);
$sth5->bindParam(':qty',$qty);
$sth5->bindParam(':amount',$price);
$sth5->bindParam(':partid',$partid);
$sth5->bindParam(':transactiontype',$typeid);
$sth5->bindParam(':siteid',$currentlocationid);
$sth5->bindParam(':accountid',$accountid);
$sth5->bindParam(':record',$record);
$sth5->bindParam(':lineid',$lastinsertid1);
$sth5->execute()or die(print_r($sth5->errorInfo(), true));

}

if(!isset($_GET['invoiceid']))
{
	header('location:'.$linkpage.'?invoiceid='.$invoiceid.'');
}

$sth3 = $pdocxn->prepare('SELECT `accountid`,`userid`,`location`,`invoiceid`,`taxgroup`,`invoicedate` FROM `'.$invtable.'` WHERE `id` = :inv');
$sth3->bindParam(':inv',$invoiceid);
$sth3->execute();
$row3 = $sth3->fetch(PDO::FETCH_ASSOC);
$accountid = $row3['accountid'];
$invuserid = $row3['userid'];
$location = $row3['location'];
$invoicenumber = $row3['invoiceid'];
$taxgroup = $row3['taxgroup'];
$invoicedate = $row3['invoicedate'];
$invoicedate2 = new DateTime($invoicedate);
$displayinvoicedate = $invoicedate2->format('n/j/Y');
$displayinvoicedate2 = $invoicedate2->format('Y-m-d');


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
<script src="http://code.jquery.com/jquery-1.10.2.js"></script>
  <script src="http://code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" >
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script type="text/javascript" src="scripts/script.js"></script>

<script src="scripts/autocomplete.js" type="text/javascript"></script>
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
<div class="headercenter">
<table class="headertable"><tr><td>
<form action="<?php echo $linkpage ;?>" method="post" name="voidinvoice">
<input type="hidden" name="void" value="1">
<input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>">
<input type="hidden" name="submit" value="1">
<input type="submit" class="cancel" value="Void Vendor Invoice" onclick="myFunction()"></form>
</td><td>
<input type="button" class="save" value="Save" onclick="self.close()"></td>
<td><form action="printinvoice.php" method="post" name="printinvoice">
<input type="hidden" name="copies" value="2">
<input type="hidden" name="print" value="1">
<input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>">
<input type="hidden" name="submit" value="1">
<input type="submit" class="save" alt="print" value="Print Vendor Invoice" name="submit"></form></td>

</tr></table></div></div>
<div id="selecteduser">
<form name="changesales" action="<?php echo $linkpage; ?>" method="POST">
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
</td><td>Vendor Invoice <b>#<?php echo $invoiceid; ?></b></td><td href="invoicedate">Date: <b><?php echo $displayinvoicedate; ?></b></td><td>PO Number: <?php if($ponumber > '0') { ?><input type="textbox" name="ponumber" value="<?php echo $ponumber; ?>" size="7"><?php } else { ?><input type="textbox" name="ponumber" placeholder="PO Number" size="7"><?php }?></td><td>Tax: 

<?php
echo "<form name=\"taxclassform\" action=\"".$linkpage."\" method=\"POST\">";
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
</td></tr></table></div>
<div id="content">
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
echo "<table><tr><td><a href=\"account.php?changecustomer=1&vinvoiceid=".$invoiceid."\" class=\"no-decoration\"><input type=\"button\" alt=\"Select Vendor\" class=\"quotebutton\" value=\"Select Vendor\" /></a></td></tr></table>";
}
?>
<?php
if($split == '1')
{
echo "<form name=\"splitform\" action=\"".$linkpage."\" method=\"POST\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"submit\" value=\"1\"><input type=\"hidden\" name=\"split\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"split1\" value=\"1\">";
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
$sbi = '1';
$tri = '1';
$tr2 = '1';
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

		echo '<li id="'.$lineid.'"><table class="invtable"><tr href="'.$tri.'" id="'.$qri.'row"><td width="35">'.$invqty.'</td><td>'.$invcomment.'</td><td width="40">'.$fet.'</td><td width="55">'.$invamount.'</td><td width="55">'.$dextprice.'</td></tr></table></li>';

${"ip".$tri} = "\n<div id=\"$tri\"><div class=\"q1\"><form name=\"updatecomment\" method=\"post\" action=\"".$linkpage."\"><table class=\"righttable\"><tr>";

if($lineitemtype == '1')
{
${"ip".$tri} .= "<td colspan=\"3\" class=\"center\"><b>".$invcomment."</b><input type=\"hidden\" name=\"comment\" value=\"".$invcomment."\"></td>";
}else{
${"ip".$tri} .= "<td colspan=\"3\" class=\"left\"><textarea class=\"wideinput\" name=\"comment\" autocomplete=\"off\" id=\"".$qri."box\">".$invcomment."</textarea></td>";
}

${"ip".$tri} .= "</tr><tr><td class=\"left\">Unit Price: <input type=\"text\" class=\"narrowinput\" name=\"price\" value=\"$unitprice\"  id=\"li".$tr2."\" autocomplete=\"off\"><input type=\"button\" id=\"multiply1".$tr2."\" data-quantity=\"1.5\" value=\"x1.5\" class=\"xsmallbutton\"><input type=\"button\" id=\"multiply2".$tr2."\" data-quantity=\"2\" value=\"x2\" class=\"xsmallbutton\"></td><td class=\"left\">QTY:<input type=\"number\" class=\"narrowinput\" name=\"qty\" value=\"".$invqty."\" step=\"any\" autocomplete=\"off\"></td><td class=\"center\">FET: <input type=\"text\" class=\"narrowinput\" name=\"newfet\" value=\"$fet\" autocomplete=\"off\"></td></tr><tr><td class=\"center\" colspan=\"2\"><input type=\"hidden\" name=\"editsubmit\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"lineid\" value=\"".$lineid."\"><input type=\"hidden\" name=\"submit\" value=\"submit\"><input type=\"submit\" class=\"xsmallbutton\" alt=\"down\" value=\"Update\"></form></td><td><form action=\"".$linkpage."\" method=\"post\" name=\"deletelinenumber\"><input type=\"hidden\" name=\"delete\" value=\"1\"><input type=\"hidden\" name=\"deletedlinenumber\" value=\"".$linenumber."\"><input type=\"hidden\" name=\"lineid\" value=\"".$lineid."\"><input type=\"hidden\" name=\"partid\" value=\"$invpartid\"><input type=\"hidden\" name=\"qty\" value=\"".$invqty."\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"submit\" value=\"submit\"><input type=\"submit\" class=\"cancel-small\" alt=\"down\" value=\"Delete Item\"></form></td></tr></table></form><table width=\"10%\"><tr>".$displayup."<td></td></tr><tr><td>".$displaydown."</td></tr></table></div></div>\n";

$tri ++;
$tr2 ++;
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

$sql3 = "SELECT SUM(`taxamount`) AS `taxtotal` FROM `tax_trans` WHERE `transid` = :invoiceid";
$sth3 = $pdocxn->prepare($sql3);
$sth3->bindParam(':invoiceid',$invoiceid);
$sth3->execute();
while($row3 = $sth3->fetch(PDO::FETCH_ASSOC))
{
	$taxamount = $row3['taxtotal'];
}
$invoicetotal = $invoicesubtotal + $taxamount;
$invoiceformtotal = round($invoicetotal,2);
$dtaxtotal = money_format('%(#0.2n',$taxamount);
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



?>
</tbody></table></form></div>
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
echo "<a href=\"editaccount.php?accountid=".$accountid."&invoiceid=".$invoiceid."\">";
echo "<input type=\"button\" alt=\"edit customer information\" class=\"cancel-small\" value=\"Edit Customer Information\" /></a></td>";
echo "</tr><tr><td colspan=\"2\" class=\"left\">".$daddress."</td></tr><tr><td>Phone 1:</td><td>".$phone1."</td></tr><tr><td>Phone 2:</td><td class=\"center\">".$phone2."</td></tr><tr><td colspan=\"2\" class=\"center\"></td></tr><tr><td colspan=\"2\" class=\"center\"><a href=\"account.php?changecustomer=1&invoiceid=".$invoiceid."\" class=\"no-decoration\"><input type=\"button\" alt=\"Change Customer\" class=\"cancel-small\" value=\"Change Customer\" /></a></td></tr></table></div></div>";
echo "\n<div id=\"invoicedate\"><div class=\"q1\"><form name=\"newdateform\" action=\"".$linkpage."\" method=\"post\"><table class=\"righttable\"><tr><td colspan=\"2\" class=\"center\">Change Invoice Date</td></tr><tr><td colspan=\"2\" class=\"center\"><input type=\"date\" name=\"newdate\" value=\"".$displayinvoicedate2."\"></td></tr><tr><td colspan=\"2\" class=\"center\"><input type=\"hidden\" name=\"changedate\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"submit\" class=\"btn-style\" name=\"submit\" value=\"Update Invoice Date\"></td></tr></table></form></div></div>";
echo "\n<div id=\"vehicleinfo\"><div class=\"q1\"><table class=\"righttable\"><tr><td colspan=\"2\" class=\"left\">".$dvehicleinfo2."</td></tr><tr><td class=\"left\" colspan=\"2\">VIN:&nbsp;&nbsp;&nbsp;&nbsp;".$currentvin."</td></tr><tr><td class=\"left\">License:&nbsp;&nbsp;&nbsp;&nbsp;".$license."</td><td class=\"left\">State:   ".$vehiclestate."</td></tr><tr href=\"editvehicleinfo\" colslpan=\"2\"><td><input type=\"button\" name=\"editvehicle\" class=\"btn-style\" value=\"Edit Vehicle\" /></td><td><form name=\"addvehicle\" action=\"vehicles.php\" method=\"post\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"submit\" name=\"addvehicle\" class=\"btn-style\" value=\"Add Vehicle\" /></form></td></tr><tr><td colspan=\"3\" class=\"center\"></td></tr></table></form><table><tr><td colspan=\"2\"><div class=\"styled-select2 black rounded\">\n<form name=\"changevehicle\" id=\"form\" action=\"".$linkpage."\" method=\"post\"><select name=\"vehiclechange\" onchange=\"form.submit()\">";
if($vehicleid> '0')
	{
		echo "<option value=\"$vehicleid\">".$dvehicleinfo2nb."</option>";
	}
else {
echo "<option value=\"0\"></option>";
}


echo "</select><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"changevehicle\" value=\"1\"><input type=\"hidden\" name=\"form\" value=\"1\"></form></td></tr></table>";

echo "</div></div>";
echo "\n<div id=\"editvehicleinfo\"><div class=\"q1\"><form name=\"changevehicle\" method=\"post\" action=\"".$linkpage."\"><table class=\"righttable\"><tr><td class=\"left\">Vehicle Info:</td><td class=\"left\" colspan=\"2\"><input type=\"hidden\" name=\"editvehicle1\" value=\"1\"><input type=\"hidden\" name=\"vehicleid\" value=\"".$vehicleid."\"><input type=\"text\" name=\"vehiclename\" value=\"".$dvehicleinfo2nb."\"></td></tr><tr><td class=\"left\">VIN:</td><td class=\"left\" colspan=\"2\"><input type=\"text\" name=\"vehiclevin\" value=\"".$currentvin."\"></td></tr><tr><td class=\"left\">License:</td><td class=\"left\"><input type=\"text\" name=\"license\" value=\"".$license."\" size=\"6\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;State:<input type=\"text\" name=\"state\" size=\"1\" value=\"".$vehiclestate."\"></td></tr><tr><td colspan=\"3\" class=\"center\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"editvehicle\" value=\"1\"><input type=\"hidden\" name=\"submit\" value=\"1\"><input type=\"image\" src=\"images/buttons/checkmark.png\" alt=\"editvehicle\" name=\"submit\" width=\"25\"></td></tr></table></form></div></div>";

echo "\n<div id=\"add\"><div class=\"q1\"><form name=\"addcomment\" method=\"post\" action=\"".$linkpage."\" onsubmit=\"return makeSearch()\"><table class=\"righttable\"><tr><td colspan=\"3\" class=\"left\"><textarea class=\"wideinput\" name=\"newcomment\" placeholder=\"Add Comment\" id=\"invoicecomment\"></textarea><div id=\"auto\"></div></td></tr><tr><td class=\"left\">Unit Price: <input type=\"text\" class=\"narrowinput\" id=\"unitprice\" name=\"newprice\" placeholder=\"0.00\" autocomplete=\"off\"><input type=\"button\" id=\"multiply1\" data-quantity=\"1.5\" value=\"x1.5\" class=\"xsmallbutton\"><input type=\"button\" id=\"multiply2\" data-quantity=\"2\" value=\"x2\" class=\"xsmallbutton\"></td><td class=\"center\">QTY:<input type=\"number\" class=\"narrowinput\" name=\"newqty\" value=\"1\" step=\"any\" ></td><td>FET: <input type=\"text\" class=\"narrowinput\" name=\"newfet\" placeholder=\"0.00\" autocomplete=\"off\"></td></tr><tr><td colspan=\"3\" class=\"center\" colspan=\"2\"><input type=\"hidden\" name=\"newlinenumber\" value=\"$newlinenumber\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"newcommentform\" value=\"1\"><input type=\"hidden\" name=\"submit\" value=\"1\"><input type=\"submit\" name=\"submit\" id=\"Add Item\" value=\"Add\" class=\"smallbutton\"></td></tr></table></form></div></div>";
echo "\n<div id=\"note\"><div class=\"q1\"><form name=\"addnotes\" method=\"post\" action=\"".$linkpage."\"><table class=\"righttable\"><tr><td colspan=\"2\" class=\"left\"><textarea class=\"wideinput\" rows=\"5\" name=\"notes\" id=\"noteitem\">".$note."</textarea></td></tr><tr><td class=\"center\" colspan=\"2\"><input type=\"hidden\" name=\"notesubmit\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"submit\" value=\"1\"><input type=\"submit\" name=\"submit\" id=\"Add Note\" value=\"Save Note\" class=\"smallbutton\"></td></tr></table></form></div></div>";
echo "\n<div id=\"total\"><div class=\"q1\"><table class=\"righttable\"><tr><td><input type=\"submit\" class=\"smallbutton\" value=\"Change Payment\"></td><td><input type=\"submit\" class=\"cancel-small\" value=\"Void Payment\"></td></tr></table></div>";
?>
</div></div>

<div class="printdiv"><table class="righttable2"><tr>
<th colspan="2" class="center"><center>Quick Add</center></th></tr><tr><td class="center" colspan="2">
<?php
echo "<a href=\"inventory-receive.php?invoiceid=".$invoiceid."\"><input type=\"button\" class=\"smallbutton\" alt=\"Add Tire\" value=\"Add Tires\"></a></td></tr><tr>";
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
$ttdi = '1';
$sql2 = 'SELECT `partid` FROM '.$invtable.' WHERE `invoicedate` > :currenttime LIMIT 12';
$sth2 = $pdocxn->prepare($sql);
$sth2->bindParam(':currenttime',$currenttime);
$sth2->execute();
while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
{
$partid = $row2['partid'];

$sql3 = 'SELECT `partid` FROM '.$invtable.' WHERE `invoicedate` > :currenttime';
$sth3 = $pdocxn->prepare($sql);
$sth3->bindParam(':currenttime',$currenttime);
$sth3->execute();
while($row3 = $sth3->fetch(PDO::FETCH_ASSOC))
{
$partid = $row3['partid'];


















$sql1 = 'SELECT * FROM `inventory` WHERE `id` = :partid';
$sth1 = $pdocxn->prepare($sql1);
$sth1->bindParam(':partid',$partid);
$sth1->execute();
if ($sth1->rowCount() > 0)
{
	while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$brandid = $row1['manid'];
	$model = $row1['model'];
	$width = $row1['width'];
	$ratio = $row1['ratio'];
	$rim = $row1['rim'];
	$size = $width."/".$ratio." ".$rim;
	$partnumber = $row1['part_number'];
	$sw = $row1['sidewall'];
	$fet = $row1['fet'];
	$load_index = $row1['load_index'];
	$speed = $row1['speed'];
	$ply = $row1['ply'];
	if($ply > '1')
	{
		$displayply = "(".$ply." ply)";
	}
$mansql = "SELECT `brand` FROM `tire_manufacturers` WHERE `id` = '$brandid'";
$manquery = mysqli_query($sqlicxn,$mansql);
while ($mrow2 = mysqli_fetch_assoc($manquery))
	{
	$brand = $mrow2['brand'];
	$description = $articleid.", ".$size." ".$brand." ".$model." ".$load_index.$speed." ".$displayply;
	}
echo "<td class=\"center\"><form action=\"".$linkpage."\" method=\"post\" name=\"additem\">";
echo "<input type=\"hidden\" name=\"additem\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\">";
echo "<input type=\"hidden\" name=\"submit\" value=\"submit\"><input type=\"submit\" class=\"xsmallbutton\" alt=\"print\" value=\"$description\" name=\"submit\"></form></td>";
	





	}}





















$ttdi++;
}}
?>
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
    url:"scripts/sorttablescript.php?r=24567",
    method:"POST",
    data:{line_id_array:line_id_array},
   });
  }
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
<link rel="stylesheet" type="text/css" href="style/indexstyle.css" >
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
<div id="selecteduser"><form name="current1" action="<?php echo $linkpage; ?>" method="POST"><table id="floatleft"><tr><td class="currentuser">Current User:</td><td class="currentitem"><div class="styled-select black rounded"><input type="hidden" name="userform" value="1">
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
<table>
	<tr><td colspan="2" class="center"><a href="recinv.php?rec=1" class="no-decoration" target="_BLANK"><input type="button" class="quotebutton" value="Receive Inventory"></a></td>
</tr>
<tr><td colspan="2" class="center"><b>Select Vendor</b></td></tr>
<tr><td>
<?php
$vtr1 = '1';
$sql1 = 'SELECT `abvname`,`accountid` FROM `tirevendor` ORDER BY `abvname` ASC';
$sth1 = $pdocxn->prepare($sql1);
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
$accountid = $row1['accountid'];
$name = $row1['abvname'];
if($vtr1 % 2)
{
	echo "</tr><tr>";
}
echo "<td><a href=\"recinv.php?accountid=".$accountid."&new=1\" class=\"no-decoration\"><input type=\"button\" class=\"quotebutton\" alt=\"vendor\" value=\"".$name."\"></a></td>";
$vtr1 ++;
}
?>
</tr></table>
</table>


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