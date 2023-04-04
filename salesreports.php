<?php
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Sales Reports';
$linkpage = 'salesreports.php';

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

if(isset($_GET['id']))
{
	$id = $_GET['id'];
}
else{
	$id = '0';
}
//default for today
$today = date("Y-m-d");
$tomorrow = date('Y-m-d',strtotime($date1 . "+1 days"));
if(isset($_GET['startdate']))
	{
		$startdate = $_GET['startdate'];
		$enddate = $_GET['enddate'];
	}
else {
	if($id == '2')
	{
	$startdate = date('Y-m-d', strtotime($today));
	}
	else{
	$startdate = date('Y-m-d', strtotime('-6 months', strtotime($today)));
	}
	$enddate = date('Y-m-d', strtotime('+1 day', strtotime($today)));
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




if($id == '1')
{
?>
<div id="content">
        	<div id="left">
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
//$sql1 = 'SELECT `id`,`accountid`,`location`,`total` FROM `invoice` WHERE `invoicedate` = :invoicedate '.$locationsql.$isvoided.' ORDER BY `id` DESC LIMIT 50';
$sql1 = 'SELECT `id`,`accountid`,`location`,`total`,`type` FROM `invoice` WHERE `type` = \'1\' ORDER BY `id` DESC LIMIT 50';
//echo 'SELECT `id`,`accountid`,`location` FROM `invoice` WHERE `invoicedate` = \''.$selecteddate.'\' AND `type` = \'6\''.$locationsql.$isvoided.' ORDER BY `id` DESC';
$sth1 = $pdocxn->prepare($sql1);
//$sth1->bindParam(':invoicedate',$selecteddate);
$sth1->execute();
while ($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$accountid = $row1['accountid'];
	$paymentid = $row1['id'];
	$locationid = $row1['location'];
	$total = $row1['total'];
	$tinvtypeid = $row1['type'];
	
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
$sql2 = 'SELECT `id`,`name` FROM `invoice_type` WHERE `id` = :id';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':id',$tinvtypeid);
$sth2->execute();
while ($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
	{
	$invtype = $row2['name'];
	echo "<tr><td>".$selecteddate."</td><td>".$customername."</td><td>".$invtype."</td><td>".$total."</td></tr>";
	}
}
?></tbody></table></div>
</div>
<?php
}




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
$customername =$firstname." ".$lastname;	
}
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
	}
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
		$cash = $cash + $paymentamount;
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
	echo "<tr href=\"ip".$tri."\"><td><b>".$displaydate."</b></td><td><b>".$customername."</b></td><td><b>".$paymenttype."</b></td><td><b>".$totalpaymentamount."</b></td></tr>";

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
${"ip".$tri} .= "</select><input type=\"hidden\" name=\"id\" value=\"2\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$paymentid."\"><input type=\"hidden\" name=\"paymentlineid\" value=\"".$paymentlineid."\"><input type=\"hidden\" name=\"startdate\" value=\"".$startdate."\"><input type=\"hidden\" name=\"enddate\" value=\"".$enddate."\"><input type=\"hidden\" name=\"paymentsubmit\" value=\"1\"></td>";
${"ip".$tri} .= "<td><input type=\"textbox\" name=\"newpaymentamount\" value=\"".$totalpaymentamount."\" class=\"mediuminput\"></td></tr><tr><td>";
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
echo "<tr><td colspan=\"4\">No Results for the selected dates, Please try again</td></tr>";
}
?></tbody></table></div>
<div class="right">
<div class="q1">
	<?php
	$displaycash = money_format('%(#0.2n',$cash);
	$displaycheck = money_format('%(#0.2n',$check);
	$displaycreditcard = money_format('%(#0.2n',$creditcard);
	$displaycfna = money_format('%(#0.2n',$cfna);
	$totalamount = $cash + $check + $creditcard + $cfna;
	$totaldeposit = $cash + $check;
	$displaytotaldeposit = money_format('%(#0.2n',$totaldeposit);
	$displaytotal = money_format('%(#0.2n',$totalamount);
	?>
	<table>
	<tr><td><b>Cash:</b></td><td><b>$<?php echo $displaycash; ?></b></td></tr>
	<tr><td><b>Check:</b></td><td><b>$<?php echo $displaycheck; ?></b></td></tr>
	<tr><td><b>Credit Card:</b></td><td><b>$<?php echo $displaycreditcard; ?></b></td></tr>
	<tr><td><b>CFNA:</b></td><td><b>$<?php echo $displaycfna; ?></b></td></tr>
	<tr><td><br /></td></tr>
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
else if($id == '4')
{
?>
 <div id="content">
        	<div id="left">
        		<form name="update" action="accountingreports.php" method="GET">Start Date: <input type="date" name="startdate" value="<?php echo $startdate; ?>">End Date: <input type="date" name="enddate" value="<?php echo $enddate; ?>"><input type="hidden" name="id" value="4"><input type="submit" class="smallbutton" value="Update Dates"></form>
<table id="highlightTable" class="blueTable">
<thead>
<tr>
<th id="standing">Standing</th>
<th id="name">Customer Name</th>
<th id="invoice">Number of Invoices</th>
<th id="total">Total Spent</th>
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
?></tbody></table></div>
</div>
<?php
}	
?>	

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
</script>
</body>
</html>