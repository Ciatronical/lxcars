<?php

include_once( "../inc/stdLib.php" );
include_once( "../inc/template.inc" );


$ebay_feld = array();
$kfz_feld = array();
$letztespalte;
$descriptionpos;

$t = new Template( $base );
doHeader($t); 



/*****************************************
*
*  Noch keine Uploaddatei übergeben
*  Uploadmske bereistellen
*
*
******************************************/
if(!$_POST['select_file']){
  $t->set_var( array( 'BASEPATH' => $_SESSION['basepath'], 'TEST' => 

  '<table width="600">
  <form action="ebayseller.php" method="post" enctype="multipart/form-data">
  <tr>
  <td width="20%">Fahrzeugverwendungsliste</td>
  <td width="80%"><input type="file" name="file" id="file" /></td>
  </tr>
   <tr>
  <td width="20%">Ebay CSV</td>
  <td width="80%"><input type="file" name="file2" id="file2" /></td>
  </tr>
  <tr>
  <td>Submit</td>
  <td><input type="submit" name="select_file" /></td>
  </tr>
  <tr>
  <td></br></td>
  <td></br></td>
  </tr>
  <tr>
  <td>Neu Version:</td>
  <td><a href="ebaysellerJquery.php">KLICK</a></td>
  </tr>
  </form>
  </table>' ) );
}


/*****************************************
*
*  Uploaddatei nach tmp kopieren und auswerten
*
*  Werte in ein Array schreiben und Formatierungen vornehmen
*  (Formatierungen der Fahrzeugverwendungsliste in Ebay)
*  Make= Model= Platform= Type= Engine= Production Periode=  und |-Trenner
*
* - Bezeichnung auftrennen zB. A3 (8LI)  zwei Felder draus machen..  A3 und 8LI
* - Hubraum kW PS in ein Feld schreiben, getrennt durch Kommas
*
* - Ebay CSV in ein Array überführen
*
*
******************************************/

else{
	//$t->set_var( array( 'BASEPATH' => $_SESSION['basepath'], 'START_CONTENT' => 'Datei eingelesen' ) );
    move_uploaded_file($_FILES['file']['tmp_name'], "tmp/import.csv");    //Coparts CSV
    move_uploaded_file($_FILES['file2']['tmp_name'], "tmp/import2.csv");  //Ebay CSV
    //Daten aus der Coparts CSV auswerten        
	$datei = fopen("tmp/import.csv", "r"); 
    $i = 0;
    while($daten = fgetcsv($datei, 1000, ';')) { 
        $j = 0;
        $baujahr = "";
        $motor = "";
        if( $i != 0 ){ // Erste Zeile nicht auswerten 
        foreach( $daten as $spaltenwert ){    		 	  
            switch ($j) {
                case 0: //Spa 
                    $kfz_feld[$i][1] = "Compatibility";
                    $kfz_feld[$i][2] = "Make=".$spaltenwert."|";
       		        $j++;
        	    break;
        		case 1: 
        		    $pos1 = strpos($spaltenwert, '(');
                    $pos2 = strpos($spaltenwert, ')');          					
                    $kfz_feld[$i][2] .= "Model=".substr($spaltenwert, 0, $pos1-1)."|Platform=".substr($spaltenwert, $pos1+1, $pos2-$pos1-1)."|";
       		        $j++;
        		break;
        		case 2:         					
                    $kfz_feld[$i][2] .= "Type=".$spaltenwert."|";
                    $j++;
        		break;
        		case 3:         					
                    $baujahr = "Production Periode=".$spaltenwert;
                    $j++;
        		break;
                case 4:         					
                    $motor = $spaltenwert." KW";
                    $j++;
        		break;
        		case 5:         					
                    $motor .= ", ".$spaltenwert." PS";
                    $j++;
        		break;
                case 6:         					
                    $kfz_feld[$i][2] .= "Engine=".$spaltenwert." ccm, ".$motor."|".$baujahr;
                    $j++;
        		break;
    		}

        }   
        }
	    $i++;  
   }
  /* echo "<pre>";
   print_r($kfz_feld);
   echo "</pre>";*/
   fclose($datei); 

   //Daten aus der Ebay CSV auswerten        
   $datei = fopen("tmp/import2.csv", "r"); 
   $i = 0;
   while($daten = fgetcsv($datei, 6000, ';')) { 
        $j = 0;
        foreach( $daten as $spaltenwert ){
            // Die Position des letzten Spalteninhalt zwischenspeichern 
            if($spaltenwert == "RelationshipDetails"){
                $letztespalte = $j;      
            } 
            $ebay_feld[$i][$j] = $spaltenwert;
            $j++;        
        }
        $i++;
    } 	
    /*echo "<pre>";
    print_r($ebay_feld);
    echo "</pre>";*/

   // CSV-Datei schreiben
   $datei = fopen("tmp/ausgabe.csv", "w");
   $kfz_feldgro = count($kfz_feld);
   $ebay_feldgro = count($ebay_feld);
   $feldadd = $kfz_feldgro + $ebay_feldgro;
   
  
  //Coparts Array und Ebay Array vereinen! 
   for( $i = 0 ; $i < $feldadd ; $i++){
        $zeile = "";
        //Zeile 0-1 sind Werte aus Ebay-Feldern
        if($i <= 1){
            //Schleife durch die Ebay Arrays
            for( $j = 0 ; $j <= $letztespalte ; $j++){
                 //Erst wenn die letzte Spalte erreicht ist, kein  ; mehr anhängen
                 if($j != $letztespalte){
                      $zeile.=$ebay_feld[$i][$j].";";
                 }
                 else{
                      $zeile.=$ebay_feld[$i][$j].=chr(13).chr(10);
                 }
            }
            fputs( $datei , $zeile, strlen($zeile)); 
        }
        // Alle folgenden Zeilen stammen aus den Coparts Array
        else{
            //nur in der vorletzen und letzen Spalte wird etwas hinzugefügt
            for( $j = 0 ; $j <= $letztespalte ; $j++){
                 if($j < ($letztespalte-1)){
                      $zeile.= ";";
                      //vorletzte und letzte Spalte
                 }elseif($j == ($letztespalte-1)){
                       $zeile.= $kfz_feld[$i-1][1].";".$kfz_feld[$i-1][2];
                       $zeile.=chr(13).chr(10);
                 }   
             }
             fputs( $datei , $zeile, strlen($zeile));
        }
   }
  
   
   // Ebay und Coparts einzeln zum testen!
   /*
   for( $i = 0 ; $i < $ebay_feldgro ; $i++){
        $zeile = "";
        for( $j = 0 ; $j <= $letztespalte ; $j++){
            if($j != $letztespalte){
                $zeile.=$ebay_feld[$i][$j].";";
            }
            else{
                $zeile.=$ebay_feld[$i][$j].=chr(10).chr(13);
            }
        }
        fputs( $datei , $zeile, strlen($zeile)); 
   }
  
   for( $i = 1 ; $i <= $kfz_feldgro ; $i++){
        $zeile = $kfz_feld[$i][1].";".$kfz_feld[$i][2]."\n";
        fputs( $datei , $zeile, strlen($zeile));                                                    
   }*/

   fclose($datei);  

   $t->set_var( array( 'BASEPATH' => $_SESSION['basepath'], 'TEST' => 
                'Neue CSV herunterladen:</br><a href="http://melissa/kivi/crm/lxcars/tmp/ausgabe.csv">KLICK</a>' ) );

}//endelse



   /*echo "<pre>";
    print_r($daten);
    echo "</pre>";*/
    

$t->set_var($miscarray);						
$t->set_file(array("tpl-file" => "ebayseller.tpl"));
$t->pparse("out",array("tpl-file"));

?>