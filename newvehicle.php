<?php
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'New Vehicle';
$linkpage = 'newvehicle.php';

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
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title><?php echo $title; ?></title>
<link rel="shortcut icon" type="image/x-icon" href="images/icons/favicon.ico" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" >
<link rel="stylesheet" type="text/css" href="style/globalstyle.css" >
<link rel="stylesheet" type="text/css" href="style/indexstyle.css" >
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
        <div id="content"><div class="year">
<?php
if($_POST['submit'])
{
if($_POST['year'])
{
$year = $_POST['year'];
$accountid = $_POST['accountid'];
$makelist = '1';
$json = "[{\"name\": \"Aragorn\",\"race\": \"Human\"},{\"name\": \"Legolas\",\"race\": \"Elf\"},{\"name\": \"Gimli\",\"race\": \"Dwarf\"}]";

$url = "https://www.carqueryapi.com/api/0.3/?callback=?&cmd=getMakes&year=".$year."&sold_in_us=1";
$url2 = "https://www.carqueryapi.com/api/0.3/?callback=?&cmd=getModels&make=".$makeid."&year=".$year."&sold_in_us=1";
//$ch = curl_init($url);
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$data = curl_exec($ch);
$data1 = substr($data,11,-3);
$data2 = json_decode($data1);
foreach ($data2 as $data3) {
	$makeid = $data3->make_id;
	$make = $data3->make_display;
	if($makelist < '10')
	{

echo "\n<form name=\"make".$makeid."\" action=\"newvehicle.php\" method=\"POST\"><input type=\"hidden\" name=\"year2\" value=\"".$year."\" /><input type=\"hidden\" name=\"makeid\" value=\"".$makeid."\"><input type=\"hidden\" name=\"make\" value=\"".$make."\"><input type=\"submit\" name=\"submit\" class=\"btn-style\" value=\"".$make."\" /></form><br />";
$makelist ++;
}
else{
echo "</div><div class=\"year\">\n";
$makelist = '1';
}
}
}

if($_POST['make'])
{
$year = $_POST['year2'];
$accountid = $_POST['accountid'];
$makeid = $_POST['makeid'];
$make = $_POST['make'];
$makelist = '1';
$url = "https://www.carqueryapi.com/api/0.3/?callback=?&cmd=getModels&make=".$makeid."&year=".$year."&sold_in_us=1";
//$ch = curl_init($url);
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$data = curl_exec($ch);
$data1 = substr($data,12,-3);
$data2 = json_decode($data1);
foreach ($data2 as $data3) {
	$model = $data3->model_name;
	if($makelist < '10')
	{

echo "\n<form name=\"model".$model."\" action=\"newvehicle.php\" method=\"POST\"><input type=\"hidden\" name=\"year2\" value=\"".$year."\" /><input type hidden=\"makeid\" value=\"".$makeid."\"><input type=\"hidden\" name=\"make\" value=\"".$make."\"><input type=\"hidden\" name=\"model\" value=\"".$model."\"><input type=\"submit\" name=\"submit\" class=\"btn-style\" value=\"".$model."\" /></form><br />";
$makelist ++;
}
else{
echo "</div><div class=\"year\">\n";
$makelist = '1';
}
}
}
if($_POST['model'])
{
$make = $_POST['make2'];
$year = $_POST['year2'];
$accountid = $_POST['accountid'];
$makeid = $_POST['makeid'];
$make = $_POST['make'];
$model = $_POST['model'];
$makelist = '1';
$enginelist = '1';
$enginelist2 = '1';
$url = "https://www.carqueryapi.com/api/0.3/?callback=?&cmd=getTrims&make=".$makeid."&year=".$year."&keyword=".$model."&sold_in_us=1";
echo $url;
$newurl = str_replace(' ', '%20', $url);
function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

echo $parsed;

//$ch = curl_init($url);
$ch = curl_init($newurl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$data = curl_exec($ch);
$data1 = substr($data,11,-3);
$data2 = json_decode($data1);
$enginearray = array();
foreach ($data2 as $data3) {
	$trim = $data3->model_trim;
$engine = get_string_between($trim, '(', ')');
$enginearray[] = $engine;
}
$enginearray2 = array_unique($enginearray);
foreach($enginearray2 as $displayengine)
{
echo "\n<form name=\"trim".$model."\" action=\"newvehicle.php\" method=\"POST\"><input type=\"hidden\" name=\"year2\" value=\"".$year."\" /><input type=\"hidden\" name=\"makeid\" value=\"".$makeid."\"><input type=\"hidden\" name=\"make2\" value=\"".$make."\"><input type=\"hidden\" name=\"model\" value=\"".$model."\"><input type=\"submit\" name=\"submit\" class=\"btn-style\" value=\"".$displayengine."\" /></form><br />";
}
}
}
else
{
$year1 = '1';
$year2 = '1';
$year3 = '1';
$year4 = '1';
$year5 = '1';

$sql3 = "SELECT `year` FROM `years` ORDER BY year DESC";
$sth3 = $pdocxn2->prepare($sql3);
$sth3->execute();
while($row3 = $sth3->fetch(PDO::FETCH_ASSOC))
{
$year = $row3['year'];
if($year > '2009')
{
echo "\n<form name=\"form".$year."\" id=\"form".$year."\" action=\"newvehicle.php\" method=\"POST\"><input type=\"hidden\" name=\"year\" value=\"".$year."\" /><input type=\"submit\" name=\"submit\" class=\"btn-style\" value=\"".$year."\" /></form><br />";
}
else if($year < '2010' && $year > '1999')
{
if($year2 =='1')
{
echo "</div><div class=\"year\">";
$year2 = '0';
}
echo "\n<form name=\"form".$year."\" id=\"form".$year."\" action=\"newvehicle.php\" method=\"POST\"><input type=\"hidden\" name=\"year\" value=\"".$year."\" /><input type=\"submit\" name=\"submit\" class=\"btn-style\" value=\"".$year."\" /></form><br />";
}
else if($year < '2000' && $year > '1989')
{
if($year3 =='1')
{
echo "</div><div class=\"year\">";
$year3 = '0';
}
echo "\n<form name=\"form".$year."\" id=\"form".$year."\" action=\"newvehicle.php\" method=\"POST\"><input type=\"hidden\" name=\"year\" value=\"".$year."\" /><input type=\"submit\" name=\"submit\" class=\"btn-style\" value=\"".$year."\" /></form><br />";
}
else if($year < '1990' && $year > '1979')
{
if($year4 =='1')
{
echo "</div><div class=\"year\">";
$year4 = '0';
}
echo "\n<form name=\"form".$year."\" id=\"form".$year."\" action=\"newvehicle.php\" method=\"POST\"><input type=\"hidden\" name=\"year\" value=\"".$year."\" /><input type=\"submit\" name=\"submit\" class=\"btn-style\" value=\"".$year."\" /></form><br />";
}
else
{
if($year5 == '1')
{
echo "\n<form name=\"form".$year."\" id=\"form".$year."\" action=\"newvehicle.php\" method=\"POST\"><input type=\"hidden\" name=\"allyears\" value=\"1\" /><input type=\"submit\" name=\"submit\" class=\"btn-style\" value=\"More Years\" /></form>";
$year5 = '0';
}
}
}
}
?>
</div>
</body></html>