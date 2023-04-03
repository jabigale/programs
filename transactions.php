<?php
//submitted form
//default search invoices today
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Accounts';
$linkpage = 'account.php';
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
//default for today
$today = date("Y-m-d");
$tomorrow = date('Y-m-d',strtotime($date1 . "+1 days"));
$begindate = $today;
$enddate = $tomorrow;
$type = '1';
//submitted form
if(isset($_POST['submit']))
{
$invtype = $_POST['type'];
$begindate = $_POST['begindate'];
$enddate = $_POST['enddate'];

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" >
<link rel="stylesheet" type="text/css" href="style/globalstyle.css" >
<link rel="stylesheet" type="text/css" href="style/transactionstyle.css" >
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
        <div id="content">
        	<div id="left">
<table id="highlightTable" class="blueTable">
<thead>
<tr>
<th>Invoice</th>
<th>Date</th>
<th>Name</th>
<th>Vehicle</th>
<th>Amount</th>
</tr>
</thead>
<tbody>
<?php
$sbi = '1';
$tri = '1';
//default search invoices today

	$sql1 = "SELECT * FROM `invoice` WHERE `invoicedate` >= :begindate AND `invoicedate` <= :enddate AND `type` = :type AND `voiddate` IS NULL";
	$sth1 = $pdocxn->prepare($sql1);
	$sth1->bindParam(':begindate',$begindate);
	$sth1->bindParam(':enddate',$enddate);
	$sth1->bindParam(':type',$type);
$sth1->execute();
if ($sth1->rowCount() > 0) {
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$invoiceid = $row1['id'];
if($type =='1')
{
	$invid = $row1['invoiceid'];
}else{
	$invid = $invoiceid;
}
	$accountid = $row1['accountid'];
	$userid = $row1['userid'];
	$location = $row1['location'];
	$vehicleid = $row1['vehicleid'];
	$invsubtotal = $row1['total'];
	$invoicedate = $row1['invoicedate'];

$sql2 = "SELECT * FROM `accounts` WHERE `acctid` = :acctid";
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':acctid',$accountid);
$sth2->execute();
while ($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
	{
$fullname = '';
	$firstname = $row2['firstname'];
	$lastname = $row2['lastname'];
	$fullname = $firstname." ".$lastname;
	}
$displayvehicle = '';
$sql3 = "SELECT`year`,`make`,`model`,`description`,`cfdescription` FROM `vehicles` WHERE `id` = :vehicleid";
$sth3 = $pdocxn->prepare($sql3);
$sth3->bindParam(':vehicleid',$vehicleid);
$sth3->execute();
while ($row3 = $sth3->fetch(PDO::FETCH_ASSOC))
	{
	$year = $row3['year'];
	$make = $row3['make'];
	$model = $row3['model'];
if($year > '0')
{
	$displayvehicle = $year." ".$make." ".$model;
}else{
$displayvehicle = $row3['description'];
}
if($displayvehicle < '1')
{
$displayvehicle = $row3['cfdescription'];
}}
$invsubtotal = '0';
/*
$sql4 = "SELECT SUM(totallineamount)as`invsubtotal` FROM `line_items` WHERE `invoiceid` = :invoiceid";
$sth4 = $pdocxn->prepare($sql4);
$sth4->bindParam(':invoiceid',$invoiceid);
$sth4->execute();
while ($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
	{
	$invsubtotal = $row4['invsubtotal'];
}
*/
	echo "<tr href=\"$tri\"><td>$invid</td><td>$invoicedate</td><td>$fullname</td><td>$displayvehicle</td><td>$".$invsubtotal."</td></tr>";
${"ip".$tri} = "<div id=\"$tri\"><div class=\"q1\">Test</div><div class=\"q3\"></div>";
$tri ++;
}}
else
{
echo "<tr href=\"$tri\"><td>&nbsp;</td><td></td><td></td><td></td></tr>";
}
?></tbody></table></div>
<div class="right"><table><tr><td>Select Type:</td><td><form name="transactions" action="transactions.php" method="POST">
<select name="type">
<?php
echo "<option value=\"1\">Invoice</option>";
$sql4 = "SELECT * FROM `invoice_type` ORDER by `name` ASC";
$sth4 = $pdocxn->prepare($sql4);
$sth4->execute();
while ($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
	{
	$invtypename = $row4['name'];
	$invtypeid = $row4['id'];
echo "<option value=\"$invtypeid\">$invtypename</option>";
}
?>
</select></td></tr><tr><td>Start Date:</td><td><input type="date" name="begindate" value="<?php echo $today; ?>"></td></tr><tr><td>End Date:</td><td><input type="date" name="enddate" value="<?php echo $today; ?>">
<tr><td><input type="submit" name="submit" value="Search">
</form>
</div>
<div class="below"><?php
while ($tri > 0) {
        echo ${"ip".$tri};
		$tri --;
}
?></div></div>
<script type="text/javascript">
$("table tr").click(function(){
    $($('#' + $(this).attr("href"))).siblings().hide().end().fadeIn();
});
</script>
</body>
</html>
<?php
}
else {
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" >
<link rel="stylesheet" type="text/css" href="style/globalstyle.css" >
<link rel="stylesheet" type="text/css" href="style/transactionstyle.css" >
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
        <div id="content">
<br/><br/><br/><br/>
        	<div id="left">
<table id="highlightTable" class="blueTable">
<thead>
<tr>
<th>Invoice</th>
<th>Name</th>
<th>Vehicle</th>
<th>Amount</th>
</tr>
</thead>
<tbody>
<?php
$sbi = '1';
$tri = '1';
//default search invoices today

	$sql1 = "SELECT * FROM `invoice` WHERE `creationdate` >= :begindate AND `creationdate` <= :enddate AND `type` = :type";
	$sth1 = $pdocxn->prepare($sql1);
	$sth1->bindParam(':begindate',$begindate);
	$sth1->bindParam(':enddate',$enddate);
	$sth1->bindParam(':type',$type);
$sth1->execute();
if ($sth1->rowCount() > 0) {
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$invid = $row1['id'];
	$accountid = $row1['accountid'];
	$userid = $row1['userid'];
	$location = $row1['location'];
	$vehicleid = $row1['vehicleid'];


$sql2 = "SELECT * FROM `accounts` WHERE `acctid` = :acctid";
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':acctid',$accountid);
$sth2->execute();
while ($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
	{
	$firstname = $row2['firstname'];
	$lastname = $row2['lastname'];
	$fullname = $firstname." ".$lastname;
	}
$sql3 = "SELECT * FROM `vehicles` WHERE `id` = :vehicleid";
$sth3 = $pdocxn->prepare($sql3);
$sth3->bindParam(':vehicleid',$vehicleid);
$sth3->execute();
while ($row3 = $sth3->fetch(PDO::FETCH_ASSOC))
	{
	$year = $row3['year'];
	$make = $row3['make'];
	$model = $row3['model'];
	$displayvehicle = $year." ".$make." ".$model;
	}
	echo "<tr href=\"$tri\"><td>$invid</td><td>$fullname</td><td>$displayvehicle</td><td>$654</td></tr>";
${"ip".$tri} = "<div id=\"$tri\"><div class=\"q1\">Test</div><div class=\"q3\"></div>";
$tri ++;
}}
else
{
echo "<tr href=\"$tri\"><td>&nbsp;</td><td></td><td></td><td></td></tr>";
}
?></tbody></table></div>
<div class="right"><table><tr><td>Select Type:</td><td><form name="transactions" action="transactions.php" method="POST">
<select name="type">
<?php
echo "<option value=\"1\">Invoice</option>";
$sql4 = "SELECT * FROM `invoice_type` ORDER by `name` ASC";
$sth4 = $pdocxn->prepare($sql4);
$sth4->execute();
while ($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
	{
	$invtypename = $row4['name'];
	$invtypeid = $row4['id'];
echo "<option value=\"$invtypeid\">$invtypename</option>";
}
?>
</select></td></tr><tr><td>Start Date:</td><td><input type="date" name="begindate" value="<?php echo $today; ?>"></td></tr><tr><td>End Date:</td><td><input type="date" name="enddate" value="<?php echo $today; ?>">
<tr><td><input type="submit" name="submit" value="Search">
</form>
</div>
<div class="below"><?php
while ($tri > 0) {
        echo ${"ip".$tri};
		$tri --;
}
?></div></div>
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