<?php
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Recent Appointments';
$linkpage = 'recentappointments.php';
$currentday = date('Y-n-j');
$currentdate = date('Y-m-d');
$showhistory = '0';

session_start();
date_default_timezone_set('America/Chicago');
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
<div id="selecteduserfullwidth"><form name="current1" action="index.php" method="POST">
<table id="floatleft" width="100%"><tr>
<td class="currentuser">Current User:</td>
<td class="currentitem"><div class="styled-select black rounded">
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
</select></div></td>
<td class="currentstore">Current Store:</td>
<td class="currentitem"><div class="styled-select black rounded"><select name="location" onchange="form.submit()"><?php
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
</select></div><input type="hidden" name="form" value="1"></td></form>
<td><form name="update" action="accounthistory.php" method="POST">
<td> Show Only:</td>
<td><div class="styled-select black rounded"><select name="invoicetype" >
	<?php
	if($selectedtype > '0')
	{
		echo "<option value=\"".$selectedtype."\">".$selectedname."</option><option value=\"0\">All</option>";
		
		}
	else{
		echo "<option value=\"0\">All</option>";
		}
?>
<option value="1">Invoices</option><option value="4">Quotes</option><option value="6">Payments</option><option value="10">Financial Transactions</option><option value="11">Unpaid Invoices</option></select></div></td>

<td><input type="hidden" name="submit" value="1"><input type="submit" class="smallbutton" value="Update Search"></td></form></tr>
</table>
</div>

<div id="content">
        	<div id="left"><form name="inventory" action="inventory.php" method="post">
<table id="highlightTable" class="blueTable">
<thead>
<tr>
<th>Date</th>
<th>Customer Name</th>
<th>Amount</th>
<th>Vehicle</th>
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
if($selectedtype > '0')
{
$invtype = 'AND `type` = '.$selectedtype.' ';
}
else{$invtype = '';}
$sql1 = 'SELECT `id`,`accountid`,`date`,`type`,`total`,`voiddate`,`vehicleid`,`taxgroup` FROM `'.$locschedule.'` WHERE `voiddate` IS NULL AND `thread` IS NULL ORDER BY `creationdate` DESC LIMIT 30';
$sth1 = $pdocxn->prepare($sql1);
$sth1->execute();
	while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
    $customerid = $row1['accountid'];
	$vehicleid1 = $row1['vehicleid'];
	$invtax = '0';
	$invsubtotal = '0';
	$invtotal = '0';
	$date = $row1['date'];
	$invoicedate2 = new DateTime($date);
	$displayinvoicedate2 = $invoicedate2->format('Y-m-d');
	$displaydate = date('m/d/Y g:i', strtotime($date));
	$taxgroup = $row1['taxgroup'];
	$id = $row1['id'];
	$type = $row1['type'];

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

$sth5 = $pdocxn->prepare('SELECT `firstname`,`lastname` FROM `accounts` WHERE `accountid` = \''.$customerid.'\'');
$sth5->execute();
while($row5 = $sth5->fetch(PDO::FETCH_ASSOC))
{
$databasefname = $row5['firstname'];
$firstname = stripslashes($databasefname);
$databaselname = $row5['lastname'];
$lastname = stripslashes($databaselname);
$fullname = $firstname." ".$lastname;
}

