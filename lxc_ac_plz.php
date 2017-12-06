<?php
/********************************************************************************************
***           AutoComplete für die Postleitzahlen Kunde / Lieferanten neu                 ***
***           geschrieben Ende Januar 2010 von Ronny Kumke ronny@lxcars.de                ***
********************************************************************************************/

require_once( "inc/conf.php" );
require_once( "inc/db.php" );
require_once( "inc/stdLib.php" );


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
switch( $mode ){
	case 0:   //Plz vervollständigen und zurückgeben
		if( $acSrc == "localdb" || $acSrc == "localdb,geodb" ){
			 // PLZ aus Kunden- bzw Lieferantenadressen holen, meistverwendetes Bundesland und Land holen  
			$sql = "SELECT  to_number AS myzipcode, (SELECT country FROM (SELECT DISTINCT ON (country )country, count(country) FROM (SELECT zipcode, country FROM customer UNION  SELECT zipcode, country FROM vendor) AS u  WHERE zipcode LIKE '".$q."%' AND country != '' GROUP BY u.country ) AS o ORDER BY count DESC LIMIT 1) AS country, (SELECT bland FROM (SELECT DISTINCT ON (bland) bland, count(bland) FROM (SELECT zipcode, bland FROM customer UNION  SELECT zipcode, bland FROM vendor) AS u  WHERE zipcode LIKE '".$q."%' AND bland > 0 GROUP BY u.bland ) AS o ORDER BY count DESC LIMIT 1) AS bland  FROM (SELECT DISTINCT ON (to_number(u.zipcode, '99999'))to_number(u.zipcode, '99999'), count(to_number(u.zipcode, '99999')) FROM ((SELECT zipcode FROM vendor UNION ALL SELECT zipcode from customer)) as u where u.zipcode like '".$q."%' GROUP BY to_number(u.zipcode, '99999')  ORDER BY to_number(u.zipcode, '99999')) AS xyz ORDER BY count DESC";
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
			$sql = "SELECT city FROM (SELECT DISTINCT ON ( city )count(city),city, zipcode FROM (SELECT city, zipcode FROM customer UNION ALL SELECT city, zipcode FROM vendor) AS u WHERE zipcode LIKE '".$plz."%' AND city ILIKE '".$q."%'  GROUP BY u.city, u.zipcode ORDER BY  u.city)AS s ORDER BY count DESC";
			$rs = $db->getall($sql);
			foreach( $rs as $value ){
				echo $value['city']."|".$value['bland']."__".$value['country']."\n";
			}
		}
	break;
	case 2:
		if( $acSrc == "localdb" || $acSrc == "localdb,geodb" ){
		//Straße aus Kunden- bzw Lieferantenadressen holen, sortiert nach Häufigkeit ohne Hausnummern
			$sql = "SELECT count(substring( street from '^[^[:digit:]]*')), substring( street from '^[^[:digit:]]*')  FROM (SELECT street FROM customer union all SELECT street FROM vendor) AS s WHERE s.street ILIKE '".$q."%' GROUP BY substring( street from '^[^[:digit:]]*') ORDER BY count DESC";
			$rs = $db->getall($sql);
			foreach( $rs as $value ){
				echo $value['substring']."\n";
			}
		}
	break;
}// switch	

?>
