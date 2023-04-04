<?php
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Invoice Payment';
$linkpage = 'payments.php';

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
if(isset($_GET['invoiceid']))
{
$invoiceid = $_GET['invoiceid'];
$sql1 = "SELECT `id`,`total` FROM `invoice` WHERE `id` = :invoiceid";
$sth1 = $pdocxn->prepare($sql1);
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->execute()or die(print_r($sth1->errorInfo(), true));
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
$invoiceamount = $row1['total'];
}
}
if(isset($_POST['invoiceid']))
{
$invoiceid = $_POST['invoiceid'];
$sql1 = "SELECT `id`,`total` FROM `invoice` WHERE `id` = :invoiceid";
$sth1 = $pdocxn->prepare($sql1);
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
$invoiceamount = $row1['total'];
}
}
if(isset($_POST['vehicleid']))
{
$vehicleid = $_POST['vehicleid'];
}
else{
$vehicleid = '0';
}
if(isset($_POST['accountid']))
{
$accountid = $_POST['accountid'];
}
else{
$accountid = '0';
}
if(isset($_POST['invoiceform']))
{
$invoiceid = $_POST['invoiceid'];
$accountid = $_POST['accountid'];
$vehicleid = $_POST['vehicleid'];
}
if(isset($_POST['enterpayment']))
{
$cash = $_POST['cash'];
$checkamount = $_POST['checkamount'];
$cc1 = $_POST['cc1'];
$cc2 = $_POST['cc2'];
$cc3 = $_POST['cc3'];
$cc1type = $_POST['cc1type'];
$cc2type = $_POST['cc2type'];
$cc3type = $_POST['cc3type'];
$roa = $_POST['roa'];
$invoiceamount = $_POST['invoiceamount'];
$invoiceid = $_POST['invoiceid'];
$accountid = $_POST['accountid'];
$vehicleid = $_POST['vehicleid'];
$changegiven = $_POST['changegiven'];
$checknumber = $_POST['checknumber'];

if(isset($_POST['roa']))
{
$roa = $_POST['roa'];
}
else{
$roa = '0';
}
$sql1 = "SELECT `id` FROM `payment` ORDER BY `id` DESC LIMIT 1";
$sth1 = $pdocxn->prepare($sql1);
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
$id = $row1['id'];
$newid = $id + '1';
}
if($cash > '0')
{

}
if($checkamount > '0')
{
$paymenttype = '2';
$changegiven = $_POST['changegiven'];
$changegiven_amount = $_POST['changegivenamount'];
$checknumber = $_POST['checknumber'];
$accountid = $_POST['accountid'];;
$vehicleid = $_POST['invoiceid'];

$sql1 = "INSERT INTO `payments` (`id`,`amount`,`paymenttype`,`date`,`changegiven`,`changegiven_amount`,`checknumber`,`accountid`,`invoiceid`,`roa`,`vehicleid`) VALUES (:id,:amount,:paymenttype,:date,:changegiven,:changegiven_amount,:checknumber,:accountid,:invoiceid,:roa,:vehicleid)')";

$sth1->bindParam(':id',$newid);
$sth1->bindParam(':amount',$checkamount);
$sth1->bindParam(':paymenttype',$paymenttype);
$sth1->bindParam(':date',$paymentdate);
$sth1->bindParam(':changegiven',$changegiven);
$sth1->bindParam(':changegiven_amount',$changegiven_amount);
$sth1->bindParam(':checknumber',$checknumber);
$sth1->bindParam(':accountid',$accountid);
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->bindParam(':roa',$roa);
$sth1->bindParam(':vehicleid',$vehicleid);



$sql2 = 'SELECT `journal` FROM `invoice_type` WHERE `id` = :typeid';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':typeid',$typeid);
$sth2->execute();
while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
{
 $confirmjournal = $row2['journal'];
}


