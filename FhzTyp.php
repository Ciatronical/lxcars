<?php
/*****************************************************************************************************
*** Zum Anlegen und Verwalten von FahrzeugTypn die nicht in der KBA-Datenbank gespeichert sind. ***
*****************************************************************************************************/
include( "../inc/template.inc" );
include( "./inc/lxcLib.php" );

$task = $_GET["task"] ? $_GET["task"] : $_POST["task"];
$c_id = $_GET["c_id"] ? $_GET["c_id"] : $_POST["c_id"]; 
$owner = $_GET["owner"] ? $_GET["owner"] : $_POST["owner"];
$hsn = $_GET["hsn"] ? $_GET["hsn"] : $_POST["hsn"];
$tsn = substr( $_GET["tsn"] ? $_GET["tsn"] : $_POST["tsn"], 0, 3 );
$ERPCSS = $_SESSION["stylesheet"];
$emp = $_SESSION["employee"];

//print_r( $_POST );


$TypDaten = compact( 'c_id', 'owner', 'hsn', 'tsn', 'ERPCSS', 'emp' );
$TypDaten = array_merge( $TypDaten, $_POST );
print_r( $TypDaten );
$tmp = GetFhzTyp( $hsn, $tsn  );
echo "TSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSS";
print_r( $tmp );
$TypDaten = array_merge( $TypDaten, GetFhzTyp( $hsn, $tsn  ) );


print_r( $TypDaten );

if( !$TypDaten['hersteller'] ){
    $Typn_id = NeuerFhzTyp( $hsn, $tsn ); 
}
if( $_POST['update'] ){
    UpdateFhzTyp( $TypDaten );
}



    





//$TypDaten = array( ERPCSS => $_SESSION["stylesheet"], owner => $owner, c_id => $c_id  );


//$TypDaten = array_merge(  $TypDaten, array( ERPCSS => $_SESSION["stylesheet"], owner => $owner, c_id => $c_id  ) );
//$TypDaten = array( ERPCSS => $_SESSION["stylesheet"], owner => $owner, c_id => $c_id  ) ;
print_r( $TypDaten );
$t = new Template( $base );
$t->set_var( $TypDaten );	
$t->set_file(array("tpl-file" => "FhzTyp.tpl"));
$t->pparse("out",array("tpl-file"));



















?>