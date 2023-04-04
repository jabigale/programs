<?php
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Edit Inventory';
$linkpage = 'editinventory.php';

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
	$newplyid = $_POST['plyid'];
	$lastcost = $_POST['lastcost'];
	$baseamount = $_POST['baseamount'];
	$sellprice = $_POST['sellprice'];
	$qs = $width.$ratio.$rim;
	$quicksearch = preg_replace('/\D/', '', $qs);

$sql1a = "UPDATE `inventory` SET `part_number`=:part_number,`plyid`=:plyid,`manid`=:manid,`model`=:model,`warranty`=:warranty,`width`=:width,`ratio`=:ratio,`rim`=:rim,`sidewall`=:sidewall,`treadwear`=:treadwear,`fet`=:fet,`load_index`=:load_index,`speed`=:speed,`quicksearch`=:quicksearch WHERE `id` = :partid";
$sth1 = $pdocxn->prepare($sql1a);
//$sth1->bindParam(':subtypeid',$subtypeid);
$sth1->bindParam(':part_number',$part_number);
$sth1->bindParam(':plyid',$newplyid);
$sth1->bindParam(':manid',$manid);
$sth1->bindParam(':model',$model);
$sth1->bindParam(':warranty',$warranty);
$sth1->bindParam(':width',$width);
$sth1->bindParam(':ratio',$ratio);
$sth1->bindParam(':rim',$rim);
$sth1->bindParam(':sidewall',$sidewall);
$sth1->bindParam(':treadwear',$utgq);
$sth1->bindParam(':fet',$fet);
$sth1->bindParam(':load_index',$load_index);
$sth1->bindParam(':speed',$speed);
$sth1->bindParam(':quicksearch',$quicksearch);
$sth1->bindParam(':partid',$partid);
$sth1->execute()or die(print_r($sth1->errorInfo(), true));

$sql2 = "UPDATE `inventory_price` SET `lastcost`=:lastcost,`baseamount`=:baseamount,`price1`=:sellprice,`lastcostdate`=:lastcostdate  WHERE `partid` = :partid AND `siteid` = :siteid";
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':lastcost',$lastcost);
$sth2->bindParam(':baseamount',$baseamount);
$sth2->bindParam(':sellprice',$sellprice);
$sth2->bindParam(':partid',$partid);
$sth2->bindParam(':siteid',$currentlocationid);
$sth2->bindParam(':lastcostdate',$currentday);
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
if(isset($_GET['partid']))
{
	$partid = $_GET['partid'];
}
if(isset($_GET['confirm']))
{
$confirm = $_GET['confirm'];
$msg = "<p class=\"warningfont\">Inventory was updated</p>";
}else{
	$msg = '';
}

$sql1 = 'SELECT * FROM `inventory` WHERE `id` = :partid';
$sth1 = $pdocxn->prepare($sql1);
$sth1->bindParam(':partid',$partid);
$sth1->execute();
if ($sth1->rowCount() > 0)
{
	while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$inventorysubtypeid = $row1['subtypeid'];
	$part_number = $row1['part_number'];
	$type = $row1['type'];
	$recordinventory = $row1['record'];
	$brandid = $row1['manid'];
	$model = $row1['model'];
	$mileage = $row1['warranty'];
	$width = $row1['width'];
	$ratio = $row1['ratio'];
	$rim = $row1['rim'];
	$size = $width."/".$ratio." ".$rim;
	$sw = $row1['sidewall'];
	$utgq = $row1['treadwear'];
	$fet = $row1['fet'];
	$load_index = $row1['load_index'];
	$speed = $row1['speed'];
	$lr = $row1['plyid'];
	$loc1onhand = $row1['loc1_onhand'];
	$loc2onhand = $row1['loc2_onhand'];
	$loc3onhand = $row1['loc3_onhand'];
	$loc4onhand = $row1['loc4_onhand'];
	$loc5onhand = $row1['loc5_onhand'];
	$loc6onhand = $row1['loc6_onhand'];
	$loc7onhand = $row1['loc7_onhand'];
	$loc8onhand = $row1['loc8_onhand'];
	$loc9onhand = $row1['loc9_onhand'];
	$loc10onhand = $row1['loc10_onhand'];
	if($ply > '1')
	{
		$displayply = "(".$ply." ply)";
	}
	$sql2 = "SELECT `id`,`lastcost`,`baseamount`,`price1`,`lastcostdate` FROM `inventory_price` WHERE `partid` = :partid AND `siteid` = :siteid";
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':partid',$partid);
$sth2->bindParam(':siteid',$currentlocationid);
$sth2->execute();
while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
{
$price = $row2['lastcost'];
$baseamount = $row2['baseamount'];
$sellprice = $row2['price1'];
$lastcostdate = $row2['lastcostdate'];
$lastpriceupdate = date('m-d-Y', strtotime($lastcostdate));
}
$sql2= "SELECT `brand` FROM `tire_manufacturers` WHERE `id` = '$brandid'";
$query2 = mysqli_query($sqlicxn,$sql2);
while ($row2 = mysqli_fetch_assoc($query2))
	{
	$brand = $row2['brand'];
	$description = $articleid.", ".$size." ".$brand." ".$model." ".$load_index.$speed." ".$displayply;
	}
	}}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Inventory - Edit</title>
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
echo $msg;
	$typeid = $row1['type'];
	$recordinventory = $row1['record'];
