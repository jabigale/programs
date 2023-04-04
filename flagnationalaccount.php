<?php
//n2update
//include mysql file
include_once ('scripts/mysql.php');
if(isset($_GET['invoiceid']))
{
    $invoiceid = $_GET['invoiceid'];
$gotid = '1';
}
if($gotid == '1')
{
if(isset($_GET['flag']) && $_GET['flag'] > '0')
{
$sth1 = $pdocxn->prepare('UPDATE `invoice` SET `naflag`=\'1\' WHERE `id` = :invoiceid');
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->execute();
}
if(isset($_GET['complete']) && $_GET['complete'] == '1')
{
$sth1 = $pdocxn->prepare('UPDATE `invoice` SET `nacomplete`=\'1\' WHERE `id` = :invoiceid');
$sth1->bindParam(':invoiceid',$invoiceid);
$sth1->execute();
}
?>
<!doctype html>
<html>
<head>
      <link rel="stylesheet" href="style/newprint.css" />
    <meta charset="utf-8">
<script>
<!--
window.onload = function() {
window.close();
}
-->
</script>
</head><body></body></html>
<?php
}else{
    echo "there was an error, close this window and try again";
}
?>
