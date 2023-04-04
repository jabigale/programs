<?php
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Confirm Appointment';
$linkpage = 'confirmappointment.php';
$showhistory = '0';

session_start();
date_default_timezone_set('America/Chicago');
$currentdate = date('Y-n-j H:i:s');
$currentday = date('Y-n-j');
$currentday2 = date('Y-m-d');
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
if(isset($_GET['accountid']))
{
    $showhistory = '1';
	$accountid = $_GET['accountid'];
	$origtype = '4';
    if(isset($_GET['vehicleid']))
    {
        $vehicleid = $_GET['vehicleid'];
    }
    $origtype = $_GET['type'];
	$sql1 = "SELECT `name` FROM `invoice_type` WHERE `id` = :selectedtype";
	$sth1 = $pdocxn->prepare($sql1);
	$sth1->bindParam(':selectedtype',$origtype);
	$sth1->execute();
	while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$origtypename = $row1['name'];
	}

}
if($showhistory == '1')
{
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

echo "<div id=\"ciheader\"></div>";

?>
<div id="contentheaderspace"></div>
        <div id="content">
		<p class="warningfont">Quotes were found for this account, would you like to use one of those for this appoinment?</p>
        	<div id="left"><form name="inventory" action="inventory.php" method="post">
<table id="highlightTable" class="blueTable">
<thead>
<tr>
<th>Date</th>
<th>Amount</th>
<th>Vehicle</th>
</tr>
</thead>
<tbody>
<?php
$tri = '1';


$sql1 = 'SELECT `id`,`invoicedate`,`type`,`total`,`vehicleid`,`mileagein`,`taxgroup` FROM `invoice` WHERE `accountid` = :accountid AND `type` = \'4\' ORDER BY `invoicedate` DESC';
$sth1 = $pdocxn->prepare($sql1);
$sth1->bindParam(':accountid',$accountid);
$sth1->execute();
	while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$vehicleid1 = $row1['vehicleid'];
	$invtax = '0';
	$invsubtotal = '0';
	$invtotal = '0';
	$date = $row1['invoicedate'];
	$displaydate = date('m/d/Y', strtotime($date));
	$taxgroup = $row1['taxgroup'];
	$id = $row1['id'];
	$type = $row1['type'];

$sql2 = 'SELECT `name` FROM `invoice_type` WHERE `id` = :type';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':type',$type);
$sth2->execute();
	while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
	{
	$typename = $row2['name'];
	}
