<?php
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Print';
$linkpage = 'printstatement.php';
$currentday = date('Y-n-j');
$rand = rand(5000,999999);
$startdate = $_GET['startdate'];
$enddate = $_GET['enddate'];
$dstartdate = date("n/j/Y", strtotime($startdate));
$denddate = date("n/j/Y", strtotime($enddate));
$dbstartdate = date('Y-m-d',strtotime('- 1 day',strtotime($startdate)));
$dbenddate = date('Y-m-d',strtotime('+ 1 day',strtotime($enddate)));

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

$sql1 = "SELECT `variable` FROM `global_settings` WHERE `id` = '8'";
$sth1 = $pdocxn->prepare($sql1);
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
	$footertag = $row1['variable'];
}
$sth2 = $pdocxn->prepare('SELECT `id`,`accountid`,`balance` FROM `s1statement_temp` ORDER BY `abvname` ASC');
$sth2->execute();
$count1 = $sth2->rowCount();
if($count1 < '1') {
    $header = "Location: index.php";
    header($header);
    exit();
}else{
while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
{
$tempid = $row2['id'];
$accountid = $row2['accountid'];
$storebalance = $row2['balance'];

if($accountid > '0')
{
$sth4 = $pdocxn->prepare('SELECT * FROM `accounts` WHERE `accountid` = :accountid');
$sth4->bindParam(':accountid',$accountid);
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
$creditlimit = $row4['creditlimit'];
$flag = $row4['flag'];
$comment = $row4['comment'];
$fullname = $fname." ".$lname;

if($creditlimit > '0')
	{
		$creditlimit = $creditlimit;
	}
	else
		{
			$creditlimit = "0";
		}
}


$sth8 = $pdocxn->prepare('SELECT * FROM `locations` WHERE `id` = :siteid');
$sth8->bindParam(':siteid',$siteid);
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
      <link rel="stylesheet" href="style/statement1.css" />
    <meta charset="utf-8">

    <?php
$sth6 = $pdocxn->prepare('SELECT `id` FROM `s1statement_temp` LIMIT 2');
$sth6->execute();
$checkcount = $sth6->rowCount();
if($checkcount > '1')
{
?>
<script>
<!--
window.onload = function() {
window.print();
window.open('printstatement.php?startdate=<?php echo $startdate; ?>&enddate=<?php echo $enddate; ?>&r=<?php echo $rand; ?>');
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
    window.open('index.php');
	window.close();
	}
	-->
	</script>
<?php
}
?>
</head>
<body>
<div class="page-header" style="text-align: left">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
            <td class="title">
                                <b><?php echo $displaystorename; ?></b><br>
                                <?php echo $storeaddress1; ?><br />
                                <?php echo $storeaddress2; ?><br>
                                Phone: <?php echo $storenumber; ?>&nbsp;
                                Fax: <?php echo $storefax; ?><br>
                            </td>
                            <td class="title">
                            <br />
                            </td>
                            <td class="title">
                                <img src="images/logo-small.jpg" style="width:100%; max-width:300px;">
                            </td>
                            <td class="title">
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
                            <td class="nametd" width="60%">
                                <b><?php echo $fullname; ?></b><br>
                                <?php echo $address1; ?><br>
                                <?php echo $citystatezip; ?><br>
                                <br><br>
                            </td>
                            <td class="tdborder" width="30%">
                            </td>  
                        </tr><tr>
</tr>
            </table>

            <table cellpadding="0" cellspacing="3" width="100%">
                <tr><td colspan="4" class="center-bold">Statement <?php echo $dstartdate; ?> - <?php echo $denddate; ?></td></tr>
            <tr style="outline: thin solid">
            	<td width="25%" class="center">
                    Account Name
                </td>
                <td colspan="2" width="25%">
                    Credit Limit
                </td>
                <td width="12%" class="right">
                    Available Credit
                </td>
                <td width="12%" class="right">
                    Account Balance
                </td>
            </tr>
            <?php
$sth4 = $pdocxn->prepare('SELECT SUM(`total`) as `begbalance` FROM `journal` WHERE `accountid` = :accountid AND `invoicedate` < :startdate AND `siteid` = :siteid');
$sth4->bindParam(':accountid',$accountid);
$sth4->bindParam(':startdate',$startdate);
$sth4->bindParam(':siteid',$siteid);
$sth4->execute();
	while($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
{
$begbalance = $row4['begbalance'];
$dbegbalance = number_format($begbalance, 2);
}

$sth4 = $pdocxn->prepare('SELECT SUM(`total`) as `newbalance` FROM `journal` WHERE `accountid` = :accountid AND `invoicedate` < :enddate AND `siteid` = :siteid');
$sth4->bindParam(':accountid',$accountid);
$sth4->bindParam(':enddate',$dbenddate);
$sth4->bindParam(':siteid',$siteid);
$sth4->execute();
	while($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
{
    $newbalance = $row4['newbalance'];
    $creditleft = $creditlimit - $newbalance;
    $dcreditleft = number_format($creditleft, 2);
    $dnewbalance = number_format($newbalance, 2);
}
echo "<tr class=\"item\"><td class=\"centertop\">".$fullname."</td><td class=\"left\" colspan=\"2\">".$creditlimit."</td><td class=\"right\">".$dcreditleft."</td><td class=\"right\">".$dnewbalance."</td></tr>\n";

?>
</table>
<br /><br />
            <table cellpadding="0" cellspacing="3" width="100%">
            <tr style="outline: thin solid">
            	<td width="5%" class="center">
                    Date
                </td>
                <td colspan="2" width="70%">
                    Transaction
                </td>
                <td width="12%" class="right">
                    Amount
                </td>
                <td width="12%" class="right">
                    Balance
                </td>
			</tr>
<?php
echo "<tr class=\"item\"><td class=\"centertop\">".$dstartdate."</td><td class=\"left\" colspan=\"2\">Balance Forward</td><td class=\"right\">".$dbegbalance."</td><td class=\"right\">".$dbegbalance."</td></tr>\n";

$sth4 = $pdocxn->prepare('SELECT `total`,`id`,`invoiceid`,`invoicedate`,`journaltype` FROM `journal` WHERE `accountid` = :accountid AND `invoicedate` > :startdate AND `invoicedate` < :enddate AND `siteid` = :siteid');
$sth4->bindParam(':accountid',$accountid);
$sth4->bindParam(':startdate',$dbstartdate);
$sth4->bindParam(':enddate',$dbenddate);
$sth4->bindParam(':siteid',$siteid);
$sth4->execute();
$newbalance = $begbalance;
	while($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
{
$invoicedate = $row4['invoicedate'];
$displaydate2 = date("n/j/Y", strtotime($invoicedate));
$total = $row4['total'];
$newbalance = $newbalance + $total;
$invoiceid = $row4['invoiceid'];
$typeid = $row4['journaltype'];
$dtotal = number_format($total, 2);
$dnewbalance = number_format($newbalance, 2);
$sql6 = 'SELECT `name` FROM `invoice_type` WHERE `id` = :type';
$sth6 = $pdocxn->prepare($sql6);
$sth6->bindParam(':type',$typeid);
$sth6->execute();
while($row6 = $sth6->fetch(PDO::FETCH_ASSOC))
{
	$typename = $row6['name'];
}
if($typeid == '1')
{
$sth5 = $pdocxn->prepare('SELECT `invoiceid` FROM `invoice` WHERE `id` = :invoiceid');
$sth5->bindParam(':invoiceid',$invoiceid);
$sth5->execute();
  while($row5 = $sth5->fetch(PDO::FETCH_ASSOC))
{
$invoiceid = $row5['invoiceid']; 
}}

if($total == '0')
{
}else{
echo "<tr class=\"item\"><td class=\"centertop\">".$displaydate2."</td><td class=\"left\" colspan=\"2\"><b>".$typename." #".$invoiceid."<b/></td><td class=\"right\">".$dtotal."</td><td class=\"right\">".$dnewbalance."</td></tr>\n";
}}
?>
<tr><td colspan="5"><br /></td></tr>
<tr class="total">
                <td colspan="4" class="right">
                    <b>Statement Amount Due:</b>
                </td>
                <td class="right"><b>$<?php echo $dnewbalance; ?></b></td>
        </tr>
<?php

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
//delete from temptable
}
$sth2 = $pdocxn->prepare('DELETE FROM `s1statement_temp` WHERE `accountid` = :accountid');
$sth2->bindParam(':accountid',$accountid);
$sth2->execute();
exit();
}}
?>