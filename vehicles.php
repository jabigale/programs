<?php
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Vehicles';
$linkpage = 'vehicles.php';

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
if(isset($_POST['accountid']))
{
$accountid = $_POST['accountid'];
}
if(isset($_POST['invoiceid']))
{
$invoiceid = $_POST['invoiceid'];
}
if(isset($_POST['invoicetype']))
{$newinvoice = $_POST['invoicetype'];}

if(isset($_POST['appointmentid']))
{$appointmentid = $_POST['appointmentid'];}
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
	$accounttype = '1';
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

$sql1 = "INSERT INTO `accounts`(`firstname`,`lastname`,`fullname`,`address`,`address2`,`city`,`state`,`zip`,`phone1`,`sphone1`,`phone2`,`sphone2`,`phone3`,`sphone3`,`phone4`,`sphone4`,`contact1`,`contact2`,`contact3`,`contact4`,`fax`,`email`,`website`,`creditlimit`,`taxid`,`priceclass`,`taxclass`,`RequirePO`,`retail`,`commercial`,`distributor`,`nationalaccount`,`affiliate`,`flag`,`statement`,`allowcod`,`comment`,`insertdate`,`lastactivedate`,`oldtype`) VALUES (:firstname,:lastname,:fullname,:address,:address2,:city,:state,:zip,:phone1,:sphone1,:phone2,:sphone2,:phone3,:sphone3,:phone4,:sphone4,:contact1,:contact2,:contact3,:contact4,:fax,:email,:website,:creditlimit,:taxid,:priceclass,:taxclass,:RequirePO,:retail,:commercial,:distributor,:nationalaccount,:affiliate,:flag,:statement,:allowcod,:comment,:insertdate,:lastactivedate,:oldtype)";

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
$sth1->bindParam(':retail',$retail);
$sth1->bindParam(':commercial',$commercial);
$sth1->bindParam(':distributor',$distributor);
$sth1->bindParam(':nationalaccount',$nationalaccount);
$sth1->bindParam(':affiliate',$affiliate);
$sth1->bindParam(':flag',$flag);
$sth1->bindParam(':statement',$statement);
$sth1->bindParam(':allowcod',$allowcod);
$sth1->bindParam(':comment',$comment);
$sth1->bindParam(':insertdate',$currentday2);
$sth1->bindParam(':lastactivedate',$currentday2);
$sth1->bindParam(':oldtype',$accounttype);
$sth1->execute()or die(print_r($sth1->errorInfo(), true));

$sqli2 = $sqlicxn->query('SELECT `accountid` FROM `accounts` ORDER BY `accountid` DESC LIMIT 1');
while ($row2 = $sqli2->fetch_assoc()) {
$accountid = ($row2['accountid']);
}
}

$sql2 = 'SELECT `firstname`,`lastname` FROM `accounts` WHERE `accountid` = :acctid';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':acctid',$accountid);
$sth2->execute();
while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
{
$fname = $row2['firstname'];
$lname = $row2['lastname'];
}
if(isset($_POST['newvehicle']))
{
$year = $_POST['car-years'];
$make1 = $_POST['car-makes'];
$model = $_POST['car-models'];
$trim = $_POST['car-model-trims'];
$make = ucfirst($make1);
$description = $_POST['description'];
$unit = $_POST['unit'];
$license = $_POST['license'];
$state = $_POST['state'];
$vin = $_POST['vin'];
$accountid = $_POST['accountid'];
$creationdate = date('Y-n-j');
$sql1 = "INSERT INTO `vehicles`(`accountid`,`year`,`make`,`model`,`submodel`,`license`,`state`,`vin`,`description`,`creationdate`,`unit`) VALUES(:accountid,:year,:make,:model,:submodel,:license,:state,:vin,:description,:creationdate,:unit)";
$sql3 = "INSERT INTO `vehicles`(`accountid`,`year`,`make`,`model`) VALUES(:accountid,:year,:make,:model)";
$sth3 = $pdocxn->prepare('INSERT INTO `vehicles`(`accountid`,`year`,`make`,`model`,`submodel`,`license`,`state`,`vin`,`description`,`creationdate`,`unit`) VALUES(:accountid,:year,:make,:model,:submodel,:license,:state,:vin,:description,:creationdate,:unit)');
$sth3->bindParam(':accountid',$accountid);
$sth3->bindParam(':year',$year);
$sth3->bindParam(':make',$make);
$sth3->bindParam(':model',$model);
$sth3->bindParam(':submodel',$trim);
$sth3->bindParam(':license',$license);
$sth3->bindParam(':state',$state);
$sth3->bindParam(':vin',$vin);
$sth3->bindParam(':description',$description);
$sth3->bindParam(':creationdate',$creationdate);
$sth3->bindParam(':unit',$unit);
$sth3->execute()or die(print_r($sth3->errorInfo(), true));
$nvehicleid = $pdocxn->lastInsertId();
if($appointmentid > '0')
{
	header('location: appointment.php?invoiceid='.$appointmentid.'&vehicleid='.$nvehicleid.'&accountid='.$accountid.'&changecustomer=1');
}
if($invoiceid > '0')
{
	header('location: invoice.php?invoiceid='.$invoiceid.'&vehicleid='.$nvehicleid.'&accountid='.$accountid.'');
}
if($newinvoice > '0')
{
header('location: invoice.php?ninvoice=1&typeid='.$newinvoice.'&vehicleid='.$nvehicleid.'&accountid='.$accountid.'');
}
//header('Location: index.php');

}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title><?php echo $title; ?></title>
<link rel="shortcut icon" type="image/x-icon" href="images/icons/favicon.ico" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" >
<link rel="stylesheet" type="text/css" href="style/globalstyle.css" >
<?php

