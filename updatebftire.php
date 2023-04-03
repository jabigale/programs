<?php
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Update Tire';
$linkpage = 'updatebftire.php';

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
$sql1 = "SELECT * FROM `tiretemp`";
$sth1 = $pdocxn->prepare($sql1);
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
    $id = $row1['id'];
    $brand = $row1['brand'];
    $model = $row1['model'];
    $article = $row1['article'];
    $mileage = $row1['mileage'];
    $fet = $row['fet'];
    $price1 = $row1['price1'];
    $osp = $row1['osp'];
    $width = $row1['width'];
    $ratio = $row1['ratio'];
    $rim = $row1['rim'];
    $manid = $row1['manid'];

    $sql2 = "SELECT `id` FROM `inventory2` WHERE `part_number` = :partnumber AND `manid` = :manid";
    $sth2 = $pdocxn->prepare($sql2);
    $sth2->bindParam(':partnumber',$article);
    $sth2->bindParam(':manid',$manid);
    $sth2->execute();
    if ($sth2->rowCount() > 0)
{
    while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
    {
    $invid = $row2['id'];
    $sth3 = $pdocxn->prepare('UPDATE `inventory2` SET `warranty`=:mileage WHERE `id` = :id');
    $sth3->bindParam(':mileage',$mileage);
    $sth3->bindParam(':id',$invid);
    $sth3->execute();  
    }}else{
        $sql3 = 'INSERT INTO `tiretemp2`(`article`,`brand`,`width`,`ratio`,`rim`,`model`) VALUES (:article,:brand,:width,:ratio,:rim,:model)';
        $sth3 = $pdocxn->prepare($sql3);
        $sth3->bindParam(':article',$article);
        $sth3->bindParam(':brand',$brand);
        $sth3->bindParam(':width',$width);
        $sth3->bindParam(':ratio',$ratio);
        $sth3->bindParam(':rim',$rim);
        $sth3->bindParam(':model',$model);
        $sth3->execute();
    }


$sth2 = $pdocxn->prepare('UPDATE `inventory_price` SET `baseprice`=:baseprice,`price1`=:price1 WHERE `article` = :article AND `manid` = :manid');
$sth2->bindParam(':baseprice',$baseprice);
$sth2->bindParam(':price1',$price1);
$sth2->bindParam(':article',$article);
$sth2->bindParam(':manid',$manid);
$sth2->execute();

}

?>