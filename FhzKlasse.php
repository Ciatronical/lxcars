<?php
/*****************************************************************************************************
*** Zum Anlegen und Verwalten von Fahrzeugklassen die nicht in der KBA-Datenbank gespeichert sind. ***
*****************************************************************************************************/
include( "../inc/template.inc" );
include( "./inc/lxcLib.php" );

$task = $_GET["task"] ? $_GET["task"] : $_POST["task"];
$c_id = $_GET["c_id"] ? $_GET["c_id"] : $_POST["c_id"]; 
$owner = $_GET["owner"] ? $_GET["owner"] : $_POST["owner"];
$NeueKlassenDaten = array( zu2 => $_GET['zu3'], zu3 => $_GET['zu3'] );









switch( $task ){

    case 1: //Fahrzeugklasse hinzufügen
        echo "Neue Fahrzeugklasse wird hinzugefügt";
        $klassen_id = NeueFhzKlasse( $NeueKlassenDaten );   


    case 2: //Fahzeuklasse bearbeiten
        $KlassenDaten = GetFhzKlasse( $klassen_id );
        
        
    case 3: // Update Klassendaten
        UpdateFhzKlasse( $klassen_id );


    break;

    






}
$KlassenDaten['ERPCSS'] = $_SESSION["stylesheet"];
$t = new Template( $base );
$t->set_var( $KlassenDaten );	
$t->set_file(array("tpl-file" => "FhzKlasse.tpl"));
$t->pparse("out",array("tpl-file"));



















?>