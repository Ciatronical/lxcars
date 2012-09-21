<?php
/*****************************************************************************************************
*** Zum Anlegen und Verwalten von FahrzeugTypn die nicht in der KBA-Datenbank gespeichert sind.    ***
*** geschrieben im September 2012 von Ronny Kumke, ronny@lxcars.de unter Artistic License          ***
*****************************************************************************************************/
include( "../inc/template.inc" );
include( "./inc/lxcLib.php" );

$c_id = $_GET["c_id"] ? $_GET["c_id"] : $_POST["c_id"]; 
$owner = $_GET["owner"] ? $_GET["owner"] : $_POST["owner"];
$hsn = $_GET["hsn"] ? $_GET["hsn"] : $_POST["hsn"];
$tsn = substr( $_GET["tsn"] ? $_GET["tsn"] : $_POST["tsn"], 0, 3 );
$ERPCSS = $_SESSION["stylesheet"];
$emp = $_SESSION["employee"];
$id = $_POST['id'];

$TypDaten = compact( 'id','c_id', 'owner', 'hsn', 'tsn', 'ERPCSS', 'emp' );
$TypDaten = array_merge( $TypDaten, $_POST );
$TypDaten['msg'] = "Fahrzeugtyp bearbeiten";
$TypDatenNeu = compact( 'hsn', 'tsn', 'emp' );
 
if( $TypDaten['update'] ){
    UpdateFhzTyp( $TypDaten );
    $TypDaten['msg'] = "Fahrzeugtyp gespeichert";
}

if( !$TypDaten['id'] = GetFhzTyp( $hsn, $tsn  ) ){
    $Typn_id = NewFhzTyp( $TypDatenNeu ); 
    $TypDaten['msg'] = "Datensatz für Fahrzeugtyp erzeugt";
}

if( is_array( GetFhzTyp( $hsn, $tsn  ) ) ){ 
    $TypDaten = array_merge( $TypDaten, CleanArray( GetFhzTyp( $hsn, $tsn ) ) );
}

$t = new Template( $base );
$t->set_var( $TypDaten );	
$t->set_file(array("tpl-file" => "FhzTyp.tpl"));
$t->pparse("out",array("tpl-file"));



















?>