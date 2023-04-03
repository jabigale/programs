<?php
//n2upd
//submit form
//pull customer info from database
//display customer interactions
//form

//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Account History';
$linkpage = 'acounthistory.php';
$changecustomer = '0';
$currentday = date('Y-n-j');
$currentdate = date('Y-m-d');
$showhistory = '0';

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
$invoiceid = $_POST['invoiceid'];
}
if(isset($_GET['accountid']))
{
    $accountid = $_GET['accountid'];
$showhistory = '1';
$startdate = date('Y-m-d', strtotime('-6 months', strtotime($currentday)));
$enddate = date('Y-m-d', strtotime('+1 day', strtotime($currentday)));
}
if(isset($_GET['vehicleid']))
{
$vehicleid = $_GET['vehicleid'];
$showhistory = '1';
$startdate = date('Y-m-d', strtotime('-6 months', strtotime($currentday)));
$enddate = date('Y-m-d', strtotime('+1 day', strtotime($currentday)));
}
if(isset($_POST['submit']))
{
//submit form
if(isset($_POST['inserttransaction']))
{
$transdate = $_POST['transdate'];
$transtype = $_POST['transactiontype'];
$transamount = $_POST['transamount'];
$transcomment = $_POST['comment'];
$accountid = $_POST['accountid'];


$sth1 = $pdocxn->prepare('SELECT `lineitem_typeid` FROM `typelink` WHERE `invoice_typeid` = :invoice_typeid');
$sth1->bindParam(':invoice_typeid',$transtype);
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
 $lineitem_typeid = $row1['lineitem_typeid'];
}

$sth2 = $pdocxn->prepare('INSERT INTO `invoice`(`userid`,`type`,`location`,`creationdate`,`invoicedate`,`accountid`) VALUES (:userid,:typeid,:location,:creationdate,:invoicedate,:accountid)');
$sth2->bindParam(':userid',$currentid);
$sth2->bindParam(':typeid',$transtype);
$sth2->bindParam(':location',$currentlocationid);
$sth2->bindParam(':creationdate',$currentdate);
$sth2->bindParam(':invoicedate',$transdate);
$sth2->bindParam(':accountid',$accountid);
$sth2->execute()or die(print_r($sth2->errorInfo(), true));
$invoiceid = $pdocxn->lastInsertId();

$linenumber = '1';
$qty = '1';
$sth2 = $pdocxn->prepare('INSERT INTO `line_items`(`invoiceid`,`comment`,`linenumber`,`qty`,`amount`,`totallineamount`,`lineitem_typeid`) VALUES (:invoiceid,:comment,:linenumber,:qty,:amount,:totallineamount,:lineitem_typeid)');
$sth2->bindParam(':invoiceid',$invoiceid);
$sth2->bindParam(':comment',$transcomment);
$sth2->bindParam(':linenumber',$linenumber);
$sth2->bindParam(':qty',$qty);
$sth2->bindParam(':amount',$transamount);
$sth2->bindParam(':totallineamount',$transamount);
$sth2->bindParam(':lineitem_typeid',$lineitem_typeid);
$sth2->execute()or die(print_r($sth2->errorInfo(), true));

$sql2 = 'SELECT `journal` FROM `invoice_type` WHERE `id` = :typeid';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':typeid',$transtype);
$sth2->execute();
while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
{
 $confirmjournal = $row2['journal'];
}

if($confirmjournal = '1')
{
if($transtype == '6' OR $transtype == '18')
{
	$transamount = $transamount * -1;
}

$sql2 = 'INSERT INTO `journal` (`invoiceid`,`accountid`,`total`,`invoicedate`,`journaltype`,`siteid`) VALUES (:invoiceid,:accountid,:total,:invoicedate,:journaltype,:siteid)';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':invoiceid',$invoiceid);
$sth2->bindParam(':accountid',$accountid);
$sth2->bindParam(':total',$transamount);
$sth2->bindParam(':invoicedate',$transdate);
$sth2->bindParam(':journaltype',$transtype);
$sth2->bindParam(':siteid',$currentlocationid);
$sth2->execute()or die(print_r($sth2->errorInfo(), true));
}}

	$showhistory = '1';
	$fullname = $_POST['fullname'];
	$accountid = $_POST['accountid'];
	$selectedtype = $_POST['invoicetype'];
	$sql1 = "SELECT `name-plural` FROM `invoice_type` WHERE `id` = :selectedtype";
	$sth1 = $pdocxn->prepare($sql1);
	$sth1->bindParam(':selectedtype',$selectedtype);
	$sth1->execute();
	while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$selectedname = $row1['name-plural'];
	}
	if(isset($_POST['voided'])&&$_POST['voided'] == '1')
	{
	$voidsql = '1';
	}
	else{$voidsql = '0';}
