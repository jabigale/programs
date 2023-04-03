<?php
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

//forward to statements or bfreports
//get total values
//getinvinfo
//submitted form
//report1
//report2
//report3
//report4
//report5
//report7
//report8
//report9
//report10
//report11
//report12
//report13
//report15
//report15a
//report16
//report17
//java
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Reports';
$linkpage = 'accountingreports.php';

$showvoided = '0';
$tri = '1';
$singlelocation = '0';
$selecteddate = $posteddate;
$begindate = $today;
$type = '1';
$cash = '0';
$check = '0';
$creditcard = '0';
$cfna = '0';
$change = '0';
$subtractcash = '0';


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

if(!isset($_COOKIE[$cookie1_name])) {	$currentid = "0";
} else {
    $currentid = $_COOKIE[$cookie1_name];
}
if(!isset($_COOKIE[$cookie2_name])) {
	$currentusername = "None Selected";
} else {
    $currentusername = $_COOKIE[$cookie2_name];
}
if(!isset($_COOKIE[$cookie3_name])) {
	$currentlocationid = "0";
} else {
	$currentlocationid = $_COOKIE[$cookie3_name];
	$siteid = $currentlocationid;
}
if(!isset($_COOKIE[$cookie4_name])) {
	$currentstorename = "None Selected";
} else {
    $currentstorename = $_COOKIE[$cookie4_name];
}
if($currentid < '1' or $currentlocationid < '1')
{
$header = "Location: index.php";
header($header);
}
if(isset($_GET['id']))
{
	$id = $_GET['id'];
}
else{
	$id = '0';
}
if($id == '12')
{
	//forward to statements or bfreports
	header('location:statements.php');
}if($id == '14')
{
	header('location:bfreports.php');
}
//default for today
$today = date("Y-m-d");
$tomorrow = date('Y-m-d',strtotime($date1 . "+1 days"));
if(isset($_GET['startdate']))
	{
		$startdate = $_GET['startdate'];
		$enddate = $_GET['enddate'];
		$lmstartdate = $startdate;
		$lmenddate = $enddate;
	}
else {
	$startdate = date('Y-m-d', strtotime('-1 month', strtotime($today)));
	$enddate = date('Y-m-d', strtotime('+1 day', strtotime($today)));

	$month_ini = new DateTime("first day of last month");
	$month_end = new DateTime("last day of last month");
	$lmstartdate = $month_ini->format('Y-m-d');
	$lmenddate = $month_end->format('Y-m-d');
}
if(isset($_POST['paymentsubmit']))
{
$invoiceid = $_POST['invoiceid'];
$paymenttype = $_POST['paymenttype'];
$paymentlineid = $_POST['paymentlineid'];
$startdate = $_POST['startdate'];
$enddate = $_POST['enddate'];
$newdate = $_POST['newinvoicedate'];
$skipdate = '1';
$paymentamount = $_POST['newpaymentamount'];
$newchecknumber = $_POST['newchecknumber'];
if($paymenttype == '10a')
{
	$newchecknumber = 'Discover';
	$paymenttype = '10';
}
if($paymenttype == '10b')
{
	$newchecknumber = 'Mastercard';
	$paymenttype = '10';
}
if($paymenttype == '10c')
{
	$newchecknumber = 'Visa';
	$paymenttype = '10';
}
if($paymenttype == '10d')
{
	$newchecknumber = 'American Express';
	$paymenttype = '10';
}
$negpaymentamount = $paymentamount * -1;

//getinvinfo
$sql1 = 'SELECT `accountid`,`location` FROM `invoice` WHERE `id` = :invoiceid';
$sth1 = $pdocxn->prepare($sql1);
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
	$accountid = $row1['accountid'];
	$siteid = $row1['location'];
}
$sql1 = 'SELECT `id` FROM `journal` WHERE `invoiceid` = :invoiceid';
$sth1 = $pdocxn->prepare($sql1);
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->execute();
if ($sth1->rowCount() > 0)
{
$sth1 = $pdocxn->prepare('UPDATE `journal` SET `total`=:total,`invoicedate`=:invoicedate WHERE `invoiceid` = :invoiceid');
$sth1->bindParam(':total',$negpaymentamount);
$sth1->bindParam(':invoicedate',$newdate);
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->execute();
}
else{
	
$sql2 = 'INSERT INTO `journal` (`invoiceid`,`accountid`,`total`,`invoicedate`,`journaltype`,`siteid`) VALUES (:invoiceid,:accountid,:total,:invoicedate,:journaltype,:siteid)';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':invoiceid',$invoiceid);
$sth2->bindParam(':accountid',$accountid);
$sth2->bindParam(':total',$negpaymentamount);
$sth2->bindParam(':invoicedate',$newdate);
$sth2->bindParam(':journaltype',$paymenttype);
$sth2->bindParam(':siteid',$siteid);
$sth2->execute()or die(print_r($sth2->errorInfo(), true));
}


$sql1 = 'UPDATE `line_items` SET `totallineamount`=:totallineamount,`comment`=:comment,`lineitem_typeid`=:lineitem_typeid WHERE `id` = :paymentlineid';
$sth1 = $pdocxn->prepare($sql1);
$sth1->bindParam(':totallineamount',$paymentamount);
$sth1->bindParam(':comment',$newchecknumber);
$sth1->bindParam(':lineitem_typeid',$paymenttype);
$sth1->bindParam(':paymentlineid',$paymentlineid);
$sth1->execute();

$sql1 = 'UPDATE `invoice` SET `invoicedate`=:invoicedate WHERE `id` = :invoiceid';
$sth1 = $pdocxn->prepare($sql1);
$sth1->bindParam(':invoicedate',$newdate);
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->execute();


}
if(isset($_GET['date']))
{
	$posteddate = $_GET['date'];
}
else{
	$posteddate = date("Y-m-d");
}
//submitted form
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" >
<link rel="stylesheet" type="text/css" href="style/globalstyle.css" >
<link rel="stylesheet" type="text/css" href="style/accountingreportstyle.css" >
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
<div id="selecteduser"><form name="current1" action="index.php" method="POST"><table id="floatleft"><tr><td class="currentuser">Current User:</td><td class="currentitem"><div class="styled-select black rounded">
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
<?php



//report1
if($id == '1')
{
	if(isset($_GET['newdate']))
	{
		$displaydate2 = $_GET['newdate'];
	}
	else{
		$displaydate2 = date('Y-m-d');
	}
	if(isset($_GET['type']))
	{
		$selectedtype = $_GET['type'];
	}
	else{
		$selectedtype = '1';
	}
	$prevday = date('Y-m-d', strtotime('-1 day', strtotime($displaydate2)));
	$nextday = date('Y-m-d', strtotime('+1 day', strtotime($displaydate2)));
?>
<div id="content">
        	<div id="left">
			<table><tr><td><a href="accountingreports.php?id=1&newdate=<?php echo $prevday."&type=".$selectedtype; ?>"><input type="button" class="smallbutton" alt="previous day" value="Prev Day"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td><form action="accountingreports.php" method="GET">
			<input type="hidden" name="id" value="1">
			<input type="date" name="newdate" value="<?php echo $displaydate2; ?>">
			<input type="submit" class="smallbutton" name="submit" value="Update"></form></td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="accountingreports.php?id=1&newdate=<?php echo $nextday."&type=".$selectedtype; ?>"><input type="button" class="smallbutton" alt="next day" value="Next Day"></a></td>
			<td><form name="invoicetype" action="accountingreports.php" method="GET"><input type="hidden" name="newdate" value="<?php echo $displaydate2; ?>"><input type="hidden" name="id" value="1"><select name="type" onchange="form.submit()"><option value="1">Invoice</option><option value="4">Quotes</option><option value="11">Work Order</option><option value="6">Payment</option><option value="18">Refund</option><option value="17">Credit Invoices</option><option value="7">Adjustment</option>
</select></form></td></tr></table>
<table id="highlightTable" class="blueTable">
<thead>
<tr>
<th>Date</th>
<th>Customer Name</th>
<th>Transaction Type</th>
<th>Total</th>
</tr>
</thead>
<tbody>

<?php

if($showvoided == '1')
{
	$isvoided = '';
}
else {
	$isvoided = ' AND `voiddate` IS NULL';
}
$sql1 = 'SELECT `id`,`invoiceid`,`accountid`,`location`,`total`,`type` FROM `invoice` WHERE `type` = :selectedtype AND `location` = :siteid AND `invoicedate` = :invoicedate AND `voiddate` IS NULL ORDER BY `id` DESC';
//echo 'SELECT `id`,`accountid`,`location` FROM `invoice` WHERE `invoicedate` = \''.$selecteddate.'\' AND `type` = \'6\''.$locationsql.$isvoided.' ORDER BY `id` DESC';
$sth1 = $pdocxn->prepare($sql1);
$sth1->bindParam(':siteid',$siteid);
$sth1->bindParam(':invoicedate',$displaydate2);
$sth1->bindParam(':selectedtype',$selectedtype);
$sth1->execute();
while ($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$accountid = $row1['accountid'];
	$paymentid = $row1['id'];
	$invoiceid = $row1['invoiceid'];
	$locationid = $row1['location'];
	$total = $row1['total'];
	$tinvtypeid = $row1['type'];
	
	if($selectedtype == '1')
	{
		$paymentid = $invoiceid;
	}
	$getname = $pdocxn->prepare('SELECT `firstname`,`lastname` from `accounts` WHERE `accountid` = :accountid');
$getname->bindParam(':accountid',$accountid);
$getname->execute();
while($getnamerow = $getname->fetch(PDO::FETCH_ASSOC))
{
$databasefname = $getnamerow['firstname'];
$firstname = stripslashes($databasefname);
$databaselname = $getnamerow['lastname'];
$lastname = stripslashes($databaselname);
$customername = $firstname." ".$lastname;	
}
$sql2 = 'SELECT `id`,`name` FROM `invoice_type` WHERE `id` = :id';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':id',$tinvtypeid);
$sth2->execute();
while ($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
	{
	$invtype = $row2['name'];
	echo "<tr><td>".$displaydate2."</td><td>".$customername."</td><td>".$invtype." &nbsp;&nbsp;#".$paymentid."</td><td>".$total."</td></tr>";
	}
}
?></tbody></table></div>
<div class="right">
<?php
while ($tri > 0) {
        echo ${"ip".$tri};
		$tri --;
}
?>
</div>
</div>
<?php
}



