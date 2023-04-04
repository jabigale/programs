<?php
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Print';
$linkpage = 'printinvoice.php';
$currentday = date('Y-n-j');
$quicksearch = '0';
$location = '1';
$invoicesubtotal = '0';
$paymentid = '-1';

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
if(isset($_GET['charge']))
{
	$charge = $_GET['charge'];
	$chargelink = '&charge=1';
	}
else {
	$charge = '0';
	$chargelink ='';
}
if(isset($_POST['invoiceid']))
{
	$invoiceid = $_POST['invoiceid'];
	}
else {
	$invoiceid = '0';
if(isset($_GET['invoiceid']))
{
	$invoiceid = $_GET['invoiceid'];
	}
else {
	$invoiceid = '0';
}
}
if($invoiceid > '0')
{
if(isset($_POST['noprint']))
{
$copies = '0';
}
if(isset($_POST['print1']))
{
$copies = '1';
}
if(isset($_POST['print2']))
{
$copies = '2';
}
if(isset($_POST['print3']))
{
$copies = '3';
}
if(isset($_POST['enterpayment']))
{
$sql8 = "SELECT `location`,`invoicedate`,`accountid` FROM `invoice` WHERE `id` = :transid";
$sth8 = $pdocxn->prepare($sql8);
$sth8->bindParam(':transid',$invoiceid);
$sth8->execute();
while($row8 = $sth8->fetch(PDO::FETCH_ASSOC))
{
	$locationid = $row8['location'];
	$accountid = $row8['accountid'];
}

$sql8 = "INSERT INTO `translink` (`transid`) VALUES (:transid)";
$sth8 = $pdocxn->prepare($sql8);
$sth8->bindParam(':transid',$invoiceid);
$sth8->execute();
$sql8 = "SELECT `linkid` FROM `translink` WHERE `transid` = :transid";
$sth8 = $pdocxn->prepare($sql8);
$sth8->bindParam(':transid',$invoiceid);
$sth8->execute();
while($row8 = $sth8->fetch(PDO::FETCH_ASSOC))
{
	$linkid = $row8['linkid'];
}
$paymentdate = $_POST['paymentdate'];
$invoiceamount = $_POST['invoiceamount'];
$cash = $_POST['cash'];
$checkamount = $_POST['checkamount'];
$checknumber = $_POST['checknumber'];
$cc1amount = $_POST['cc1'];
$cc1type = $_POST['cc1type'];
$cc2amount = $_POST['cc2'];
$cc2type = $_POST['cc2type'];
$cc3amount = $_POST['cc3'];
$cc3type = $_POST['cc3type'];
$credit = '0';
$copycount = $_POST['copy'];
if($copycount == 'Save & Close')
{
	$copies = '0';
}elseif($copycount == '1 Copy')
{
	$copies = '1';
}elseif($copycount == 'Save & Print')
{
	$copies = '2';
}elseif($copycount == '3 Copies')
{
	$copies = '3';
}else{
	$copies = '2';
}
$totalpaymentamount = $cash + $checkamount + $cc1amount + $cc2amount + $cc3amount;
if($totalpaymentamount > $invoiceamount)
{
	$sql8 = "SELECT `complete` FROM `temp-payment` WHERE `invoiceid` = :invoiceid";
	$sth8 = $pdocxn->prepare($sql8);
	$sth8->bindParam(':invoiceid',$invoiceid);
	$sth8->execute();
	$linecount = $sth8->rowCount();
	if($linecount == '0') {
		$completed = '0';
	}else{
	while($row8 = $sth8->fetch(PDO::FETCH_ASSOC))
	{
		$completed = $row8['complete'];
	}
	$changeamount = '0';
	$chargeamount = '0';
	$changeamount = $_POST['changeamount'];
	$chargeamount = $_POST['chargeamount'];
}
if($completed == '0')
	{
		if($changeamount > '0' OR $chargeamount > '0')
		{
			$totalpaymentamount = $cash + $checkamount + $cc1amount + $cc2amount + $cc3amount - $changeamount;
		}else{
	if($cash < '1')
	{
		$cash = '0';
	}
	if($checkamount < '1')
	{
		$checkamount = '0';
		$checknumber = '0';
	}
	if($cc1amount < '1')
	{
		$cc1amount = '0';
		$cc1type = '0';
	}
	if($cc2amount < '1')
	{
		$cc2amount = '0';
		$cc2type = '0';
	}
	if($cc3amount < '1')
	{
		$cc3amount = '0';
		$cc3type = '0';
	}
		
$sql9 = 'INSERT INTO `temp-payment`(`invoiceid`,`paymentdate`,`invoiceamount`,`cash`,`checkamount`,`checknumber`,`cc1`,`cc1type`,`cc2`,`cc2type`,`cc3`,`cc3type`) VALUES (:invoiceid,:paymentdate,:invoiceamount,:cash,:checkamount,:checknumber,:cc1,:cc1type,:cc2,:cc2type,:cc3,:cc3type)';
$sth9 = $pdocxn->prepare($sql9);
$sth9->bindParam(':invoiceid',$invoiceid);
$sth9->bindParam(':paymentdate',$paymentdate);
$sth9->bindParam(':invoiceamount',$invoiceamount);
$sth9->bindParam(':cash',$cash);
$sth9->bindParam(':checkamount',$checkamount);
$sth9->bindParam(':checknumber',$checknumber);
$sth9->bindParam(':cc1',$cc1amount);
$sth9->bindParam(':cc1type',$cc1type);
$sth9->bindParam(':cc2',$cc2amount);
$sth9->bindParam(':cc2type',$cc2type);
$sth9->bindParam(':cc3',$cc3amount);
$sth9->bindParam(':cc3type',$cc3type);
$sth9->execute();
$tempid = $pdocxn->lastInsertId();
$header = 'Location: checkchange.php?tempid='.$tempid;
	header($header);
	exit();
}}}

$paymenttype = '6';
$paysql = "INSERT INTO `invoice`(`accountid`,`type`,`location`,`creationdate`,`invoicedate`) VALUES (:accountid,:type,:location,:creationdate,:paymentdate)";
//$paysql2 = "INSERT INTO `invoice`(`accountid`,`type`,`location`,`creationdate`,`invoicedate`) VALUES (".$accountid.",".$paymenttype.",".$locationid.",".$currentdate.",".$paymentdate.")";
//echo $paysql2;
$paysth = $pdocxn->prepare($paysql);
$paysth->bindParam(':accountid',$accountid);
$paysth->bindParam(':type',$paymenttype);
$paysth->bindParam(':location',$locationid);
$paysth->bindParam(':creationdate',$currentdate);
$paysth->bindParam(':paymentdate',$paymentdate);
$paysth->execute();
$paymentlinkid = $pdocxn->lastInsertId();

$paysql = "INSERT INTO `translink`(`transid`,`linktoid`,`amount`) VALUES (:transid,:linktoid,:amount)";
$paysth = $pdocxn->prepare($paysql);
$paysth->bindParam(':transid',$paymentlinkid);
$paysth->bindParam(':linktoid',$linkid);
$paysth->bindParam(':amount',$totalpaymentamount);
$paysth->execute();
$negtotalpaymentamount =  $totalpaymentamount * -1;


$sql1 = 'SELECT `id` FROM `journal` WHERE `invoiceid` = :invoiceid';
$sth1 = $pdocxn->prepare($sql1);
$sth1->bindParam(':invoiceid',$paymentlinkid);
$sth1->execute();
if ($sth1->rowCount() > 0)
{
$sth1 = $pdocxn->prepare('UPDATE `journal` SET `total`=:total,`invoicedate`=:invoicedate,`accountid`=:accountid WHERE `invoiceid` = :invoiceid');
$sth1->bindParam(':total',$negtotalpaymentamount);
$sth1->bindParam(':invoicedate',$invoicedate);
$sth1->bindParam(':accountid',$accountid);
$sth1->bindParam(':invoiceid',$paymentlinkid);
$sth1->execute();
}
else{
$sql2 = 'INSERT INTO `journal` (`invoiceid`,`accountid`,`total`,`invoicedate`,`journaltype`,`siteid`) VALUES (:invoiceid,:accountid,:total,:invoicedate,:journaltype,:siteid)';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':invoiceid',$paymentlinkid);
$sth2->bindParam(':accountid',$accountid);
$sth2->bindParam(':total',$negtotalpaymentamount);
$sth2->bindParam(':invoicedate',$paymentdate);
$sth2->bindParam(':journaltype',$paymenttype);
$sth2->bindParam(':siteid',$locationid);
$sth2->execute()or die(print_r($sth2->errorInfo(), true));
}
//$quickpayments cash(8) Check(9) Credit(10)
if($cash > '0')
{
$saletype = '12';
$linitemtype = '8';
$paymentdescription = "Cash";
//give change
//enter change into lineitems
$enterpaymentsql = "INSERT INTO `line_items`(`invoiceid`,`comment`,`totallineamount`,`lineitem_typeid`,`lineitem_saletype`) VALUES (:invoiceid,:comment,:totallineamount,:lineitem_typeid,:lineitem_saletype)";
$enterpaymentsth = $pdocxn->prepare($enterpaymentsql);
$enterpaymentsth->bindParam(':invoiceid',$paymentlinkid);
$enterpaymentsth->bindParam(':lineitem_typeid',$linitemtype);
$enterpaymentsth->bindParam(':totallineamount',$cash);
$enterpaymentsth->bindParam(':comment',$paymentdescription);
$enterpaymentsth->bindParam(':lineitem_saletype',$saletype);
$enterpaymentsth->execute();
}
if($checkamount > '0')
{
$saletype = '12';
$linitemtype = '9';
$enterpaymentsql = "INSERT INTO `line_items`(`invoiceid`,`comment`,`totallineamount`,`lineitem_typeid`,`lineitem_saletype`) VALUES (:invoiceid,:comment,:totallineamount,:lineitem_typeid,:lineitem_saletype)";
$enterpaymentsth = $pdocxn->prepare($enterpaymentsql);
$enterpaymentsth->bindParam(':invoiceid',$paymentlinkid);
$enterpaymentsth->bindParam(':lineitem_typeid',$linitemtype);
$enterpaymentsth->bindParam(':totallineamount',$checkamount);
$enterpaymentsth->bindParam(':comment',$checknumber);
$enterpaymentsth->bindParam(':lineitem_saletype',$saletype);
$enterpaymentsth->execute();
}
if($cc1amount > '0')
{
$saletype = '12';
$linitemtype = '10';
$enterpaymentsql = "INSERT INTO `line_items`(`invoiceid`,`comment`,`totallineamount`,`lineitem_typeid`,`lineitem_saletype`) VALUES (:invoiceid,:comment,:totallineamount,:lineitem_typeid,:lineitem_saletype)";
$enterpaymentsth = $pdocxn->prepare($enterpaymentsql);
$enterpaymentsth->bindParam(':invoiceid',$paymentlinkid);
$enterpaymentsth->bindParam(':lineitem_typeid',$linitemtype);
$enterpaymentsth->bindParam(':totallineamount',$cc1amount);
$enterpaymentsth->bindParam(':comment',$cc1type);
$enterpaymentsth->bindParam(':lineitem_saletype',$saletype);
$enterpaymentsth->execute();
}
if($cc2amount > '0')
{
$saletype = '12';
$linitemtype = '10';
$enterpaymentsql = "INSERT INTO `line_items`(`invoiceid`,`comment`,`totallineamount`,`lineitem_typeid`,`lineitem_saletype`) VALUES (:invoiceid,:comment,:totallineamount,:lineitem_typeid,:lineitem_saletype)";
$enterpaymentsth = $pdocxn->prepare($enterpaymentsql);
$enterpaymentsth->bindParam(':invoiceid',$paymentlinkid);
$enterpaymentsth->bindParam(':lineitem_typeid',$linitemtype);
$enterpaymentsth->bindParam(':totallineamount',$cc2amount);
$enterpaymentsth->bindParam(':comment',$cc2type);
$enterpaymentsth->bindParam(':lineitem_saletype',$saletype);
$enterpaymentsth->execute();
}
if($cc3amount > '0')
{
$saletype = '12';
$linitemtype = '10';
$enterpaymentsql = "INSERT INTO `line_items`(`invoiceid`,`comment`,`totallineamount`,`lineitem_typeid`,`lineitem_saletype`) VALUES (:invoiceid,:comment,:totallineamount,:lineitem_typeid,:lineitem_saletype)";
$enterpaymentsth = $pdocxn->prepare($enterpaymentsql);
$enterpaymentsth->bindParam(':invoiceid',$paymentlinkid);
$enterpaymentsth->bindParam(':lineitem_typeid',$linitemtype);
$enterpaymentsth->bindParam(':totallineamount',$cc3amount);
$enterpaymentsth->bindParam(':comment',$cc3type);
$enterpaymentsth->bindParam(':lineitem_saletype',$saletype);
$enterpaymentsth->execute();
}
if($changeamount > '0')
{
$changeid = '25';
$changesaletype = '13';
$negchangeamount = $changeamount * -1;
$paymentdescription = 'Change Given';
	//not sure about this
$enterpaymentsql = "INSERT INTO `line_items`(`invoiceid`,`comment`,`totallineamount`,`lineitem_typeid`,`lineitem_saletype`) VALUES (:invoiceid,:comment,:totallineamount,:lineitem_typeid,:lineitem_saletype)";
$enterpaymentsth = $pdocxn->prepare($enterpaymentsql);
$enterpaymentsth->bindParam(':invoiceid',$paymentlinkid);
$enterpaymentsth->bindParam(':lineitem_typeid',$changeid);
$enterpaymentsth->bindParam(':totallineamount',$negchangeamount);
$enterpaymentsth->bindParam(':comment',$paymentdescription);
$enterpaymentsth->bindParam(':lineitem_saletype',$changesaletype);
$enterpaymentsth->execute();
}
}
//end Enter payment Post

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
$mileagein1 = $row3['mileagein'];
$mileagein = number_format($mileagein1);
$mileageout = $row3['mileageout'];
$location = $row3['location'];
$taxtotal = $row3['tax'];
$subtotal = $row3['subtotal'];
$ponumber = $row3['ponumber'];
$dsubtotal = money_format('%(#0.2n',$subtotal);
$total = $row3['total'];
$dtotal = money_format('%(#0.2n',$total);
$invoicedate = $row3['invoicedate'];
$invoicedate2 = new DateTime($invoicedate);
$displaydate = $invoicedate2->format('M j, Y');
$duedatedisplay = $invoicedate2->format('M j, Y');
$displayinvoicedate2 = $invoicedate2->format('Y-m-d');
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
$dvehicleinfo = "\n<b>".$year." ".$make." ".$model." ".$engine."</b><br>VIN: ".$vin."<br>License: ".$license." (".$vehiclestate.")<br>Mileage: ".$mileagein;
}
else
{
$dvehicleinfo = "\n<b>".$description."</b><br>VIN: ".$vin."<br>License: ".$license." (".$vehiclestate.")<br>Mileage: ".$mileagein;
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

$sth7 = $pdocxn->prepare('SELECT * FROM notes WHERE `invoiceid` = :invoiceid');
$sth7->bindParam(':invoiceid',$invoiceid);
$sth7->execute();
while($row7 = $sth7->fetch(PDO::FETCH_ASSOC))
{
$databasenote = $row7['note'];
$note = stripslashes($databasenote);
$displaynote = nl2br($note);
$split = '<br />';

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
	<?php if($typeid == '1')
{

?>
<script>
<!--
window.onload = function() {
window.print();
window.open('printinvoice2.php?invoiceid=<?php echo $invoiceid.$chargelink; ?>');
window.close();
}
-->
</script>

<?php
}else{
?>
	<script>
	<!--
	window.onload = function() {
	window.print();
	window.close();
	}
	-->
	</script>
<?php
}
?>
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
                            	<b><?php echo $typename; ?> #: <?php if($typeid == '1'){echo $invoicenumber;}else{echo $invoiceid; }?></b><br>
                                PO #: <?php if($ponumber > '0'){echo $ponumber; }?><br>
								Date: <?php echo $displaydate; ?><br>
<?php

//justin
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
if($typeid == '1')
{
if($paymentid == '-1')
{
	$duedatedisplay = date('M j, Y', strtotime('+30 day', strtotime($invoicedate)));
	//$enddate = date('Y-m-d', strtotime('+1 day', strtotime($currentday)));
}}
	?>
                                Due: <?php echo $duedatedisplay; ?><br>
                                Salesperson: <?php echo $salesperson; ?>
                            </td>
                            <td class="tdborderright" width="33%"><?php echo $dvehicleinfo; ?></td>  
                        </tr><tr>
<td class="notesborder" colspan="3"><?php
echo $displaynote;
?></td></tr>
            </table><table cellpadding="0" cellspacing="3" width="100%">
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
$fettotal = '0';
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
$invcomment1 = $row4['comment'];
$invcomment = nl2br($invcomment1);
$linefet = $row4['fet'];
$linefet1 = $linefet * $invqty;
$fettotal = $fettotal + $linefet1;
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
echo "<tr class=\"item\"><td class=\"centertop\">".$invqty."&nbsp;</td><td class=\"left\" colspan=\"2\">".$invcomment."</td><td class=\"right\">".$dsingleprice."</td><td class=\"right\">".$dextprice."</td></tr>\n";
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




<?php
if($displayinvoicedate2 < '2020-04-04')
{

	$sql3 = "SELECT SUM(`taxamount`) AS `taxtotal` FROM `tax_trans_old` WHERE `transid` = :invoiceid";
	$sth3 = $pdocxn->prepare($sql3);
	$sth3->bindParam(':invoiceid',$invoiceid);
	$sth3->execute();
	while($row3 = $sth3->fetch(PDO::FETCH_ASSOC))
	{
		$taxamount = $row3['taxtotal'];
	}
$invoicetotal = $invoicesubtotal + $taxamount + $fet;
$invoiceformtotal = round($invoicetotal,2);
$taxtotal = money_format('%(#0.2n',$taxamount);
$dsubtotal = money_format('%(#0.2n',$invoicesubtotal);
$dtotal = money_format('%(#0.2n',$invoicetotal);
}



?>
<td colspan="2" class="right">
                   Tax:  <?php echo $taxtotal; ?></td>
                <td colspan="2" class="right">
                   Subtotal:</td><td class="right"><?php echo $dsubtotal; ?>
                </td>
            </tr>
			<?php
			if($fettotal > '0')
			{
				$dfettotal = number_format($fettotal, 2);
				?>
			<tr class="heading">
			<td colspan="2" class="right">
                   Federal Tax:  <?php echo $dfettotal; ?></td>
				   <td colspan="2" class="boldright">
                   Total:</td><td class="boldright"><?php echo $dtotal; ?>
                </td>
            </tr>
<?php
}else{
	?>
            <tr class="heading">
                <td colspan="4" class="boldright">
                   Total:</td><td class="boldright"><?php echo $dtotal; ?>
                </td>
            </tr>
<?php
}
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
if($typeid == '1')
{
if($charge == '1')
{
	echo "<tr class=\"total\"><td colspan=\"2\" class=\"right\"></td><td class=\"boldright\" colspan=\"2\">Net 30 Charge</td><td class=\"right\"></td></tr>";

}else{
if($paymentid == '-1')
{
if($total > '0' && $charge == '0')
{
?>
<script type="text/javascript">
<!--
        window.location.replace('payments.php?invoiceid=<?php echo $invoiceid; ?>');
//-->
</script>
<?php
}}else{
$sql10 = "SELECT `id`,`invoicedate` FROM `invoice` WHERE `id` = :paymentid";
$sth10 = $pdocxn->prepare($sql10);
$sth10->bindParam(':paymentid',$paymentid);
$sth10->execute();
while($row10 = $sth10->fetch(PDO::FETCH_ASSOC))
{
$paymentdate = $row10['invoicedate'];
$paymentdate2 = new DateTime($paymentdate);
$displaypaymentdate = $paymentdate2->format('m/j/Y');
}
$payrow = '1';
$sql11 = "SELECT `id`,`totallineamount`,`lineitem_typeid`,`comment` FROM `line_items` WHERE `invoiceid` = :paymentid";
$sth11 = $pdocxn->prepare($sql11);
$sth11->bindParam(':paymentid',$paymentid);
$sth11->execute();
$totalpayment = '0';
while($row11 = $sth11->fetch(PDO::FETCH_ASSOC))
{
 $paymentamount = $row11['totallineamount'];
 $paymentdesc = $row11['comment'];
 $paymenttypeid = $row11['lineitem_typeid'];
 if($paymenttypeid == '8')
 {
   $paymentdesc = "Cash";
  }
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
echo "<tr class=\"total\"><td colspan=\"2\" class=\"right\">Payment Method:&nbsp;&nbsp;&nbsp;&nbsp;".$displaypaymentdate."</td><td class=\"right\" colspan=\"2\">".$paymentdesc."</td><td class=\"right\">".$paymentamount."</td></tr>";
}else{
echo "<tr class=\"total\"><td colspan=\"2\"></td><td class=\"right\" colspan=\"2\">".$paymentdesc."</td><td class=\"right\">".$paymentamount."</td></tr>";
}
$payrow ++;
$totalpayment = $totalpayment + $paymentamount;
}}}
$invbalance = $total-$totalpayment;
$dinvbalance = money_format('%(#0.2n',$invbalance);

$balsql1 = 'SELECT SUM(`total`) AS `storebalance` FROM `journal` WHERE `accountid` = :accountid AND `siteid` = :siteid';
$balsth1 = $pdocxn->prepare($balsql1);
$balsth1->bindParam(':accountid',$accountid);
$balsth1->bindParam(':siteid',$location);
$balsth1->execute();
while($balrow1 = $balsth1->fetch(PDO::FETCH_ASSOC))
{
$storebalance1 = $balrow1['storebalance'];
$storebalance = money_format('%(#0.2n',$storebalance1);
}

$balsql2 = 'SELECT SUM(`total`) AS `totalbalance` FROM `journal` WHERE `accountid` = :accountid';
$balsth2 = $pdocxn->prepare($balsql2);
$balsth2->bindParam(':accountid',$accountid);
$balsth2->execute();
while($balrow2 = $balsth2->fetch(PDO::FETCH_ASSOC))
{
$totalbalance1 = $balrow2['totalbalance'];
$totalbalance = money_format('%(#0.2n',$totalbalance1);
}
?>
<tr class="total">
                <td colspan="2" class="right">
                    Invoice Balance:
                </td>
                <td class="right" colspan="2">
                </td>
                <td class="right"><?php echo $dinvbalance; ?></td>
        </tr>
<tr class="total">
                <td colspan="2" class="right">
                    Current Account Balance:
                </td>
                <td class="right" colspan="2">
                </td>
                <td class="right">$<?php echo $storebalance; ?></td>
        </tr>
<?php
}
?>

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