?>
		<div id="content"><a href="tirehistory.php?partid=<?php echo $partid; ?>"><button name="parthistory" class="quotebutton">Inventory History</button></a>
		<a href="adjustinventory.php?partid=<?php echo $partid; ?>"><button name="parthistory" class="quotebutton">Adjust inventory qty</button></a>
		<form name="account" action="editinventory.php" method="post">
<table class="searchtable">
<tr><td colspan="3">
<tr><th>Part Number:</th><td colspan="2"><input type="text" name="part_number" id="part_number" autocomplete="off" value="<?php echo $part_number; ?>"></td><tr><th>Model:</th><td colspan="2"><input type="text" name="model" autocomplete="off" value="<?php echo $model; ?>"></td>
<th>Manufacturer:</th><td colspan="2">
<?php
echo "<select name=\"newbrand\">";
    $sql2 = "SELECT `id`,`brand` FROM `tire_manufacturers` WHERE `id` = '$brandid'";
$query2 = mysqli_query($sqlicxn,$sql2);
while ($row2 = mysqli_fetch_assoc($query2))
	{
    $selectedbrand = $row2['brand'];
    $selectedbrandid = $row2['id'];
    echo '<option value="'.$selectedbrandid.'">'.$selectedbrand.'</option>';
	}

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

<tr><th>Width:</th><td class="left"><input type="text" name="width" autocomplete="off" value="<?php echo $width; ?>" class="narrowinput"></td><th>Ratio:</th><td class="left"><input type="text" name="ratio" id="ratio" autocomplete="off" value="<?php echo $ratio; ?>" class="narrowinput"></td><td>Rim: </td><td class="left"><input type="text" name="rim" id="rim" autocomplete="off" value="<?php echo $rim; ?>" class="narrowinput"></td></tr>
<tr><th>Speed:</th><td class="left"><input type="text" name="speed" autocomplete="off" value="<?php echo $speed; ?>" class="narrowinput"></td><th>Load Index:</th><td class="left"><input type="text" name="load_index" id="load_index" autocomplete="off" onkeyup="javascript:capitalize(this.id, this.value);" value="<?php echo $load_index; ?>" class="narrowinput"></td></tr>
<tr><th>Treadwear:</th><td class="left"><input type="text" name="utgq" autocomplete="off" value="<?php echo $utgq; ?>" class="narrowinput"></td><th>Mileage:</th><td class="left"><input type="text" name="mileage" id="mileage" autocomplete="off"  class="narrowinput" value="<?php echo $mileage; ?>"></td></tr>
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
</td><th>Load Range:</th><td>
<?php
echo "<select name=\"plyid\">";
$sql2 = "SELECT `id`,`ply`,`range` FROM `inventory_loadrange` WHERE `id` = '$lr'";
$query2 = mysqli_query($sqlicxn,$sql2);
while ($row2 = mysqli_fetch_assoc($query2))
	{
    $selectedlr = $row2['range'];
	$selectedlrid = $row2['id'];
	$selectedlrrange = $row2['ply'];
	$displaylr1 = $selectedlr." (".$selectedlrrange." ply)";

    echo '<option value="'.$selectedlrid.'">'.$displaylr1.'</option>';
	}

$sql3 = "SELECT `id`,`ply`,`range` FROM `inventory_loadrange` ORDER BY `ply` ASC";
$query3 = mysqli_query($sqlicxn,$sql3);
while ($row3 = mysqli_fetch_assoc($query3))
	{
		$lr = $row3['range'];
		$lrid = $row3['id'];
		$lrrange = $row3['ply'];
		$displaylr = $lr." (".$lrrange." ply)";
    echo '<option value="'.$lrid.'">'.$displaylr.'</option>';
	}
echo "</select>";
?>
</td></tr>
<tr><th>Last Cost:</th><td colspan="2" class="left"><input type="text" name="lastcost" autocomplete="off" value="<?php echo $price; ?>" class="mediuminput"></td><th>Base Amount: </th><td colspan="2" class="left"><input type="text" name="baseamount" autocomplete="off" value="<?php echo $baseamount; ?>" class="mediuminput"></td></tr>
<tr><th>Sell Price:</th><td colspan="2" class="left"><input type="text" name="sellprice" autocomplete="off" value="<?php echo $sellprice; ?>" class="mediuminput"></td><th>FET:</th><td class="left"><input type="text" name="fet" id="fet" autocomplete="off" value="<?php echo $fet; ?>" class="narrowinput"></td></tr>
<tr><th>Last Price Update:</th><th><?php echo $lastpriceupdate; ?></th></tr>

<tr><td colspan="4"><input type="hidden" name="editinventory" value="1"><input type="hidden" name="partid" value="<?php echo $partid; ?>"><input type="submit" name="submit" class="quotebutton" value="Update"></td></tr>
        	</table>
        </form></div>

</body>
</html>
<?php
}
?>
