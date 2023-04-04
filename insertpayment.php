<?php
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Insert Payment';
$linkpage = 'insertpayment.php';

session_start();
date_default_timezone_set('America/Chicago');
$currentdate = date('Y-n-j H:i:s');
$currentday = date('Y-n-j');
$currentdate = date('Y-m-d');
$showhistory = '0';
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


if(isset($_POST['search']))
{
	//Zero Values Prior
	$firstname = '0';
	$lastname='0';
	$phone = '0';
	$account = '0';
	$newinvoice = $_POST['invoice'];
	$firstname = $_POST['firstname'];
	$lastname = $_POST['lastname'];
	$phone = $_POST['phone'];
	$account = $_POST['acctnumber'];
	$searchnumber = preg_replace('/\D/', '', $phone);
	$accounttype = $_POST['accttype'];
	$invoiceid = $_POST['invoiceid'];
	$schedule = $_POST['schedule'];
//fkmfkm
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
<link rel="stylesheet" type="text/css" href="style/accountstyle.css" >
<script type="text/javascript" src="scripts/jquery-latest.js"></script>
<script type="text/javascript" src="scripts/script.js"></script>
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
if($newinvoice == '0')
{
if($currentlocationid == '1')
{
echo "<div id=\"header\">".$headernavigation."</div>";
}
else{
echo "<div id=\"header2\">".$headernavigation2."</div>";
}}else{
	echo "<div id=\"ciheader\"><br /><br /><b>Please Select Customer or Vehicle Below</b></div>";
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
        <div id="content">

        	<div id="left">
<table id="highlightTable" class="blueTable">
<thead>
<tr>
<th>Last Name</th>
<th>First name</th>
<th>Phone</th>
<th>City</th>
<th>Select Customer</th>
</tr>
</thead>
<tbody>
<?php
$tri = '1';
if($accounttype = '1')
{
	$acctsql = " AND `retail` = '1'";
}
if($accounttype = '2')
{
	$acctsql = " AND `vendor` = '1'";
}
if($accounttype = '3')
{
	$acctsql = " AND `distributor` = '1'";
}
if($accounttype = '4')
{
	$acctsql = " AND `nationalaccount` = '1'";
}
if($accounttype = '5')
{
	$acctsql = " AND `affiliate` = '1'";
}
if($accounttype = '6')
{
	$acctsql = "";
}
if($lastname > '0')
	{
$sql1 = 'SELECT * FROM `accounts` WHERE `lastname` LIKE :lastname';
	}
else
{
	$sql1 = "SELECT * FROM `accounts` WHERE ";
}
if($firstname > '0' && $lastname > '0')
	{
		$sql1 .= " AND `firstname` LIKE :firstname";
	}
if($firstname > '0' && $lastname < '1')
	{
		$sql1 .= " `firstname` LIKE :firstname";
	}	
if($searchnumber > '0' && $lastname < '1' && $firstname < '1')
	{
$sql1 = 'SELECT * FROM `accounts` WHERE `sphone1` LIKE :phone OR `sphone2` LIKE :phone OR `sphone3` LIKE :phone OR `sphone4` LIKE :phone';
	}
if($searchnumber > '0' && $lastname > '0' && $firstname > '0')
	{
	$sql1 .= " AND `sphone1` LIKE :phone OR `lastname` LIKE :lastname AND `firstname` LIKE :firstname AND `sphone2` LIKE :phone OR `lastname` LIKE :lastname AND `firstname` LIKE :firstname AND `sphone3` LIKE :phone OR `lastname` LIKE :lastname AND `firstname` LIKE :firstname AND `sphone4` LIKE :phone";
}
if($searchnumber > '0' && $lastname < '1' && $firstname > '0')
	{
	$sql1 .= " AND `sphone1` LIKE :phone OR `firstname` LIKE :firstname AND `sphone2` LIKE :phone OR `firstname` LIKE :firstname AND `sphone3` LIKE :phone OR `firstname` LIKE :firstname AND `sphone4` LIKE :phone";
}
if($searchnumber > '0' && $lastname > '0' && $firstname < '1')
	{
	$sql1 .= " AND `sphone1` LIKE :phone OR `lastname` LIKE :lastname AND `sphone2` LIKE :phone OR `lastname` LIKE :lastname AND `sphone3` LIKE :phone OR `lastname` LIKE :lastname AND `sphone4` LIKE :phone";
}
if($changecustomer =='2')
{
$sql1 .= " AND `oldtype` = '2' ORDER BY `lastname` ASC, `firstname` ASC";
}else{
$sql1 .= " AND `oldtype` = '1' ORDER BY `lastname` ASC, `firstname` ASC";
}
$sth1 = $pdocxn->prepare($sql1);
if($lastname > '0')
{
$searchlastname = $lastname."%";
$sth1->bindParam(':lastname',$searchlastname);
}
if($firstname > '0')
{
$searchfirstname = $firstname."%";
$sth1->bindParam(':firstname',$searchfirstname);
}
if($searchnumber > '0')
{
$searchphone = "%".$searchnumber."%";
$sth1->bindParam(':phone',$searchphone);
}
$sth1->execute();
if($sth1->rowCount() > 0) {
	while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$accountid = $row1['accountid'];
	$databasefname = $row1['firstname'];
	$fname = stripslashes($databasefname);
	$databaselname = $row1['lastname'];
	$lname = stripslashes($databaselname);
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
if($phone1 > '0')
	{
		$dphone1 = "<tr><td class=\"left\" colspan=\"2\">Phone 1: <b>".$phone1."</b></td><td colspan=\"2\">Contact: <b>".$contact1."</b></td></tr>";
	}
	else
		{
			$dphone1 = "";
		}
if($phone2 > '0')
	{
		$dphone2 = "<tr><td class=\"left\" colspan=\"2\">Phone 2: <b>".$phone2."</b></td><td colspan=\"2\">Contact: <b>".$contact2."</b></td></tr>";
	}
	else
		{
			$dphone2 = "";
		}
if($phone3 > '0')
	{
		$dphone3 = "<tr><td class=\"left\" colspan=\"2\">Phone 3: <b>".$phone3."</b></td><td colspan=\"2\">Contact: <b>".$contact3."</b></td></tr>";
	}
	else
		{
			$dphone3 = "";
		}
if($phone4 > '0')
	{
		$dphone4 = "<tr><td class=\"left\" colspan=\"2\">Phone 4: <b>".$phone4."</b></td><td colspan=\"2\">Contact: <b>".$contact4."</b></td></tr>";
	}
	else
		{
			$dphone4 = "";
		}
if($fax > '0')
	{
		$dfax = "<tr><td colspan=\"2\" class=\"left\">Fax: <b>".$fax."</b></td></tr>";
	}
	else
		{
			$dfax = "";
		}
if($creditlimit > '0')
	{
		$creditlimit = $creditlimit;
	}
	else
		{
			$creditlimit = "0";
		}
if($taxid > '0')
	{
		$taxid = $taxid;
	}
	else
		{
			$taxid = "0";
		}
if($requirepo == '1')
	{
		$requirepo = "Yes";
	}
	else
		{
			$requirepo = "No";
		}
if($priceclass == '1')
	{
		$dpriceclass = "Consumer";
	}
	else
		{
			$dpriceclass = "Resale";
		}
		//n2update
		$dtaxclass = "Consumer";
		$dlastactivedate = "12/20/2017";
		if($address2 > '1')
		{
		$daddress2 = "<tr><td colspan=\"3\">$address2</td></tr>";
		}
		else {
			$daddress2 = "";
		}
	echo "<tr href=\"$tri\"><td><b>$lname</b></td><td><b>$fname</b></td><td>$phone1</td><td>$city</td>";

			echo "<td class=\"center\"><a href=\"insertpayment.php?accountid=".$accountid."\" class=\"no-decoration\"><input type=\"button\" class=\"smallbutton\" value=\"Enter Payment\"></a></td>";

		echo "</tr>\n";

		$balsql1 = 'SELECT SUM(`total`) AS `storebalance` FROM `journal` WHERE `accountid` = :accountid AND `siteid` = :siteid';
		$balsth1 = $pdocxn->prepare($balsql1);
		$balsth1->bindParam(':accountid',$accountid);
		$balsth1->bindParam(':siteid',$currentlocationid);
		$balsth1->execute();
		while($balrow1 = $balsth1->fetch(PDO::FETCH_ASSOC))
		{
		$storebalance1 = $balrow1['storebalance'];
		$storebalance = money_format('%(#0.2n',$storebalance1);
		}
		
		$balsql2 = 'SELECT SUM(`total`) AS `totalbalance` FROM `journal` WHERE `accountid` = :accountid';
		$balsth2 = $pdocxn->prepare($balsql2);
		$balsth2->bindParam(':accountid',$accountid);
		$balsth2->execute();
		while($balrow2 = $balsth2->fetch(PDO::FETCH_ASSOC))
		{
		$totalbalance1 = $balrow2['totalbalance'];
		$totalbalance = money_format('%(#0.2n',$totalbalance1);
		}
${"ip".$tri} = "<div id=\"$tri\"><div class=\"q1\"><table class=\"righttable\"><tr><td class=\"center\">";
${"ip".$tri} .= "Account Balance at this Store:</td><td><b>$".$storebalance1;

${"ip".$tri} .= "</b></td><td calss=\"center\">Balance at ALL Stores:</td><td class=\"center\"><b>$".$totalbalance1."</b></td></tr><tr><td colspan=\"4\" class=\"left\">$address&nbsp;$city,&nbsp;$state&nbsp;$zip</td></tr>".$daddress2."".$dphone1."".$dphone2."".$dphone3."".$dphone4."".$dfax."<tr></table></div><div class=\"q3\"><table class=\"righttable\"><tr><td class=\"center\">";

if($newinvoice > '0' OR $invoiceid > '0' OR $appointmentid > '0')
{
if($schedule > '0')
	{
	${"ip".$tri} .= "Click Vehicle Below to Select</td><td class=\"center\" colspan=\"2\"><form name=\"updatecustomer\" action=\"vehicles.php\" method=\"post\"><input type=\"hidden\" name=\"changecustomer\" value=\"1\"><input type=\"hidden\" name=\"appointmentid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"submit\" name=\"submit\" class=\"smallbutton\" value=\"Add a Vehicle\" /></form></td></tr>";
	}else{
if($appointmentid > '0')
{
${"ip".$tri} .= "Click Vehicle Below to Select</td><td class=\"center\" colspan=\"2\"><form name=\"updatecustomer\" action=\"vehicles.php\" method=\"post\"><input type=\"hidden\" name=\"changecustomer\" value=\"1\"><input type=\"hidden\" name=\"appointmentid\" value=\"".$appointmentid."\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"submit\" name=\"submit\" class=\"smallbutton\" value=\"Add a Vehicle\" /></form></td></tr>";
}
if($invoiceid > '0'){
${"ip".$tri} .= "Click Vehicle Below to Select</td><td class=\"center\" colspan=\"2\"><form name=\"updatecustomer\" action=\"vehicles.php\" method=\"post\"><input type=\"hidden\" name=\"changecustomer\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"submit\" name=\"submit\" class=\"smallbutton\" value=\"Add a Vehicle\" /></form></td></tr>";
}
if($newinvoice > '0'){
${"ip".$tri} .= "Click Vehicle Below to Select</td><td class=\"center\" colspan=\"2\"><form name=\"updatecustomer\" action=\"vehicles.php\" method=\"post\"><input type=\"hidden\" name=\"changecustomer\" value=\"1\"><input type=\"hidden\" name=\"newinvoice\" value=\"".$newinvoice."\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"submit\" name=\"submit\" class=\"smallbutton\" value=\"Add a Vehicle\" /></form></td></tr>";
}}}
else
{
${"ip".$tri} .= "<form name=\"addvehicle\" action=\"vehicles.php\" method=\"POST\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"submit\" name=\"vehiclelist\" class=\"smallbutton\" value=\"Detailed List\" /></td><td class=\"center\" colspan=\"2\"><input type=\"submit\" name=\"addvehicle\" class=\"smallbutton\" value=\"Add a Vehicle\" /></form></td></tr>";
}
${"ip".$tri} .= "<tr><th>Vehicle</th><th>License</th><th>VIN</th></tr>\n";
$sql2 = 'SELECT * FROM `vehicles` WHERE `accountid` = :accountid AND `active` = \'1\' ORDER BY `year` DESC, `make` ASC, `model` ASC';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':accountid',$accountid);
$sth2->execute();
if ($sth2->rowCount() > 0)
{
while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
	{
$year = "";
$make = "";
$model = "";
	$vehicleid = $row2['id'];
	$year = $row2['year'];
	$model1 = $row2['model'];
$model = str_replace("?","",$model1);
	$make1 = $row2['make'];
$make = str_replace("?","",$make1);
	$vin1 = $row2['vin'];
	$vinlen = strlen($vin1);
	if($vinlen = '16')
	{
		$v2 = substr($vin1,-8);
		$v1 = substr($vin1,0,9);
		$vin = $v1."<b>".$v2."</b>";
	}else{
		$vin = $vin1;
	}
	$license = $row2['license'];
if($year < '1')
{
$displayvehicle = $row2['description'];
$displayvehicle1 = str_replace("?","",$$displayvehicle1a);
}else{
$displayvehicle = $year."&nbsp;&nbsp;".$make."&nbsp;&nbsp;".$model;
}

if($newinvoice > '0')
{
${"ip".$tri} .= "\n<tr><td colspan=\"3\" class=\"left\"><form name=\"newinvoice\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"new\" value=\"1\"><input type=\"hidden\" name=\"type\" value=\"".$newinvoice."\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"hidden\" name=\"vehicleid\" value=\"".$vehicleid."\"><input type=\"submit\" name=\"submit\" class=\"smallbutton\" value=\"".$displayvehicle."\"/> ".$license." ".$vin." </form></td></tr>";
}else{
	if($schedule > '0')
	{
	${"ip".$tri} .= "\n<tr><td colspan=\"3\" class=\"left\"><form name=\"updatecustomer\" action=\"appointment.php\" method=\"post\"><input type=\"hidden\" name=\"changecustomer\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"hidden\" name=\"vehicleid\" value=\"".$vehicleid."\"><input type=\"submit\" name=\"submit\" class=\"smallbutton\" value=\"".$displayvehicle."\"/> ".$license." ".$vin." </form></td></tr>";
	}else{
if($invoiceid > '0' OR $appointmentid > '0')
{
if($appointmentid > '0')
{
${"ip".$tri} .= "\n<tr><td colspan=\"3\" class=\"left\"><form name=\"updatecustomer\" action=\"appointment.php\" method=\"post\"><input type=\"hidden\" name=\"changecustomer\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$appointmentid."\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"hidden\" name=\"vehicleid\" value=\"".$vehicleid."\"><input type=\"submit\" name=\"submit\" class=\"smallbutton\" value=\"".$displayvehicle."\"/> ".$license." ".$vin." </form></td></tr>";
}
else{
${"ip".$tri} .= "\n<tr><td colspan=\"3\" class=\"left\"><form name=\"updatecustomer\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"changecustomer\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"hidden\" name=\"vehicleid\" value=\"".$vehicleid."\"><input type=\"submit\" name=\"submit\" class=\"smallbutton\" value=\"".$displayvehicle."\"/> ".$license." ".$vin." </form></td></tr>";
}}
else
{
${"ip".$tri} .= "\n<tr><td class=\"left\"><a href=\"accounthistory.php?vehicleid=".$vehicleid."\" class=\"no-decoration\"><input type=\"button\" class=\"smallbutton\" name=\"submit\" value=\"$displayvehicle\"></a></td><td>$license</td><td>$vin</td></tr>\n";
}}}
}
${"ip".$tri} .= "</table></div></div>";
$tri ++;
}
else {
${"ip".$tri} .= "</table></div></div>";
$tri ++;
}}}else{
	echo "<tr href=\"insertpayment.php".$invlink.$apptlink.$newinvoicelink.$schedulelink."\"><td colspan=\"3\"><a href=\"insertpayment.php".$invlink.$apptlink.$newinvoicelink.$schedulelink."\">No Results, try again</a></td>";
	echo "<td colspan=\"3\"><a href=\"newinsertpayment.php".$invlink.$apptlink.$newinvoicelink.$schedulelink."\">Create a New Account</a></td></tr>\n";

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
<script>
function highlight(tableIndex) {
    // Just a simple check. If .highlight has reached the last, start again
    if( (tableIndex+1) > $('#highlightTable tbody tr').length )
        tableIndex = 0;
    // Element exists?
    if($('#highlightTable tbody tr:eq('+tableIndex+')').length > 0)
    {
        // Remove other highlights
        $('#highlightTable tbody tr').removeClass('highlight');
        // Highlight your target
        $('#highlightTable tbody tr:eq('+tableIndex+')').addClass('highlight');
    }
}
$('#goto_first').click(function() {
    highlight(0);
});
$('#goto_prev').click(function() {
    highlight($('#highlightTable tbody tr.highlight').index() - 1);
});
$('#goto_next').click(function() {
    highlight($('#highlightTable tbody tr.highlight').index() + 1);
});
$('#goto_last').click(function() {
    highlight($('#highlightTable tbody tr:last').index());
});
$(document).keydown(function (e) {
    switch(e.which) 
    {
        case 38:
            $('#goto_prev').trigger('click');
            break;
        case 40:
            $('#goto_next').trigger('click');
            break;
    }
 });
</script>
</body>
</html>
<?php
}
else {
if(isset($_POST['invoiceid']))
{
$invoiceid = $_POST['invoiceid'];
}
if(isset($_GET['accountid']))
{
    $accountid = $_GET['accountid'];
$showhistory = '1';
$startdate = date('Y-m-d', strtotime('-1 year', strtotime($currentday)));
$enddate = date('Y-m-d', strtotime('+1 day', strtotime($currentday)));
}
if(isset($_POST['submit']))
{

if(isset($_POST['inserttransaction']))
{

    if(isset($_POST['roa']) && $_POST['roa'] == '1')
{
    $roa = '1';
}else{
    $roa = '0';
}
$accountid = $_POST['accountid'];
$transdate = $_POST['transdate'];
$transamount = $_POST['transamount'];
//$transcomment = $_POST['comment'];
$checknumber = $_POST['checknumber'];
$lineitem_typeid = $_POST['paymenttype'];
$siteid = $_POST['paymentsite'];
$transtype = '6';


$sth2 = $pdocxn->prepare('INSERT INTO `invoice`(`userid`,`type`,`location`,`creationdate`,`invoicedate`,`accountid`,`roa`) VALUES (:userid,:typeid,:location,:creationdate,:invoicedate,:accountid,:roa)');
$sth2->bindParam(':userid',$currentid);
$sth2->bindParam(':typeid',$transtype);
$sth2->bindParam(':location',$currentlocationid);
$sth2->bindParam(':creationdate',$currentdate);
$sth2->bindParam(':invoicedate',$transdate);
$sth2->bindParam(':accountid',$accountid);
$sth2->bindParam(':roa',$roa);
$sth2->execute()or die(print_r($sth2->errorInfo(), true));
$invoiceid = $pdocxn->lastInsertId();

$linenumber = '1';
$qty = '1';
$sth2 = $pdocxn->prepare('INSERT INTO `line_items`(`invoiceid`,`comment`,`linenumber`,`qty`,`amount`,`totallineamount`,`lineitem_typeid`) VALUES (:invoiceid,:comment,:linenumber,:qty,:amount,:totallineamount,:lineitem_typeid)');
$sth2->bindParam(':invoiceid',$invoiceid);
$sth2->bindParam(':comment',$checknumber);
$sth2->bindParam(':linenumber',$linenumber);
$sth2->bindParam(':qty',$qty);
$sth2->bindParam(':amount',$transamount);
$sth2->bindParam(':totallineamount',$transamount);
$sth2->bindParam(':lineitem_typeid',$lineitem_typeid);
$sth2->execute()or die(print_r($sth2->errorInfo(), true));

$sql2 = 'SELECT `journal` FROM `invoice_type` WHERE `id` = :typeid';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':typeid',$transtype);
$sth2->execute();
while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
{
 $confirmjournal = $row2['journal'];
}

if($confirmjournal = '1')
{
$transamount2 = $transamount * -1;
$sql2 = 'INSERT INTO `journal` (`invoiceid`,`accountid`,`total`,`invoicedate`,`journaltype`,`siteid`) VALUES (:invoiceid,:accountid,:total,:invoicedate,:journaltype,:siteid)';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':invoiceid',$invoiceid);
$sth2->bindParam(':accountid',$accountid);
$sth2->bindParam(':total',$transamount2);
$sth2->bindParam(':invoicedate',$transdate);
$sth2->bindParam(':journaltype',$transtype);
$sth2->bindParam(':siteid',$currentlocationid);
$sth2->execute()or die(print_r($sth2->errorInfo(), true));
}

if(isset($_POST['linktoinvoiceid'])){
	if(!empty($_POST['linktoinvoiceid'])){
	// Loop to store and display values of individual checked checkbox.
	foreach($_POST['linktoinvoiceid'] as $linktoid){

$sql8 = "SELECT `linkid` FROM `translink` WHERE `transid` = :transid";
$sth8 = $pdocxn->prepare($sql8);
$sth8->bindParam(':transid',$linktoid);
$sth8->execute();
while($row8 = $sth8->fetch(PDO::FETCH_ASSOC))
{
	$linkid = $row8['linkid'];
}
$paysql = "INSERT INTO `translink`(`transid`,`linktoid`,`amount`) VALUES (:transid,:linktoid,:amount)";
$paysth = $pdocxn->prepare($paysql);
$paysth->bindParam(':transid',$invoiceid);
$paysth->bindParam(':linktoid',$linkid);
$paysth->bindParam(':amount',$transamount);
$paysth->execute();
}}}
}

	$showhistory = '1';
	$accountid = $_POST['accountid'];
	$selectedtype = $_POST['invoicetype'];

