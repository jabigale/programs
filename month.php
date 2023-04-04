<?php
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Month';
$linkpage = 'month.php';
$weektire = '0';
$monthtire = '0';

session_start();
date_default_timezone_set('America/Chicago');
$currentdate = date('Y-n-j H:i:s');
$currentday = date('Y-m-j');
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

if(isset($_GET['id']))
{
$transactionid = $_GET['id'];
}
else{
$transactionid = '';
}
if(isset($_GET['accountid']))
{
$accountid = $_GET['accountid'];
}
else{
$accountid = '';
}
if(isset($_GET['change']))
{
$change = $_GET['change'];
$displaychange = "&change=1&id=".$transactionid;
}
else{
$change = '0';
$displaychange = '';
}
if(isset($_GET['q']))
{
$invtosched = $_GET['q'];
$invoiceid = $_GET['i'];
$displaychange2 = "&q=1&i=".$invoiceid;
}
else{
$invoiceid = '0';
$invtosched = '0';
$displaychange2 = '';
}
	if(isset($_POST['form']))
	{
	setcookie($cookie1_name, $cookie1_value, time() - (3600), "/");
	setcookie($cookie2_name, $cookie2_value, time() - (3600), "/");
	setcookie($cookie3_name, $cookie3_value, time() - (3600), "/");
	setcookie($cookie4_name, $cookie4_value, time() - (3600), "/");
	$userid = $_POST['user'];
	$locationid = $_POST['location'];
	$sth1 = $pdocxn->prepare('SELECT `username` FROM `employees` WHERE `id` = :userid');
	$sth1->bindParam(':userid',$userid);
	$sth1->execute();
	while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
	{
	$username = $row1['username'];
	}
	$sth2 = $pdocxn->prepare('SELECT `storename` FROM `locations` WHERE `id` = :locationid');
	$sth2->bindParam(':locationid',$locationid);
	$sth2->execute();
	while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
	{
	$storename = $row2['storename'];
	}
	$cookie1_value = $userid;
	$cookie2_value = $username;
	$cookie3_value = $locationid;
	$cookie4_value = $storename;
	$cookie5_value = $_POST['password'];
	setcookie($cookie1_name, $cookie1_value, time() + (86400 * 30), "/");
	setcookie($cookie2_name, $cookie2_value, time() + (86400 * 30), "/");
	setcookie($cookie3_name, $cookie3_value, time() + (86400 * 30), "/");
	setcookie($cookie4_name, $cookie4_value, time() + (86400 * 30), "/");
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
		$locschedule = "scheduleloc".$currentlocationid;
		$lischedule = "s".$currentlocationid."line_items";
	}
	if(!isset($_COOKIE[$cookie4_name])) {
		$currentstorename = "None Selected";
	} else {
	    $currentstorename = $_COOKIE[$cookie4_name];
	}