if($type == '1' OR $type == '2' OR $type == '4' OR $type == '5' OR $type == '8' OR $type == '11' OR $type == '14' OR $type == '17')
{
${"ip".$tri} = "<div id=\"$tri\"><div class=\"q1\"><table class=\"righttable\"><tr><td class=\"center\"><form name=\"invoicehistory\" action=\"printinvoice.php\" method=\"post\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$id."\"><input type=\"submit\" class=\"btn-style\" name=\"submit\" value=\"Print\"></form></td><td class=\"center\"><a href=\"schedule.php?i=".$id."&q=1\" class=\"no-decoration\"><input type=\"hidden\" name=\"transactionid\" value=\"".$id."\"><input type=\"submit\" class=\"btn-style\"  value=\"Schedule\"></a></td><td class=\"center\"><a href=\"appointment.php?invoiceid=".$id."\" target=\"_BLANK\" class=\"no-decoration\"><input type=\"button\" class=\"btn-style\" name=\"submit\" value=\"Edit ".$typename."\"></a></td></tr></table></div><div class=\"q3\"><table class=\"righttable\"><tr><th>qty</th><th>comment</th><th>unit cost</th><th>total cost</td></tr>\n";
}else{
	${"ip".$tri} = "<div id=\"$tri\"><div class=\"q1\"><table class=\"righttable\"><tr><td class=\"center\"><form name=\"invoicehistory\" action=\"printpayment.php\" method=\"post\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$id."\"><input type=\"submit\" class=\"btn-style\" name=\"submit\" value=\"Print\"></form></td><td class=\"center\"></td><td class=\"center\"><a href=\"appointment.php?invoiceid=".$id."\" target=\"_BLANK\" class=\"no-decoration\"><input type=\"button\" class=\"btn-style\" name=\"submit\" value=\"Edit Appointment\"></a></td></tr></table></div><div class=\"q3\"><table class=\"righttable\"><tr><th>comment</th><th>amount</td></tr>\n";
}	
$sql2 = 'SELECT `qty`,`comment`,`amount`,`totallineamount`,`lineitem_typeid` FROM `'.$lischedule.'` WHERE `invoiceid` = :transactionid ORDER BY `linenumber` ASC';
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
	if($litypeid == '25')
	{
		$comment = 'Change given';
	}
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

	if($displayinvoicedate2 > '2020-04-04')
	{
	$taxsql = "SELECT SUM(`taxamount`) AS `taxtotal` FROM `tax_trans` WHERE `transid` = :invoiceid";
	$taxsth = $pdocxn->prepare($taxsql);
	$taxsth->bindParam(':invoiceid',$id);
	$taxsth->execute();
	while($taxrow = $taxsth->fetch(PDO::FETCH_ASSOC))
	{
		$invtax = $taxrow['taxtotal'];
	}}else{
		$taxsql = "SELECT SUM(`taxamount`) AS `taxtotal` FROM `tax_trans_old` WHERE `transid` = :invoiceid";
		$taxsth = $pdocxn->prepare($taxsql);
		$taxsth->bindParam(':invoiceid',$id);
		$taxsth->execute();
		while($taxrow = $taxsth->fetch(PDO::FETCH_ASSOC))
		{
			$invtax = $taxrow['taxtotal'];
		}
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
	echo "<tr href=\"$tri\"><td><b>$displaydate</b></td><td><b>".$fullname."</b></td><td>$displayinvtotal</td><td><b>$abvvehicle</b></td></tr>\n";
$tri ++;
}
else {
${"ip".$tri} .= "</table></div></div>";
$tri ++;
}
}
?></tbody></table>
    </form></div><div class="right">
<?php
while ($tri > 0) {
        echo ${"ip".$tri};
		$tri --;
}
?>
<div id="newtransaction"><div class="q1"></div><div class="q3">
<form name="newtransaction" action="inserttransactions.php" method="post">
<table class="righttable">
<tr><td>Transaction Date:</td><td><input type="date" name="transdate" value="<?php echo $currentdate; ?>"></td></tr>
<tr><td>Transaction Type:</td><td><select name="transactiontype"><option value="0">Select Transaction</option>
<?php
$sql1 = 'SELECT `id`,`name` FROM `invoice_type` WHERE `customerinsertselect` = \'1\'';
$sth1 = $pdocxn->prepare($sql1);
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
        $optionid = $row1['id'];
        $optionname = $row1['name'];
echo '<option value="'.$optionid.'">'.$optionname.'</option>';
    }
?>
</select></td>
</tr>
<tr><td>Transaction Amount</td><td><input type="textbox" name="transamount" placeholder="amount" size="10"></td></tr>
<tr><td colspan="2"><textarea name="comment" placeholder="comment"></textarea></td></tr>
<tr><td colspan="2" class="center">
<input type="hidden" name="inserttransaction" value="1">
<input type="hidden" name="accountid" value="<?php echo $accountid; ?>">
<input type="submit" class="btn-style" name="submit" value="Submit"></td>
</tr></table></form></div>
</div></div></div></div>
</div></div>
<script type="text/javascript">
$("table tr").click(function(){
    $($('#' + $(this).attr("href"))).siblings().hide().end().fadeIn();
});
</script>
</body>
</html>