if(isset($_POST['startdate']))
	{
		$startdate = $_POST['startdate'];
		$enddate = $_POST['enddate'];
	}
else {
	$startdate = date('Y-m-d', strtotime('-1 year', strtotime($currentday)));
	$enddate = date('Y-m-d', strtotime('+1 day', strtotime($currentday)));
}
}
if($showhistory == '1')
{
	if($accountid > '0')
	{
	$sth5 = $pdocxn->prepare('SELECT `firstname`,`lastname` FROM `accounts` WHERE `accountid` = :accountid');
	$sth5->bindParam(':accountid',$accountid);
	$sth5->execute();
	while($row5 = $sth5->fetch(PDO::FETCH_ASSOC))
	{
	$databasefname = $row5['firstname'];
	$firstname = stripslashes($databasefname);
	$databaselname = $row5['lastname'];
	$lastname = stripslashes($databaselname);
	$fullname = $firstname." ".$lastname;
	}}else{
		$fullname = "No Customer Selected";
	}
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
<title><?php echo $title.' - '.$fullname; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" >
<link rel="stylesheet" type="text/css" href="style/globalstyle.css" >
<link rel="stylesheet" type="text/css" href="style/paymentstyle1.css" >
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
<?php
if(isset($_POST['inserttransaction']))
{
?>
<script>
<!--
window.onload = function() {
window.open('printpayment.php?invoiceid=<?php echo $invoiceid; ?>');
}
-->
</script>
<?php
}
?>
</head>
<body>
<?php
if($currentlocationid == '1')
{
echo "<div id=\"header\">".$headernavigation."</div>";
}
else{
echo "<div id=\"header2\">".$headernavigation2."</div>";
}
?>
<div id="selecteduserfullwidth"><form name="current1" action="index.php" method="POST"><table id="floatleft" width="100%"><tr><td class="currentuser">Current User:</td><td class="currentitem"><div class="styled-select black rounded">
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
</select></div><input type="hidden" name="form" value="1"></td></form><form name="update" action="accounthistory.php" method="POST"><input type="hidden" name="accountid" value="<?php echo $accountid; ?>">

<td>Start Date: <input type="date" name="startdate" value="<?php echo $startdate; ?>"></td><td>End Date: <input type="date" name="enddate" value="<?php echo $enddate; ?>"></td><td><input type="hidden" name="submit" value="1"><input type="submit" class="smallbutton" value="Update Search"></td></form></tr>
<tr><td colspan="2"><p class="titletext-red"><?php echo $fullname; ?></p></td>
<td colspan="2"><p class="titletext">Current Account Balance:</p></td>
<td><p class="titletext">
<?php
$balsql1 = 'SELECT SUM(`total`) AS `storebalance` FROM `journal` WHERE `accountid` = :accountid AND `siteid` = :siteid';
$balsth1 = $pdocxn->prepare($balsql1);
$balsth1->bindParam(':accountid',$accountid);
$balsth1->bindParam(':siteid',$currentlocationid);
$balsth1->execute();
while($balrow1 = $balsth1->fetch(PDO::FETCH_ASSOC))
{
$storebalance1 = $balrow1['storebalance'];
$storebalance = money_format('%(#0.2n',$storebalance1);
echo '<u>'.$storebalance.'</u>';
}
//edited today
?></p></td><td><p class="titletext">Balance all stores:</p></td><td><p class="titletext">
<?php
$balsql2 = 'SELECT SUM(`total`) AS `totalbalance` FROM `journal` WHERE `accountid` = :accountid';
$balsth2 = $pdocxn->prepare($balsql2);
$balsth2->bindParam(':accountid',$accountid);
$balsth2->execute();
while($balrow2 = $balsth2->fetch(PDO::FETCH_ASSOC))
{
$totalbalance1 = $balrow2['totalbalance'];
$totalbalance = money_format('%(#0.2n',$totalbalance1);
echo '<u>'.$totalbalance.'</u>';
}
?>
</p></td></tr>
</table>
</div>
        <div id="content">

        	<div id="left"><form name="newtransaction" action="insertpayment.php" method="post">
<table id="highlightTable" class="blueTable">
<thead>
<tr>
<th>Date</th>
<th>Invoice #</th>
<th>Invoice Total</th>
<th>Remaining Balance</th>
<th>Select Invoices to link payment to</th>
</tr>
</thead>
<tbody>
<?php
$tri = '1';

$sql1 = 'SELECT `id`,`invoiceid`,`invoicedate`,`type`,`total`,`taxgroup` FROM `invoice` WHERE `accountid` = :accountid AND `type`=\'1\' AND `voiddate` IS NULL AND `invoicedate` > :startdate AND `invoicedate` < :enddate ORDER BY `invoicedate` DESC';
$sth1 = $pdocxn->prepare($sql1);
$sth1->bindParam(':accountid',$accountid);
$sth1->bindParam(':startdate',$startdate);
$sth1->bindParam(':enddate',$enddate);
$sth1->execute();
	while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$invoicenumber = $row1['invoiceid'];
	$invtotal = $row1['total'];
	$date = $row1['invoicedate'];
	$displaydate = date('m/d/Y', strtotime($date));
	$taxgroup = $row1['taxgroup'];
    $id = $row1['id'];


    $sql8 = "SELECT `linkid` FROM `translink` WHERE `transid` = :transid";
    $sth8 = $pdocxn->prepare($sql8);
    $sth8->bindParam(':transid',$id);
    $sth8->execute();
    while($row8 = $sth8->fetch(PDO::FETCH_ASSOC))
    {
        $linkid = $row8['linkid'];
    }
    $sql9 = "SELECT `linkid`,`transid`,`amount` FROM `translink` WHERE `linktoid` = :linkid";
    $sth9 = $pdocxn->prepare($sql9);
    $sth9->bindParam(':linkid',$linkid);
    $sth9->execute();
    while($row9 = $sth9->fetch(PDO::FETCH_ASSOC))
    {
		$paymentid = $row9['transid'];
		$invpaymentamount = $row9['amount'];
	}
	$invbalance = $invtotal - $invpaymentamount;
if($invbalance > '1')
{

$displayinvtotal = money_format('%(#0.2n',$invtotal);
$dinvbalance = money_format('%(#0.2n',$invbalance);


	echo "<tr><td>$displaydate</td><td>$invoicenumber</td><td>$displayinvtotal</td><td>$dinvbalance</td><td><input type=\"checkbox\" name=\"linktoinvoiceid[]\" value=\"".$id."\"></tr>\n";
$tri ++;

}else{
$paymentid = '0';
}}