//This gets today's date
 $date =time () ;  //This puts the day, month, and year in seperate variables
 $today = date('d', $date) ;
 $currentmonth = date('m', $date) ;
 $currentyear = date('Y', $date) ;
 
 $month = $_GET['m'];
 $year = $_GET['y'];
 $prevyear = $year;
 $nextyear = $year;
 $nday = '01';
 $dnday = '1';
 $tqmtotal = '0';
 $tqtotal = '0';
 $tq = '0';
 $gq = '0';
 $dailylof = '0';
 //Here we generate the first day of the month 
 $first_day = mktime(0,0,0,$month, 1, $year) ; 

 //This gets us the month name 
 $title = date('F', $first_day) ;
 //This gets us the Previous Month 
 $prevmonth = $month - 1;
 $prevfirst_day = mktime(0,0,0,$prevmonth, 1, $year) ; 
 $prevtitle = date('F', $prevfirst_day) ;
 //This gets us the Next Month
 $nextmonth = $month + 1;
 if($prevmonth < '10')
 {
 	$prevmonth = '0'.$prevmonth;
 }
  if($nextmonth < '10')
 {
 	$nextmonth = '0'.$nextmonth;
 }
 $nextfirst_day = mktime(0,0,0,$nextmonth, 1, $year) ; 
 $nexttitle = date('F', $nextfirst_day) ;

  if($prevmonth < '1')
 {
 	$prevyear = $year - '1';
	$prevmonth = '12';
 }
 $pmonthday = cal_days_in_month(0, $prevmonth, $prevyear) ;  
  if($nextmonth > '12')
 {
 	$nextyear = $year + '1';
	$nextmonth = '1';
 }
 
 //Here we find out what day of the week the first day of the month falls on 
 $day_of_week = date('D', $first_day) ; 

 //Once we know what day of the week it falls on, we know how many blank days occure before it. If the first day of the week is a Sunday then it would be zero
 switch($day_of_week){
 case "Sun": $blank = 0; break;
 case "Mon": $blank = 1; break;
 case "Tue": $blank = 2; break;
 case "Wed": $blank = 3; break;
 case "Thu": $blank = 4; break;
 case "Fri": $blank = 5; break;
 case "Sat": $blank = 6; break;
 }

 //We then determine how many days are in the current month
 $days_in_month = cal_days_in_month(0, $month, $year) ; 
 
 //Here we start building the table heads 
 echo "\n\n<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">";
 echo "\n<html xmlns=\"http://www.w3.org/1999/xhtml\">\n";
 echo "\n<link rel=\"stylesheet\" href=\"css/autocomplete.css\" type=\"text/css\" media=\"screen\">";
 echo "\n\n<link rel=\"stylesheet\" href=\"css/calendar.css\" type=\"text/css\">";
 echo "\n<center>\n<table border=\"1\" width=\"75%\" height=\"500\">";
 echo "\n<tr height=\"75\"><td colspan=\"2\"><a href=\"month.php?m=".$prevmonth."&y=".$prevyear."\"><div style=\"height:100%;width:100%\"><font size=\"6\"><center>$prevtitle</center></div></a></td><td colspan=\"4\"><center><font size=\"6\">$title $year</font></center></td><td colspan=\"2\"><a href=\"month.php?m=".$nextmonth."&y=".$nextyear."\"><div style=\"height:100%;width:100%\"><font size=\"6\"><center>$nexttitle</center></div></a></td></tr>";
 echo "\n<tr valign=\"top\" height=\"30\"><td width=\"13%\" align=\"center\">Sunday</td>\n<td width=\"13%\" align=\"center\">Monday</td><td width=\"13%\" align=\"center\">Tuesday</td><td width=\"13%\" align=\"center\">Wednesday</td><td width=\"13%\" align=\"center\">Thursday</td><td width=\"13%\" align=\"center\">Friday</td><td width=\"13%\" align=\"center\">Saturday</td><td width=\"9%\" align=\"center\">Total Tires by Week</td></tr>";

 //This counts the days in the week, up to 7
 $day_count = 1;

 echo "\n<tr>";
 //first we take care of those blank days
 while ( $blank > 0 ) 
 {
$dailytire = '0';
$blank = $blank-1;
$pday = $pmonthday - $blank;
$drc = $prevyear."-".$prevmonth."-".$pday;
$drcsearch = $drc."%";

$checkinvsql = 'SELECT `id`,`lof` FROM `'.$locschedule.'` WHERE `date` LIKE :currentdate AND `voiddate` IS NULL';
	$checkinvsth = $pdocxn->prepare($checkinvsql);
	$checkinvsth->bindParam(':currentdate',$drcsearch);
	$checkinvsth->execute();
	while($checkinvrow = $checkinvsth->fetch(PDO::FETCH_ASSOC))
	{
		$checkinvid = $checkinvrow['id'];
		$lof = $checkinvrow['lof'];
		$dailylof = $dailylof + $lof;
$sth4 = $pdocxn->prepare('SELECT `qty` FROM `'.$lischedule.'` WHERE `invoiceid` = :inv AND `partid` > 0');
$sth4->bindParam(':inv',$checkinvid);
$sth4->execute();
	while($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
{
	$tq = $row4['qty'];
	$dailytire = $dailytire + $tq;
	$lof = '0';
}}


//$tqtotal = $tqtotal + $dailytire;
 echo "\n<td valign=\"middle\" bgcolor=\"#BBBBBB\"> <a href=\"schedule.php?selectedday=".$drc."&accountid=".$accountid.$displaychange.$displaychange2."\"><div style=\"height:100%;width:100%\"><font size=\"7\"><center>$pday<br /></font><font size=\"2\">Tires: $dailytire<br />GOF: $dailylof</font></center></div></a></td>";
 $day_count++;
 $weektire = $weektire + $dailytire;
 }
  //sets the first day of the month to 1 
 $day_num = 1;
 //count up the days, untill we've done all of them in the month
 while ( $day_num <= $days_in_month ) 
 {
 	if($day_num<'10')
{
	$dday = "0".$day_num;
}
else {
	$dday = $day_num;
}
$drc = $year.'-'.$month.'-'.$dday;
$drcsearch = $drc."%";
$dailytire = '0';
$checkinvsql = 'SELECT `id`,`lof` FROM `'.$locschedule.'` WHERE `date` LIKE :currentdate AND `voiddate` IS NULL';
	$checkinvsth = $pdocxn->prepare($checkinvsql);
	$checkinvsth->bindParam(':currentdate',$drcsearch);
	$checkinvsth->execute();
	while($checkinvrow = $checkinvsth->fetch(PDO::FETCH_ASSOC))
	{
		$checkinvid = $checkinvrow['id'];
		$lof = $checkinvrow['lof'];
		$dailylof = $dailylof + $lof;
$sth4 = $pdocxn->prepare('SELECT `qty` FROM `'.$lischedule.'` WHERE `invoiceid` = :inv AND `partid` > 0');
$sth4->bindParam(':inv',$checkinvid);
$sth4->execute();
	while($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
{
	$tq = $row4['qty'];
	$dailytire = $dailytire + $tq;
	$lof = '0';
}}
if($today==$day_num&&$currentmonth==$month&&$currentyear==$year)
{
	echo "\n<td bgcolor=\"#0066ff\" valign=\"middle\"> <a href=\"schedule.php?selectedday=".$drc."&accountid=".$accountid.$displaychange.$displaychange2."\"><div style=\"height:100%;width:100%\"><font size=\"7\" color=\"#000\"><center>$day_num<br /></font><font size=\"2\" color=\"#000\">Tires: ".$dailytire."<br />GOF: ".$dailylof."</center></font></div></a></td>";
}
else {
	echo "\n<td valign=\"middle\"> <a href=\"schedule.php?selectedday=".$drc."&accountid=".$accountid.$displaychange.$displaychange2."\"><div style=\"height:100%;width:100%\"><font size=\"7\"><center>$day_num<br /></font><font size=\"2\">Tires: ".$dailytire."<br />GOF: ".$dailylof."</font></center></div></a></td>";
}
$dailylof = '0';
$weektire = $weektire + $dailytire;
$monthtire = $monthtire + $dailytire;
 $day_num++;
 $day_count++;
 //Make sure we start a new row every week
 if ($day_count > 7)
 {

 echo "<td align=\"center\"><font size=\"6\">".$weektire."</font></td></tr>\n<tr>";
 $weektire = '0';
 $day_count = 1;
 }
 $tq = '0';
 $gq = '0';
 }
  //Finaly we finish out the table with some blank details if needed
 while ( $day_count >1 && $day_count <=7 ) 
 {
$dailytire = '0';
$ndrc = $nextyear.'-'.$nextmonth.'-'.$nday;
$ndrcsearch = $ndrc."%";
/*	$sql1 = "SELECT `id`, SUM(lof) AS gq_sum FROM `".$locschedule."` WHERE `date` LIKE :searchdate AND `voiddate` IS NULL";
	$sth1 = $pdocxn->prepare($sql1);
	$sth1->bindParam(':searchdate',$ndrc);
	$sth1->execute();
	while($gqrow = $sth1->fetch(PDO::FETCH_ASSOC))
	{
$gq = $gqrow['gq_sum'];
$tq = '1';
}
*/
$checkinvsql = 'SELECT `id` FROM `'.$locschedule.'` WHERE `date` LIKE :currentdate AND `voiddate` IS NULL';
	$checkinvsth = $pdocxn->prepare($checkinvsql);
	$checkinvsth->bindParam(':currentdate',$drcsearch);
	$checkinvsth->execute();
	while($checkinvrow = $checkinvsth->fetch(PDO::FETCH_ASSOC))
	{
		$checkinvid = $checkinvrow['id'];
$sth4 = $pdocxn->prepare('SELECT `qty` FROM `'.$lischedule.'` WHERE `invoiceid` = :inv AND `partid` > 0');
$sth4->bindParam(':inv',$checkinvid);
$sth4->execute();
	while($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
{
	$tq = $row4['qty'];
	$dailytire = $dailytire + $tq;
}}
$weektire = $weektire + $dailytire;
 	echo "\n<td valign=\"middle\" bgcolor=\"#BBBBBB\"> <a href=\"schedule.php?selectedday=".$ndrc."&accountid=".$accountid.$displaychange.$displaychange2."\"><div style=\"height:100%;width:100%\"><font size=\"7\"><center>$dnday<br /></font><font size=\"2\">Tires: $tq<br />GOF: $gq</font></center></div></a></td>";
 $dnday ++;
 $nday ++;
 $nday = '0'.$nday;
 $day_count++;
 }
 echo "\n<td align=\"center\"><font size=\"6\">".$tqtotal."</font></td></tr>";
 echo "<tr align=\"center\"><td bgcolor=\"#a8ffa2\"><font size=\"5\">Total Tires This Month<br /></font></td><td bgcolor=\"#a8ffa2\"><font size=\"5\">".$currentstorename."<br />";
 echo $monthtire."</font></td><td bgcolor=\"#a8ffa2\"><font size=\"5\">Other location<br /></font></td><td bgcolor=\"#a8ffa2\"><font size=\"5\">All Stores<br /></td><td bgcolor=\"#48dcee\"><font size=\"5\">Total Tires This Year</font></td><td bgcolor=\"#48dcee\"><font size=\"5\">".$currentstorename."<br/></font></td><td bgcolor=\"#48dcee\"><font size=\"5\">Other locations<br/></font></td><td bgcolor=\"#48dcee\"><font size=\"5\">All Stores<br /></font></td></table></center><br /><br /><br />"; 
?>
