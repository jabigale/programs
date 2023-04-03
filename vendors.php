<?php
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Vendors';
$linkpage = 'vendors.php';

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
$changecustomer = $_POST['changecustomer'];
}
if(isset($_POST['search']))
{
	//Zero Values Prior
	$lastname='0';
	$phone = '0';
	$account = '0';
	$lastname = $_POST['lastname'];
	$phone = $_POST['phone'];
	$account = $_POST['acctnumber'];
	$searchnumber=ereg_replace('[^0-9]', '', $phone);
	$accounttype = '2';
//fkmfkm
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
<script type="text/javascript" src="http://whatcomputertobuy.com/js/script.js"></script>
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
if($invoice == '0')
{
if($currentlocationid == '1')
{
echo "<div id=\"header\">".$headernavigation."</div>";
}
else{
echo "<div id=\"header2\">".$headernavigation."</div>";
}}else{
	echo "<div id=\"ciheader\"><br /><br /><b>Please Select Vendor or Vehicle Below</b></div>";
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

        	<div id="left"><form name="inventory" action="inventory.php" method="post">
<table id="highlightTable" class="blueTable">
<thead>
<tr>
<th>Act ID</th>
<th>Vendor</th>
<th>Phone</th>
</tr>
</thead>
<tbody>
<?php
$tri = '1';
if($accounttype = '1')
{
	$acctsql = " AND `retail` = '1'";
}
if($accounttype = '2')
{
	$acctsql = " AND `vendor` = '1'";
}
if($accounttype = '3')
{
	$acctsql = " AND `distributor` = '1'";
}
if($accounttype = '4')
{
	$acctsql = " AND `nationalaccount` = '1'";
}
if($accounttype = '5')
{
	$acctsql = " AND `affiliate` = '1'";
}
if($accounttype = '6')
{
	$acctsql = "";
}
if($lastname > '0')
	{
$sql1 = 'SELECT * FROM `accounts` WHERE `lastname` LIKE :lastname';
	}
else
{
	$sql1 = "SELECT * FROM `accounts` WHERE ";
}
if($searchnumber > '0' && $lastname < '1')
	{
$sql1 = 'SELECT * FROM `accounts` WHERE `sphone1` LIKE :phone OR `sphone2` LIKE :phone OR `sphone3` LIKE :phone OR `sphone4` LIKE :phone';
	}
if($searchnumber > '0' && $lastname > '0')
	{
	$sql1 .= " AND `sphone1` LIKE :phone OR `lastname` LIKE :lastname AND `sphone2` LIKE :phone OR `lastname` LIKE :lastname AND `sphone3` LIKE :phone OR `lastname` LIKE :lastname AND `sphone4` LIKE :phone";
}
if($searchnumber > '0' && $lastname < '1')
	{
	$sql1 .= " AND `sphone1` LIKE :phone AND `sphone2` LIKE :phone AND `sphone3` LIKE :phone AND `sphone4` LIKE :phone";
}
if($searchnumber > '0' && $lastname > '0')
	{
	$sql1 .= " AND `sphone1` LIKE :phone OR `lastname` LIKE :lastname AND `sphone2` LIKE :phone OR `lastname` LIKE :lastname AND `sphone3` LIKE :phone OR `lastname` LIKE :lastname AND `sphone4` LIKE :phone";
}
$sql1 .= " AND `oldtype` = '2' ORDER BY `lastname` ASC";

$sth1 = $pdocxn->prepare($sql1);
if($lastname > '0')
{
$searchlastname = $lastname."%";
$sth1->bindParam(':lastname',$searchlastname);
}
if($searchnumber > '0')
{
$searchphone = "%".$searchnumber."%";
$sth1->bindParam(':phone',$searchphone);
}
$sth1->execute();
if($sth1->rowCount() > 0) {
	while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$accountid = $row1['accountid'];
	$databaselname = $row1['lastname'];
	$lname = stripslashes($databaselname);
	$address = $row1['address'];
	$address2 = $row1['address2'];
	$city = $row1['city'];
	$state = $row1['state'];
	$zip = $row1['zip'];
	$phone1 = $row1['phone1'];
	$phone2 = $row1['phone2'];
	$phone3 = $row1['phone3'];
	$phone4 = $row1['phone4'];
	$contact1 = $row1['contact1'];
	$contact2 = $row1['contact2'];
	$contact3 = $row1['contact3'];
	$contact4 = $row1['contact4'];
	$fax = $row1['fax'];
	$email = $row1['email'];
	$creditlimit = $row1['creditlimit'];
	$taxid = $row1['taxid'];
	$priceclass = $row1['priceclass'];
	$taxclass = $row1['taxclass'];
	$requirepo = $row1['requirepo'];
	$accounttype = $row1['accounttype'];
	$flag = $row1['flag'];
	$comment = $row1['comment'];
	$insertdate = $row1['insertdate'];
	$lastactivedate = $row1['lastactivedate'];
if($phone1 > '0')
	{
		$dphone1 = "<tr><td class=\"left\" colspan=\"2\">Phone 1: <b>".$phone1."</b></td><td>Contact: <b>".$contact1."</b></td></tr>";
	}
	else
		{
			$dphone1 = "";
		}
if($phone2 > '0')
	{
		$dphone2 = "<tr><td class=\"left\" colspan=\"2\">Phone 2: <b>".$phone2."</b></td><td>Contact: <b>".$contact2."</b></td></tr>";
	}
	else
		{
			$dphone2 = "";
		}
if($phone3 > '0')
	{
		$dphone3 = "<tr><td class=\"left\" colspan=\"2\">Phone 3: <b>".$phone3."</b></td><td>Contact: <b>".$contact3."</b></td></tr>";
	}
	else
		{
			$dphone3 = "";
		}
if($phone4 > '0')
	{
		$dphone4 = "<tr><td class=\"left\" colspan=\"2\">Phone 4: <b>".$phone4."</b></td><td>Contact: <b>".$contact4."</b></td></tr>";
	}
	else
		{
			$dphone4 = "";
		}
if($fax > '0')
	{
		$dfax = "<tr><td colspan=\"2\" class=\"left\">Fax: <b>".$fax."</b></td></tr>";
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
		//n2update
		$dtaxclass = "Consumer";
		$dlastactivedate = "12/20/2017";
		if($address2 > '1')
		{
		$daddress2 = "<tr><td colspan=\"3\">$address2</td></tr>";
		}
		else {
			$daddress2 = "";
		}
	echo "<tr href=\"$tri\"><td>$accountid</td><td><b>$lname</b></td><td>$phone1</td>";
	if($invoice == '0')
	{
		echo "<td><a href=\"#\" onClick=\"window.open('http://auto-shop-software.com/fkm/vendorinvoice.php?accountid=".$accountid."&type=2', '_blank')\" class=\"no-decoration\"><input type=\"button\" class=\"smallbutton\" value=\"Receive Inventory\"></a></td>";
		}
		echo "</tr>\n";

${"ip".$tri} = "<div id=\"$tri\"><div class=\"q1\"><table class=\"righttable\"><tr><td class=\"center\">";
if($invoice > '0')
{
${"ip".$tri} .= "<form name=\"newinvoice\" action=\"vendorinvoice.php\" method=\"post\"><input type=\"hidden\" name=\"lname\" value=\"".$lname."\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"hidden\" name=\"new\" value=\"1\"><input type=\"hidden\" name=\"type\" value=\"".$invoice."\"><input type=\"submit\" class=\"smallbutton\" name=\"submit\" value=\"Select Vendor\"></form>";
}else{
if($invoiceid > '0')
{
if($changecustomer == '2')
{
${"ip".$tri} .= "<form name=\"updatecustomer\" action=\"vendorinvoice.php\" method=\"post\"><input type=\"hidden\" name=\"lname\" value=\"$lname\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"hidden\" name=\"changecustomer\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"submit\" class=\"smallbutton\" name=\"submit\" value=\"Select Vendor\"></form>";
}else{
${"ip".$tri} .= "<form name=\"updatecustomer\" action=\"vendorinvoice.php\" method=\"post\"><input type=\"hidden\" name=\"lname\" value=\"$lname\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"hidden\" name=\"changecustomer\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"submit\" class=\"smallbutton\" name=\"submit\" value=\"Select Vendor\"></form>";
}}
else
{
${"ip".$tri} .= "<form name=\"accounthistory\" action=\"accounthistory.php\" method=\"post\"><input type=\"hidden\" name=\"lname\" value=\"$lname\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"submit\" class=\"smallbutton\" name=\"submit\" value=\"Vendor History\"></form>";
}}
${"ip".$tri} .= "</td><td calss=\"center\"><a href=\"#\" onClick=\"window.open('http://auto-shop-software.com/fkm/customerinteraction.php?accountid=".$accountid."', '_blank')\" class=\"no-decoration\"><input type=\"button\" class=\"smallbutton\" value=\"".$lname."\"></a></td><td class=\"center\"><form name=\"editaccount\" action=\"editaccount.php\" method=\"post\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"submit\" class=\"smallbutton\" name=\"submit\" value=\"Edit Info\"></form></td></tr><tr><td colspan=\"3\" class=\"left\">$address&nbsp;$city,&nbsp;$state&nbsp;$zip</td></tr>".$daddress2."".$dphone1."".$dphone2."".$dphone3."".$dphone4."".$dfax."<tr></table></div><div class=\"q3\"><table class=\"righttable\"><tr><td class=\"center\">";
if($changecustomer == '2')
{
${"ip".$tri} .= "</table></div></div>";
$tri ++;
}else{

${"ip".$tri} .= "</table></div></div>";
$tri ++;
}}}else{
	echo "<tr href=\"vendors.php\"><td colspan=\"6\"><a href=\"vendors.php\">No Results, try again</a></td></tr>\n";

}

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
<script>
function highlight(tableIndex) {
    // Just a simple check. If .highlight has reached the last, start again
    if( (tableIndex+1) > $('#highlightTable tbody tr').length )
        tableIndex = 0;
    // Element exists?
    if($('#highlightTable tbody tr:eq('+tableIndex+')').length > 0)
    {
        // Remove other highlights
        $('#highlightTable tbody tr').removeClass('highlight');
        // Highlight your target
        $('#highlightTable tbody tr:eq('+tableIndex+')').addClass('highlight');
    }
}
$('#goto_first').click(function() {
    highlight(0);
});
$('#goto_prev').click(function() {
    highlight($('#highlightTable tbody tr.highlight').index() - 1);
});
$('#goto_next').click(function() {
    highlight($('#highlightTable tbody tr.highlight').index() + 1);
});
$('#goto_last').click(function() {
    highlight($('#highlightTable tbody tr:last').index());
});
$(document).keydown(function (e) {
    switch(e.which) 
    {
        case 38:
            $('#goto_prev').trigger('click');
            break;
        case 40:
            $('#goto_next').trigger('click');
            break;
    }
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
<link rel="stylesheet" type="text/css" href="style/accountstyle.css" >
<script type="text/javascript" src="scripts/globaljs.js"></script>
<script type="text/javascript" src="scripts/jquery-1.9.1.js"></script>
<script type="text/javascript" src="http://whatcomputertobuy.com/js/script.js"></script>
</head>
<body>
<?php
if($invoice == '0')
{
if($currentlocationid == '1')
{
echo "<div id=\"header\">".$headernavigation."</div>";
}
else{
echo "<div id=\"header2\">".$headernavigation."</div>";
}}else{
	echo "<div id=\"ciheader\"><br /><br /><b>Search for a Vendor Below</b></div>";
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
        	<table><tr><td colspan="4"><a href="newaccount.php" class="no-decoration"><input type="button" name="submit" class="smallquotebutton" value="New Vendor"></a><br /></td></tr><form name="account" action="vendors.php" method="post"><tr><th>Vendor:</th><td><input type="text" name="lastname" autocomplete="off" autofocus></td></tr>
<tr><th>Phone:</th><td><input type="text" name="phone" autocomplete="off"></td><th>Account Number:</th><td><input type="text" name="acctnumber" autocomplete="off"></td></tr>
<?php
if($invoice == '0')
{
?>
<tr><td colspan="4"><input type="hidden" name="search" value="1"><input type="hidden" name="invoice" value="<?php echo $invoice; ?>"><?php if($invoiceid > '0'){?><input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>"><input type="hidden" name="changecustomer" value="<?php echo $changecustomer; ?>"><?php } ?><input type="submit" name="submit" class="quotebutton" value="Search"></td></tr></form>
<?php
			} else{
				?>
<tr><td colspan="2"><input type="hidden" name="search" value="1"><input type="hidden" name="invoice" value="<?php echo $invoice; ?>"><?php if($invoiceid > '0'){?><input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>"><input type="hidden" name="changecustomer" value="<?php echo $changecustomer; ?>"><?php } ?><input type="submit" name="submit" class="quotebutton" value="Search"></td>
<td colspan="2"><a href="vendors.php"><input type="button" name="Cancel" class="quotebutton" value="Cancel"></a></td>
</tr></form>
<?php	
			}
			?>     	</table>
        </div>
</body>
</html>
<?php
}
?>