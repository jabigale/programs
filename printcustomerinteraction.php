<?php
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Print';
$linkpage = 'printcustomerinteraction.php';
$currentday = date('Y-n-j');
$quicksearch = '0';;
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


if(isset($_POST['invoiceid']))
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
	//submit form general
	if(isset($_POST['invoiceid']))
	{
	$invoiceid = $_POST['invoiceid'];
	}
else {
	$invoiceid = '0';
}

if(isset($_POST['enterpayment']))
{
$paymentdate = $_POST['paymentdate'];
$sql8 = "SELECT `location`,`invoicedate`,`accountid` FROM `customerinteractions` WHERE `id` = :transid";
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
$cash = $_POST['cash'];
$checkamount = $_POST['checkamount'];
$checknumber = $_POST['checknumber'];
$cc1amount = $_POST['cc1'];
$cc1type = $_POST['cc1type'];
$cc2amount = $_POST['cc2'];
$cc2type = $_POST['cc2type'];
$cc3amount = $_POST['cc3'];
$cc3type = $_POST['cc3type'];
$totalpaymentamount = $cash + $checkamount + $cc1amount + $cc2amount + $cc3amount;
$paymenttype = '6';

$paysql = "INSERT INTO `customerinteractions`(`accountid`,`type`,`location`,`creationdate`,`invoicedate`) VALUES (:accountid,:type,:location,:creationdate,:paymentdate)";
//$paysql2 = "INSERT INTO `customerinteractions`(`accountid`,`type`,`location`,`creationdate`,`invoicedate`) VALUES (".$accountid.",".$paymenttype.",".$locationid.",".$currentdate.",".$paymentdate.")";
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
//$quickpayments cash(8) Check(9) Credit(10)
if($cash > '0')
{
$saletype = '12';
$linitemtype = '8';
$paymentdescription = "Cash";
$enterpaymentsql = "INSERT INTO `ci_line_items`(`invoiceid`,`comment`,`totallineamount`,`lineitem_typeid`,`lineitem_saletype`) VALUES (:invoiceid,:comment,:totallineamount,:lineitem_typeid,:lineitem_saletype)";
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
$enterpaymentsql = "INSERT INTO `ci_line_items`(`invoiceid`,`comment`,`totallineamount`,`lineitem_typeid`,`lineitem_saletype`) VALUES (:invoiceid,:comment,:totallineamount,:lineitem_typeid,:lineitem_saletype)";
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
$enterpaymentsql = "INSERT INTO `ci_line_items`(`invoiceid`,`comment`,`totallineamount`,`lineitem_typeid`,`lineitem_saletype`) VALUES (:invoiceid,:comment,:totallineamount,:lineitem_typeid,:lineitem_saletype)";
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
$enterpaymentsql = "INSERT INTO `ci_line_items`(`invoiceid`,`comment`,`totallineamount`,`lineitem_typeid`,`lineitem_saletype`) VALUES (:invoiceid,:comment,:totallineamount,:lineitem_typeid,:lineitem_saletype)";
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
$enterpaymentsql = "INSERT INTO `ci_line_items`(`invoiceid`,`comment`,`totallineamount`,`lineitem_typeid`,`lineitem_saletype`) VALUES (:invoiceid,:comment,:totallineamount,:lineitem_typeid,:lineitem_saletype)";
$enterpaymentsth = $pdocxn->prepare($enterpaymentsql);
$enterpaymentsth->bindParam(':invoiceid',$paymentlinkid);
$enterpaymentsth->bindParam(':lineitem_typeid',$linitemtype);
$enterpaymentsth->bindParam(':totallineamount',$cc3amount);
$enterpaymentsth->bindParam(':comment',$cc3type);
$enterpaymentsth->bindParam(':lineitem_saletype',$saletype);
$enterpaymentsth->execute();
}
}
$sth3 = $pdocxn->prepare('SELECT * FROM invoice WHERE id = :inv');
$sth3->bindParam(':inv',$invoiceid);
$sth3->execute();
$row3 = $sth3->fetch(PDO::FETCH_ASSOC);
$accountid = $row3['accountid'];
$invoicenumber = $row3['invoiceid'];
$typeid = $row3['type'];
$userid = $row3['userid'];
$vehicleid = $row3['vehicleid'];
$mileagein = $row3['mileagein'];
$mileageout = $row3['mileageout'];
$location = $row3['location'];
$taxtotal = $row3['tax'];
$subtotal = $row3['subtotal'];
$total = $row3['total'];
$invoicedate = $row3['invoicedate'];
$invoicedate2 = new DateTime($invoicedate);
$displaydate = $invoicedate2->format('M j, Y');
if($accountid > '0')
{
$sth4 = $pdocxn->prepare('SELECT * FROM accounts WHERE acctid = :acct');
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
$sth5 = $pdocxn->prepare('SELECT * FROM accountbalance WHERE acctid = :acct');
$sth5->bindParam(':acct',$accountid);
$sth5->execute();
while($row5 = $sth5->fetch(PDO::FETCH_ASSOC))
{
	$store1 = $row5['store1'];
	$store2 = $row5['store2'];
	$store3 = $row5['store3'];
	$store4 = $row5['store4'];
	$store5 = $row5['store5'];
	$store6 = $row5['store6'];
	$store7 = $row5['store7'];
	$store8 = $row5['store8'];
	$store9 = $row5['store9'];
	$store10 = $row5['store10'];
	$totalbalance = $store1+$store2+$store3+$store4+$store5+$store6+$store7+$store8+$store9+$store10;
}}
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
$dvehicleinfo = "\n<b>".$year." ".$make." ".$model." ".$submodel." ".$engine."</b><br>VIN: ".$vin."<br>License: ".$license." (".$vehiclestate.")<br>Mileage: ".$mileagein."</td>";
}
else
{
$dvehicleinfo = "\n<b>".$description."</b><br>VIN: ".$vin."<br>License: ".$license." (".$vehiclestate.")<br>Mileage: ".$mileagein."</td>";
}
$sql6 = 'SELECT `name` FROM `invoice_type` WHERE `id` = :type';
$sth6 = $pdocxn->prepare($sql6);
$sth6->bindParam(':type',$typeid);
$sth6->execute();
while($row6 = $sth6->fetch(PDO::FETCH_ASSOC))
{
	$typename = $row6['name'];
}
}
}
}
$sth7 = $pdocxn->prepare('SELECT * FROM notes WHERE `invoiceid` = :invoiceid');
$sth7->bindParam(':invoiceid',$invoiceid);
$sth7->execute();
while($row7 = $sth7->fetch(PDO::FETCH_ASSOC))
{
$note = $row7['note'];
$displaynote = nl2br($note);
$split = '<br />';
//if updated to 5.3 or later $line0 = strstr($displaynote,$split);
$linenum = preg_split('/\n/',$note);
$lni = '0';
$linenumber = count($linenum);
while($lni <= $linenumber)
{
${"line".$lni} = $linenum[$lni];
${"str".$lni} = strlen(${"line".$lni});
$lni ++;
}
$linenumber = count($linenum);
}

