<?php
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


if(isset($_POST['editinventory']))
{
	$record = '1';
	$partid = $_POST['partid'];
    $inventorysubtypeid = $_POST['subtypeid'];
    $part_number = $_POST['part_number'];
    $type = $_POST['type'];
    $recordinventory = $_POST['record'];
    $manid = $_POST['newbrand'];
    $model = $_POST['model'];
    $warranty = $_POST['mileage'];
    $width = $_POST['width'];
    $ratio = $_POST['ratio'];
    $rim = $_POST['rim'];
    $sw = $_POST['sidewall'];
    $utgq = $_POST['utgq'];
    $fet = $_POST['fet'];
    $load_index = $_POST['load_index'];
    $speed = $_POST['speed'];
	$ply = $_POST['ply'];
	$lastcost = $_POST['lastcost'];
	$baseamount = $_POST['baseamount'];
    $sellprice = $_POST['sellprice'];
    $traction = $_POST['traction'];
    $tempurature = $_POST['tempurature'];
    $plyid = $_POST['plyid'];
    $treaddepth = $_POST['treaddepth'];


$sql1a = 'INSERT INTO `inventory`(`subtypeid`,`part_number`,`quicksearch`,`model`,`width`,`ratio`,`rim`,`manid`,`fet`,`load_index`,`speed`,`treadwear`,`traction`,`tempurature`,`warranty`,`sidewall`,`ply`,`plyid`,`treaddepth`,`lastactivedate`,`userid`,`active`,`non-stock`,`weight`) VALUES (:subtypeid,:part_number,:quicksearch,:model,:width,:ratio,:rim,:manid,:fet,:load_index,:speed,:treadwear,:traction,:tempurature,:warranty,:sidewall,:ply,:plyid,:treaddepth,:lastactivedate,:userid,:active,:non-stock,:weight)';
$sth1 = $pdocxn->prepare($sql1a);

$sth1->bindParam(':subtypeid',$subtypeid);
$sth1->bindParam(':part_number',$part_number);
$sth1->bindParam(':model',$model);
$sth1->bindParam(':width',$width);
$sth1->bindParam(':ratio',$ratio);
$sth1->bindParam(':rim',$rim);
$sth1->bindParam(':manid',$manid);
$sth1->bindParam(':fet',$fet);
$sth1->bindParam(':load_index',$load_index);
$sth1->bindParam(':speed',$speed);
$sth1->bindParam(':treadwear',$utgq);
$sth1->bindParam(':traction',$traction);
$sth1->bindParam(':tempurature',$tempurature);
$sth1->bindParam(':warranty',$warranty);
$sth1->bindParam(':sidewall',$sidewall);
$sth1->bindParam(':ply',$ply);
$sth1->bindParam(':plyid',$plyid);
$sth1->bindParam(':treaddepth',$treaddepth);
$sth1->bindParam(':lastactivedate',$lastactivedate);
$sth1->execute()or die(print_r($sth1->errorInfo(), true));


$sql2 = "UPDATE `inventory_price` SET `lastcost`=:lastcost,`baseamount`=:baseamount,`price1`=:sellprice  WHERE `partid` = :partid AND `siteid` = :siteid";
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':lastcost',$lastcost);
$sth2->bindParam(':baseamount',$baseamount);
$sth2->bindParam(':sellprice',$sellprice);
$sth2->bindParam(':partid',$partid);
$sth2->bindParam(':siteid',$currentlocationid);
$sth2->execute();
while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
{
$price = $row2['lastcost'];
$baseamount = $row2['baseamount'];
}


