<?php
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Statements';
$linkpage = 'statements.php';

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
$currentday = date('Y-n-j');
$currentdate = date('Y-m-d');
$showhistory = '0';
if(isset($_GET['accountid']))
{
    $accountid = $_GET['accountid'];
    $sql2 = "SELECT `firstname`,`lastname` FROM `accounts` WHERE `accountid` = :accountid";
	$sth2 = $pdocxn->prepare($sql2);
	$sth2->bindParam(':accountid',$accountid);
	$sth2->execute();
	while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
	{
	$fname = $row2['firstname'];
	$lname = $row2['lastname'];
	$fullname = $fname." ".$lname;
	}
}else{
    $accountid = '0';
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
    $siteid = $currentlocationid;
}
if(!isset($_COOKIE[$cookie4_name])) {
	$currentstorename = "None Selected";
} else {
    $currentstorename = $_COOKIE[$cookie4_name];
}

if(isset($_GET['startdate']))
	{
		$startdate = $_GET['startdate'];
        $enddate = $_GET['enddate'];
        $dbenddate = date('Y-n-j',strtotime('+ 1 day',strtotime($enddate)));
        $enddate = date('Y-m-d', strtotime('+1 day', strtotime($enddate)));
        $showhistory = '1';
    }else
    {
        $dstartdate = date("Y-m-01", strtotime('- 30 days'));
        $denddate = date("Y-m-t", strtotime('- 30 days'));
    }
if($showhistory == '1')
{
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
<script type="text/javascript">
function close_window() {
    window.close();
}
</script>
</head>
<body>
<div id="ciheader">
<div class="headercenter"><a href="javascript:close_window();" class="no-decoration"><input type="button" class="cancel" value="Cancel/Exit"></a></div></div>
<div id="selecteduserfullwidth"><form name="current1" action="index.php" method="POST">
<table id="floatleft" width="100%"><tr>
<td class="currentuser">Current User:</td>
<td class="currentitem"><div class="styled-select black rounded">
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
</select></div></td>
<td class="currentstore">Current Store:</td>
<td class="currentitem"><div class="styled-select black rounded"><select name="location" onchange="form.submit()"><?php
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
</select></div><input type="hidden" name="form" value="1"></td></form>

<td>Start Date: <input type="date" name="startdate" value="<?php echo $startdate; ?>"></td>
<td>End Date: <input type="date" name="enddate" value="<?php echo $enddate; ?>"></td>
<td><input type="hidden" name="submit" value="1"><input type="submit" class="smallbutton" value="Update Search"></td></form></tr>
</table>
</div>
        <div id="content"><div id="left">
        <form name="selectstatements" method="POST" action="statements1.php">
<input type="hidden" name="startdate" value="<?php echo $startdate; ?>">
<input type="hidden" name="enddate" value="<?php echo $enddate; ?>">
<table id="highlightTable" class="blueTable">
<thead>
<tr>
<th>Statement</th>
<th>Customer</th>
<th>Current Balance</th>
<th>Credit Limit</th>
<th>Phone</th>
<th>Phone</th>
</tr>
</thead>
<tbody>
<?php
$tri = '1';

//$sql1 = 'SELECT SUM(`total`) AS `balance`,`accountid` FROM `journal` WHERE `siteid` = :siteid AND `invoicedate` < :enddate GROUP BY `accountid`';
$sql1 = 'SELECT SUM(`total`) AS `balance`,`accountid` FROM `journal` WHERE `siteid` = :siteid AND `invoicedate` < :enddate GROUP BY `accountid`';
$sth1 = $pdocxn->prepare($sql1);
$sth1->bindParam(':siteid',$siteid);
$sth1->bindParam(':enddate',$dbenddate);
$sth1->execute();
	while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$accountid = $row1['accountid'];
	$totalbalance = $row1['balance'];
	if($totalbalance > '.02')
	{
            $sql2 = "SELECT `firstname`,`lastname`,`creditlimit`,`statement`,`phone1`,`phone2`,`contact1`,`contact2`,`oldtype` FROM `accounts` WHERE `accountid` = :accountid";
            $sth2 = $pdocxn->prepare($sql2);
            $sth2->bindParam(':accountid',$accountid);
            $sth2->execute();
            while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
            {
            $fname = $row2['firstname'];
            $lname = $row2['lastname'];
            $fullname = $fname." ".$lname;
            $creditlimit = $row2['creditlimit'];
            $mailstatement = $row2['statement'];
            $phone1 = $row2['phone1'];
            $phone2 = $row2['phone2'];
            $contact1 = $row2['contact1'];
            $contact2 = $row2['contact2'];
            $accounttype = $row2['oldtype'];
            if($accounttype == '1')
            {
            if($mailstatement == '0')
            {
                $checked = 'checked="yes"';
            }else{
                $checked = '';
            }
            if($creditlimit < $totalbalance)
            {
                $overlimit = '1';
            }else{
                $overlimit = '0';
            }
            /*
            $sql5 = 'SELECT `invoicedate` FROM `invoice` WHERE `location` = :siteid AND `accountid` = :accountid AND `type` = \'6\' ORDER BY `invoicedate` DESC LIMIT 1';
$sth5 = $pdocxn->prepare($sql5);
$sth5->bindParam(':siteid',$siteid);
$sth5->bindParam(':accountid',$accountid);
$sth5->execute();
	while($row5 = $sth5->fetch(PDO::FETCH_ASSOC))
	{
    $paymentdate = $row5['invoicedate'];
    $dpaymentdate = date("n/j/Y", strtotime($paymentdate));
    }*/
            //${"ip".$tri} = "<div id=\"$tri\"><div class=\"q1\"><table class=\"righttable\"><tr><td class=\"center\"><form name=\"invoicehistory\" action=\"printinvoice.php\" method=\"post\"><input type=\"hidden\" name=\"invoiceid\" value=\"".$id."\"><input type=\"submit\" class=\"btn-style\" name=\"submit\" value=\"Print\"></form></td><td class=\"center\"><a href=\"schedule.php?i=".$id."&q=1\" class=\"no-decoration\"><input type=\"hidden\" name=\"transactionid\" value=\"".$id."\"><input type=\"submit\" class=\"btn-style\"  value=\"Schedule\"></a></td><td class=\"center\"><a href=\"invoice.php?invoiceid=".$id."\" target=\"_BLANK\" class=\"no-decoration\"><input type=\"button\" class=\"btn-style\" name=\"submit\" value=\"Edit ".$typename."\"></a></td></tr></table></div><div class=\"q3\"><table class=\"righttable\"><tr><th>qty</th><th>comment</th><th>unit cost</th><th>total cost</td></tr>\n";


            //${"ip".$tri} .= "</table></div></div>";
            echo "<tr href=\"$tri\"><td><input type=\"checkbox\" name=\"acctstatement[]\" value=\"$accountid\" $checked><td>$fullname</td><td>";
            if($overlimit == '1')
            {
                echo "<p class=\"warningfontcolor\">".$totalbalance."</p>";
            }else{
            echo "<p>".$totalbalance."</p>";
            }
            echo "</td><td>$creditlimit</td><td>".$phone1." ".$contact1."</td><td>".$phone2." ".$contact2."</td></tr>\n";
            $tri ++;
    }}
    
    }else{}
}
?>
<tr><td colspan="4"><input type="submit" name="submit" class="quotebutton" value="Print Selected Statements"></td></tr>
<tr><td colspan="4"><br /><br /><br /></td></tr>

