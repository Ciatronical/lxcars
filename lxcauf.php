<?php
/*********************>>> LxCars Auftrag <<<******************/
/* written by Ronny Kumke LxCars in April 2010 */
/*************************************************************/

include_once("../inc/db.php");
include_once("./inc/lxcLib.php");
include_once("../inc/template.inc");
include_once("../inc/UserLib.php");
ob_start();
//print_r($_GET);
$task = ($_GET["task"])?$_GET["task"]:$_POST["task"]; //echo "task==".$task." ***** "; 			//Aufgabe
$c_id = ($_GET["c_id"])?$_GET["c_id"]:$_POST["c_id"]; //echo "c_id==".$c_id." ***** ";			//CarId	
$owner = ($_GET["owner"])?$_GET["owner"]:$_POST["owner"];// echo "owner==".$owner." ***** ";	//Besitzer
$a_id = ($_GET["a_id"])?$_GET["a_id"]:$_POST["a_id"]; //echo "a_id==".$a_id." ***** ";			//AuftragsId
$b = ($_GET["b"])?$_GET["b"]:$_POST["b"]; if( !$b ) $b = 2 ;		//BackLink 1==Suche, 2==Fahrzeug
$cd = ShowCar( $c_id );//CarData
//print_r($_POST);
if( !$task ){ $task=1; }

