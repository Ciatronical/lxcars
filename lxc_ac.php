<?php
/********************************************************************************************
***           AutoComplete für die Postleitzahlen Kunde / Lieferanten neu                 ***
***           geschrieben Ende Januar 2010 von Ronny Kumke ronny@lxcars.de                ***
********************************************************************************************/
//ToDo: switch durch if ersetzen

require_once( "../inc/db.php" );
require_once( "../inc/stdLib.php" );


$acSrc = "localdb";
global $db;
$mode = 0;
$q = '';
if( isset( $_GET['q'] ) ){
    $q = $_GET['q'];
}
$plz = '';
if( isset( $_GET['plz'] ) ){
    $plz = $_GET['plz'];
    $mode = 1;
}
if( isset( $_GET['street'] ) ){
	$mode = 2;
}

if( $_GET['case']=='owner' ){
	$mode = 3;
}
if( $_GET['case']=='g_art' ){
	$mode = 4;
}
if( isset( $_GET['kz'] ) ){
	$mode = 5;
}
if( $_GET['case']=='fastsearch' ){
	$mode = 6;
}
switch( $mode ){
	case 0:   //Plz vervollständigen und zurückgeben
		if( $acSrc == "localdb" || $acSrc == "localdb,geodb" ){
			 // PLZ aus Kunden- bzw Lieferantenadressen holen, meistverwendetes Bundesland und Land holen  
			$sql = " SELECT  to_number AS myzipcode, (SELECT country FROM ( SELECT DISTINCT ON ( country )country, count( country ) FROM ( ( SELECT zipcode, country FROM customer UNION ALL SELECT zipcode, country FROM vendor ) UNION ALL SELECT shiptozipcode, shiptocountry FROM shipto ) AS u  WHERE zipcode LIKE '".$q."%' AND country != '' GROUP BY u.country ) AS o ORDER BY count DESC LIMIT 1 ) AS country, ( SELECT bland FROM ( SELECT DISTINCT ON ( bland ) bland, count( bland ) FROM ( ( SELECT zipcode, bland FROM customer UNION ALL SELECT zipcode, bland FROM vendor) UNION ALL SELECT shiptozipcode, shiptobland FROM shipto) AS u  WHERE zipcode LIKE '".$q."%' AND bland > 0 GROUP BY u.bland ) AS o ORDER BY count DESC LIMIT 1) AS bland  FROM (SELECT DISTINCT ON (to_number(u.zipcode, '99999'))to_number(u.zipcode, '99999'), count(to_number(u.zipcode, '99999')) FROM ((SELECT zipcode FROM vendor UNION ALL SELECT zipcode from customer)UNION ALL SELECT shiptozipcode FROM shipto) as u where u.zipcode like '".$q."%' GROUP BY to_number(u.zipcode, '99999')  ORDER BY to_number(u.zipcode, '99999')) AS xyz ORDER BY count DESC LIMIT 8";
			$rs = $db->getall($sql);
			foreach( $rs as $value ){
				echo $value['myzipcode']."|".$value['country']."__".$value['bland']."\n";
			}
		}
		if( $acSrc == "localdb,geodb" || $ac == "geodb" ){
			// Geodb derzeit noch zu langsam, wird jedoch geändert
		}
	break;
	case 1:  // Ort(e) für Plz vervollständigen und zurückgeben
		if( $acSrc == "localdb" || $acSrc == "localdb,geodb" ){
			$sql = "SELECT city FROM (SELECT DISTINCT ON ( city )count(city),city, zipcode FROM ((SELECT city, zipcode FROM customer UNION ALL SELECT city, zipcode FROM vendor)UNION ALL SELECT shiptocity, shiptozipcode FROM shipto) AS u WHERE zipcode LIKE '".$plz."%' AND city ILIKE '".$q."%'  GROUP BY u.city, u.zipcode ORDER BY  u.city)AS s ORDER BY count DESC";
			//echo $sql;			
			$rs = $db->getall($sql);
			foreach( $rs as $value ){
				echo $value['city']."\n";
			}
		}
	break;
	case 2:
		if( $acSrc == "localdb" || $acSrc == "localdb,geodb" ){
		//Straße aus Kunden- bzw Lieferantenadressen holen, sortiert nach Häufigkeit ohne Hausnummern
			$sql = "SELECT  count( trim( both ' ' FROM substring( street from '^[^[:digit:]]*') ) ), trim( both ' ' FROM substring( street from '^[^[:digit:]]*') ) || ' ' AS street FROM ((SELECT street FROM customer UNION ALL SELECT street FROM vendor) UNION ALL SELECT shiptostreet AS street FROM shipto ) AS s WHERE s.street ILIKE '".$q."%' GROUP BY trim(both ' ' FROM substring( street from '^[^[:digit:]]*')) ORDER BY count DESC, street LIMIT 8";
			$rs = $db->getall($sql);
			foreach( $rs as $value ){
				echo $value['street']."\n";
			}
		}
	break;
	case 3:
        //Owner
	    if (empty($_GET['term'])) exit;//ToDo  nach oben 
		include_once( "../inc/crmLib.php" );
		include_once( "../inc/FirmenLib.php" );
		$suchwort = mkSuchwort( "%".$_GET['term'] );
		$rsC = getAllFirmen( $suchwort, true,"C" );
		$rs = array();
		foreach( $rsC as $key => $value ){
		if (count($rs) > 11) break;;
			//echo $value['name'].' -> '.$value['city']."\n"
			array_push($rs,array('label'=>$value['name']." aus ".$value['city'],'value'=>$value['name']));
			echo json_encode($rs); 
		}
	break;
	case 4:
        //Getriebe
            if (empty($_GET['term'])) exit;//ToDo  nach oben 
			$sql = "SELECT c_gart, count(c_gart) FROM lxc_cars WHERE c_gart != '' AND c_gart ILIKE '".$_GET['term']."%' GROUP BY c_gart ORDER BY count DESC";			
			$rsG = $db->getall($sql);
			$rs = array();
			foreach( $rsG as $value ){
				//echo $value['c_gart']."\n";
				array_push($rs,array('value'=>$value['c_gart']));
				echo json_encode($rs); 
			}
	break;
	case 5:
			include("inc/lxcLib.php");
			$sql = "SELECT c_ln, name FROM lxc_cars JOIN customer ON c_ow = id WHERE c_ln ilike '%".$_GET['q']."%'";
			$rs = $db->getAll( $sql );
			//print_r( $rs );
			foreach( $rs as $value ){
				echo $value['c_ln'].' -> '.$value['name']."\n";
			}
	break;
    case 6:
        if (empty($_GET['term'])) exit;
        require_once("../inc/crmLib.php"); 
        require_once("../inc/FirmenLib.php"); 
        require_once("../inc/persLib.php"); 
        $suchwort = mkSuchwort($_GET['term']); 
        $rsC = getAllFirmen($suchwort,true,"C"); 
        $rsV = getAllFirmen($suchwort,true,"V"); 
        $rsK = getAllPerson($suchwort); 
        $sql = "SELECT c_ln, name FROM lxc_cars JOIN customer ON c_ow = id WHERE c_ln ilike '%".$_GET['term']."%'";
		$rsFhz = $db->getAll( $sql );
        $rs = array(); 
        foreach ( $rsC as $key => $value ) { 
            if (count($rs) > 11) break;
            array_push($rs,array('label'=>$value['name'],'category'=>'')); 
        } 
        foreach ( $rsV as $key => $value ) {
            if (count($rs) > 11) break; 
            array_push($rs,array('label'=>$value['name'],'category'=>'Lieferanten'));//ToDo translate 
        } 
        foreach ( $rsK as $key => $value ) {
            if (count($rs) > 11) break;  
            array_push($rs,array('label'=>$value['cp_givenname']." ".$value['cp_name'],'category'=>'Personen'));//ToDo translate 
        } 
        foreach ( $rsFhz as $key => $value ) {
            if (count($rs) > 11) break;  
            array_push($rs,array('label'=>$value['c_ln']." -> ".$value['name'],'value'=>$value['c_ln'],'category'=>'Fahrzeuge'));//ToDo translate 
        } 
        echo json_encode($rs); 
	break;	
	
}// switch	zz


?>