//report2
else if($id == '2')
{
?>
<div id="content">
<form name="updatepaymentreport" action="accountingreports.php?id=2" method="GET">
<table><tr><td>
Start Date:</td><td><input type="date" name="startdate" value="<?php echo $startdate; ?>"></td>
<td>End Date:</td><td><input type="date" name="enddate" value="<?php echo $enddate; ?>"></td>
<td><Select name="type"><option value="0">Show All</option><option value="8">Cash</option></option></Select></td>
<td><input type="hidden" name="id" value="2"><input type="hidden" name="submit" value="submit"><input type="submit" class="save" name="update" value="Update Search"></td></tr>
</table>
</form>
        	<div id="left">
<table id="highlightTable" class="blueTable">
<thead>
<tr>
<th id="tc1">Date</th>
<th id="tc2">Customer Name</th>
<th id="tc3">Type</th>
<th id="tc4">Total</th>
</tr>
</thead>
<tbody>
<?php
if($showvoided == '1')
{
	$isvoided = '';
}
else {
	$isvoided = ' AND `voiddate` IS NULL';
}
$sql1 = 'SELECT `id`,`accountid`,`location`,`invoicedate` FROM `invoice` WHERE `invoicedate` >= :startdate AND `invoicedate` <= :enddate AND `type` = \'6\' AND `location` = :currentlocationid'.$isvoided.' ORDER BY `id` DESC';
$sth1 = $pdocxn->prepare($sql1);
$sth1->bindParam(':startdate',$startdate);
$sth1->bindParam(':enddate',$enddate);
$sth1->bindParam(':currentlocationid',$currentlocationid);
$sth1->execute();

if($sth1->rowCount() > 0) {
while ($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$accountid = $row1['accountid'];
	$paymentid = $row1['id'];
	$locationid = $row1['location'];
	$dbdate = $row1['invoicedate'];
	$displaydate = date('m/d/Y', strtotime($dbdate));
	
$getname = $pdocxn->prepare('SELECT `firstname`,`lastname` from `accounts` WHERE `accountid` = :accountid');
$getname->bindParam(':accountid',$accountid);
$getname->execute();
while($getnamerow = $getname->fetch(PDO::FETCH_ASSOC))
{
$databasefname = $getnamerow['firstname'];
$firstname = stripslashes($databasefname);
$databaselname = $getnamerow['lastname'];
$lastname = stripslashes($databaselname);
if($firstname > '0')
{
$customername =$firstname." ".$lastname;	
}else{
	$customername = $lastname;	
}}
$sql2 = 'SELECT `id`,`invoiceid`,`comment`,`totallineamount`,`lineitem_typeid` FROM `line_items` WHERE `invoiceid` = :invoiceid ORDER BY `linenumber` ASC';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':invoiceid',$paymentid);
$sth2->execute();
$paymentrowcount = $sth2->rowcount();
if($paymentrowcount > 0) {
while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
	{
	$currentchecknum = '0';
	$paymenttype = $row2['comment'];
	$paymentamount = $row2['totallineamount'];
	$paymentlineid = $row2['id'];
	$paymentid2 = $row2['invoiceid'];
	$lineitem_typeid = $row2['lineitem_typeid'];
	if($lineitem_typeid == '8')
	 {
	 	$cash =  $cash + $paymentamount;
		$paymenttype = "Cash";
		if($paymentrowcount > 1) {
		$sqlchange = 'SELECT `id`,`totallineamount`,`lineitem_typeid` FROM `line_items` WHERE `invoiceid` = :invoiceid';
$sthchange = $pdocxn->prepare($sqlchange);
$sthchange->bindParam(':invoiceid',$paymentid);
$sthchange->execute();
while($rowchange = $sthchange->fetch(PDO::FETCH_ASSOC))
	{
	$checkchangeid = $rowchange['lineitem_typeid'];
	$checkchangeamount = $rowchange['totallineamount'];
	if($checkchangeid == '25')
	{
		$subtractcash = $subtractcash + $checkchangeamount;
	}}}}
	if($lineitem_typeid == '9')
	{
		$currentchecknum = $paymenttype;
		$checknum = $paymenttype;
		$paymenttype = "Check #".$paymenttype; 
		$check =  $check + $paymentamount;
	}
	if($lineitem_typeid == '10')
	{
		$creditcard = $creditcard + $paymentamount;
	}
	if($lineitem_typeid == '27')
	{
		$paymenttype = "CFNA"; 
		$cfna = $cfna + $paymentamount;
	}
	if($lineitem_typeid == '25')
	{
		$paymenttype = "Change Given"; 
		$change = $change + $paymentamount;
	}
	$totalpaymentamount = $paymentamount;
	if($paymentrowcount > '1')
	{
		$sql3 = 'SELECT SUM(`totallineamount`) AS `sum` FROM `line_items` WHERE `invoiceid` = :paymentid2';
		$sth3 = $pdocxn->prepare($sql3);
		$sth3->bindParam(':paymentid2',$paymentid2);
		$sth3->execute()or die(print_r($sth3->errorInfo(), true));
		while($row3 = $sth3->fetch(PDO::FETCH_ASSOC))
		{
			$totalpaymentamount = $row3['sum'];
		}}else{
			$totalpaymentamount = $paymentamount;
		}
		if($lineitem_typeid == '25')
		{}else{
	echo "\n<tr href=\"ip".$tri."\"><td><b>".$displaydate."</b></td><td><b>".$customername."</b></td><td><b>".$paymenttype."</b></td><td><b>".$paymentamount."</b></td></tr>";

${"ip".$tri} = "<div id=\"ip".$tri."\"><table><tr><td><form name=\"form".$tri."\" action\"accountingreports.php\" method=\"POST\"><select name=\"paymenttype\"> <option value=\"".$lineitem_typeid."\">".$paymenttype."</option>";
if($lineitem_typeid != '8'){
${"ip".$tri} .= "<option value=\"8\">Cash</option>";
}
if($lineitem_typeid != '9'){
${"ip".$tri} .= "<option value=\"9\">Check</option>";
}
if($lineitem_typeid != '27'){
	${"ip".$tri} .= "<option value=\"27\">CFNA</option>";
}
${"ip".$tri} .= "<option value=\"10a\">Discover</option>";
${"ip".$tri} .= "<option value=\"10b\">Mastercard</option>";
${"ip".$tri} .= "<option value=\"10c\">Visa</option>";
${"ip".$tri} .= "<option value=\"10d\">American Express</option>";
${"ip".$tri} .= "</select><input type=\"hidden\" name=\"id\" value=\"2\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$paymentid."\"><input type=\"hidden\" name=\"paymentlineid\" value=\"".$paymentlineid."\"><input type=\"hidden\" name=\"startdate\" value=\"".$startdate."\"><input type=\"hidden\" name=\"enddate\" value=\"".$enddate."\"><input type=\"hidden\" name=\"paymentsubmit\" value=\"1\"></td>";
${"ip".$tri} .= "<td><input type=\"textbox\" name=\"newpaymentamount\" value=\"".$paymentamount."\" class=\"mediuminput\"></td></tr><tr><td>";
if($currentchecknum > '0')
{
${"ip".$tri} .= "<input type=\"textbox\" name=\"newchecknumber\" value=\"$currentchecknum\" class=\"mediuminput\">";
}else
{
${"ip".$tri} .= "<input type=\"textbox\" name=\"newchecknumber\" placeholder=\"check #\" class=\"mediuminput\">";
}
${"ip".$tri} .= "</td><td><input type=\"date\" name=\"newinvoicedate\" value=\"".$dbdate."\"></tr>";

${"ip".$tri} .= "<tr><td colspan=\"3\"><input type=\"submit\" class=\"smallbutton\" value=\"Update payment\"></form></td></tr></table></div>\n";
$tri++;
		}}}}
}

else{
echo "\n<tr><td colspan=\"4\">No Results for the selected dates, Please try again</td></tr>";
}
?></tbody><tfoot><tr><td colspan="4"><br /><br /><br /><br /></td></tr></tfoot></table></div>
<div class="right">
<div class="q1">
	<?php
	$cash = $cash + $subtractcash;
	$displaycash = money_format('%(#0.2n',$cash);
	$displaycheck = money_format('%(#0.2n',$check);
	$displaycreditcard = money_format('%(#0.2n',$creditcard);
	$displaycfna = money_format('%(#0.2n',$cfna);
	$displaychange = money_format('%(#0.2n',$change);
	$totalamount = $cash + $check + $creditcard + $cfna + $change;
	$totaldeposit = $cash + $check + $change;
	$displaytotaldeposit = money_format('%(#0.2n',$totaldeposit);
	$displaytotal = money_format('%(#0.2n',$totalamount);
	?>
	<table>
	<tr><td><b>Cash:</b></td><td><b>$<?php echo $displaycash; ?></b></td></tr>
	<tr><td><b>Check:</b></td><td><b>$<?php echo $displaycheck; ?></b></td></tr>
	<tr><td><b>Credit Card:</b></td><td><b>$<?php echo $displaycreditcard; ?></b></td></tr>
	<tr><td><b>CFNA:</b></td><td><b>$<?php echo $displaycfna; ?></b></td></tr>
	<tr><td><br /></td></tr>
	<tr><td><b>Change Give:</b><td><b>$<?php echo $displaychange; ?></b></td></tr>
	<tr><td><br /></td></tr>
	<tr><td><b>Total Deposit Amount:</b></td><td><b>$<?php echo $displaytotaldeposit; ?></b></td></tr>
	<tr><td><b>Total Payments:</b></td><td><b>$<?php echo $displaytotal; ?></b></td></tr>
	</table>
</div>
<div class="q3">
<?php
while($tri > '0')
{
echo ${"ip".$tri};
$tri--;
}
?>
</div>
</div>