switch( $task ){
	case 1:	
	$rs = HoleAuftraege( $c_id );
	?>
	<html>
	<head><title>Auftrag auswaehlen</title>
	<link REL="stylesheet" HREF="../css/main.css" TYPE="text/css" TITLE="Lx-System stylesheet">
	</head>
	<body>
   <script language="JavaScript">
	<!--
	function call_lxc_auf (owner,c_id,a_id) {
		Frame=eval("parent.main_window");
		uri1="lxcauf.php?owner=" + owner;
		uri2="&c_id=" + c_id;
		uri3="&task=3"
		uri4="&a_id=" + a_id;
		uri=uri1+uri2+uri3+uri4;
		location.href=uri;
	}
	//-->
	</script>
	<p class="listtop">Aufträge des Fahrzeugs <?echo $cd['c_ln'];?></p>

	<?php
	echo "<table class=\"liste\">\n";
	echo "<tr class='bgcol3'><th>Auftragstext</th><th class='bgcol3'>Datum</th><th class='bgcol3'>Status</th><th class='bgcol3'>Auftragsnummer</th><th></th></tr>\n";
	$i = 0;
	//print_r($rs);
	$status = array( 'Michael', 'angenommen', 'bearbeitet', 'abgerechnet' );//Zum Gedenken an Michael Gartenschläger
	//print_r($status);
	if( $rs ){
		foreach( $rs as $row ){
			echo 	"<tr onMouseover=\"this.bgColor='#0033FF';\" onMouseout=\"this.bgColor='".$bgcol[($i%2+1)]."';\" bgcolor='".$bgcol[($i%2+1)]."'onClick='call_lxc_auf(\"$owner\",\"$c_id\",".$row["lxc_a_id"].");'>".
					"<td class=\"liste\">".$row["lxc_a_pos_todo"]."</td><td class=\"liste\">".$row['to_char']."</td>".                                           
					"<td class=\"liste\">".$status[$row['lxc_a_status']]."</td><td class=\"liste\">".$row["lxc_a_id"]."</td></tr>\n";
			$i++;
		}
		echo "</table>\n";
	}
	else{ //Wenn kein Auftrag vorhanden ist wir erstma einer erzeugt	
			header("Location: lxcauf.php?owner=$owner&task=2&c_id=$c_id");//bei ev. Problemen ganzen Pfad angeben
	}
	?>	
	<form name="extra" action="lxcauf.php?task=2&owner=<?echo $owner;?>&c_id=<?echo $c_id;?>" method="post" >
	<input type="submit" name="neuer_auftrag" value="Neuer Auftrag">
	</form>
	<form name="back" action="lxcmain.php?task=3&owner=<?echo $owner;?>&c_id=<?echo $c_id;?>" method="post" >
	<input type="submit"  value="zurück">
	</form>
	</html>
	<?php	
	break;
	
	/*************************************************Task==2***********************************************************************************/
	case 2:  $a_id = NeuerAuftrag( $c_id );
				echo "<p class=\"listtop\">Neuer Auftrag wird erzeugt </p> ".$c_id;

	case 3: 
		if( $_POST[update] || $_POST[printa] ){
			$mem = 0;
			$mytimestamp = mktime();
			$mts = date("d.m.Y H:i:s",$mytimestamp);	
			/***************gepostete Auftragsdaten (ohne pOSOTIONEN)***************************************************************************/
			$a_data = array( $_POST['lxc_a_finish_time'], $_POST['lxc_a_km'], $_SESSION['employee'], $mts, $_POST['lxc_a_status'], $_POST['lxc_a_text']);
			UpdateAuftragsDaten( $a_id );
			$zaehler = 0;
			foreach( $_POST as $key => $value ){
				if( strrpos( $key, "___" ) ){//StingPosition()  (sind drei Underlines enthalten?
					$zaehler++;					
					$geteilt = explode( "___", $key );//Underlines abtrennen, explode teilt einen string, Rückgabe ist ein Array 
					$poscontent[$zaehler] = $_POST[$key];		
					if( $zaehler == 7 ){
						$zaehler = 0;
						//print_r($poscontent);
						UpdatePosition( $geteilt[1], $poscontent );
					}
				}
			}
	
/*Hier entscheidet sich ob eine neue Position erzeugt wird*/
/*Es müssen genau n+1 Positionen angezeigt werden*/
/* wass passiert wenn eine Position leer ist? wenn diese nicht am Ende steht??*/

		if( $_POST[printa]== "drucken" ){
			header("Location: lxcaufPrt.php?a_id=$a_id&pdf=0&owner=".$owner."&c_id=".$c_id);
		}
		if( $_POST[printa] == "Pdf"){
			header("Location: lxcaufPrt.php?a_id=$a_id&pdf=1");
		}
	}	
	
//$schrauber;
	$gruppen=getGruppen();
//print_r($gruppen);
foreach($gruppen as $key=>$value){
	//echo "Der aktuelle Wert ist: " . $key . "</br>";
	if($gruppen[$key]['grpname']=="Werkstatt") {$schrauber=getMitglieder($gruppen[$key]['grpid']);}
}
if( !$schrauber ){
	echo "<b>Gruppe Werstatt nicht angelegt oder ihr keine  Mitglieder zugewiesn. install.txt lesn!!</br>CRM->Admin->Gruppen</b>";
} 
//print_r( $schrauber );
array_unshift( $schrauber, array(id => 0, name => "Monteur") );
$ad = HoleAuftragsDaten( $a_id );
$stat = "lxc_a_status".$ad[0]['lxc_a_status'];
$tpl_array = array(a_id=>$a_id,c_id=>$c_id,ln=>$cd['c_ln'],ownerstring=>$cd['ownerstring'],$stat=>'selected', owner=>$owner, b=>$b);
//print_r($tpl_array);
if($ad) {
	$tpl_array+=$ad[0];
}
//print_r($ad);
$pos = HoleAuftragsPositionen( $a_id );
$i = 0;
foreach( $pos as $key => $posdata ){
	$i = $key;
}//max ermitteln geht sicher auch eleganter...
//echo "Maximum: ".$i."</br>";

if( $pos[$i]['lxc_a_pos_todo'] != "" ){ 
	NeuePosition($a_id);
	//echo "Neue Position erzeugt";
}

$pos = HoleAuftragsPositionen( $a_id );

$ta = new Template( $base );
$ta->set_file( array( "tpl-file" => "lxcauf.tpl" ) );
$ta->set_var( $tpl_array );
$ta->set_block( "tpl-file","pos_block","blockersatz" );

$abbrechen = false;
$last_pos_todo = "";
foreach( $pos as $key => $posdata ){//pos =: AuftragsPosition
	if( $abbrechen ){ break; }
	if( $posdata['lxc_a_pos_todo'] == "" ){
		//echo "Noch ein Feld";
		$abbrechen = true;
	} 
	$last_pos_todo = $posdata['lxc_a_pos_todo'];
	
	$schrauberAuswahlString = "";//array(lxc_schauber_auswahl=>'<option value="1"  > Schraubername');
	foreach ($schrauber as $key1=>$value){
   	if( $posdata['lxc_a_pos_emp'] == $key1 ){
   		$selectString = "selected";
   	}
   	else{
   		$selectString = "";
   	}
   	$schrauberAuswahlString = " "."<option value=\"$key1\" ".$selectString." > ".$schrauber[$key1]['name'].$schrauberAuswahlString;   
   }
	$schrauberAuswahlArray = Array( lxc_schauber_auswahl => $schrauberAuswahlString );
   $tst = array( posid => $posdata['lxc_a_pos_id'], lxc_a_pos_status.$posdata['lxc_a_pos_status'].$posdata['lxc_a_pos_id'] => "selected" );
  	$tst += $schrauberAuswahlArray;
   $tst += $posdata;
   $ta->set_var( $tst );
	$ta->parse( "blockersatz", "pos_block", true );
}
$ta->pparse("out",array("tpl-file"));
}
ob_end_flush();
?>