$sth8 = $pdocxn->prepare('SELECT * FROM locations WHERE `id` = :locationid');
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
    <meta charset="utf-8">
    <title></title>
    <style>
    .invoice-box {
        width: 8.5in;
        margin: auto;
        padding: 0px;
        border: 0px solid #eee;
        box-shadow: 0 0 0px rgba(0, 0, 0, .15);
        font-size: 14px;
        line-height: 24px;
        font-family:  Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        color: #555;
    }
    .invoice-box table {
        width: 100%;
        line-height: inherit;
        text-align: left;
    }
    .td #left {
        text-align: left;
    }
    .invoice-box table td {
        padding: 1px;
        vertical-align: top;
    }
    .left{
        text-align: left;
    }
    .right{
    	text-align: right;
    }
    .center{
    	text-align: center;
    }
.bold{
        font-weight: bold;
}
.boldright{
text-align: right;
        font-weight: bold;
}
    .invoice-box table tr.top table td {
        padding-bottom: 5px;
    }
    
    .invoice-box table tr.top table td.title {
        font-size: 16px;
        color: #333;
    }
    .invoice-box table tr.information table td {
        padding-bottom: 5px;
    }
    .invoice-box table tr.heading td {
        background: #ccc;
        border-bottom: 1px solid #ddd;
        font-weight: bold;
    }
    .invoice-box table tr.details td {
        padding-bottom: 5px;
    }
    .invoice-box table tr.item td{
        border-bottom: 1px solid #ccc;
    }
    .invoice-box table tr.item.last td {
        border-bottom: none;
    }
    .invoice-box table tr.total td:nth-child(2) {
        border-top: 0px;
    }
