<?php
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Search Invoice';
$linkpage = 'serachinvoice.php';
$currentday = date('Y-n-j');
$vehicleid = '0';


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
if(isset($_POST['submit']))
{
	$invoiceid = $_POST['invoicenumber'];
	$keyword = $_POST['keyword'];
	$invoicetype = $_POST['selectedtype'];
	$voidselect = '0';
	if(isset($_POST['startdate']))
	{
		$startdate = $_POST['startdate'];
		$enddate = $_POST['enddate'];
		$enddate = date('Y-m-d', strtotime('+1 day', strtotime($enddate)));
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
<?php
if($invoiceid > '0')
{
}else{
echo  "Invoices with Keyword \"".$keyword."\"";
}
?>

<table id="highlightTable" class="blueTable">
<thead>
<tr>
<th>ID</th>
<th>Date</th>
<th>Customer</th>
<th>Amount</th>
<th>Vehicle</th>
<th>Location</th>
</tr>
</thead>
<tbody>

<?php
if($invoiceid > '0')
{
$tri = '1';
if($invoicetype > '0')
{
$sqltype = " AND `invoicetype` = '".$invoicetype."' ";
}else{
$sqltype = "";
}
if($voidselect == '1')
{
$void = '';
}else{
$void = ' AND `voiddate` IS NULL ';
}



$sql4 = "SELECT `id`,`invoiceid`,`type`,`accountid`,`total`,`invoicedate`,`vehicleid`,`location` FROM `invoice` WHERE `invoiceid` = :invoiceid".$void.$sqltype."";
$sth4 = $pdocxn->prepare($sql4);
$sth4->bindParam(':invoiceid',$invoiceid);
$sth4->execute();
while($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
{
$invoiceid1 = $row4['id'];
$invoicenumber = $row4['invoiceid'];
$total = $row4['total'];
$customerid = $row4['accountid'];
$vehicleid = $row4['vehicleid'];
$vehicle = $row4['abvvehicle'];
$abvname = $row4['abvname'];
$invoicedate = $row4['invoicedate'];
$dinvoicedate = date('m/d/Y',strtotime($invoicedate));
$typeid = $row4['type'];
$invoicelocationid = $row4['location'];
$displaytotal = '$'.$total;
if($customerid < '1')
{
$fullname = "No Customer Selected";
}
else{
$sth5 = $pdocxn->prepare('SELECT `firstname`,`lastname` FROM `accounts` WHERE `accountid` = \''.$customerid.'\'');
$sth5->execute();
while($row5 = $sth5->fetch(PDO::FETCH_ASSOC))
{
$firstname = $row5['firstname'];
$lastname = $row5['lastname'];
$fullname = $firstname." ".$lastname;
}}
if($vehicleid < '1')
{
	$displayvehicle = "No Vehicle Selected";
}
else
{
$sql2 = 'SELECT `year`,`make`,`model`,`description` FROM `vehicles` WHERE `id` = :vehicleid';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':vehicleid',$vehicleid);
$sth2->execute();
while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
	{
$year = "";
$make = "";
$model = "";
$year = $row2['year'];
$model1 = $row2['model'];
$model = str_replace("?","",$model1);
$make1 = $row2['make'];
$make = str_replace("?","",$make1);
if($year < '1')
{
$displayvehicle = $row2['description'];
//$displayvehicle1 = str_replace("?","",$$displayvehicle1a);
}else{
$displayvehicle = $year."&nbsp;&nbsp;".$make."&nbsp;&nbsp;".$model;
}
}
}
$sql2 = 'SELECT * FROM `invoice_type` WHERE `id` = :typeid';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':typeid',$typeid);
$sth2->execute();
while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
	{
$typename = $row2['name'];
}
$sql2 = 'SELECT `storename` FROM `locations` WHERE `id` = :invoicelocationid';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':invoicelocationid',$invoicelocationid);
$sth2->execute();
while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
	{
$invoicestorename = $row2['storename'];
}

echo "\n<tr id=\"highlight\"><td><a href=\"invoice.php?invoiceid=".$invoiceid1."\" target=\"_BLANK\" class=\"no-decoration\"><input type=\"button\" name=\"submit\" class=\"btn-style\" id=\"".$invoicenumber."\" value=\"".$typename." #";
if($typeid == '1')
{
	echo $invoicenumber;
	}
else
{
	echo $invoiceid1;
}
	echo "\" /></a></td><td><a href=\"invoice.php?invoiceid=".$invoiceid1."\" target=\"_BLANK\" class=\"no-decoration\"><input type=\"button\" name=\"submit\" class=\"btn-style\" id=\"".$invoicenumber."\" value=\"".$dinvoicedate."\" /></a></td><td><a href=\"invoice.php?invoiceid=".$invoiceid."\" target=\"_BLANK\" class=\"no-decoration\"><input type=\"button\" name=\"submit\" class=\"btn-style\" id=\"".$invoicenumber."\" value=\"".$fullname."\" /></a></td><td><a href=\"invoice.php?invoiceid=".$invoiceid."\" target=\"_BLANK\" class=\"no-decoration\"><input type=\"button\" name=\"submit\" class=\"btn-style\" id=\"".$invoicenumber."\" value=\"".$displaytotal."\" /></a></td><td><a href=\"invoice.php?invoiceid=".$invoiceid."\" target=\"_BLANK\" class=\"no-decoration\"><input type=\"button\" name=\"submit\" class=\"btn-style\" id=\"".$invoicenumber."\" value=\"".$displayvehicle."\" /></a></td><td><a href=\"invoice.php?invoiceid=".$invoiceid."\" target=\"_BLANK\" class=\"no-decoration\"><input type=\"button\" name=\"submit\" class=\"btn-style\" id=\"".$invoicenumber."\" value=\"".$invoicestorename."\" /></a></td></tr>";
}







$sql4 = "SELECT `id`,`invoiceid`,`type`,`accountid`,`total`,`invoicedate`,`vehicleid`,`location` FROM `invoice` WHERE `id` = :invoiceid".$void.$sqltype."";
$sth4 = $pdocxn->prepare($sql4);
$sth4->bindParam(':invoiceid',$invoiceid);
$sth4->execute();
while($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
{
$invoiceid = $row4['id'];
$invoicenumber = $row4['invoiceid'];
$total = $row4['total'];
$customerid = $row4['accountid'];
$vehicleid = $row4['vehicleid'];
$vehicle = $row4['abvvehicle'];
$abvname = $row4['abvname'];
$invoicedate = $row4['invoicedate'];
$dinvoicedate = date('m/d/Y',strtotime($invoicedate));
$typeid = $row4['type'];
$invoicelocationid = $row4['location'];
$displaytotal = '$'.$total;
if($customerid < '1')
{
$fullname = "No Customer Selected";
}
else{
$sth5 = $pdocxn->prepare('SELECT `firstname`,`lastname` FROM `accounts` WHERE `accountid` = \''.$customerid.'\'');
$sth5->execute();
while($row5 = $sth5->fetch(PDO::FETCH_ASSOC))
{
$firstname = $row5['firstname'];
$lastname = $row5['lastname'];
$fullname = $firstname." ".$lastname;
}}
if($vehicleid < '1')
{
	$displayvehicle = "No Vehicle Selected";
}
else
{
$sql2 = 'SELECT `year`,`make`,`model`,`description` FROM `vehicles` WHERE `id` = :vehicleid';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':vehicleid',$vehicleid);
$sth2->execute();
while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
	{
$year = "";
$make = "";
$model = "";
$year = $row2['year'];
$model1 = $row2['model'];
$model = str_replace("?","",$model1);
$make1 = $row2['make'];
$make = str_replace("?","",$make1);
if($year < '1')
{
$displayvehicle = $row2['description'];
//$displayvehicle1 = str_replace("?","",$$displayvehicle1a);
}else{
$displayvehicle = $year."&nbsp;&nbsp;".$make."&nbsp;&nbsp;".$model;
}
}
}
$sql2 = 'SELECT * FROM `invoice_type` WHERE `id` = :typeid';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':typeid',$typeid);
$sth2->execute();
while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
	{
$typename = $row2['name'];
}
$sql2 = 'SELECT `storename` FROM `locations` WHERE `id` = :invoicelocationid';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':invoicelocationid',$invoicelocationid);
$sth2->execute();
while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
	{
$invoicestorename = $row2['storename'];
}