</tbody></table>
    </form></div>
</div></div>
</div></div>
<script type="text/javascript">
$("table tr").click(function(){
    $($('#' + $(this).attr("href"))).siblings().hide().end().fadeIn();
});
</script>
</body>
</html>
<?php
}else {
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
<link rel="stylesheet" type="text/css" href="style/accounthistory.css" >
<script type="text/javascript" src="scripts/globaljs.js"></script>
<script type="text/javascript" src="scripts/jquery-1.9.1.js"></script>
<script type="text/javascript">
function close_window() {
    window.close();
}
</script>
</head>
<body>
<div id="ciheader">
<div class="headerright"><br /><a href="javascript:close_window();" class="no-decoration"><input type="button" class="save" value="<?php echo $fullname; ?>"></a></div>
<div class="headerleft"><br /><a href="javascript:close_window();" class="no-decoration"><input type="button" class="cancel" value="Cancel/Exit"></a></div>
<div class="headercenter"><a href="inventory.php?accountid=<?php echo $accountid; ?>&ci=1" onmouseover="popup('inventory')"><img src="images/icons/tire-white.png" height="40"></a><a href="schedule.php?r=<?php echo $r; ?>&accountid=<?php echo $accountid; ?>" onmouseover="popup('scheduler')"><img src="images/icons/schedule.png" height="40"></a><a href="customerinteraction-invoice.php?accountid=<?php echo $accountid; ?>" onmouseover="popup('transactions')"><img src="images/icons/phone.png" height="40"></a></div></div>
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
if($accountid > '0')
{
?>        <div id="content"><form name="account" action="statements1.php" method="POST">
        	<table class="searchtable">
            <tr><th colspan="4">Print Statement for <?php echo $fullname; ?><input type="hidden" name="acctstatement" value="<?php echo $accountid; ?>"></th></tr>
            <tr><th>Statement Begining Date:</th><td><input type="date" name="startdate" value="<?php echo $dstartdate; ?>"></td><th>Statement End Date:</th><td><input type="date" name="enddate" value="<?php echo $denddate; ?>"></td></tr>
<tr><td colspan="4"><input type="submit" name="submit" class="quotebutton" value="Run Report"></form></td></tr>
        	</table>
<?php
}else{
?>
        <div id="content"><form name="account" action="statements.php" method="GET">
        	<table class="searchtable">
            <tr><th>Statement Begining Date:</th><td><input type="date" name="startdate" value="<?php echo $dstartdate; ?>"></td><th>Statement End Date:</th><td><input type="date" name="enddate" value="<?php echo $denddate; ?>"></td></tr>
<tr><td colspan="4"><input type="submit" name="submit" class="quotebutton" value="Run Report"></form></td></tr>
        	</table>
<?php
}
?>
        </div>
</body>
</html>
<?php
}
?>