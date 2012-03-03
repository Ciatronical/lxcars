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

//Positionierung der Variablen im PDF
// FPDF beginnt oben links
$repfont='Helvetica';	
$repsizeN='12';
$repsizeL='14'; 
$repsizeXL='18';


$ln[x] = 88; 	$ln[y] = 26;	//Kennzeichen 
$cn[x] = 20; 	$cn[y] = 32; 	//CarName

$ow[x] = 45;		$ow[y] = 42;
$street[x] = 45;	$street[y] = 47.9;
$city[x] = 45;		$city[y] = 53.4;
$phone[x] = 45;	$phone[y] = 59.3;
$mobile[x] = 45;	$mobile[y] = 64.7;
$emp[x] = 45;		$emp[y] = 70.3;
$fertig[x] = 61;  $fertig[y] = 86;
$kba[x] = 133;		$kba[y] = 42.0;  
$cd[x] = 133;		$cd[y] = 48;
$fin[x] = 133;		$fin[y] = 53.7;
$mkb[x] = 133;		$mkb[y] = 59.4;
$hu[x] = 133;		$hu[y] = 65.4;
$km[x] = 133;		$km[y] = 70.8;
$abg[x] = 133;    $abg[y] = 76.6;
$pos_todo[x] = 20;$pos_todo[y] = 130; 
$datum[x] = 40;	$datum[y] = 274;  
$aufData = HoleAuftragsDaten($a_id);//print_r($aufData);
$carData = ShowCar($aufData[0]['lxc_a_c_id']);
//print_r($carData);
$posData = HoleAuftragsPositionen($a_id);
//print_r($_SESSION);

$pdf = new FPDI('P','mm','A4');
$seiten=$pdf->setSourceFile("vorlage/lxcRepAuftrag.pdf");
$hdl=$pdf->ImportPage(1); //wofür?? für die Vorlage
$pdf->addPage();
$pdf->useTemplate($hdl);
$pdf->SetFont($repfont,'B',$repsizeXL);
$pdf->Text($ln[x],$ln[y],$carData["c_ln"]);
$pdf->SetFont($repfont,'',$repsizeL);
$pdf->Text($cn[x],$cn[y],$carData["cm"]."  ".$carData["ct"]);
$pdf->Text($ow[x],$ow[y],utf8_decode($carData["ownerstring"]));
$pdf->Text($street[x],$street[y],utf8_decode($carData["street"]));
$pdf->Text($city[x],$city[y],utf8_decode($carData["city"]));
$pdf->Text($phone[x],$phone[y],$carData["phone"]);
$pdf->Text($mobile[x],$mobile[y],$carData["mobile"]);
$pdf->Text($emp[x],$emp[y],utf8_decode($_SESSION["employee"]));

$pdf->Text($kba[x],$kba[y],$carData["c_2"]." ".$carData["c_3"]);
$pdf->Text($cd[x],$cd[y],$carData["c_d"]);
$pdf->Text($fin[x],$fin[y],$carData["fin"]);
$pdf->Text($mkb[x],$mkb[y],$carData["mkb"]);
$pdf->Text($hu[x],$hu[y],$carData["c_hu"]);
$pdf->Text($km[x],$km[y],$aufData[0]['lxc_a_km']);
$pdf->Text($abg[x],$abg[y],$carData["c_em"]);
$pdf->Text($fertig[x],$fertig[y],utf8_decode($aufData[0]['lxc_a_finish_time']));
$pdf->Text($datum[x],$datum[y],date('d.m.Y'));

foreach($posData as $index => $element){
	//echo $posData[$index]['lxc_a_pos_todo']."</br>";	;
	$b = 16;
	$y = $pos_todo[y]+$b*$index;	//echo "tst  :  ".$y;
	$pdf->Text($pos_todo[x],$y,utf8_decode($posData[$index]['lxc_a_pos_todo']));//*$b*$index
	}
	
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
