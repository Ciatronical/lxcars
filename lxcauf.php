<?php
/*************************************************************/
/************************ LxCars Auftrag *********************/
/************** ronny@lxcars.de, April 2010 ******************/
/*************************************************************/
include_once("../inc/db.php");
include_once("inc/lxcLib.php");
include_once("../inc/template.inc");
include_once("../inc/UserLib.php");

ob_start();
$task = $_GET["task"] ? $_GET["task"] : $_POST["task"];
$c_id = $_GET["c_id"] ? $_GET["c_id"] : $_POST["c_id"]; 
$owner = $_GET["owner"] ? $_GET["owner"] : $_POST["owner"];
$a_id = $_GET["a_id"] ? $_GET["a_id"] : $_POST["a_id"]; 
$b = $_GET["b"] ? $_GET["b"] : $_POST["b"]; if( !$b ) $b = 2 ;//ZurückButton 1==Suche, 2==Fahrzeug
$cd = ShowCar( $c_id );//Fahrzeugdaten holen
if( !$task ) $task=1; 

switch( $task ){
    case 1://Alle Aufträge anzeigen
	$rs = HoleAuftraege( $c_id );
	$menu =  $_SESSION['menu'];
	if( $rs ){
	?>
	<html>
        <head><title>Auftrag auswaehlen</title>
        <?php echo $menu['stylesheets']; ?>
        <link type="text/css" REL="stylesheet" HREF="../css/<?php echo $_SESSION["stylesheet"]; ?>/main.css"></link>
        <?php echo $menu['javascripts']; ?>
        </head>
        <body>
        <?php echo $menu['pre_content']; ?>   
        <?php echo $menu['start_content']; ?>     
            <script language="JavaScript">
                <!--
                function call_lxc_auf( owner, c_id, a_id ){
                    uri1="lxcauf.php?owner=" + owner;
                    uri2="&c_id=" + c_id;
                    uri3="&task=3"
                    uri4="&a_id=" + a_id;
                    uri=uri1+uri2+uri3+uri4;
                    location.href=uri;
	           }
	           //-->
	       </script>
        <p class="listtop">Aufträge des Fahrzeugs: <?echo $cd['c_ln'];?></p>
        <?php
        echo "<table class=\"liste\">\n";
        echo "<tr class='bgcol3'><th>Auftragstext</th><th class='bgcol3'>Datum</th><th class='bgcol3'>Status</th><th class='bgcol3'>Auftragsnummer</th><th></th></tr>\n";
        $i = 0;
        $status = array( 'Michael', 'angenommen', 'bearbeitet', 'abgerechnet' );//Zum Gedenken an Michael Gartenschläger
	
		foreach( $rs as $row ){
            echo 	"<tr onMouseover=\"this.bgColor='#0033FF';\" onMouseout=\"this.bgColor='".$bgcol[($i%2+1)]."';\" bgcolor='".$bgcol[($i%2+1)]."'onClick='call_lxc_auf(\"$owner\",\"$c_id\",".$row["lxc_a_id"].");'>".
					"<td class=\"liste\">".$row["lxc_a_pos_todo"]."</td><td class=\"liste\">".$row['to_char']."</td>".                                           
					"<td class=\"liste\">".$status[$row['lxc_a_status']]."</td><td class=\"liste\">".$row["lxc_a_id"]."</td></tr>\n";
			$i++;
        }
        echo "</table>\n";
        ?>	
        <form name="extra" action="lxcauf.php?task=2&owner=<?echo $owner;?>&c_id=<?echo $c_id;?>" method="post" >
            <input type="submit" name="neuer_auftrag" value="Neuer Auftrag">
        </form>
	    <form name="back" action="lxcmain.php?task=3&owner=<?echo $owner;?>&c_id=<?echo $c_id;?>" method="post" >
            <input type="submit"  value="zurück">
        </form>
        <?php } echo $menu['end_content']; ?>      
	    </body>
	    </html>
	    <?php
        if( $i > 0 ) break 1; 

    case 2:  
        $a_id = NeuerAuftrag( $c_id );
        $msg = "Neuer";

	case 3: 
	    $_POST['lxc_a_km'] = $_POST['lxc_a_km'] == '' ? '0' : $_POST['lxc_a_km'];
        if( $_POST['lxc_a_km'] == '0' ) {
            $jqmsg = '<div id="dialog" title="Kilometerstand fehlt">
	                     <p>Bitte geben Sie den Stand des Wegstreckenzählers ein.</p>
	                  </div>';
	    }        
        $gruppen=getGruppen();
        foreach($gruppen as $key=>$value){
            if($gruppen[$key]['grpname']=="Werkstatt") {$schrauber=getMitglieder($gruppen[$key]['grpid']);}
        }   
        if( !$schrauber ){
	       echo "<b>Gruppe Werkstatt nicht angelegt oder ihr sind keine  Mitglieder zugewiesn. install.txt lesn!!</br>CRM->Admin->Gruppen</b>";
        }
        array_unshift( $schrauber, array(id => 0, name => "Monteur") );
        $schrauber = array_reverse( $schrauber );
		if( $_POST[update] || $_POST[printa] ){
			$mem = 0;
			$mytimestamp = mktime();
			$mts = date("d.m.Y H:i:s",$mytimestamp);	
			$a_data = array( $_POST['lxc_a_finish_time'], $_POST['lxc_a_km'], $_SESSION['employee'], $mts, $_POST['lxc_a_status'], $_POST['lxc_a_text']);
			UpdateAuftragsDaten( $a_id, $a_data  );
			$zaehler = 0;
			foreach( $_POST as $key => $value ){
				if( strrpos( $key, "___" ) ){//StingPosition()  (sind drei Underlines enthalten?
					$zaehler++;					
					$geteilt = explode( "___", $key );//Underlines abtrennen, explode teilt einen string, Rückgabe ist ein Array 
					$poscontent[$zaehler] = $_POST[$key];		
					if( $zaehler == 7 ){
						$zaehler = 0;
						
                        $poscontent['7'] = $schrauber[$poscontent['7']]['name'];
						UpdatePosition( $geteilt[1], $poscontent );
					}
				}
			}
        if( $_POST[printa]== "drucken" ){
			header("Location: lxcaufPrt.php?a_id=$a_id&pdf=0&owner=".$owner."&c_id=".$c_id);
		}
		if( $_POST[printa] == "Pdf"){
			header("Location: lxcaufPrt.php?a_id=$a_id&pdf=1");
		}
	}	
    $ad = HoleAuftragsDaten( $a_id );
    $stat = "lxc_a_status".$ad[0]['lxc_a_status'];
    $tpl_array = array( a_id => $a_id, c_id => $c_id, ln => $cd['c_ln'], ownerstring => $cd['ownerstring'], $stat => 'selected', owner => $owner, b => $b, ERPCSS => $_SESSION["stylesheet"], msg => $msg);
    if($ad) {
	   $tpl_array+=$ad[0];
    }
    if( $pos[$n]['lxc_a_pos_todo'] != "" ){ 
        NeuePosition($a_id);
    }
    $pos = HoleAuftragsPositionen( $a_id );
    $n = count($pos) - 1;
    if( $pos[$n]['lxc_a_pos_todo'] != "" ){ 
        NeuePosition($a_id);
    }  
    $pos = HoleAuftragsPositionen( $a_id );
    $ta = new Template( $base );
    $menu =  $_SESSION['menu']; 
    $ta->set_var( array(
        JAVASCRIPTS   => $menu['javascripts'],
        STYLESHEETS   => $menu['stylesheets'],
        PRE_CONTENT   => $menu['pre_content'],
        START_CONTENT => $menu['start_content'],
        END_CONTENT   => $menu['end_content'],
        BASEPATH      => $_SESSION['basepath'],
        JQMSG           => $jqmsg ) );
    $ta->set_file( array( "tpl-file" => "lxcauf.tpl" ) );
    $ta->set_var( $tpl_array );
    $ta->set_block( "tpl-file","pos_block","blockersatz" );

    $abbrechen = false;
    $last_pos_todo = "";
    foreach( $pos as $key => $posdata ){//pos =: AuftragsPosition
        if( $abbrechen ){ break; }
        if( $posdata['lxc_a_pos_todo'] == "" ){
            $abbrechen = true;
        } 
        $last_pos_todo = $posdata['lxc_a_pos_todo'];
        $schrauberAuswahlString = "";//array(lxc_schauber_auswahl=>'<option value="1"  > Schraubername');
        foreach ($schrauber as $key1=>$value){
            if( $posdata['lxc_a_pos_emp'] == $value['name'] ){
                $selectString = "selected";
            } 
   	        else{
   		       $selectString = "";
   	        }
   	        $schrauberAuswahlString = " "."<option value=\"$key1\" ".$selectString." > ".$schrauber[$key1]['name'].$schrauberAuswahlString;   
        }    
        $schrauberAuswahlArray = Array( lxc_schauber_auswahl => $schrauberAuswahlString );
        $ta_array = array( posid => $posdata['lxc_a_pos_id'], lxc_a_pos_status.$posdata['lxc_a_pos_status'].$posdata['lxc_a_pos_id'] => "selected" );
        $ta_array += $schrauberAuswahlArray;
        $ta_array += $posdata;
        $ta->set_var( $ta_array );
        $ta->parse( "blockersatz", "pos_block", true );
    }
$ta->pparse("out",array("tpl-file"));
}
ob_end_flush();
?>