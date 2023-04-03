<?php
//display no search
//start search post
//pull from db
//display results
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Accounts';
$linkpage = 'account-combine.php';
$firstname = '0';
$lastname='0';
$phone = '0';
$account = '0';
$firstname = $_POST['firstname'];
$lastname = $_POST['lastname'];
$phone = $_POST['phone'];
$account = $_POST['acctnumber'];
$searchnumber=ereg_replace('[^0-9]', '', $phone);
$accounttype = $_POST['accttype'];	

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
if(isset($_POST['search']))
{
	//start search post
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
<script type="text/javascript">
function highlight_row() {
    var table = document.getElementById('highlightTable');
    var cells = table.getElementsByTagName('td');
    for (var i = 0; i < cells.length; i++) {
        // Take each cell
        var cell = cells[i];
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
</select></div><input type="hidden" name="form" value="1"></td></tr></table></form></div>
        <div id="content">

        	<div id="left"><form name="inventory" action="inventory.php" method="post">
<table id="highlightTable" class="blueTable">
<thead>
<tr>
<th>Act ID</th>
<th>Last Name</th>
<th>First name</th>
<th>Phone</th>
<th>City</th>
</tr>
</thead>
<tbody>
<?php
//pull from db
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
if($phone > '0' && $lastname < '1' && $firstname < '1')
	{
$sql1 = 'SELECT * FROM `accounts` WHERE `sphone1` LIKE :phone OR `sphone2` LIKE :phone OR `sphone3` LIKE :phone OR `sphone4` LIKE :phone';
	}
if($phone > '0' && $lastname > '0' && $firstname > '0')
	{
	$sql1 .= " AND `sphone1` LIKE :phone OR `lastname` LIKE :lastname AND `firstname` LIKE :firstname AND `sphone2` LIKE :phone OR `lastname` LIKE :lastname AND `firstname` LIKE :firstname AND `sphone3` LIKE :phone OR `lastname` LIKE :lastname AND `firstname` LIKE :firstname AND `sphone4` LIKE :phone";
}
if($phone > '0' && $lastname < '1' && $firstname > '0')
	{
	$sql1 .= " AND `sphone1` LIKE :phone OR `firstname` LIKE :firstname AND `sphone2` LIKE :phone OR `firstname` LIKE :firstname AND `sphone3` LIKE :phone OR `firstname` LIKE :firstname AND `sphone4` LIKE :phone";
}
if($phone > '0' && $lastname > '0' && $firstname < '1')
	{
	$sql1 .= " AND `sphone1` LIKE :phone OR `lastname` LIKE :lastname AND `sphone2` LIKE :phone OR `lastname` LIKE :lastname AND `sphone3` LIKE :phone OR `lastname` LIKE :lastname AND `sphone4` LIKE :phone";
}
$sql1 .= " ORDER BY `lastname` ASC, `firstname` ASC";
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
if($phone > '0')
{
$searchphone = "%".$searchnumber."%";
$sth1->bindParam(':phone',$searchphone);
}
$sth1->execute();
	while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$accountid = $row1['acctid'];
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
		if($address2 > '1')
		{
		$daddress2 = "<tr><td colspan=\"3\">$address2</td></tr>";
		}
		else {
			$daddress2 = "";
		}
	echo "<tr href=\"$tri\"><td>$accountid</td><td><b>$lname</b></td><td><b>$fname</b></td><td>$phone1</td><td>$city</td></tr>\n";

${"ip".$tri} = "<div id=\"$tri\"><div class=\"q1\"><table class=\"righttable\"><tr><td class=\"center\">";
if($invoiceid > '0')
{
${"ip".$tri} .= "<form name=\"updatecustomer\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"fullname\" value=\"$fullname\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"hidden\" name=\"changecustomer\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"submit\" class=\"btn-style\" name=\"submit\" value=\"Select Customer\"></form>";
}
else
{
${"ip".$tri} .= "<form name=\"accounthistory\" action=\"accounthistory.php\" method=\"post\"><input type=\"hidden\" name=\"fullname\" value=\"$fullname\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"submit\" class=\"btn-style\" name=\"submit\" value=\"Account History\"></form>";
}
${"ip".$tri} .= "</td><td calss=\"center\"><b>$fullname</b></td><td class=\"center\"><form name=\"editaccount\" action=\"editaccount.php\" method=\"post\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"submit\" class=\"btn-style\" name=\"submit\" value=\"Edit Info\"></form></td></tr><tr><td colspan=\"3\" class=\"left\">$address&nbsp;$city,&nbsp;$state&nbsp;$zip</td></tr>".$daddress2."".$dphone1."".$dphone2."".$dphone3."".$dphone4."".$dfax."<tr></table></div><div class=\"q3\"><table class=\"righttable\"><tr><td class=\"center\">";
if($invoiceid > '0')
{
${"ip".$tri} .= "Click Vehicle Below to Select</td><td class=\"center\" colspan=\"2\"><form name=\"updatecustomer\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"changecustomer\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"submit\" name=\"submit\" class=\"btn-style\" value=\"Add a Vehicle\" /></form></td></tr>";
}
else
{
${"ip".$tri} .= "<form name=\"addvehicle\" action=\"vehicles.php\" method=\"POST\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"submit\" name=\"vehiclelist\" class=\"btn-style\" value=\"Detailed List\" /></td><td class=\"center\" colspan=\"2\"><input type=\"submit\" name=\"addvehicle\" class=\"btn-style\" value=\"Add a Vehicle\" /></form></td></tr>";
}
${"ip".$tri} .= "<tr><th>Vehicle</th><th>License</th><th>VIN</th></tr>\n";
$sql2 = 'SELECT * FROM `vehicles` WHERE `accountid` = :acctid AND `inactive` = \'0\' ORDER BY `year` DESC, `make` ASC, `model` ASC';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':acctid',$accountid);
$sth2->execute();
if ($sth2->rowCount() > 0)
{
while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
	{
	$vehicleid = $row2['id'];
	$year = $row2['year'];
	$model = $row2['model'];
	$make = $row2['make'];
	$vin = $row2['vin'];
	$license = $row2['license'];
if($year < '1')
{
$displayvehicle = $row2['cfdescription'];
}else{
$displayvehicle = $year."&nbsp;&nbsp;".$make."&nbsp;&nbsp;".$model;
}
if($invoiceid > '0')
{
${"ip".$tri} .= "\n<tr><td colspan=\"3\" class=\"left\"><form name=\"updatecustomer\" action=\"invoice.php\" method=\"post\"><input type=\"hidden\" name=\"changecustomer\" value=\"1\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$invoiceid."\"><input type=\"hidden\" name=\"accountid\" value=\"".$accountid."\"><input type=\"hidden\" name=\"vehicleid\" value=\"".$vehicleid."\"><input type=\"submit\" name=\"submit\" class=\"btn-style\" value=\"".$displayvehicle." ".$license." ".$vin."\" /></form></td></tr>";
}
else
{
${"ip".$tri} .= "\n<tr><td class=\"left\"><form name=\"schedule\" action=\"accounthistory.php\" method=\"POST\"><input type=\"hidden\" name=\"fullname\" value=\"$fullname\"><input type=\"hidden\" name=\"acccountid\" value=\"$accountid\"><input type=\"hidden\" name=\"vehicleid\" value=\"$vehicleid\"><input type=\"submit\" class=\"btn-style\" name=\"submit\" value=\"$displayvehicle\"></form></td><td>$license</td><td>$vin</td></tr>\n";
}
}
${"ip".$tri} .= "</table></div></div>";
$tri ++;
}
else {
${"ip".$tri} .= "</table></div></div>";
$tri ++;
}}
?></tbody></table>
    </form></div><div class="right">
<?php
//display results
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
	//display no search
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
        <div id="content">
        	<table class="searchtable"><tr><td colspan="4"><form action="newaccount.php" method="post"><input type="submit" name="submit" class="smallquotebutton" value="New Customer"></form><br /></td></tr><form name="account" action="account.php" method="post"><tr><th>Last Name:</th><td><input type="text" name="lastname" autocomplete="off" autofocus></td><th>First Name:</th><td><input type="text" name="firstname" autocomplete="off"></td></tr>
<tr><th>Phone:</th><td><input type="text" name="phone" autocomplete="off"></td><th>Account Number:</th><td><input type="text" name="acctnumber" autocomplete="off"></td></tr>
<tr><th>Account Type:</th><td>Retail Customer:<input type="radio" name="accttype" id="accttype" checked="yes" value="1" /></td><th><td>Wholesale Customer:<input type="radio" name="accttype" id="accttype" value="2"></th></tr>
<tr><th> </th><td>Vendor:<input type="radio" name="accttype" id="accttype" value="3"></td><th><td>National Account:<input type="radio" name="accttype" id="accttype" value="4"></th></tr>
	<tr><th> </th><td>Affiliate:<input type="radio" name="accttype" id="accttype" value="5"></td><th><td>All:<input type="radio" name="accttype" id="accttype" value="6"></th></tr>
<tr><td colspan="4"><input type="hidden" name="search" value="1"><?php if($invoiceid > '0'){?><input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>"><?php } ?><input type="submit" name="submit" class="quotebutton" value="Search"></td></tr></form>
        	</table>
        </div>
</body>
</html>
<?php
}
?>
