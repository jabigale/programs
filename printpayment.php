<?php
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Print';
$linkpage = 'printpayment.php';
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
$paymentdate = $_POST['paymentdate'];
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
}
$sth3 = $pdocxn->prepare('SELECT * FROM `invoice` WHERE `id` = :inv');
$sth3->bindParam(':inv',$invoiceid);
$sth3->execute();
$row3 = $sth3->fetch(PDO::FETCH_ASSOC);
$accountid = $row3['accountid'];
$invoicenumber = $row3['invoiceid'];
$typeid = $row3['type'];
$userid = $row3['userid'];
$siteid = $row3['location'];
$taxtotal = $row3['tax'];
$subtotal = $row3['subtotal'];
$dsubtotal = money_format('%(#0.2n',$subtotal);
$total = $row3['total'];
$dtotal = money_format('%(#0.2n',$total);
$invoicedate = $row3['invoicedate'];
$invoicedate2 = new DateTime($invoicedate);
$displaydate = $invoicedate2->format('M j, Y');
$roa = $row3['roa'];
if ($roa == '1')
{
    $roabrand ="**ROA Payment**";
}
else
{
    $roabrand ="";
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

	$typename = 'Payment';

$sth8 = $pdocxn->prepare('SELECT * FROM `locations` WHERE `id` = :locationid');
$sth8->bindParam(':locationid',$siteid);
$sth8->execute();
while($row8 = $sth8->fetch(PDO::FETCH_ASSOC))
{
$displaystorenameshort = $row8['storename'];
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
                            	<b><?php echo $typename; ?> #: <?php if($typeid == '1'){echo $invoicenumber;}else{echo $invoiceid; }?></b><br>
								Date: <?php echo $displaydate; ?><br>
                                Salesperson: <?php echo $salesperson; ?>
                            </td>
                            <td class="tdborderright" width="33%"><b><?php echo $roabrand; ?></b></td>  
                        </tr><tr>
<td class="notesborder" colspan="3"><?php
echo $displaynote;
?></td></tr>
            </table><table cellpadding="0" cellspacing="0">
            <tr style="outline: thin solid">
                <td colspan="2" width="70%">
                    Description
                </td>
                <td width="15%" class="right">
                    Payment Amount
                </td>
			</tr>

<?php
$sth4 = $pdocxn->prepare('SELECT `id`,`lineitem_typeid`,`totallineamount`,`comment` FROM `line_items` WHERE invoiceid = :inv ORDER BY linenumber ASC');
$sth4->bindParam(':inv',$invoiceid);
$sth4->execute();
$linecount = $sth4->rowCount();
$dtotal = '0';
	while($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
{
$lineid = $row4['id'];
$paymenttypeid = $row4['lineitem_typeid'];
$totallineamount = $row4['totallineamount'];
$paymentdesc = $row4['comment'];
$dextprice = money_format('%(#0.2n',$totallineamount);
if($paymenttypeid == '8')
{
  $paymentdesc = "Cash";
 }
if($paymenttypeid == '9')
{
  $paymentdesc = "Check #: ".$paymentdesc;
 }
if($invqty == '0')
{
 $invqty = '';
 $dextprice = '';
 $dsingleprice = '';
}
echo "<tr class=\"item\"><td class=\"left\" colspan=\"2\">".$paymentdesc."</td><td class=\"right\">".$dextprice."</td></tr>\n";
$dtotal = $dtotal + $dextprice;
}
$dtotal1 = money_format('%(#0.2n',$dtotal);
?>
<tr><td colpan="3"><br /><br /><br /></td></tr>
            <tr class="heading">
                <td colspan="3" class="boldright">
<br /></td>
            </tr>

            <tr class="heading">
                <td colspan="2" class="boldright">
                   Total:</td><td class="boldright">$<?php echo $dtotal1; ?>
                </td>
            </tr><tr><td colpan="3"><br /><br /><br /></td></tr>
<?php
$balsql1 = 'SELECT SUM(`total`) AS `storebalance` FROM `journal` WHERE `accountid` = :accountid AND `siteid` = :siteid';
$balsth1 = $pdocxn->prepare($balsql1);
$balsth1->bindParam(':accountid',$accountid);
$balsth1->bindParam(':siteid',$siteid);
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
echo "<tr class=\"total\"><td class=\"tdleft\">Current Account Balance from All Stores:&nbsp;&nbsp;$totalbalance</td><td class=\"tdright\">Account Balance at ".$displaystorenameshort.":</td><td>".$storebalance."</td></tr>";
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