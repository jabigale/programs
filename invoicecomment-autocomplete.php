<?php
include_once ('scripts/mysql.php');
$postedcomment = $_GET['part'];
$searchcomment = "%".$postedcomment."%";
$namesql = "SELECT `comment` FROM `invoiceautocomplete` WHERE `comment` LIKE :comment ORDER by `comment` ASC";
$sth1 = $pdocxn->prepare($namesql);
$sth1->bindParam(':comment',$searchcomment);
$sth1->execute();
while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
{
	$sqlcomment = $row1['comment'];
   		$comments[]=$sqlcomment;
}
// check the parameter
if(isset($_GET['part']) and $_GET['part'] != '')
{
	// initialize the results array
	$results = array();
	foreach($comments as $comment1)
	{
			$results[] = $comment1;

	}
	// return the array as json
	echo json_encode($results);
}
?>
