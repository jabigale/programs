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
$title = 'Receive Inventory';
$linkpage = 'receiveinventory.php';

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
else{
$invoiceid = '0';
}
if(!isset($_COOKIE[$cookie1_name])) {
	$currentid = "0";
} else {
    $currentid = $_COOKIE[$cookie1_name];
}
if(!isset($_COOKIE[$cookie2_name])) {
	$currentusername = "None Selected";
} else {
    $currentusername = $_COOKIE[$cookie2_name];
}
if(!isset($_COOKIE[$cookie3_name])) {
	$currentlocationid = "0";
} else {
    $currentlocationid = $_COOKIE[$cookie3_name];
}
if(!isset($_COOKIE[$cookie4_name])) {
	$currentstorename = "None Selected";
} else {
    $currentstorename = $_COOKIE[$cookie4_name];
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
	foreach($_POST['onhand'] as $selectedonhand);
	foreach($_POST['obligated'] as $selectedobligated);
	foreach($_POST['order'] as $selectedorder);

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
</select></div><input type="hidden" name="form" value="1"></td><td><a href="inventoryvehicle.php">Search Tire Size by Vehicle</a></td><td><a href="inventory.php">Change Search</a></td><td><a href="inventory.php">Reset</a></td><?php if($invoiceid > '0') { echo "<td><font color=\"red\">Adding Tires to Invoice</font></td>";}?></tr></table></form></div>
        <div id="content">
        	<div id="left"><form name="inventory" action="inventory.php" method="post">
<table id="highlightTable" class="blueTable">
<thead>
<tr>
<th><input type="checkbox" class="selectall"></th>
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
	$locdate = "loc1date";
}
if($currentlocationid=='7')
{
	$loconhand = "loc7_onhand";
	$locdate = "loc2date";
}
if($currentlocationid=='8')
{
	$loconhand = "loc8_onhand";
	$locdate = "loc3date";
}
if($currentlocationid=='9')
{
	$loconhand = "loc9_onhand";
	$locdate = "loc4date";
}
if($currentlocationid=='10')
{
	$loconhand = "loc10_onhand";
	$locdate = "loc5date";
}
$sbi = '1';
$tri = '1';
//Search by partnumber
if($selectedpartnumber > '0')
{
	$sql1 = 'SELECT * FROM `inventory` WHERE `part_number` LIKE :searchpartnumber AND `active` = \'1\'';
	if($selectedquicksearch > '1')
	{
$sql1 .= ' AND `quicksearch` LIKE :quicksearch';
	}
	if($selectedonhand == '1')
	{
		$sql1 .=' AND `'.$loconhand.'` > \'0\'';
	}
foreach($_POST['brand'] as $selectedbrand2){
if($sbi =='1')
{
	if($selectedquicksearch > '1')
	{
 $sql1 .= " AND `manid` = ".$selectedbrand2."";
	}
}
else{
	if($selectedquicksearch > '1')
	{
			if($selectedonhand == '1')
	{
		$sql1 .= " OR `part_number` LIKE :searchpartnumber AND `quicksearch` LIKE :quicksearch AND `'.$loconhand.'` > \'0\' AND `manid` = ".$selectedbrand2."";
	}else
	{
		$sql1 .= " OR `part_number` LIKE :searchpartnumber AND `quicksearch` LIKE :quicksearch AND `manid` = ".$selectedbrand2."";
	}}
	else
		{
			$sql1 .= " OR `part_number` LIKE :searchpartnumber AND `manid` = ".$selectedbrand2."";
		}
}
	$sbi ++;
}
}
else {
//Search by quicksearch
if($selectedquicksearch > '1')
	{
	if($selectedonhand == '1')
	{
		$sql1 = 'SELECT * FROM `inventory` WHERE `quicksearch` LIKE :quicksearch AND `'.$loconhand.'` > \'0\'';
		$startsql = '0';
	}else{
		$sql1 = 'SELECT * FROM `inventory` WHERE `quicksearch` LIKE :quicksearch';
		$startsql = '0';
	}
	}
else
{
	if($selectedonhand == '1')
	{
	$sql1 = "SELECT * FROM `inventory` WHERE `'.$loconhand.'` > \'0\'";
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
}
else{
$sql1 .= "`active` = '1'";
}
}
//Search by brand
//warning This is unsecure - secure in the future
foreach($_POST['brand'] as $selectedbrand2){
if($sbi =='1')
{
	if($selectedquicksearch OR $selectedonhand > '1')
	{
 $sql1 .= " AND `manid` = ".$selectedbrand2."";
	}
	else {
		$sql1 .= " `manid` = ".$selectedbrand2."";
	}
}
else{
	if($selectedquicksearch > '1')
	{
		if($selectedonhand > '1')
	{
		$sql1 .= " OR `quicksearch` LIKE :quicksearch AND `'.$loconhand.'` > \'0\' AND `manid` = ".$selectedbrand2."";
	}else{
		$sql1 .= " OR `quicksearch` LIKE :quicksearch AND `manid` = ".$selectedbrand2."";
	}}
	else
		{
			if($selectedonhand > '1')
			{
			$sql1 .= " OR `'.$loconhand.'` > \'0\' AND `manid` = ".$selectedbrand2."";
		}else{
			$sql1 .= " OR `manid` = ".$selectedbrand2."";
			}}
}
	$sbi ++;
}
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
//if($selectedwidth > '0'){$sth1->bindParam(':width',$selectedwidth);}
//if($selectedratio > '0'){$sth1->bindParam(':ratio',$selectedratio);}
//if($selectedrim > '0'){$sth1->bindParam(':rim',$selectedrim);}
//if($selectedply > '0'){$sth1->bindParam(':ply',$selectedply);}
//if($selectedtraction > '0'){$sth1->bindParam(':traction',$selectedtraction);}
//if($selectedtemperature > '0'){$sth1->bindParam(':temperature',$selectedtemperature);}
//if($selectedtype > '0'){$sth1->bindParam(':type',$selectedtype);}
//if($selectedspeed > '0'){$sth1->bindParam(':speed',$selectedspeed);}
$sth1->execute();
if ($sth1->rowCount() > 0)
{
	while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$partid = $row1['id'];
	$type = $row1['type'];
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
	$load_index = $row1['load'];
	$ply = $row1['ply'];
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
	$sql2 = "SELECT `id`,`price1` FROM `inventory_price` WHERE `partid` = :partid AND `siteid` = :siteid";
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':partid',$partid);
$sth2->bindParam(':siteid',$currentlocationid);
$sth2->execute();
while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
{
$price = $row2['price1'];
}
$installprice = "17.95";
	$displayprice = number_format((float)$price, 2, '.', '');

	$installedprice1 = $price+$installprice;
	$installedprice1a = number_format((float)$installedprice1, 2, '.', '');
	$installedprice2 = $price*'2' +'35.90';
	$installedprice2a = number_format((float)$installedprice2, 2, '.', '');
	$installedprice3 = $price*'3' +'53.85';
	$installedprice3a = number_format((float)$installedprice3, 2, '.', '');
	$installedprice4 = $price*'4' +'71.80';
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
}
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
}
//Display value
$display = '1';
if($selectedply != '0' && $selectedply != $ply )
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
	
	
	if($invoiceid > '0')
{
if($display == '0')
{
}
else {
$sql2= "SELECT `brand` FROM `tire_manufacturers` WHERE `id` = '$brandid'";
$query2 = mysql_query($sql2);
while ($row2 = mysql_fetch_assoc($query2))
	{
	$brand = $row2['brand'];
	$description = $partnumber.", ".$size." ".$brand." ".$model;
	echo "<tr href=\"$tri\"><td><input type=\"checkbox\" name=\"partnumber\" value=\"".$partnumber."\"><td>$partnumber</td><td>$size</td><td>$ply</td><td>$brand</td><td>$model</td><td><b>$displayprice</b></td><td>$sw</td><td>$mileage</td><td>$type</td><td>$load_index</td><td>$dloconhand</td></tr>";
${"ip".$tri} = "<div id=\"$tri\"><div class=\"q1\">1 Installed<br/>Subtotal: $installedprice1a<br/><br/><b>Total w/ tax:$installedprice1b</b><br /><form name=\"form1q$partid\" id=\"form1q$partid\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"articleid\" value=\"$partid\"><input type=\"hidden\" name=\"price\" value=\"$price\"><input type=\"hidden\" name=\"desc\" value=\"$description\"><input type=\"hidden\" name=\"fet\" value=\"$fet\"><input type=\"hidden\" name=\"qty\" value=\"1\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"2\"><input type=\"hidden\" name=\"invoiceid\" value=\"$invoiceid\"><input type=\"submit\" name=\"submit\" class=\"smallbutton\" value=\"Add 1 Tire\"></form></div><div class=\"q2\">2 Installed<br />Subtotal: $installedprice2a<br/><br/><b>Total w/ tax:$installedprice2b</b><br /><form name=\"form2q$partid\" id=\"form2q$partid\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"articleid\" value=\"$partid\"><input type=\"hidden\" name=\"price\" value=\"$price\"><input type=\"hidden\" name=\"desc\" value=\"$description\"><input type=\"hidden\" name=\"fet\" value=\"$fet\"><input type=\"hidden\" name=\"qty\" value=\"2\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"2\"><input type=\"hidden\" name=\"invoiceid\" value=\"$invoiceid\"><input type=\"submit\" name=\"submit\" class=\"smallbutton\" value=\"Add 2 Tires\"></form></div><div class=\"q3\">3 Installed<br />Subtotal: $installedprice3a<br/><br/><b>Total w/ tax:$installedprice3b</b><br /><form name=\"form3q$partid\" id=\"form3q$partid\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"articleid\" value=\"$partid\"><input type=\"hidden\" name=\"price\" value=\"$price\"><input type=\"hidden\" name=\"desc\" value=\"$description\"><input type=\"hidden\" name=\"fet\" value=\"$fet\"><input type=\"hidden\" name=\"qty\" value=\"3\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"2\"><input type=\"hidden\" name=\"invoiceid\" value=\"$invoiceid\"><input type=\"submit\" name=\"submit\" class=\"smallbutton\" value=\"Add 3 Tires\"></form></div><div class=\"q4\">4 Installed<br />Subtotal: $installedprice4a<br/><br/><b>Total w/ tax:$installedprice4b</b><br /><form name=\"form4q$partid\" id=\"form4q$partid\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"articleid\" value=\"$partid\"><input type=\"hidden\" name=\"price\" value=\"$price\"><input type=\"hidden\" name=\"desc\" value=\"$description\"><input type=\"hidden\" name=\"fet\" value=\"$fet\"><input type=\"hidden\" name=\"qty\" value=\"4\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"2\"><input type=\"hidden\" name=\"invoiceid\" value=\"$invoiceid\"><input type=\"submit\" name=\"submit\" class=\"smallbutton\" value=\"Add 4 Tires\"></form></div><br /></div>";
$tri ++;
	}}
}else
{
if($display == '0')
{
}
else {
$sql2= "SELECT `brand` FROM `tire_manufacturers` WHERE `id` ='$brandid'";
$query2 = mysql_query($sql2);
while ($row2 = mysql_fetch_assoc($query2))
	{
	$brand = $row2['brand'];
	$description = $partnumber.", ".$size." ".$brand." ".$model;
	echo "<tr href=\"$tri\"><td><input type=\"checkbox\" name=\"partnumber\" value=\"".$partnumber."\"><td>$partnumber</td><td>$size</td><td>$ply</td><td>$brand</td><td>$model</td><td><b>$displayprice</b></td><td>$sw</td><td>$mileage</td><td>$type</td><td>$load_index</td><td>$dloconhand</td></tr>";
${"ip".$tri} = "<div id=\"$tri\"><div class=\"q1\">1 Installed<br/>Subtotal: $installedprice1a<br/><br /><b>Total w/ tax:$installedprice1b</b><br /><table width=\"100%\"><tr><td><center><form name=\"form1q$partid\" id=\"form1q$partid\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"articleid\" value=\"$partid\"><input type=\"hidden\" name=\"price\" value=\"$price\"><input type=\"hidden\" name=\"desc\" value=\"$description\"><input type=\"hidden\" name=\"fet\" value=\"$fet\"><input type=\"hidden\" name=\"qty\" value=\"1\"><input type=\"hidden\" name=\"type\" value=\"2\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Quote\"></form></center></td></tr><tr><td><center><form name=\"forms$partid\" id=\"form$partid\" action=\"schedule.php\" method=\"post\"><input type=\"hidden\" name=\"articleid\" value=\"$partid\"><input type=\"hidden\" name=\"qty\" value=\"1\"><input type=\"hidden\" name=\"type\" value=\"1\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Schedule\"></form></center></td></tr><tr><td><center><form name=\"form$partid\" id=\"formi$partid\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"articleid\" value=\"$partid\"><input type=\"hidden\" name=\"qty\" value=\"1\"><input type=\"hidden\" name=\"type\" value=\"5\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Invoice\"></form></center></td></tr></table></div>
<div class=\"q2\">2 Installed<br/>Subtotal: $installedprice2a<br/><br /><b>Total w/ tax:$installedprice2b</b><br /><table width=\"100%\"><tr><td><center><form name=\"form2q$partid\" id=\"form2q$partid\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"articleid\" value=\"$partid\"><input type=\"hidden\" name=\"price\" value=\"$price\"><input type=\"hidden\" name=\"desc\" value=\"$description\"><input type=\"hidden\" name=\"fet\" value=\"$fet\"><input type=\"hidden\" name=\"qty\" value=\"2\"><input type=\"hidden\" name=\"type\" value=\"2\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Quote\"></form></center></td></tr><tr><td><center><form name=\"forms$partid\" id=\"form$partid\" action=\"schedule.php\" method=\"post\"><input type=\"hidden\" name=\"articleid\" value=\"$partid\"><input type=\"hidden\" name=\"qty\" value=\"2\"><input type=\"hidden\" name=\"type\" value=\"1\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Schedule\"></form></center></td></tr><tr><td><center><form name=\"form$partid\" id=\"formi$partid\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"articleid\" value=\"$partid\"><input type=\"hidden\" name=\"qty\" value=\"2\"><input type=\"hidden\" name=\"type\" value=\"5\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Invoice\"></form></center></td></tr></table></div><div class=\"q3\">3 Installed<br/>Subtotal: $installedprice3a<br/><br /><b>Total w/ tax:$installedprice3b</b><br /><table width=\"100%\"><tr><td><center><form name=\"form3q$partid\" id=\"form3q$partid\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"articleid\" value=\"$partid\"><input type=\"hidden\" name=\"price\" value=\"$price\"><input type=\"hidden\" name=\"desc\" value=\"$description\"><input type=\"hidden\" name=\"fet\" value=\"$fet\"><input type=\"hidden\" name=\"qty\" value=\"3\"><input type=\"hidden\" name=\"type\" value=\"2\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Quote\"></form></center></td></tr><tr><td><center><form name=\"forms$partid\" id=\"form$partid\" action=\"schedule.php\" method=\"post\"><input type=\"hidden\" name=\"articleid\" value=\"$partid\"><input type=\"hidden\" name=\"qty\" value=\"3\"><input type=\"hidden\" name=\"type\" value=\"1\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Schedule\"></form></center></td></tr><tr><td><center><form name=\"form$partid\" id=\"formi$partid\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"articleid\" value=\"$partid\"><input type=\"hidden\" name=\"qty\" value=\"3\"><input type=\"hidden\" name=\"type\" value=\"5\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Invoice\"></form></center></td></tr></table></div><div class=\"q4\">4 Installed<br/>Subtotal: $installedprice4a<br/><br /><b>Total w/ tax:$installedprice4b</b><br /><table width=\"100%\"><tr><td><center><form name=\"form4q$partid\" id=\"form4q$partid\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"articleid\" value=\"$partid\"><input type=\"hidden\" name=\"price\" value=\"$price\"><input type=\"hidden\" name=\"desc\" value=\"$description\"><input type=\"hidden\" name=\"fet\" value=\"$fet\"><input type=\"hidden\" name=\"qty\" value=\"4\"><input type=\"hidden\" name=\"type\" value=\"2\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Quote\"></form></center></td></tr><tr><td><center><form name=\"forms$partid\" id=\"form$partid\" action=\"schedule.php\" method=\"post\"><input type=\"hidden\" name=\"articleid\" value=\"$partid\"><input type=\"hidden\" name=\"qty\" value=\"4\"><input type=\"hidden\" name=\"type\" value=\"1\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Schedule\"></form></center></td></tr><tr><td><center><form name=\"form$partid\" id=\"formi$partid\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"articleid\" value=\"$partid\"><input type=\"hidden\" name=\"qty\" value=\"4\"><input type=\"hidden\" name=\"type\" value=\"5\"><input type=\"hidden\" name=\"inventorysubmit\" value=\"1\"><input type=\"submit\" class=\"xsmallbutton\" name=\"submit\" value=\"Invoice\"></form></center></td></tr></table></div></div>";
$tri ++;
	}}
}}}
else
{
?>
<tr><td colspan="10"><a href="inventory.php"><b>No results, please try again</b></a></td></tr>
<?php
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
<script type="text/javascript">
$('.selectall').click(function() {
    if ($(this).is(':checked')) {
        $('input:checkbox').attr('checked', true);
    } else {
        $('input:checkbox').attr('checked', false);
    }
});
</script>
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
</select></div><input type="hidden" name="form" value="1"></td><td><a href="inventoryvehicle.php">Search Tire Size by Vehicle</a></td></tr></tr></table></form></div>
        <div id="content"><form name="inventory" action="inventory.php" method="post"><div class="searchright"><label for="onhand">Search on Hand:<input type="checkbox" name="onhand[]" value="1" id="onhand"></label><br /><label for="obligated">Search Obligated Only:<input type="checkbox" name="obligated[]" value="1" id="obligated"></label><br /><label for="ordered">Search on Order:<input type="checkbox" name="order[]" value="1" id="ordered"></label><br /><input type="hidden" name="submit" value="submit"><input type="image" src="images/buttons/search-big.png" alt="updateservice" name="submit">
<br/><a href="inventory.php"><img src="images/buttons/reset.png" alt="reset"></a>
        </div>
        	<table class="searchtable"><tr><th>Quick Search:</th><td><input type="text" name="quicksearch" autocomplete="off" autofocus></td><th>Part Number:</th><td><input type="text" name="partnumber" autocomplete="off"></td></tr>
        	</table>
<table>
<tr><th>Tire Brands:</th></tr><tr class="highlight"><td colspan="7" class="left"><label><input type="checkbox" name="brand" value="0" class="all" checked="checked">All</label></td>
        			<?php
        			$sql1= "SELECT * FROM `tire_manufacturers` WHERE `inactive` = '0' ORDER BY `brand` ASC";
					$tb1 = '0';
					$query1 = mysql_query($sql1);
	while ($row1 = mysql_fetch_assoc($query1))
	{
	$brand = $row1['brand'];
	$id = $row1['id'];
	if($tb1 % '7')
	{
		echo "<td id=\"brand\" class=\"left\"><label><input type=\"checkbox\" name=\"brand[]\" value=\"".$id."\" class=\"brands\" id=\"brands\">".$brand."</label></td>\n";
	}
	else {
	echo "</tr><tr><td id=\"brand\" class=\"left\"><label><input type=\"checkbox\" name=\"brand[]\" value=\"".$id."\" class=\"brands\" id=\"brands\">".$brand."</label></td>\n";
	}
	$tb1 ++;
	}
?></tr></table>
<table>
        		<tr><th>Width:<input type="text" name="width" autocomplete="off"></th><th>Ratio:<input type="text" name="ratio" autocomplete="off"></th><th>Rim:<input type="text" name="rim" autocomplete="off"></th><th>Load Range:<select name="ply"><option value="0"></option><option value="4">B (4 Ply)</option><option value="6">C (6 Ply)</option><option value="8">D (8 Ply)</option><option value="10">E (10 Ply)</option><option value="12">F (12 Ply)</option><option value="14">G (14 Ply)</option><option value="16">H (16 Ply)</option><option value="18">J (18 Ply)</option></select></th></tr>
</table><table>
        		<tr><th class="left">Traction:</th><td><label for="all">All:</label><Input type="radio" name="traction" value="0" id="all" checked="checked"></td><td><label for="AA">AA:</label><Input type="radio" name="traction" value="AA" id="AA"></td><td><label for="A">A:</label><Input type="radio" name="traction" value="A" id="A"></td><td><label for="B">B:</label><Input type="radio" name="traction" value="B" id="B"></td><td><label for="C">C:</label><Input type="radio" name="traction" value="C" id="C"></td></tr>
        		<tr><th class="left">Treadwear:</th><td><label for="treadall">All:</label><Input type="radio" name="treadwear" value="0" id="treadall" checked="checked"></td><td><label for="200">200+</label><Input type="radio" name="treadwear" value="200" id="200"></td><td><label for="400">400+</label><Input type="radio" name="treadwear" value="400" id="400"></td><td><label for="600">600+</label><Input type="radio" name="treadwear" value="600" id="600"></td><td><label for="800">800+</label><Input type="radio" name="treadwear" value="800" id="800"></td></tr>
        		<tr><th class="left">Temperature:</th><td><label for="tempall">All:</label><Input type="radio" name="temperature" value="0" id="tempall" checked="checked"></td><td><label for="tempA">A:</label><Input type="radio" name="temperature" value="A" id="tempA"></td><td><label for="tempB">B:</label><Input type="radio" name="temperature" value="B" id="tempB"></td><td><label for="tempC">C:</label><Input type="radio" name="temperature" value="C" id="tempC"></td></tr>
        		</table><table><tr><th>Type:</th></tr><tr><td><label for="1">All</label><Input type="radio" name="type" value="0" id="1" checked="checked"></td>
        			<?php
        			$sql2 = "SELECT * FROM `inventory_type` WHERE `inactive`='0' ORDER BY `value` ASC";
        			$tb2 = '1';
					$query2 = mysql_query($sql2);
	while ($row2 = mysql_fetch_assoc($query2))
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
if($invoiceid > '0')
{
echo "<input type=\"hidden\" name=\"invoiceid\" value=\"$invoiceid\">";
}
?>
</form></div>
<script>
$('input.all').on('change', function() {
    $('input.brands').not(this).prop('checked', false);
    $('td').removeClass("highlight");
    $(this).closest("tr").toggleClass("highlight", this.checked);
});
$('input.brands').on('change', function() {
    $('input.all').not(this).prop('checked', false);
    $('tr').removeClass("highlight");
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