<?php
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Edit Account';
$linkpage = 'editaccount.php';

session_start();
date_default_timezone_set('America/Chicago');
$currentdate = date('Y-n-j H:i:s');
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
if(isset($_POST['editaccount']))
{
	$accountid = $_POST['accountid'];
	$fname = $_POST['firstname'];
	$lname = $_POST['lastname'];
	$address = $_POST['address'];
	$address2 = $_POST['address2'];
	$city = $_POST['city'];
	$state = $_POST['state'];
	$zip = $_POST['zip'];
	$phone1 = $_POST['phone1'];
	$phone2 = $_POST['phone2'];
	$phone3 = $_POST['phone3'];
	$phone4 = $_POST['phone4'];
	if($phone1 > '0')
	{
	$sphone1=preg_replace('/[^0-9]/', '', $phone1);
	}else{$shpone1 = '0';}
	if($phone2 > '0')
	{
	$sphone2=preg_replace('/[^0-9]/', '', $phone2);
	}else{$shpone2 = '0';}
	if($phone3 > '0')
	{
	$sphone3=preg_replace('/[^0-9]/', '', $phone3);
	}else{$shpone3 = '0';}
	if($phone4 > '0')
	{
	$sphone4=preg_replace('/[^0-9]/', '', $phone4);
	}else{$sphone4 = '0';}
	$contact1 = $_POST['contact1'];
	$contact2 = $_POST['contact2'];
	$contact3 = $_POST['contact3'];
	$contact4 = $_POST['contact4'];
	$fax = $_POST['fax'];
	$email = $_POST['email'];
	$creditlimit = $_POST['creditlimit'];
	$taxid = $_POST['taxid'];
	$priceclass = $_POST['priceclass'];
	$taxclass = $_POST['taxclass'];
	$nationalaccount = $_POST['nationalaccount'];
	$requirepo = $_POST['requirepo'];
	$accounttype = $_POST['accounttype'];
	$flag = "0";
	$comment = $_POST['comment'];
	$fullname = $fname." ".$lname;
$website = '0';
$statement = '1';
$allowcod =  '0';
$RequirePO =  '0';
$retail =  '1';
$vendor = '0';
$commercial =  '0';
$resale =  '0';
$distributor =  '0';
$affiliate =  '0';
if(isset($_POST['invoiceid']))
{
$invoiceid = $_POST['invoiceid'];
}
if(isset($_POST['appointmentid']))
{
$appointmentid = $_POST['appointmentid'];
}
/*
echo "<br>".$accountid;
echo "<br>".$fname;
echo "<br>".$lname;
echo "<br>".$address;
echo "<br>".$address2;
echo "<br>".$city;
echo "<br>".$state;
echo "<br>".$zip;
echo "<br>".$phone1;
echo "<br>".$phone2;
echo "<br>".$phone3;
echo "<br>".$phone4;
echo "<br>".$sphone1;
echo "<br>".$sphone2;
echo "<br>".$sphone3;
echo "<br>".$sphone4;
echo "<br>".$contact1;
echo "<br>".$contact2;
echo "<br>".$contact3;
echo "<br>".$contact4;
echo "<br>".$fax;
echo "<br>".$email;
echo "<br>".$creditlimit;
echo "<br>".$taxid;
echo "<br>".$priceclass;
echo "<br>".$taxclass;
echo "<br>".$nationalaccount;
echo "<br>".$requirepo;
echo "<br>".$accounttype;
echo "<br>".$flag;
echo "<br>".$comment;
echo "<br>".$fullname;
echo "<br>".$invoiceid;
*/
$sql1a = "UPDATE `accounts` SET `firstname`=:firstname,`lastname`=:lastname,`fullname`=:fullname,`address`=:address,`address2`=:address2,`city`=:city,`state`=:state,`zip`=:zip,`phone1`=:phone1,`sphone1`=:sphone1,`phone2`=:phone2,`sphone2`=:sphone2,`phone3`=:phone3,`sphone3`=:sphone3,`phone4`=:phone4,`sphone4`=:sphone4,`contact1`=:contact1,`contact2`=:contact2,`contact3`=:contact3,`contact4`=:contact4,`fax`=:fax,`email`=:email,`website`=:website,`creditlimit`=:creditlimit,`taxid`=:taxid,`priceclass`=:priceclass,`taxclass`=:taxclass,`RequirePO`=:requirepo,`retail`=:retail,`commercial`=:commercial,`vendor`=:vendor,`nationalaccount`=:nationalaccount,`affiliate`=:affiliate,`flag`=:flag,`statement`=:statement,`allowcod`=:allowcod,`comment`=:comment,`lastactivedate`=:lastactiveday WHERE `accountid` = :accountid";
$sql1 = "UPDATE `accounts` SET `firstname`=:firstname,`lastname`=:lastname,`fullname`=:fullname,`address`=:address,`address2`=:address2,`city`=:city,`state`=:state,`zip`=:zip WHERE `accountid` = :accountid";
$sql1d = "UPDATE `accounts` SET `firstname`='$fname',`lastname`='$lname',`fullname`='$fullname',`address`='$address',`address2`='$address2',`city`='$city',`state`='$state',`zip`='$zip',`phone1`='$phone1',`sphone1`='$sphone1',`phone2`='$phone2',`sphone2`='$sphone2',`phone3`='$phone3',`sphone3`='$sphone3',`phone4`='$phone4',`sphone4`='$sphone4',`contact1`='$contact1',`contact2`='$contact2',`contact3`='$contact3',`contact4`='$contact4',`fax`='$fax',`email`='$email',`website`='$website',`creditlimit`='$creditlimit',`taxid`='$taxid',`priceclass`='$priceclass',`taxclass`='$taxclass',`RequirePO`='$requirepo',`retail`='$retail',`commercial`='$commercial',`vendor`='$vendor',`nationalaccount`='$nationalaccount',`affiliate`='$affiliate',`flag`='$flag',`statement`='$statement',`allowcod`='$allowcod',`comment`='$comment',`lastactivedate`='$currentday2' WHERE `accountid` = :accountid";
//echo $sql1d;
$sth1 = $pdocxn->prepare($sql1a);

$sth1->bindParam(':firstname',$fname);
$sth1->bindParam(':lastname',$lname);
$sth1->bindParam(':fullname',$fullname);
$sth1->bindParam(':address',$address);
$sth1->bindParam(':address2',$address2);
$sth1->bindParam(':city',$city);
$sth1->bindParam(':state',$state);
$sth1->bindParam(':zip',$zip);
$sth1->bindParam(':phone1',$phone1);
$sth1->bindParam(':sphone1',$sphone1);
$sth1->bindParam(':phone2',$phone2);
$sth1->bindParam(':sphone2',$sphone2);
$sth1->bindParam(':phone3',$phone3);
$sth1->bindParam(':sphone3',$sphone3);
$sth1->bindParam(':phone4',$phone4);
$sth1->bindParam(':sphone4',$sphone4);
$sth1->bindParam(':contact1',$contact1);
$sth1->bindParam(':contact2',$contact2);
$sth1->bindParam(':contact3',$contact3);
$sth1->bindParam(':contact4',$contact4);
$sth1->bindParam(':fax',$fax);
$sth1->bindParam(':email',$email);
$sth1->bindParam(':website',$website);
$sth1->bindParam(':creditlimit',$creditlimit);
$sth1->bindParam(':taxid',$taxid);
$sth1->bindParam(':priceclass',$priceclass);
$sth1->bindParam(':taxclass',$taxclass);
$sth1->bindParam(':requirepo',$requirepo);
$sth1->bindParam(':retail',$retail);
$sth1->bindParam(':commercial',$commercial);
$sth1->bindParam(':vendor',$vendor);
$sth1->bindParam(':nationalaccount',$nationalaccount);
$sth1->bindParam(':affiliate',$affiliate);
$sth1->bindParam(':flag',$flag);
$sth1->bindParam(':statement',$statement);
$sth1->bindParam(':allowcod',$allowcod);
$sth1->bindParam(':comment',$comment);
$sth1->bindParam(':lastactiveday',$currentday2);
$sth1->bindParam(':accountid',$accountid);
$sth1->execute()or die(print_r($sth1->errorInfo(), true));

if($invoiceid > '0')
{
$header = "Location: invoice.php?invoiceid=".$invoiceid;
header($header);
exit();
}
if($appointmentid > '0')
{
$header = "Location: appointment.php?invoiceid=".$appointmentid;
header($header);
exit();
}
$header = "Location: editaccount.php?accountid=".$accountid."&confirm=1";
header($header);
}
else{
if(isset($_GET['accountid']))
{
$accountid = $_GET['accountid'];
}
if(isset($_GET['confirm']))
{
$confirm = $_GET['confirm'];
$msg = "<p class=\"warningfont\">Account was updated</p>";
}else{
	$msg = '';
}
if(isset($_GET['appointmentid']))
{
$appointmentid = $_GET['appointmentid'];
}
if(isset($_GET['invoiceid']))
{
$invoiceid = $_GET['invoiceid'];
}

$sql1 = "SELECT * FROM `accounts` WHERE `accountid` = :accountid";
$sth1 = $pdocxn->prepare($sql1);
$sth1->bindParam(':accountid',$accountid);
$sth1->execute();
	while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$accountid = $row1['accountid'];
	$databasefname = $row1['firstname'];
	$firstname = stripslashes($databasefname);
	$databaselname = $row1['lastname'];
	$lastname = stripslashes($databaselname);
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
	$nationalaccount = $row1['nationalaccount'];
	$requirepo = $row1['requirepo'];
	$accounttype = $row1['accounttype'];
	$flag = $row1['flag'];
	$comment = $row1['comment'];
	$insertdate = $row1['insertdate'];
	$lastactivedate = $row1['lastactivedate'];
	$fullname = $fname." ".$lname;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title><?php echo $title; ?></title>
<link rel="stylesheet" type="text/css" href="style/globalstyle.css" >
<link rel="stylesheet" type="text/css" href="style/accountstyle.css" >
<script type="text/javascript" src="scripts/globaljs.js"></script>
<script type="text/javascript" src="scripts/jquery-1.9.1.js"></script>
  <script type="text/javascript">
  function capitalize(textboxid, str) {
      // string with alteast one character
      if (str && str.length >= 1)
      {       
          var firstChar = str.charAt(0);
          var remainingStr = str.slice(1);
          str = firstChar.toUpperCase() + remainingStr;
      }
      document.getElementById(textboxid).value = str;
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
<?php
if(isset($_POST['newaccount']))
{
?>
        <div id="content">
<form name="account" action="account.php" method="post">
        	<table class="searchtable"><tr><th>Account was added successfully:</th></tr>
<tr><td colspan="4"><input type="hidden" name="search" value="1"><input type="hidden" name="newaccount" value="1"><input type="hidden" name="accountid" value="<?php echo $newaccountid; ?>"><input type="submit" name="submit" class="btn-style" value="Add New Vehicle"></td></tr>
        	</table>
        </form></div>
<?php
}
else{
?>
<?php
echo $msg;
?>
        <div id="content"><form name="account" action="editaccount.php" method="post">
        	<table class="searchtable"><tr><th>Last Name:</th><td><input type="text" name="lastname" id="lastname" autocomplete="off" onkeyup="javascript:capitalize(this.id, this.value);" value="<?php echo $lastname; ?>"></td><th>First Name:</th><td><input type="text" name="firstname" id="firstname" autocomplete="off" onkeyup="javascript:capitalize(this.id, this.value);" value="<?php echo $firstname; ?>"></td></tr>
<tr><th>Phone 1:</th><td><input type="text" name="phone1" autocomplete="off" value="<?php echo $phone1; ?>"></td><th>Contact:</th><td><input type="text" name="contact1" id="contact1" autocomplete="off" onkeyup="javascript:capitalize(this.id, this.value);" value="<?php echo $contact1; ?>"></td></tr>
<tr><th>Phone 2:</th><td><input type="text" name="phone2" autocomplete="off" value="<?php echo $phone2; ?>"></td><th>Contact:</th><td><input type="text" name="contact2" id="contact2" autocomplete="off" onkeyup="javascript:capitalize(this.id, this.value);" value="<?php echo $contact2; ?>"></td></tr>
<tr><th>Phone 3:</th><td><input type="text" name="phone3" autocomplete="off" value="<?php echo $phone3; ?>"></td><th>Contact:</th><td><input type="text" name="contact3" id="contact3" autocomplete="off" onkeyup="javascript:capitalize(this.id, this.value);" value="<?php echo $contact3; ?>"></td></tr>
<tr><th>Phone 4:</th><td><input type="text" name="phone4" autocomplete="off" value="<?php echo $phone4; ?>"></td><th>Contact:</th><td><input type="text" name="contact4" id="contact4" autocomplete="off" onkeyup="javascript:capitalize(this.id, this.value);" value="<?php echo $contact4; ?>"></td></tr>
<tr><th>Address:</th><td><input type="text" name="address" autocomplete="off" value="<?php echo $address; ?>"></td><th>Address 2:</th><td><input type="text" name="address2" autocomplete="off" value="<?php echo $address2; ?>"></td></tr>
<tr><th>City:</th><td><input type="text" name="city" id="city" autocomplete="off" onkeyup="javascript:capitalize(this.id, this.value);" value="<?php echo $city; ?>"></td><th>State:</th><td><input type="text" name="state" autocomplete="off" value="<?php echo $state; ?>"></td><th>Zip:</th><td><input type="text" name="zip" autocomplete="off" value="<?php echo $zip; ?>"></td></tr>
<tr><th>Fax:</th><td><input type="text" name="fax" autocomplete="off" value="<?php echo $fax; ?>"></td><th>email:</th><td><input type="text" name="email" autocomplete="off" value="<?php echo $email; ?>"></td></tr>
<tr><th>Credit Limit:</th><td><input type="text" name="creditlimit" autocomplete="off" value="<?php echo $creditlimit; ?>"></td><th>Tax id:</th><td><input type="text" name="taxid" autocomplete="off" value="<?php echo $taxid; ?>"></td></tr>
<tr><th>Price Class:</th><td><select name="priceclass"><option value="1">Retail</option><option value="2">Dealer</option></select></td><th>Tax Class:</th><td><select name="taxclass"><option value="1">Retail</option><option value="2">Non-Taxable</option><option value="3">Farm</option></select></td></tr>
<?php
if($nationalaccount == '1')
{
	echo "<tr><th>National Account:</th><td><select name=\"nationalaccount\"><option value=\"1\">Yes</option><option value=\"0\">No</option></select></td></tr>";
}else{
echo "<tr><th>National Account:</th><td><select name=\"nationalaccount\"><option value=\"0\">No</option><option value=\"1\">Yes</option></select></td></tr>";
}?>
<tr><th>Account Type:</th><td><select name="accounttype">

<?php
$sql1 = "SELECT `type`,`code` FROM `accounts_type` WHERE `code` = :accounttype";
$sth1 = $pdocxn->prepare($sql1);
$sth1->bindParam(':accounttype',$accounttype);
$sth1->execute();
	while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
$typename = $row1['type'];
$code = $row1['code'];
echo '<option value="'.$code.'">'.$typename.'</option>';
}
$sql1 = "SELECT `type`,`code` FROM `accounts_type` WHERE `inactive` IS NULL ORDER BY `id` DESC";
$sth1 = $pdocxn->prepare($sql1);
$sth1->execute();
	while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
$typename = $row1['type'];
$code = $row1['code'];
echo '<option value="'.$code.'">'.$typename.'</option>';
}
?>
</select></td><th>Require PO:</th><td><select name="requirepo"><option value="0">No</option><option value="1">Yes</option></select></td></tr>
<tr><th>Account Type:</th><td><input type="text" name="accounttype" autocomplete="off"></td><th>Require PO:</th><td><select name="requirepo"><option value="0">No</option><option value="1">Yes</option></select></td></tr>

<tr><th>Comment:</th><td><input type="text" name="comment" autocomplete="off"></td></tr>
<tr><th>Statements:</th><td><select name="statement"><option value="1">Mail Statement</option><option value="2">email Statement</option><option value="3">Don't Send Statement</option></select></td><th>Statement email:</th><td><input type="text" name="statementemail" autocomplete="off"></td></tr>
<tr><td colspan="4"><input type="hidden" name="editaccount" value="1"><input type="hidden" name="accountid" value="<?php echo $accountid; ?>"><?php if($appointmentid > '1'){?><input type="hidden" name="appointmentid" value="<?php echo $appointmentid; ?>"><?php } ?><?php if($invoiceid > '1'){?><input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>"><?php } ?><input type="submit" name="submit" class="btn-style" value="Submit"></td></tr>
        	</table>
        </form></div>
<?php
}
?>
</body>
</html>
<?php
}
?>