if($confirmjournal = '1')
{
$sql1 = 'SELECT `id` FROM `journal` WHERE `invoiceid` = :invoiceid';
$sth1 = $pdocxn->prepare($sql1);
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->execute();
if ($sth1->rowCount() > 0)
{
$sth1 = $pdocxn->prepare('UPDATE `journal` SET `total`=:total,`invoicedate`=:invoicedate,`accountid`=:accountid WHERE `invoiceid` = :invoiceid');
$sth1->bindParam(':total',$invoicetotal);
$sth1->bindParam(':invoicedate',$invoicedate);
$sth1->bindParam(':accountid',$accountid);
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->execute();
}
else{
	
$sql2 = 'INSERT INTO `journal` (`invoiceid`,`accountid`,`total`,`invoicedate`,`journaltype`,`siteid`) VALUES (:invoiceid,:accountid,:total,:invoicedate,:journaltype,:siteid)';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':invoiceid',$invoiceid);
$sth2->bindParam(':accountid',$accountid);
$sth2->bindParam(':total',$invoicetotal);
$sth2->bindParam(':invoicedate',$invoicedate);
$sth2->bindParam(':journaltype',$typeid);
$sth2->bindParam(':siteid',$location);
$sth2->execute()or die(print_r($sth2->errorInfo(), true));
}}


}
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title><?php echo $title; ?></title>
<link rel="shortcut icon" type="image/x-icon" href="images/icons/favicon.ico" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" >
<link rel="stylesheet" type="text/css" href="style/globalstyle.css" >
<link rel="stylesheet" type="text/css" href="style/indexstyle.css" >
<link rel="stylesheet" type="text/css" href="style/paymentstyle.css" >
<script type="text/javascript" src="http://whatcomputertobuy.com/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="http://whatcomputertobuy.com/js/jquery.js"></script>
<script type="text/javascript" src="scripts/script.js"></script>
<script type="text/javascript">
txt1.Attributes.Add("onfocus", "javascript:this.value='345';");
</script>
<script type="text/javascript">
focuscash = function getFocus() {           
  document.getElementById("cash").focus();
}
focuscheck = function getFocus() {           
  document.getElementById("check").focus();
}
focuscc1 = function getFocus() {           
  document.getElementById("cc1").focus();
}
focuscc2 = function getFocus() {           
  document.getElementById("cc2").focus();
}
focuscc3 = function getFocus() {           
  document.getElementById("cc3").focus();
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
<form name="enterpayment" id="enterpayment" action="printinvoice.php" method="POST"><table class="paymenttable">
    <tr><td>Payment Date:</td><td><input type="date" name="paymentdate" value="<?php echo date('Y-m-d'); ?>"></td><td rowspan="2"><b>Invoice Amount: <?php echo $invoiceamount; ?></b>
    <div id="totalpayments"></div>
 
    </td></tr>
    <tr><td><center><input type="button" name="submit" class="quotebutton" value="Cash" onclick="focuscash()"></center></td><td>
        <input type="textbox" name="cash" id="cash" placeholder="cash" ></td></tr>

<tr><td><center><input type="button" name="submit" class="quotebutton" value="Check" onclick="focuscheck()"></center></td>
<td>
<input type="textbox" name="checkamount" placeholder="check" id="check" ></td><td><input type="textbox" name="checknumber" placeholder="Check Number"></td>
</tr>
<tr><td><center><input type="button" name="submit" class="quotebutton" value="Credit Card" onclick="focuscc1()"></center></td><td><input type="textbox" name="cc1" placeholder="credit card" id="cc1"></td><td><select name="cc1type">
<?php
$sql3 = "SELECT `name`,`id` FROM `payment_type` WHERE `iscreditcard` = '1' ORDER BY `name` DESC";
$sth3 = $pdocxn->prepare($sql3);
$sth3->execute();
while($row3 = $sth3->fetch(PDO::FETCH_ASSOC))
{
$ccname = $row3['name'];
$ccid = $row3['id'];
echo "<option value=\"".$ccname."\">".$ccname."</option>";
}
?>
</select></td></tr>
<tr><td><center><input type="button" name="submit" class="quotebutton" value="Credit Card" onclick="focuscc2()"></center></td><td><input type="textbox" name="cc2" placeholder="credit card" id="cc2"></td><td><select name="cc2type">
<?php
$sql3 = "SELECT `name`,`id` FROM `payment_type` WHERE `iscreditcard` = '1' ORDER BY `name` DESC";
$sth3 = $pdocxn->prepare($sql3);
$sth3->execute();
while($row3 = $sth3->fetch(PDO::FETCH_ASSOC))
{
$ccname = $row3['name'];
$ccid = $row3['id'];
echo "<option value=\"".$ccname."\">".$ccname."</option>";
}
?>
</select></td>
</tr>
<tr><td><center><input type="button" name="submit" class="quotebutton" value="Credit Card" onclick="focuscc3()"></center></td><td><input type="textbox" name="cc3" placeholder="credit card" id="cc3"></td><td><select name="cc3type">
<?php
$sql3 = "SELECT `name`,`id` FROM `payment_type` WHERE `iscreditcard` = '1' ORDER BY `name` DESC";
$sth3 = $pdocxn->prepare($sql3);
$sth3->execute();
while($row3 = $sth3->fetch(PDO::FETCH_ASSOC))
{
$ccname = $row3['name'];
$ccid = $row3['id'];
echo "<option value=\"".$ccname."\">".$ccname."</option>";
}
?>
</select></td>
</tr>
<tr><td><center><a href="printinvoice.php?invoiceid=<?php echo $invoiceid; ?>&charge=1" class="no-decoration"><input type="button" name="charge" class="quotebutton" value="Charge"></center></a></td><td><p>Current Credit Left</td></tr>
<tr id="change"></tr>
<tr><td><center><input type="submit" class="smallbutton" alt="Save & Close" value="Save & Close" name="copy"></center></td><td><input type="submit" class="smallbutton" alt="Save & Print" value="Save & Print" name="copy"></td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td>
<input type="hidden" name="invoiceamount" value="<?php echo $invoiceamount; ?>"><input type="hidden" name="accountid" value="<?php echo $accountid; ?>"><input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>"><input type="hidden" name="vehicleid" value="<?php echo $vehicleid; ?>"><center><input type="submit" class="smallbutton" alt="Save & Print 1 Copy" value="1 Copy" name="copy"></center></td><td><input type="hidden" name="enterpayment" value="1"><input type="submit" class="smallbutton" alt="Save & Print 3 Copies" value="3 Copies" name="copy"></td></tr></table>
<script>
function myFunction() {
    var cash = document.getElementById("cash").value;
    if (cash <= <?php echo $invoiceamount; ?>) {
        document.getElementById("change").innerHTML = "";
    }
        if (cash > <?php echo $invoiceamount; ?>) {
        var changediplay = cash - <?php echo $invoiceamount; ?>;
        document.getElementById("change").innerHTML = changedisplay;
    }
}
</script>
<script>
var input1 = document.getElementById('cash');
input1.onkeyup = function() {
       var invamount = <?php echo $invoiceamount; ?>;
       var txtcash = document.getElementById('cash').value;
       var txtcheck = document.getElementById('check').value;
       var txtcc1 = document.getElementById('cc1').value;
       var txtcc2 = document.getElementById('cc2').value;
       var txtcc3 = document.getElementById('cc3').value;
       if (txtcash == "")
           { txtcash = 0; }
       if (txtcheck == "")
           { txtcheck = 0; }
       if (txtcc1 == "")
           { txtcc1 = 0; }
       if (txtcc2 == "")
           { txtcc2 = 0; }
       if (txtcc3 == "")
           { txtcc3 = 0; }
       var result1 = parseFloat(txtcash) + parseFloat(txtcheck) + parseFloat(txtcc1) + parseFloat(txtcc2) + parseFloat(txtcc3);
       var result = result1.toFixed(2);
       if (result > invamount)
       {
           var change = parseFloat(result) - parseFloat(invamount);
           var displaychange = change.toFixed(2);
       document.getElementById("totalpayments").innerHTML = "<b>Payment Amount: " + result + "</b><br /><b>Overpayment: " + displaychange + "</b>";
       }else{
       document.getElementById("totalpayments").innerHTML = "<b>Payment Amount: " + result + "</b>";
       }
}
var input1 = document.getElementById('cash');
input1.onfocus = function() {
       var invamount = <?php echo $invoiceamount; ?>;
       var txtcash = document.getElementById('cash').value;
       var txtcheck = document.getElementById('check').value;
       var txtcc1 = document.getElementById('cc1').value;
       var txtcc2 = document.getElementById('cc2').value;
       var txtcc3 = document.getElementById('cc3').value;
       if (txtcash == "")
           { txtcash = 0; }
       if (txtcheck == "")
           { txtcheck = 0; }
       if (txtcc1 == "")
           { txtcc1 = 0; }
       if (txtcc2 == "")
           { txtcc2 = 0; }
       if (txtcc3 == "")
           { txtcc3 = 0; }
       var result1 = parseFloat(txtcash) + parseFloat(txtcheck) + parseFloat(txtcc1) + parseFloat(txtcc2) + parseFloat(txtcc3);
       var balance1 = parseFloat(invamount) - parseFloat(result1);
       var balance = balance1.toFixed(2);
       if(balance <= 0)
       { var balance = 0; }
       if (txtcash == "")
           {
       document.getElementById("cash").value = balance;
           }
       var txtcash = document.getElementById('cash').value;
       if (txtcash == "")
           { txtcash = 0; }
       var result1 = parseFloat(txtcash) + parseFloat(txtcheck) + parseFloat(txtcc1) + parseFloat(txtcc2) + parseFloat(txtcc3);
       var result = result1.toFixed(2);
       if (result > invamount)
       {
           var change = parseFloat(result) - parseFloat(invamount);
           var displaychange = change.toFixed(2);
       document.getElementById("totalpayments").innerHTML = "<b>Payment Amount: " + result + "</b><br /><b>Overpayment: " + displaychange + "</b>";
       }else{
       document.getElementById("totalpayments").innerHTML = "<b>Payment Amount: " + result + "</b>";
       }
}
var input2 = document.getElementById('check');
input2.onkeyup = function() {
    var invamount = <?php echo $invoiceamount; ?>;
       var txtcash = document.getElementById('cash').value;
       var txtcheck = document.getElementById('check').value;
       var txtcc1 = document.getElementById('cc1').value;
       var txtcc2 = document.getElementById('cc2').value;
       var txtcc3 = document.getElementById('cc3').value;
       if (txtcash == "")
           { txtcash = 0; }
       if (txtcheck == "")
           { txtcheck = 0; }
       if (txtcc1 == "")
           { txtcc1 = 0; }
       if (txtcc2 == "")
           { txtcc2 = 0; }
       if (txtcc3 == "")
           { txtcc3 = 0; }
       var result1 = parseFloat(txtcash) + parseFloat(txtcheck) + parseFloat(txtcc1) + parseFloat(txtcc2) + parseFloat(txtcc3);
       var result = result1.toFixed(2);
       if (result > invamount)
       {
           var change = parseFloat(result) - parseFloat(invamount);
           var displaychange = change.toFixed(2);
       document.getElementById("totalpayments").innerHTML = "<b>Payment Amount: " + result + "</b><br /><b>Overpayment: " + displaychange + "</b>";
       }else{
       document.getElementById("totalpayments").innerHTML = "<b>Payment Amount: " + result + "</b>";
       }
}
input2.onfocus = function() {
    var invamount = <?php echo $invoiceamount; ?>;
       var txtcash = document.getElementById('cash').value;
       var txtcheck = document.getElementById('check').value;
       var txtcc1 = document.getElementById('cc1').value;
       var txtcc2 = document.getElementById('cc2').value;
       var txtcc3 = document.getElementById('cc3').value;
       if (txtcash == "")
           { txtcash = 0; }
       if (txtcheck == "")
           { txtcheck = 0; }
       if (txtcc1 == "")
           { txtcc1 = 0; }
       if (txtcc2 == "")
           { txtcc2 = 0; }
       if (txtcc3 == "")
           { txtcc3 = 0; }
       var result1 = parseFloat(txtcash) + parseFloat(txtcheck) + parseFloat(txtcc1) + parseFloat(txtcc2) + parseFloat(txtcc3);
       var balance1 = parseFloat(invamount) - parseFloat(result1);
       var balance = balance1.toFixed(2);
       if(balance <= 0)
       { var balance = 0; }
       if (txtcheck == "")
           {
       document.getElementById("check").value = balance;
           }
           var txtcheck = document.getElementById('check').value;
           if (txtcheck == "")
           { txtcheck = 0; }
       var result1 = parseFloat(txtcash) + parseFloat(txtcheck) + parseFloat(txtcc1) + parseFloat(txtcc2) + parseFloat(txtcc3);
       var result = result1.toFixed(2);
       if (result > invamount)
       {
           var change = parseFloat(result) - parseFloat(invamount);
           var displaychange = change.toFixed(2);
       document.getElementById("totalpayments").innerHTML = "<b>Payment Amount: " + result + "</b><br /><b>Overpayment: " + displaychange + "</b>";
       }else{
       document.getElementById("totalpayments").innerHTML = "<b>Payment Amount: " + result + "</b>";
       }
}
var input3 = document.getElementById('cc1');
input3.onkeyup = function() {
    var invamount = <?php echo $invoiceamount; ?>;
       var txtcash = document.getElementById('cash').value;
       var txtcheck = document.getElementById('check').value;
       var txtcc1 = document.getElementById('cc1').value;
       var txtcc2 = document.getElementById('cc2').value;
       var txtcc3 = document.getElementById('cc3').value;
       if (txtcash == "")
           { txtcash = 0; }
       if (txtcheck == "")
           { txtcheck = 0; }
       if (txtcc1 == "")
           { txtcc1 = 0; }
       if (txtcc2 == "")
           { txtcc2 = 0; }
       if (txtcc3 == "")
           { txtcc3 = 0; }
       var result1 = parseFloat(txtcash) + parseFloat(txtcheck) + parseFloat(txtcc1) + parseFloat(txtcc2) + parseFloat(txtcc3);
       var result = result1.toFixed(2);
       if (result > invamount)
       {
           var change = parseFloat(result) - parseFloat(invamount);
           var displaychange = change.toFixed(2);
       document.getElementById("totalpayments").innerHTML = "<b>Payment Amount: " + result + "</b><br /><b>Overpayment: " + displaychange + "</b>";
       }else{
       document.getElementById("totalpayments").innerHTML = "<b>Payment Amount: " + result + "</b>";
       }
}
input3.onfocus = function() {
    var invamount = <?php echo $invoiceamount; ?>;
       var txtcash = document.getElementById('cash').value;
       var txtcheck = document.getElementById('check').value;
       var txtcc1 = document.getElementById('cc1').value;
       var txtcc2 = document.getElementById('cc2').value;
       var txtcc3 = document.getElementById('cc3').value;
       if (txtcash == "")
           { txtcash = 0; }
       if (txtcheck == "")
           { txtcheck = 0; }
       if (txtcc1 == "")
           { txtcc1 = 0; }
       if (txtcc2 == "")
           { txtcc2 = 0; }
       if (txtcc3 == "")
           { txtcc3 = 0; }
       var result1 = parseFloat(txtcash) + parseFloat(txtcheck) + parseFloat(txtcc1) + parseFloat(txtcc2) + parseFloat(txtcc3);
       var balance1 = parseFloat(invamount) - parseFloat(result1);
       var balance = balance1.toFixed(2);
       if(balance <= 0)
       { var balance = 0; }
       if (txtcc1 == "")
           {
       document.getElementById("cc1").value = balance;
           }
           var txtcc1 = document.getElementById('cc1').value;
       if (txtcc1 == "")
           { txtcc1 = 0; }
       var result1 = parseFloat(txtcash) + parseFloat(txtcheck) + parseFloat(txtcc1) + parseFloat(txtcc2) + parseFloat(txtcc3);   
       var result = result1.toFixed(2);
       if (result > invamount)
       {
           var change = parseFloat(result) - parseFloat(invamount);
           var displaychange = change.toFixed(2);
       document.getElementById("totalpayments").innerHTML = "<b>Payment Amount: " + result + "</b><br /><b>Overpayment: " + displaychange + "</b>";
       }else{
       document.getElementById("totalpayments").innerHTML = "<b>Payment Amount: " + result + "</b>";
       }
}
var input4 = document.getElementById('cc2');
input4.onkeyup = function() {
    var invamount = <?php echo $invoiceamount; ?>;
       var txtcash = document.getElementById('cash').value;
       var txtcheck = document.getElementById('check').value;
       var txtcc1 = document.getElementById('cc1').value;
       var txtcc2 = document.getElementById('cc2').value;
       var txtcc3 = document.getElementById('cc3').value;
       if (txtcash == "")
           { txtcash = 0; }
       if (txtcheck == "")
           { txtcheck = 0; }
       if (txtcc1 == "")
           { txtcc1 = 0; }
       if (txtcc2 == "")
           { txtcc2 = 0; }
       if (txtcc3 == "")
           { txtcc3 = 0; }
       var result1 = parseFloat(txtcash) + parseFloat(txtcheck) + parseFloat(txtcc1) + parseFloat(txtcc2) + parseFloat(txtcc3);
       var result = result1.toFixed(2);
       if (result > invamount)
       {
           var change = parseFloat(result) - parseFloat(invamount);
           var displaychange = change.toFixed(2);
       document.getElementById("totalpayments").innerHTML = "<b>Payment Amount: " + result + "</b><br /><b>Overpayment: " + displaychange + "</b>";
       }else{
       document.getElementById("totalpayments").innerHTML = "<b>Payment Amount: " + result + "</b>";
       }
}
input4.onfocus = function() {
    var invamount = <?php echo $invoiceamount; ?>;
       var txtcash = document.getElementById('cash').value;
       var txtcheck = document.getElementById('check').value;
       var txtcc1 = document.getElementById('cc1').value;
       var txtcc2 = document.getElementById('cc2').value;
       var txtcc3 = document.getElementById('cc3').value;
       if (txtcash == "")
           { txtcash = 0; }
       if (txtcheck == "")
           { txtcheck = 0; }
       if (txtcc1 == "")
           { txtcc1 = 0; }
       if (txtcc2 == "")
           { txtcc2 = 0; }
       if (txtcc3 == "")
           { txtcc3 = 0; }
       var result1 = parseFloat(txtcash) + parseFloat(txtcheck) + parseFloat(txtcc1) + parseFloat(txtcc2) + parseFloat(txtcc3);
       var balance1 = parseFloat(invamount) - parseFloat(result1);
       var balance = balance1.toFixed(2);
       if(balance <= 0)
       { var balance = 0; }
       if (txtcc1 == "")
           {
       document.getElementById("cc2").value = balance;
           }
           var txtcc2 = document.getElementById('cc2').value;
       if (txtcc2 == "")
           { txtcc2 = 0; }
       var result1 = parseFloat(txtcash) + parseFloat(txtcheck) + parseFloat(txtcc1) + parseFloat(txtcc2) + parseFloat(txtcc3); 
       var result = result1.toFixed(2);
       if (result > invamount)
       {
           var change = parseFloat(result) - parseFloat(invamount);
           var displaychange = change.toFixed(2);
       document.getElementById("totalpayments").innerHTML = "<b>Payment Amount: " + result + "</b><br /><b>Overpayment: " + displaychange + "</b>";
       }else{
       document.getElementById("totalpayments").innerHTML = "<b>Payment Amount: " + result + "</b>";
       }
}
var input5 = document.getElementById('cc3');
input5.onkeyup = function() {
    var invamount = <?php echo $invoiceamount; ?>;
       var txtcash = document.getElementById('cash').value;
       var txtcheck = document.getElementById('check').value;
       var txtcc1 = document.getElementById('cc1').value;
       var txtcc2 = document.getElementById('cc2').value;
       var txtcc3 = document.getElementById('cc3').value;
       if (txtcash == "")
           { txtcash = 0; }
       if (txtcheck == "")
           { txtcheck = 0; }
       if (txtcc1 == "")
           { txtcc1 = 0; }
       if (txtcc2 == "")
           { txtcc2 = 0; }
       if (txtcc3 == "")
           { txtcc3 = 0; }
       var result1 = parseFloat(txtcash) + parseFloat(txtcheck) + parseFloat(txtcc1) + parseFloat(txtcc2) + parseFloat(txtcc3);
       var result = result1.toFixed(2);
       if (result > invamount)
       {
           var change = parseFloat(result) - parseFloat(invamount);
           var displaychange = change.toFixed(2);
       document.getElementById("totalpayments").innerHTML = "<b>Payment Amount: " + result + "</b><br /><b>Overpayment: " + displaychange + "</b>";
       }else{
       document.getElementById("totalpayments").innerHTML = "<b>Payment Amount: " + result + "</b>";
       }
}
input5.onfocus = function() {
    var invamount = <?php echo $invoiceamount; ?>;
       var txtcash = document.getElementById('cash').value;
       var txtcheck = document.getElementById('check').value;
       var txtcc1 = document.getElementById('cc1').value;
       var txtcc2 = document.getElementById('cc2').value;
       var txtcc3 = document.getElementById('cc3').value;
       if (txtcash == "")
           { txtcash = 0; }
       if (txtcheck == "")
           { txtcheck = 0; }
       if (txtcc1 == "")
           { txtcc1 = 0; }
       if (txtcc2 == "")
           { txtcc2 = 0; }
       if (txtcc3 == "")
           { txtcc3 = 0; }
       var result1 = parseFloat(txtcash) + parseFloat(txtcheck) + parseFloat(txtcc1) + parseFloat(txtcc2) + parseFloat(txtcc3);
       var balance1 = parseFloat(invamount) - parseFloat(result1);
       var balance = balance1.toFixed(2);
       if(balance <= 0)
       { var balance = 0; }
       if (txtcc3 == "")
           {
       document.getElementById("cc3").value = balance;
           }
       var txtcc3 = document.getElementById('cc3').value;
       if (txtcc3 == "")
           { txtcc3 = 0; }
       var result1 = parseFloat(txtcash) + parseFloat(txtcheck) + parseFloat(txtcc1) + parseFloat(txtcc2) + parseFloat(txtcc3); 
       var result = result1.toFixed(2);
       if (result > invamount)
       {
           var change = parseFloat(result) - parseFloat(invamount);
           var displaychange = change.toFixed(2);
       document.getElementById("totalpayments").innerHTML = "<b>Payment Amount: " + result + "</b><br /><b>Overpayment: " + displaychange + "</b>";
       }else{
       document.getElementById("totalpayments").innerHTML = "<b>Payment Amount: " + result + "</b>";
       }
}
</script></form>
</div>
</body></html>

