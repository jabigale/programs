<?php
//include mysql file
header("Expires: Mon, 01 Jan 2018 05:00:00 GMT");
header("Last-Modified: ".gmdate( 'D, d M Y H:i:s')." GMT");
header("Cache-Control: private, no-store, max-age=0, no-cache, must-revalidate, post-check=0, pre-check=0");
header("Pragma: no-cache");
include_once ('scripts/mysql.php');
$currentday = date('Y-n-j');
$currentday2 = date('Y-m-d');
$defaultstate = "WI";
$yearywi = date('Y', strtotime('+1 Year', strtotime($currentyear)));
$dbyear = '1965';
include_once ('scripts/global.php');
if(isset($_POST['form']))
{
setcookie($cookie1_name, $cookie1_value, time() - (3600), "/");
setcookie($cookie2_name, $cookie2_value, time() - (3600), "/");
setcookie($cookie3_name, $cookie3_value, time() - (3600), "/");
setcookie($cookie4_name, $cookie4_value, time() - (3600), "/");
$userid = $_POST['user'];
$locationid = $_POST['location'];
$sth1 = $pdocxn->prepare('SELECT `username` FROM `employees` WHERE `id` = :userid');
$sth1->bindParam(':userid',$userid);
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
$username = $row1['username'];
}
$sth2 = $pdocxn->prepare('SELECT `storename` FROM `locations` WHERE `id` = :locationid');
$sth2->bindParam(':locationid',$locationid);
$sth2->execute();
while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
{
$storename = $row2['storename'];
}
$cookie1_value = $userid;
$cookie2_value = $username;
$cookie3_value = $locationid;
$cookie4_value = $storename;
$cookie5_value = $_POST['password'];
setcookie($cookie1_name, $cookie1_value, time() + (86400 * 30), "/");
setcookie($cookie2_name, $cookie2_value, time() + (86400 * 30), "/");
setcookie($cookie3_name, $cookie3_value, time() + (86400 * 30), "/");
setcookie($cookie4_name, $cookie4_value, time() + (86400 * 30), "/");
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
if($_POST['editvehicle']&&$_POST['editvehicle']=='1')
{
//edit vehicle info
$vehicledesc = $_POST['vehiclename'];
$vin = $_POST['vehiclevin'];
$license = $_POST['license'];
$state = $_POST['state'];
$invoiceid = $_POST['invoiceid'];
$vehicleid = $_POST['vehicleid'];
$postedyear = $_POST['vehicleyear'];
$postedmake = $_POST['vehiclemake'];
$postedmodel = $_POST['vehiclemodel'];
if($postedyear > '1')
{
$sth1 = $pdocxn->prepare('UPDATE `vehicles` SET `vin`=:vin,`state`=:nstate,`license`=:license,`year`=:nyear,`make`=:make,`model`=:model WHERE `id` = :vehicleid');
$sth1->bindParam(':nyear',$postedyear);
$sth1->bindParam(':make',$postedmake);
$sth1->bindParam(':model',$postedmodel);
}else{
$sth1 = $pdocxn->prepare('UPDATE `vehicles` SET `vin`=:vin,`state`=:nstate,`license`=:license,`description`=:ndescription WHERE `id` = :vehicleid');
$sth1->bindParam(':ndescription',$vehicledesc);
}
$sth1->bindParam(':vin',$vin);
$sth1->bindParam(':nstate',$state);
$sth1->bindParam(':license',$license);
$sth1->bindParam(':vehicleid',$vehicleid);
$sth1->execute();
$sth1 = $pdocxn->prepare('UPDATE `'.$invtable.'` SET `abvvehicle`=:abvvehicle WHERE `id` = :invoiceid');
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->bindParam(':abvvehicle',$vehicledesc);
$sth1->execute();
}