</div>
<?php
}
//report4
else if($id == '4')
{
?>
 <div id="content">
        	<div id="left">
        		<form name="update" action="accountingreports.php" method="GET">Start Date: <input type="date" name="startdate" value="<?php echo $startdate; ?>">End Date: <input type="date" name="enddate" value="<?php echo $enddate; ?>"><input type="hidden" name="id" value="4"><input type="submit" class="smallbutton" value="Update Dates"></form>
<table id="highlightTable" class="blueTable">
<thead>
<tr>
<th id="tc1">Standing</th>
<th id="tc2">Customer Name</th>
<th id="tc3">Number of Invoices</th>
<th id="tc4">Total Spent</th>
</tr>
</thead>
<tbody>
<?php
$sql2 = 'SELECT * FROM `invoice` WHERE `invoicedate` = :invoicedate ORDER BY `id` DESC';

if($showvoided == '1')
{
	$isvoided = '';
}
else {
	$isvoided = ' AND `voiddate` IS NULL';
}

$sql1 = 'SELECT `accountid`, COUNT(accountid) AS invcount, SUM(total) AS totalspent FROM `invoice` WHERE `type` = \'1\' AND `invoicedate` > :startdate AND `invoicedate` < :enddate GROUP BY `accountid` ORDER BY COUNT(accountid) DESC LIMIT 100 ';
//echo 'SELECT `id`,`accountid`,`location` FROM `invoice` WHERE `invoicedate` = \''.$selecteddate.'\' AND `type` = \'6\''.$locationsql.$isvoided.' ORDER BY `id` DESC';
$sth1 = $pdocxn->prepare($sql1);
$sth1->bindParam(':startdate',$startdate);
$sth1->bindParam(':enddate',$enddate);
$sth1->execute();
while ($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$accountid = $row1['accountid'];
	$invcount = $row1['invcount'];
	$totalspent = $row1['totalspent'];
	
	$getname = $pdocxn->prepare('SELECT `firstname`,`lastname` from `accounts` WHERE `accountid` = :accountid');
$getname->bindParam(':accountid',$accountid);
$getname->execute();
while($getnamerow = $getname->fetch(PDO::FETCH_ASSOC))
{
$databasefname = $getnamerow['firstname'];
$firstname = stripslashes($databasefname);
$databaselname = $getnamerow['lastname'];
$lastname = stripslashes($databaselname);
$customername =$firstname." ".$lastname;	
}
	echo "<tr href=\"ip".$tri."\"><td>".$tri."</td><td>".$customername."</td><td>".$invcount."</td><td>".$totalspent."</td></tr>";
	$tri++;
	}
?>
<tr><td><br /><br /></td></tr></tbody></table></div>
</div>
<?php
}
//report5
else if($id == '5')
{
	$r = $_GET['r'];
	if($r == '1')
	{
	$sql1 = 'SELECT `invoiceid` FROM `invoice` WHERE `type` = \'1\' AND `invoicedate` > :startdate AND `invoicedate` < :enddate AND `voiddate` IS NULL';
$sth1 = $pdocxn->prepare($sql1);
$sth1->bindParam(':startdate',$startdate);
$sth1->bindParam(':enddate',$enddate);
$sth1->execute();
while ($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$invoiceid = $row1['invoiceid'];
	$sql2 = 'INSERT INTO `temp-toptire1` (`invoiceid`),VALUES(:invoiceid)';
	$sth2 = $pdocxn->prepare($sql2);
	$sth2->bindparam(':invoiceid',$invoiceid);
	$sth2->execute();
	}
	header('location:accountingreports.php?id=5&r=2')
	}else if ($r == '2'){
		$sql1 = 'SELECT `invoiceid` FROM `temp-toptire1`';
		$sth1 = $pdocxn->prepare($sql1);
		$sth1->execute();
		while ($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
			{
			$invoiceid = $row1['invoiceid'];
			$sql2 = 'SELECT `partid`,`qty` FROM `line_items` WHERE `invoiceid` = :invoiceid';
			$sth2 = $pdocxn->prepare($sql2);
			$sth2->bindparam(':invoiceid',$invoiceid);
			$sth2->execute();
			while ($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
			{
			$partid = $row2['partid'];
			$qty = $row2['qty'];
			$sql3 = 'INSERT INTO `temp-toptire2` (`partid`,`qty`),VALUES(:partid,:qty)';
			$sth3 = $pdocxn->prepare($sql3);
			$sth3->bindparam(':partid',$partid);
			$sth3->bindparam(':qty',$qty);
			$sth3->execute();
			}}
			header('location:accountingreports.php?id=5&r=3')
		}else
		{
			
?>
 <div id="content">
        	<div id="left">
        		<form name="update" action="accountingreports.php" method="GET">Start Date: <input type="date" name="startdate" value="<?php echo $startdate; ?>">End Date: <input type="date" name="enddate" value="<?php echo $enddate; ?>"><input type="hidden" name="id" value="5"><input type="submit" class="smallbutton" value="Update Dates"></form>
<table id="highlightTable" class="blueTable">
<thead>
<tr>
<th id="tc1">Standing</th>
	   <th id="tc2">Tire</th>
	   <th id="tc3">Number of Invoices</th>
	   <th id="tc4">Total QTY sold</th>
</tr>
</thead>
<tbody>
<?php
$sql1 = 'SELECT `accountid`, COUNT(accountid) AS invcount, SUM(qty) AS totaltire FROM `temp-toptire2` GROUP BY `partid` ORDER BY COUNT(qty) DESC LIMIT 100 ';
//echo 'SELECT `id`,`accountid`,`location` FROM `invoice` WHERE `invoicedate` = \''.$selecteddate.'\' AND `type` = \'6\''.$locationsql.$isvoided.' ORDER BY `id` DESC';
$sth1 = $pdocxn->prepare($sql1);
$sth1->bindParam(':startdate',$startdate);
$sth1->bindParam(':enddate',$enddate);
$sth1->execute();
while ($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$accountid = $row1['accountid'];
	$invcount = $row1['invcount'];
	$totalspent = $row1['totalspent'];
	
	$getname = $pdocxn->prepare('SELECT `firstname`,`lastname` from `accounts` WHERE `accountid` = :accountid');
$getname->bindParam(':accountid',$accountid);
$getname->execute();
while($getnamerow = $getname->fetch(PDO::FETCH_ASSOC))
{
$databasefname = $getnamerow['firstname'];
$firstname = stripslashes($databasefname);
$databaselname = $getnamerow['lastname'];
$lastname = stripslashes($databaselname);
$customername =$firstname." ".$lastname;	
}
	echo "<tr href=\"ip".$tri."\"><td>".$tri."</td><td>".$tire."</td><td>".$invcount."</td><td>".$totaltire."</td></tr>";
	$tri++;
	}
?>
<tr><td><br /><br /></td></tr></tbody></table></div>
</div>
<?php
	echo "<tr href=\"ip".$tri."\"><td>".$tri."</td><td>".$tire."</td><td>".$invcount."</td><td>".$totalspent."</td></tr>";
	$tri++;
?>
<tr><td><br /><br /></td></tr></tbody></table></div>
</div>
<?php

}
}
//report7
else if($id == '7')
{
?>
<div id="content">
        	<div id="left">
			<form name="update" action="accountingreports.php" method="GET">Start Date: <input type="date" name="startdate" value="<?php echo $startdate; ?>">End Date: <input type="date" name="enddate" value="<?php echo $enddate; ?>"><input type="hidden" name="id" value="7"><input type="submit" class="smallbutton" value="Update Dates"></form>
<table id="highlightTable" class="blueTable">
<thead>
<tr>
<th>Subtotal</th>
<th>Tax Total</th>
<th>Total Sales</th>
</tr>
</thead>
<tbody>

<?php
$ss = '0';
$tt = '0';
$total = '0';
$sql1 = 'SELECT SUM(`subtotal`) AS `ss`,SUM(`tax`) AS `tt`,SUM(`total`) AS `total` FROM `invoice` WHERE `type` = \'1\' AND `invoicedate` > :startdate AND `invoicedate` < :enddate AND `location` = :siteid AND `voiddate` IS NULL';
$sth1 = $pdocxn->prepare($sql1);
$sth1->bindParam(':startdate',$startdate);
$sth1->bindParam(':enddate',$enddate);
$sth1->bindParam(':siteid',$siteid);
$sth1->execute();
while ($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$ss = $row1['ss'];
	$tt = $row1['tt'];
	$total = $row1['total'];
	$total = $ss + $tt;
	$subtotal = number_format($ss, 2, '.', ',');
	$taxtotal = number_format($tt, 2, '.', ',');
	$totalsales = number_format($total, 2, '.', ',');
$getname = $pdocxn->prepare('SELECT `firstname`,`lastname` from `accounts` WHERE `acctid` = :accountid');
$getname->bindParam(':accountid',$accountid);
$getname->execute();
while($getnamerow = $getname->fetch(PDO::FETCH_ASSOC))
{
$databasefname = $getnamerow['firstname'];
$firstname = stripslashes($databasefname);
$databaselname = $getnamerow['lastname'];
$lastname = stripslashes($databaselname);
$customername =$firstname." ".$lastname;	
}

	echo "<tr><td>".$subtotal."</td><td>".$taxtotal."</td><td>".$totalsales."</td></tr>";
}
?></tbody></table></div>
</div>
<?php
}

//report8
else if($id == '8')
{
?>
<div id="content">
        	<div id="left">
			<form name="update" action="accountingreports.php" method="GET">Start Date: <input type="date" name="startdate" value="<?php echo $startdate; ?>">End Date: <input type="date" name="enddate" value="<?php echo $enddate; ?>"><input type="hidden" name="id" value="8"><input type="submit" class="smallbutton" value="Update Dates"></form>
<table id="highlightTable" class="blueTable">
<thead>
<tr>
<th id="tc1">Tax Code</th>
<th id="tc4">Qty of Invoices</th>
<th id="tc2">Total Sales</th>
<th id="tc3">Total Tax</th>
</tr>
</thead>
<tbody>

<?php
$tt = '0';
$ss = '0';

$sql1 = 'SELECT SUM(`tax`) AS `tt`,SUM(`subtotal`) AS `ss`,`taxgroup` FROM `invoice` WHERE `type` = \'1\' AND `invoicedate` > :startdate AND `invoicedate` < :enddate AND `location` = :siteid AND `voiddate` IS NULL GROUP BY `taxgroup`';

$sth1 = $pdocxn->prepare($sql1);
$sth1->bindParam(':startdate',$startdate);
$sth1->bindParam(':enddate',$enddate);
$sth1->bindParam(':siteid',$siteid);
$sth1->execute();
while ($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$taxgroup = $row1['taxgroup'];
	$tt = $row1['tt'];
	$ss = $row1['ss'];
	$taxtotal = number_format($tt, 2, '.', ',');
	$subtotal = number_format($ss, 2, '.', ',');
$getname = $pdocxn->prepare('SELECT `firstname`,`lastname` from `accounts` WHERE `acctid` = :accountid');
$getname->bindParam(':accountid',$accountid);
$getname->execute();
while($getnamerow = $getname->fetch(PDO::FETCH_ASSOC))
{
$databasefname = $getnamerow['firstname'];
$firstname = stripslashes($databasefname);
$databaselname = $getnamerow['lastname'];
$lastname = stripslashes($databaselname);
$customername =$firstname." ".$lastname;	
}

$sql2 = 'SELECT `id`,`description` FROM `tax_rate` WHERE `id` = :id';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':id',$taxgroup);
$sth2->execute();
while ($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
	{
$sql3 = 'SELECT `id` FROM `invoice` WHERE `type` = \'1\' AND `invoicedate` > :startdate AND `invoicedate` < :enddate AND `location` = :siteid AND `taxgroup` = :taxgroup AND `voiddate` IS NULL ';
$sth3 = $pdocxn->prepare($sql3);
$sth3->bindParam(':startdate',$startdate);
$sth3->bindParam(':enddate',$enddate);
$sth3->bindParam(':siteid',$siteid);
$sth3->bindParam(':taxgroup',$taxgroup);
$sth3->execute();
$numinv = $sth3->rowCount();
$taxname = $row2['description'];
	echo "<tr><td>".$taxname."</td><td>".$numinv."</td><td>".$subtotal."</td><td>".$taxtotal."</td></tr>";
	}
	$subtotal = '0';
}
?></tbody></table></div>
</div>
<?php
}

//report9
else if($id == '9')
{
?>
<div id="content">
        	<div id="left">
<table id="highlightTable" class="blueTable">
<thead>
<tr>
<th id="tc1">Customer Name</th>
</tr>
</thead>
<tbody>
<?php


$sql1 = 'SELECT `accountid`,`firstname`,`lastname` FROM `accounts` WHERE `nationalaccount` = \'1\' ORDER BY `firstname` ASC';
$sth1 = $pdocxn->prepare($sql1);
$sth1->execute();

if($sth1->rowCount() > 0) {
while ($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
$accountid = $row1['accountid'];
$databasefname = $row1['firstname'];
$firstname = stripslashes($databasefname);
$databaselname = $row1['lastname'];
$lastname = stripslashes($databaselname);
if($firstname > '0')
{
$customername =$firstname." ".$lastname;	
}else{
	$customername = $lastname;	
}


	echo "\n<tr href=\"ip".$tri."\"><td><b>".$customername."</b></td></tr>";

${"ip".$tri} = "<div id=\"ip".$tri."\"><table><tr><td><a href=\"editaccount.php?accountid=".$accountid."\"><button class=\"smallbutton\">edit account</button></a></td></tr><tr><td><a href=\"accountingreports.php?id=10&accountid=".$accountid."\"><button class=\"smallbutton\">Show Invoices from this account</button></a></td></tr></table></div>\n";
$tri++;
		}}

else{
echo "\n<tr><td colspan=\"4\">No Results for the selected dates, Please try again</td></tr>";
}
?></tbody><tfoot><tr><td colspan="4"><br /><br /><br /><br /></td></tr></tfoot></table></div>
<div class="right">
<div class="q1">
</div>
<div class="q3">
<?php
while($tri > '0')
{
echo ${"ip".$tri};
$tri--;
}
?>
</div>
</div>

</div>
<?php
}

//report10
else if($id == '10')
{
	if(isset($_GET['accountid']))
	{
		$accountid = $_GET['accountid'];
	}
	if(isset($_POST['accountid']))
	{
		$accountid = $_POST['accountid'];
	}
?>

<div id="content">
<div id="left10">
<form name="update" action="accountingreports.php" method="GET">Start Date: <input type="date" name="startdate" value="<?php echo $startdate; ?>">End Date: <input type="date" name="enddate" value="<?php echo $enddate; ?>">
<?php
if($accountid > '0')
{
	echo "<input type=\"hidden\" name=\"accountid\" value=\"$accountid\">";
}
?>
<input type="hidden" name="id" value="10"><input type="submit" class="smallbutton" value="Update Dates"></form>
<table id="highlightTable" class="blueTable">
<thead>
<tr>
<th id="tc1">Store</th>
<th id="tc2">Date</th>
<th id="tc3">Customer Name</th>
<th id="tc4">Invoice Number</th>
<th id="tc5">PO Number</th>
<th id="tc6">Vehicle</th>
</tr>
</thead>
<tbody>
<?php
$tri = '1';
if($voidsql == '1')
{
$void = "";
}
else{
$void = "AND `voiddate` IS NULL ";
}
if($accountid > '0')
{


	$sql1 = 'SELECT `accountid`,`firstname`,`lastname` FROM `accounts` WHERE `accountid` = :accountid';
	$sth1 = $pdocxn->prepare($sql1);
	$sth1->bindParam(':accountid',$accountid);
	$sth1->execute();
	while ($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$accountid = $row1['accountid'];
	$databasefname = $row1['firstname'];
	$firstname = stripslashes($databasefname);
	$databaselname = $row1['lastname'];
	$lastname = stripslashes($databaselname);
	if($firstname > '0')
	{
	$customername =$firstname." ".$lastname;	
	}else{
		$customername = $lastname;	
	}

	$sql2 = 'SELECT `id`,`location`,`invoicedate`,`voiddate`,`vehicleid`,`invoiceid`,`ponumber`,`naflag`,`nacomplete` FROM `invoice` WHERE `accountid` = :accountid AND `type` = \'1\'AND `invoicedate` > :startdate AND `invoicedate` < :enddate AND `voiddate` IS NULL ORDER BY `invoicedate` DESC LIMIT 5';
	$sth2 = $pdocxn->prepare($sql2);
	$sth2->bindParam(':accountid',$accountid);
	$sth2->bindParam(':startdate',$startdate);
	$sth2->bindParam(':enddate',$enddate);
	$sth2->execute();
	if($sth2->rowCount() > 0) {
	while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
	{
	$siteid = $row2['location'];
	$vehicleid1 = $row2['vehicleid'];
	$ponumber = $row2['ponumber'];
	$invoicenumber = $row2['invoiceid'];
	$date = $row2['invoicedate'];
	$id = $row2['id'];
	$naflag = $row2['naflag'];
	$nacomplete = $row2['nacomplete'];
	if($siteid == '1')
	{
		$store = 'Rapids';
	}else{
		$store = 'Plover';
	}
	if($nacomplete == '1')
	{}else{
	$getvehicle = $pdocxn->prepare('SELECT `year`,`make`,`model`,`description` from `vehicles` WHERE `id` = :vehicleid');
	$getvehicle->bindParam(':vehicleid',$vehicleid1);
	$getvehicle->execute();
	if ($getvehicle->rowCount() > 0)
	{
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
	}}}else
	{
	$abvvehicle = '';
	}
	
	${"ip".$tri} = "\n<div id=\"$tri\"><div class=\"q3\"><table class=\"righttable\"><tr><td class=\"center\"><a href=\"printinvoice2.php?invoiceid=".$id."\" target=\"_BLANK\"><button class=\"smallbutton\">Print</button></a></td><td class=\"center\"><a href=\"invoice.php?invoiceid=".$id."\" target=\"_BLANK\"><button class=\"smallbutton\">View Invoice</button></a></td></tr>\n";
	${"ip".$tri} .= "\n<tr><td class=\"center\"><a href=\"flagnationalaccount.php?invoiceid=".$id."&complete=1\" target=\"_BLANK\"><button class=\"smallbutton\">Mark as Completed</button></a></td><td class=\"center\"><a href=\"flagnationalaccount.php?invoiceid=".$id."&flag=1\" target=\"_BLANK\"><button class=\"smallbutton\">Flag Invoice</button></a></td></tr></table>\n";
	${"ip".$tri} .= "</div></div>";
	if($naflag == '1')
	{
	echo "<tr href=\"$tri\"><td><p class=\"warningfontcolorb\">$store</p></td><td><p class=\"warningfontcolorb\">$date</p></td><td><p class=\"warningfontcolorb\">".$customername."</p></td><td><p class=\"warningfontcolorb\">$invoicenumber</p></td><td><p class=\"warningfontcolorb\">$ponumber</p></td><td><p class=\"warningfontcolorb\">$abvvehicle</p></td></tr>\n";
	}else{
		echo "<tr href=\"$tri\"><td><p class=\"boldfont\">$store</p></td><td><p class=\"boldfont\">$date</p></td><td><p class=\"boldfont\">".$customername."</p></td><td><p class=\"boldfont\">$invoicenumber</p></td><td><p class=\"boldfont\">$ponumber</p></td><td><p class=\"boldfont\">$abvvehicle</p></td></tr>\n";
	}$tri ++;
	}}
	}
	
	else{
	}}



















}else{
$sql1 = 'SELECT `accountid`,`firstname`,`lastname` FROM `accounts` WHERE `nationalaccount` = \'1\' ORDER BY `lastname` ASC';
$sth1 = $pdocxn->prepare($sql1);
$sth1->execute();
while ($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
$accountid = $row1['accountid'];
$databasefname = $row1['firstname'];
$firstname = stripslashes($databasefname);
$databaselname = $row1['lastname'];
$lastname = stripslashes($databaselname);
if($firstname > '0')
{
$customername =$firstname." ".$lastname;	
}else{
	$customername = $lastname;	
}

}
//${"ip".$tri} = "<div id=\"ip".$tri."\"><table><tr><td><a href=\"editaccount.php?accountid=".$accountid."\"><button class=\"smallbutton\">edit account</button></a></td></tr><tr><td><a href=\"accountingreports.php?id=10&accountid=".$accountid."\"><button class=\"smallbutton\">Show Invoices from this account</button></a></td></tr></table></div>\n";


$sql2 = 'SELECT `id`,`location`,`invoicedate`,`voiddate`,`vehicleid`,`invoiceid`,`ponumber`,`naflag`,`nacomplete` FROM `invoice` WHERE `accountid` = :accountid AND `type` = \'1\'AND `invoicedate` > :startdate AND `invoicedate` < :enddate AND `voiddate` IS NULL ORDER BY `invoicedate` DESC LIMIT 5';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':accountid',$accountid);
$sth2->bindParam(':startdate',$startdate);
$sth2->bindParam(':enddate',$enddate);
$sth2->execute();
if($sth2->rowCount() > 0) {
while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
{
$siteid = $row2['location'];
$vehicleid1 = $row2['vehicleid'];
$ponumber = $row2['ponumber'];
$invoicenumber = $row2['invoiceid'];
$date = $row2['invoicedate'];
$id = $row2['id'];
$naflag = $row2['naflag'];
$nacomplete = $row2['nacomplete'];
if($siteid == '1')
{
	$store = 'Rapids';
}else{
	$store = 'Plover';
}
if($nacomplete == '1')
{}else{
$getvehicle = $pdocxn->prepare('SELECT `year`,`make`,`model`,`description` from `vehicles` WHERE `id` = :vehicleid');
$getvehicle->bindParam(':vehicleid',$vehicleid1);
$getvehicle->execute();
if ($getvehicle->rowCount() > 0)
{
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
}}}else
{
$abvvehicle = '';
}

${"ip".$tri} = "\n<div id=\"$tri\"><div class=\"q3\"><table class=\"righttable\"><tr><td class=\"center\"><a href=\"printinvoice2.php?invoiceid=".$id."\" target=\"_BLANK\"><button class=\"smallbutton\">Print</button></a></td><td class=\"center\"><a href=\"invoice.php?invoiceid=".$id."\" target=\"_BLANK\"><button class=\"smallbutton\">View Invoice</button></a></td></tr>\n";
${"ip".$tri} .= "\n<tr><td class=\"center\"><a href=\"flagnationalaccount.php?invoiceid=".$id."&complete=1\" target=\"_BLANK\"><button class=\"smallbutton\">Mark as Completed</button></a></td><td class=\"center\"><a href=\"flagnationalaccount.php?invoiceid=".$id."&flag=1\" target=\"_BLANK\"><button class=\"smallbutton\">Flag Invoice</button></a></td></tr></table>\n";
${"ip".$tri} .= "</div></div>";
if($naflag == '1')
{
echo "<tr href=\"$tri\"><td><p class=\"warningfontcolorb\">$store</p></td><td><p class=\"warningfontcolorb\">$date</p></td><td><p class=\"warningfontcolorb\">".$customername."</p></td><td><p class=\"warningfontcolorb\">$invoicenumber</p></td><td><p class=\"warningfontcolorb\">$ponumber</p></td><td><p class=\"warningfontcolorb\">$abvvehicle</p></td></tr>\n";
}else{
	echo "<tr href=\"$tri\"><td><p class=\"boldfont\">$store</p></td><td><p class=\"boldfont\">$date</p></td><td><p class=\"boldfont\">".$customername."</p></td><td><p class=\"boldfont\">$invoicenumber</p></td><td><p class=\"boldfont\">$ponumber</p></td><td><p class=\"boldfont\">$abvvehicle</p></td></tr>\n";
}$tri ++;
}}
}

