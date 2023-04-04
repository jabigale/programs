<?php
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Accounts';
$linkpage = 'account.php';
$today = date("Y-m-d");
$tomorrow = date('Y-m-d',strtotime($date1 . "+1 days"));
$begindate = $today;
$enddate = $tomorrow;
$type = '1';

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

//submitted form
if(isset($_GET['catid']))
{
$category = $_GET['catid'];
}
else
{
$category = '0';
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Reports</title>
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
        	<div id="left">
<table id="highlightTable" class="blueTable">
<thead>
<tr>
<th><a href="reports.php?catid=1" class="no-decoration"><input type="button" class="<?php if($category == '1'){ echo "cancel";}else{ echo save;}?>" value="Accounting"></a></th>
<th><a href="reports.php?catid=2" class="no-decoration"><input type="button" class="<?php if($category == '2'){ echo "cancel";}else{ echo save;}?>" value="Customer"></a></th>
<th><a href="reports.php?catid=3" class="no-decoration"><input type="button" class="<?php if($category == '3'){ echo "cancel";}else{ echo save;}?>" value="Inventory"></a></th>
<th><a href="reports.php?catid=4" class="no-decoration"><input type="button" class="<?php if($category == '4'){ echo "cancel";}else{ echo save;}?>" value="Transactions"></a></th>
<th><a href="reports.php?catid=5" class="no-decoration"><input type="button" class="<?php if($category == '5'){ echo "cancel";}else{ echo save;}?>" value="Vendor"></a></th>
</tr>
</thead>
<tbody>

<?php
$tri = "1";
$tdi = "1";
if($category > '0')
{
$sql1 = "SELECT * FROM `reports` WHERE `category` = :reportcategory ORDER BY `title` ASC";
$sth1 = $pdocxn->prepare($sql1);
$sth1->bindParam(":reportcategory",$category);
}
else
{
$sql1 = "SELECT * FROM `reports` ORDER BY `title` ASC";
$sth1 = $pdocxn->prepare($sql1);
}
$sth1->execute();
while ($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$reporttitle = $row1['title'];
    $reportid = $row1['id'];
    $reportlink = $row1['link'];
	$reportdesc = $row1['description'];
    echo "<tr href=\"ip".$tri."\"><td colspan=\"5\">".$reporttitle."</td></tr>";
    if($reportlink > '0')
    {
        ${"ip".$tri} = "<div id=\"ip".$tri."\"><div class=\"q1\"><table><tr><td>".$reportdesc."</td></tr><tr><td><a href=\"".$reportlink."\" class=\"no-decoration\"><input type=\"button\" class=\"quotebutton\" value=\"Start Report1\"></a></td></tr></table></div></div>";
    }
	${"ip".$tri} = "<div id=\"ip".$tri."\"><div class=\"q1\"><table><tr><td>".$reportdesc."</td></tr><tr><td><a href=\"accountingreports.php?id=".$reportid."\" class=\"no-decoration\"><input type=\"button\" class=\"quotebutton\" value=\"Start Report\"></a></td></tr></table></div></div>";
	$tri++;
}
?></tbody></table></div>
<div class="right"><?php
while ($tri > 0) {
    echo ${"ip".$tri};
		$tri --;
}

?></div></div></div>
<script type="text/javascript">
$("table tr").click(function(){
    $($('#' + $(this).attr("href"))).siblings().hide().end().fadeIn();
});
</script>
</body>
</html>