if(isset($_POST['startdate']))
	{
		$startdate = $_POST['startdate'];
		$enddate = $_POST['enddate'];
		$enddate = date('Y-m-d', strtotime('+1 day', strtotime($enddate)));
	}
else {
	$startdate = date('Y-m-d', strtotime('-6 months', strtotime($currentday)));
	$enddate = date('Y-m-d', strtotime('+1 day', strtotime($currentday)));
}
}
if(isset($_GET['accountid']))
{
	$accountid = $_GET['accountid'];
	$sql2 = "SELECT `firstname`,`lastname` FROM `accounts` WHERE `accountid` = :accountid";
	$sth2 = $pdocxn->prepare($sql2);
	$sth2->bindParam(':accountid',$accountid);
	$sth2->execute();
	while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
	{
	$fname = $row2['firstname'];
	$lname = $row2['lastname'];
	$fullname = $fname." ".$lname;
	}}
if(isset($_GET['vehicleid']))
{
	$vehicleid = $_GET['vehicleid'];
}else{
	$vehicleid = '';
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
<title><?php echo $fullname; ?></title>
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
<script type="text/javascript">
function close_window() {
    window.close();
}
</script>
</head>
<body>
<div id="ciheader">
<div class="headerright"><a href="javascript:close_window();" class="no-decoration"><input type="button" class="save" value="<?php echo $fullname; ?>"></a></div>
<div class="headerleft"><a href="javascript:close_window();" class="no-decoration"><input type="button" class="cancel" value="Cancel/Exit"></a></div>
<div class="headercenter"><a href="inventory.php?accountid=<?php echo $accountid; ?>&ci=1" onmouseover="popup('inventory')"><img src="images/icons/tire-white.png" height="40"></a><a href="schedule.php?r=<?php echo $r; ?>&accountid=<?php echo $accountid; ?>" onmouseover="popup('scheduler')"><img src="images/icons/schedule.png" height="40"></a><a href="customerinteraction-invoice.php?accountid=<?php echo $accountid; ?>" onmouseover="popup('transactions')"><img src="images/icons/phone.png" height="40"></a></div></div>
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
<td><form name="update" action="accounthistory.php?accountid=<?php echo $accountid; ?>" method="POST"><input type="hidden" name="accountid" value="<?php echo $accountid; ?>">
<?php
if($vehicleid > '0')
{
echo "<input type=\"hidden\" name=\"vehicleid\" value=\"".$vehicleid."\">";
}
?>
Include Voided Transactions<input type="checkbox" name="voided" value="1"></td><td> Show Only:</td>
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
<td>Start Date: <input type="date" name="startdate" value="<?php echo $startdate; ?>"></td>
<td>End Date: <input type="date" name="enddate" value="<?php echo $enddate; ?>"></td>
<td><input type="hidden" name="submit" value="1"><input type="submit" class="smallbutton" value="Update Search"></td></form></tr>




<tr href="newtransaction"><td colspan="2"><input type="button" class="quotebutton" value="Insert New Transaction"></td>
<td colspan="2"><a href="statements.php?accountid=<?php echo $accountid; ?>"><input type="button" class="quotebutton" value="Print Statement"></a></td>
<td colspan="2"><p class="titletext">Account Balance at <?php echo $currentstorename; ?>:</p></td>
<td><p class="titletext">
<?php
$balsql1 = 'SELECT SUM(`total`) AS `storebalance` FROM `journal` WHERE `accountid` = :accountid AND `siteid` = :siteid';
$balsth1 = $pdocxn->prepare($balsql1);
$balsth1->bindParam(':accountid',$accountid);
$balsth1->bindParam(':siteid',$currentlocationid);
$balsth1->execute();
while($balrow1 = $balsth1->fetch(PDO::FETCH_ASSOC))
{
$storebalance1 = $balrow1['storebalance'];
$storebalance = money_format('%(#0.2n',$storebalance1);
$newbalance = $storebalance;
$dnewbalance = money_format('%(#0.2n',$newbalance);
echo '<u>'.$storebalance.'</u>';
}
//edited today
?></p></td><td><p class="titletext">Account Balance all Stores:</p></td><td><p class="titletext">
<?php
$balsql2 = 'SELECT SUM(`total`) AS `totalbalance` FROM `journal` WHERE `accountid` = :accountid';
$balsth2 = $pdocxn->prepare($balsql2);
$balsth2->bindParam(':accountid',$accountid);
$balsth2->execute();
while($balrow2 = $balsth2->fetch(PDO::FETCH_ASSOC))
{
$totalbalance1 = $balrow2['totalbalance'];
$totalbalance = money_format('%(#0.2n',$totalbalance1);
echo '<u>'.$totalbalance.'</u>';
}
?>
</p></td></tr></table>
</div>
        <div id="content">

        	<div id="left"><form name="inventory" action="inventory.php" method="post">
<table id="highlightTable" class="blueTable">
<thead>
<tr>
<th>Date</th>
<th>Type</th>
<th>Amount</th>
<th>Vehicle</th>
<th>Mileage</th>
<th>Balance</th>
</tr>
</thead>
<tbody>
<?php
//pull customer info from database
$tri = '1';
if($voidsql == '1')
{
$void = "";
}
else{
$void = "AND `voiddate` IS NULL ";
}
$sitesql = 'AND `location` = :siteid';
if($selectedtype > '0')
{
$invtype = 'AND `type` = '.$selectedtype.' ';
}
else{$invtype = '';}
if($vehicleid > '0')
{
$sql1 = 'SELECT `id`,`invoicedate`,`invoiceid`,`type`,`total`,`voiddate`,`vehicleid`,`mileagein`,`taxgroup`,`roa` FROM `invoice` WHERE `vehicleid` = :vehicleid '.$void.'AND `invoicedate` > :startdate AND `invoicedate` < :enddate '.$typeselect.''.$invtype.'ORDER BY `invoicedate` DESC, `id` DESC';
}else{
$sql1 = 'SELECT `id`,`invoicedate`,`invoiceid`,`type`,`total`,`voiddate`,`vehicleid`,`mileagein`,`taxgroup`,`roa` FROM `invoice` WHERE `accountid` = :accountid '.$sitesql.' '.$void.'AND `invoicedate` > :startdate AND `invoicedate` < :enddate '.$typeselect.''.$invtype.'ORDER BY `invoicedate` DESC, `id` DESC';
}
$sth1 = $pdocxn->prepare($sql1);
if($vehicleid > '0')
{
$sth1->bindParam(':vehicleid',$vehicleid);
}
if($accountid > '0')
{
$sth1->bindParam(':accountid',$accountid);
}
$sth1->bindParam(':siteid',$currentlocationid);
$sth1->bindParam(':startdate',$startdate);
$sth1->bindParam(':enddate',$enddate);
$sth1->execute();
	while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$vehicleid1 = $row1['vehicleid'];
	$mileage1 = $row1['mileagein'];
	if($mileage1 > '0')
	{
	$mileage = number_format($mileage1);
	}else{
	$mileage = '';
	}
	$invtax = '0';
	$invsubtotal = '0';
	$invtotal = '0';
	$displayid = $row1['invoiceid'];
	$date = $row1['invoicedate'];
	$invoicedate2 = new DateTime($date);
	$displayinvoicedate2 = $invoicedate2->format('Y-m-d');
	$displaydate = date('m/d/Y', strtotime($date));
	$taxgroup = $row1['taxgroup'];
	$id = $row1['id'];
	$type = $row1['type'];
	if($type == '1')
	{
		$showid = $displayid;
	}else{
		$showid = $id;
	}
	$voiddate = $row1['voiddate'];
	if ($voiddate == NULL)
	{
		$voidedbrand = "";
		$voidbutton = '0';
	}
else
	{
		$voidedbrand ="<font color=\"red\">*VOIDED*</font>";
		$voidbutton = '1';
	}
	$roa = $row1['roa'];
	if ($roa == '1')
	{
		$roabrand ="**ROA**";
	}
else
	{
		$roabrand ="";
	}
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
	if($voidbutton == '1')
	{${"ip".$tri} = "<div id=\"$tri\"><div class=\"q1\"><table class=\"righttable\"><tr><td class=\"center\"><a href=\"restoreinvoice.php?invoiceid=".$id."\" target=\"_BLANK\" class=\"no-decoration\"><input type=\"button\" class=\"btn-style\" name=\"submit\" value=\"Restore ".$typename."\"></a></td></tr></table></div><div class=\"q3\"><table class=\"righttable\"><tr><th>comment</th><th>amount</td></tr>\n";

	}else{

${"ip".$tri} = "<div id=\"$tri\"><div class=\"q1\"><table class=\"righttable\"><tr><td class=\"center\"><form name=\"invoicehistory\" action=\"printinvoice.php\" method=\"post\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$id."\"><input type=\"submit\" class=\"btn-style\" name=\"submit\" value=\"Print\"></form></td><td class=\"center\"><a href=\"schedule.php?i=".$id."&q=1\" class=\"no-decoration\"><input type=\"hidden\" name=\"transactionid\" value=\"".$id."\"><input type=\"submit\" class=\"btn-style\"  value=\"Schedule\"></a></td><td class=\"center\"><a href=\"invoice.php?invoiceid=".$id."\" target=\"_BLANK\" class=\"no-decoration\"><input type=\"button\" class=\"btn-style\" name=\"submit\" value=\"Edit ".$typename."\"></a></td></tr></table></div><div class=\"q3\"><table class=\"righttable\"><tr><th>qty</th><th>comment</th><th>unit cost</th><th>total cost</td></tr>\n";
}}else{
	if($voidbutton == '1')
	{
		${"ip".$tri} = "<div id=\"$tri\"><div class=\"q1\"><table class=\"righttable\"><tr><td class=\"center\"><a href=\"restoreinvoice.php?invoiceid=".$id."\" target=\"_BLANK\" class=\"no-decoration\"><input type=\"button\" class=\"btn-style\" name=\"submit\" value=\"Restore ".$typename."\"></a></td></tr></table></div><div class=\"q3\"><table class=\"righttable\"><tr><th>comment</th><th>amount</td></tr>\n";
	}else{
	${"ip".$tri} = "<div id=\"$tri\"><div class=\"q1\"><table class=\"righttable\"><tr><td class=\"center\"><form name=\"invoicehistory\" action=\"printpayment.php\" method=\"post\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$id."\"><input type=\"submit\" class=\"btn-style\" name=\"submit\" value=\"Print\"></form></td><td class=\"center\"></td><td class=\"center\"><a href=\"invoice.php?invoiceid=".$id."\" target=\"_BLANK\" class=\"no-decoration\"><input type=\"button\" class=\"btn-style\" name=\"submit\" value=\"Edit ".$typename."\"></a></td></tr></table></div><div class=\"q3\"><table class=\"righttable\"><tr><th>comment</th><th>amount</td></tr>\n";
}}
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