.tdborder{
border: 2px solid #333;
}
.notesborder{
border-top: 2px solid #333;
border-left: 2px solid #333;
border-right: 2px solid #333;
}
.notes2border{
border-bottom: 2px solid #333;
border-left: 2px solid #333;
border-right: 2px solid #333;
}
.tdborderright{
border: 2px solid #333;
text-align: right;
}
    @media only screen and (max-width: 600px) {
        .invoice-box table tr.top table td {
            width: 100%;
            display: block;
            text-align: center;
        }
        .invoice-box table tr.information table td {
            width: 100%;
            display: block;
            text-align: center;
        }
    }
    /** RTL **/
    .rtl {
        direction: rtl;
        font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
    }
    .rtl table {
        text-align: right;
    }
    .rtl table tr td:nth-child(2) {
        text-align: left;
    }
    </style>
<script>
<!--
window.onload = function() {
window.print();
window.location.replace('http://auto-shop-software.com/fkm/invoice.php');
}
//-->
</script>
</head>
<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                <img src="images/logo-small.jpg" style="width:100%; max-width:300px;">
                            </td>
                            <td class="title">
                                <b><?php echo $typename; ?> #: <?php if($typeid == '1'){echo $invoicenumber;}else{echo $invoiceid; }?></b><br>
                                PO #: <?php if($ponumber = '0'){echo $ponumber; }?><br>
                                Date: <?php echo $displaydate; ?><br>
                                Due: <?php echo $displaydate; ?><br>
                                Salesperson: <?php echo $salesperson; ?>
                            </td>
                            <td class="title">
                                <b><?php echo $displaystorename; ?></b><br>
                                <?php echo $storeaddress1; ?><br>
                                <?php echo $storeaddress2; ?><br>
                                Phone: <?php echo $storenumber; ?><br>
                                Fax: <?php echo $storefax; ?><br>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="information">
                <td colspan="2">
                    <table cellpadding="0" cellspacing="0">
                        <tr>
                            <td class="tdborder" width="30%">
                                <b><?php echo $fullname; ?></b><br>
                                <?php echo $address1; ?><br>
                                <?php echo $citystatezip; ?><br>
                                <?php echo $phone1; ?><br>
                            </td>
                            <td class="notesborder"><b>Notes:</b><br>
                            	<?php echo $line0."<br/>";
                            	if ($str0 > '90'){
                            		}else{
                            			echo $line1."<br />";
									}
                            	if ($str0 or $str1 > '30'){
                            		}else{ echo $line2."<br/>";;
									}?>
                            </td>
                            <td class="tdborderright" width="30%"><?php echo $dvehicleinfo; ?></td>  
                        </tr><tr>
