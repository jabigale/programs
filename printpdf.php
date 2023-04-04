<?php
//submit form general

//include mysql file
include_once ('scripts/mysql.php');
include_once ('scripts/global.php');
//default page variables
$title = 'Print';
$linkpage = 'printpdf.php';
$currentday = date('Y-n-j');
$quicksearch = '0';
$location = '1';
$invoicesubtotal = '0';

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

if(isset($_POST['invoiceid']))
{
if(isset($_POST['noprint']))
{
$copies = '0';
}
if(isset($_POST['print1']))
{
$copies = '1';
}
if(isset($_POST['print2']))
{
$copies = '2';
}
if(isset($_POST['print3']))
{
$copies = '3';
}
	//submit form general
	if(isset($_POST['invoiceid']))
	{
	$invoiceid = $_POST['invoiceid'];
	}
else {
	$invoiceid = '0';
}

if(isset($_POST['enterpayment']))
{
$paymentdate = $_POST['paymentdate'];
$sql8 = "SELECT `location`,`invoicedate`,`accountid` FROM `invoice` WHERE `id` = :transid";
$sth8 = $pdocxn->prepare($sql8);
$sth8->bindParam(':transid',$invoiceid);
$sth8->execute();
while($row8 = $sth8->fetch(PDO::FETCH_ASSOC))
{
	$locationid = $row8['location'];
	$accountid = $row8['accountid'];
	$paymentdate = $row8['invoicedatedate'];
$paymentdate2 = new DateTime($paymentdate);
$displaypaymentdate = $paymentdate2->format('n/j/Y');
}
$sql8 = "INSERT INTO `translink` (`transid`) VALUES (:transid)";
$sth8 = $pdocxn->prepare($sql8);
$sth8->bindParam(':transid',$invoiceid);
$sth8->execute();


$sql8 = "SELECT `linkid` FROM `translink` WHERE `transid` = :transid";
$sth8 = $pdocxn->prepare($sql8);
$sth8->bindParam(':transid',$invoiceid);
$sth8->execute();
while($row8 = $sth8->fetch(PDO::FETCH_ASSOC))
{
	$linkid = $row8['linkid'];
}

$cash = $_POST['cash'];
$checkamount = $_POST['checkamount'];
$checknumber = $_POST['checknumber'];
$cc1amount = $_POST['cc1'];
$cc1type = $_POST['cc1type'];
$cc2amount = $_POST['cc2'];
$cc2type = $_POST['cc2type'];
$cc3amount = $_POST['cc3'];
$cc3type = $_POST['cc3type'];
$totalpaymentamount = $cash + $checkamount + $cc1amount + $cc2amount + $cc3amount;
$paymenttype = '6';

$paysql = "INSERT INTO `invoice`(`accountid`,`type`,`location`,`creationdate`,`invoicedate`) VALUES (:accountid,:type,:location,:creationdate,:paymentdate)";
$paysth = $pdocxn->prepare($paysql);
$paysth->bindParam(':accountid',$accountid);
$paysth->bindParam(':type',$paymenttype);
$paysth->bindParam(':location',$locationid);
$paysth->bindParam(':creationdate',$currentdate);
$paysth->bindParam(':paymentdate',$paymentdate);
$paysth->execute();
$paymentlinkid = $pdocxn->lastInsertId();

$paysql = "INSERT INTO `translink`(`transid`,`linktoid`,`amount`) VALUES (:transid,:linktoid,:amount)";
$paysth = $pdocxn->prepare($paysql);
$paysth->bindParam(':transid',$paymentlinkid);
$paysth->bindParam(':linktoid',$linkid);
$paysth->bindParam(':amount',$totalpaymentamount);
$paysth->execute();
//$quickpayments cash(8) Check(9) Credit(10)
if($cash > '0')
{
$saletype = '12';
$linitemtype = '8';
$paymentdescription = "Cash";
$enterpaymentsql = "INSERT INTO `line_items`(`invoiceid`,`comment`,`totallineamount`,`lineitem_typeid`,`lineitem_saletype`) VALUES (:invoiceid,:comment,:totallineamount,:lineitem_typeid,:lineitem_saletype)";
$enterpaymentsth = $pdocxn->prepare($enterpaymentsql);
$enterpaymentsth->bindParam(':invoiceid',$paymentlinkid);
$enterpaymentsth->bindParam(':lineitem_typeid',$linitemtype);
$enterpaymentsth->bindParam(':totallineamount',$cash);
$enterpaymentsth->bindParam(':comment',$paymentdescription);
$enterpaymentsth->bindParam(':lineitem_saletype',$saletype);
$enterpaymentsth->execute();
}
if($checkamount > '0')
{
$saletype = '12';
$linitemtype = '9';
$enterpaymentsql = "INSERT INTO `line_items`(`invoiceid`,`comment`,`totallineamount`,`lineitem_typeid`,`lineitem_saletype`) VALUES (:invoiceid,:comment,:totallineamount,:lineitem_typeid,:lineitem_saletype)";
$enterpaymentsth = $pdocxn->prepare($enterpaymentsql);
$enterpaymentsth->bindParam(':invoiceid',$paymentlinkid);
$enterpaymentsth->bindParam(':lineitem_typeid',$linitemtype);
$enterpaymentsth->bindParam(':totallineamount',$checkamount);
$enterpaymentsth->bindParam(':comment',$checknumber);
$enterpaymentsth->bindParam(':lineitem_saletype',$saletype);
$enterpaymentsth->execute();
}
if($cc1amount > '0')
{	
$saletype = '12';
$linitemtype = '10';
$enterpaymentsql = "INSERT INTO `line_items`(`invoiceid`,`comment`,`totallineamount`,`lineitem_typeid`,`lineitem_saletype`) VALUES (:invoiceid,:comment,:totallineamount,:lineitem_typeid,:lineitem_saletype)";
$enterpaymentsth = $pdocxn->prepare($enterpaymentsql);
$enterpaymentsth->bindParam(':invoiceid',$paymentlinkid);
$enterpaymentsth->bindParam(':lineitem_typeid',$linitemtype);
$enterpaymentsth->bindParam(':totallineamount',$cc1amount);
$enterpaymentsth->bindParam(':comment',$cc1type);
$enterpaymentsth->bindParam(':lineitem_saletype',$saletype);
$enterpaymentsth->execute();
}
if($cc2amount > '0')
{	
$saletype = '12';
$linitemtype = '10';
$enterpaymentsql = "INSERT INTO `line_items`(`invoiceid`,`comment`,`totallineamount`,`lineitem_typeid`,`lineitem_saletype`) VALUES (:invoiceid,:comment,:totallineamount,:lineitem_typeid,:lineitem_saletype)";
$enterpaymentsth = $pdocxn->prepare($enterpaymentsql);
$enterpaymentsth->bindParam(':invoiceid',$paymentlinkid);
$enterpaymentsth->bindParam(':lineitem_typeid',$linitemtype);
$enterpaymentsth->bindParam(':totallineamount',$cc2amount);
$enterpaymentsth->bindParam(':comment',$cc2type);
$enterpaymentsth->bindParam(':lineitem_saletype',$saletype);
$enterpaymentsth->execute();
}
if($cc3amount > '0')
{	
$saletype = '12';
$linitemtype = '10';
$enterpaymentsql = "INSERT INTO `line_items`(`invoiceid`,`comment`,`totallineamount`,`lineitem_typeid`,`lineitem_saletype`) VALUES (:invoiceid,:comment,:totallineamount,:lineitem_typeid,:lineitem_saletype)";
$enterpaymentsth = $pdocxn->prepare($enterpaymentsql);
$enterpaymentsth->bindParam(':invoiceid',$paymentlinkid);
$enterpaymentsth->bindParam(':lineitem_typeid',$linitemtype);
$enterpaymentsth->bindParam(':totallineamount',$cc3amount);
$enterpaymentsth->bindParam(':comment',$cc3type);
$enterpaymentsth->bindParam(':lineitem_saletype',$saletype);
$enterpaymentsth->execute();
}
}

$sth3 = $pdocxn->prepare('SELECT * FROM invoice WHERE id = :inv');
$sth3->bindParam(':inv',$invoiceid);
$sth3->execute();
$row3 = $sth3->fetch(PDO::FETCH_ASSOC);
$accountid = $row3['accountid'];
$invoicenumber = $row3['invoiceid'];
$type = $row3['type'];
$userid = $row3['userid'];
$vehicleid = $row3['vehicleid'];
$mileagein = $row3['mileagein'];
$mileageout = $row3['mileageout'];
$location = $row3['location'];
$taxtotal = $row3['tax'];
$subtotal = $row3['subtotal'];
$total = $row3['total'];
$invoicedate = $row3['invoicedate'];
$invoicedate2 = new DateTime($invoicedate);
$displaydate = $invoicedate2->format('M j, Y');

if($accountid > '0')
{
$sth4 = $pdocxn->prepare('SELECT * FROM accounts WHERE acctid = :acct');
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
}}
if($vehicleid > '0')
{
$sql5 = 'SELECT * FROM `vehicles` WHERE `id` = :vehicleid';
$sth5 = $pdocxn->prepare($sql5);
$sth5->bindParam(':vehicleid',$vehicleid);
$sth5->execute();
if ($sth5->rowCount() > 0)
{
while($row5 = $sth5->fetch(PDO::FETCH_ASSOC))
	{
	$year = $row5['year'];
	$model = $row5['model'];
	$make = $row5['make'];
	$vin = $row5['vin'];
	$sobmodel = $row5['submodel'];
	$engine = $row5['engine'];
	$license = $row5['license'];
	$vehiclestate = $row5['state'];
	$description = $row5['cfdescription'];
if($year > '1')
{
$dvehicleinfo = "\n<b>".$year." ".$make." ".$model." ".$submodel." ".$engine."</b><br>VIN: ".$vin."<br>License: ".$license." (".$vehiclestate.")<br>Mileage: ".$mileagein."</td>";
}
else
{
$dvehicleinfo = "\n<b>".$description."</b><br>VIN: ".$vin."<br>License: ".$license." (".$vehiclestate.")<br>Mileage: ".$mileagein."</td>";
}

$sql6 = 'SELECT `name` FROM `invoice_type` WHERE `id` = :type';
$sth6 = $pdocxn->prepare($sql6);
$sth6->bindParam(':type',$type);
$sth6->execute();
while($row6 = $sth6->fetch(PDO::FETCH_ASSOC))
{
	$typename = $row6['name'];
}
}
}
}
$sth7 = $pdocxn->prepare('SELECT * FROM notes WHERE `invoiceid` = :invoiceid');
$sth7->bindParam(':invoiceid',$invoiceid);
$sth7->execute();
while($row7 = $sth7->fetch(PDO::FETCH_ASSOC))
{
$note = $row7['note'];
$displaynote = nl2br($note);
$split = '<br />';
//if updated to 5.3 or later $line0 = strstr($displaynote,$split);

$linenum = preg_split('/\n|\r/',$note);

$lni = '0';
$linenumber = count($linenum);
while($lni <= $linenumber)
{
${"line".$lni} = $linenum[$lni];
${"str".$lni} = strlen(${"line".$lni});
$lni ++;
}
$linenumber = count($linenum);
}
require('fpdf/fpdf.php');

class PDF extends FPDF
{
// Page header
function Header()
{
    // Logo
    $this->Image('images/logo.jpg',10,6,30);
    // Arial bold 15
    $this->SetFont('Arial','B',15);
    // Move to the right
    $this->Cell(80);
    // Title
    $this->Cell(30,10,'<table><tr><td><b>'.$typename.' #: '.$invoicenumber.'</b><br>PO #: '.$ponumber.'<br>Date: '.$displaydate.'<br>Due: '.$displaydate.'<br>Salesperson: '.$salesperson.'</td></tr></table>',1,0,'C');
    // Line break
    $this->Ln(20);
}

// Page footer
function Footer()
{
    // Position at 1.5 cm from bottom
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','I',8);
    // Page number
    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
}
}

// Instanciation of inherited class
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);
for($i=1;$i<=40;$i++)
    $pdf->Cell(0,10,'Printing line number '.$i,0,1);
$pdf->Output();
?>