?>
<script type="text/javascript" src="http://www.carqueryapi.com/js/jquery.min.js"></script>
<script type="text/javascript" src="http://www.carqueryapi.com/js/carquery.0.3.4.js"></script>
<script type="text/javascript">
$(document).ready(
function()
{
     //Create a variable for the CarQuery object.  You can call it whatever you like.
     var carquery = new CarQuery();

     //Run the carquery init function to get things started:
     carquery.init();
     
     //Optionally, you can pre-select a vehicle by passing year / make / model / trim to the init function:
     carquery.init('---', '---', '---', 11636);

     //Optional: Pass sold_in_us:true to the setFilters method to show only US models. 
     carquery.setFilters( {sold_in_us:true} );

     //Optional: initialize the year, make, model, and trim drop downs by providing their element IDs
     carquery.initYearMakeModelTrim('car-years', 'car-makes', 'car-models', 'car-model-trims');

     //Optional: set the onclick event for a button to show car data.
     $('#cq-show-data').click(  function(){ carquery.populateCarData('car-model-data'); } );

     //Optional: initialize the make, model, trim lists by providing their element IDs.
     carquery.initMakeModelTrimList('make-list', 'model-list', 'trim-list', 'trim-data-list');

     //Optional: set minimum and/or maximum year options.
     carquery.year_select_min=1945;
     //carquery.year_select_max=2019;
 
     //Optional: initialize search interface elements.
     //The IDs provided below are the IDs of the text and select inputs that will be used to set the search criteria.
     //All values are optional, and will be set to the default values provided below if not specified.
     var searchArgs =
     ({
         body_id:                       "cq-body"
        ,default_search_text:           "Keyword Search"
        ,doors_id:                      "cq-doors"
        ,drive_id:                      "cq-drive"
        ,engine_position_id:            "cq-engine-position"
        ,engine_type_id:                "cq-engine-type"
        ,fuel_type_id:                  "cq-fuel-type"
        ,min_cylinders_id:              "cq-min-cylinders"
        ,min_mpg_hwy_id:                "cq-min-mpg-hwy"
        ,min_power_id:                  "cq-min-power"
        ,min_top_speed_id:              "cq-min-top-speed"
        ,min_torque_id:                 "cq-min-torque"
        ,min_weight_id:                 "cq-min-weight"
        ,min_year_id:                   "cq-min-year"
        ,max_cylinders_id:              "cq-max-cylinders"
        ,max_mpg_hwy_id:                "cq-max-mpg-hwy"
        ,max_power_id:                  "cq-max-power"
        ,max_top_speed_id:              "cq-max-top-speed"
        ,max_weight_id:                 "cq-max-weight"
        ,max_year_id:                   "cq-max-year"
        ,search_controls_id:            "cq-search-controls"
        ,search_input_id:               "cq-search-input"
        ,search_results_id:             "cq-search-results"
        ,search_result_id:              "cq-search-result"
        ,seats_id:                      "cq-seats"
        ,sold_in_us_id:                 "cq-sold-in-us"
     }); 
     carquery.initSearchInterface(searchArgs);

     //If creating a search interface, set onclick event for the search button.  Make sure the ID used matches your search button ID.
     $('#cq-search-btn').click( function(){ carquery.search(); } );
});
</script>
<script>
function uncheck()
{
 var uncheck=document.getElementsByTagName('input');
 for(var i=0;i<uncheck.length;i++)
 {
  if(uncheck[i].type=='checkbox')
  {
   uncheck[i].checked=false;
  }
 }
}
</script>
<?php

