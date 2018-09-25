<?php
/* CSV Erstellen aus Kundendaten*/

include_once("../inc/template.inc");
require_once("./inc/lxcLib.php");
require_once("../inc/stdLib.php");
include_once("../inc/UserLib.php");
include_once("../inc/FirmenLib.php");

unlink('./mytmp/daten.csv');
mkdir ("./mytmp", 0744); //Directory seems to get lost during git updates, this line prevents 404 errors!
$fp = fopen('./mytmp/daten.csv', 'c');
// Kopfzeile der CSV Datei
$kopf_zeile = array('primaer_HU', 'Anrede', 'Name', 'Straße', 'PLZ/Ort', 'Kennzeichen');
fputcsv($fp, $kopf_zeile);
$i=1;
//Jede Zeile des CSV ist ein Kunde
foreach($_POST["daten"] as $wert) {
	$csv_zeile = array();
	$id_kz = explode("__", $wert);
	$kunde = getFirmenStamm($id_kz[0]);
	array_push($csv_zeile, $i, $kunde['greeting'], $kunde['shiptoname'], $kunde['shiptostreet'], $kunde['shiptozipcode']." ".$kunde['shiptocity'], $id_kz[1]);
	fputcsv($fp, $csv_zeile);
	$i++;	
}
fclose($fp);

?>