?></tbody></table>
    </div><div class="right">
<div class="q1"></div><div class="q3">
<table class="righttable">
<tr><td>Payment Date:</td><td><input type="date" name="transdate" value="<?php echo $currentdate; ?>"></td></tr>
<tr><td>Payment Type:</td><td><select name="paymenttype"><option value="0"></option>
<?php
$sql1 = 'SELECT `id`,`name`,`linkid` FROM `payment_type` ORDER BY `name` ASC';
$sth1 = $pdocxn->prepare($sql1);
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
        $linkid = $row1['linkid'];
        $optionname = $row1['name'];
echo '<option value="'.$linkid.'">'.$optionname.'</option>';
    }
?>
</select> &nbsp;&nbsp;&nbsp;<input type="textbox" class="narrowinput" name="checknumber" placeholder="check#"></td>
</tr>
<tr><td>Amount: </td><td><input type="textbox" name="transamount" placeholder="amount" size="10"></td></tr>
<tr><td>ROA: <input type="checkbox" name="roa" value="1"></td><td>Site: <select name="paymentsite"><option value="currentlocationid"><?php echo $currentstorename; ?></option>
<?php
$sql1 = 'SELECT `id`,`storename` FROM `locations` ORDER BY `storename` ASC';
$sth1 = $pdocxn->prepare($sql1);
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
        $linkid = $row1['id'];
        $optionname = $row1['storename'];
