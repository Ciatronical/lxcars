<?php

$pdf->AddPage();
$pdf->setSourceFile("vorlage/SEPA-Basislastschrift.pdf");
$tplId = $pdf->importPage(1);
$pdf->useTemplate($tplId);
$height = $pdf->GetPageHeight();
$pdf->SetFont('Helvetica', '', 10);

$pdf->SetFillColor(255, 255, 255);
$pdf->SetLineWidth(0.2);

$mandatsname = '';
$mandatsstrasse = '';
$mandatsplz = '';
$mandatsort = '';
if(!isset($_POST['mandat-identisch']))
{
    if(isset($_POST['mandats-name']) && !empty($_POST['mandats-name'])) $mandatsname = utf8_decode($_POST['mandats-name']);
    if(isset($_POST['mandats-strasse']) && !empty($_POST['mandats-strasse'])) $mandatsstrasse = utf8_decode($_POST['mandats-strasse']);
    if(isset($_POST['mandats-plz']) && !empty($_POST['mandats-plz'])) $mandatsplz = utf8_decode($_POST['mandats-plz']);
    if(isset($_POST['mandats-ort']) && !empty($_POST['mandats-ort'])) $mandatsort = utf8_decode($_POST['mandats-ort']);
}
else
{
    if(isset($_POST['vorname']) && !empty($_POST['vorname'])) $mandatsname = utf8_decode($_POST['vorname']) . ' ';
    if(isset($_POST['name']) && !empty($_POST['name'])) $mandatsname .= utf8_decode($_POST['name']);
    if(isset($_POST['strasse']) && !empty($_POST['strasse'])) $mandatsstrasse = utf8_decode($_POST['strasse']) . ' ';
    if(isset($_POST['hsnr']) && !empty($_POST['hsnr'])) $mandatsstrasse .= utf8_decode($_POST['hsnr']);
    if(isset($_POST['plz']) && !empty($_POST['plz'])) $mandatsplz = utf8_decode($_POST['plz']);
    if(isset($_POST['ort']) && !empty($_POST['ort'])) $mandatsort = utf8_decode($_POST['ort']);
}

if(!empty($mandatsname))
{
    $pdf->Rect(36, $height - 194.5, 163, 5, 'F');
    $pdf->Text(34, $height - 191, $mandatsname);
}
if(!empty($mandatsstrasse))
{
    $pdf->Rect(36, $height - 184.4, 163, 5, 'F');
    $pdf->Text(34, $height - 181, $mandatsstrasse);
}
if(!empty($mandatsplz))
{
    $pdf->Rect(36, $height - 175, 42, 5, 'F');
    $pdf->Text(34, $height - 171, $mandatsplz);
}
if(!empty($mandatsort))
{
    $pdf->Rect(86, $height - 175, 113, 5, 'F');
    $pdf->Text(85, $height - 171, $mandatsort);
}
if(isset($_POST['mandats-land']) && !empty($_POST['mandats-land']))
{
    $pdf->Text(34, $height - 161, utf8_decode($_POST['mandats-land']));
}
if(isset($_POST['mandats-iban']) && !empty($_POST['mandats-iban']))
{
    $pdf->Rect(36, $height - 155, 163, 5.1, 'F');
    $pdf->Text(34, $height - 152, utf8_decode($_POST['mandats-iban']));
}
if(isset($_POST['mandats-bic']) && !empty($_POST['mandats-bic']))
{
    $pdf->Rect(36, $height - 135, 50, 5, 'F');
    $pdf->Text(34, $height - 131, utf8_decode($_POST['mandats-bic']));
}
if(isset($_POST['mandats-bank']) && !empty($_POST['mandats-bank']))
{
    $pdf->Text(94, $height - 131, utf8_decode($_POST['mandats-bank']));
}

$haltername = '';
if(isset($_POST['vorname']) && !empty($_POST['vorname'])) $haltername .= utf8_decode($_POST['vorname']) . ' ';
if(isset($_POST['name']) && !empty($_POST['name'])) $haltername .= utf8_decode($_POST['name']);
if(!isset($_POST['mandat-identisch']) && !empty($haltername))
{
    $pdf->Rect(36, $height - 105, 163, 5, 'F');
    $pdf->Text(34, $height - 101, $haltername);
}
else
{
    //$pdf->Text(34, $height - 47, 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
    $pdf->Rect(32, $height - 47, 171.5, 5, 'F');
}