else{
}}
?><tr><td colspan="6"><br /><br /><br /><br /></td></tr></tbody></table>
</div><div class="right10">
<?php
while ($tri > 0) {
echo ${"ip".$tri};
$tri --;
}
?>
</div></div>
<?php
}


//report11
else if($id == '11')
{
	$inventorysales = '0';
	$laborsales = '0';
	$commentsales = '0';
	$adjustmentsales = '0';
?>
<div id="content">
        	<div id="left">
			<form name="update" action="accountingreports.php" method="GET">Start Date: <input type="date" name="startdate" value="<?php echo $lmstartdate; ?>">End Date: <input type="date" name="enddate" value="<?php echo $lmenddate; ?>"><input type="hidden" name="id" value="11"><input type="submit" class="smallbutton" value="Update Dates"></form>
<table id="highlightTable" class="blueTable">
<thead>
<tr>
<th id="tc1">Inventory Totals</th>
<th id="tc2">Labor/Service</th>
<th id="tc3">Comment</th>
<th id="tc4">Adjustments</th>
</tr>
</thead>
<tbody>

<?php

$sql1 = 'SELECT `id` FROM `invoice` WHERE `type` = \'1\' AND `invoicedate` > :startdate AND `invoicedate` < :enddate AND `location` = :siteid AND `voiddate` IS NULL';
$sth1 = $pdocxn->prepare($sql1);
$sth1->bindParam(':startdate',$lmstartdate);
$sth1->bindParam(':enddate',$lmenddate);
$sth1->bindParam(':siteid',$siteid);
$sth1->execute();
while ($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$invoiceid = $row1['id'];
	$sql2 = 'SELECT `id`,`comment`,`totallineamount`,`lineitem_typeid` FROM `line_items` WHERE `invoiceid` = :invoiceid';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':invoiceid',$invoiceid);
$sth2->execute();
while ($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
	{
	$comment = $row2['comment'];
	$lineamount = $row2['totallineamount'];
	$lineitem_typeid = $row2['lineitem_typeid'];
if($lineitem_typeid == '1')
	{
		$inventorysales = $inventorysales + $lineamount;
	}	
if($lineitem_typeid == '3')
	{
		$inventorysales = $inventorysales + $lineamount;
	}
if($lineitem_typeid == '4')
{
	$laborsales = $laborsales + $lineamount;
}
if($lineitem_typeid == '5')
{
	if(strpos($comment, 'labor'))
	{
	$laborsales = $laborsales + $lineamount;
}else{
	$commentsales = $commentsales + $lineamount;
}}
if($lineitem_typeid == '16')
{
	$adjustmentsales = $adjustmentsales + $lineamount;
}
	}

	$inventorysales2 = number_format($inventorysales, 2, '.', ',');
	$laborsales2 = number_format($laborsales, 2, '.', ',');
	$commentsales2 = number_format($commentsales, 2, '.', ',');
	$adjustmentsales2 = number_format($adjustmentsales, 2, '.', ',');
}
	echo "<tr><td>".$inventorysales2."</td><td>".$laborsales2."</td><td>".$commentsales2."</td><td>".$adjustmentsales2."</td></tr>";

?></tbody></table></div>
</div>
<?php
}
//report15
else if($id == '15')
{
if($_GET['d'])
{
	$setdate = $_GET['d'];
	$echodate = date("n/j/Y", strtotime($setdate));
	$bydate = '1';
}else{
	$echodate = date("n/j/Y");
	$setdate = date("Y-m-d");
	$bydate = '0';
}
if($bydate == '1')
{
	$invsql = 'SELECT SUM(`total`) AS `tt1` FROM `journal` WHERE `journaltype` = \'1\' AND `siteid` = \'1\' AND `invoicedate` < :invoicedate';
	$arsth1 = $pdocxn->prepare($invsql);
	$arsth1->bindParam(':invoicedate',$setdate);
	$arsth1->execute();
	while($arrow1 = $arsth1->fetch(PDO::FETCH_ASSOC))
	{
		$tt1a = $arrow1['tt1'];
	}
	$servicechargesql = 'SELECT SUM(`total`) AS `tt2` FROM `journal` WHERE `journaltype` = \'3\' AND `siteid` = \'1\' AND `invoicedate` < :invoicedate';
	$arsth2 = $pdocxn->prepare($servicechargesql);
	$arsth2->bindParam(':invoicedate',$setdate);
	$arsth2->execute();
	while($arrow2 = $arsth2->fetch(PDO::FETCH_ASSOC))
	{
		$tt2a = $arrow2['tt2'];
	}
	$paysql = 'SELECT SUM(`total`) AS `tt3` FROM `journal` WHERE `journaltype` = \'6\' AND `siteid` = \'1\' AND `invoicedate` < :invoicedate';
	$arsth3 = $pdocxn->prepare($paysql);
	$arsth3->bindParam(':invoicedate',$setdate);
	$arsth3->execute();
	while($arrow3 = $arsth3->fetch(PDO::FETCH_ASSOC))
	{
		$tt3a = $arrow3['tt3'];
	}
	$adjsql = 'SELECT SUM(`total`) AS `tt4` FROM `journal` WHERE `journaltype` = \'7\' AND `siteid` = \'1\' AND `invoicedate` < :invoicedate';
	$arsth4 = $pdocxn->prepare($adjsql);
	$arsth4->bindParam(':invoicedate',$setdate);
	$arsth4->execute();
	while($arrow4 = $arsth4->fetch(PDO::FETCH_ASSOC))
	{
		$tt4a = $arrow4['tt4'];
	}
	$creditsql = 'SELECT SUM(`total`) AS `tt5` FROM `journal` WHERE `journaltype` = \'17\' AND `siteid` = \'1\' AND `invoicedate` < :invoicedate';
	$arsth5 = $pdocxn->prepare($creditsql);
	$arsth5->bindParam(':invoicedate',$setdate);
	$arsth5->execute();
	while($arrow5 = $arsth5->fetch(PDO::FETCH_ASSOC))
	{
		$tt5a = $arrow5['tt5'];
	}
	$refundinvsql = 'SELECT SUM(`total`) AS `tt6` FROM `journal` WHERE `journaltype` = \'18\' AND `siteid` = \'1\' AND `invoicedate` < :invoicedate';
	$arsth6 = $pdocxn->prepare($refundinvsql);
	$arsth6->bindParam(':invoicedate',$setdate);
	$arsth6->execute();
	while($arrow6 = $arsth6->fetch(PDO::FETCH_ASSOC))
	{
		$tt6a = $arrow6['tt6'];
	}
	$begbalanceinvsql = 'SELECT SUM(`total`) AS `tt7` FROM `journal` WHERE `journaltype` = \'19\' AND `siteid` = \'1\'';
	$arsth7 = $pdocxn->prepare($begbalanceinvsql);
	$arsth7->bindParam(':invoicedate',$setdate);
	$arsth7->execute();
	while($arrow7 = $arsth7->fetch(PDO::FETCH_ASSOC))
	{
		$tt7a = $arrow7['tt7'];
	}
$artotala = $tt1a + $tt2a + $tt3a + $tt4a + $tt5a + $tt6a + $tt7a;

//Get Plover now
$invsql = 'SELECT SUM(`total`) AS `tt1` FROM `journal` WHERE `journaltype` = \'1\' AND `siteid` = \'2\' AND `invoicedate` < :invoicedate';
$arsth1 = $pdocxn->prepare($invsql);
$arsth1->bindParam(':invoicedate',$setdate);
$arsth1->execute();
while($arrow1 = $arsth1->fetch(PDO::FETCH_ASSOC))
{
	$tt1b = $arrow1['tt1'];
}
$servicechargesql = 'SELECT SUM(`total`) AS `tt2` FROM `journal` WHERE `journaltype` = \'3\' AND `siteid` = \'2\' AND `invoicedate` < :invoicedate';
$arsth2 = $pdocxn->prepare($servicechargesql);
$arsth2->bindParam(':invoicedate',$setdate);
$arsth2->execute();
while($arrow2 = $arsth2->fetch(PDO::FETCH_ASSOC))
{
	$tt2b = $arrow2['tt2'];
}
$paysql = 'SELECT SUM(`total`) AS `tt3` FROM `journal` WHERE `journaltype` = \'6\' AND `siteid` = \'2\' AND `invoicedate` < :invoicedate';
$arsth3 = $pdocxn->prepare($paysql);
$arsth3->bindParam(':invoicedate',$setdate);
$arsth3->execute();
while($arrow3 = $arsth3->fetch(PDO::FETCH_ASSOC))
{
	$tt3b = $arrow3['tt3'];
}
$adjsql = 'SELECT SUM(`total`) AS `tt4` FROM `journal` WHERE `journaltype` = \'7\' AND `siteid` = \'2\' AND `invoicedate` < :invoicedate';
$arsth4 = $pdocxn->prepare($adjsql);
$arsth4->bindParam(':invoicedate',$setdate);
$arsth4->execute();
while($arrow4 = $arsth4->fetch(PDO::FETCH_ASSOC))
{
	$tt4b = $arrow4['tt4'];
}
$creditsql = 'SELECT SUM(`total`) AS `tt5` FROM `journal` WHERE `journaltype` = \'17\' AND `siteid` = \'2\' AND `invoicedate` < :invoicedate';
$arsth5 = $pdocxn->prepare($creditsql);
$arsth5->bindParam(':invoicedate',$setdate);
$arsth5->execute();
while($arrow5 = $arsth5->fetch(PDO::FETCH_ASSOC))
{
	$tt5b = $arrow5['tt5'];
}
$refundinvsql = 'SELECT SUM(`total`) AS `tt6` FROM `journal` WHERE `journaltype` = \'18\' AND `siteid` = \'2\' AND `invoicedate` < :invoicedate';
$arsth6 = $pdocxn->prepare($refundinvsql);
$arsth6->bindParam(':invoicedate',$setdate);
$arsth6->execute();
while($arrow6 = $arsth6->fetch(PDO::FETCH_ASSOC))
{
	$tt6b = $arrow6['tt6'];
}
$begbalanceinvsql = 'SELECT SUM(`total`) AS `tt7` FROM `journal` WHERE `journaltype` = \'19\' AND `siteid` = \'2\' AND `invoicedate` < :invoicedate';
$arsth7 = $pdocxn->prepare($begbalanceinvsql);
$arsth7->bindParam(':invoicedate',$setdate);
$arsth7->execute();
while($arrow7 = $arsth7->fetch(PDO::FETCH_ASSOC))
{
	$tt7b = $arrow7['tt7'];
}
}else{
	//now without date
	$invsql = 'SELECT SUM(`total`) AS `tt1` FROM `journal` WHERE `journaltype` = \'1\' AND `siteid` = \'1\'';
	$arsth1 = $pdocxn->prepare($invsql);
	$arsth1->execute();
	while($arrow1 = $arsth1->fetch(PDO::FETCH_ASSOC))
	{
		$tt1a = $arrow1['tt1'];
	}
	$servicechargesql = 'SELECT SUM(`total`) AS `tt2` FROM `journal` WHERE `journaltype` = \'3\' AND `siteid` = \'1\'';
	$arsth2 = $pdocxn->prepare($servicechargesql);
	$arsth2->execute();
	while($arrow2 = $arsth2->fetch(PDO::FETCH_ASSOC))
	{
		$tt2a = $arrow2['tt2'];
	}
	$paysql = 'SELECT SUM(`total`) AS `tt3` FROM `journal` WHERE `journaltype` = \'6\' AND `siteid` = \'1\'';
	$arsth3 = $pdocxn->prepare($paysql);
	$arsth3->execute();
	while($arrow3 = $arsth3->fetch(PDO::FETCH_ASSOC))
	{
		$tt3a = $arrow3['tt3'];
	}
	$adjsql = 'SELECT SUM(`total`) AS `tt4` FROM `journal` WHERE `journaltype` = \'7\' AND `siteid` = \'1\'';
	$arsth4 = $pdocxn->prepare($adjsql);
	$arsth4->execute();
	while($arrow4 = $arsth4->fetch(PDO::FETCH_ASSOC))
	{
		$tt4a = $arrow4['tt4'];
	}
	$creditsql = 'SELECT SUM(`total`) AS `tt5` FROM `journal` WHERE `journaltype` = \'17\' AND `siteid` = \'1\'';
	$arsth5 = $pdocxn->prepare($creditsql);
	$arsth5->execute();
	while($arrow5 = $arsth5->fetch(PDO::FETCH_ASSOC))
	{
		$tt5a = $arrow5['tt5'];
	}
	$refundinvsql = 'SELECT SUM(`total`) AS `tt6` FROM `journal` WHERE `journaltype` = \'18\' AND `siteid` = \'1\'';
	$arsth6 = $pdocxn->prepare($refundinvsql);
	$arsth6->execute();
	while($arrow6 = $arsth6->fetch(PDO::FETCH_ASSOC))
	{
		$tt6a = $arrow6['tt6'];
	}
	$begbalanceinvsql = 'SELECT SUM(`total`) AS `tt7` FROM `journal` WHERE `journaltype` = \'19\' AND `siteid` = \'1\'';
	$arsth7 = $pdocxn->prepare($begbalanceinvsql);
	$arsth7->execute();
	while($arrow7 = $arsth7->fetch(PDO::FETCH_ASSOC))
	{
		$tt7a = $arrow7['tt7'];
	}
$artotala = $tt1a + $tt2a + $tt3a + $tt4a + $tt5a + $tt6a + $tt7a;

//Get Plover now
$invsql = 'SELECT SUM(`total`) AS `tt1` FROM `journal` WHERE `journaltype` = \'1\' AND `siteid` = \'2\'';
$arsth1 = $pdocxn->prepare($invsql);
$arsth1->execute();
while($arrow1 = $arsth1->fetch(PDO::FETCH_ASSOC))
{
	$tt1b = $arrow1['tt1'];
}
$servicechargesql = 'SELECT SUM(`total`) AS `tt2` FROM `journal` WHERE `journaltype` = \'3\' AND `siteid` = \'2\'';
$arsth2 = $pdocxn->prepare($servicechargesql);
$arsth2->execute();
while($arrow2 = $arsth2->fetch(PDO::FETCH_ASSOC))
{
	$tt2b = $arrow2['tt2'];
}
$paysql = 'SELECT SUM(`total`) AS `tt3` FROM `journal` WHERE `journaltype` = \'6\' AND `siteid` = \'2\'';
$arsth3 = $pdocxn->prepare($paysql);
$arsth3->execute();
while($arrow3 = $arsth3->fetch(PDO::FETCH_ASSOC))
{
	$tt3b = $arrow3['tt3'];
}
$adjsql = 'SELECT SUM(`total`) AS `tt4` FROM `journal` WHERE `journaltype` = \'7\' AND `siteid` = \'2\'';
$arsth4 = $pdocxn->prepare($adjsql);
$arsth4->execute();
while($arrow4 = $arsth4->fetch(PDO::FETCH_ASSOC))
{
	$tt4b = $arrow4['tt4'];
}
$creditsql = 'SELECT SUM(`total`) AS `tt5` FROM `journal` WHERE `journaltype` = \'17\' AND `siteid` = \'2\'';
$arsth5 = $pdocxn->prepare($creditsql);
$arsth5->execute();
while($arrow5 = $arsth5->fetch(PDO::FETCH_ASSOC))
{
	$tt5b = $arrow5['tt5'];
}
$refundinvsql = 'SELECT SUM(`total`) AS `tt6` FROM `journal` WHERE `journaltype` = \'18\' AND `siteid` = \'2\'';
$arsth6 = $pdocxn->prepare($refundinvsql);
$arsth6->execute();
while($arrow6 = $arsth6->fetch(PDO::FETCH_ASSOC))
{
	$tt6b = $arrow6['tt6'];
}
$begbalanceinvsql = 'SELECT SUM(`total`) AS `tt7` FROM `journal` WHERE `journaltype` = \'19\' AND `siteid` = \'2\'';
$arsth7 = $pdocxn->prepare($begbalanceinvsql);
$arsth7->execute();
while($arrow7 = $arsth7->fetch(PDO::FETCH_ASSOC))
{
	$tt7b = $arrow7['tt7'];
}}
$artotalb = $tt1b + $tt2b + $tt3b + $tt4b + $tt5b + $tt6b + $tt7b;
$artotal = $artotala + $artotalb;
$tt1 = $tt1a + $tt1b;
$tt2 = $tt2a + $tt2b;
$tt3 = $tt3a + $tt3b;
$tt4 = $tt4a + $tt4b;
$tt5 = $tt5a + $tt5b;
$tt6 = $tt6a + $tt6b;
$tt7 = $tt7a + $tt7b;
echo '<center><b>Showing Recievable Accounts up to Date: '.$echodate.'</b></center>';
echo '<center><b><form name="updatedate" action="accountingreports.php" method="GET"> <input type="hidden" name="id" value="15"> <input type="date" name="d" value="'.$setdate.'"><input type="submit" name="submit" class="xsmallbutton" value="Update Date"></b></center>';
echo '<table id="highlightTable" class="blueTable"><thead>';
echo '<tr><th>Location:</th><th>Total Accounts Receivable:</th><th>Total Invoices:</th><th>Total Payments:</th><th>Total Adjustments:</th><th>Total Service Charges:</th><th>Total Credits:</th><th>Total Refunds:</th><th>Total Begining Balance:</th></tr></thead><tbody>';
echo '<tr><td><b>Rapids</b></td><td><b>'.number_format($artotala,2).'</b><td>'.number_format($tt1a,2).'</td><td>'.number_format($tt3a,2).'</td><td>'.number_format($tt4a,2).'</td><td>'.number_format($tt2a,2).'</td><td>'.number_format($tt5a,2).'</td><td>'.number_format($tt6a,2).'</td><td>'.number_format($tt7a,2).'</td></tr>';
echo '<tr><td><b>Plover</b></td><td><b>'.number_format($artotalb,2).'</b><td>'.number_format($tt1b,2).'</td><td>'.number_format($tt3b,2).'</td><td>'.number_format($tt4b,2).'</td><td>'.number_format($tt2b,2).'</td><td>'.number_format($tt5b,2).'</td><td>'.number_format($tt6b,2).'</td><td>'.number_format($tt7b,2).'</td></tr>';
echo '<tr><td colspan="9"><br /><br /></td></tr>';
echo '<tr><td><b>All Locations</b></td><td><b>'.number_format($artotal,2).'</b><td>'.number_format($tt1,2).'</td><td>'.number_format($tt3,2).'</td><td>'.number_format($tt4,2).'</td><td>'.number_format($tt2,2).'</td><td>'.number_format($tt5,2).'</td><td>'.number_format($tt6,2).'</td><td>'.number_format($tt7,2).'</td></tr>';
}
//report13
else if($id == '13')
{

$date30 = date('Y-m-d',"-30 days");
$date60 = date('Y-m-d',"-60 days");
$date90 = date('Y-m-d',"-90 days");
$date120 = date('Y-m-d',"-120 days");
$balance60 ='0';
$balance90 = '0';
$balance120 = '0';
echo '<center><b>Showing Aged Accounts For: Rapids</b></center>';
echo '<table id="highlightTable" class="blueTable"><thead>';
echo '<tr><th>Customer:</th><th>Current Account Balance:</th><th>Account Balance 30 Days:</th><th>Account Balance 60 Days:</th><th>Account Balance 90 Days:</th><th>Account Balance 120+ Days:</th></tr></thead><tbody>';

$sql1 = 'SELECT SUM(`total`) AS `balance`,`accountid` FROM `journal` WHERE `siteid` = :siteid AND `invoicedate` < :date30 GROUP BY `accountid`';
$sth1 = $pdocxn->prepare($sql1);
$sth1->bindParam(':siteid',$currentlocationid);
$sth1->bindParam(':date30',$date30);
$sth1->execute();
	while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$accountid = $row1['accountid'];
	$balance30 = $row1['balance'];
	}