$displayinvtotal = money_format('%(#0.2n',$invtotal);
${"ip".$tri} .= "\n<tr><td colspan=\"2\" class=\"left\">Subtotal:</td><td></td><td class=\"left\">".$dsubtotal."</td></tr>\n";
${"ip".$tri} .= "\n<tr><td colspan=\"2\" class=\"left\">Tax:</td><td></td><td class=\"left\">".$dinvtax."</td></tr>\n";
${"ip".$tri} .= "\n<tr><td colspan=\"2\" class=\"left\"><b>Total:</b></td><td></td><td class=\"left\"><b>".$displayinvtotal."</b></td></tr>\n";
}else{
	$invtotal = $invsubtotal;
	}
	$displayinvtotal = money_format('%(#0.2n',$invtotal);


${"ip".$tri} .= "<tr><td colspan=\"4\"><br /><br /><br /><br /></td></tr></table></div></div>";
	echo "<tr href=\"$tri\"><td>$displaydate</td><td><b>".$voidedbrand." ".$roabrand." ".$typename." #".$showid."</b></td><td>$displayinvtotal</td><td>$abvvehicle</td><td>$mileage</td><td>$dnewbalance</td></tr>\n";
$tri ++;
if($type == '1' OR $type == '3' OR $type == '6' OR $type == '7' OR $type == '14' OR $type == '16' OR $type == '17' OR $type == '18' OR $type == '19')
	{
$balsql1 = 'SELECT `total` FROM `journal` WHERE `invoiceid` = :invoiceid';
$balsth1 = $pdocxn->prepare($balsql1);
$balsth1->bindParam(':invoiceid',$id);
$balsth1->execute();
while($balrow1 = $balsth1->fetch(PDO::FETCH_ASSOC))
{
$journalamount = $balrow1['total'];
$newbalance = $newbalance - $journalamount;
$dnewbalance = money_format('%(#0.2n',$newbalance);
}}
}
else {
${"ip".$tri} .= "<tr><td colspan=\"4\"><br /><br /><br /><br /></td></tr></table></div></div>";
$tri ++;
}
}