$sql5 = 'SELECT * FROM `vehicles` WHERE `id` = :vehicleid';
$sth5 = $pdocxn->prepare($sql5);
$sth5->bindParam(':vehicleid',$vehicleid);
$sth5->execute();
if ($sth5->rowCount() > 0)
{
while($row5 = $sth5->fetch(PDO::FETCH_ASSOC))
	{
	$currentvehicleyear = $row5['year'];
	$currentvehiclemodel = $row5['model'];
	$currentvehiclemake = $row5['make'];
	$currentvin1 = $row5['vin'];
	$vinlen = strlen($currentvin1);
	if($vinlen = '16')
	{
		$v2 = substr($currentvin1,-8);
		$v1 = substr($currentvin1,0,9);
		$currentvin = $v1."<u><b>".$v2."</b></u>";
	}else{
		$vin = $vin1;
	}
	$submodel = $row5['submodel'];
	$engine = $row5['engine'];
	$license = $row5['license'];
	$currentlicense = $license;
	$vehiclestate = $row5['state'];
	$currentvehiclestate = $vehiclestate;
	if($vehiclestate > '0')
	{$vehiclestate1 = $vehiclestate;}
	else
	{$vehiclestate1 = '';}
	$description = $row5['description'];
if($currentvehicleyear > '1')
{
$dvehicleinfo = "<b>".$currentvehicleyear." ".$currentvehiclemake." ".$currentvehiclemodel." ".$engine."</b></td><td href=\"vehicleinfo\">VIN: ".$currentvin."</td><td href=\"vehicleinfo\">License: ".$license." ".$vehiclestate."";
$dvehicleinfo1 = "<font color=\"red\">".$currentvehicleyear." ".$currentvehiclemake." ".$currentvehiclemodel." ".$engine."</font>";
$dlicensestate = "<font color=\"red\">License: ".$license." ".$vehiclestate1."</font>";
$dvehicleinfo2 = "<b>".$currentvehicleyear." ".$currentvehiclemake." ".$currentvehiclemodel." ".$engine."</b>";
$dvehicleinfo2nb = $currentvehicleyear." ".$currentvehiclemake." ".$currentvehiclemodel." ".$engine;
$selectvehicleinfo = "".$currentvehicleyear." ".$currentvehiclemake." ".$currentvehiclemodel." ".$engine."   VIN: ".$currentvin."  License: ".$license." ";
}
else
{
$dvehicleinfo = "".$description."</td><td href=\"vehicleinfo\">VIN: ".$currentvin."</td><td href=\"vehicleinfo\">License: ".$license." ".$vehiclestate1."";
$dvehicleinfo1 = "<font color=\"red\">".$description."</font>";
$dvehicleinfo1a = "<font color=\"red\">VIN: ".$currentvin."</font>";
$dlicensestate = "<font color=\"red\">License: ".$license." (".$vehiclestate.")</font>";
$dvehicleinfo2 = "<b>".$description."</b>";
$dvehicleinfo2nb = $description;
$selectvehicleinfo = "".$description."   VIN: ".$currentvin."   License: ".$license."";
}
}}




if($currentvehicleyear > '1')
{
	echo "\n<form name=\"changevehicle\" method=\"post\" action=\"editvehicle.php\"><table class=\"righttable\"><tr><td class=\"left\"><select name=\"vehicleyear\"><option value=\"".$currentvehicleyear."\">".$currentvehicleyear."</option>";
while($yearywi > $dbyear)
{
echo "<option value=\"".$yearywi."\">".$yearywi."</option>";
$yearywi --;
}
echo "</select></td><td class=\"left\"><input type=\"hidden\" name=\"editvehicle1\" value=\"1\"><input type=\"hidden\" name=\"vehicleid\" value=\"".$vehicleid."\"><input type=\"text\" name=\"vehiclemake\" value=\"".$currentvehiclemake."\"></td><td><input type=\"text\" name=\"vehiclemodel\" value=\"".$currentvehiclemodel."\"></td></tr><tr><td class=\"left\">VIN:</td><td class=\"left\" colspan=\"2\"><input type=\"text\" name=\"vehiclevin\" value=\"".$currentvin1."\"></td></tr><tr><td class=\"left\">License:</td><td class=\"left\"><input type=\"text\" name=\"license\" value=\"".$currentlicense."\" size=\"6\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;State:<input type=\"text\" name=\"state\" size=\"1\" value=\"".$currentvehiclestate."\"></td></tr><tr><td colspan=\"3\" class=\"center\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"editvehicle\" value=\"1\"><input type=\"hidden\" name=\"submit\" value=\"1\"><input type=\"image\" src=\"images/buttons/checkmark.png\" alt=\"editvehicle\" name=\"submit\" width=\"25\"></td></tr></table></form></div></div>";

}else{
echo "\n<div id=\"editvehicleinfo\"><div class=\"q1\"><form name=\"changevehicle\" method=\"post\" action=\"".$pagelink2."\"><table class=\"righttable\"><tr><td class=\"left\">Vehicle Info:</td><td class=\"left\" colspan=\"2\"><input type=\"hidden\" name=\"editvehicle1\" value=\"1\"><input type=\"hidden\" name=\"vehicleid\" value=\"".$vehicleid."\"><input type=\"text\" name=\"vehiclename\" value=\"".$dvehicleinfo2nb."\"></td></tr><tr><td class=\"left\">VIN:</td><td class=\"left\" colspan=\"2\"><input type=\"text\" name=\"vehiclevin\" value=\"".$currentvin1."\"></td></tr><tr><td class=\"left\">License:</td><td class=\"left\"><input type=\"text\" name=\"license\" value=\"".$currentlicense."\" size=\"6\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;State:<input type=\"text\" name=\"state\" size=\"1\" value=\"".$currentvehiclestate."\"></td></tr><tr><td colspan=\"3\" class=\"center\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"editvehicle\" value=\"1\"><input type=\"hidden\" name=\"submit\" value=\"1\"><input type=\"image\" src=\"images/buttons/checkmark.png\" alt=\"editvehicle\" name=\"submit\" width=\"25\"></td></tr></table></form></div></div>";
}
?>