echo "\n<tr id=\"highlight\"><td><a href=\"invoice.php?invoiceid=".$invoiceid."\" target=\"_BLANK\" class=\"no-decoration\"><input type=\"button\" name=\"submit\" class=\"btn-style\" id=\"".$invoicenumber."\" value=\"".$typename." #";
if($typeid == '1')
{
	echo $invoicenumber;
	}
else
{
	echo $invoiceid;
}
	echo "\" /></a></td><td><a href=\"invoice.php?invoiceid=".$invoiceid."\" target=\"_BLANK\" class=\"no-decoration\"><input type=\"button\" name=\"submit\" class=\"btn-style\" id=\"".$invoicenumber."\" value=\"".$dinvoicedate."\" /></a></td><td><a href=\"invoice.php?invoiceid=".$invoiceid."\" target=\"_BLANK\" class=\"no-decoration\"><input type=\"button\" name=\"submit\" class=\"btn-style\" id=\"".$invoicenumber."\" value=\"".$fullname."\" /></a></td><td><a href=\"invoice.php?invoiceid=".$invoiceid."\" target=\"_BLANK\" class=\"no-decoration\"><input type=\"button\" name=\"submit\" class=\"btn-style\" id=\"".$invoicenumber."\" value=\"".$displaytotal."\" /></a></td><td><a href=\"invoice.php?invoiceid=".$invoiceid."\" target=\"_BLANK\" class=\"no-decoration\"><input type=\"button\" name=\"submit\" class=\"btn-style\" id=\"".$invoicenumber."\" value=\"".$displayvehicle."\" /></a></td><td><a href=\"invoice.php?invoiceid=".$invoiceid."\" target=\"_BLANK\" class=\"no-decoration\"><input type=\"button\" name=\"submit\" class=\"btn-style\" id=\"".$invoicenumber."\" value=\"".$invoicestorename."\" /></a></td></tr>";
}

}

















