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
$linkpage = 'recinv-subtype.php';
$invtable = 'invoice';
$invlinetable ='line_items';
$typeid = '2';

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
<link rel="stylesheet" type="text/css" href="style/globalstyle.css" >
<link rel="stylesheet" type="text/css" href="style/accountstyle.css" >
<link rel="stylesheet" type="text/css" href="style/schedulestyle.css" >
</head>
<body>
<a name="top"></a><h1>Select Inventory Subtype</h1>
<?php
$sth1 = $pdocxn->prepare('SELECT `id`,`description` FROM `inventory_subtype` WHERE `active` = \'1\' ORDER BY `description` ASC');
$sth1->bindParam(':userid',$userid);
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{

$description = $row1['description'];
$typeid = $row1['id'];
$letter1 = substr($description, 0, 1);
if($prevletter != $letter1)
{
    echo "\n&nbsp;<a href=\"#".$letter1."\"><b>".$letter1."</b></a>&nbsp;";
}
$prevletter = $letter1;
}
?>
<br /><br /><br />
<table id="highlightTable" class="blueTable">
<thead>
<tr>
<th>Subtype</th>
</tr>
</thead>
<tbody>
<?php
$sth1 = $pdocxn->prepare('SELECT `id`,`description` FROM `inventory_subtype` WHERE `active` = \'1\' ORDER BY `description` ASC');
$sth1->bindParam(':userid',$userid);
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
$description = $row1['description'];
$typeid = $row1['id'];
$letter1 = substr($description, 0, 1);
if($prevletter != $letter1)
{
    echo "\n<tr><td class=\"redstatus\"><b><a name=\"".$letter1."\"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$letter1."</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"#top\">Back to top</a></td></tr>";
}
echo "<tr><td><b><a href=\"inventory-add.php?st=".$typeid."\">$description</a></b></td></tr>";
$prevletter = $letter1;
}
?>
</tbody></table>