?>
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
if($storenum == '1')
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
        	<table id="highlightTable" class="blueTable">
        		<thead>
        		<tr><th>edit</th><th id="vcle">Vehicle</th><th id="license">License</th><th id="vin">VIN</th><th id="instdate">Last Installed Tire (Date)</th><th id="oildate">Last Oil Change (Date)</th></tr>
        	</thead><tbody>
<?php
$sql3 = 'SELECT `id`,`year`,`model`,`make`,`vin`,`license`,`description` FROM `vehicles` WHERE `accountid` = :acctid AND `active` = \'1\' ORDER BY `year` DESC, `make` ASC, `model` ASC';
$sth3 = $pdocxn->prepare($sql3);
$sth3->bindParam(':acctid',$accountid);
$sth3->execute();
if ($sth3->rowCount() > 0)
{
while($row3 = $sth3->fetch(PDO::FETCH_ASSOC))
	{
$year = "";
$make = "";
$model = "";
$displayvehicle = "";
$vehicleid = $row3['id'];
$year = $row3['year'];
$model1 = $row3['model'];
$model = str_replace("?","",$model1);
$make1 = $row3['make'];
$make = str_replace("?","",$make1);
$vin = $row3['vin'];
$license = $row3['license'];
if($year < '1')
{
$displayvehicle = $row3['description'];
$displayvehicle1 = str_replace("?","",$$displayvehicle1a);
}else{
$displayvehicle = $year."&nbsp;".$make."&nbsp;".$model;
}
$tirecomment = '';
$oilchangecomment = '';


echo "\n<tr><td><a href=\"editvehicle.php?vehicleid=".$vehicleid."\"><img src=\"images/icons/setting.jpeg\" width=\"25\"></td><td>".$displayvehicle."</td><td>".$license."</td><td>".$vin."</td>";
//echo "<tr><td><p hidden>$displayvehicle</p><form name=\"schedule\" action=\"accounthistory.php\" method=\"POST\"><input value=\"$displayvehicle\" type=\"submit\" class=\"smallbutton\" name=\"submit\"><input type=\"hidden\" name=\"fullname\" value=\"$fullname\"><input type=\"hidden\" name=\"acccountid\" value=\"$accountid\"><input type=\"hidden\" name=\"vehicleid\" value=\"$vehicleid\"></form></td><td>$license</td><td>$vin</td>";
$foundtire = '0';
$foundoil = '0';


$sql4 = 'SELECT `id`,`invoicedate` FROM `invoice` WHERE `vehicleid` = :vehicleid AND `voiddate` IS NULL AND `type` = \'1\' ORDER BY `invoicedate` DESC';
$sth4 = $pdocxn->prepare($sql4);
$sth4->bindParam(':vehicleid',$vehicleid);
$sth4->execute();
	while($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
	{
	$date = $row4['invoicedate'];
	$displaydate = date('m/d/Y', strtotime($date));
	$tinvoiceid = $row4['id'];
$sql5 = 'SELECT `id`,`qty`,`comment`,`lineitem_typeid` FROM `line_items` WHERE `invoiceid` = :transactionid';
$sth5 = $pdocxn->prepare($sql5);
$sth5->bindParam(':transactionid',$tinvoiceid);
$sth5->execute();
if ($sth5->rowCount() > 0)
{
while($row5 = $sth5->fetch(PDO::FETCH_ASSOC))
	{
	$qty = number_format($row5['qty']);
	$comment = $row5['comment'];
	$lineitem_typeid = $row5['lineitem_typeid'];

    if($lineitem_typeid == '1')
	{
		$tirecomment = $qty." - ".$comment." (".$displaydate.")";
		$foundtire = '1';
		$tireinv = $tinvoiceid;
    }
	
	if($foundtire == '1')
	{
		break;
	}
    }}}

	if($foundtire =='1')
	{
        echo "\n<td>$tirecomment</td>";
        //echo "\n<td><p hidden>$tirecomment</p><form name=\"schedule\" action=\"invoice.php\" method=\"POST\"><input type=\"hidden\" name=\"invoiceid\" value=\"$invoiceid\"><input type=\"submit\" class=\"smallbutton\" name=\"submit\" value=\"$tirecomment\"></form></td>";
}
else{
echo "\n<td></td>\n";
}
if($foundoil =='1')
{
echo "<td>oil</td>\n";
}else
{
    echo "<td></td>";
}
echo "</tr>";
}}
echo "</tbody></table></div>";
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
var f_vcle = 1;
var f_license = 1;
var f_vin = 1;
var f_instdate = 1;
var f_oildate = 1;

$("#vcle").click(function(){
    f_vcle *= -1;
    var n = $(this).prevAll().length;
    sortTable(f_vcle,n);
});
$("#license").click(function(){
    f_license *= -1;
    var n = $(this).prevAll().length;
    sortTable(f_license,n);
});
$("#vin").click(function(){
    f_vin *= -1;
    var n = $(this).prevAll().length;
    sortTable(f_vin,n);
});
$("#instdate").click(function(){
    f_instdate *= -1;
    var n = $(this).prevAll().length;
    sortTable(f_instdate,n);
});
$("#oildate").click(function(){
    f_oildate *= -1;
    var n = $(this).prevAll().length;
    sortTable(f_oildate,n);
});
</script>
<font color="red">Enter a New Vehicle for <b><?php echo $fname." ".$lname; ?></b></font>
<?php
if($appointmentid > '0')
{
	echo '<a href="appointment.php?invoiceid='.$appointmentid.'&accountid='.$accountid.'&changecustomer=1"><button class="cancel">Skip for now</button></a>';
}
if($invoiceid > '0')
{
	echo '<a href="invoice.php?invoiceid='.$invoiceid.'&accountid='.$accountid.'"><button class="cancel">Skip for now</button></a>';
}
if($newinvoice > '0')
{
	echo '<a href="invoice.php?ninvoice=1&typeid='.$newinvoice.'&accountid='.$accountid.'"><button class="cancel">Skip for now</button></a>';
}
?>
<form name="newvehicleform" action="vehicles.php" method="POST">
<table><tr><td>Year: <select name="car-years" id="car-years"></select></td><td>
Make: <select name="car-makes" id="car-makes"></select></td><td>
Model: <select name="car-models" id="car-models"></select></td><td>
Trim: <select name="car-model-trims" id="car-model-trims" onchange="uncheck()"><option value="0">--</option></select></td><td><input type="checkbox" name="itrim" id="itrim" checked="checked"><label for="itrim">(ignore trim)</label></td></tr></table>
<table><tr><td>Description:&nbsp;&nbsp;<input type="textbox" name="description" placeholder="Description"></td><td>Unit #:&nbsp;&nbsp;<input type="textbox" name="unit" placeholder="Unit #"></td></tr>
<tr><td>License & state:&nbsp;&nbsp;<input type="textbox" name="license" placeholder="license" size="8">&nbsp;&nbsp;<input type="textbox" name="state" Value="<?php echo $defaultstate; ?>" size="4"></td><td>VIN:&nbsp;&nbsp;<input type="textbox" name="vin" placeholder="VIN"></td></tr>
<tr><td colspan="2"><center>
<?php
if($appointmentid > '0')
{
echo "<input type=\"hidden\" name=\"appointmentid\" value=\"".$appointmentid."\">";
}
if($invoiceid > '0'){
	echo "<input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\">";
}
if($newinvoice > '0'){
	echo "<input type=\"hidden\" name=\"newinvoice\" value=\"".$newinvoice."\">";
}
?>
<input type="hidden" name="accountid" value="<?php echo $accountid; ?>"><input type="hidden" name="newvehicle" value="1"><input type="hidden" name="submit" value="1"><input type="submit" class="quotebutton" value="Add Vehicle"></center></td></tr>
</table></form></div>
</body></html>
