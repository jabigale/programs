
<?php
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Insert Service';
$linkpage = 'editvehicle.php';
$changecustomer = '0';
$defaultstate = "WI";
$yearywi = date('Y', strtotime('+1 Year', strtotime($currentyear)));
$dbyear = '1965';
$partid = $_GET['partid'];
$qty = $_GET['qty'];
$type = $_GET['type'];
$acct = $_GET['acct'];
$inv = $_GET['inv'];
$quicksearch = '0';

session_start();
date_default_timezone_set('America/Chicago');
$currentdate = date('Y-n-j H:i:s');
$currentday = date('Y-n-j');
header("Expires: Mon, 01 Jan 2018 05:00:00 GMT");
header("Last-Modified: ".gmdate( 'D, d M Y H:i:s')." GMT");
header("Cache-Control: private, no-store, max-age=0, no-cache, must-revalidate, post-check=0, pre-check=0");
header("Pragma: no-cache");

//check if logged in
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

if(isset($_POST['newservice']))
{
$newcost1 = $_POST['newprice1'];
$newcost2 = $_POST['newprice2'];
$newcost3 = $_POST['newprice3'];
$newlaborhours = $_POST['newlaborhours'];
$newtitle = $_POST['newtitle'];
$newcode = $_POST['newcode'];
$newnote = $_POST['newnote'];

$sql1 = "INSERT INTO `realetp3_mtccalendar`.`service` (`cost1`,`cost2`,`cost3`,`laborhours`,`title`,`code`,`note`) VALUES (:newcost1,:newcost2,:newcost3,:newlaborhours,:newtitle,:newcode,:newnote)";
$sth1 = $pdocxn->prepare($sql1);
$sth1->bindParam(':newcost1',$newcost1);
$sth1->bindParam(':newcost2',$newcost2);
$sth1->bindParam(':newcost3',$newcost3);
$sth1->bindParam(':newlaborhours',$newlaborhours);
$sth1->bindParam(':newtitle',$newtitle);
$sth1->bindParam(':newcode',$newcode);
$sth1->bindParam(':newnote',$newnote);
$sth1->execute();
}
if(isset($_POST['updateservice']))
{
$cost1 = $_POST['1price'];
$cost2 = $_POST['2price'];
$cost3 = $_POST['3price'];
$id = $_POST['id'];
$laborhours = $_POST['laborhours'];
$title = $_POST['title'];
$code = $_POST['code'];
$note = $_POST['note'];

$sql2 = "UPDATE `service` SET `cost1`=:cost1,`cost2`=:cost2,`cost3`=:cost3,`laborhours`=:laborhours,`title`=:title,`code`=:code,`note`=:note WHERE `id`=:id";
echo $sql2;
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindPAram(':id',$id);
$sth2->bindParam(':cost1',$cost1);
$sth2->bindParam(':cost2',$cost2);
$sth2->bindParam(':cost3',$cost3);
$sth2->bindParam(':laborhours',$laborhours);
$sth2->bindParam(':title',$title);
$sth2->bindParam(':code',$code);
$sth2->bindParam(':note',$note);
$sth2->execute();
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" >
<link rel="stylesheet" type="text/css" href="style/globalstyle.css" >
<link rel="stylesheet" type="text/css" href="style/insertservicestyle.css" >
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
<body><?php
if($currentlocationid == '1')
{
echo "<div id=\"header\">".$headernavigation."</div>";
}
else{
echo "<div id=\"header2\">".$headernavigation."</div>";
}
?>
        <div id="content">
        	<div id="left"><form name="inventory" action="inventory.php" method="post">
<table id="highlightTable" class="blueTable">
<thead>
<tr>
<th>Code</th>
<th>Title</th>
<th>Description</th>
<th>Price 1</th>
<th>Price 2</th>
<th>Price 3</th>
</tr>
</thead>
<tbody>
<?php
$tri = '1';
$sql1 = 'SELECT * FROM `service` ORDER BY `code` ASC';
$sth1 = $pdocxn->prepare($sql1);
$sth1->execute();
	while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$id = $row1['id'];
	$cost1 = $row1['cost1'];
	$cost2 = $row1['cost2'];
	$cost3 = $row1['cost3'];
	$type = $row1['type'];
	$taxexempt = $row1['taxexempt'];
	$laborhours = $row1['laborhours'];
	$title = $row1['title'];
	$code = $row1['code'];
	$note = $row1['note'];

	echo "<tr href=\"$tri\"><td>$code</td><td>$title</td><td>$note</td><td>$cost1</td><td>$cost2</td><td>$cost3</td></tr>";
${"ip".$tri} = "<div id=\"$tri\"><div class=\"q1\"><form name=\"updateservice\" action=\"insertservice.php\" method=\"post\"><table class=\"righttable\"><tr><td colspan=\"2\"><textarea class=\"wideinput\" name=\"title\" rows=\"2\" autocomplete=\"off\">".$title."</textarea></td></tr><tr><td colspan=\"2\"><textarea class=\"wideinput\" name=\"note\" rows=\"3\" autocomplete=\"off\">".$note."</textarea></td></tr><tr><td>Price 1 $<input type=\"text\" class=\"narrowinput\" name=\"1price\" value=\"$cost1\" autocomplete=\"off\"></td><td>Price 2 $<input type=\"text\" class=\"narrowinput\" name=\"2price\" value=\"$cost2\" autocomplete=\"off\"></td></tr><tr><td>Price 3 $<input type=\"text\" class=\"narrowinput\" name=\"3price\" value=\"$cost3\" autocomplete=\"off\"></td><td>Code: <input type=\"text\" class=\"narrowinput\" name=\"code\" value=\"".$code."\" autocomplete=\"off\"></td></tr><tr><td colspan=\"2\">Labor Hours: <input type=\"number\" class=\"narrowinput\" name=\"laborhours\" value=\"".$laborhours."\" step=\"0.25\" autocomplete=\"off\"></td></tr><tr><td colspan=\"2\"><input type=\"hidden\" name=\"id\" value=\"".$id."\"><input type=\"hidden\" name=\"updateservice\" value=\"1\"><input type=\"image\" src=\"images/buttons/update.png\" alt=\"updateservice\" name=\"submit\"></td></tr></table></form></div><div class=\"q3\"></div></div>\n";
$tri ++;
	}
