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
if(isset($_GET['invoice']))
{
	$newinvoice = $_GET['invoice'];
	$newinvoicelink = "&invoice=".$newinvoice;
}else{
	$newinvoice = '0';
}
if(isset($_GET['schedule']))
{
	$schedule = $_GET['schedule'];
	$schedulelink = "&schedule=".$schedule;
}else{
	$schedule = '0';
	$schedulelink = '';
}
if(isset($_GET['newtrans']))
{
	$newtrans = $_GET['newtrans'];
	$newtranslink = "&newtrans=".$newtrans;
}else{
	$newtrans = '0';
	$newtranslink = '';
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
if($currentid < '1' or $currentlocationid < '1')
{
$header = "Location: index.php";
header($header);
}
if(isset($_GET['invoiceid']))
{
$invoiceid = $_GET['invoiceid'];
$changecustomer = $_GET['changecustomer'];
$invlink = "?invoiceid=".$invoiceid."&changecustomer=".$changecustomer;
}
else{
	$invlink = "?invoiceid=0";
}
if(isset($_GET['appointmentid']))
{
$appointmentid = $_GET['appointmentid'];
$apptlink = "&appointmentid=".$appointmentid;
}
else{
	$apptlink = '';
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
echo "<div id=\"header2\">".$headernavigation."</div>";
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
<?php
if($invoiceid > '0' OR $appointmentid > '0' OR $newinvoice > '0'){
echo "<th>Select Customer</th>";
}else{
echo "<th>Begin Conversation</th>";
}
//fkmfkmfkm
?>
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
		$dphone1 = "<tr><td class=\"left\" colspan=\"2\">Phone 1: <b>".$phone1."</b></td><td>Contact: <b>".$contact1."</b></td></tr>";
	}
	else
		{
			$dphone1 = "";
		}
if($phone2 > '0')
	{
		$dphone2 = "<tr><td class=\"left\" colspan=\"2\">Phone 2: <b>".$phone2."</b></td><td>Contact: <b>".$contact2."</b></td></tr>";
	}
	else
		{
			$dphone2 = "";
		}
if($phone3 > '0')
	{
		$dphone3 = "<tr><td class=\"left\" colspan=\"2\">Phone 3: <b>".$phone3."</b></td><td>Contact: <b>".$contact3."</b></td></tr>";
	}
	else
		{
			$dphone3 = "";
		}
if($phone4 > '0')
	{
		$dphone4 = "<tr><td class=\"left\" colspan=\"2\">Phone 4: <b>".$phone4."</b></td><td>Contact: <b>".$contact4."</b></td></tr>";
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
	if($invoiceid > '0' OR $appointmentid > '0' OR $newinvoice > '0'){
		if($schedule > '0')
		{
			echo "<td class=\"center\"><a href=\"appointment.php?invoiceid=".$invoiceid."&changecustomer=1&accountid=".$accountid."\" class=\"no-decoration\"><input type=\"button\" class=\"smallbutton\" value=\"Select Customer\"></a></td>";
		}else{
		if($invoiceid > '0')
		{
			echo "<td class=\"center\"><a href=\"invoice.php?invoiceid=".$invoiceid."&changecustomer=1&accountid=".$accountid."\" class=\"no-decoration\"><input type=\"button\" class=\"smallbutton\" value=\"Select Customer\"></a></td>";
		}if($appointmentid > '0'){
		echo "<td class=\"center\"><a href=\"appointment.php?invoiceid=".$appointmentid."&changecustomer=1&accountid=".$accountid."\" class=\"no-decoration\"><input type=\"button\" class=\"smallbutton\" value=\"Select Customer\"></a></td>";
	}if($newinvoice > '0'){
		echo "<td class=\"center\"><form name=\"newinvoice\" action=\"invoice.php\" method=\"POST\"> <input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"hidden\" name=\"new\" value=\"1\"><input type=\"hidden\" name=\"type\" value=\"".$newinvoice."\"><input type=\"submit\" class=\"smallbutton\"  name=\"submit\" value=\"Select Customer\"></form></td>";
	}}
}else{
		echo "<td class=\"center\"><a href=\"#\" onClick=\"window.open('customerinteraction.php?accountid=".$accountid."', '_blank')\" class=\"no-decoration\"><input type=\"button\" class=\"smallbutton\" value=\"begin\"></a></td>";
		}
		echo "</tr>\n";

${"ip".$tri} = "<div id=\"$tri\"><div class=\"q1\"><table class=\"righttable\"><tr><td class=\"center\">";
if($newinvoice > '0')
{
${"ip".$tri} .= "<form name=\"newinvoice\" action=\"invoice.php\" method=\"POST\"> <input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"hidden\" name=\"new\" value=\"1\"><input type=\"hidden\" name=\"type\" value=\"".$newinvoice."\"><input type=\"submit\" class=\"smallbutton\"  name=\"submit\" value=\"Select Customer\"></form>";
}else{
	if($schedule > '0')
	{
	${"ip".$tri} .= "<form name=\"updatecustomer\" action=\"appointment.php\" method=\"post\"><input type=\"hidden\" name=\"fullname\" value=\"$fullname\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"hidden\" name=\"changecustomer\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"submit\" class=\"smallbutton\" name=\"submit\" value=\"Select Customer\"></form>";	
	}
	else {	
if($invoiceid > '0' OR $appointmentid > '0')
{
if($appointmentid > '0')
{
${"ip".$tri} .= "<form name=\"updatecustomer\" action=\"appointment.php\" method=\"post\"><input type=\"hidden\" name=\"fullname\" value=\"$fullname\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"hidden\" name=\"changecustomer\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$appointmentid."\"><input type=\"submit\" class=\"smallbutton\" name=\"submit\" value=\"Select Customer\"></form>";	
}
else {
if($changecustomer == '2')
{
${"ip".$tri} .= "<form name=\"updatecustomer\" action=\"vendorinvoice.php\" method=\"post\"><input type=\"hidden\" name=\"fullname\" value=\"$fullname\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"hidden\" name=\"changecustomer\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"submit\" class=\"smallbutton\" name=\"submit\" value=\"Select Vendor\"></form>";
}else{
${"ip".$tri} .= "<a href=\"invoice.php?accountid=".$accountid."&changecustomer=1&invoiceid=".$invoiceid."\" target=\"_BLANK\" class=\"no-decoration\"><input type=\"button\" class=\"smallbutton\" value=\"Select Customer\"></a>";
}}}
else
{
${"ip".$tri} .= "<form name=\"accounthistory\" action=\"accounthistory.php\" method=\"post\"><input type=\"hidden\" name=\"fullname\" value=\"$fullname\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"submit\" class=\"smallbutton\" name=\"submit\" value=\"Account History\"></form>";
}}}
${"ip".$tri} .= "</td><td calss=\"center\"><a href=\"#\" onClick=\"window.open('inserttransactions.php?accountid=".$accountid."', '_blank')\" class=\"no-decoration\"><input type=\"button\" class=\"smallbutton\" value=\"Insert Transaction\"></a></td><td class=\"center\"><a href=\"editaccount.php?accountid=".$accountid."\" class=\"no-decoration\"><input type=\"button\" class=\"smallbutton\" value=\"Edit Info\"></a></td></tr><tr><td colspan=\"3\" class=\"left\">$address&nbsp;$city,&nbsp;$state&nbsp;$zip</td></tr>".$daddress2."".$dphone1."".$dphone2."".$dphone3."".$dphone4."".$dfax."<tr></table></div><div class=\"q3\"><table class=\"righttable\"><tr><td class=\"center\">";
if($changecustomer == '2')
{
${"ip".$tri} .= "</table></div></div>";
$tri ++;
}else{

$tdi = '1';
	$sql1 = 'SELECT `id`,`name` FROM `invoice_type` WHERE `customerinsertselect` = \'1\' ORDER BY `name` ASC';
	$sth1 = $pdocxn->prepare($sql1);
	$sth1->execute();
	while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
		{
			$optionid = $row1['id'];
			$optionname = $row1['name'];
			if($tdi%2)
			{
${"ip".$tri} .= "<tr><td><a href=\"inserttransactions.php?accountid=".$optionid."&typeid=".$optionid."\"><input type=\"button\" name=\"vehiclelist\" class=\"smallbutton\" value=\"".$optionname."\" /></a></td>";
			}else{
				${"ip".$tri} .= "<td><a href=\"inserttransactions.php?accountid=".$optionid."&typeid=".$optionid."\"><input type=\"button\" name=\"vehiclelist\" class=\"smallbutton\" value=\"".$optionname."\" /></a></td></tr>";				
			}$tdi ++;
	}

$sql2 = 'SELECT * FROM `vehicles` WHERE `accountid` = :accountid AND `active` = \'1\' ORDER BY `year` DESC, `make` ASC, `model` ASC';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':accountid',$accountid);
$sth2->execute();


${"ip".$tri} .= "</table></div></div>";
$tri ++;
}}}else{
	echo "<tr href=\"account.php".$invlink.$apptlink.$newinvoicelink.$schedulelink."\"><td colspan=\"3\"><a href=\"accountnewtrans.php\">No Results, try again</a></td>";
	echo "<td colspan=\"3\"><a href=\"newaccount.php".$invlink.$apptlink.$newinvoicelink.$schedulelink."\">Create a New Account</a></td></tr>\n";

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
<link rel="stylesheet" type="text/css" href="style/accountstyle.css" >
<script type="text/javascript" src="scripts/globaljs.js"></script>
<script type="text/javascript" src="scripts/jquery-1.9.1.js"></script>
<script type="text/javascript" src="scripts/script.js"></script>
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
echo "<div id=\"header2\">".$headernavigation."</div>";
}}else{
	echo "<div id=\"ciheader\"><br /><br /><b>Search for a Customer Below</b></div>";
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
        	<table><tr><td colspan="4"><a href="newaccount.php<?php echo $invlink.$apptlink.$newinvoicelink.$schedulelink; ?>" class="no-decoration"><input type="button" name="submit" class="smallquotebutton" value="New Customer"></a><br /></td></tr><form name="account" action="accountnewtrans.php" method="post"><tr><th>Last Name:</th><td><input type="text" name="lastname" autocomplete="off" autofocus></td><th>First Name:</th><td><input type="text" name="firstname" autocomplete="off"></td></tr>
<tr><th>Phone:</th><td><input type="text" name="phone" autocomplete="off"></td><th>Account Number:</th><td><input type="text" name="acctnumber" autocomplete="off"></td></tr>
<?php
if($newinvoice == '0')
{
?>
<tr><td colspan="4"><input type="hidden" name="search" value="1"><input type="hidden" name="invoice" value="<?php echo $newinvoice; ?>"><?php if($invoiceid > '0'){?><input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>"><input type="hidden" name="changecustomer" value="<?php echo $changecustomer; ?>"><?php } ?><?php if($schedule > '0'){?><input type="hidden" name="schedule" value="<?php echo $schedule; ?>"><input type="hidden" name="changecustomer" value="<?php echo $changecustomer; ?>"><?php } ?><?php if($appointmentid > '0'){?><input type="hidden" name="appointmentid" value="<?php echo $appointmentid; ?>"><?php } ?><input type="submit" name="submit" class="quotebutton" value="Search"></td></tr></form>
<?php
			} else{
				?>
<tr><td colspan="2"><input type="hidden" name="search" value="1"><input type="hidden" name="invoice" value="<?php echo $newinvoice; ?>"><?php if($invoiceid > '0'){?><input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>"><input type="hidden" name="changecustomer" value="<?php echo $changecustomer; ?>"><?php } ?><?php if($schedule > '0'){?><input type="hidden" name="schedule" value="<?php echo $schedule; ?>"><input type="hidden" name="changecustomer" value="<?php echo $changecustomer; ?>"><?php } ?><?php if($appointmentid > '0'){?><input type="hidden" name="appointmentid" value="<?php echo $appointmentid; ?>"><?php } ?><input type="submit" name="submit" class="quotebutton" value="Search"></td>
<td colspan="2"><input type="button" name="Cancel" class="quotebutton" value="Cancel" onclick="self.close()"></td>

</tr></form>
<?php	
			}
			?>     	</table>
        </div>
</body>
</html>
<?php
}
?>