//display customer interactions
echo "<tr><td colspan=\"6\" bgcolor=\"gray\"><center><b>Customer Interactions/Recommendations Below</b></center></td></tr>\n";
if($vehicleid > '0')
{
$sql1 = 'SELECT `id`,`invoicedate`,`type`,`total`,`voiddate`,`taxgroup`,`abvvehicle` FROM `customerinteractions` WHERE `vehicleid` = :vehicleid AND `invoicedate` > :startdate AND `invoicedate` < :enddate ORDER BY `invoicedate` DESC';
}else{
$sql1 = 'SELECT `id`,`invoicedate`,`type`,`total`,`voiddate`,`taxgroup`,`abvvehicle` FROM `customerinteractions` WHERE `accountid` = :accountid AND `invoicedate` > :startdate AND `invoicedate` < :enddate ORDER BY `invoicedate` DESC';
}
$sth1 = $pdocxn->prepare($sql1);
if($vehicleid > '0')
{
$sth1->bindParam(':vehicleid',$vehicleid);
}
if($accountid > '0')
{
$sth1->bindParam(':accountid',$accountid);
}
$sth1->bindParam(':startdate',$startdate);
$sth1->bindParam(':enddate',$enddate);
$sth1->execute();
	while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$invtax = '0';
	$invsubtotal = '0';
	$invtotal = '0';
	$date = $row1['invoicedate'];
	$displaydate = date('n/d/Y', strtotime($date));
	$taxgroup = $row1['taxgroup'];
	$id = $row1['id'];
	$type = $row1['type'];
	$voiddate = $row1['voiddate'];
	$abvvehicle = $row1['abvvehicle'];
	if ($voiddate['column'] == NULL)
	{
		$voidedbrand ="";
		$voidbutton = '0';
	}
