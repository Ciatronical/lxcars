<?php

define("x",0);  define("y",1); 

require("fpdf.php");
//require("fpdi.php");
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
$pdf->Text('20','26','Auto-Spar Reparaturauftrag');
$pdf->Text('160','26',utf8_decode($carData["c_ln"]));
$pdf->SetFont('Helvetica','','14');
$pdf->Text('20','35',$carData["cm"]."  ".$carData["ct"]);

//Feste Werte
$pdf->SetFont('Helvetica','','16');
$pdf->Text('22','45','Kunde:');
$pdf->Text('22','52',utf8_decode('Straße').':');
$pdf->Text('22','59','Ort:');
$pdf->Text('22','66','Tele.:');
$pdf->Text('22','73','Mobil:');
$pdf->Text('22','80','Bearb.:');
$pdf->Text('22','87','Farbe:');
$pdf->Text('22','94','Hubr.:');
$pdf->Text('124','45','KBA:');
$pdf->Text('124','52','Baujahr:');
$pdf->Text('124','59','FIN:');
$pdf->Text('124','66','MK:');
$pdf->Text('124','73','AU/HU:');
$pdf->Text('124','80','KM:');
$pdf->Text('124','87','Abgas.:');
$pdf->Text('124','94','Peff:');

$pdf->SetLineWidth(0.3); 
$pdf->Rect('20', '38', '100', '60'); 
$pdf->Rect('122', '38', '84', '60'); 

//Daten aus DB
$pdf->SetFont('Helvetica','','14');
// Besitzerstring einkürzen, wenn der dieser zu lang wird
if(strlen($carData["ownerstring"])>34){
    $carData["ownerstring"] = substr($carData["ownerstring"],0,34).".";
}
$pdf->Text('43','45',utf8_decode($carData["ownerstring"]));
$pdf->Text('43','52',utf8_decode($carData["street"]));
$pdf->Text('43','59',utf8_decode($carData["city"]));
$pdf->Text('43','66',$carData["phone"]);
$pdf->Text('43','73',$carData["mobile"]);
$pdf->Text('43','87',$carData["c_color"]);
$pdf->Text('43','94',$carData["vh"]);
$pdf->Text('148','45',$carData["c_2"]." ".$carData["c_3"]);
$pdf->Text('148','52',$carData["c_d"]);
$pdf->Text('148','59',$carData["fin"]);
$pdf->Text('148','66',$carData["mkb"]);
$pdf->Text('148','73',$carData["c_hu"]);
$pdf->Text('148','80',$aufData[0]['lxc_a_km']);
$pdf->Text('148','87',$carData["c_em"]);
$pdf->Text('148','94',$carData["peff"]);

$pdf->SetFont('Helvetica','B','16');
$pdf->SetTextColor(255, 0, 0); 
$pdf->Text('20','110','Fertigstellung:');
$pdf->SetFont('Helvetica','','16');
$pdf->Text('75','110',utf8_decode($aufData[0]['lxc_a_finish_time']));
$pdf->SetTextColor(0, 0, 0); 

$pos_todo[x] = 20;$pos_todo[y] = 130; 

foreach($posData as $index => $element){
	//echo $posData[$index]['lxc_a_pos_todo']."</br>";	;
	$b = 16;
	$y = $pos_todo[y]+$b*$index;	//echo "tst  :  ".$y;
	$pdf->Text($pos_todo[x],$y,utf8_decode($posData[$index]['lxc_a_pos_todo']));//*$b*$index
	}

$pdf->SetFont('Helvetica','','14');
$pdf->Text('22','270','Datum:');
$pdf->Text('45','270',date('d.m.Y'));
$pdf->Text('110','270','Unterschrift: __________________');
$pdf->SetTextColor(255, 0, 0);
$pdf->Text('22','280',utf8_decode('Endkontrolle durchgeführt von:'));
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Helvetica','','08');
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
