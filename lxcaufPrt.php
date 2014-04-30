<?php

define("x",0);  define("y",1); 

require("fpdf.php");
require("fpdi.php");
require_once("inc/lxcLib.php");
include_once("inc/config.php");
define("FPDF_FONTPATH","../font/");


$a_id = $_GET["a_id"]; //echo "a_id==".$a_id." ***** ";			//AuftragsId
$print_pdf = $_GET["pdf"];	
$owner = $_GET["owner"];
$c_id = $_GET["c_id"]; 


$aufData = HoleAuftragsDaten($a_id);//print_r($aufData);
$carData = ShowCar($aufData[0]['lxc_a_c_id']);
//print_r($carData);
$posData = HoleAuftragsPositionen($a_id);
//print_r($_SESSION);

$pdf=new FPDF('P','mm','A4');
$pdf->AddPage();
$pdf->SetFont('Helvetica','B','18');
$pdf->Text('20','26','Reparaturauftrag');
$pdf->Text('80','26',$carData["c_ln"]);
$pdf->SetFont('Helvetica','','14');
$pdf->Text('20','35',$carData["cm"]."  ".$carData["ct"]);

//Feste Werte
$pdf->SetFont('Helvetica','','16');
$pdf->Text('22','45','Kunde:');
$pdf->Text('22','52',utf8_decode('Straße').':');
$pdf->Text('22','59','Ort:');
$pdf->Text('22','66','Telefon:');
$pdf->Text('22','73','Mobil:');
$pdf->Text('22','80','Bearbeiter:');
$pdf->Text('22','87','Farbe:');
$pdf->Text('22','94','Hubraum:');
$pdf->Text('108','45','KBA:');
$pdf->Text('108','52','Baujahr:');
$pdf->Text('108','59','FIN:');
$pdf->Text('108','66','MKE:');
$pdf->Text('108','73','AU/HU:');
$pdf->Text('108','80','KM-Stand:');
$pdf->Text('108','87','Abgasschl.:');
$pdf->Text('108','94','Leistung:');

$pdf->SetLineWidth(0.3); 
$pdf->Rect('20', '38', '80', '60'); 
$pdf->Rect('105', '38', '98', '60'); 

//Daten aus DB
$pdf->SetFont('Helvetica','','15');
$pdf->Text('55','45',utf8_decode($carData["ownerstring"]));
$pdf->Text('55','52',utf8_decode($carData["street"]));
$pdf->Text('55','59',utf8_decode($carData["city"]));
$pdf->Text('55','66',$carData["phone"]);
$pdf->Text('55','73',$carData["mobile"]);
$pdf->Text('55','87',$carData["c_color"]);
$pdf->Text('55','94',$carData["vh"]);
$pdf->Text('141','45',$carData["c_2"]." ".$carData["c_3"]);
$pdf->Text('141','52',$carData["c_d"]);
$pdf->Text('141','59',$carData["fin"]);
$pdf->Text('141','66',$carData["mkb"]);
$pdf->Text('141','73',$carData["c_hu"]);
$pdf->Text('141','80',$aufData[0]['lxc_a_km']);
$pdf->Text('141','87',$carData["c_em"]);
$pdf->Text('141','94',$carData["peff"]);

$pdf->SetFont('Helvetica','B','16');
$pdf->SetTextColor(255, 0, 0); 
$pdf->Text('20','105','Fertigstellung:');
$pdf->SetFont('Helvetica','','16');
$pdf->Text('65','105',utf8_decode($aufData[0]['lxc_a_finish_time']));
$pdf->SetTextColor(0, 0, 0); 

$pos_todo[x] = 20;$pos_todo[y] = 130; 

foreach($posData as $index => $element){
	//echo $posData[$index]['lxc_a_pos_todo']."</br>";	;
	$b = 16;
	$y = $pos_todo[y]+$b*$index;	//echo "tst  :  ".$y;
	$pdf->Text($pos_todo[x],$y,utf8_decode($posData[$index]['lxc_a_pos_todo']));//*$b*$index
	}

$pdf->SetFont('Helvetica','','14'); 
$pdf->Text('22','274','Auto-Spar, Datum:');
$pdf->Text('65','274',date('d.m.Y'));  
$pdf->Text('110','274','Unterschrift: __________________');
$pdf->SetFont('Helvetica','','06'); 
$pdf->Text('75','290','Powered by lxcars.de - Freie Kfz-Werkstatt Software');

//print_r($_SESSION);
//$datum = getdate();
//print_r($datum);
//echo date('d.m.Y');
//echo $print_pdf."*****";
if($print_pdf){
	$daten = $pdf->OutPut('Reparaturauftrag_'.$_GET["a_id"].'.pdf',"I");
}
else{
	$test = $pdf->OutPut('out.pdf');
	system("$lpr out.pdf");
	header("Location: lxcmain.php?task=3&owner=".$owner."&c_id=".$c_id);
}

//print_r ($aufData);
//print_r($posData);



?>
