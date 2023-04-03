<?php
//verify salt not in db first

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

else{
	if(isset($_POST['invoiceid']))
{
  do{
    //verify salt not in db first
    $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $salt = substr(str_shuffle($permitted_chars),0, 16);
    $sth1 = $pdocxn->prepare('SELECT `id` FROM `customeremail` WHERE `salt` = :salt');
    $sth1->bindParam(':salt',$salt);
    $sth1->execute();
    }
    while ($sth1->rowCount() == 0)
    
    $sth2 = $pdocxn->prepare('INSERT INTO `customeremail` (`invoiceid`,`salt`)VALUES(:inv,:salt)');
    $sth2->bindParam(':inv',$invoiceid);
    $sth2->bindParam('salt',$salt);
    $sth2->execute();
$invoiceid = $_POST['invoiceid'];
$to = $_POST['emailto'];
$invoiceid = $_POST['invoiceid'];
$typedmessage = $_POST['message'];
$typedsubject = $_POST['subject'];
$subject = "Team Matthews Tire Center ".$typename;

$message = "
<html>
<head>
<title></title>
</head>
<body>
<a href=\"http://tmatire.com/customer/emailedinvoice.php?i=".$invoiceid."&s=".$salt."\">Click Here</a>
".$typedmessage."<br /><br /><br />if the above link does not work, copy the below link and open it in a web browser<br/>
http://tmatire.com/customer/emailedinvoice.php?i=".$invoiceid."&s=".$salt."
</body>
</html>
";
// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From: no-reply@auto-shop-software.com" . "\r\n";
$headers .="Reply-To: no-reply@auto-shop-software.com" . "\r\n";
$headers .="X-Mailer: PHP/" . phpversion();

mail($to,$typedsubject,$message,$headers);
echo "This email was Sent to: ".$to;
}
else{
	echo "Sorry, there was an error, please try again";
}}
else
{
  if(isset($_GET['invoiceid']))
  {
$invoiceid = $_GET['invoiceid'];
$dbemail = $_GET['dbemail'];
$typename = $_GET['typename'];
  }else{
 header('location:invoice.php') }
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
<link rel="shortcut icon" type="image/x-icon" href="images/icons/favicon.ico" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" >
<link rel="stylesheet" type="text/css" href="style/globalstyle.css" >
<link rel="stylesheet" type="text/css" href="style/indexstyle.css" >
</head>
<body>
<div id="content">
<form name="emailinvoice" action="emailinvoice.php" method="POST"><table>
	<tr><td colspan="2">Emailing invoice for <?php echo $fullname; ?></td></tr>
<tr><td>email Subject:</td><td><input type="text" name="subject" size="50" value="Team Matthews Tire Center <?php echo $typename; ?>"></td></tr>
<tr><td>email Message:</td><td><textarea name="message" cols="40" rows="3">Here is a copy of the <?php echo $typename; ?>, thank you</textarea></td></tr>
<?php
if($dbemail > '0')
{
?>
<tr><td><input type="text" name="emailto" autocomplete="off" size="50" value="<?php echo $dbemail; ?>">
<?php	
}
else {
?>
<tr><td><input type="text" name="emailto" autocomplete="off" size="50" placeholder="email to address">
<?php	
}
?>
</td><td><input type="text" name="emailfrom" autocomplete="off" size="50" placeholder="email from address"></td></tr>
<tr><td><input type="text" name="cell" autocomplete="off" placeholder="email to cell phone"></td><td><select Name="carrier"><option value="0">Select Cell Phone Carrier</option><option value="mms.alltelwireless.com">Alltel</option><option value="mms.att.net">AT&T</option><option value="myboostmobile.com">Boost Mobil</option><option value="cellcom.quiktxt.com">CellCom</option><option value="mms.mycricket.com">Cricket</option><option value="SMS.elementmobile.net">Element</option><option value="pm.sprint.com">Sprint</option><option value="tmomail.net">T-Mobil</option><option value="mms.uscc.net">US Cellular</option><option value="vzwpix.com">Verizon</option><option value="vmpix.com">Virgin Mobil</option></select></td></tr>
<tr><td><input type="hidden" name="email" value="1"><input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>"><input type="hidden" name="s" value="<?php echo $salt; ?>"><input type="submit" name="submit" class="quotebutton" value="Send">
</table></form></div>
</body></html>
<?php
}
?>