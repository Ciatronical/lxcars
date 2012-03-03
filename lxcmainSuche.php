<?php
/* ++++++++++++++++ LxC Suche +++++++++++++++++++
+++++ Sucht Fahrzeuge bzw. deren Besitzer +++++ 
+++++ begonnen Ende Juli 2010 Ronny Kumke +++++ */
/************************************************/
include_once("../inc/template.inc");
require_once("./inc/lxcLib.php");
require_once("../inc/stdLib.php");
include_once("../inc/UserLib.php");
include_once("../inc/FirmenLib.php");
//require("../crmajax/firmacommon".XajaxVer.".php");
require("libphp-snoopy/Snoopy.class.php");
$Q="C";
$bgcol[1]="#ddddff";
$bgcol[2]="#ddffdd";


//$formdata = array('Q'=>$Q);
$formdata = $_POST;

if( $pos = strpos( $formdata['c_ow'], " -> " ) ){
	$formdata['c_ow'] = substr( $formdata['c_ow'], 0, $pos );
}

if( $pos = strpos( $formdata['c_ln'], " -> " ) ){
	$formdata['c_ln'] = substr( $formdata['c_ln'], 0, $pos );
}
$was = $formdata;
//print_r($was);
$formdata['Q'] = "C";// kann weg??
$t = new Template($base);
if( !$was[reset] ){
	$t->set_var($formdata);
}
$t->set_file(array("tpl-file" => "lxcmainSuche.tpl"));
$t->set_block("tpl-file","Liste","Block");

if($_POST["suche"]) {
	 
	//print_r( $was );
	 
	$rs = sucheCars( $was ); 
	//print_r($_POST);
	
	if($_POST["filter"]){
		//echo "filter wurde gepostet";
		
		foreach($rs as $zeile){
			//zu2 zu3 ermitteln
			//print_r($zeile);
			$zu2 = $zeile[z2];
			$zu3 = $zeile[z3];
			//echo "zu2 ".$zu2."</b>";
			//echo "zu3 ".$zu3."</b>";
			// Snoopy erzeugen 
			$snoopy = new Snoopy;
			$snoopy->agent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)";
			$snoopy->referer = "http://www.jonasjohn.de/";

			$fetchstring = "http://www.mann-hummel.com/mf_prodkata_eur/index.html?ktlg_page=5&ktlg_lang=2&ktlg_05_szu2=".$zu2."&ktlg_05_szu3=".$zu3;
			//echo $fetchstring;
			$snoopy->fetch($fetchstring);
			//$sr = htmlentities($snoopy->results);//snoopyresult
			$sr = $snoopy->results;
			$filter[Nummer] = 1;
			//echo $snoopyresult;
			$davor = "<nobr><b>";
			$danach = "</b></nobr>";
			
					
			$wasL = 'title="Luftfilter"';
			$wasK = 'title="Kraftstoff"';  
			$wasI = 'class="finav" title="Innenraumluftfilter / Kabinenluftfilter';
			$wasO = 'title="Schmier';
		
			$filter += Lies($sr, $wasL, $davor, $danach);
			$filter += Lies($sr, $wasK, $davor, $danach);
			$filter += Lies($sr, $wasI	, $davor, $danach);
			$davor = 'class="finav"><b>';
			$danach = '</b></a>';
			$filter += Lies($sr, $wasO	, 'class="finav"><b>', '</b></a>');
			
			// Resultat auswerten und in einem Array speichern
		
		
		// Aus dem Array eine DAteierzeugen
		}//foreach
		ksort($filter);
		//print_r($filter);
		$text = "";
		
		foreach($filter as $nummer => $anzahl){
			//echo $nummer[0];
			if( ($nummer[0] == "H") || ($nummer[0] == "C") || ($nummer[0] == "W") || ($nummer[0] == "P")){
				$text = $text.$nummer.((strlen($nummer) < 8)?("\t"):("\t")).$anzahl.(($anzahl > 15)?("\t2"):("\t1"))."\n";	
			}
		}
		//echo $text;
		
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Datum aus Vergangenheit
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");    
		header("Content-type: application/octetstream");//octetstream
		header('Content-Disposition: attachment; filename="filter.csv"');
		header("Content-Disposition: filename=filter.csv");
		
		print $text;
	}
}
if ($_POST["felder"]) {
		$rc=doReport($_POST,$Q);
		$t->set_file(array("fa1" => "firmen1.tpl"));
		if ($rc) { 
			$tmp="<div style='width:300px'>[<a href='tmp/report_".$_SESSION["loginCRM"].".csv'>download Report</a>]</div>";
		} else {
			$tmp="Sorry, not found";
		}
		$t->set_var(array(report => $tmp));
}

$i = 0;
if ($rs && ($i < $listLimit)){//
	
	foreach ($rs as $zeile){
		$tst = getFirmenStamm($zeile[c_ow]);
		if(!$tst){
			echo "Kunde ".$zeile[c_ow]." zum Kennzeichen ".$zeile[c_ln]." existiert nicht!  Datenbank prüfen!";
		}
		else { 
			$zeile += $tst;
		}
		$t->set_var(array(Q => $Q,
                        ID => $zeile["id"],
                        LineCol => $bgcol[($i%2+1)],
                        KdNr => ($Q=="C")?$zeile["customernumber"]:$zeile["vendornumber"],
                        Name => $zeile["name"],
                        Plz => $zeile["zipcode"],
                        Ort => $zeile["city"],
                        Strasse => $zeile["street"],
                        Telefon => $zeile["phone"],
                        eMail => $zeile["email"],
                        KZ => $zeile["c_ln"],
                        CarID => $zeile["c_id"],
                        Herst => $zeile["c_m"],
                        CarTyp => $zeile["c_t"]));// end set_var
 		$t->parse("Block","Liste",true);
     	$i++;
     	
	}
}

if(!$_POST["filter"]){
	$t->pparse("out",array("tpl-file"),$_SESSION["lang"],"firma");
}
?>