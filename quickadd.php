<?php
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Quick Add';
$linkpage = 'quickadd.php';

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

if(isset($_GET['invoiceid']))
{
  $invoiceid = $_GET['invoiceid'];
}
else{
  $invoiceid ='';
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
 <script type="text/javascript" src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
 <script type="text/javascript">
$(document).ready(function(){
  $("input[type='checkbox']").change(function(e) {
    if($(this).is(":checked")){ 
   $(this).closest('label').addClass("highlight");
  }
    else{
      $(this).closest('label').removeClass("highlight");
    }
  });
});
</script>
<link rel="stylesheet" href="style/quickadd.css" type="text/css">
<title><?php echo $title; ?></title>
</head><body>
<center>
<form name="quickadd" id="quickadd" action="invoice.php" method="post">
<table border="1" width="95%" height="500">
<tr height="50"><td colspan="2" align="center">
<font size="5"><b>Addings items to invoice# <?php echo $invoiceid; ?></b></font></td></tr>
<tr height="50"><td valign="middle" align="center"><b>Oil Change:</b></td><td><label for="gof" >GOF: <input type="checkbox" name="gof" id="gof" value="1" ></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select name="oilbrand" id="oilbrand"><option value=""></option><option value="MoorFlow">MoorFlow</option><option value="Full-Synthetic">Full Synthetic</option><option value="Mobil Clean 5000">Mobil Clean 5000</option><option value="Shell Rotella">Shell Rotella</option><option value="Quaker-State">Quaker-State</option><option value="Penzoil">Penzoil</option></select>&nbsp;&nbsp;&nbsp;<label for="fullservice" >Full Service: <input type="checkbox" name="fullservice" id="fullservice" value="1" ></label>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
<?php
$sth1 = $pdocxn->prepare('SELECT `title`,`id` FROM `quickadd_categories` WHERE `inactive` IS NULL ORDER BY `sort` ASC');
$sth1->execute() or die(print_r($sth1->errorInfo(), true));
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
$i='0';
$category = $row1['title'];
$catid = $row1['id'];
echo "<tr><td valign=\"middle\" align=\"center\"><b>".$category."</b></td><td><table><tr>";
$sth2 = $pdocxn->prepare('SELECT `title`,`id`,`showprice`,`showpart` FROM `quickadd` WHERE `category` = :catid ORDER BY `sort` ASC');
$sth2->bindParam(':catid',$catid);
$sth2->execute() or die(print_r($sth2->errorInfo(), true));
while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
{
$optiontitle = $row2['title'];
$optionid = $row2['id'];
$showprice = $row2['showprice'];
$showpart = $row2['showpart'];
echo "<td class=\"right\"><label for=\"".$optionid."\" >".$optiontitle.": <input type=\"checkbox\" id=\"".$optionid."\" name=\"qa[]\" value=\"".$optionid."\" ></label></td>";
if($showprice == '1'){
  echo "<td><input type=\"textbox\" class=\"narrowinput\" name=\"qa".$optionid."price\" placeholder=\"price\"></td>";
}
if($showpart == '1'){
  echo "<td><input type=\"textbox\" class=\"narrowinput\" name=\"qa".$optionid."part\" placeholder=\"part #\"></td>";
}
if($i%2)
{
  echo "</tr><tr>";
}
$i++;
}
echo "</td></tr></table></tr>";
}
/*
?>
<tr><td valign="middle" align="center"><b>Repairs:</b></td><td valign="middle">
<table><tr><td><label for="replf" >LF Rep: <input type="checkbox" id="replf" name="replf" value="1" ></label></td><td>
<label for="reprf" >RF Rep: <input type="checkbox" id="reprf" name="reprf" value="1" ></label></td></tr><tr><td>
<label for="replr" >LR Rep: <input type="checkbox" id="replr" name="replr" value="1" ></label></td><td>
<label for="reprr" >RR Rep: <input type="checkbox" id="reprr" name="reprr" value="1" ></label></td></tr></table>
</tr>
<tr><td valign="middle" align="center"><b>Rotate</b></td><td valign="middle"><label for="rotate" >Rotate<input type="checkbox" id="rotate" name="rotate" value="1" ></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="rotatefree" >No Charge:<input type="checkbox" id="rotatefree" name="rotatefree" value="1" ></label></td></tr>
<tr><td valign="middle" align="center" rowspan="2"><h3>Brakes</h3></td><td valign="middle"><label for="fbrakes" >Front Brakes:<input type="checkbox" id="fbrakes" name="fbrakes" value="1" ></label>$<input type="textbox" id="fbprice" name="fbprice" placeholder="Price"  size="5"><input type="textbox" id="fbpart" name="fbpart" placeholder="Part #"  size="5">
&nbsp;&nbsp;&nbsp;&nbsp;<span><label for="rbrakes" >Rear Brakes:<input type="checkbox" name="rbrakes" id="rbrakes" value="1" ></label></span>$<input type="textbox" id="rbprice" name="rbprice" placeholder="Price"  size="5"><input type="textbox" id="rbpart" name="rbpart" placeholder="Part #"  size="5"></td></tr>
<tr><td valign="middle"><label for="frotor" >Front Rotors:<input type="checkbox" name="frotors" id="frotor" value="1" ></label>$<input type="textbox" id="frprice" name="frprice" placeholder="Price"  size="5">
<input type="textbox" id="frpart" name="frpart" placeholder="Part #"  size="5">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<label for="rrotor" >Rear Rotors:<input type="checkbox" name="rrotors" id="rrotor" value="1" ></label>$<input type="textbox" id="rrprice" name="rrprice" placeholder="Price"  size="5"><input type="textbox" id="rrpart" name="rrpart" placeholder="Part #"  size="5"></td></tr>
<tr><td valign="middle" align="center" rowspan="2"><h3>Ball Joints</h3></td><td valign="middle"><label for="lfu" >LF Upper:<input type="checkbox" name="lfup" id="lfu" value="1" ></label>$<input type="textbox" id="lfuprice" name="lfuprice" placeholder="Price"  size="4"><input type="textbox" id="lfupart" name="lfupart" placeholder="Part #"  size="5">&nbsp;&nbsp;&nbsp;&nbsp;<label for="rfu" >RF Upper:<input type="checkbox" name="rfup" id="rfu" value="1" ></label>$<input type="textbox" id="rfuprice" name="rfuprice" placeholder="Price"  size="4"><input type="textbox" id="rfupart" name="rfupart" placeholder="Part #"  size="5"></td></tr>
<tr><td valign="middle"><label for="lflo" >LF Lower:<input type="checkbox" name="lflo" id="lflo" value="1" ></label>$<input type="textbox" id="lflprice" name="lflprice" placeholder="Price"  size="4"><input type="textbox" id="lflpart" name="lflpart" placeholder="Part #"  size="5">
&nbsp;&nbsp;&nbsp;&nbsp;<label for="rflo" >RF Lower:<input type="checkbox" name="rflo" id="rflo" value="1" ></label>$<input type="textbox" id="rflprice" name="rflprice" placeholder="Price"  size="4"><input type="textbox" id="rflpart" name="rflpart" placeholder="Part #"  size="5"></td></tr>
<tr><td valign="middle" align="center" rowspan="2"><h3>Tie Rods</h3></td><td valign="middle"><label for="lfo" >LF Outer:<input type="checkbox" name="lfout" id="lfo" value="1" ></label>$<input type="textbox" id="lfoprice" name="lfoprice" placeholder="Price"  size="4"><input type="textbox" id="lfopart" name="lfopart" placeholder="Part #"  size="5">
&nbsp;&nbsp;&nbsp;&nbsp;<label for="rfo" >RF Outer:<input type="checkbox" name="rfout" id="rfo" value="1" ></label>$<input type="textbox" id="rfoprice" name="rfoprice" placeholder="Price"  size="4"><input type="textbox" id="rfopart" name="rfopart" placeholder="Part #"  size="5">
</td></tr>
<tr><td valign="middle"><label for="lfi" >LF Inner:&nbsp;<input type="checkbox" name="lfin" id="lfi" value="1" ></label>$<input type="textbox" id="lfiprice" name="lfiprice" placeholder="Price"  size="4"><input type="textbox" id="lfipart" name="lfipart" placeholder="Part #"  size="5">
&nbsp;&nbsp;&nbsp;&nbsp;<label for="rfi" >RF Inner:&nbsp;<input type="checkbox" name="rfin" id="rfi" value="1" ></label>$<input type="textbox" id="rfiprice" name="rfiprice" placeholder="Price"  size="4"><input type="textbox" id="rfipart" name="rfipart" placeholder="Part #"  size="5">
</td></tr>
<tr><td valign="middle" align="center" rowspan="2"><h3>Wheel Bearings</h3></td><td valign="middle"><label for="lfb" >LF Bearing:<input type="checkbox" name="lfbearing" id="lfb" value="1" ></label>$<input type="textbox" id="lfbprice" name="lfbprice" placeholder="Price"  size="4"><input type="textbox" id="lfbpart" name="lfbpart" placeholder="Part #"  size="5">
&nbsp;&nbsp;&nbsp;&nbsp;<label for="rfb" >RF Bearing:<input type="checkbox" name="rfbearing" id="rfb" value="1" ></label>$<input type="textbox" id="rfbprice" name="rfbprice" placeholder="Price"  size="4"><input type="textbox" id="rfbpart" name="rfbpart" placeholder="Part #"  size="5">
</td></tr>
<tr><td valign="middle"><label for="lrb" >LR Bearing:<input type="checkbox" name="lrbearing" id="lrb" value="1" ></label>$<input type="textbox" id="lrbprice" name="lrbprice" placeholder="Price"  size="4"><input type="textbox" id="lrbpart" name="lrbpart" placeholder="Part #"  size="5">
&nbsp;&nbsp;&nbsp;&nbsp;<label for="rrb" >RR Bearing:<input type="checkbox" name="rrbearing" id="rrb" value="1" ></label>$<input type="textbox" id="rrbprice" name="rrbprice" placeholder="Price"  size="4"><input type="textbox" id="rrbpart" name="rrbpart" placeholder="Part #"  size="5">
</td></tr>
<tr><td valign="middle" align="center" rowspan="2"><h3>Suspension</h3></td><td valign="middle"><label for="fst"  >Front Struts:&nbsp;<input type="checkbox" name="fstruts" id="fst" value="1" ></label>&nbsp;$<input type="textbox" id="lflprice" name="fstprice" placeholder="Price"  size="4"><input type="textbox" id="fstpart" name="fstpart" placeholder="Part #"  size="5">
&nbsp;&nbsp;&nbsp;&nbsp;<label for="rst" >Rear Struts:&nbsp;<input type="checkbox" name="rstruts" id="rst" value="1" ></label>&nbsp;$<input type="textbox" id="rstprice" name="rstprice" placeholder="Price"  size="4"><input type="textbox" id="rstpart" name="rstpart" placeholder="Part #"  size="5">
</td></tr>
<tr><td valign="middle"><label for="fsh" >Front Shocks:<input type="checkbox" name="fshocks" id="fsh" value="1" ></label>$<input type="textbox" id="fshprice" name="fshprice" placeholder="Price"  size="4"><input type="textbox" id="fshpart" name="fshpart" placeholder="Part #"  size="5">
&nbsp;&nbsp;&nbsp;&nbsp;<label for="rsh" >Rear Shocks:<input type="checkbox" name="rshocks" id="rsh" value="1" ></label>$<input type="textbox" id="rshprice" name="rshprice" placeholder="Price"  size="4"><input type="textbox" id="rshpart" name="rshpart" placeholder="Part #"  size="5">
</td></tr>
<tr><td valign="middle" align="center" rowspan="2"><h3>AC/Heat</h3></td><td valign="middle"><label for="ac" >Charge A/C:&nbsp;<input type="checkbox" name="ac" id="ac" value="1" ></label>&nbsp;$<input type="textbox" id="acprice" name="acprice" placeholder="Price"  size="4"><input type="textbox" id="acpart" name="acpart" placeholder="Part #"  size="5">
&nbsp;&nbsp;&nbsp;&nbsp;<label for="accompressor" >A/C Compressor:&nbsp;<input type="checkbox" name="accompressor" id="accompressor" value="1" ></label>&nbsp;$<input type="textbox" id="accprice" name="accprice" placeholder="Price"  size="4"><input type="textbox" id="accpart" name="accpart" placeholder="Part #"  size="5">
</td></tr>
<tr><td valign="middle" ><label for="nheat" >No Heat:&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="nheat" id="nheat" value="1" ></label>&nbsp;&nbsp;$<input type="textbox" id="nheatprice" name="nheatprice" placeholder="Price"  size="4"><input type="textbox" id="nheatpart" name="nheatpart" placeholder="Part #"  size="5">
&nbsp;&nbsp;&nbsp;&nbsp;<label for="hcore" >Heater Core:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="hcore" id="hcore" value="1" ></label>&nbsp;&nbsp;$<input type="textbox" id="hcprice" name="hcprice" placeholder="Price"  size="4"><input type="textbox" id="hcpart" name="hcpart" placeholder="Part #"  size="5">
</td></tr>

<tr><td valign="middle" align="center" rowspan="2"><h3>Engine</h3></td><td valign="middle"><label for="ac" >Tune-Up:&nbsp;<input type="checkbox" name="ac" id="ac" value="1" ></label>&nbsp;$<input type="textbox" id="acprice" name="acprice" placeholder="Price"  size="4"><input type="textbox" id="acpart" name="acpart" placeholder="Part #"  size="5">
&nbsp;&nbsp;&nbsp;&nbsp;<label for="accompressor" >take2:&nbsp;<input type="checkbox" name="accompressor" id="accompressor" value="1" ></label>&nbsp;$<input type="textbox" id="accprice" name="accprice" placeholder="Price"  size="4"><input type="textbox" id="accpart" name="accpart" placeholder="Part #"  size="5">
</td></tr>
<tr><td valign="middle" ><label for="nheat" >take3:&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="nheat" id="nheat" value="1" ></label>&nbsp;&nbsp;$<input type="textbox" id="nheatprice" name="nheatprice" placeholder="Price"  size="4"><input type="textbox" id="nheatpart" name="nheatpart" placeholder="Part #"  size="5">
&nbsp;&nbsp;&nbsp;&nbsp;<label for="hcore" >take4:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="hcore" id="hcore" value="1" ></label>&nbsp;&nbsp;$<input type="textbox" id="hcprice" name="hcprice" placeholder="Price"  size="4"><input type="textbox" id="hcpart" name="hcpart" placeholder="Part #"  size="5">
</td></tr>


<tr><td valign="middle" align="center"><b>Additional Info</b></td><td valign="middle"><textarea name="additionalinfo" rows="5" cols="100">
</textarea>&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
<?php
*/
?>

<input type="hidden" name="submit" value="submit">
<tr align="center"><td colspan="2"><input type="hidden" name="invoiceid" value="<?php echo $invoiceid; ?>"><input type="hidden" name="quickaddsubmit" value="1"><input type="submit" name="submit" class="bluebutton" alt="Quick Add" value="Add Selected Items"></td></tr></table></form></center></body></html>
