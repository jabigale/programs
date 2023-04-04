<?php
//submit insert new transaction
//include mysql file
//get tax multiply
//record the inventory transaction in inventory_transactions
//roundtax
//package/service submit
//update invoice w/ total

include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Print';
$linkpage = 'printquickquote.php';
$currentday = date('Y-n-j');
$currentday2 = date('Y-m-d');
$invoicesubtotal = '0';
$invtable = 'invoice';

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


$sql1 = "SELECT `variable` FROM `global_settings` WHERE `id` = '7'";
$sth1 = $pdocxn->prepare($sql1);
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
	$footertag = $row1['variable'];
}

if(isset($_GET['accountid']))
{
$accountid = $_GET['accountid'];
$qty = $_GET['qty'];
$partid = $_GET['partid'];
$packacgeid = $_GET['pid'];
$addpackage = $_GET['pid2'];
$typeid = '4';

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
$taxgroup = '1';
}
//get tax multiply
$gettax = $pdocxn->prepare('SELECT `id`,`multiply`,`description` FROM `tax_rate` WHERE `id` = :taxgroup');
$gettax->bindparam(':taxgroup',$taxgroup);
$gettax->execute();
while($gettaxraterow = $gettax->fetch(PDO::FETCH_ASSOC))
{
$taxmultiply = $gettaxraterow['multiply'];
$taxdescription = $gettaxraterow['description'];
}
if(isset($_GET['vehicleid']))
{$vehicleid = $_GET['vehicleid'];

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
}else{
$abvvehicle = $getvehiclerow['description'];
}
if($abvvehicle < '1')
{
$abvvehicle = '';
}}
}else{
$vehicleid = '';
$abvvehicle = '';
}
$newlinenumber = '1';

$sth1 = $pdocxn->prepare('SELECT `id` FROM `'.$invtable.'` ORDER BY `id` DESC LIMIT 1');
$sth1->execute();
$row1 = $sth1->fetch(PDO::FETCH_ASSOC);
$lastinvid = $row1['id'];
$invoiceid = $lastinvid + '1';

$sth2 = $pdocxn->prepare('INSERT INTO `'.$invtable.'`(`id`,`userid`,`type`,`location`,`creationdate`,`invoicedate`,`taxgroup`,`accountid`,`vehicleid`,`abvvehicle`,`abvname`) VALUES (:id,:userid,:typeid,:location,:creationdate,:invoicedate,:taxgroup,:accountid,:vehicleid,:abvvehicle,:abvname)');
$sth2->bindParam(':id',$invoiceid);
$sth2->bindParam(':userid',$currentid);
$sth2->bindParam(':typeid',$typeid);
$sth2->bindParam(':location',$currentlocationid);
$sth2->bindParam(':creationdate',$currentdate);
$sth2->bindParam(':invoicedate',$currentday);
$sth2->bindParam(':taxgroup',$taxgroup);
$sth2->bindParam(':accountid',$accountid);
$sth2->bindParam(':vehicleid',$vehicleid);
$sth2->bindParam(':abvvehicle',$abvvehicle);
$sth2->bindParam(':abvname',$abvname);
$sth2->execute()or die(print_r($sth2->errorInfo(), true));