$header = "Location: editinventory.php?partid=".$partid."&confirm=1";
header($header);
}
else{

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
echo $msg;

	$typeid = $row1['type'];
	$recordinventory = $row1['record'];
?>
		<div id="content">
		<form name="account" action="insertinventory.php" method="post">
<table class="searchtable">
<tr><td colspan="3">
<tr><th>Part Number:</th><td colspan="2"><input type="text" name="part_number" id="part_number" autocomplete="off" placeholder="part #"></td><tr><th>Model:</th><td colspan="2"><input type="text" name="model" autocomplete="off" placeholder="model"></td>
<th>Manufacturer:</th><td colspan="2">
<?php
echo "<select name=\"newbrand\">";
    echo '<option value="0">Manufacturer</option>';


$sql3 = "SELECT `id`,`brand` FROM `tire_manufacturers` WHERE `inactive` = '0' ORDER BY `brand` ASC";
$query3 = mysqli_query($sqlicxn,$sql3);
while ($row3 = mysqli_fetch_assoc($query3))
	{
	$brandoptions = $row3['brand'];
    $brandid = $row3['id'];
    echo '<option value="'.$brandid.'">'.$brandoptions.'</option>';
	}
echo "</select>";
?>
</td></tr>

<tr><th>Width:</th><td class="left"><input type="text" name="width" autocomplete="off" placeholder="width" class="narrowinput"></td><th>Ratio:</th><td class="left"><input type="text" name="ratio" id="ratio" autocomplete="off" placeholder="ratio" class="narrowinput"></td><th>Rim: </th><td class="left"><input type="text" name="rim" id="rim" autocomplete="off" placeholder="rim" class="narrowinput"></td></tr>
<tr><th>Speed:</th><td class="left"><input type="text" name="speed" autocomplete="off" placeholder="speed" class="narrowinput"></td><th>Load Index:</th><td class="left"><input type="text" name="load_index" id="load_index" autocomplete="off" onkeyup="javascript:capitalize(this.id, this.value);" placeholder="load" class="narrowinput"></td></tr>
<tr><th>Treadwear:</th><td class="left"><input type="text" name="utgq" autocomplete="off" placeholder="treadwear" class="narrowinput"></td><th>Mileage:</th><td class="left"><input type="text" name="mileage" id="mileage" autocomplete="off"  class="narrowinput" placeholder="mileage"></td></tr>
<tr><th>Sidewall:</th><td>

<?php
echo "<select name=\"sidewall\">";
$sql2 = "SELECT `id`,`code` FROM `inventory_sidewall` WHERE `id` = '$sw'";
$query2 = mysqli_query($sqlicxn,$sql2);
while ($row2 = mysqli_fetch_assoc($query2))
	{
    $selectedsw = $row2['code'];
    $selectedswid = $row2['id'];
    echo '<option value="'.$selectedswid.'">'.$selectedsw.'</option>';
	}

$sql3 = "SELECT `id`,`code` FROM `inventory_sidewall` ORDER BY `code` ASC";
$query3 = mysqli_query($sqlicxn,$sql3);
while ($row3 = mysqli_fetch_assoc($query3))
	{
	$swoptions = $row3['code'];
    $swid = $row3['id'];
    echo '<option value="'.$swid.'">'.$swoptions.'</option>';
	}
echo "</select>";
?>
</td></tr>
<tr><th>Last Cost:</th><td colspan="2" class="left"><input type="text" name="lastcost" autocomplete="off" value="<?php echo $price; ?>" class="mediuminput"></td><th>Base Amount: </th><td colspan="2" class="left"><input type="text" name="baseamount" autocomplete="off" value="<?php echo $baseamount; ?>" class="mediuminput"></td></tr>
<tr><th>Sell Price:</th><td colspan="2" class="left"><input type="text" name="sellprice" autocomplete="off" value="<?php echo $sellprice; ?>" class="mediuminput"></td><th>FET:</th><td class="left"><input type="text" name="fet" id="fet" autocomplete="off" value="<?php echo $fet; ?>" class="narrowinput"></td></tr>


<tr><td colspan="4"><input type="hidden" name="editinventory" value="1"><input type="hidden" name="partid" value="<?php echo $partid; ?>"><input type="submit" name="submit" class="quotebutton" value="Insert"></td></tr>
        	</table>
        </form></div>

</body>
</html>
<?php
}
?>
