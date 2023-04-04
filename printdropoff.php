<?php
//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Print';
$linkpage = 'printdropoff.php';
$changecustomer = '0';
$currentday = date('Y-n-j');
$appttime = '8:00';
$quicksearch = '0';
$location = '1';
$invoicesubtotal = '0';
$paymentid = '-1';
$invoiceid = '0';

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

    $sql1 = "SELECT `variable` FROM `global_settings` WHERE `id` = '7'";
    $sth1 = $pdocxn->prepare($sql1);
    $sth1->execute();
    while($row1 = $sth1->fetch(PDO::FETCH_ASSOC))
    {
        $footertag = $row1['variable'];
    }
    if(isset($_GET['invoiceid']))
{
    $invoiceid = $_GET['invoiceid'];
    $siteid = $_GET['siteid'];
    $scheduletable = "dropoffloc".$siteid;
    $litable = "drop".$siteid."line_items";
	}
if(isset($_POST['invoiceid']))
{
    $invoiceid = $_POST['invoiceid'];
    $siteid = $_POST['siteid'];
    $scheduletable = "dropoffloc".$siteid;
    $litable = "drop".$siteid."line_items";
	}

    if($invoiceid > '0')
    {
    if(isset($_POST['noprint']))
    {
    $copies = '0';
    }
    if(isset($_POST['print1']))
    {
    $copies = '1';
    }
    $sth3 = $pdocxn->prepare('SELECT * FROM '.$scheduletable.' WHERE `id` = :inv');
    $sth3->bindParam(':inv',$invoiceid);
    $sth3->execute();
    $row3 = $sth3->fetch(PDO::FETCH_ASSOC);
    $isgof = $row3['lof'];
    $accountid = $row3['accountid'];
    $invoicenumber = $row3['invoiceid'];
    $typeid = $row3['type'];
    $userid = $row3['userid'];
    $mileagein = $row3['mileagein'];
    $mileageout = $row3['mileageout'];
    $location = $row3['location'];
    $taxtotal = $row3['tax'];
    $dropoffinfo = $row3['dropoffinfo'];
    $subtotal = $row3['subtotal'];
    $schedule1 = $row3['schedule'];
    $dsubtotal = money_format('%(#0.2n',$subtotal);
    $total = $row3['total'];
    $dtotal = money_format('%(#0.2n',$total);
    $invoicedate = $row3['date'];
    $invoicedate2 = new DateTime($invoicedate);
    $appttime = $invoicedate2->format('g:i');
    if($schedule1 == '1')
    {
        $displayschedule = 'Tire';
    }else    {
        $displayschedule = 'Service';
    }
    $apptdate = $invoicedate2->format('D, F j');
    if($userid > '0')
    $sth5 = $pdocxn->prepare('SELECT `username` FROM `employees` WHERE `id` = :userid');
    $sth5->bindParam(':userid',$userid);
    $sth5->execute();
    while($row5 = $sth5->fetch(PDO::FETCH_ASSOC))
    {
    $salesperson = $row5['username'];
    }
    if($accountid > '0')
    {
    $sth4 = $pdocxn->prepare('SELECT * FROM `accounts` WHERE `accountid` = :acct');
    $sth4->bindParam(':acct',$accountid);
    $sth4->execute();
    while($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
    {
    $fname = $row4['firstname'];
    $lname = $row4['lastname'];
    $address1 = $row4['address'];
    $address2 = $row4['address2'];
    $city = $row4['city'];
    $state = $row4['state'];
    $zip = $row4['zip'];
    $citystatezip = $city.", ".$state." ".$zip;
    $phone1 = $row4['phone1'];
    $phone2 = $row4['phone2'];
    $phone3 = $row4['phone3'];
    $phone4 = $row4['phone4'];
    $contact1 = $row4['contact1'];
    $contact2 = $row4['contact2'];
    $contact3 = $row4['contact3'];
    $contact4 = $row4['contact4']; 
    $fax = $row4['fax'];
    $email = $row4['email'];
    $creditlimit = $row4['creditlimit'];
    $taxid = $row4['taxid'];
    $priceclass = $row4['priceclass'];
    $taxclass = $row4['taxclass'];
    $nationalaccount = $row4['nationalaccount'];
    $requirepo = $row4['requirepo'];
    $accounttype = $row4['accounttype'];
    $flag = $row4['flag'];
    $comment = $row4['comment'];
    $insertdate = $row4['insertdate'];
    $lastactivedate = $row4['lastactivedate'];
    $fullname = $fname." ".$lname;
    $sth4 = $pdocxn->prepare('UPDATE '.$scheduletable.' SET `status` = \'2\'WHERE `id` = :inv');
    $sth4->bindParam(':inv',$invoiceid);
    $sth4->execute();
    if($phone1 > '0')
        {
            $dphone1 = "<tr><td class=\"left\">Phone 1: ".$phone1."</td><td>Contact: ".$contact1."</td></tr>";
        }
        else
            {
                $dphone1 = "";
            }
    if($phone2 > '0')
        {
            $dphone2 = "<tr><td class=\"left\">Phone 2: ".$phone2."</td><td>Contact: ".$contact2."</td></tr>";
        }
        else
            {
                $dphone2 = "";
            }
    if($phone3 > '0')
        {
            $dphone3 = "<tr><td class=\"left\">Phone 3: ".$phone3."</td><td>Contact: ".$contact3."</td></tr>";
        }
        else
            {
                $dphone3 = "";
            }
    if($phone4 > '0')
        {
            $dphone4 = "<tr><td class=\"left\">Phone 4: ".$phone4."</td><td>Contact: ".$contact4."</td></tr>";
        }
        else
            {
                $dphone4 = "";
            }
    if($fax > '0')
        {
            $dfax = "<tr><td colspan=\"2\" class=\"left\">Fax: ".$fax."</td></tr>";
        }
        else
            {
                $dfax = "";
            }
    if($creditlimit > '0')
        {
            $creditlimit = $creditlimit;
        }
        else
            {
                $creditlimit = "0";
            }
    if($taxid > '0')
        {
            $taxid = $taxid;
        }
        else
            {
                $taxid = "0";
            }
    if($requirepo == '1')
        {
            $requirepo = "Yes";
        }
        else
            {
                $requirepo = "No";
            }
    if($priceclass == '1')
        {
            $dpriceclass = "Consumer";
        }
        else
            {
                $dpriceclass = "Resale";
            }
    $dtaxclass = "Consumer";
    $dlastactivedate = "12/20/2017";
    }
    }

    $sth8 = $pdocxn->prepare('SELECT * FROM `locations` WHERE `id` = :locationid');
    $sth8->bindParam(':locationid',$siteid);
    $sth8->execute();
    while($row8 = $sth8->fetch(PDO::FETCH_ASSOC))
    {
    $displaystorename = $row8['displaystorename'];
    $storeaddress1 = $row8['address'];
    $storecity = $row8['city'];
    $storestate = $row8['state'];
    $storezip = $row8['zip'];
    $storeaddress2 = $storecity.", ".$storestate." ".$storezip;
    $storenumber = $row8['phone'];
    $storefax = $row8['fax'];
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
window.print();
//window.location.replace('schedule.php');
  window.open('','_parent',''); 
  self.close(); 
}
-->
    </script>
    </head>
    <body>
    <div class="page-header" style="text-align: center">
            <table cellpadding="0" cellspacing="0" >
                <tr class="top">
                <?php
                                if($license > '0')
                                {
                                    $licdisplay = '<u>'.$license.'__'.$displaystate.'</u>__';
                                }else{
                                    $licdisplay = '______';
                                }
                                if($vin > '0')
                                {
                                    $vinlen = strlen($vin);
                                    if($vinlen = '16')
                                    {
                                        $v2 = substr($vin,9,8);
                                        $v1 = substr($vin,0,9);
                                        $vindisplay = $v1."<u><b>".$v2."</b></u>";
                                    }else{
                                        $vindisplay = $vin;
                                    }
                                }else{
                                    $vindisplay = '______';
                                }
                                if($mileagein > '0')
                                {
                                    $mileagedisplay = '<u>'.$mileagein.'</u>__';
                                }else{
                                    $mileagedisplay = '______';
                                }
                                ?>
                                <td class="title2">
                                Drop Off: <u><?php echo $appttime; ?></u>&nbsp;&nbsp;&nbsp;<?php echo $displayschedule; ?></td>
                                </tr>
                                <tr class="top">
                                <td class="title2">
                                    <?php echo $apptdate; ?>
                            </td></tr>
                                <tr class="top"><td class="title3">
                                Called After Completed <input type="checkbox"></td>
                            </tr></tbody>
    </table></div>
      <div class="page-footer">
      <?php 
      echo $footertag;
      ?>
      </div>
    <table cellpadding="0" cellspacing="0" width="100%">
    <thead>
          <tr>
            <td>
              <!--place holder for the fixed-position header-->
              <div class="page-header-space"></div>
            </td>
          </tr></thead>
        <tbody>
    
    <tr><td><div class="page" >
    <table>
    <tr class="information">
                                <td class="tdborder" width="33%">
                                    <b><?php echo $fullname; ?></b><br>
                                    <?php echo $phone1; ?><br>
                                    <?php echo $phone2; ?><br>
                                </td>
                                <td class="tdborder" width="33%">
                                    <b>Drop off ID # <?php echo $invoiceid; ?></b><br>
                                    Date: <?php echo $displaydate; ?><br>
                                    Salesperson: <?php echo $salesperson; ?>
                                </td>
                                <td class="tdborderright" width="33%"><?php echo $dropoffinfo; ?></td>  
                            </tr>
                </table><table cellpadding="0" cellspacing="0">
                <tr class="heading">
                    <td width="5%" class="center">
                        Qty
                    </td>
                    <td colspan="2" width="75%">
                        Description
                    </td>
                </tr>
    <?php
    $sth4 = $pdocxn->prepare('SELECT * FROM '.$litable.' WHERE invoiceid = :inv ORDER BY linenumber ASC');
    $sth4->bindParam(':inv',$invoiceid);
    $sth4->execute();
    $linecount = $sth4->rowCount();
        while($row4 = $sth4->fetch(PDO::FETCH_ASSOC))
    {
    $lineid = $row4['id'];
    $invqty = $row4['qty'];
    $totallineamount = $row4['totallineamount'];
    $invpartid = $row4['partid'];
    $invpackageid = $row4['packageid'];
    $invserviceid = $row4['serviceid'];
    $invcomment1 = $row4['comment'];
    $invcomment = nl2br($invcomment1);
    $fet = $row4['fet'];
    $printflag = $row4['printflag'];
    $singleprice = $totallineamount / $invqty;
    $dextprice = money_format('%(#0.2n',$totallineamount);
    $dsingleprice = number_format($singleprice, 2);
    $linenumber = $row4['linenumber'];
    $invoicesubtotal = $invoicesubtotal+$totallineamount;
    if($invqty == '0')
    {
     $invqty = '';
     $dextprice = '';
     $dsingleprice = '';
    }
    if($printflag > '0')
    {
    echo "<tr class=\"item\"><td class=\"centertop\">".$invqty."</td><td class=\"left\" colspan=\"2\"><b>".$invcomment."</b>   Completed ___</td></tr>\n";
    }}
    ?>
    <tr><td><br /><br /></td></tr>
    <?php
    if($isgof > '0')
    {
        echo "<tr class=\"item\"><td class=\"center\" colspan=\"3\"><b>Sitcker _____&nbsp;&nbsp;&nbsp;Maintenance Light Reset _____</td></tr>";
        echo "<tr class=\"item\"><td class=\"center\" colspan=\"3\"><b>Oil type & weight __________&nbsp;&nbsp;&nbsp;Qts.________</td></tr>";
        echo "<tr><td><br /></td></tr>";
    }
?>
<tr><td class="center" colspan="3">Tech Initials __________&nbsp;&nbsp;&nbsp;Lug Nuts Initials __________</td></tr>
            </tbody>
    
    <tfoot><tr><td><br /><br />
        <div class="footer-space">&nbsp;</div>
      </td></tr></tfoot>
            </table>
    </body>
    </html>
    <?php
    }
    else
    {
    echo $link = 'dropoffs.php';
    header('location:'.$link);
    }
    ?>