echo '<option value="'.$linkid.'">'.$optionname.'</option>';
    }
?>
</select>
<!--<tr><td colspan="2"><textarea name="comment" placeholder="comment (optional)"></textarea></td></tr>-->
<tr><td colspan="2" class="center">
<input type="hidden" name="inserttransaction" value="1">
<input type="hidden" name="accountid" value="<?php echo $accountid; ?>">
<input type="submit" class="quotebutton" name="submit" value="Submit"></td>
</tr></table></form>
</div></div></div></div>
</div></div>
<script type="text/javascript">
$("table tr").click(function(){
    $($('#' + $(this).attr("href"))).siblings().hide().end().fadeIn();
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
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title><?php echo $title; ?></title>
<link rel="stylesheet" type="text/css" href="style/globalstyle.css" >
<link rel="stylesheet" type="text/css" href="style/paymentstyle1.css" >
<script type="text/javascript" src="scripts/globaljs.js"></script>
<script type="text/javascript" src="scripts/jquery-1.9.1.js"></script>
</head>
<body>
<?php
if($currentlocationid == '1')
{
echo "<div id=\"header\">".$headernavigation."</div>";
}
else{
echo "<div id=\"header2\">".$headernavigation2."</div>";
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
        <div id="content"><form name="account" action="insertpayment.php" method="post">
        	<table class="searchtable"><tr><th>Last Name:</th><td><input type="text" name="lastname" autocomplete="off" autofocus></td><th>First Name:</th><td><input type="text" name="firstname" autocomplete="off"></td></tr>
<tr><th>Phone:</th><td><input type="text" name="phone" autocomplete="off"></td><th>Account Number:</th><td><input type="text" name="acctnumber" autocomplete="off"></td></tr>
<tr><td colspan="4"><input type="hidden" name="search" value="1"><?php if($invoiceid > '0'){?><input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>"><?php } ?><input type="submit" name="submit" class="quotebutton" value="Search"></form></td>
</tr>
        	</table>
        </div>
</body>
</html>
<?php
}}
?>