if($balance30 > '0' OR $balance60 > '0' OR $balance90 > '0' OR $balance120 > '0')
{
	$sql2 = "SELECT `firstname`,`lastname`,`creditlimit`,`oldtype` FROM `accounts` WHERE `accountid` = :accountid";
	$sth2 = $pdocxn->prepare($sql2);
	$sth2->bindParam(':accountid',$accountid);
	$sth2->execute();
	while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
	{
	$acttype = $row2['oldtype'];
	if($acttype = '1')
	{
	$fname = $row2['firstname'];
	$lname = $row2['lastname'];
	$fullname = $fname." ".$lname;
	$creditlimit = $row2['creditlimit'];

	$invsql = 'SELECT SUM(`total`) AS `tt0` FROM `journal` WHERE `accountid` = :accountid AND `siteid` = :currentsite';
	$arsth1 = $pdocxn->prepare($invsql);
	$arsth1->bindparam(':accountid',$accountid);
	$arsth1->bindParam(':currentsite',$currentlocationid);
	$arsth1->execute();
	while($arrow1 = $arsth1->fetch(PDO::FETCH_ASSOC))
	{
		$currentbalance = $arrow1['tt0'];
	}
echo '<tr><td><b>'.$fullname.'</b></td><td><b>'.$currentbalance.'</b><td>'.$balance30.'</td><td>'.$balance60.'</td><td>'.$balance90.'</td><td>'.$balance120.'</td></tr>';
}}}else{
$currentbalance = '0';
$balance30 = '0';
$balance60 = '0';
$balance90 = '0';
$balance120 = '0';
}
}