echo "<tr href=\"add\"><td></td><td></td><td>Add Item <img src=\"images/buttons/add-black.png\" width=\"25\"> </td><td></td><td></td><td></td></tr>";
?></tbody></table>
<table>
<tr><th></th></tr>
</table>
    </form></div><div class="right">
<?php
while ($tri > 0) {
        echo ${"ip".$tri};
		$tri --;
}
echo "<div id=\"add\"><div class=\"q1\"><form name=\"newservice\" action=\"insertservice.php\" method=\"post\"><table class=\"righttable\"><tr><td colspan=\"2\">Add Service</td></tr><tr><td colspan=\"2\"><textarea class=\"wideinput\" name=\"newtitle\" rows=\"1\" placeholder=\"Title\" autocomplete=\"off\"></textarea></td></tr><tr><td colspan=\"2\"><textarea class=\"wideinput\" name=\"newnote\" placeholder=\"Service Note\" autocomplete=\"off\"></textarea></td></tr><tr><td>Price 1 $<input type=\"text\" class=\"narrowinput\" name=\"newprice1\" placeholder=\"0.00\" step=\"1.00\" autocomplete=\"off\"></td><td>Price 2 $<input type=\"text\" class=\"narrowinput\" name=\"newprice2\" placeholder=\"0.00\" step=\"1.00\" autocomplete=\"off\"></td></tr><tr><td>Price 3 $<input type=\"text\" class=\"narrowinput\" name=\"newprice3\" placeholder=\"0.00\" step=\"1.00\" autocomplete=\"off\"></td><td>Code: <input type=\"text\" class=\"narrowinput\" name=\"newcode\" placeholder=\"Code\" autocomplete=\"off\"></td></tr><tr><td colspan=\"2\">Labor Hours: <input type=\"number\" class=\"narrowinput\" name=\"newlaborhours\" value=\"1.00\" step=\"0.25\" autocomplete=\"off\"></td></tr><tr><td colspan=\"2\"><input type=\"hidden\" name=\"newservice\" value=\"1\"><input type=\"image\" src=\"images/buttons/add-service.png\" alt=\"newservice\" name=\"submit\" /></td></tr></table></form></div><div class=\"q3\"></div></div>\n";
?>
</div></div>
<script type="text/javascript">
$("table tr").click(function(){
    $($('#' + $(this).attr("href"))).siblings().hide().end().fadeIn();
});
</script>
</body>
</html>