if($fet < '.001')
{
	$fet = '0';
}
if($invoiceid < '1')
{
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

if($typeid == '1')
{
$locationcolumn = "loc".$currentlocationid."_onhand";
$sth1 = $pdocxn->prepare('UPDATE `inventory` SET `'.$locationcolumn.'`=`'.$locationcolumn.'` - :qty WHERE `id` = :partid');
$sth1->bindParam(':qty',$qty);
$sth1->bindParam(':partid',$partid);
$sth1->execute();
$sth2 = $pdocxn->prepare('UPDATE `'.$invtable.'` SET `tire`=\'1\' WHERE `id` = :invoiceid');
$sth2->bindParam(':invoiceid',$invoiceid);
$sth2->execute();
$record = '1';
}else
{
	$record = '0';
}

$record = '0';
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
$lineitem_typeid = '1';
$lineitem_saletype = '1';

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
$newlinenumber ++;
$lastinsertid1 = $pdocxn->lastInsertId();

$taxamount = $totallineamount*$taxmultiply;
//roundtax
$tabletaxamount = round($taxamount,2);
$sql5 = "INSERT INTO `tax_trans`(`transid`,`taxamount`,`lineid`) VALUES (:inv,:taxamount,:lineid)";
$sth5 = $pdocxn->prepare($sql5);
$sth5->bindParam(':inv',$invoiceid);
$sth5->bindParam(':taxamount',$tabletaxamount);
$sth5->bindParam(':lineid',$lastinsertid1);
$sth5->execute()or die(print_r($sth5->errorInfo(), true));


$sth5 = $pdocxn->prepare('INSERT INTO `inventory_transactions` (`invoiceid`,`qty`,`amount`,`partid`,`transactiontype`,`location`,`accountid`,`record`,`lineid`)VALUES(:invoiceid,:qty,:amount,:partid,:transactiontype,:location,:accountid,:record,:lineid)');
$sth5->bindParam(':invoiceid',$invoiceid);
$sth5->bindParam(':qty',$qty);
$sth5->bindParam(':amount',$price);
$sth5->bindParam(':partid',$partid);
$sth5->bindParam(':transactiontype',$typeid);
$sth5->bindParam(':location',$currentlocationid);
$sth5->bindParam(':accountid',$accountid);
$sth5->bindParam(':record',$record);
$sth5->bindParam(':lineid',$lastinsertid1);
$sth5->execute()or die(print_r($sth5->errorInfo(), true));


//package/service submit
$sth4 = $pdocxn->prepare('SELECT * FROM `packages` WHERE `quickprint` = :quickprintid');
$sth4->bindParam(':quickprintid',$qty);
$sth4->execute();
while($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
{
$packageid = $row4['id'];
$packagetitle = $row4['description'];
$tpkcost1 = $row4['price1'];
$tpkcost2 = $row4['price2'];
$tpkcost3 = $row4['price3'];
$tpkcost4 = $row4['price4'];
$tpkcost5 = $row4['price5'];
$tpkcost6 = $row4['price6'];
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
$linesth2 = $pdocxn->prepare('INSERT INTO `line_items`(`invoiceid`,`qty`,`amount`,`linenumber`,`comment`,`totallineamount`,`lineitem_typeid`,`lineitem_subtypeid`,`lineitem_saletype`)VALUES(:invoiceid,\'1\',:amount,:linenumber,:comment,:totallineamount,:lineitem_typeid,:lineitem_subtypeid,:lineitem_saletype)');
$linesth2->bindParam(':invoiceid',$invoiceid);
$linesth2->bindParam(':totallineamount',$lr1cost);
$linesth2->bindParam(':amount',$lr1cost);
$linesth2->bindParam(':comment',$packagetitle);
$linesth2->bindParam(':linenumber',$newlinenumber);
$linesth2->bindParam(':lineitem_typeid',$lineitem_typeid);
$linesth2->bindParam(':lineitem_subtypeid',$lineitem_subtypeid);
$linesth2->bindParam(':lineitem_saletype',$lineitem_saletype);
$linesth2->execute();

$newlinenumber ++;
//addlinenumber
$lastinsertid1 = $pdocxn->lastInsertId();

$taxamount = $lr1cost*$taxmultiply;
//roundtax
$tabletaxamount = round($taxamount,2);
$sql5 = "INSERT INTO `tax_trans`(`transid`,`taxamount`,`lineid`) VALUES (:inv,:taxamount,:lineid)";
$sth5 = $pdocxn->prepare($sql5);
$sth5->bindParam(':inv',$invoiceid);
$sth5->bindParam(':taxamount',$tabletaxamount);
$sth5->bindParam(':lineid',$lastinsertid1);
$sth5->execute()or die(print_r($sth5->errorInfo(), true));


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
$linesth2 = $pdocxn->prepare('INSERT INTO `line_items`(`invoiceid`,`qty`,`amount`,`linenumber`,`comment`,`totallineamount`,`lineitem_typeid`,`lineitem_subtypeid`,`lineitem_saletype`)VALUES(:invoiceid,:qty,:amount,:linenumber,:comment,:totallineamount,:lineitem_typeid,:lineitem_subtypeid,:lineitem_saletype)');
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
$newlinenumber ++;
$lineid = $pdocxn->lastInsertId();
//addlinenumber

$taxamount = $totallineamount*$taxmultiply;
//roundtax
$tabletaxamount = round($taxamount,2);
$sql5 = "INSERT INTO `tax_trans`(`transid`,`taxamount`,`lineid`) VALUES (:inv,:taxamount,:lineid)";
$sth5 = $pdocxn->prepare($sql5);
$sth5->bindParam(':inv',$invoiceid);
$sth5->bindParam(':taxamount',$tabletaxamount);
$sth5->bindParam(':lineid',$lineid);
$sth5->execute()or die(print_r($sth5->errorInfo(), true));
}

//update invoice w/ total

$sth4 = $pdocxn->prepare('SELECT SUM(`totallineamount`) AS `invsubtotal` FROM `line_items` WHERE `invoiceid` = :invoiceid');
$sth4->bindParam(':invoiceid',$invoiceid);
$sth4->execute();
$linecount = $sth4->rowCount();
	while($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
{
$invoicesubtotal = $row4['invsubtotal'];
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
$dtaxtotal22 = money_format('%(#0.2n',$taxamount);
$dsubtotal = money_format('%(#0.2n',$invoicesubtotal);
$dinvoicetotal22 = money_format('%(#0.2n',$invoicetotal);

$sth1 = $pdocxn->prepare('UPDATE `invoice` SET `subtotal`=:subtotal,`tax`=:tax,`total`=:total WHERE `id` = :invoiceid');
$sth1->bindParam(':subtotal',$invoicesubtotal);
$sth1->bindParam(':tax',$taxamount);
$sth1->bindParam(':total',$invoicetotal);
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->execute();




$sth3 = $pdocxn->prepare('SELECT * FROM `invoice` WHERE `id` = :inv');
$sth3->bindParam(':inv',$invoiceid);
$sth3->execute();
while($row3 = $sth3->fetch(PDO::FETCH_ASSOC))
{
$accountid = $row3['accountid'];
$invoicenumber = $row3['invoiceid'];
$typeid = $row3['type'];
$userid = $row3['userid'];
$vehicleid = $row3['vehicleid'];
$location = $row3['location'];
$taxtotal1 = $row3['tax'];
$subtotal1 = $row3['subtotal'];
$ponumber = $row3['ponumber'];
$total1 = $row3['total'];

$dtaxtotal1 = money_format('%(#0.2n',$taxtotal1);
$dsubtotal1 = money_format('%(#0.2n',$subtotal1);
$dtotal1 = money_format('%(#0.2n',$total1);
$invoicedate = $row3['invoicedate'];
$invoicedate2 = new DateTime($invoicedate);
$displaydate = $invoicedate2->format('M j, Y');
$duedatedisplay = $invoicedate2->format('M j, Y');
}
if($userid > '0')
$sth5 = $pdocxn->prepare('SELECT `username` FROM `employees` WHERE `id` = :userid');
$sth5->bindParam(':userid',$userid);
$sth5->execute();
while($row5 = $sth5->fetch(PDO::FETCH_ASSOC))
{
$salesperson = $row5['username'];
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
$address1 = $row4['address'];
$address2 = $row4['address2'];
$city = $row4['city'];
$state = $row4['state'];
$zip = $row4['zip'];
$citystatezip = $city.", ".$state." ".$zip;
$phone1 = $row4['phone1'];
$phone2 = $row4['phone2'];
$phone3 = $row4['phone3'];
$phone4 = $row4['phone4'];
$contact1 = $row4['contact1'];
$contact2 = $row4['contact2'];
$contact3 = $row4['contact3'];
$contact4 = $row4['contact4']; 
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
$dtaxclass = "Consumer";
$dlastactivedate = "12/20/2017";
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
	$submodel = $row5['submodel'];
	$engine = $row5['engine'];
	$license = $row5['license'];
	$vehiclestate = $row5['state'];
	$description = $row5['description'];
if($year > '1')
{
$dvehicleinfo = "\n<b>".$year." ".$make." ".$model." ".$submodel." ".$engine."</b><br>VIN: ".$vin."<br>License: ".$license." (".$vehiclestate.")<br>Mileage: ".$mileagein;
}
else
{
$dvehicleinfo = "\n<b>".$description."</b><br>VIN: ".$vin."<br>License: ".$license." (".$vehiclestate.")<br>Mileage: ";
}}}}else{
		$dvehicleinfo = '';
	}
$sql6 = 'SELECT `name` FROM `invoice_type` WHERE `id` = :type';
$sth6 = $pdocxn->prepare($sql6);
$sth6->bindParam(':type',$typeid);
$sth6->execute();
while($row6 = $sth6->fetch(PDO::FETCH_ASSOC))
{
	$typename = $row6['name'];
}

$sth8 = $pdocxn->prepare('SELECT * FROM `locations` WHERE `id` = :locationid');
$sth8->bindParam(':locationid',$location);
$sth8->execute();
while($row8 = $sth8->fetch(PDO::FETCH_ASSOC))
{
$displaystorename = $row8['displaystorename'];
$storeaddress1 = $row8['address'];
$storecity = $row8['city'];
$storestate = $row8['state'];
$storezip = $row8['zip'];
$storeaddress2 = $storecity.", ".$storestate." ".$storezip;
$storenumber = $row8['phone'];
$storefax = $row8['fax'];
}
?>
<!doctype html>
<html>
<head>
      <link rel="stylesheet" href="style/newprint.css" />
    <meta charset="utf-8">
	<script>
	<!--
	window.onload = function() {
	window.print();
	window.close();
	}
	-->
	</script>
</head>
<body>
<div class="page-header" style="text-align: center">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                            <td class="title">
                                <img src="images/logo-small.jpg" style="width:100%; max-width:300px;">
                            </td>
                            <td class="title">
                            </td>
                            <td class="title">
                                <b><?php echo $displaystorename; ?></b><br>
                                <?php echo $storeaddress1; ?><br>
                                <?php echo $storeaddress2; ?><br>
                                Phone: <?php echo $storenumber; ?><br>
                                Fax: <?php echo $storefax; ?><br>
                            </td>
                        </tr></tbody>
</table></div>

  <div class="page-footer">
  <?php 
  echo $footertag;
  ?>
  </div>
<table cellpadding="0" cellspacing="0" width="100%">
<thead>
      <tr>
        <td>
          <!--place holder for the fixed-position header-->
          <div class="page-header-space"></div>
        </td>
	  </tr></thead>
    <tbody>

<tr><td><div class="page" >
<table class="infotable">
<tr class="information">
                            <td class="tdborder" width="33%">
                                <b><?php echo $fullname; ?></b><br>
                                <?php echo $address1; ?><br>
                                <?php echo $citystatezip; ?><br>
                                <?php echo $phone1; ?><br>
                                <?php echo $phone2; ?><br>
                            </td>
                            <td class="tdborder" width="33%">
                            	<b>Quote #: <?php echo $invoiceid; ?></b><br>
                                PO #: <?php if($ponumber > '0'){echo $ponumber; }?><br>
								Date: <?php echo $displaydate; ?><br>
                                Salesperson: <?php echo $salesperson; ?>
                            </td>
                            <td class="tdborderright" width="33%"><?php echo $dvehicleinfo; ?></td>  
                        </tr><tr>
<td class="notesborder" colspan="3"><?php
echo $displaynote;
?></td></tr>
            </table><table cellpadding="0" cellspacing="0">
            <tr style="outline: thin solid">
            	<td width="5%" class="center">
                    Qty
                </td>
                <td colspan="2" width="70%">
                    Description
                </td>
                <td width="12%" class="right">
                    Unit Price
                </td>
                <td width="13%" class="right">
                    Ext Price
                </td>
			</tr>

<?php
$sth4 = $pdocxn->prepare('SELECT * FROM `line_items` WHERE invoiceid = :inv ORDER BY linenumber ASC');
$sth4->bindParam(':inv',$invoiceid);
$sth4->execute();
$linecount = $sth4->rowCount();
	while($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
{
$lineid = $row4['id'];
$invqty = $row4['qty'];
$totallineamount = $row4['totallineamount'];
$invpartid = $row4['partid'];
$invpackageid = $row4['packageid'];
$invserviceid = $row4['serviceid'];
$invcomment = $row4['comment'];
$fet = $row4['fet'];
$singleprice = $totallineamount / $invqty;
$dextprice = money_format('%(#0.2n',$totallineamount);
$dsingleprice = number_format($singleprice, 2);
$linenumber = $row4['linenumber'];
$invoicesubtotal = $invoicesubtotal+$totallineamount;
if($invqty == '0')
{
 $invqty = '';
 $dextprice = '';
 $dsingleprice = '';
}
echo "<tr class=\"item\"><td  class=\"center\">".$invqty."</td><td class=\"left\" colspan=\"2\">".$invcomment."</td><td class=\"right\">".$dsingleprice."</td><td class=\"right\">".$dextprice."</td></tr>\n";
}
?>
<tr class="total">
<td colspan="2" class="right"><br /></td>
<td colspan="2" class="right"><br /></td>
<td class="right"><br /></td></tr>
            <tr class="heading">
                <td colspan="4" class="boldright">
<br /></td><td class="boldright"></td>
            </tr>

<tr class="total">
<td colspan="2" class="right">
                   Tax:  <?php echo $taxdescription."  ".$dtaxtotal1; ?></td>
                <td colspan="2" class="right">
                   Subtotal:</td><td class="right"><?php echo $dsubtotal; ?>
                </td>
            </tr>
            <tr class="heading">
                <td colspan="4" class="boldright">
                   Total:</td><td class="boldright"><?php echo $dtotal1; ?>
                </td>
            </tr>
</table></div></td></tr>
        </tbody>

<tfoot><tr><td><br /><br />
    <div class="footer-space">&nbsp;</div>
  </td></tr></tfoot>
        </table>
</body>
</html>
<?php
}
else
{
echo "test";
}
?>