//report15a
else if($id == '15a')
{
$last30date = new \DateTime('-30 days');
$lastmonth30 = $last30date->format('Y-m-d');

$last60date = new \DateTime('-60 days');
$lastmonth60 = $last60date->format('Y-m-d');

$invsql = 'SELECT SUM(`total`) AS `tt1` FROM `journal` WHERE `journaltype` = \'1\' AND `siteid` = \'1\'';
$arsth1 = $pdocxn->prepare($invsql);
$arsth1->execute();
while($arrow1 = $arsth1->fetch(PDO::FETCH_ASSOC))
{
	$tt1a = $arrow1['tt1'];
}
$servicechargesql = 'SELECT SUM(`total`) AS `tt2` FROM `journal` WHERE `journaltype` = \'3\' AND `siteid` = \'1\'';
$arsth2 = $pdocxn->prepare($servicechargesql);
$arsth2->execute();
while($arrow2 = $arsth2->fetch(PDO::FETCH_ASSOC))
{
	$tt2a = $arrow2['tt2'];
}
$paysql = 'SELECT SUM(`total`) AS `tt3` FROM `journal` WHERE `journaltype` = \'6\' AND `siteid` = \'1\'';
$arsth3 = $pdocxn->prepare($paysql);
$arsth3->execute();
while($arrow3 = $arsth3->fetch(PDO::FETCH_ASSOC))
{
	$tt3a = $arrow3['tt3'];
}
$adjsql = 'SELECT SUM(`total`) AS `tt4` FROM `journal` WHERE `journaltype` = \'7\' AND `siteid` = \'1\'';
$arsth4 = $pdocxn->prepare($adjsql);
$arsth4->execute();
while($arrow4 = $arsth4->fetch(PDO::FETCH_ASSOC))
{
	$tt4a = $arrow4['tt4'];
}
$creditsql = 'SELECT SUM(`total`) AS `tt5` FROM `journal` WHERE `journaltype` = \'17\' AND `siteid` = \'1\'';
$arsth5 = $pdocxn->prepare($creditsql);
$arsth5->execute();
while($arrow5 = $arsth5->fetch(PDO::FETCH_ASSOC))
{
	$tt5a = $arrow5['tt5'];
}
$refundinvsql = 'SELECT SUM(`total`) AS `tt6` FROM `journal` WHERE `journaltype` = \'18\' AND `siteid` = \'1\'';
$arsth6 = $pdocxn->prepare($refundinvsql);
$arsth6->execute();
while($arrow6 = $arsth6->fetch(PDO::FETCH_ASSOC))
{
	$tt6a = $arrow6['tt6'];
}
$begbalanceinvsql = 'SELECT SUM(`total`) AS `tt7` FROM `journal` WHERE `journaltype` = \'19\' AND `siteid` = \'1\'';
$arsth7 = $pdocxn->prepare($begbalanceinvsql);
$arsth7->execute();
while($arrow7 = $arsth7->fetch(PDO::FETCH_ASSOC))
{
	$tt7a = $arrow7['tt7'];
}
$artotala = $tt1a + $tt2a + $tt3a + $tt4a + $tt5a + $tt6a + $tt7a;

//Get Plover now
$invsql = 'SELECT SUM(`total`) AS `tt1` FROM `journal` WHERE `journaltype` = \'1\' AND `siteid` = \'2\'';
$arsth1 = $pdocxn->prepare($invsql);
$arsth1->execute();
while($arrow1 = $arsth1->fetch(PDO::FETCH_ASSOC))
{
$tt1b = $arrow1['tt1'];
}
$servicechargesql = 'SELECT SUM(`total`) AS `tt2` FROM `journal` WHERE `journaltype` = \'3\' AND `siteid` = \'2\'';
$arsth2 = $pdocxn->prepare($servicechargesql);
$arsth2->execute();
while($arrow2 = $arsth2->fetch(PDO::FETCH_ASSOC))
{
$tt2b = $arrow2['tt2'];
}
$paysql = 'SELECT SUM(`total`) AS `tt3` FROM `journal` WHERE `journaltype` = \'6\' AND `siteid` = \'2\'';
$arsth3 = $pdocxn->prepare($paysql);
$arsth3->execute();
while($arrow3 = $arsth3->fetch(PDO::FETCH_ASSOC))
{
$tt3b = $arrow3['tt3'];
}
$adjsql = 'SELECT SUM(`total`) AS `tt4` FROM `journal` WHERE `journaltype` = \'7\' AND `siteid` = \'2\'';
$arsth4 = $pdocxn->prepare($adjsql);
$arsth4->execute();
while($arrow4 = $arsth4->fetch(PDO::FETCH_ASSOC))
{
$tt4b = $arrow4['tt4'];
}
$creditsql = 'SELECT SUM(`total`) AS `tt5` FROM `journal` WHERE `journaltype` = \'17\' AND `siteid` = \'2\'';
$arsth5 = $pdocxn->prepare($creditsql);
$arsth5->execute();
while($arrow5 = $arsth5->fetch(PDO::FETCH_ASSOC))
{
$tt5b = $arrow5['tt5'];
}
$refundinvsql = 'SELECT SUM(`total`) AS `tt6` FROM `journal` WHERE `journaltype` = \'18\' AND `siteid` = \'2\'';
$arsth6 = $pdocxn->prepare($refundinvsql);
$arsth6->execute();
while($arrow6 = $arsth6->fetch(PDO::FETCH_ASSOC))
{
$tt6b = $arrow6['tt6'];
}
$begbalanceinvsql = 'SELECT SUM(`total`) AS `tt7` FROM `journal` WHERE `journaltype` = \'19\' AND `siteid` = \'2\'';
$arsth7 = $pdocxn->prepare($begbalanceinvsql);
$arsth7->execute();
while($arrow7 = $arsth7->fetch(PDO::FETCH_ASSOC))
{
$tt7b = $arrow7['tt7'];
}
$artotalb = $tt1b + $tt2b + $tt3b + $tt4b + $tt5b + $tt6b + $tt7b;
$artotal = $artotala + $artotalb;
$tt1 = $tt1a + $tt1b;
$tt2 = $tt2a + $tt2b;
$tt3 = $tt3a + $tt3b;
$tt4 = $tt4a + $tt4b;
$tt5 = $tt5a + $tt5b;
$tt6 = $tt6a + $tt6b;
$tt7 = $tt7a + $tt7b;








//get last 30 day values
	$invsql = 'SELECT SUM(`total`) AS `tt1` FROM `journal` WHERE `journaltype` = \'1\' AND `siteid` = \'1\' AND `invoicedate` > :invoicedate';
	$arsth1 = $pdocxn->prepare($invsql);
	$arsth1->bindParam(':invoicedate',$lastmonth30);
	$arsth1->execute();
	while($arrow1 = $arsth1->fetch(PDO::FETCH_ASSOC))
	{
		$tt1a30 = $arrow1['tt1'];
	}
	$servicechargesql = 'SELECT SUM(`total`) AS `tt2` FROM `journal` WHERE `journaltype` = \'3\' AND `siteid` = \'1\' AND `invoicedate` > :invoicedate';
	$arsth2 = $pdocxn->prepare($servicechargesql);
	$arsth2->bindParam(':invoicedate',$lastmonth30);
	$arsth2->execute();
	while($arrow2 = $arsth2->fetch(PDO::FETCH_ASSOC))
	{
		$tt2a30 = $arrow2['tt2'];
	}
	$paysql = 'SELECT SUM(`total`) AS `tt3` FROM `journal` WHERE `journaltype` = \'6\' AND `siteid` = \'1\' AND `invoicedate` > :invoicedate';
	$arsth3 = $pdocxn->prepare($paysql);
	$arsth3->bindParam(':invoicedate',$lastmonth30);
	$arsth3->execute();
	while($arrow3 = $arsth3->fetch(PDO::FETCH_ASSOC))
	{
		$tt3a30 = $arrow3['tt3'];
	}
	$adjsql = 'SELECT SUM(`total`) AS `tt4` FROM `journal` WHERE `journaltype` = \'7\' AND `siteid` = \'1\' AND `invoicedate` > :invoicedate';
	$arsth4 = $pdocxn->prepare($adjsql);
	$arsth4->bindParam(':invoicedate',$lastmonth30);
	$arsth4->execute();
	while($arrow4 = $arsth4->fetch(PDO::FETCH_ASSOC))
	{
		$tt4a30 = $arrow4['tt4'];
	}
	$creditsql = 'SELECT SUM(`total`) AS `tt5` FROM `journal` WHERE `journaltype` = \'17\' AND `siteid` = \'1\' AND `invoicedate` > :invoicedate';
	$arsth5 = $pdocxn->prepare($creditsql);
	$arsth5->bindParam(':invoicedate',$lastmonth30);
	$arsth5->execute();
	while($arrow5 = $arsth5->fetch(PDO::FETCH_ASSOC))
	{
		$tt5a30 = $arrow5['tt5'];
	}
	$refundinvsql = 'SELECT SUM(`total`) AS `tt6` FROM `journal` WHERE `journaltype` = \'18\' AND `siteid` = \'1\' AND `invoicedate` > :invoicedate';
	$arsth6 = $pdocxn->prepare($refundinvsql);
	$arsth6->bindParam(':invoicedate',$lastmonth30);
	$arsth6->execute();
	while($arrow6 = $arsth6->fetch(PDO::FETCH_ASSOC))
	{
		$tt6a30 = $arrow6['tt6'];
	}
	$begbalanceinvsql = 'SELECT SUM(`total`) AS `tt7` FROM `journal` WHERE `journaltype` = \'19\' AND `siteid` = \'1\' AND `invoicedate` > :invoicedate';
	$arsth7 = $pdocxn->prepare($begbalanceinvsql);
	$arsth7->bindParam(':invoicedate',$lastmonth30);
	$arsth7->execute();
	while($arrow7 = $arsth7->fetch(PDO::FETCH_ASSOC))
	{
		$tt7a30 = $arrow7['tt7'];
	}
$artotala30 = $tt1a30 + $tt2a30 + $tt3a30 + $tt4a30 + $tt5a30 + $tt6a30 + $tt7a30;

//Get Plover now
$invsql = 'SELECT SUM(`total`) AS `tt1` FROM `journal` WHERE `journaltype` = \'1\' AND `siteid` = \'2\' AND `invoicedate` > :invoicedate';
$arsth1 = $pdocxn->prepare($invsql);
$arsth1->bindParam(':invoicedate',$lastmonth30);
$arsth1->execute();
while($arrow1 = $arsth1->fetch(PDO::FETCH_ASSOC))
{
	$tt1b30 = $arrow1['tt1'];
}
$servicechargesql = 'SELECT SUM(`total`) AS `tt2` FROM `journal` WHERE `journaltype` = \'3\' AND `siteid` = \'2\' AND `invoicedate` > :invoicedate';
$arsth2 = $pdocxn->prepare($servicechargesql);
$arsth2->bindParam(':invoicedate',$lastmonth30);
$arsth2->execute();
while($arrow2 = $arsth2->fetch(PDO::FETCH_ASSOC))
{
	$tt2b30 = $arrow2['tt2'];
}
$paysql = 'SELECT SUM(`total`) AS `tt3` FROM `journal` WHERE `journaltype` = \'6\' AND `siteid` = \'2\' AND `invoicedate` > :invoicedate';
$arsth3 = $pdocxn->prepare($paysql);
$arsth3->bindParam(':invoicedate',$lastmonth30);
$arsth3->execute();
while($arrow3 = $arsth3->fetch(PDO::FETCH_ASSOC))
{
	$tt3b30 = $arrow3['tt3'];
}
$adjsql = 'SELECT SUM(`total`) AS `tt4` FROM `journal` WHERE `journaltype` = \'7\' AND `siteid` = \'2\' AND `invoicedate` > :invoicedate';
$arsth4 = $pdocxn->prepare($adjsql);
$arsth4->bindParam(':invoicedate',$lastmonth30);
$arsth4->execute();
while($arrow4 = $arsth4->fetch(PDO::FETCH_ASSOC))
{
	$tt4b30 = $arrow4['tt4'];
}
$creditsql = 'SELECT SUM(`total`) AS `tt5` FROM `journal` WHERE `journaltype` = \'17\' AND `siteid` = \'2\' AND `invoicedate` > :invoicedate';
$arsth5 = $pdocxn->prepare($creditsql);
$arsth5->bindParam(':invoicedate',$lastmonth30);
$arsth5->execute();
while($arrow5 = $arsth5->fetch(PDO::FETCH_ASSOC))
{
	$tt5b30 = $arrow5['tt5'];
}
$refundinvsql = 'SELECT SUM(`total`) AS `tt6` FROM `journal` WHERE `journaltype` = \'18\' AND `siteid` = \'2\' AND `invoicedate` > :invoicedate';
$arsth6 = $pdocxn->prepare($refundinvsql);
$arsth6->bindParam(':invoicedate',$lastmonth30);
$arsth6->execute();
while($arrow6 = $arsth6->fetch(PDO::FETCH_ASSOC))
{
	$tt6b30 = $arrow6['tt6'];
}
$begbalanceinvsql = 'SELECT SUM(`total`) AS `tt7` FROM `journal` WHERE `journaltype` = \'19\' AND `siteid` = \'2\' AND `invoicedate` > :invoicedate';
$arsth7 = $pdocxn->prepare($begbalanceinvsql);
$arsth7->bindParam(':invoicedate',$lastmonth30);
$arsth7->execute();
while($arrow7 = $arsth7->fetch(PDO::FETCH_ASSOC))
{
	$tt7b30 = $arrow7['tt7'];
}

//get last 60 day values
	$invsql = 'SELECT SUM(`total`) AS `tt1` FROM `journal` WHERE `journaltype` = \'1\' AND `siteid` = \'1\' AND `invoicedate` > :invoicedate';
	$arsth1 = $pdocxn->prepare($invsql);
	$arsth1->bindParam(':invoicedate',$lastmonth60);
	$arsth1->execute();
	while($arrow1 = $arsth1->fetch(PDO::FETCH_ASSOC))
	{
		$tt1a60a = $arrow1['tt1'];
	}
	$servicechargesql = 'SELECT SUM(`total`) AS `tt2` FROM `journal` WHERE `journaltype` = \'3\' AND `siteid` = \'1\' AND `invoicedate` > :invoicedate';
	$arsth2 = $pdocxn->prepare($servicechargesql);
	$arsth2->bindParam(':invoicedate',$lastmonth60);
	$arsth2->execute();
	while($arrow2 = $arsth2->fetch(PDO::FETCH_ASSOC))
	{
		$tt2a60a = $arrow2['tt2'];
	}
	$paysql = 'SELECT SUM(`total`) AS `tt3` FROM `journal` WHERE `journaltype` = \'6\' AND `siteid` = \'1\' AND `invoicedate` > :invoicedate';
	$arsth3 = $pdocxn->prepare($paysql);
	$arsth3->bindParam(':invoicedate',$lastmonth60);
	$arsth3->execute();
	while($arrow3 = $arsth3->fetch(PDO::FETCH_ASSOC))
	{
		$tt3a60a = $arrow3['tt3'];
	}
	$adjsql = 'SELECT SUM(`total`) AS `tt4` FROM `journal` WHERE `journaltype` = \'7\' AND `siteid` = \'1\' AND `invoicedate` > :invoicedate';
	$arsth4 = $pdocxn->prepare($adjsql);
	$arsth4->bindParam(':invoicedate',$lastmonth60);
	$arsth4->execute();
	while($arrow4 = $arsth4->fetch(PDO::FETCH_ASSOC))
	{
		$tt4a60a = $arrow4['tt4'];
	}
	$creditsql = 'SELECT SUM(`total`) AS `tt5` FROM `journal` WHERE `journaltype` = \'17\' AND `siteid` = \'1\' AND `invoicedate` > :invoicedate';
	$arsth5 = $pdocxn->prepare($creditsql);
	$arsth5->bindParam(':invoicedate',$lastmonth60);
	$arsth5->execute();
	while($arrow5 = $arsth5->fetch(PDO::FETCH_ASSOC))
	{
		$tt5a60a = $arrow5['tt5'];
	}
	$refundinvsql = 'SELECT SUM(`total`) AS `tt6` FROM `journal` WHERE `journaltype` = \'18\' AND `siteid` = \'1\' AND `invoicedate` > :invoicedate';
	$arsth6 = $pdocxn->prepare($refundinvsql);
	$arsth6->bindParam(':invoicedate',$lastmonth60);
	$arsth6->execute();
	while($arrow6 = $arsth6->fetch(PDO::FETCH_ASSOC))
	{
		$tt6a60a = $arrow6['tt6'];
	}
	$begbalanceinvsql = 'SELECT SUM(`total`) AS `tt7` FROM `journal` WHERE `journaltype` = \'19\' AND `siteid` = \'1\' AND `invoicedate` > :invoicedate';
	$arsth7 = $pdocxn->prepare($begbalanceinvsql);
	$arsth7->bindParam(':invoicedate',$lastmonth60);
	$arsth7->execute();
	while($arrow7 = $arsth7->fetch(PDO::FETCH_ASSOC))
	{
		$tt7a60a = $arrow7['tt7'];
	}
$artotala60a = $tt1a60a + $tt2a60a + $tt3a60a + $tt4a60a + $tt5a60a + $tt6a60a + $tt7a60a;

//Get Plover now
$invsql = 'SELECT SUM(`total`) AS `tt1` FROM `journal` WHERE `journaltype` = \'1\' AND `siteid` = \'2\' AND `invoicedate` > :invoicedate';
$arsth1 = $pdocxn->prepare($invsql);
$arsth1->bindParam(':invoicedate',$lastmonth60);
$arsth1->execute();
while($arrow1 = $arsth1->fetch(PDO::FETCH_ASSOC))
{
	$tt1b60a = $arrow1['tt1'];
}
$servicechargesql = 'SELECT SUM(`total`) AS `tt2` FROM `journal` WHERE `journaltype` = \'3\' AND `siteid` = \'2\' AND `invoicedate` > :invoicedate';
$arsth2 = $pdocxn->prepare($servicechargesql);
$arsth2->bindParam(':invoicedate',$lastmonth60);
$arsth2->execute();
while($arrow2 = $arsth2->fetch(PDO::FETCH_ASSOC))
{
	$tt2b60a = $arrow2['tt2'];
}
$paysql = 'SELECT SUM(`total`) AS `tt3` FROM `journal` WHERE `journaltype` = \'6\' AND `siteid` = \'2\' AND `invoicedate` > :invoicedate';
$arsth3 = $pdocxn->prepare($paysql);
$arsth3->bindParam(':invoicedate',$lastmonth60);
$arsth3->execute();
while($arrow3 = $arsth3->fetch(PDO::FETCH_ASSOC))
{
	$tt3b60a = $arrow3['tt3'];
}
$adjsql = 'SELECT SUM(`total`) AS `tt4` FROM `journal` WHERE `journaltype` = \'7\' AND `siteid` = \'2\' AND `invoicedate` > :invoicedate';
$arsth4 = $pdocxn->prepare($adjsql);
$arsth4->bindParam(':invoicedate',$lastmonth60);
$arsth4->execute();
while($arrow4 = $arsth4->fetch(PDO::FETCH_ASSOC))
{
	$tt4b60a = $arrow4['tt4'];
}
$creditsql = 'SELECT SUM(`total`) AS `tt5` FROM `journal` WHERE `journaltype` = \'17\' AND `siteid` = \'2\' AND `invoicedate` > :invoicedate';
$arsth5 = $pdocxn->prepare($creditsql);
$arsth5->bindParam(':invoicedate',$lastmonth60);
$arsth5->execute();
while($arrow5 = $arsth5->fetch(PDO::FETCH_ASSOC))
{
	$tt5b60a = $arrow5['tt5'];
}
$refundinvsql = 'SELECT SUM(`total`) AS `tt6` FROM `journal` WHERE `journaltype` = \'18\' AND `siteid` = \'2\' AND `invoicedate` > :invoicedate';
$arsth6 = $pdocxn->prepare($refundinvsql);
$arsth6->bindParam(':invoicedate',$lastmonth60);
$arsth6->execute();
while($arrow6 = $arsth6->fetch(PDO::FETCH_ASSOC))
{
	$tt6b60a = $arrow6['tt6'];
}
$begbalanceinvsql = 'SELECT SUM(`total`) AS `tt7` FROM `journal` WHERE `journaltype` = \'19\' AND `siteid` = \'2\' AND `invoicedate` > :invoicedate';
$arsth7 = $pdocxn->prepare($begbalanceinvsql);
$arsth7->bindParam(':invoicedate',$lastmonth60);
$arsth7->execute();
while($arrow7 = $arsth7->fetch(PDO::FETCH_ASSOC))
{
	$tt7b60a = $arrow7['tt7'];
}
//define 30 day values
$artotalb30 = $tt1b30 + $tt2b30 + $tt3b30 + $tt4b30 + $tt5b30 + $tt6b30 + $tt7b30;
$artotal30 = $artotala30 + $artotalb30;
$tt130 = $tt1a30 + $tt1b30;
$tt230 = $tt2a30 + $tt2b30;
$tt330 = $tt3a30 + $tt3b30;
$tt430 = $tt4a30 + $tt4b30;
$tt530 = $tt5a30 + $tt5b30;
$tt630 = $tt6a30 + $tt6b30;
$tt730 = $tt7a30 + $tt7b30;

//define 60 day values
$artotalb60a = $tt1b60a + $tt2b60a + $tt3b60a + $tt4b60a + $tt5b60a + $tt6b60a + $tt7b60a;
$artotal60a = $artotala60a + $artotalb60a;
$tt160a = $tt1a60a + $tt1b60a;
$tt260a = $tt2a60a + $tt2b60a;
$tt360a = $tt3a60a + $tt3b60a;
$tt460a = $tt4a60a + $tt4b60a;
$tt560a = $tt5a60a + $tt5b60a;
$tt660a = $tt6a60a + $tt6b60a;
$tt760a = $tt7a60a + $tt7b60a;

$artotalb60 = $artotalb60a - $artotalb30;
$artotala60 = $artotal60a - $artotal30;
$tt1a60 = $tt160a - $tt130;
$tt2a60 = $tt260a - $tt230;
$tt3a60 = $tt360a - $tt330;
$tt4a60 = $tt460a - $tt430;
$tt5a60 = $tt560a - $tt530;
$tt6a60 = $tt660a - $tt630;
$tt7a60 = $tt760a - $tt730;


$tt1b60 = $tt1b60a - $tt1b30;
$tt2b60 = $tt2b60a - $tt2b30;
$tt3b60 = $tt3b60a - $tt3b30;
$tt4b60 = $tt4b60a - $tt4b30;
$tt5b60 = $tt5b60a - $tt5b30;
$tt6b60 = $tt6b60a - $tt6b30;
$tt7b60 = $tt7b60a - $tt7b30;

$tt160 = $tt1a60 + $tt1b60;
$tt260 = $tt2a60 + $tt2b60;
$tt360 = $tt3a60 + $tt3b60;
$tt460 = $tt4a60 + $tt4b60;
$tt560 = $tt5a60 + $tt5b60;
$tt660 = $tt6a60 + $tt6b60;
$tt760 = $tt7a60 + $tt7b60;
$artotal60 = $artotala60 + $artotalb60;




echo '<table id="highlightTable" class="blueTable"><thead>';
echo '<tr><th>Location:</th><th>Total Accounts Receivable:</th><th>Total Invoices:</th><th>Total Payments:</th><th>Total Adjustments:</th><th>Total Service Charges:</th><th>Total Credits:</th><th>Total Refunds:</th><th>Total Begining Balance:</th></tr></thead>';
echo '<tbody><tr><th colspan="9"><h2><center>Last 30 Days</center></h2></th></tr>';
echo '<tr><td><b>Rapids</b></td><td><b>'.number_format($artotala30,2).'</b><td>'.number_format($tt1a30,2).'</td><td>'.number_format($tt3a30,2).'</td><td>'.number_format($tt4a30,2).'</td><td>'.number_format($tt2a30,2).'</td><td>'.number_format($tt5a30,2).'</td><td>'.number_format($tt6a30,2).'</td><td>'.number_format($tt7a30,2).'</td></tr>';
echo '<tr><td><b>Plover</b></td><td><b>'.number_format($artotalb30,2).'</b><td>'.number_format($tt1b30,2).'</td><td>'.number_format($tt3b30,2).'</td><td>'.number_format($tt4b30,2).'</td><td>'.number_format($tt2b30,2).'</td><td>'.number_format($tt5b30,2).'</td><td>'.number_format($tt6b30,2).'</td><td>'.number_format($tt7b30,2).'</td></tr>';
echo '<tr><td colspan="9"><br /></td></tr>';
echo '<tr><td><b>All Locations</b></td><td><b>'.number_format($artotal30,2).'</b><td>'.number_format($tt130,2).'</td><td>'.number_format($tt330,2).'</td><td>'.number_format($tt430,2).'</td><td>'.number_format($tt230,2).'</td><td>'.number_format($tt530,2).'</td><td>'.number_format($tt630,2).'</td><td>'.number_format($tt730,2).'</td></tr>';
echo '<tr><td colspan="9"><br /><br /><br /></td></tr>';

echo '<tbody><tr><th colspan="9"><h2><center>Last 60 Days</center></h2></th></tr>';
echo '<tr><td><b>Rapids</b></td><td><b>'.number_format($artotala60,2).'</b><td>'.number_format($tt1a60,2).'</td><td>'.number_format($tt3a60,2).'</td><td>'.number_format($tt4a60,2).'</td><td>'.number_format($tt2a60,2).'</td><td>'.number_format($tt5a60,2).'</td><td>'.number_format($tt6a60,2).'</td><td>'.number_format($tt7a60,2).'</td></tr>';
echo '<tr><td><b>Plover</b></td><td><b>'.number_format($artotalb60,2).'</b><td>'.number_format($tt1b60,2).'</td><td>'.number_format($tt3b60,2).'</td><td>'.number_format($tt4b60,2).'</td><td>'.number_format($tt2b60,2).'</td><td>'.number_format($tt5b60,2).'</td><td>'.number_format($tt6b60,2).'</td><td>'.number_format($tt7b60,2).'</td></tr>';
echo '<tr><td colspan="9"><br /></td></tr>';
echo '<tr><td><b>All Locations</b></td><td><b>'.number_format($artotal60,2).'</b><td>'.number_format($tt160,2).'</td><td>'.number_format($tt360,2).'</td><td>'.number_format($tt460,2).'</td><td>'.number_format($tt260,2).'</td><td>'.number_format($tt560,2).'</td><td>'.number_format($tt660,2).'</td><td>'.number_format($tt760,2).'</td></tr>';
echo '<tr><td colspan="9"><br /><h2><center>Total Accounts Receivable</center></h2></td></tr>';

echo '<tr><td><b>Rapids</b></td><td><b>'.number_format($artotala,2).'</b><td>'.number_format($tt1a,2).'</td><td>'.number_format($tt3a,2).'</td><td>'.number_format($tt4a,2).'</td><td>'.number_format($tt2a,2).'</td><td>'.number_format($tt5a,2).'</td><td>'.number_format($tt6a,2).'</td><td>'.number_format($tt7a,2).'</td></tr>';
echo '<tr><td><b>Plover</b></td><td><b>'.number_format($artotalb,2).'</b><td>'.number_format($tt1b,2).'</td><td>'.number_format($tt3b,2).'</td><td>'.number_format($tt4b,2).'</td><td>'.number_format($tt2b,2).'</td><td>'.number_format($tt5b,2).'</td><td>'.number_format($tt6b,2).'</td><td>'.number_format($tt7b,2).'</td></tr>';
echo '<tr><td colspan="9"><br /></td></tr>';
echo '<tr><td><b>All Locations</b></td><td><b>'.number_format($artotal,2).'</b><td>'.number_format($tt1,2).'</td><td>'.number_format($tt3,2).'</td><td>'.number_format($tt4,2).'</td><td>'.number_format($tt2,2).'</td><td>'.number_format($tt5,2).'</td><td>'.number_format($tt6,2).'</td><td>'.number_format($tt7,2).'</td></tr>';
}
//report16
else if($id == '16')
{

	?>
	<div id="content">
				<div id="left">
				<table id="highlightTable" class="blueTable">
	<thead>
	<tr>
	<th>Location</th>
	<th>Total Number of Tires</th>
	<th>Cost Total</th>
	<th>Sell Price Total</th>
	</tr>
	</thead>
	<tbody>
	<?php
	$numtotal = '0';
	$costtotal = '0';
	$selltotal = '0';
	$numtotal1 = '0';
	$costtotal1 = '0';
	$selltotal1 = '0';
	$numtotal2 = '0';
	$costtotal2 = '0';
	$selltotal2 = '0';
	$sql1 = 'SELECT `id`,`loc1_onhand` FROM `inventory` WHERE `loc1_onhand` > \'0\'';
	$sth1 = $pdocxn->prepare($sql1);
	$sth1->execute();
	while ($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
		{
		$tireid = $row1['id'];
		$qty1 = $row1['loc1_onhand'];
		$numtotal1 = $numtotal1 + $qty1;
		$sql1a = 'SELECT `lastcost`,`price1` FROM `inventory_price` WHERE `partid` = \''.$tireid.'\' AND `siteid` = \'1\'';
		$sth1a = $pdocxn->prepare($sql1a);
		$sth1a->execute();
		while ($row1a = $sth1a->fetch(PDO::FETCH_ASSOC))
			{
			$lastcost1 = $row1a['lastcost'];
			$price1 = $row1a['price1'];
			if($price1 < $lastcost1)
			{
				$price1 = $lastcost1 + '25';
			}
			$tempcost1 = $lastcost1 * $qty1;
			$tempsell1 = $price1 * $qty1;
			$costtotal1 = $costtotal1 + $tempcost1;
			$selltotal1 = $selltotal1 + $tempsell1;
}}
		echo "<tr><td><b>Rapids</b></td><td>".number_format($numtotal1,0)."<td>$".number_format($costtotal1,2)."</td><td>$".number_format($selltotal1,2)."</td></tr>";
	//store2
	$sql2 = 'SELECT `id`,`loc2_onhand` FROM `inventory` WHERE `loc2_onhand` > \'0\'';
	$sth2 = $pdocxn->prepare($sql2);
	$sth2->execute();
	while ($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
		{
		$tireid = $row2['id'];
		$qty2 = $row2['loc2_onhand'];
		$numtotal2 = $numtotal2 + $qty2;
		$sql2a = 'SELECT `lastcost`,`price1` FROM `inventory_price` WHERE `partid` = \''.$tireid.'\' AND `siteid` = \'2\'';
		$sth2a = $pdocxn->prepare($sql2a);
		$sth2a->execute();
		while ($row2a = $sth2a->fetch(PDO::FETCH_ASSOC))
			{
			$lastcost2 = $row2a['lastcost'];
			$price2 = $row2a['price1'];
			if($price2 < $lastcost2)
			{
				$price2 = $lastcost2 + '25';
			}
			$tempcost2 = $lastcost2 * $qty2;
			$tempsell2 = $price2 * $qty2;
			$costtotal2 = $costtotal2 + $tempcost2;
			$selltotal2 = $selltotal2 + $tempsell2;
	}}
		echo "<tr><td><b>Plover</b></td><td>".number_format($numtotal2,0)."<td>$".number_format($costtotal2,2)."</td><td>$".number_format($selltotal2,2)."</td></tr>";
		$numtotal = $numtotal1 + $numtotal2;
		$costtotal = $costtotal1 + $costtotal2;
		$selltotal = $selltotal1 + $selltotal2;
		echo "<tr><td><b>All Stores</b></td><td>".number_format($numtotal,0)."<td>$".number_format($costtotal,2)."</td><td>$".number_format($selltotal,2)."</td></tr>";
	?></tbody></table></div>
	</div>
	<?php
}
//report17
else if($id =='17')
{
	$sql1a = 'SELECT `lastcost`,`price1`,`partid` FROM `inventory_price` WHERE `siteid` = \'1\'';
	$sth1a = $pdocxn->prepare($sql1a);
	$sth1a->execute();
	while ($row1a = $sth1a->fetch(PDO::FETCH_ASSOC))
		{
		$lastcost1 = $row1a['lastcost'];
		$price1 = $row1a['price1'];
		$partid = $row1a['partid'];
			
			$sth3 = $pdocxn->prepare('UPDATE `inventory_price` SET `lastcost`=:lastcost1,`price1`=:price1 WHERE `partid` = :partid2 AND `siteid` = \'2\'');
$sth3->bindParam(':lastcost1',$lastcost1);
$sth3->bindParam(':price1',$price1);
$sth3->bindParam(':partid2',$partid);
$sth3->execute();

}}
//java
?>
<br /><br />
<script type="text/javascript">
	$("table tr").click(function(){
		$($('#' + $(this).attr("href"))).siblings().hide().end().fadeIn();
	});
	</script>
<script type="text/javascript">
function sortTable(f,n){
	var rows = $('#highlightTable tbody  tr').get();
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
		$('#highlightTable').children('tbody').append(row);
	});
}
var f_tc1 = 1;
var f_tc2 = 1;
var f_tc3 = 1;
var f_tc4 = 1;
var f_tc5 = 1;
var f_tc6 = 1;
$("#tc1").click(function(){
    f_tc1 *= -1;
    var n = $(this).prevAll().length;
    sortTable(f_tc1,n);
});
$("#tc2").click(function(){
    f_tc2 *= -1;
    var n = $(this).prevAll().length;
    sortTable(f_tc2,n);
});
$("#tc3").click(function(){
    f_tc3 *= -1;
    var n = $(this).prevAll().length;
    sortTable(f_tc3,n);
});
$("#tc4").click(function(){
    f_tc4 *= -1;
    var n = $(this).prevAll().length;
    sortTable(f_tc4,n);
});
$("#tc5").click(function(){
    f_tc5 *= -1;
    var n = $(this).prevAll().length;
    sortTable(f_tc5,n);
});
$("#tc6").click(function(){
    f_tc6 *= -1;
    var n = $(this).prevAll().length;
    sortTable(f_tc6,n);
});
</script>
</body>
</html>