<td class="notes2border" colspan="3"><?php
if ($str0 > '90'){
echo $line1."<br/>";
}
if ($str0 or $str1 > '30'){
	echo $line2."<br/>";
}
if($lni > '4'){echo $line3."<br/>";}
if($lni > '5'){echo $line4."<br/>";}
if($lni > '6'){echo $line5."<br/>";}
if($lni > '7'){echo $line6."<br/>";}
if($lni > '8'){echo $line7."<br/>";}
if($lni > '9'){echo $line8."<br/>";}
if($lni > '10'){echo $line9."<br/>";}
if($lni > '11'){echo $line10."<br/>";}
?></td></tr>
                    </table>
                </td>
            </tr>
            </table><table cellpadding="0" cellspacing="0">
            <tr class="heading">
            	<td width="5%" class="center">
                    Qty
                </td>
                <td colspan="2" width="75%">
                    Description
                </td>
                <td width="10%" class="right">
                    Unit Price
                </td>
                <td width="10%" class="right">
                    Ext Price
                </td>
            </tr>
<?php
$sth4 = $pdocxn->prepare('SELECT * FROM ci_line_items WHERE invoiceid = :inv ORDER BY linenumber ASC');
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
setlocale(LC_MONETARY,"en_US");
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
echo "<tr class=\"item\"><td  class=\"center\">".$invqty."</td><td class=\"left\" colspan=\"2\">".$invcomment."</td><td class=\"right\">".$dsingleprice."</td><td class=\"right\">".$dextprice."</td></tr>";
}
?>
                        <tr class="total"><td></td>
                <td width="65%" class="right">
                   Tax:  <?php echo $taxtotal; ?>
                <td colspan="2" class="right">
                   SubTotal:</td><td class="right"><?php echo $subtotal; ?>
                </td>
            </tr>
            <tr class="heading">
                <td colspan="4" class="boldright">
                   Total:</td><td class="boldright">$<?php echo $total; ?>
                </td>
            </tr>
<?php
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
if($total > '0')
{
?>
<script type="text/javascript">
<!--
  if(confirm("Would you like to enter a payment for this invoice?")) {
        window.location.replace('http://auto-shop-software.com/fkm/payments.php?invoiceid=<?php echo $invoiceid; ?>');
    } else {
    }
//-->
</script>
<?
}}else{
$sql10 = "SELECT `id`,`invoicedate` FROM `customerinteractions` WHERE `id` = :paymentid";
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
$sql11 = "SELECT `id`,`totallineamount`,`lineitem_typeid`,`comment` FROM `ci_line_items` WHERE `invoiceid` = :paymentid LIMIT 1";
$sth11 = $pdocxn->prepare($sql11);
$sth11->bindParam(':paymentid',$paymentid);
$sth11->execute();
while($row11 = $sth11->fetch(PDO::FETCH_ASSOC))
{
 $paymentamount = $row11['totallineamount'];
 $paymentdesc = $row11['comment'];
 $paymenttypeid = $row11['lineitem_typeid'];
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
echo "<tr class=\"total\"><td colspan=\"2\"></td><td class=\"right\" colspan=\"2\"></td><td class=\"right\">".$paymenttype."&nbsp;&nbsp;&nbsp;&nbsp;".$paymentamount."</td></tr>";
}
$payrow ++;
}}
$invbalance = $total-$paymentamount;
$dinvbalance = money_format('%(#0.2n',$invbalance);
?>
<tr class="total">
                <td colspan="2" class="right">
                    Invoice Balance
                </td>
                <td class="right" colspan="2">
                </td>
                <td class="right"><?php echo $dinvbalance; ?></td>
        </tr>
<?php
}
?>
<!--                      <tr class="total">
                <td colspan="2">
                    Current Account Balance
                </td>
                <td class="right" colspan="2">
                </td>
                <td class="right">$<?php echo $totalbalance; ?></td>
        </tr>
        -->
        </table>
    </div>
</body>
</html>
<?php
}
else
{
echo "test";
}
?>