else
	{
		$voidedbrand ="*VOIDED*";
		$voidbutton = '1';
	}
$sql2 = 'SELECT `name` FROM `invoice_type` WHERE `id` = :type';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':type',$type);
$sth2->execute();
	while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
	{
	$typename = $row2['name'];
	}

${"ip".$tri} = "<div id=\"$tri\"><div class=\"q1\"><table class=\"righttable\"><tr><td class=\"center\"><form name=\"invoicehistory\" action=\"printcustomerinteraction.php?accountid=".$accountid."\" method=\"post\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$id."\"><input type=\"submit\" class=\"btn-style\" name=\"submit\" value=\"Print\"></form></td><td class=\"center\"><a href=\"schedule.php?r=".$r."&q=1&i=".$invoiceid."&accountid=".$accountid."\" class=\"no-decoration\"><input type=\"button\" class=\"btn-style\" name=\"submit\" value=\"Schedule\"></a></td><td class=\"center\"><form name=\"invoicehistory\" action=\"customerinteraction-invoice.php?accountid=".$accountid."\" method=\"post\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$id."\"><input type=\"submit\" class=\"btn-style\" name=\"submit\" value=\"Edit ".$typename."\"></form></td></tr></table></div><div class=\"q3\"><table class=\"righttable\"><tr><th>qty</th><th>comment</th><th>unit cost</th><th>total cost</td></tr>\n";
$sql2 = 'SELECT `qty`,`comment`,`amount`,`totallineamount` FROM `ci_line_items` WHERE `invoiceid` = :transactionid ORDER BY `linenumber` ASC';
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
	$unitcost = $totalamount / $qty;