else{

$tri = '1';
if($invoicetype > '0')
{
$type = " AND `invoicetype` = '".$invoicetype."' ";
}else{
$type = "";
}
if($voidselect == '1')
{
$void = '';
}else{
$void = ' AND `voiddate` IS NULL ';
}
if($startdate == '0')
{
$sql1 = 'SELECT `id`,`invoiceid` FROM `line_items` WHERE `comment` LIKE :keyword ORDER BY `invoiceid` DESC';
}else{
$sql1 = 'SELECT `id`,`invoiceid` FROM `line_items` WHERE `comment` LIKE :keyword ORDER BY `invoiceid` DESC';
}
$searchkeyword = "%".$keyword."%";
$sth1=$pdocxn->prepare($sql1);
$sth1->bindParam(':keyword',$searchkeyword);
if($searchdate == '1')
{
$sth1->bindParam(':startdate',$startdate);
$sth1->bindParam(':enddate',$enddate);
}
$sth1->execute();
	while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$invoiceid = $row1['invoiceid'];
	if($oldid != $invoiceid)
{

$sql4 = "SELECT `id`,`invoiceid`,`type`,`accountid`,`total`,`invoicedate`,`vehicleid`,`location` FROM `invoice` WHERE `id` = :invoiceid".$void.$sqltype."";
$sth4 = $pdocxn->prepare($sql4);
$sth4->bindParam(':invoiceid',$invoiceid);
$sth4->execute();
while($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
{
$invoiceid = $row4['id'];
$invoicenumber = $row4['invoiceid'];
$total = $row4['total'];
$customerid = $row4['accountid'];
$vehicleid = $row4['vehicleid'];
$vehicle = $row4['abvvehicle'];
$abvname = $row4['abvname'];
$invoicedate = $row4['invoicedate'];
$dinvoicedate = date('m/d/Y',strtotime($invoicedate));
$typeid = $row4['type'];
$invoicelocationid = $row4['location'];
$displaytotal = '$'.$total;
if($customerid < '1')
{
$fullname = "No Customer Selected";
}
else{
$sth5 = $pdocxn->prepare('SELECT `firstname`,`lastname` FROM `accounts` WHERE `accountid` = \''.$customerid.'\'');
$sth5->execute();
while($row5 = $sth5->fetch(PDO::FETCH_ASSOC))
{
$firstname = $row5['firstname'];
$lastname = $row5['lastname'];
$fullname = $firstname." ".$lastname;
}}
if($vehicleid < '1')
{
	$displayvehicle = "No Vehicle Selected";
}
else
{
$sql2 = 'SELECT `year`,`make`,`model`,`description` FROM `vehicles` WHERE `id` = :vehicleid';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':vehicleid',$vehicleid);
$sth2->execute();
while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
	{
$year = "";
$make = "";
$model = "";
$year = $row2['year'];
$model1 = $row2['model'];
$model = str_replace("?","",$model1);
$make1 = $row2['make'];
$make = str_replace("?","",$make1);
if($year < '1')
{
$displayvehicle = $row2['description'];
//$displayvehicle1 = str_replace("?","",$$displayvehicle1a);
}else{
$displayvehicle = $year."&nbsp;&nbsp;".$make."&nbsp;&nbsp;".$model;
}
}
}
$sql2 = 'SELECT * FROM `invoice_type` WHERE `id` = :typeid';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':typeid',$typeid);
$sth2->execute();
while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
	{
$typename = $row2['name'];
}
$sql2 = 'SELECT `storename` FROM `locations` WHERE `id` = :invoicelocationid';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':invoicelocationid',$invoicelocationid);
$sth2->execute();
while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
	{
$invoicestorename = $row2['storename'];
}
$newid = $invoiceid;

echo "\n<tr id=\"highlight\"><td><a href=\"invoice.php?invoiceid=".$invoiceid."\" target=\"_BLANK\" class=\"no-decoration\"><input type=\"button\" name=\"submit\" class=\"btn-style\" id=\"".$invoicenumber."\" value=\"".$typename." #";
if($typeid == '1')
{
	echo $invoicenumber;
	}
else
{
	echo $invoiceid;
}
	echo "\" /></a></td><td><a href=\"invoice.php?invoiceid=".$invoiceid."\" target=\"_BLANK\" class=\"no-decoration\"><input type=\"button\" name=\"submit\" class=\"btn-style\" id=\"".$invoicenumber."\" value=\"".$dinvoicedate."\" /></a></td><td><a href=\"invoice.php?invoiceid=".$invoiceid."\" target=\"_BLANK\" class=\"no-decoration\"><input type=\"button\" name=\"submit\" class=\"btn-style\" id=\"".$invoicenumber."\" value=\"".$fullname."\" /></a></td><td><a href=\"invoice.php?invoiceid=".$invoiceid."\" target=\"_BLANK\" class=\"no-decoration\"><input type=\"button\" name=\"submit\" class=\"btn-style\" id=\"".$invoicenumber."\" value=\"".$displaytotal."\" /></a></td><td><a href=\"invoice.php?invoiceid=".$invoiceid."\" target=\"_BLANK\" class=\"no-decoration\"><input type=\"button\" name=\"submit\" class=\"btn-style\" id=\"".$invoicenumber."\" value=\"".$displayvehicle."\" /></a></td><td><a href=\"invoice.php?invoiceid=".$invoiceid."\" target=\"_BLANK\" class=\"no-decoration\"><input type=\"button\" name=\"submit\" class=\"btn-style\" id=\"".$invoicenumber."\" value=\"".$invoicestorename."\" /></a></td></tr>";
}
}
$oldid = $invoiceid;
}
}
?>

</table></div>
</body>
</html>
<?php

}
else{
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
        <div id="content"><form name="account" action="searchinvoice.php" method="post">
		<table class="searchtable"><tr><th>Transaction ID:</th><td><input type="text" name="invoicenumber" placeholder="Transaction ID" autocomplete="off" autofocus><br /></td></tr>
        	<tr><th>Keyword:</th><td><input type="text" name="keyword" autocomplete="off" placeholder="keyword"></td></td></tr>
<tr><th>Transaction Type:</th><td><center><div class="styled-select black rounded"><select name="selectedtype"><option value="0">All Types</option><option value="1">Invoices</option><option value="4">Quotes</option><option value="52">Schedule</option></select></div></center></td></tr>
<tr><td colspan="2"><input type="hidden" name="search" value="1"><input type="submit" name="submit" class="btn-style" value="Search"></form></td></tr>
        	</table>
        </div>
</body>
</html>
<?php
}
?>