<?php
	//include mysql file
	include_once ('scripts/mysql.php');
if('1' == '2')
{
	$sql1 = 'SELECT `id`,`invoiceid` FROM `inventory_transactions` WHERE `transactiontype` > \'40\'';
	$sth1 = $pdocxn->prepare($sql1);
	$sth1->execute();
		while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
		{
        $invoiceid = $row1['invoiceid'];
        $id = $row1['id'];
        $sql2 = 'SELECT `accountid` FROM `invoice` WHERE `id` = :invoiceid';
        $sth2 = $pdocxn->prepare($sql2);
        $sth2->bindParam(':invoiceid',$invoiceid);
        $sth2->execute();
            while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
            {
            $accountid = $row2['accountid'];
            }
            $sql3 = 'UPDATE `inventory_transactions` SET `accountid`=:accountid WHERE `id` = :id';
            $sth3 = $pdocxn->prepare($sql3);
            $sth3->bindParam(':accountid',$accountid);
            $sth3->bindParam(':id',$id);
            $sth3->execute();
            echo $accountid.'<br />';
        }}else{


            $sql1 = 'SELECT `id`,`accountid`,`location`,`invoicedate` FROM `invoice` WHERE `type` = \'1\' AND `invoicedate` > \'2020-04-04\'';
            $sth1 = $pdocxn->prepare($sql1);
            $sth1->execute();
                while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
                {
                    $invoiceid = $row1['id'];
                    $accountid = $row1['accountid'];
                    $siteid = $row1['location'];
                    $invoicedate = $row1['invoicedate'];
                    $invoicetype = '1';

                    $sql2 = 'SELECT `id`,`partid`,`qty`,`amount` FROM `line_items` WHERE `invoiceid` = :invoiceid AND `partid` > \'1\'';
                    $sth2 = $pdocxn->prepare($sql2);
                    $sth2->bindParam(':invoiceid',$invoiceid);
                    $sth2->execute();
                        while($row2 = $sth2->fetch(PDO::FETCH_ASSOC))
                        {
                $lineid = $row2['id'];
                $partid = $row2['partid'];
                $qty = $row2['qty'];
                $amount = $row2['amount'];
                $negqty = $qty * -1;
                $record = '1';
                
                    $sql3 = 'SELECT `id` FROM `inventory_transactions` WHERE `lineid` = :lineid';
                    $sth3 = $pdocxn->prepare($sql3);
                    $sth3->bindParam(':lineid',$lineid);
                    $sth3->execute();
                            $checkid = $sth3->rowCount();
                            
                    if($checkid < '1')
                    {
                        echo 'do nothing';
                    }else{
                        $sql4 = 'INSERT INTO `inventory_transactions`(`partid`,`datetime`,`qty`,`transactiontype`,`invoiceid`,`accountid`,`location`,`amount`,`record`,`lineid`) VALUES (:partid,:datetime,:qty,:transactiontype,:invoiceid,:accountid,:location,:amount,:record,:lineid)';
                        $sth4 = $pdocxn->prepare($sql4);
                        $sth4->bindParam(':partid',$partid);
                        $sth4->bindParam(':datetime',$invoicedate);
                        $sth4->bindParam(':qty',$negqty);
                        $sth4->bindParam(':transactiontype',$invoicetype);
                        $sth4->bindParam(':invoiceid',$invoiceid);
                        $sth4->bindParam(':accountid',$accountid);
                        $sth4->bindParam(':location',$siteid);
                        $sth4->bindParam(':amount',$amount);
                        $sth4->bindParam(':record',$record);
                        $sth4->bindParam(':lineid',$lineid);
                        $sth4->execute()or die(print_r($sth4->errorInfo(), true));
                        echo $invoicedate.'<br />';
                    }}}}
        ?>