<?php
//get the file and convert everything to variables
//Search by partnumber
//Search by quicksearch
//default some values for the sql
//Search by brand
//highlight javascript
//display tire quotes

//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Inventory';
$linkpage = 'inventory.php';
$changecustomer = '0';
$a = '5';
$b = 'a';

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
	$header = 'Location: index2.php?refpage='.$pagenum.'';
	header($header);
}
	if(isset($_GET['transfer']))
	{
	$transfer = '1';
	}else{
	$transfer = '0';
	}
	if(isset($_POST['ci']))
	{	
	$ci = $_POST['ci'];
	}else{
	if(isset($_GET['ci']))
	{
	$ci = $_GET['ci'];
	$cilink = "?ci=".$ci;
}else{
	$cilink = "?ci=0";
	$ci = '0';
	}}
	if(isset($_POST['accountid']))
	{
		$accountid = $_POST['accountid'];
	}else{
	if(isset($_GET['accountid']))
	{
	$accountid = $_GET['accountid'];
	$acctlink = "&accountid=".$accountid;
	}else{
	$accountid = '0';
	$acctlink = '';
	}}
	if($_GET['qsr'])
	{
		$qsr = $_GET['qsr'];
	}else{
		$qsr = '0';
	}
	if($accountid > '0')
	{
		$sql1 = 'SELECT `lastname`,`firstname` FROM `accounts` WHERE `accountid` = :accountid';
	$sth1 = $pdocxn->prepare($sql1);
	$sth1->bindParam(':accountid',$accountid);
	$sth1->execute();
		while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
		{
		$lastname = $row1['lastname'];
		$firstname = $row1['firstname'];
		$customername = $firstname." ".$lastname;
		}
	}else{
		$customername = "";
	}
	if(isset($_GET['receive']))
	{
	$receive = '1';
	}else{
	$receive = '0';
	}
	if(isset($_GET['invoiceid']))
	{
	$invoiceid = $_GET['invoiceid'];
	$invlink = "&invoiceid=".$invoiceid;
	}
	else{
	$invoiceid = '0';
	$invlink = '';
	}
	if(isset($_GET['scheduleid']))
	{
	$scheduleid = $_GET['scheduleid'];
	$schedlink = "&scheduleid=".$scheduleid;
}
	else{
	$scheduleid = '0';
	$schedlink = '';
	}
	$selectedquicksearch = '0';
	$selectedbrand = '0';
	$selectedpartnumber = '0';
	$selectedwidth = '0';
	$selectedratio = '0';
	$selectedrim = '0';
	$selectedply = '0';
	$selectedtraction = '0';
	$selectedtreadwear = '0';
	$selectedtemperature = '0';
	$selectedtype = '0';
	$selectedspeed = '0';
	$installedprice = "17.95";
	$installed2 = $installed1 * 2;
	$installed3 = $installed3;
	$installed4 = $installed4;
	if(isset($_POST['submit']))
	{
	$active = '1';
		$selectedquicksearch = $_POST['quicksearch'];
		$searchquicksearch = $selectedquicksearch."%";
		$selectedbrand = $_POST['brand'];
		$selectedpartnumber = $_POST['partnumber'];
		$selectedwidth = $_POST['width'];
		$selectedratio = $_POST['ratio'];
		$selectedrim = $_POST['rim'];
		$selectedply = $_POST['ply'];
		$selectedtraction = $_POST['traction'];
		$selectedtreadwear = $_POST['treadwear'];
		$selectedtemperature = $_POST['temperature'];
		$selectedtype = $_POST['type'];
		$selectedspeed = $_POST['speed'];
		if(isset($_POST['onhand']))
		{
			$selectedonhand = '1';
		}
		else {
			$selectedonhand = '0';
		}
		if(isset($_POST['obligated']))
		{
			$selectedobligated = '1';
		}
		else {
			$selectedobligated = '0';
		}
		if(isset($_POST['order']))
		{
			$selectedorder = '1';
		}
		else {
			$selectedorder = '0';
		}

	?>
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
	<html>
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
	<link rel="stylesheet" type="text/css" href="style/inventorystyle.css" >
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
	if($ci == '1')
	{
	echo "<div id=\"header\"><font color=\"red\">Customer Interaction for ".$customername."</font><br /><form name=\"cancel\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"submit\" name=\"submit\" class=\"cancel\" Value=\"Cancel\"></form></div>";
	}else{
	if($invoiceid > '0' OR $scheduleid > '0')
	{
		if($invoiceid > '0')
		{
	echo "<div id=\"header\"><font color=\"red\">Adding Tires to an Invoice</font><br /><form name=\"cancel\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"submit\" name=\"submit\" class=\"cancel\" Value=\"Cancel\"></form></div>";
	}else{
	echo "<div id=\"header\"><font color=\"red\">Adding Tires to an Appointment</font><br /><a href=\"appointment.php?id=".$scheduleid."\" class=\"no-decoration\"><input type=\"button\" name=\"submit\" class=\"cancel\" Value=\"Cancel\"></a></div>";
	}}else{
	if($currentlocationid == '1')
	{
	echo "<div id=\"header\">".$headernavigation."</div>";
	}
	else{
	echo "<div id=\"header2\">".$headernavigation2."</div>";
	}}}
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
	</select></div><input type="hidden" name="form" value="1"></td><!--<td><a href="inventoryvehicle.php">Search Tire Size by Vehicle</a></td>--><td><a href="inventory.php<?php echo $cilink.$acctlink.$invlink.$schedlink; ?>&qsr=<?php echo $selectedquicksearch; ?>">Change Search</a></td><td><a href="inventory.php<?php echo $cilink.$acctlink.$invlink.$schedlink; ?>">Reset</a></td><?php if($invoiceid > '0') { echo "<td><font color=\"red\">Adding Tires to Invoice</font><br /><form name=\"cancel\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"submit\" name=\"submit\" class=\"cancel\" Value=\"Cancel\"></form></td>";}?><td><form name="taxclass" action="inventory" method="post"><select name="taxclass"><option value="1">Taxable</option><option value="0">Non-Taxable</option></select></form></td></tr>
	<?php
	if($ci == '1')
	{
	?>
	<form name="qpi" action="inventory.php" method="GET"><tr><td class="vmiddle">Include w/ Quick Print:</td>
	<td class="vmiddle"><label for="4w">4 wheel Alignment <input type="checkbox" name="4align" id="4w" value="1"></label></td>
	<td class="vmiddle"><label for="2w">2 wheel Alignment <input type="checkbox" name="2align" id="2w" value="1"></label></td>
	<td class="vmiddle"><input type="hidden" name="ci" value="1"><input type="hidden" name="accountid" value="<?php echo $accountid; ?>"><input type="submit" name="submit" value="update" class="smallquotebutton"></td></tr>
	</form>
	<?php
	}
	   ?></table></form></div>
			<div id="content">
			<center>Click table headers to sort</center>
				<div id="left"><form name="inventory" action="inventory.php<?php echo $cilink.$acctlink.$invlink.$schedlink; ?>" method="post">
	<table id="highlightTable" class="blueTable">
	<thead>
	<tr>

	<?php
	if($ci == '1')
	{
	}else{
	?>
	<th>edit</th>
	<?php
	}
	?>
	<th id="part">Part #</th>
	<th id="size">Size</th>
	<th id="ply">Ply</th>
	<th id="brand">Brand</th>
	<th id="model">Model</th>
	<th id="price">Price</th>
	<th id="sidewall">Sidewall</th>
	<th id="mileage">Mileage</th>
	<th id="type">Type</th>
	<th id="speed">Speed</th>
	<th id="onhand">On<br>Hand</th>
	<th id="otherlocations">Other<br>Stores</th>
<?php
	if($ci == '1')
	{
		echo "<th id=\"qprint\">Quick Print</th>";
	}
?>
	</tr>
	</thead>
	<tbody>
	<?php
	if($currentlocationid=='1')
	{
		$loconhand = "loc1_onhand";
		$locdate = "loc1date";
	}
	else if($currentlocationid=='2')
	{
		$loconhand = "loc2_onhand";
		$locdate = "loc2date";
	}
	if($currentlocationid=='3')
	{
		$loconhand = "loc3_onhand";
		$locdate = "loc3date";
	}
	if($currentlocationid=='4')
	{
		$loconhand = "loc4_onhand";
		$locdate = "loc4date";
	}
	if($currentlocationid=='5')
	{
		$loconhand = "loc5_onhand";
		$locdate = "loc5date";
	}
	if($currentlocationid=='6')
	{
		$loconhand = "loc6_onhand";
		$locdate = "loc6date";
	}
	if($currentlocationid=='7')
	{
		$loconhand = "loc7_onhand";
		$locdate = "loc7date";
	}
	if($currentlocationid=='8')
	{
		$loconhand = "loc8_onhand";
		$locdate = "loc8date";
	}
	if($currentlocationid=='9')
	{
		$loconhand = "loc9_onhand";
		$locdate = "loc9date";
	}
	if($currentlocationid=='10')
	{
		$loconhand = "loc10_onhand";
		$locdate = "loc10date";
	}
	//default some values for the sql
	$sbi = '1';
	$tri = '1';
	$startsql = '1';
	$partnumbersql = "";
	$quicksearchsql =  "";
	$selectonhandsql = "";
	$activesql = "";
	$qs = '0';

	//Search by partnumber
	if($selectedpartnumber > '0')
	{
		$sql1 = 'SELECT * FROM `inventory` WHERE `part_number` LIKE :searchpartnumber AND `active` = \'1\'';
		$partnumbersql = " OR `part_number` LIKE :searchpartnumber ";
		$qs = '1';
		$startsql = '0';
	}
	else {
	//Search by quicksearch
	if($selectedquicksearch > '1')
		{
		if($selectedonhand == '1')
		{

			$sql1 = 'SELECT `id`,`manid`,`model`,`warranty`,`width`,`ratio`,`rim`,`part_number`,`sidewall`,`treadwear`,`fet`,`load_index`,`ply`,`plyid`,`loc1_onhand`,`loc2_onhand` FROM `inventory` WHERE `quicksearch` LIKE :quicksearch AND `'.$loconhand.'` > \'0\'';
			$quicksearchsql =  " OR `quicksearch` LIKE :quicksearch AND `'.$loconhand.'` > \'0\'";
			$qs = '1';
			$startsql = '0';
		}else{
			$sql1 = 'SELECT `id`,`manid`,`model`,`warranty`,`width`,`ratio`,`rim`,`part_number`,`sidewall`,`treadwear`,`fet`,`load_index`,`ply`,`plyid`,`loc1_onhand`,`loc2_onhand` FROM `inventory` WHERE `quicksearch` LIKE :quicksearch';
			$sql1 = 'SELECT * FROM `inventory` WHERE `quicksearch` LIKE :quicksearch';
			$quicksearchsql =  " OR `quicksearch` LIKE :quicksearch";
			$qs = '1';
			$startsql = '0';
		}
		}
	else
	{
		if($selectedonhand == '1')
		{
		$sql1 = 'SELECT * FROM `inventory` WHERE `'.$loconhand.'` > \'0\'';
		if($qs == '0')
		{
			$selectonhandsql = ' OR `'.$loconhand.'` > \'0\'';
			$qs = '1';
		}else{
		$selectonhandsql = ' AND `'.$loconhand.'` > \'0\'';
		}
		$startsql = '0';
		}else{
		$sql1 = "SELECT * FROM `inventory` WHERE ";
		$startsql = '1';
	}}
	if($active == '1')
	{
	if($startsql =='0')
	{
	$sql1 .= " AND `active` = '1'";
	if($qs == '1')
	{
	$activesql = " AND `active` = '1'";
	}else{
		$activesql = " OR `active` = '1'";
		$qs = '1';
	}}
	else{
	$sql1 .= "`active` = '1'";
	$activesql = " AND `active` = '1'";
		$startsql = '0';
	}
	}
	else
	{
	$startsql ='1';
	}
	}
	//Search by brand
	foreach($_POST['brand'] as $selectedbrand2){
	if($sbi =='1')
	{
		if($startsql == '0')
		{
	$sql1 .= " AND `manid` = ".$selectedbrand2."";
		}
		else {
			$sql1 .= " `manid` = ".$selectedbrand2."";
		}
	}
	else{
		if($qs == '1')
		{
			$sql1 .= $partnumbersql.$quicksearchsql.$selectonhandsql.$activesql." AND `manid` = ".$selectedbrand2."";
	}else{
		$sql1 .= " OR `manid` = ".$selectedbrand2."";
	}}
		$sbi ++;
	}
	$sth1 = $pdocxn->prepare($sql1);
	if($selectedquicksearch > '0')
	{
	$sth1->bindParam(':quicksearch',$searchquicksearch);
	}
	if($selectedpartnumber > '0')
	{
	$searchpartnumber = "%".$selectedpartnumber."%";
	$sth1->bindParam(':searchpartnumber',$searchpartnumber);
	}
	$sth1->execute();
	if ($sth1->rowCount() > 0)
	{
		while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
		{
		$partid = $row1['id'];
		$parttype = $row1['subtypeid'];
		$brandid = $row1['manid'];
		$model = $row1['model'];
		$mileage = $row1['warranty'];
		$width = $row1['width'];
		$ratio = $row1['ratio'];
		$rim = $row1['rim'];
		$size = $width."/".$ratio.$rim;
		$partnumber = $row1['part_number'];
		$sw = $row1['sidewall'];
		$utgq = $row1['treadwear'];
		$fet = $row1['fet'];
		$load_index = $row1['load_index'];
		$ply = $row1['ply'];
		$plyid = $row1['plyid'];
		$loc1onhand = $row1['loc1_onhand'];
		$loc2onhand = $row1['loc2_onhand'];
		//$loc3onhand = $row1['loc3_onhand'];
		//$loc4onhand = $row1['loc4_onhand'];
		//$loc5onhand = $row1['loc5_onhand'];
		//$loc6onhand = $row1['loc6_onhand'];
		//$loc7onhand = $row1['loc7_onhand'];
		//$loc8onhand = $row1['loc8_onhand'];
		//$loc9onhand = $row1['loc9_onhand'];
		//$loc10onhand = $row1['loc10_onhand'];
		$otherloconhand = '0';
		if($loc1onhand < '0')
		{
			$dloc1onhand = '0';
		}else
	{
		$dloc1onhand = $loc1onhand;
	}
	if($loc2onhand < '0')
	{
		$dloc2onhand = '0';
	}else
{
	$dloc2onhand = $loc2onhand;
}
if($loc3onhand < '0')
{
	$dloc3onhand = '0';
}else
{
$dloc3onhand = $loc3onhand;
}
if($loc4onhand < '0')
{
	$dloc4onhand = '0';
}else
{
$dloc4onhand = $loc4onhand;
}
if($loc5onhand < '0')
{
	$dloc5onhand = '0';
}else
{
$dloc5onhand = $loc5onhand;
}
if($loc6onhand < '0')
{
	$dloc6onhand = '0';
}else
{
$dloc6onhand = $loc6onhand;
}
if($loc7onhand < '0')
{
	$dloc7onhand = '0';
}else
{
$dloc7onhand = $loc7onhand;
}
if($loc8onhand < '0')
{
	$dloc8onhand = '0';
}else
{
$dloc8onhand = $loc8onhand;
}
if($loc9onhand < '0')
{
	$dloc9onhand = '0';
}else
{
$dloc9onhand = $loc9onhand;
}
if($loc10onhand < '0')
{
	$dloc10onhand = '0';
}else
{
$dloc10onhand = $loc10onhand;
}
		$otherloconhand = $dloc1onhand + $dloc2onhand + $dloc3onhand + $dloc4onhand + $dloc5onhand + $dloc6onhand + $dloc7onhand + $dloc8onhand + $dloc9onhand + $dloc10onhand;
		$sql2 = "SELECT `id`,`price1` FROM `inventory_price` WHERE `partid` = :partid AND `siteid` = :siteid";
	$sth2 = $pdocxn->prepare($sql2);
	$sth2->bindParam(':partid',$partid);
	$sth2->bindParam(':siteid',$currentlocationid);
	$sth2->execute();
	while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
	{
	$price = $row2['price1'];
	}
	$installprice = "28.99";
	$iprice2 = $installprice * 2;
	$iprice3 = $installprice * 3;
	$iprice4 = $installprice * 4;
		$displayprice = number_format((float)$price, 2, '.', '');

		$installedprice1 = $price+$installprice;
		$installedprice1a = number_format((float)$installedprice1, 2, '.', '');
		$installedprice2 = $price*'2' +$iprice2;
		$installedprice2a = number_format((float)$installedprice2, 2, '.', '');
		$installedprice3 = $price*'3' +$iprice3;
		$installedprice3a = number_format((float)$installedprice3, 2, '.', '');
		$installedprice4 = $price*'4' +$iprice4;
		$installedprice4a = number_format((float)$installedprice4, 2, '.', '');
		$installedprice1ab = round($installedprice1*$salestax,2);
		$installedprice1b = number_format((float)$installedprice1ab, 2, '.', '');
		$installedprice2ab = round($installedprice2*$salestax,2);
		$installedprice2b = number_format((float)$installedprice2ab, 2, '.', '');
		$installedprice3ab = round($installedprice3*$salestax,2);
		$installedprice3b = number_format((float)$installedprice3ab, 2, '.', '');
		$installedprice4ab = round($installedprice4*$salestax,2);
		$installedprice4b = number_format((float)$installedprice4ab, 2, '.', '');

	if($currentlocationid=='1')
	{
		$dloconhand = $row1['loc1_onhand'];
	}
	if($currentlocationid=='2')
	{
		$dloconhand = $row1['loc2_onhand'];
	}/*
	if($currentlocationid=='3')
	{
		$dloconhand = $row1['loc3_onhand'];
	}
	if($currentlocationid=='4')
	{
		$dloconhand = $row1['loc4_onhand'];
	}
	if($currentlocationid=='5')
	{
		$dloconhand = $row1['loc5_onhand'];
	}
	if($currentlocationid=='6')
	{
		$dloconhand = $row1['loc6_onhand'];
	}
	if($currentlocationid=='7')
	{
		$dloconhand = $row1['loc7_onhand'];
	}
	if($currentlocationid=='8')
	{
		$dloconhand = $row1['loc8_onhand'];
	}
	if($currentlocationid=='9')
	{
		$dloconhand = $row1['loc9_onhand'];
	}
	if($currentlocationid=='10')
	{
		$dloconhand = $row1['loc10_onhand'];
	}*/
	if($dloconhand < '0')
	{
		$dploconhand = '0';
	}else{
		$dploconhand = $dloconhand;
	}
	$dotherloconhand = $otherloconhand - $dploconhand;
	//Display value
	$display = '1';
	if($selectedply != '0' && $selectedply != $plyid )
	{$display = '0';}
	if($selectedtraction != '0' && $selectedtraction != $traction)
	{$display = '0';}
	if($selectedtreadwear != '0' && $selectedtreadwear > $treadwear)
	{$display = '0';}
	if($selectedtemperature != '0' && $selectedtemperature != $temperature)
	{$display = '0';}
	if($selectedtype != '0' && $selectedtype != $type)
	{$display = '0';}
	if($selectedspeed != '0' && $selectedspeed != $speed)
	{$display = '0';}
	if($ci == '1')
	{
	if($display == '0')
	{
	}
	else {
	$sql2= "SELECT `brand` FROM `tire_manufacturers` WHERE `id` = '".$brandid."'";
	$query2 = mysqli_query($sqlicxn,$sql2);
	while ($row2 = mysqli_fetch_array($query2))
		{
		$brand = $row2['brand'];
		$description = $partnumber.", ".$size." ".$brand." ".$model;
		echo "<tr href=\"$tri\">";
		if($ci == '1')
{}else{
echo "<td><a href=editinventory.php?partid=".$partid."\"><img src=\"/images/icons/settings.jpeg\"></a></td>";
}
echo "<td>$partnumber</td><td>$size</td><td>$ply</td><td>$brand</td><td>$model</td><td><b>$displayprice</b></td><td>$sw</td><td>$mileage</td><td>$type</td><td>$load_index</td><td>$dloconhand</td><td><a href=\"printquickquote.php?accountid=".$accountid."&partid=".$partid."&qty=1&vehicleid=".$vehicleid."\" target=\"_blank\"><input type=\"button\" value=\"1\" class=\"xsmallbutton\" ></a><a href=\"printquickquote.php?accountid=".$accountid."&partid=".$partid."&qty=2&vehicleid=".$vehicleid."\" target=\"_blank\"><input type=\"button\" value=\"2\" class=\"xsmallbutton\" ></a><a href=\"printquickquote.php?accountid=".$accountid."&partid=".$partid."&qty=3&vehicleid=".$vehicleid."\" target=\"_blank\"><input type=\"button\" value=\"3\" class=\"xsmallbutton\" ></a><a href=\"printquickquote.php?accountid=".$accountid."&partid=".$partid."&qty=4&vehicleid=".$vehicleid."\" target=\"_blank\"><input type=\"button\" value=\"4\" class=\"xsmallbutton\" ></a></td></tr>";
	${"ip".$tri} = "<div id=\"$tri\"><div class=\"q1\">1 Installed<br/>Subtotal: $installedprice1a<br/><br /><b>Total w/ tax:$installedprice1b</b><br /><table width=\"100%\"><tr><td><center><form name=\"form1q$partid\" id=\"form1q$partid\" action=\"invoice.php\" method=\"post\" target=\"_BLANK\"><input type=\"hidden\" name=\"partid\" value=\"$partid\"><input type=\"hidden\" name=\"packageid\" value=\"5\"><input type=\"hidden\" name=\"qty\" value=\"1\"><input type=\"hidden\" name=\"type\" value=\"4\"><input type=\"hidden\" name=\"new\" value=\"1\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Quote\"></form></center></td></tr><tr><td><center><form name=\"forms$partid\" id=\"form$partid\" action=\"schedule.php\" method=\"post\"><input type=\"hidden\" name=\"partid\" value=\"$partid\"><input type=\"hidden\" name=\"packageid\" value=\"5\"><input type=\"hidden\" name=\"qty\" value=\"1\"><input type=\"hidden\" name=\"type\" value=\"51\"><input type=\"hidden\" name=\"new\" value=\"1\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Schedule\"></form></center></td></tr><tr><td><center><form name=\"form$partid\" id=\"formi$partid\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"partid\" value=\"$partid\"><input type=\"hidden\" name=\"packageid\" value=\"5\"><input type=\"hidden\" name=\"qty\" value=\"1\"><input type=\"hidden\" name=\"type\" value=\"1\"><input type=\"hidden\" name=\"new\" value=\"1\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Invoice\"></form></center></td></tr></table></div><div class=\"q2\">2 Installed<br/>Subtotal: $installedprice2a<br/><br /><b>Total w/ tax:$installedprice2b</b><br /><table width=\"100%\"><tr><td><center><form name=\"form2q$partid\" id=\"form2q$partid\" action=\"invoice.php\" method=\"post\" target=\"_BLANK\"><input type=\"hidden\" name=\"partid\" value=\"$partid\"><input type=\"hidden\" name=\"packageid\" value=\"4\"><input type=\"hidden\" name=\"qty\" value=\"2\"><input type=\"hidden\" name=\"type\" value=\"4\"><input type=\"hidden\" name=\"new\" value=\"1\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Quote\"></form></center></td></tr><tr><td><center><form name=\"forms$partid\" id=\"form$partid\" action=\"schedule.php\" method=\"post\"><input type=\"hidden\" name=\"partid\" value=\"$partid\"><input type=\"hidden\" name=\"packageid\" value=\"4\"><input type=\"hidden\" name=\"qty\" value=\"2\"><input type=\"hidden\" name=\"type\" value=\"51\"><input type=\"hidden\" name=\"new\" value=\"1\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Schedule\"></form></center></td></tr><tr><td><center><form name=\"form$partid\" id=\"formi$partid\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"partid\" value=\"$partid\"><input type=\"hidden\" name=\"packageid\" value=\"4\"><input type=\"hidden\" name=\"qty\" value=\"2\"><input type=\"hidden\" name=\"type\" value=\"1\"><input type=\"hidden\" name=\"new\" value=\"1\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Invoice\"></form></center></td></tr></table></div><div class=\"q3\">3 Installed<br/>Subtotal: $installedprice3a<br/><br /><b>Total w/ tax:$installedprice3b</b><br /><table width=\"100%\"><tr><td><center><form name=\"form3q$partid\" id=\"form3q$partid\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"partid\" value=\"$partid\"><input type=\"hidden\" name=\"packageid\" value=\"11\"><input type=\"hidden\" name=\"qty\" value=\"3\"><input type=\"hidden\" name=\"type\" value=\"4\"><input type=\"hidden\" name=\"new\" value=\"1\" target=\"_BLANK\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Quote\"></form></center></td></tr><tr><td><center><form name=\"forms$partid\" id=\"form$partid\" action=\"schedule.php\" method=\"post\"><input type=\"hidden\" name=\"partid\" value=\"$partid\"><input type=\"hidden\" name=\"packageid\" value=\"11\"><input type=\"hidden\" name=\"qty\" value=\"3\"><input type=\"hidden\" name=\"type\" value=\"51\"><input type=\"hidden\" name=\"new\" value=\"1\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Schedule\"></form></center></td></tr><tr><td><center><form name=\"form$partid\" id=\"formi$partid\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"partid\" value=\"$partid\"><input type=\"hidden\" name=\"packageid\" value=\"11\"><input type=\"hidden\" name=\"qty\" value=\"3\"><input type=\"hidden\" name=\"type\" value=\"1\"><input type=\"hidden\" name=\"new\" value=\"1\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Invoice\"></form></center></td></tr></table></div><div class=\"q4\">4 Installed<br/>Subtotal: $installedprice4a<br/><br /><b>Total w/ tax:$installedprice4b</b><br /><table width=\"100%\"><tr><td><center><form name=\"form4q$partid\" id=\"form4q$partid\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"partid\" value=\"$partid\"><input type=\"hidden\" name=\"price\" value=\"$price\"><input type=\"hidden\" name=\"desc\" value=\"$description\"><input type=\"hidden\" name=\"fet\" value=\"$fet\"><input type=\"hidden\" name=\"packageid\" value=\"3\"><input type=\"hidden\" name=\"qty\" value=\"4\"><input type=\"hidden\" name=\"type\" value=\"4\"><input type=\"hidden\" name=\"new\" value=\"1\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Quote\"></form></center></td></tr><tr><td><center><form name=\"forms$partid\" id=\"form$partid\" action=\"schedule.php\" method=\"post\"><input type=\"hidden\" name=\"partid\" value=\"$partid\"><input type=\"hidden\" name=\"packageid\" value=\"3\"><input type=\"hidden\" name=\"qty\" value=\"4\"><input type=\"hidden\" name=\"type\" value=\"51\"><input type=\"hidden\" name=\"new\" value=\"1\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Schedule\"></form></center></td></tr><tr><td><center><form name=\"form$partid\" id=\"formi$partid\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"partid\" value=\"$partid\"><input type=\"hidden\" name=\"packageid\" value=\"3\"><input type=\"hidden\" name=\"qty\" value=\"4\"><input type=\"hidden\" name=\"type\" value=\"1\"><input type=\"hidden\" name=\"new\" value=\"1\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Invoice\"></form></center></td></tr></table></div></div>";
	$tri ++;
	}}}else{
		if($display == '0')
	{
	}else{
	if($invoiceid > '0' OR $scheduleid > '0')
	{
	if($scheduleid > '0')
	{
        $sql2= "SELECT `brand` FROM `tire_manufacturers` WHERE `id` = '".$brandid."'";
        $query2 = mysqli_query($sqlicxn,$sql2);
        while ($row2 = mysqli_fetch_array($query2))
		{
		$brand = $row2['brand'];
		$description = $partnumber.", ".$size." ".$brand." ".$model;
		echo "<tr href=\"$tri\">";
		if($ci == '1')
{}else{
	echo "<td><a href=editinventory.php?partid=".$partid."\"><img src=\"/images/icons/settings.jpeg\"></a></td>";
}
//display tire quotes
echo "<td>$partnumber</td><td>$size</td><td>$ply</td><td>$brand</td><td>$model</td><td><b>$displayprice</b></td><td>$sw</td><td>$mileage</td><td>$type</td><td>$load_index</td><td><b>$dloconhand</b></td><td>$dotherloconhand</td></tr>";
	${"ip".$tri} = "<div id=\"$tri\">
	<div class=\"q1\">1 Installed<br/>Subtotal: $installedprice1a<br/><br/><b>Total w/ tax:$installedprice1b</b><br /><table width=\"100%\"><tr><td><center><form name=\"form1q$partid\" id=\"form1q$partid\" action=\"appointment.php\" method=\"post\"><input type=\"hidden\" name=\"partid\" value=\"$partid\"><input type=\"hidden\" name=\"qty\" value=\"1\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"hidden\" name=\"scheduleid\" value=\"".$scheduleid."\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Add 1 Tire\"></form></center></td></tr><tr><td><center><form name=\"forms$partid\" id=\"form$partid\" action=\"appointment.php\" method=\"post\"><input type=\"hidden\" name=\"partid\" value=\"$partid\"><input type=\"hidden\" name=\"packageid\" value=\"5\"><input type=\"hidden\" name=\"qty\" value=\"1\"><input type=\"hidden\" name=\"scheduleid\" value=\"".$scheduleid."\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Add 1 tire w/ install\"></form></center></td></tr></table></div>
	<div class=\"q2\">2 Installed<br/>Subtotal: $installedprice2a<br/><br /><b>Total w/ tax:$installedprice2b</b><br /><table width=\"100%\"><tr><td><center><form name=\"form2q$partid\" id=\"form2q$partid\" action=\"appointment.php\" method=\"post\"><input type=\"hidden\" name=\"partid\" value=\"$partid\"><input type=\"hidden\" name=\"qty\" value=\"2\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"hidden\" name=\"scheduleid\" value=\"".$scheduleid."\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Add 2 Tires\"></form></center></td></tr><tr><td><center><form name=\"forms$partid\" id=\"form$partid\" action=\"appointment.php\" method=\"post\"><input type=\"hidden\" name=\"partid\" value=\"$partid\"><input type=\"hidden\" name=\"packageid\" value=\"4\"><input type=\"hidden\" name=\"qty\" value=\"2\"><input type=\"hidden\" name=\"scheduleid\" value=\"".$scheduleid."\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Add 2 tires w/ install\"></form></center></td></tr></table></div>
	<div class=\"q3\">3 Installed<br/>Subtotal: $installedprice3a<br/><br /><b>Total w/ tax:$installedprice3b</b><br /><table width=\"100%\"><tr><td><center><form name=\"form3q$partid\" id=\"form3q$partid\" action=\"appointment.php\" method=\"post\"><input type=\"hidden\" name=\"partid\" value=\"$partid\"><input type=\"hidden\" name=\"qty\" value=\"3\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"hidden\" name=\"scheduleid\" value=\"".$scheduleid."\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Add 3 Tires\"></form></center></td></tr><tr><td><center><form name=\"forms$partid\" id=\"form$partid\" action=\"appointment.php\" method=\"post\"><input type=\"hidden\" name=\"partid\" value=\"$partid\"><input type=\"hidden\" name=\"packageid\" value=\"11\"><input type=\"hidden\" name=\"qty\" value=\"3\"><input type=\"hidden\" name=\"scheduleid\" value=\"".$scheduleid."\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Add 3 tires w/ install\"></form></center></td></tr></table></div>
	<div class=\"q4\">4 Installed<br/>Subtotal: $installedprice4a<br/><br /><b>Total w/ tax:$installedprice4b</b><br /><table width=\"100%\"><tr><td><center><form name=\"form4q$partid\" id=\"form4q$partid\" action=\"appointment.php\" method=\"post\"><input type=\"hidden\" name=\"partid\" value=\"$partid\"><input type=\"hidden\" name=\"qty\" value=\"4\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"hidden\" name=\"scheduleid\" value=\"".$scheduleid."\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Add 4 Tires\"></form></center></td></tr><tr><td><center><form name=\"forms$partid\" id=\"form$partid\" action=\"appointment.php\" method=\"post\"><input type=\"hidden\" name=\"partid\" value=\"$partid\"><input type=\"hidden\" name=\"packageid\" value=\"3\"><input type=\"hidden\" name=\"qty\" value=\"4\"><input type=\"hidden\" name=\"scheduleid\" value=\"".$scheduleid."\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Add 4 tires w/ install\"></form></center></td></tr></table></div></div>";
	$tri ++;
		}}else {
	$sql2= "SELECT `brand` FROM `tire_manufacturers` WHERE `id` = '".$brandid."'";
	$query2 = mysqli_query($sqlicxn,$sql2);
	while ($row2 = mysqli_fetch_array($query2))
		{
		$brand = $row2['brand'];
		$description = $partnumber.", ".$size." ".$brand." ".$model;
		echo "<tr href=\"$tri\"><td><input type=\"checkbox\" name=\"partnumber\" value=\"".$partnumber."\"><td>$partnumber</td><td>$size</td><td>$ply</td><td>$brand</td><td>$model</td><td><b>$displayprice</b></td><td>$sw</td><td>$mileage</td><td>$type</td><td>$load_index</td><td>$dloconhand</td></tr>";
	${"ip".$tri} = "<div id=\"$tri\">
<div class=\"q1\">1 Installed<br/>Subtotal: $installedprice1a<br/><br /><b>Total w/ tax:$installedprice1b</b><br /><table width=\"100%\"><tr><td><center><form name=\"form1q$partid\" id=\"form1q$partid\" action=\"invoice.php\" method=\"post\" target=\"_BLANK\"><input type=\"hidden\" name=\"partid\" value=\"$partid\"><input type=\"hidden\" name=\"qty\" value=\"1\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Add 1 Tire\"></form></center></td></tr><tr><td><br/></td></tr><tr><td><center><form name=\"form$partid\" id=\"formi$partid\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"partid\" value=\"$partid\"><input type=\"hidden\" name=\"packageid\" value=\"5\"><input type=\"hidden\" name=\"qty\" value=\"1\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Add 1 tire w/ Install\"></form></center></td></tr></table></div>
<div class=\"q2\">2 Installed<br/>Subtotal: $installedprice2a<br/><br /><b>Total w/ tax:$installedprice2b</b><br /><table width=\"100%\"><tr><td><center><form name=\"form2q$partid\" id=\"form2q$partid\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"partid\" value=\"$partid\"><input type=\"hidden\" name=\"qty\" value=\"2\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Add 2 Tires\"></form></center></td></tr><tr><td><br/></td></tr><tr><td><center><form name=\"form$partid\" id=\"formi$partid\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"partid\" value=\"$partid\"><input type=\"hidden\" name=\"packageid\" value=\"4\"><input type=\"hidden\" name=\"qty\" value=\"2\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Add 2 tires w/ Install\"></form></center></td></tr></table></div>
<div class=\"q3\">3 Installed<br/>Subtotal: $installedprice3a<br/><br /><b>Total w/ tax:$installedprice3b</b><br /><table width=\"100%\"><tr><td><center><form name=\"form3q$partid\" id=\"form3q$partid\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"partid\" value=\"$partid\"><input type=\"hidden\" name=\"qty\" value=\"3\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Add 3 Tires\"></form></center></td></tr><tr><td><br/></td></tr><tr><td><center><form name=\"form$partid\" id=\"formi$partid\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"partid\" value=\"$partid\"><input type=\"hidden\" name=\"packageid\" value=\"11\"><input type=\"hidden\" name=\"qty\" value=\"3\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Add 3 tires w/ Install\"></form></center></td></tr></table></div>
<div class=\"q4\">4 Installed<br/>Subtotal: $installedprice4a<br/><br /><b>Total w/ tax:$installedprice4b</b><br /><table width=\"100%\"><tr><td><center><form name=\"form4q$partid\" id=\"form4q$partid\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"partid\" value=\"$partid\"><input type=\"hidden\" name=\"qty\" value=\"4\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Add 4 Tires\"></form></center></td></tr><tr><td><br/></td></tr><tr><td><center><form name=\"form$partid\" id=\"formi$partid\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"partid\" value=\"$partid\"><input type=\"hidden\" name=\"packageid\" value=\"3\"><input type=\"hidden\" name=\"qty\" value=\"4\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Add 4 tires w/ Install\"></form></center></td></tr></table></div></div>";
	$tri ++;
	}}}else{
	$sql2= "SELECT `brand` FROM `tire_manufacturers` WHERE `id` = '".$brandid."'";
	$query2 = mysqli_query($sqlicxn,$sql2);
	while ($row2 = mysqli_fetch_array($query2))
		{
		$brand = $row2['brand'];
		$description = $partnumber.", ".$size." ".$brand." ".$model;
		echo "<tr href=\"$tri\">";
if($ci == '1')
{}else{
	echo "<td><a href=\"editinventory.php?partid=".$partid."\"><img src=\"images/icons/setting.jpeg\" width=\"25\"></a></td>";
}
echo "<td>$partnumber</td><td>$size</td><td>$ply</td><td>$brand</td><td>$model</td><td><b>$displayprice</b></td><td>$sw</td><td>$mileage</td><td>$type</td><td>$load_index</td><td><b>$dloconhand</b></td><td>$dotherloconhand</td></tr>";
	${"ip".$tri} = "<div id=\"$tri\"><div class=\"q1\">1 Installed<br/>Subtotal: $installedprice1a<br/><br /><b>Total w/ tax:$installedprice1b</b><br /><table width=\"100%\"><tr><td><center><form name=\"form1q$partid\" id=\"form1q$partid\" action=\"invoice.php\" method=\"post\" target=\"_BLANK\"><input type=\"hidden\" name=\"partid\" value=\"$partid\"><input type=\"hidden\" name=\"packageid\" value=\"5\"><input type=\"hidden\" name=\"qty\" value=\"1\"><input type=\"hidden\" name=\"type\" value=\"4\"><input type=\"hidden\" name=\"new\" value=\"1\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Quote\"></form></center></td></tr><tr><td><center><form name=\"forms$partid\" id=\"form$partid\" action=\"schedule.php\" method=\"post\"><input type=\"hidden\" name=\"partid\" value=\"$partid\"><input type=\"hidden\" name=\"packageid\" value=\"5\"><input type=\"hidden\" name=\"qty\" value=\"1\"><input type=\"hidden\" name=\"type\" value=\"51\"><input type=\"hidden\" name=\"new\" value=\"1\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Schedule\"></form></center></td></tr><tr><td><center><form name=\"form$partid\" id=\"formi$partid\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"partid\" value=\"$partid\"><input type=\"hidden\" name=\"packageid\" value=\"5\"><input type=\"hidden\" name=\"qty\" value=\"1\"><input type=\"hidden\" name=\"type\" value=\"1\"><input type=\"hidden\" name=\"new\" value=\"1\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Invoice\"></form></center></td></tr></table></div><div class=\"q2\">2 Installed<br/>Subtotal: $installedprice2a<br/><br /><b>Total w/ tax:$installedprice2b</b><br /><table width=\"100%\"><tr><td><center><form name=\"form2q$partid\" id=\"form2q$partid\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"partid\" value=\"$partid\"><input type=\"hidden\" name=\"packageid\" value=\"4\"><input type=\"hidden\" name=\"qty\" value=\"2\"><input type=\"hidden\" name=\"type\" value=\"4\"><input type=\"hidden\" name=\"new\" value=\"1\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Quote\"></form></center></td></tr><tr><td><center><form name=\"forms$partid\" id=\"form$partid\" action=\"schedule.php\" method=\"post\"><input type=\"hidden\" name=\"partid\" value=\"$partid\"><input type=\"hidden\" name=\"packageid\" value=\"4\"><input type=\"hidden\" name=\"qty\" value=\"2\"><input type=\"hidden\" name=\"type\" value=\"51\"><input type=\"hidden\" name=\"new\" value=\"1\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Schedule\"></form></center></td></tr><tr><td><center><form name=\"form$partid\" id=\"formi$partid\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"partid\" value=\"$partid\"><input type=\"hidden\" name=\"packageid\" value=\"4\"><input type=\"hidden\" name=\"qty\" value=\"2\"><input type=\"hidden\" name=\"type\" value=\"1\"><input type=\"hidden\" name=\"new\" value=\"1\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Invoice\"></form></center></td></tr></table></div><div class=\"q3\">3 Installed<br/>Subtotal: $installedprice3a<br/><br /><b>Total w/ tax:$installedprice3b</b><br /><table width=\"100%\"><tr><td><center><form name=\"form3q$partid\" id=\"form3q$partid\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"partid\" value=\"$partid\"><input type=\"hidden\" name=\"packageid\" value=\"11\"><input type=\"hidden\" name=\"qty\" value=\"3\"><input type=\"hidden\" name=\"type\" value=\"4\"><input type=\"hidden\" name=\"new\" value=\"1\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Quote\"></form></center></td></tr><tr><td><center><form name=\"forms$partid\" id=\"form$partid\" action=\"schedule.php\" method=\"post\"><input type=\"hidden\" name=\"partid\" value=\"$partid\"><input type=\"hidden\" name=\"packageid\" value=\"11\"><input type=\"hidden\" name=\"qty\" value=\"3\"><input type=\"hidden\" name=\"type\" value=\"51\"><input type=\"hidden\" name=\"new\" value=\"1\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Schedule\"></form></center></td></tr><tr><td><center><form name=\"form$partid\" id=\"formi$partid\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"partid\" value=\"$partid\"><input type=\"hidden\" name=\"packageid\" value=\"11\"><input type=\"hidden\" name=\"qty\" value=\"3\"><input type=\"hidden\" name=\"type\" value=\"1\"><input type=\"hidden\" name=\"new\" value=\"1\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Invoice\"></form></center></td></tr></table></div><div class=\"q4\">4 Installed<br/>Subtotal: $installedprice4a<br/><br /><b>Total w/ tax:$installedprice4b</b><br /><table width=\"100%\"><tr><td><center><form name=\"form4q$partid\" id=\"form4q$partid\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"partid\" value=\"$partid\"><input type=\"hidden\" name=\"price\" value=\"$price\"><input type=\"hidden\" name=\"desc\" value=\"$description\"><input type=\"hidden\" name=\"fet\" value=\"$fet\"><input type=\"hidden\" name=\"packageid\" value=\"3\"><input type=\"hidden\" name=\"qty\" value=\"4\"><input type=\"hidden\" name=\"type\" value=\"4\"><input type=\"hidden\" name=\"new\" value=\"1\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Quote\"></form></center></td></tr><tr><td><center><form name=\"forms$partid\" id=\"form$partid\" action=\"schedule.php\" method=\"post\"><input type=\"hidden\" name=\"partid\" value=\"$partid\"><input type=\"hidden\" name=\"packageid\" value=\"3\"><input type=\"hidden\" name=\"qty\" value=\"4\"><input type=\"hidden\" name=\"type\" value=\"51\"><input type=\"hidden\" name=\"new\" value=\"1\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Schedule\"></form></center></td></tr><tr><td><center><form name=\"form$partid\" id=\"formi$partid\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"partid\" value=\"$partid\"><input type=\"hidden\" name=\"packageid\" value=\"3\"><input type=\"hidden\" name=\"qty\" value=\"4\"><input type=\"hidden\" name=\"type\" value=\"1\"><input type=\"hidden\" name=\"new\" value=\"1\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Invoice\"></form></center></td></tr></table></div></div>";
	$tri ++;
	}}}}}}
	else
	{
	?>
	<tr><td colspan="12"><a href="inventory.php<?php echo $cilink.$acctlink.$invlink.$schedlink; ?>"><b>No results, please try again</b></a></td></tr>
	<?php
	}
	?>
	</tbody>
	<tfoot><td colspan="13"><center></form><form name="print" action="printinventory2.php" method="POST">
	<input type="hidden" name="sql1" value="<?php echo $sql1; ?>">
	<input type="hidden" name="searchquicksearch" value="<?php echo $searchquicksearch; ?>">
	<input type="hidden" name="searchpartnumber" value="<?php echo $searchpartnumber; ?>">
	<input type="hidden" name="submit" value="1">
<input type="submit" class="save" value="Print Inventory">
	</form>
	<br /><br /><br />
</center></td></tfoot>
	</table>
		</div><div class="right">
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
	<script type="text/javascript">
	$('.selectall').click(function() {
		if ($(this).is(':checked')) {
			$('input:checkbox').attr('checked', true);
		} else {
			$('input:checkbox').attr('checked', false);
		}
	});
	</script>
	<?php
	//highlight javascript
	?>
	<script type="text/javascript">
	function sortTable(f,n){
		var rows = $('#highlightTable tbody  tr').get();
		rows.sort(function(a, b) {
			var A = getVal(a);
			var B = getVal(b);
			if(A < B) {
				return -1*f;
			}
			if(A > B) {
				return 1*f;
			}
			return 0;
		});
		function getVal(elm){
			var v = $(elm).children('td').eq(n).text().toUpperCase();
			if($.isNumeric(v)){
				v = parseInt(v,10);
			}
			return v;
		}

		$.each(rows, function(index, row) {
			$('#highlightTable').children('tbody').append(row);
		});
	}
	var f_part = 1;
	var f_size = 1;
	var f_ply = 1;
	var f_brand = 1;
	var f_model = 1;
	var f_price = 1;
	var f_sidewall = 1;
	var f_mileage = 1;
	var f_type = 1;
	var f_speed = 1;
	var f_onhand = 1;
	$("#part").click(function(){
		f_part *= -1;
		var n = $(this).prevAll().length;
		sortTable(f_part,n);
	});
	$("#size").click(function(){
		f_size *= -1;
		var n = $(this).prevAll().length;
		sortTable(f_size,n);
	});
	$("#ply").click(function(){
		f_ply *= -1;
		var n = $(this).prevAll().length;
		sortTable(f_ply,n);
	});
	$("#brand").click(function(){
		f_brand *= -1;
		var n = $(this).prevAll().length;
		sortTable(f_brand,n);
	});
	$("#model").click(function(){
		f_model *= -1;
		var n = $(this).prevAll().length;
		sortTable(f_model,n);
	})
	$("#price").click(function(){
		f_price *= -1;
		var n = $(this).prevAll().length;
		sortTable(f_price,n);
	});
	$("#sidewall").click(function(){
		f_sidewall *= -1;
		var n = $(this).prevAll().length;
		sortTable(f_sidewall,n);
	});
	$("#mileage").click(function(){
		f_mileage *= -1;
		var n = $(this).prevAll().length;
		sortTable(f_mileage,n);
	});
	$("#type").click(function(){
		f_type *= -1;
		var n = $(this).prevAll().length;
		sortTable(f_type,n);
	});
	$("#speed").click(function(){
		f_speed *= -1;
		var n = $(this).prevAll().length;
		sortTable(f_speed,n);
	});
	$("#onhand").click(function(){
		f_onhand *= -1;
		var n = $(this).prevAll().length;
		sortTable(f_onhand,n);
	});
	</script>
<script>
	$(":checkbox").change(function() {
		$(this).closest("td").toggleClass("highlight", this.checked);
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
	<title><?php echo $title; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" >
	<link rel="stylesheet" type="text/css" href="style/globalstyle.css" >
	<link rel="stylesheet" type="text/css" href="style/inventorystyle.css" >
	<script type="text/javascript" src="scripts/jquery-latest.js"></script>
	<script type="text/javascript" src="scripts/script.js"></script>
	</head>
	<body>
	<?php
	if($ci == '1')
	{
	echo "<div id=\"header\"><font color=\"red\">Customer Interaction for ".$customername."</font><br /><form name=\"cancel\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"submit\" name=\"submit\" class=\"cancel\" Value=\"Cancel\"></form></div>";
	}else{
	if($invoiceid > '0' OR $scheduleid > '0')
	{
		if($invoiceid > '0')
		{
	echo "<div id=\"header\"><font color=\"red\">Adding Tires to an Invoice</font><br /><form name=\"cancel\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"submit\" name=\"submit\" class=\"cancel\" Value=\"Cancel\"></form></div>";
	}else{
	echo "<div id=\"header\"><font color=\"red\">Adding Tires to an Appointment</font><br /><a href=\"appointment.php?id=".$scheduleid."\" class=\"no-decoration\"><button class=\"cancel\">Cancel</button></a></div>";
	}}else{
	if($currentlocationid == '1')
	{
	echo "<div id=\"header\">".$headernavigation."</div>";
	}
	else{
	echo "<div id=\"header2\">".$headernavigation2."</div>";
	}}}
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
	</select></div><input type="hidden" name="form" value="1"></td><!--<td><a href="inventoryvehicle.php">Search Tire Size by Vehicle</a></td>--></tr></tr></table></form></div>
			<div id="content"><form name="inventory" action="inventory.php<?php echo $cilink.$acctlink.$invlink.$schedlink; ?>" method="post"><div class="searchright"><label for="onhand">Search on Hand:<input type="checkbox" name="onhand[]" value="1" id="onhand"></label><br /><label for="obligated">Search Obligated Only:<input type="checkbox" name="obligated[]" value="1" id="obligated"></label><br /><label for="ordered">Search on Order:<input type="checkbox" name="order[]" value="1" id="ordered"></label><br />
		<?php
		if($ci == '1')
		{
		echo "<input type=\"hidden\" name=\"ci\" value=\"1\">";
		}
		?>
		<input type="submit" class="quotebutton" name="submit" value="search"><br />
	<br /><a href="inventory.php<?php echo $cilink.$acctlink.$invlink.$schedlink; ?>" class="no-decoration"><input type="button" class="cancel" name="submit" value="Reset"></a>
	<br /><br />
	<?php
	if($transfer =='1')
	{
	echo "<input type=\"hidden\" name=\"transfer\" value=\"1\">";
	}else{
	?>
	<br /><br /><a href="inventory.php?transfer=1"  class="no-decoration"><input type="button" value="Transfer Inventory Out" class="smallbutton"></a>
	<?php
	}
	?>
	<br /><br />
	<?php
	if($receive =='1')
	{
	echo "<input type=\"hidden\" name=\"receive\" value=\"1\">";
	}else{
	?>
	<a href="recinv.php"  class="no-decoration" target="_BLANK"><input type="button" value="Receive Inventory" class="smallbutton"></a>
	<?php
	}
	?>
	<br /><br /><a href="recinv-subtype.php"  class="no-decoration"><input type="button" value="Insert New Inventory" class="smallbutton"></a>
	<br /><br /><a href="adjustinventory.php"  class="no-decoration"><input type="button" value="Adjust Inventory Quantity" class="smallbutton"></a>
	</div>
	<table>
	<?php
	if($receive =='1')
	{
	echo "<tr><th colspan=\"4\"><font color=\"red\">Enter Inventory to Receive</font></th></tr>";
	}
	if($transfer =='1')
	{
	echo "<tr><th colspan=\"4\"><font color=\"red\">Enter Inventory to Transfer Out</font></th></tr>";
	}
	?>
	<tr><th>Quick Search:</th><td>
	<?php
	if($qsr > '0')
	{
	echo '<input type="text" name="quicksearch" autocomplete="off" autofocus value="'.$qsr.'">';
	}else{
	echo '<input type="text" name="quicksearch" autocomplete="off" autofocus>';
	}
	?>
	</td><th>Part Number:</th><td><input type="text" name="partnumber" autocomplete="off"></td></tr>
				</table>
	<table class="brandstable">
	<tr><th>Tire Brands:</th></tr><tr><td colspan="3" id="alltd" class="highlight"><label><input type="checkbox" name="brand" value="0" class="all" checked="checked">All</label></td><td colspan="4" id="affiliatechecktd" class="affiliatechecktd"><label><input type="checkbox" name="brand" value="0" class="affiliatecheck">Affiliate Brands</label></td>
						<?php
						$sql1= "SELECT `id`,`brand`,`affiliate` FROM `tire_manufacturers` WHERE `inactive` = '0' ORDER BY `brand` ASC";
						$tb1 = '0';
						$query1 = mysqli_query($sqlicxn,$sql1);
		while ($row1 = mysqli_fetch_assoc($query1))
		{
		$brand = $row1['brand'];
		$id = $row1['id'];
		$affiliate = $row1['affiliate'];
		if($affiliate == '1')
		{
			$checkboxclass = 'affiliatebrands';
			$tdclass = 'affiliatebrandstd';
		}
		else
			{
				$checkboxclass = 'brands';
				$tdclass = 'brandstd';
			}
		if($tb1 % '7')
		{
			echo "<td id=\"".$tdclass."\" class=\"".$tdclass."\"><label><input type=\"checkbox\" name=\"brand[]\" value=\"".$id."\" class=\"".$checkboxclass."\" id=\"".$checkboxclass."\">".$brand."</label></td>\n";
		}
		else {
		echo "</tr><tr><td id=\"".$tdclass."\" class=\"".$tdclass."\"><label><input type=\"checkbox\" name=\"brand[]\" value=\"".$id."\" class=\"".$checkboxclass."\" id=\"".$checkboxclass."\">".$brand."</label></td>\n";
		}
		$tb1 ++;
		}
	?></tr></table>
	<table>
					<tr><th>Width:<input type="text" name="width" autocomplete="off"></th><th>Ratio:<input type="text" name="ratio" autocomplete="off"></th><th>Rim:<input type="text" name="rim" autocomplete="off"></th><th>Load Range:<select name="ply"><option value="0"></option><option value="1">B (4 Ply)</option><option value="2">C (6 Ply)</option><option value="3">D (8 Ply)</option><option value="4">E (10 Ply)</option><option value="5">F (12 Ply)</option><option value="6">G (14 Ply)</option><option value="7">H (16 Ply)</option><option value="8">J (18 Ply)</option></select></th></tr>
	</table><table>
					<tr><th class="left">Traction:</th><td><label for="all">All:</label><Input type="radio" name="traction" value="0" id="all" checked="checked"></td><td><label for="AA">AA:</label><Input type="radio" name="traction" value="AA" id="AA"></td><td><label for="A">A:</label><Input type="radio" name="traction" value="A" id="A"></td><td><label for="B">B:</label><Input type="radio" name="traction" value="B" id="B"></td><td><label for="C">C:</label><Input type="radio" name="traction" value="C" id="C"></td></tr>
					<tr><th class="left">Treadwear:</th><td><label for="treadall">All:</label><Input type="radio" name="treadwear" value="0" id="treadall" checked="checked"></td><td><label for="200">200+</label><Input type="radio" name="treadwear" value="200" id="200"></td><td><label for="400">400+</label><Input type="radio" name="treadwear" value="400" id="400"></td><td><label for="600">600+</label><Input type="radio" name="treadwear" value="600" id="600"></td><td><label for="800">800+</label><Input type="radio" name="treadwear" value="800" id="800"></td></tr>
					<tr><th class="left">Temperature:</th><td><label for="tempall">All:</label><Input type="radio" name="temperature" value="0" id="tempall" checked="checked"></td><td><label for="tempA">A:</label><Input type="radio" name="temperature" value="A" id="tempA"></td><td><label for="tempB">B:</label><Input type="radio" name="temperature" value="B" id="tempB"></td><td><label for="tempC">C:</label><Input type="radio" name="temperature" value="C" id="tempC"></td></tr>
					</table><table><tr><th>Type:</th></tr><tr><td><label for="1">All</label><Input type="radio" name="type" value="0" id="1" checked="checked"></td>
						<?php
						$sql2 = "SELECT * FROM `inventory_type` WHERE `inactive`='0' ORDER BY `value` ASC";
						$tb2 = '1';
						$query2 = mysqli_query($sqlicxn,$sql2);
		while ($row2 = mysqli_fetch_assoc($query2))
		{
		$type = $row2['value'];
		$typeid = $row2['id'];
		if($tb2 % '7')
		{
					echo "<td><label for=\"".$typeid."\">".$type."</label><Input type=\"radio\" name=\"type\" value=\"".$typeid."\" id=\"".$typeid."\"></td>\n";
		}
		else {
			echo "</tr><tr><td><label for=\"".$typeid."\">".$type."</label><Input type=\"radio\" name=\"type\" value=\"".$typeid."\" id=\"".$typeid."\"></td>\n";
		}
		$tb2 ++;
		}
	?>
	</table>
	<?php
	if($ci == '1')
	{
	echo "<input type=\"hidden\" name=\"ci\" value=\"1\">";
	}
	if($accountid > '0')
	{
	echo "<input type=\"hidden\" name=\"accountid\" value=\"$accountid\">";
	}
	if($invoiceid > '0')
	{
	echo "<input type=\"hidden\" name=\"invoiceid\" value=\"$invoiceid\">";
	}
	if($scheduleid > '0')
	{
	echo "<input type=\"hidden\" name=\"scheduleid\" value=\"".$scheduleid."\">";
	}
	//highlight the selections
	?>
	</form></div>
	<script>
	$('input.all').on('change', function() {
		$('input.brands').not(this).prop('checked', false);
		$('input.affiliatebrands').not(this).prop('checked', false);
		$('input.affiliatecheck').not(this).prop('checked', false);
		$('alltd').addClass("highlight");
		$('.brandstd').removeClass("highlight");
		$('.affiliatechecktd').removeClass("highlight");
		$('.affiliatebrandstd').removeClass("highlight");
		$(this).closest("td").toggleClass("highlight", this.checked);
	});
	$('input.brands').on('change', function() {
		$('input.all').not(this).prop('checked', false);
		$('input.affiliatecheck').not(this).prop('checked', false);
		$('.affiliatechecktd').removeClass("highlight");
		$('#alltd').removeClass("highlight");
		$(this).closest("td").toggleClass("highlight", this.checked);
	});
	$('input.affiliatebrands').on('change', function() {
		$('input.all').not(this).prop('checked', false);
		$('input.affiliatecheck').not(this).prop('checked', false);
		$('.affiliatechecktd').removeClass("highlight");
		$('#alltd').removeClass("highlight");
		$(this).closest("td").toggleClass("highlight", this.checked);
	});
	$('input.affiliatecheck').on('change', function() {
		$('input.all').not(this).prop('checked', false);
		$('input.brands').not(this).prop('checked', false);
		$('input.affiliatebrands').not(this).prop('checked', true);
		$('.brandstd').removeClass("highlight");
		$('#alltd').removeClass("highlight");
		$('.affiliatebrandstd').addClass("highlight");
		$(this).closest("td").toggleClass("highlight", this.checked);
	});
	</script>
	<script>
	$(":checkbox").change(function() {
		$(this).closest("td").toggleClass("highlight", this.checked);
	});
	</script>
	</body>
	</html>
	<?php
	}
	?>
	<?php
	if($a==$b)
	{
	//enter inventory
	$sql1 = 'UPDATE `inventory` SET :storelocation = :storelocation1 + :insertqty WHERE `id` = :id';
	$sth1 = $pdocxn->prepare($sql1);
	$sth1->bindParam(':storelocation',$storelocation);
	$sth1->bindParam(':storelocation1',$storelocation1);
	$sth1->bindParam(':insertqty',$insertqty);
	$sth1->bindParam(':id',$id);
	$sth1->execute();
	//Create a record of the transaction
	$sql2 = 'INSERT INTO `inventory_transactions` (`partid`,`datetime`,`qty`,`transactiontype`,`location`,`amount`)VALUES(:partid,:datetime,:qty,:transactiontype,:location,:amount)';
	$sth2 = $pdocxn->prepare($sql2);
	$sth2->bindParam(':partid',$partid);
	$sth2->bindParam(':datetime',$datetime);
	$sth2->bindParam(':qty',$qty);
	$sth2->bindParam(':transactiontype',$transactiontype);
	$sth2->bindParam(':location',$location);
	$sth2->bindParam(':amount',$amount);
	$sth2->execute();
	//get the file and convert everything to variables


	if(isset($_POST["importnewinventory"])){
		$manid = $_POST['manid'];
		
		$filename=$_FILES["file"]["tmp_name"];    
		if($_FILES["file"]["size"] > 0)
		{
			$file = fopen($filename, "r");
			while (($getData = fgetcsv($file, 10000, ",")) !== FALSE)
			{

					$sql2 = 'INSERT INTO `inventory` (`manid`,`model`,`warranty`,`width`,`ratio`,`rim`,`part_number`,`sidewall`,`treadwear`,`fet`,`load`,`ply`,`plyid`)VALUES(:manid,:model,:warranty,:width,:ratio,:rim,:part_number,:sidewall,:treadwear,:fet,:load,:ply,:plyid)';
					$sth2 = $pdocxn->prepare('INSERT INTO `invoice`(`id`,`invoiceid`,`userid`,`type`,`location`,`creationdate`,`invoicedate`,`taxgroup`,`accountid`,`vehicleid`,`mileagein`,`mileageout`) VALUES (:id,:invoicenumber,:userid,:typeid,:location,:creationdate,:invoicedate,:taxgroup,:accountid,:vehicleid,:mileagein,:mileageout)');
					$sth2->bindParam(':manid',$manid);
					$sth2->bindParam(':model',$getdata[1]);
					$sth2->bindParam(':warranty',$getdata[2]);
					$sth2->bindParam(':width',$getdata[3]);
					$sth2->bindParam(':ratio',$getdata[4]);
					$sth2->bindParam(':rim',$getdata[5]);
					$sth2->bindParam(':part_number',$getdata[6]);
					$sth2->bindParam(':sidewall',$getdata[7]);
					$sth2->bindParam(':treadwear',$getdata[8]);
					$sth2->bindParam(':fet',$getdata[9]);
					$sth2->bindParam(':load',$getdata[10]);
					$sth2->bindParam(':ply',$getdata[11]);
					$sth2->bindParam(':plyid',$plyid);
					$sth2->bindParam('cost',$getdata[12]);
					$sth2->execute();
					
			if(!isset($result))
			{
			echo "<script type=\"text/javascript\">
				alert(\"Invalid File:Please Upload CSV File.\");
				window.location = \"index.php\"
				</script>";    
			}
			else {
				echo "<script type=\"text/javascript\">
				alert(\"CSV File has been successfully Imported.\");
				window.location = \"index.php\"
			</script>";
			}
			}
			fclose($file);  
		}
	}

	$subtypeid = $_POST['subtypeid'];
	$manid = $_POST['manid'];
	$model = $_POST['manid'];
	$warranty = $_POST['manid'];
	$width = $_POST['manid'];
	$ratio = $_POST['manid'];
	$rim = $_POST['manid'];
	$part_number = $_POST['manid'];
	$sidewall = $_POST['manid'];
	$treadwear = $_POST['manid'];
	$fet = $_POST['manid'];
	$load = $_POST['manid'];
	$ply = $_POST['manid'];
	$plyid = $_POST['manid'];
	$cost = $_POST['manid'];

	$sql2 = 'INSERT INTO `inventory` (`subtypeid`,`manid`,`model`,`warranty`,`width`,`ratio`,`rim`,`part_number`,`sidewall`,`treadwear`,`fet`,`load`,`ply`,`plyid`)VALUES(:subtypeid,:manid,:model,:warranty,:width,:ratio,:rim,:part_number,:sidewall,:treadwear,:fet,:load,:ply,:plyid)';
	$sth2 = $pdocxn->prepare('INSERT INTO `invoice`(`id`,`invoiceid`,`userid`,`type`,`location`,`creationdate`,`invoicedate`,`taxgroup`,`accountid`,`vehicleid`,`mileagein`,`mileageout`) VALUES (:id,:invoicenumber,:userid,:typeid,:location,:creationdate,:invoicedate,:taxgroup,:accountid,:vehicleid,:mileagein,:mileageout)');
	$sth2->bindParam(':subtypeid',$subtypeid);
	$sth2->bindParam(':manid',$manid);
	$sth2->bindParam(':model',$model);
	$sth2->bindParam(':warranty',$warranty);
	$sth2->bindParam(':width',$width);
	$sth2->bindParam(':ratio',$ratio);
	$sth2->bindParam(':rim',$rim);
	$sth2->bindParam(':part_number',$part_number);
	$sth2->bindParam(':sidewall',$sidewall);
	$sth2->bindParam(':treadwear',$treadwear);
	$sth2->bindParam(':fet',$fet);
	$sth2->bindParam(':load',$load);
	$sth2->bindParam(':ply',$ply);
	$sth2->bindParam(':plyid',$plyid);
	$sth2->bindParam('cost',$cost);
	$sth2->execute();
	}
	?>
