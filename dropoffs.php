<?php
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Drop Offs';
$linkpage = 'dropoffs.php';

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
$locschedule = "scheduleloc".$currentlocationid;
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
<th id="part">Drop Off Date</th>
<th id="size">Customer Name</th>
<th id="ply">Vehicle</th>
<th>Details</th>
</tr>
</thead>
<tbody>

<?php

$sql2 = 'SELECT * FROM `dropoff` WHERE `voiddate` IS NULL ORDER BY `date` DESC';
	
	$sth2 = $pdocxn->prepare($sql2);
	$sth2->execute();
	$nrows = $sth2->rowCount();
	
	if ($nrows > 0)
	{
	while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
	{
		$appointmentdate = $row2['invoicedate'];
		$scheduleid = $row2['id'];
		$length = $row2['length'];
		$currentstatus = $row2['status'];
		$return = $row2['return'];
		$accountid = $row2['accountid'];
		$userid = $row2['userid'];
		$vehicleid = $row2['vehicleid'];
		$mileage = $row2['mileagein'];
		$location = $row2['location'];
		$creationdate = $row2['creationdate'];
		$abvname = $row2['abvname'];
		$abvvehicle = $row2['abvvehicle'];
		$insertdate = $row2['insertdate'];
		$phonenumber = $row2['phone'];
		
	// get the status
	$displayinfo = $abvname." ".$abvvehicle." ".$phonenumber;
	echo "<tr><td>".$appointmentdate."</td><td><b>".$abvname."</b></td><td>".$abvvehicle."</td><td><a href=\"appointment.php?id=".$scheduleid."&change=3\" class=\"no-decoration\"><input type=\"button\" value=\"Details\"></a></td><td><a href=\"schedule.php?id=".$scheduleid."&change=1\" class=\"no-decoration\"><img src=\"images/icons/schedulechange.png\" width=\"25\"></a></td></tr>";
	}
	}
		?>
</tbody></table></div>
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
</script>
</body>
</html>
