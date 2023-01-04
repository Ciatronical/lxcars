<?php
$pdf->AddPage();
$pdf->setSourceFile("vorlage/versicherung_an_eides_statt_1.pdf");
$tplId = $pdf->importPage(1);
$pdf->useTemplate($tplId);
$height = $pdf->GetPageHeight();
$pdf->SetFont('Helvetica', '', 10.5);

$pdf->Text(152, $height - 239.5, 'Rehfelde, '.date('d.m.Y'));
if(isset($_POST['name']) && !empty($_POST['name'])) $pdf->Text(39, $height - 226.6, utf8_decode($_POST['name']));
if(isset($_POST['vorname']) && !empty($_POST['vorname'])) $pdf->Text(100, $height - 226.6, utf8_decode($_POST['vorname']));
if(isset($_POST['gebdatum']) && !empty($_POST['gebdatum'])) $pdf->Text(163, $height - 226.6, utf8_decode($_POST['gebdatum']));

$anschrift1 = '';
$anschrift2 = '';

if(isset($_POST['strasse']) && !empty($_POST['strasse'])) $anschrift1 = $_POST['strasse'];
if(isset($_POST['hsnr']) && !empty($_POST['hsnr'])) $anschrift1 .= ' '.$_POST['hsnr'];
if(isset($_POST['plz']) && !empty($_POST['plz'])) $anschrift2 = $_POST['plz'];
if(isset($_POST['ort']) && !empty($_POST['ort'])) $anschrift2 .= ' '.$_POST['ort'];

if(!empty($anschrift1)) $pdf->Text(54, $height - 214, utf8_decode($anschrift1));
if(!empty($anschrift2)) $pdf->Text(54, $height - 209, utf8_decode($anschrift2));

if(isset($_POST['kennzeichen']) && !empty($_POST['kennzeichen'])) $pdf->Text(69, $height - 192.6, utf8_decode($_POST['kennzeichen']));

$pdf->AddPage();
$pdf->setSourceFile("vorlage/versicherung_an_eides_statt_2.pdf");
$tplId = $pdf->importPage(1);
$pdf->useTemplate($tplId);
$height = $pdf->GetPageHeight();
$pdf->SetFont('Helvetica', '', 10.5);

if(isset($_POST['fahrzeugschein'])) $pdf->Text(30.4, $height - 242.5, 'X');
if(isset($_POST['fahrzeugbrief'])) $pdf->Text(30.4, $height - 238.5, 'X');
if(isset($_POST['amtlicheskenn'])) $pdf->Text(30.4, $height - 234.5, 'X');
if(isset($_POST['roterschein'])) $pdf->Text(30.4, $height - 230, 'X');
if(isset($_POST['fuehrerschein'])) $pdf->Text(30.4, $height - 225.5, 'X');
if(isset($_POST['betriebserlaubnis'])) $pdf->Text(30.4, $height - 221.25, 'X');
if(isset($_POST['sonstiges']))
{
	$pdf->Text(30.4, $height - 217.2, 'X');
	if(isset($_POST['sonstiges-text']) && !empty($_POST['sonstiges-text'])) $pdf->Text(59, $height - 217, utf8_decode($_POST['sonstiges-text']));
}

if(isset($_POST['erklaerung']) && !empty($_POST['erklaerung']))
{
	$pdf->SetXY(25, $height - 197);
	$pdf->MultiCell(160, 4.5, utf8_decode($_POST['erklaerung']), 0, 'L');
}