${"ip".$tri} .= "\n<tr><td class=\"left\">".$qty."</td><td class=\"left\"><b>".$comment."</b></td><td class=\"left\">".$unitcost."</td><td class=\"left\"><b>".$totalamount."</b></td></tr>\n";
$invsubtotal = $invsubtotal + $totalamount;
}
$sql3 = "SELECT SUM(taxamount) as `invtax` FROM `tax_trans` WHERE `transid` = :transactionid";
$sth3 = $pdocxn->prepare($sql3);
$sth3->bindParam(':transactionid',$id);
$sth3->execute();
if ($sth3->rowCount() > 0)
{
while($row3 = $sth3->fetch(PDO::FETCH_ASSOC))
	{
$invtax = $row3['invtax'];
}
}
else{
	$invtax = '0';
}
$invtotal = $invsubtotal + $invtax;
$displayinvtotal = money_format('%(#0.2n',$invtotal);

${"ip".$tri} .= "<tr><td colspan=\"4\"><br /><br /><br /><br /></td></tr></table></div></div>";
	echo "<tr href=\"$tri\"><td><b>$displaydate</b></td><td><b>$voidedbrand $typename </b></td><td>".$displayinvtotal."</td><td>$abvvehicle</td><td>$mileage</td><td></td></tr>\n";
$tri ++;
}
else {
${"ip".$tri} .= "<tr><td colspan=\"4\"><br /><br /><br /><br /></td></tr></table></div></div>";
$tri ++;
}}


$appointmentdate = date('Y-m-d', strtotime('-30 day', strtotime($currentday)));
echo "<tr><td colspan=\"6\" bgcolor=\"gray\"><center><b>Upcoming Appointments Below</b></center></td></tr>\n";
if($vehicleid > '0')
{
$sql1 = 'SELECT `id`,`date`,`type`,`total`,`voiddate`,`abvvehicle`,`invoicedate`,`taxgroup` FROM `'.$scheduletable.'` WHERE `vehicleid` = :vehicleid AND `invoicedate` > :startdate ORDER BY `invoicedate` ASC';
}else{
$sql1 = 'SELECT `id`,`date`,`type`,`total`,`voiddate`,`abvvehicle`,`invoicedate`,`taxgroup` FROM `'.$scheduletable.'` WHERE `accountid` = :accountid AND `invoicedate` > :startdate ORDER BY `invoicedate` ASC';
}
$sth1 = $pdocxn->prepare($sql1);
if($vehicleid > '0')
{
$sth1->bindParam(':vehicleid',$vehicleid);
}
else
{
$sth1->bindParam(':accountid',$accountid);
}
$sth1->bindParam(':startdate',$appointmentdate);
$sth1->execute()or die(print_r($sth1->errorInfo(), true));
	while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$invtax = '0';
	$invsubtotal = '0';
	$invtotal = '0';
	$date = $row1['date'];
	$invoicedate = $row1['invoicedate'];
	$displaydate = date('D - n/d/Y g:i a', strtotime($date));
	$date1 = new DateTime();
	$date2 = new DateTime($date);
	if($date1 > $date2)
	{
		$prevapt1 = "<font color=\"red\">";
		$prevapt2 = "</font>";
	}else {
	$prevapt1 = '';
	$prevapt2 = '';
}
	$taxgroup = $row1['taxgroup'];
	$id = $row1['id'];
	$type = $row1['type'];
	$voiddate = $row1['voiddate'];
	$abvvehicle = $row1['abvvehicle'];
	if ($voiddate['column'] == NULL)
	{
		$voidedbrand ="";
	}
else
	{
		$voidedbrand ="*VOIDED*";
	}