$getvehicle = $pdocxn->prepare('SELECT `year`,`make`,`model`,`description` from `vehicles` WHERE `id` = :vehicleid');
$getvehicle->bindParam(':vehicleid',$vehicleid1);
$getvehicle->execute();
if($type == '1' OR $type == '4')
{
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
}
else{
	$abvvehicle = '';
}
if($type == '1' OR $type == '2' OR $type == '4' OR $type == '5' OR $type == '8' OR $type == '11' OR $type == '14' OR $type == '17')
{
${"ip".$tri} = "<div id=\"$tri\"><div class=\"q1\"><table class=\"righttable\"><tr>
<td class=\"center\"><a href=\"appointment.php?i=".$id."&confirm=1&confirmappointment=".$scheduleid."\" class=\"no-decoration\"><input type=\"button\" class=\"smallbutton\" name=\"submit\" value=\"Use this ".$typename."\"></a></td></tr></table></div><div class=\"q3\"><table class=\"righttable\"><tr><th>qty</th><th>comment</th><th>unit cost</th><th>total cost</td></tr>\n";
}else{
	${"ip".$tri} = "<div id=\"$tri\"><div class=\"q1\"><table class=\"righttable\"><tr><td class=\"center\"><form name=\"invoicehistory\" action=\"printinvoice.php\" method=\"post\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$id."\"><input type=\"submit\" class=\"btn-style\" name=\"submit\" value=\"Print\"></form></td><td class=\"center\"><a href=\"schedule.php?i=".$id."&q=1\" class=\"no-decoration\"><input type=\"hidden\" name=\"transactionid\" value=\"".$id."\"><input type=\"submit\" class=\"btn-style\"  value=\"Schedule\"></a></td><td class=\"center\"><a href=\"invoice.php?invoiceid=".$id."\" class=\"no-decoration\"><input type=\"button\" class=\"btn-style\" name=\"submit\" value=\"Edit ".$typename."\"></a></td></tr></table></div><div class=\"q3\"><table class=\"righttable\"><tr><th>comment</th><th>amount</td></tr>\n";
}
$sql2 = 'SELECT `qty`,`comment`,`amount`,`totallineamount`,`lineitem_typeid` FROM `line_items` WHERE `invoiceid` = :transactionid ORDER BY `linenumber` ASC';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':transactionid',$id);
$sth2->execute();
if ($sth2->rowCount() > 0)
{
while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
	{
	$qty = $row2['qty'];
	$amount = $row2['amount'];
	$comment = $row2['comment'];
	$totalamount = $row2['totallineamount'];
	$litypeid = $row2['lineitem_typeid'];
	if($totalamount != '0')
	{
	$unitcost = $totalamount / $qty;
	}else{
		$unitcost= '0';
	}
	if($type == '1' OR $type == '2' OR $type == '4' OR $type == '5' OR $type == '8' OR $type == '11' OR $type == '14' OR $type == '17')
{
${"ip".$tri} .= "\n<tr><td class=\"left\">".$qty."</td><td class=\"left\"><b>".$comment."</b></td><td class=\"left\">".$unitcost."</td><td class=\"left\"><b>".$totalamount."</b></td></tr>\n";
}else{
	${"ip".$tri} .= "\n<tr><td class=\"left\"><b>".$comment."</b></td><td class=\"left\"><b>".$totalamount."</b></td></tr>\n";
	}
	$invsubtotal = $invsubtotal + $totalamount;
}
if($type == '1' OR $type == '2' OR $type == '4' OR $type == '5' OR $type == '8' OR $type == '11' OR $type == '14' OR $type == '17')
{
	$sql3 = "SELECT SUM(`taxamount`) AS `taxtotal` FROM `tax_trans` WHERE `transid` = :invoiceid";
	$sth3 = $pdocxn->prepare($sql3);
	$sth3->bindParam(':invoiceid',$id);
	$sth3->execute();
	while($row3 = $sth3->fetch(PDO::FETCH_ASSOC))
	{
		$invtax = $row3['taxtotal'];
		$invtotal = $invsubtotal + $invtax;
	}
$invtotal = $invsubtotal + $invtax;
$invoiceformtotal = round($invtotal,2);
$dinvtax = money_format('%(#0.2n',$invtax);
$dsubtotal = money_format('%(#0.2n',$invsubtotal);
$dinvoicetotal = money_format('%(#0.2n',$invtotal);
///
//$invtax = $invsubtotal*$taxmultiply;
//$dinvtax = money_format('%(#0.2n',$invtax);
//$invtotal = $invsubtotal + $invtax;

$displayinvtotal = money_format('%(#0.2n',$invtotal);
${"ip".$tri} .= "\n<tr><td colspan=\"2\" class=\"left\">Subtotal:</td><td></td><td class=\"left\">".$dsubtotal."</td></tr>\n";
${"ip".$tri} .= "\n<tr><td colspan=\"2\" class=\"left\">Tax:</td><td></td><td class=\"left\">".$dinvtax."</td></tr>\n";
${"ip".$tri} .= "\n<tr><td colspan=\"2\" class=\"left\"><b>Total:</b></td><td></td><td class=\"left\"><b>".$displayinvtotal."</b></td></tr>\n";
}else{
	$invtotal = $invsubtotal;
	}
	$displayinvtotal = money_format('%(#0.2n',$invtotal);


${"ip".$tri} .= "</table></div></div>";
	echo "<tr href=\"$tri\"><td>$displaydate</td><td>$displayinvtotal</td><td>$abvvehicle</td></tr>\n";
$tri ++;
}
else {
${"ip".$tri} .= "</table></div></div>";
$tri ++;
}
}
echo "<tr href=\"$tri\"><td colspan=\"5\" class=\"center\"><a href=\"appointment.php?accountid=".$accountid."&cninvoice=1&invoiceid=".$scheduleid."&changecustomer=1&vehicleid=".$vehicleid."\"><input type=\"button\" class=\"quotebutton\" value=\"Create a New Appoinment \"></a></td></tr>\n";
?></tbody></table>
    </form></div><div class="right">
<?php
while ($tri > 0) {
        echo ${"ip".$tri};
		$tri --;
}
?>
</div></div>
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
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-117796361-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-117796361-1');
</script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title><?php echo $title; ?></title>
<link rel="stylesheet" type="text/css" href="style/globalstyle.css" >
<link rel="stylesheet" type="text/css" href="style/accounthistory.css" >
<script type="text/javascript" src="scripts/globaljs.js"></script>
<script type="text/javascript" src="scripts/jquery-1.9.1.js"></script>
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
        <div id="content"><form name="account" action="account.php" method="post">
        	<table class="searchtable"><tr><th>Last Name:</th><td><input type="text" name="lastname" autocomplete="off" autofocus></td><th>First Name:</th><td><input type="text" name="firstname" autocomplete="off"></td></tr>
<tr><th>Phone:</th><td><input type="text" name="phone" autocomplete="off"></td><th>Account Number:</th><td><input type="text" name="acctnumber" autocomplete="off"></td></tr>
<tr><td colspan="2"><input type="hidden" name="search" value="1"><?php if($invoiceid > '0'){?><input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>"><?php } ?><input type="submit" name="submit" class="btn-style" value="Search"></form></td><td colspan="2"><form action="newaccount.php" method="post"><input type="submit" name="submit" class="btn-style" value="New Customer"></form></td></tr>
        	</table>
        </div>
</body>
</html>
<?php
}
?>