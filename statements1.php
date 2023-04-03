<?php
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Statements';
$linkpage = 'statements.php';
$changecustomer = '0';

session_start();
date_default_timezone_set('America/Chicago');
$currentdate = date('Y-n-j H:i:s');
$currentday = date('Y-n-j');
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

$startdate = $_POST['startdate'];
$enddate = $_POST['enddate'];
$siteid = '1';
$sth1a = $pdocxn->prepare('DELETE FROM `s1statement_temp` WHERE `id` > \'0\'');
$sth1a->execute();
$idnew = '1';


//for each checkbox//
foreach($_POST['acctstatement'] as $mailstatement){
    $sql1 = "SELECT `firstname`,`lastname` FROM `accounts` WHERE `accountid` = :accountid";
    $sth1 = $pdocxn->prepare($sql1);
    $sth1->bindParam(':accountid',$mailstatement);
    $sth1->execute();
    while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
    {
    $fname = $row1['firstname'];
    $lname = $row1['lastname'];
    if($fname > '0')
    {
    $abvname = substr($fname,0,4);
    }else{
        $abvname = substr($lname,0,4);
    }
    }
$sth2 = $pdocxn->prepare('INSERT INTO `s1statement_temp`(`id`,`accountid`,`abvname`) VALUES (:idnew,:accountid,:abvname)');
$sth2->bindParam(':idnew',$idnew);
$sth2->bindParam(':accountid',$mailstatement);
$sth2->bindParam(':abvname',$abvname);
$sth2->execute();
$idnew ++;
}
    $header = "Location: printstatement.php?startdate=".$startdate."&enddate=".$enddate."&siteid=".$siteid;
    header($header);
    exit();
?>