${"ip".$tri} = "<div id=\"$tri\"><div class=\"q1\"><table class=\"righttable\"><tr><td class=\"center\"><form name=\"invoicehistory\" action=\"printcustomerinteraction.php?accountid=".$accountid."\" method=\"post\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$id."\"><input type=\"submit\" class=\"btn-style\" name=\"submit\" value=\"Print\"></form></td><td class=\"center\"><a href=\"schedule.php?r=".$r."&q=1&i=".$invoiceid."&accountid=".$accountid."\" class=\"no-decoration\"><input type=\"button\" class=\"btn-style\" name=\"submit\" value=\"Schedule\"></a></td><td class=\"center\"><a href=\"appointment.php?invoiceid=".$id."\" class=\"no-decoration\"><input type=\"button\" class=\"btn-style\" name=\"submit\" value=\"Edit Appointment\"></td></tr></table></div><div class=\"q3\"><table class=\"righttable\"><tr><th>qty</th><th>comment</th><th>unit cost</th><th>total cost</td></tr>\n";
$sql2 = 'SELECT `qty`,`comment`,`amount`,`totallineamount` FROM `'.$schedulelineitems.'` WHERE `invoiceid` = :transactionid ORDER BY `linenumber` ASC';
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
	$unitcost = $totalamount / $qty;
${"ip".$tri} .= "\n<tr><td class=\"left\">".$qty."</td><td class=\"left\"><b>".$comment."</b></td><td class=\"left\">".$unitcost."</td><td class=\"left\"><b>".$totalamount."</b></td></tr>\n";
$invsubtotal = $invsubtotal + $totalamount;
}

$invtotal = $invsubtotal;
$displayinvtotal = money_format('%(#0.2n',$invtotal);

${"ip".$tri} .= "<tr><td colspan=\"4\"><br /><br /><br /><br /></td></tr></table></div></div>";
	echo "<tr href=\"".$tri."\"><td>".$id."</td><td><b>".$prevapt1.$displaydate." ".$voidedbrand.$prevapt2."</b></td><td></td><td><b>".$abvvehicle."</b></td><td>".$displayinvtotal."</td><td></td></tr>\n";
$tri ++;
}
else {
${"ip".$tri} .= "<tr></tr><tr></tr></table></div></div>";
$tri ++;
}}



echo "<tr><td colspan=\"5\"><br /><br /><br /></td></tr>";
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
<?php
}
else {
	if(isset($_GET['accountid']))
{
	$accountid = $_GET['accountid'];
	$sql2 = "SELECT `firstname`,`lastname` FROM `accounts` WHERE `accountid` = :accountid";
	$sth2 = $pdocxn->prepare($sql2);
	$sth2->bindParam(':accountid',$accountid);
	$sth2->execute();
	while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
	{
	$fname = $row2['firstname'];
	$lname = $row2['lastname'];
	$fullname = $fname." ".$lname;
	}}
if(isset($_GET['vehicleid']))
{
	$vehicleid = $_GET['vehicleid'];
}else{
	$vehicleid = '';
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
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title><?php echo $fullname; ?></title>
<link rel="stylesheet" type="text/css" href="style/globalstyle.css" >
<link rel="stylesheet" type="text/css" href="style/accounthistory.css" >
<script type="text/javascript" src="scripts/globaljs.js"></script>
<script type="text/javascript" src="scripts/jquery-1.9.1.js"></script>
<script type="text/javascript">
function close_window() {
    window.close();
}
</script>
</head>
<body>
<div id="ciheader">
<div class="headerright"><br /><a href="javascript:close_window();" class="no-decoration"><input type="button" class="save" value="<?php echo $fullname; ?>"></a></div>
<div class="headerleft"><br /><a href="javascript:close_window();" class="no-decoration"><input type="button" class="cancel" value="Cancel/Exit"></a></div>
<div class="headercenter"><a href="inventory.php?accountid=<?php echo $accountid; ?>&ci=1" onmouseover="popup('inventory')"><img src="images/icons/tire-white.png" height="40"></a><a href="schedule.php?r=<?php echo $r; ?>&accountid=<?php echo $accountid; ?>" onmouseover="popup('scheduler')"><img src="images/icons/schedule.png" height="40"></a><a href="customerinteraction-invoice.php?accountid=<?php echo $accountid; ?>" onmouseover="popup('transactions')"><img src="images/icons/phone.png" height="40"></a></div></div>
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
//form
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