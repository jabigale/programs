<?php
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Insert Vehicle';
$linkpage = 'insertvehicle.php';
$quicksearch = '0';
$location = '1';
$invoicesubtotal = '0';

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

if($_POST['submit'])
{
	//submit form general
	if(isset($_POST['accountid']))
	{
	$accountid = $_POST['accountid'];
	}
else {
	$accountid = '0';
}
	if($_POST['new']&&$_POST['new'] == '1')
{
	//submit insert new transaction
$type = $_POST['type'];
$sth1 = $pdocxn->prepare('SELECT `id` FROM invoice ORDER BY `id` DESC LIMIT 1');
$sth1->execute();
$row1 = $sth1->fetch(PDO::FETCH_ASSOC);
$lastinvid = $row1['id'];
$invoiceid = $lastinvid + '1';

$sth2 = $pdocxn->prepare('INSERT INTO `vehicles`(`accountid`,`year`,`make`,`model`,`submodel`,`engine`,`drive`,`license`,`state`,`vin`,`unit`,`drivername`) VALUES (:accountid,:year,:make,:model,:submodel,:engine,:drive,:license,:state,:vin,:unit,:drivername)');
$sth2->bindParam(':id',$invoiceid);
$sth2->bindParam(':accountid',$accountid);
$sth2->bindParam(':year',$year);
$sth2->bindParam(':make',$make);
$sth2->bindParam(':model',$model);
$sth2->bindParam(':submodel',$submodel);
$sth2->bindParam(':engine',$engine);
$sth2->bindParam(':drive',$drive);
$sth2->bindParam(':license',$license);
$sth2->bindParam(':state',$state);
$sth2->bindParam(':vin',$vin);
$sth2->bindParam(':unit',$unit);
$sth2->bindParam(':drivername',$drivername);
$sth2->bindParam(':creationdate',$currentdate);
$sth2->execute();
}
if($_POST['newcommentform']&&$_POST['newcommentform'] == '1')
{
	//submit newcomment
	$qty = $_POST['newqty'];
	$amount = $_POST['newprice'];
	$linenumber = $_POST['newlinenumber'];
	$comment = $_POST['newcomment'];
	$fet = $_POST['newfet'];
	
$sth1 = $pdocxn->prepare('INSERT INTO `line_items`(`invoiceid`,`qty`,`amount`,`linenumber`,`comment`,`fet`)VALUES(:invoiceid,:qty,:amount,:linenumber,:comment,:fet)');
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->bindParam(':qty',$qty);
$sth1->bindParam(':amount',$amount);
$sth1->bindParam(':linenumber',$linenumber);
$sth1->bindParam(':comment',$comment);
$sth1->bindParam(':fet',$fet);
$sth1->execute();
}
if($_POST['editsubmit']&&$_POST['editsubmit'] == '1')
{
	//submit edit line item
$lineid = $_POST['lineid'];
$qty = $_POST['qty'];
$amount = $_POST['price'];
$lineid = $_POST['lineid'];
$comment = $_POST['comment'];
$fet = $_POST['fet'];
$sth1 = $pdocxn->prepare('UPDATE `line_items` SET `qty`=:qty,`amount`=:amount,`comment`=:comment WHERE `id` = :lineid');
$sth1->bindParam(':qty',$qty);
$sth1->bindParam(':amount',$amount);
$sth1->bindParam(':lineid',$lineid);
$sth1->bindParam(':comment',$comment);
$sth1->execute();
}
/*
if($_GET['up'])
{
$inv = $_GET['inv'];
$linenum1 = $_GET['linenum'];
$linenum2 = $linenum1 - '1';
$lineid1 = $_GET['lineid1'];
$lineid2 = $_GET['lineid2'];
$sth1 = $pdocxn->prepare('UPDATE `line_items` SET `linenumber`=:linenum2 WHERE `id` = :lineid1');
$sth1->bindParam(':linenum2',$linenum2);
$sth1->bindParam(':lineid1',$lineid1);
$sth1->execute();
$sth2 = $pdocxn->prepare('UPDATE `line_items` SET `linenumber`=:linenum1 WHERE `id` = :lineid2');
$sth2->bindParam(':linenum1',$linenum1);
$sth2->bindParam(':lineid2',$lineid2);
$sth2->execute();
$header = "Location: invoice.php?inv=".$inv;
header($header);
}
*/

$sth3 = $pdocxn->prepare('SELECT * FROM invoice WHERE id = :inv');
$sth3->bindParam(':inv',$invoiceid);
$sth3->execute();
$row3 = $sth3->fetch(PDO::FETCH_ASSOC);
$accountid = $row3['accountid'];
$type = $row3['type'];
$userid = $row3['userid'];
$vehicleid = $row3['vehicleid'];
$mileagein = $row3['mileagein'];
$mileageout = $row3['mileageout'];
$location = $row3['location'];
if($accountid > '1')
{
$sth4 = $pdocxn->prepare('SELECT * FROM accounts WHERE acctid = :acct');
$sth4->bindParam(':acct',$accountid);
$sth4->execute();
while($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
{
$fname = $row4['firstname'];
$lname = $row4['lastname'];
$address = $row4['address'];
$address2 = $row4['address2'];
$city = $row4['city'];
$state = $row4['state'];
$zip = $row4['zip'];
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
if($vehicleid > '1')
{
$sql5 = 'SELECT * FROM `vehicles` WHERE `accountid` = :acctid';
$sth5 = $pdocxn->prepare($sql5);
$sth5->bindParam(':acctid',$accountid);
$sth5->execute();
if ($sth5->rowCount() > 0)
{
while($row5 = $sth5->fetch(PDO::FETCH_ASSOC))
	{
	$vehicleid = $row5['id'];
	$year = $row5['year'];
	$model = $row5['model'];
	$make = $row5['make'];
	$vin = $row5['vin'];
${"ip".$tri} = "<div id=\"$tri\"><div class=\"q1\"><table class=\"righttable\"><tr><td colspan=\"2\" class=\"center\"><a href=\"accounthistory.php?acctid=".$acctid."\">Account History</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"editaccounts.php?acctid=".$acctid."\">Edit Info</a></td></tr><tr><td colspan=\"2\" class=\"center\">$fullname</td></tr><tr><td colspan=\"2\" class=\"left\">$address&nbsp;$city,&nbsp;$state&nbsp;$zip</td></tr><tr><td colspan=\"2\">$address2</td></tr>".$dphone1."".$dphone2."".$dphone3."".$dphone4."".$dfax."<tr></table></div><div class=\"q3\"><table class=\"righttable\"><tr><th>Vehicle</th><th>License</th><th>VIN</th></tr><tr><td class=\"left\">$year&nbsp;&nbsp;$make&nbsp;&nbsp;$model</td><td></td><td>$vin</td></tr></table></div></div>";
//${"ip".$tri} = "<div id=\"$tri\"><div class=\"q1\"><table class=\"righttable\"><tr><td colspan=\"2\" class=\"center\">Info&nbsp;&nbsp;&nbsp;<a href=\"vehicles.php?acctid=".$acctid."\">Vehicles</a>&nbsp;&nbsp;&nbsp;<a href=\"editaccounts.php?acctid=".$acctid."\">Edit Info</a></td></tr><tr><td colspan=\"2\" class=\"center\">$fullname</td></tr><tr><td colspan=\"2\" class=\"left\">$address&nbsp;$city,&nbsp;$state&nbsp;$zip</td></tr><tr><td colspan=\"2\">$address2</td></tr>".$dphone1."".$dphone2."".$dphone3."".$dphone4."".$dfax."<tr><td>e-mail:</td><td>$email</td></tr><tr><td>Cedit Limit:</td><td>".$creditlimit."</td></tr><tr><td>Tax ID: ".$taxid."</td><td>Tax Class: ".$dtaxclass."</td></tr><tr><td>Price Class: ".$dpriceclass."</td><td>Require PO: ".$requirepo."</td></tr><tr><td colspan=\"2\">Account Note: ".$comment."</td></tr><tr><td>Last visited:</td><td>".$dlastactivedate."</td></tr></table></div></div>";
$tri ++;
}}
}
}
else {
${"ip".$tri} = "<div id=\"$tri\"><div class=\"q1\"><table class=\"righttable\"><tr><td colspan=\"2\" class=\"center\"><a href=\"accounthistory.php?acctid=".$acctid."\">Account History</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"editaccounts.php?acctid=".$acctid."\">Edit Info</a></td></tr><tr><td colspan=\"2\" class=\"center\">$fullname</td></tr><tr><td colspan=\"2\" class=\"left\">$address&nbsp;$city,&nbsp;$state&nbsp;$zip</td></tr><tr><td colspan=\"2\">$address2</td></tr>".$dphone1."".$dphone2."".$dphone3."".$dphone4."".$dfax."<tr></table></div><div class=\"q3\"><table class=\"righttable\"><tr><th>Vehicle</th><th>License</th><th>VIN</th></tr><tr><td class=\"left\">$year&nbsp;&nbsp;$make&nbsp;&nbsp;$model</td><td></td><td>$vin</td></tr></table></div></div>";
}
$sql6 = 'SELECT `name` FROM `invoice_type` WHERE `id` = :type';
$sth6 = $pdocxn->prepare($sql6);
$sth6->bindParam(':type',$type);
$sth6->execute();
while($row6 = $sth6->fetch(PDO::FETCH_ASSOC))
{
	$typename = $row6['name'];
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" >
<meta http-equiv="cache-control" content="no-cache, must-revalidate, post-check=0, pre-check=0" />
  <meta http-equiv="cache-control" content="max-age=0" />
  <meta http-equiv="expires" content="0" />
  <meta http-equiv="expires" content="Tue, 01 Jan 2018 1:00:00 GMT" />
  <meta http-equiv="pragma" content="no-cache" />
<link rel="stylesheet" type="text/css" href="style/globalstyle.css" >
<link rel="stylesheet" type="text/css" href="style/invoicestyle.css" >
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
</select></div><input type="hidden" name="form" value="1"></td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td><?php echo $typename; ?> <b>#<?php echo $invoiceid; ?></b></td></tr></table></form></div>
        <div id="content">
        	<div id="left"><form name="invoice" action="invoice.php" method="post">
<table id="highlightTable" class="blueTable">
<thead>
<tr>
<th>Qty</th>
<th>Description</th>
<th>FET</th>
<th>Unit Price</th>
<th>Ext Price</th>
</tr>
</thead>
<tbody>
	<?php
$sbi = '1';
$tri = '1';
$sth4 = $pdocxn->prepare('SELECT * FROM line_items WHERE invoiceid = :inv ORDER BY linenumber ASC');
$sth4->bindParam(':inv',$invoiceid);
$sth4->execute();
	while($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
{
$lineid = $row4['id'];
$invqty = $row4['qty'];
$invamount = $row4['amount'];
$invpartid = $row4['partid'];
$invpackageid = $row4['packageid'];
$invserviceid = $row4['serviceid'];
$invcomment = $row4['comment'];
$fet = $row4['fet'];
$extprice = $invamount * $invqty;
setlocale(LC_MONETARY,"en_US");
$dextprice = money_format('%(#0.2n',$extprice);
$linenumber = $row4['linenumber'];
$invoicesubtotal = $invoicesubtotal+$extprice;
if($linenumber == '1')
{
	$displayup = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
}
else {
	$displayup = "<a href=\"invoice.php?inv=2&linenum=$linenumber&up=1&lineid1=$lineid&lineid2=$prevlineid\"><img src=\"images/icons/up.png\" width=\"20\"></a>";
}
if($linenumber == '1')
{
	$displaydown = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
}
else {
	$displaydown = "<a href=\"invoice.php?inv=2&linenum=$linenumber&up=1&lineid1=$lineid&lineid2=$prevlineid\"><img src=\"images/icons/down.png\" width=\"20\"></a>";
}
$prevlineid = $row4['id'];
	echo "\n<tr href=\"$tri\"><td>$invqty</td><td>$invcomment</td><td>$fet</td><td>$invamount</td><td>$dextprice</td></tr>";
${"ip".$tri} = "\n<div id=\"$tri\"><div class=\"q1\"><form name=\"updatecomment\" method=\"post\" action=\"invoice.php\"><table class=\"righttable\"><tr><td colspan=\"2\" class=\"left\"><textarea class=\"wideinput\" name=\"comment\">".$invcomment."</textarea></td></tr><tr><td class=\"left\">Unit Price: <input type=\"text\" class=\"narrowinput\" name=\"price\" value=\"$invamount\" ></td><td class=\"left\">QTY:<input type=\"number\" class=\"narrowinput\" name=\"qty\" value=\"".$invqty."\" step=\"1.00\" ></td></tr><tr><td colspan=\"2\" class=\"center\">FET: <input type=\"text\" class=\"narrowinput\" name=\"newfet\" value=\"$fet\" ></td></tr><tr><td class=\"center\" colspan=\"2\"><input type=\"hidden\" name=\"editsubmit\" value=\"1\"><input type=\"hidden\" name=\"inv\" value=\"".$inv."\"><input type=\"hidden\" name=\"lineid\" value=\"".$lineid."\">$displayup<input type=\"image\" src=\"images/buttons/checkmark.png\" width=\"25\" name=\"submit\"> <img src=\"images/buttons/delete.png\" width=\"25\"></td></tr></table></form></div><div class=\"q3\"><img src=\"images/buttons/checkmark.png\" width=\"25\"><img src=\"images/buttons/delete.png\" width=\"25\"></div></div>\n";
$tri ++;
	}
if($linenumber < '1')
{
	$newlinenumber = '1';
}
else
{
	$newlinenumber = $linenumber + 1;
}

echo "<tr href=\"add\"><td></td><td><b>Add Item </b><img src=\"images/buttons/add-black.png\" width=\"25\"> </td><td></td><td></td><td></td></tr>";
$taxtotal = $invoicesubtotal*$tax;
$invoicetotal = $invoicesubtotal*$salestax;
$dtaxtotal = money_format('%(#0.2n',$taxtotal);
$dsubtotal = money_format('%(#0.2n',$invoicesubtotal);
$dinvoicetotal = money_format('%(#0.2n',$invoicetotal);
echo "<tr href=\"total\"><td colspan=\"4\">Subtotal:</td><td>".$dsubtotal."</td></tr>";
echo "<tr href=\"total\"><td colspan=\"4\">Sales Tax:</td><td>".$dtaxtotal."</td></tr>";
echo "<tr href=\"total\"><td colspan=\"4\"><b>Total:</b></td><td><b>".$dinvoicetotal."</b></td></tr>";
?></tbody></table></form></div>
<div class="right">
<?php
while ($tri > 0) {
        echo ${"ip".$tri};
		$tri --;
}
echo "\n<div id=\"add\"><div class=\"q1\"><form name=\"addcomment\" method=\"post\" action=\"invoice.php\"><table class=\"righttable\"><tr><td colspan=\"2\" class=\"left\"><textarea class=\"wideinput\" name=\"newcomment\" placeholder=\"Add Comment\"></textarea></td></tr><tr><td class=\"left\">Unit Price: <input type=\"text\" class=\"narrowinput\" name=\"newprice\" placeholder=\"0.00\" autocomplete=\"off\"></td><td class=\"left\">QTY:<input type=\"number\" class=\"narrowinput\" name=\"newqty\" value=\"1\" step=\"1.00\" ></td></tr><tr><td colspan=\"2\" class=\"center\">FET: <input type=\"text\" class=\"narrowinput\" name=\"newfet\" placeholder=\"0.00\" autocomplete=\"off\"></td></tr><tr><td class=\"center\" colspan=\"2\"><input type=\"hidden\" name=\"newlinenumber\" value=\"$newlinenumber\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"newcommentform\" value=\"1\"><input type=\"hidden\" name=\"submit\" value=\"1\"><input type=\"image\" src=\"images/buttons/checkmark.png\" alt=\"addcomment\" name=\"submit\" width=\"25\"></td></tr></table></form></div><div class=\"q3\">Add Tires&nbsp;&nbsp;&nbsp;&nbsp;Add Service/Package</div></div>";
echo "\n<div id=\"total\"><div class=\"q1\"><table class=\"righttable\"><tr><td colspan=\"2\" class=\"left\"><tr href=\"total\"><td colspan=\"4\">Subtotal:</td><td>0.00</td></tr><tr href=\"total\"><td colspan=\"4\">Sales Tax:</td><td>0.00</td></tr><tr href=\"total\"><td colspan=\"4\"><b>Total:</b></td><td><b>0.00</b></td></tr></table></div>";

?>
</div></div>
</div>
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
<meta http-equiv="cache-control" content="no-cache, must-revalidate, post-check=0, pre-check=0" />
  <meta http-equiv="cache-control" content="max-age=0" />
  <meta http-equiv="expires" content="0" />
  <meta http-equiv="expires" content="Tue, 01 Jan 2018 1:00:00 GMT" />
  <meta http-equiv="pragma" content="no-cache" />
<link rel="stylesheet" type="text/css" href="style/globalstyle.css" >
<link rel="stylesheet" type="text/css" href="style/invoicestyle.css" >
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
$(document).ready(function() {
    loadPopupManual();
});

function loadPopupManual() {
    $('#load-div').fadeIn("slow"); 
    $.get('scripts/selecteduser.php', function(data) {
        $('#load-div').html(data);
    });
}
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
        <div id="content">
        	<div id="left">
<table>
	<tr><td><form name="quote" action="invoice.php" method="post"><input type="hidden" name="new" value="1"><input type="hidden" name="submit" value="1"><input type="hidden" name="type" value="2"><input type="image" src="images/icons/clipboard.png" alt="Create Quote" name="submit"></form></td><td><form name="quote" action="invoice.php" method="post"><input type="hidden" name="new" value="1"><input type="hidden" name="submit" value="1"><input type="hidden" name="type" value="5"><input type="image" src="images/icons/clipboard.png" alt="Create Invoice" name="submit"></form></td></tr>
	<tr><td>Create Quote</td><td>Create Invoice</td></tr>
	<tr><td colspan="2"><form name="search" action="invoice.php" method="post"><input type="hidden" name="submit" value="1"><input type="text" name="invoiceid" placeholder="Enter Transaction ID" autocomplete="off"> <input type="image" src="images/icons/clipboard.png" alt="Create Quote" name="submit"></form></td></tr>
</table></div></div>
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