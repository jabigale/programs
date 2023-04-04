<?php
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Give Change/Apply as Credit';
$linkpage = 'checkchange.php';
$quicksearch = '0';
$location = '1';
$invoicesubtotal = '0';
$paymentid = '-1';

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

$sql1 = "SELECT `variable` FROM `global_settings` WHERE `id` = '7'";
$sth1 = $pdocxn->prepare($sql1);
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
	$footertag = $row1['variable'];
}
if(isset($_GET['charge']))
{
	$charge = $_GET['charge'];
	$chargelink = '&charge=1';
	}
else {
	$charge = '0';
	$chargelink ='';
}
if(isset($_GET['tempid']))
{
	$tempid = $_GET['tempid'];
	}

$sql1 = "SELECT `invoiceid`,`paymentdate`,`invoiceamount`,`cash`,`checkamount`,`checknumber`,`cc1`,`cc1type`,`cc2`,`cc2type`,`cc3`,`cc3type`,`complete`,`copies` FROM `temp-payment` WHERE `id` = :tempid";
$sth1 = $pdocxn->prepare($sql1);
$sth1->bindParam(':tempid',$tempid);
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
  $invoiceid = $row1['invoiceid'];
  $paymentdate = $row1['paymentdate'];
  $invoiceamount = $row1['invoiceamount'];
  $cash = $row1['cash'];
  $checkamount = $row1['checkamount'];
  $checknumber = $row1['checknumber'];
  $cc1amount = $row1['cc1'];
  $cc1type = $row1['cc1type'];
  $cc2amount = $row1['cc2'];
  $cc2type = $row1['cc2type'];
  $cc3amount = $row1['cc3'];
  $cc3type = $row1['cc3type'];
  $copies = $row1['copies'];
}
$totalpaymentamount = $cash + $checkamount + $cc1amount + $cc2amount + $cc3amount;
$differenceamount = $totalpaymentamount - $invoiceamount;

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
<link rel="stylesheet" type="text/css" href="style/changestyle.css" >
<script type="text/javascript" src="scripts/globaljs.js"></script>
<script type="text/javascript" src="scripts/jquery-1.9.1.js"></script>
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
<body>
<div id="ciheader"><br /><br /><b>Enter Change Amount</b></div>
<div id="selecteduser"></div>
        <div id="content">
		<div class="right1">
		<b>Invoice Amount: <?php echo $invoiceamount; ?></b><br />
		<b>Payment Amount: <?php echo $totalpaymentamount; ?></b><br />
		<b>Overpayment: <?php echo $differenceamount; ?></b><br />
		<div id="totalpayments"></div></div><div id="left1">
        	<table><tr><td colspan="4"><a href="payments.php?invoiceid=<?php echo $invoiceid; ?>"><input type="button" name="submit" class="cancel" value="Cancel/Back"></a><br /></td></tr>
            <tr><td><br /><br /><br /><br /><br /></td></tr><tr><td colspan="4"><center><b>Select amount of Change or to Apply as credit</b></center><br/></td></tr><tr><td><br /></td></tr>
<tr>
<form name="account" action="printinvoice.php" method="post">
<th>Change Amount:</th><td><input type="text" name="changeamount" autocomplete="off" id="changeamount" value="<?php echo $differenceamount;?>" autofocus></td>
<th>Amount to Apply as Credit:</th><td><input type="text" name="chargeamount" id="chargeamount" autocomplete="off"></td></tr>
<tr><td colspan="4"><input type="hidden" name="search" value="1">
<input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>">
<input type="hidden" name="invoiceamount" value="<?php echo $invoiceamount; ?>">
<input type="hidden" name="paymentdate" value="<?php echo $paymentdate; ?>">
<input type="hidden" name="cash" value="<?php echo $cash; ?>">
<input type="hidden" name="checkamount" value="<?php echo $checkamount; ?>">
<input type="hidden" name="cc1" value="<?php echo $cc1amount; ?>">
<input type="hidden" name="cc1type" value="<?php echo $cc1type; ?>">
<input type="hidden" name="cc2" value="<?php echo $cc2amount; ?>">
<input type="hidden" name="cc2type" value="<?php echo $cc2type; ?>">
<input type="hidden" name="cc3" value="<?php echo $cc3amount; ?>">
<input type="hidden" name="cc3type" value="<?php echo $cc3type; ?>">
<input type="hidden" name="charge" value="<?php echo $charge; ?>">
<input type="hidden" name="enterpayment" value="1">
<input type="submit" name="submit" class="quotebutton" value="Apply Payment"></td></tr></form>
</tr></form>
</table>
    </div>

