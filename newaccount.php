<?php
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Add new Account';
$linkpage = 'newaccount.php';

session_start();
date_default_timezone_set('America/Chicago');
$currentdate = date('Y-n-j H:i:s');
$currentday = date('Y-n-j');
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
else{
	$invoiceid = '0';
}
if(isset($_GET['schedule'])&&$_GET['schedule']>'0')
{
$appointmentid = $_GET['invoiceid'];
}else{
	$appointmentid = '0';
}
if(isset($_GET['invoice']))
{
$invoicetype = $_GET['invoice'];
}
if(isset($_GET['lname']))
{
$lname = $_GET['lname'];
}
else{
	$lname = '0';
}
if(isset($_GET['fname']))
{
$fname = $_GET['fname'];
}
else{
	$fname = '0';
}
if(isset($_POST['newaccount']))
{
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
	$sphone1=ereg_replace('[^0-9]', '', $phone1);
	$sphone2=ereg_replace('[^0-9]', '', $phone2);
	$sphone3=ereg_replace('[^0-9]', '', $phone3);
	$sphone4=ereg_replace('[^0-9]', '', $phone4);
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
$commercial =  '0';
$distributor =  '0';
$nationalaccount =  '0';
$affiliate =  '0';
$oldtype = '1';

$sql1 = "INSERT INTO `accounts`(`firstname`,`lastname`,`fullname`,`address`,`address2`,`city`,`state`,`zip`,`phone1`,`sphone1`,`phone2`,`sphone2`,`phone3`,`sphone3`,`phone4`,`sphone4`,`contact1`,`contact2`,`contact3`,`contact4`,`fax`,`email`,`website`,`creditlimit`,`taxid`,`priceclass`,`taxclass`,`RequirePO`,`oldtype`,`flag`,`statement`,`allowcod`,`comment`,`insertdate`,`lastactivedate`) VALUES (:firstname,:lastname,:fullname,:address,:address2,:city,:state,:zip,:phone1,:sphone1,:phone2,:sphone2,:phone3,:sphone3,:phone4,:sphone4,:contact1,:contact2,:contact3,:contact4,:fax,:email,:website,:creditlimit,:taxid,:priceclass,:taxclass,:RequirePO,:oldtype,:flag,:statement,:allowcod,:comment,:insertdate,:lastactivedate)";

$sth1 = $pdocxn->prepare($sql1);
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
$sth1->bindParam(':RequirePO',$RequirePO);
$sth1->bindParam(':oldtype',$oldtype);
$sth1->bindParam(':flag',$flag);
$sth1->bindParam(':statement',$statement);
$sth1->bindParam(':allowcod',$allowcod);
$sth1->bindParam(':comment',$comment);
$sth1->bindParam(':insertdate',$currentday);
$sth1->bindParam(':lastactivedate',$currentday);
$sth1->execute();

$sqli2 = $sqlicxn->query('SELECT `acctid` FROM `accounts` ORDER BY `acctid` DESC LIMIT 1');
while ($row2 = $sqli2->fetch_assoc()) {
$newaccountid = ($row2['acctid']);
}
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
        <div id="content"><form name="addvehicle" action="vehicles.php" method="POST">
        	<table class="searchtable"><tr><th>Account was added successfully:</th></tr>
<tr><td colspan="4"><input type="hidden" name="search" value="1"><input type="hidden" name="newaccount" value="1"><input type="hidden" name="accountid" value="<?php echo $newaccountid; ?>"><input type="submit" name="submit" class="btn-style" value="Add New Vehicle"></td></tr>
        	</table>
        </form></div>
<?php
}
else{
?>
        <div id="content"><form name="account" action="vehicles.php" method="post">
        	<table class="searchtable"><tr>
<?php
if($lname > '0')
{
echo "<th>Last Name:</th><td><input type=\"text\" name=\"lastname\" id=\"lastname\" autocomplete=\"off\" onkeyup=\"javascript:capitalize(this.id, this.value);\" value=\"".$lname."\"></td>";
}else{
?>	
<th>Last Name:</th><td><input type="text" name="lastname" id="lastname" autocomplete="off" onkeyup="javascript:capitalize(this.id, this.value);" placeholder="Last Name"></td>
<?php
}
if($lname > '0')
{
echo "<th>First Name:</th><td><input type=\"text\" name=\"firstname\" id=\"firstname\" autocomplete=\"off\" onkeyup=\"javascript:capitalize(this.id, this.value);\" value=\"".$fname."\"></td></tr>";
}else{
	?>
<th>First Name:</th><td><input type="text" name="firstname" id="firstname" autocomplete="off" onkeyup="javascript:capitalize(this.id, this.value);" placeholder="First Name"></td></tr>
<?php
}
?>
<tr><th>Phone 1:</th><td><input type="text" name="phone1" autocomplete="off" placeholder="Phone"></td><th>Contact:</th><td><input type="text" name="contact1" id="contact1" autocomplete="off" onkeyup="javascript:capitalize(this.id, this.value);" placeholder="Contact"></td></tr>
<tr><th>Phone 2:</th><td><input type="text" name="phone2" autocomplete="off" placeholder="Phone"></td><th>Contact:</th><td><input type="text" name="contact2" id="contact2" autocomplete="off" onkeyup="javascript:capitalize(this.id, this.value);" placeholder="Contact"></td></tr>
<tr><th>Phone 3:</th><td><input type="text" name="phone3" autocomplete="off" placeholder="Phone"></td><th>Contact:</th><td><input type="text" name="contact3" id="contact3" autocomplete="off" onkeyup="javascript:capitalize(this.id, this.value);" placeholder="Contact"></td></tr>
<tr><th>Phone 4:</th><td><input type="text" name="phone4" autocomplete="off" placeholder="Phone"></td><th>Contact:</th><td><input type="text" name="contact4" id="contact4" autocomplete="off" onkeyup="javascript:capitalize(this.id, this.value);" placeholder="Contact"></td></tr>
<tr><th>Address:</th><td><input type="text" name="address" autocomplete="off" placeholder="Address"></td><th>Address 2:</th><td><input type="text" name="address2" autocomplete="off" placeholder="Address"></td></tr>
<tr><th>City:</th><td><input type="text" name="city" id="city" autocomplete="off" onkeyup="javascript:capitalize(this.id, this.value);" placeholder="City"></td><th>State:</th><td><input type="text" name="state" autocomplete="off" placeholder="State"></td><th>Zip:</th><td><input type="text" name="zip" autocomplete="off" placeholder="Zip"></td></tr>
<tr><th>Fax:</th><td><input type="text" name="fax" autocomplete="off" placeholder="Fax"></td><th>email:</th><td><input type="text" name="email" autocomplete="off" placeholder="email"></td></tr>
<tr><th>Credit Limit:</th><td><input type="text" name="creditlimit" autocomplete="off" placeholder="Credit Limit"></td><th>Tax id:</th><td><input type="text" name="taxid" autocomplete="off" placeholder="Tax ID"></td></tr>
<tr><th>Price Class:</th><td><select name="priceclass"><option value="1">Retail</option><option value="2">Dealer</option></select></td><th>Tax Class:</th><td><select name="taxclass"><option value="1">Retail</option><option value="2">Non-Taxable</option><option value="3">Farm</option></select></td></tr>
<tr><th>National Account:</th><td><select name="nationalaccount"><option value="0">No</option><option value="1">Yes</option></select></td></tr>
<tr><th>Account Type:</th><td><input type="text" name="accounttype" autocomplete="off"></td><th>Require PO:</th><td><select name="requirepo"><option value="0">No</option><option value="1">Yes</option></select></td></tr>
<tr><th>Comment:</th><td><input type="text" name="comment" autocomplete="off"></td></tr>
<tr><td colspan="4"><input type="hidden" name="newaccount" value="1">
<?php if($invoiceid > '1'){?><input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>"><?php } ?>
<?php if($appointmentid > '1'){?><input type="hidden" name="appointmentid" value="<?php echo $appointmentid; ?>"><?php } ?>
<?php if($invoicetype > '1'){?><input type="hidden" name="invoicetype" value="<?php echo $invoicetype; ?>"><?php } ?>
<input type="submit" name="submit" class="quotebutton" value="Insert"></td></tr>
        	</table>
        </form></div>
<?php
}
?>
</body>
</html>
