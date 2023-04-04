<?php
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Accounts';
$linkpage = 'restoreinvoice.php';
$pagelink2 = 'invoice.php';
$dbyear = '1965';
$invtable = 'invoice';
$invlinetable ='line_items';
$sort = '0';
$split = '0';
$currentdate = date('Y-n-j H:i:s');
$currentday = date('Y-n-j');
$currentday2 = date('Y-m-d');
$invoiceid = $_GET['invoiceid'];

session_start();
date_default_timezone_set('America/Chicago');
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


$sql1 = 'UPDATE `invoice` SET `voiddate` = NULL WHERE `id` = :invoiceid';
$sth1 = $pdocxn->prepare($sql1);
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->execute()or die(print_r($sth1->errorInfo(), true));

$sql1a = 'SELECT `type`,`total`,`invoicedate`,`accountid`,`siteid` FROM `invoice` WHERE `id` = :invoiceid';
$sth1a = $pdocxn->prepare($sql1a);
$sth1a->bindParam(':invoiceid',$invoiceid);
$sth1a->execute();
while($row1a = $sth1a->fetch(PDO::FETCH_ASSOC))
{
    $typeid = $row1a['type'];
    $invoicetotal = $row1a['total'];
    $invoicedate = $row1a['invoicedate'];
    $accountid = $row1a['accountid'];
    $siteid = $row1a['siteid'];
}

if($invoicedate > '2020-04-04')
{
$sql2 = 'SELECT `journal` FROM `invoice_type` WHERE `id` = :typeid';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':typeid',$typeid);
$sth2->execute();
while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
{
 $confirmjournal = $row2['journal'];
}

if($confirmjournal > '0')
{
	if($typeid == '6')
	{
        $invoicetotalsth = $pdocxn->prepare('SELECT SUM(`totallineamount`) AS `invtotal` FROM `line_items` WHERE `invoiceid` = :invoiceide');
        $invoicesth->bindParam(':invoiceid',$invoiceid);
        $invoicesth->execute();
        while($invtotalrow = $invoicesth->fetch(PDO::FETCH_ASSOC))
        {
            $invoicetotal = $invtotalrow['invtotal'];
        }
        $invoicetotal = $invoicetotal * -1;
	}
$sql1 = 'SELECT `id` FROM `journal` WHERE `invoiceid` = :invoiceid';
$sth1 = $pdocxn->prepare($sql1);
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->execute();
if ($sth1->rowCount() > 0)
{
$sth1 = $pdocxn->prepare('UPDATE `journal` SET `total`=:total,`invoicedate`=:invoicedate,`accountid`=:accountid WHERE `invoiceid` = :invoiceid');
$sth1->bindParam(':total',$invoicetotal);
$sth1->bindParam(':invoicedate',$invoicedate);
$sth1->bindParam(':accountid',$accountid);
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->execute();
}
else{
$sql2 = 'INSERT INTO `journal` (`invoiceid`,`accountid`,`total`,`invoicedate`,`journaltype`,`siteid`) VALUES (:invoiceid,:accountid,:total,:invoicedate,:journaltype,:siteid)';
$sth2 = $pdocxn->prepare($sql2);
$sth2->bindParam(':invoiceid',$invoiceid);
$sth2->bindParam(':accountid',$accountid);
$sth2->bindParam(':total',$invoicetotal);
$sth2->bindParam(':invoicedate',$invoicedate);
$sth2->bindParam(':journaltype',$typeid);
$sth2->bindParam(':siteid',$siteid);
$sth2->execute()or die(print_r($sth2->errorInfo(), true));
}}
}
header('location:invoice.php?invoiceid='.$invoiceid.'');
?>