<script>



var input1 = document.getElementById('changeamount');
input1.onfocus = function() {
       var invamount = <?php echo $invoiceamount; ?>;
	   var totalpaymentamount = <?php echo $totalpaymentamount; ?>;
	   var differenceamount = <?php echo $differenceamount; ?>;
       var txtchange = document.getElementById('changeamount').value;
       var txtcharge = document.getElementById('chargeamount').value;
       if (txtchange == "")
           { txtchange = 0; }
       if (txtcharge == "")
           { txtcharge = 0; }
		   var result1 = parseFloat(differenceamount) - parseFloat(txtcharge);
       var remainderbalance = result1.toFixed(2);
       if(remainderbalance <= 0)
       { var remainderbalance = 0; }
       if (txtcharge == "")
           {
       document.getElementById("changeamount").value = remainderbalance;
           }
		   var displaychange = remainderbalance.toFixed(2);
       document.getElementById("totalpayments").innerHTML = "<p class=\"warningfontcolorb\">Give change: " + displaychange + "</p><p class=\"warningfontcolorb\">Apply as credit: " + txtcharge + "</p>";
	   if (txtchange == "")
           {document.getElementById("chargeamount").value = displaycharge;}
}
input1.onkeyup = function() {
       var invamount = <?php echo $invoiceamount; ?>;
	   var totalpaymentamount = <?php echo $totalpaymentamount; ?>;
	   var differenceamount = <?php echo $differenceamount; ?>;
       var txtchange = document.getElementById('changeamount').value;
       var txtcharge = document.getElementById('chargeamount').value;
       if (txtcharge == "")
           { txtcharge = 0; }
       if (txtchange == "")
           { txtchange = 0; }
      var displaycharge = differenceamount - txtchange;
       var displaycharge2 = displaycharge.toFixed(2);
       var displaychange = differenceamount - displaycharge2;
		   var displaychange2 = displaychange.toFixed(2);
       document.getElementById("totalpayments").innerHTML = "<p class=\"warningfontcolorb\">Give change: " + displaychange2 + "</p><p class=\"warningfontcolorb\">Apply as credit: " + displaycharge2 + "</p>";
	   document.getElementById("chargeamount").value = displaycharge2;
}


var input2 = document.getElementById('chargeamount');
input2.onfocus = function() {
       var invamount = <?php echo $invoiceamount; ?>;
	   var totalpaymentamount = <?php echo $totalpaymentamount; ?>;
	   var differenceamount = <?php echo $differenceamount; ?>;
       var txtchange = document.getElementById('changeamount').value;
       var txtcharge = document.getElementById('chargeamount').value;
       if (txtchange == "")
           { txtchange = 0; }
       if (txtcharge == "")
           { txtcharge = 0; }
		   var result1 = parseFloat(differenceamount) - parseFloat(txtchange);
       var remainderbalance = result1.toFixed(2);
       if(remainderbalance <= 0)
       { var remainderbalance = 0; }
       if (txtcharge == "")
           {
       document.getElementById("chargeamount").value = remainderbalance;
           }
		   var displaychange = remainderbalance.toFixed(2);
       document.getElementById("totalpayments").innerHTML = "<p class=\"warningfontcolorb\">Give change: " + displaychange + "</p><p class=\"warningfontcolorb\">Apply as credit: " + txtcharge + "</p>";
	   if (txtchange == "")
           {document.getElementById("changeamount").value = displaychange;}
}
input2.onkeyup = function() {
     var invamount = <?php echo $invoiceamount; ?>;
	   var totalpaymentamount = <?php echo $totalpaymentamount; ?>;
	   var differenceamount = <?php echo $differenceamount; ?>;
     var txtchange = document.getElementById('changeamount').value;
     var txtcharge = document.getElementById('chargeamount').value;
       if (txtchange == "")
           { txtchange = 0; }
       if (txtcharge == "")
           { txtcharge = 0; }
		   var displaychange = differenceamount - txtcharge;
		   var displaychange2 = displaychange.toFixed(2);
       var displaycharge = differenceamount - displaychange2;
       var displaycharge2 = displaycharge.toFixed(2);
       document.getElementById("totalpayments").innerHTML = "<p class=\"warningfontcolorb\">Give change: " + displaychange2 + "</p><p class=\"warningfontcolorb\">Apply as credit: " + displaycharge2 + "</p>";
	   document.getElementById("changeamount").value = displaychange2;
}




</script>


</body>
</html>