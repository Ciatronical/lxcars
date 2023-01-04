<?php
require    'fpdf.php';
use setasign\Fpdi\Fpdi;
require 'fpdi/src/autoload.php';

$pdf = new Fpdi();
$pdf->AddPage();
$pdf->setSourceFile("vorlage/antrag_auf_zulassung_umschreibung.pdf");
$tplId = $pdf->importPage(1);
$pdf->useTemplate($tplId);
$pdf->SetFont('Helvetica', '', 12);
$height = $pdf->GetPageHeight();

if(isset($_POST['evb-nummer']) && !empty($_POST['evb-nummer'])) $pdf->Text(24, $height - 242, 'eVB-Nummer: ' . utf8_decode($_POST['evb-nummer']));
if(isset($_POST['auftragsart']))
{
    if(strcmp($_POST['auftragsart'], 'zulassung') === 0) $pdf->Text(29, $height - 227, 'X');
    if(strcmp($_POST['auftragsart'], 'umschreibung') === 0) $pdf->Text(60, $height - 227, 'X');
    if(strcmp($_POST['auftragsart'], 'abmeldung') === 0) $pdf->Text(101, $height - 227, 'X');
    if(strcmp($_POST['auftragsart'], 'aenderung') === 0) $pdf->Text(136, $height - 227, 'X');
    if(strcmp($_POST['auftragsart'], 'ersatz') === 0) $pdf->Text(167, $height - 227, 'X');
}
if(isset($_POST['kennzeichen']) && !empty($_POST['kennzeichen'])) $pdf->Text(147, $height - 242, utf8_decode($_POST['kennzeichen']));
if(isset($_POST['firma']) && !empty($_POST['firma'])) $pdf->Text(38, $height - 215, utf8_decode($_POST['firma']));
if(isset($_POST['name']) && !empty($_POST['name'])) $pdf->Text(38, $height - 207, utf8_decode($_POST['name']));
if(isset($_POST['vorname']) && !empty($_POST['vorname'])) $pdf->Text(126, $height - 207, utf8_decode($_POST['vorname']));
if(isset($_POST['gebname']) && !empty($_POST['gebname'])) $pdf->Text(53, $height - 199, utf8_decode($_POST['gebname']));
if(isset($_POST['gebdatum']) && !empty($_POST['gebdatum'])) $pdf->Text(53, $height - 190, utf8_decode($_POST['gebdatum']));
if(isset($_POST['gebort']) && !empty($_POST['gebort'])) $pdf->Text(126, $height - 190, utf8_decode($_POST['gebort']));
if(isset($_POST['strasse']) && !empty($_POST['strasse'])) $pdf->Text(40, $height - 182, utf8_decode($_POST['strasse']));
if(isset($_POST['hsnr']) && !empty($_POST['hsnr'])) $pdf->Text(164, $height - 182, utf8_decode($_POST['hsnr']));
if(isset($_POST['plz']) && !empty($_POST['plz'])) $pdf->Text(41, $height - 174, utf8_decode($_POST['plz']));
if(isset($_POST['ort']) && !empty($_POST['ort'])) $pdf->Text(74, $height - 174, utf8_decode($_POST['ort']));
if(isset($_POST['geschlecht']))
{
    if(strcmp($_POST['geschlecht'], 'weiblich') === 0) $pdf->Text(50, $height - 167, 'X');
    if(strcmp($_POST['geschlecht'], 'maennlich') === 0) $pdf->Text(77, $height - 167, 'X');
    if(strcmp($_POST['geschlecht'], 'firma') === 0) $pdf->Text(106, $height - 167, 'X');
    if(strcmp($_POST['geschlecht'], 'divers') === 0)
    {
        $pdf->Rect(127, $height - 170.5, 5, 5, 'D');
        $pdf->Text(128, $height - 167, 'X');
        $pdf->Text(134, $height - 166.5, 'divers');
    }
}
if(isset($_POST['geschlecht']) && strcmp($_POST['geschlecht'], 'firma') === 0)
{
    if(isset($_POST['beruf']) && !empty($_POST['beruf'])) $pdf->Text(75, $height - 158, utf8_decode($_POST['beruf']));
    if(isset($_POST['gewerbe-strasse']) && !empty($_POST['gewerbe-strasse'])) $pdf->Text(40, $height - 144, utf8_decode($_POST['gewerbe-strasse']));
    if(isset($_POST['gewerbe-hsnr']) && !empty($_POST['gewerbe-hsnr'])) $pdf->Text(164, $height - 144, utf8_decode($_POST['gewerbe-hsnr']));
    if(isset($_POST['gewerbe-plz']) && !empty($_POST['gewerbe-plz'])) $pdf->Text(41, $height - 136, utf8_decode($_POST['gewerbe-plz']));
    if(isset($_POST['gewerbe-ort']) && !empty($_POST['gewerbe-ort'])) $pdf->Text(74, $height - 136, utf8_decode($_POST['gewerbe-ort']));
}
if(isset($_POST['fahrzeug-id']) && !empty($_POST['fahrzeug-id'])) $pdf->Text(105, $height - 124, utf8_decode($_POST['fahrzeug-id']));
if(isset($_POST['auftragsart']))
{
    if(strcmp($_POST['auftragsart'], 'zulassung') === 0) $pdf->Text(28, $height - 67, 'X');
    if(strcmp($_POST['auftragsart'], 'umschreibung') === 0) $pdf->Text(54, $height - 67, 'X');
    if(strcmp($_POST['auftragsart'], 'abmeldung') === 0) $pdf->Text(88, $height - 67, 'X');
    if(strcmp($_POST['auftragsart'], 'aenderung') === 0) $pdf->Text(117, $height - 67, 'X');
    if(strcmp($_POST['auftragsart'], 'ersatz') === 0) $pdf->Text(143, $height - 67, 'X');
}

$pdf->Text(43, $height - 37, ', '.date('d.m.Y'));

if(isset($_POST['auftragsart']))
{
    if(strcmp($_POST['auftragsart'], 'ersatz') === 0)
	{
		require 'carreg_eid.php';
	}

    if(strcmp($_POST['auftragsart'], 'zulassung') === 0 || strcmp($_POST['auftragsart'], 'umschreibung') === 0)
    {
		require 'carreg_sepa.php';
    }
}

$pdf->Output('I', 'Antrag_und_Vollmacht.pdf');

