<?php
/***********************************************************************************************
***                        +++ LxCars - Werkstattsoftware +++                                ***
***             geschrieben von Ronny Kumke ronny@lxcars.de Artistic License 2               ***
***********************************************************************************************/             
ob_start();
//require('Smarty.class.php');
//$smarty = new Smarty;
include_once( "../inc/stdLib.php" );
include_once( "../inc/db.php" );
include( "../inc/template.inc" );
include( "./inc/lxcLib.php" );
include( "../inc/conf.php" );
include_once( "../inc/UserLib.php" );
if( XajaxVer == "05" ) require_once( "../crmajax/xajax_core/xajax.inc.php" );
else require_once( "../crmajax/xajax/xajax.inc.php" );

$owner = $_GET["owner"] ? $_GET["owner"] : $_POST["owner"]; 
$task = $_GET["task"] ? $_GET["task"] : $_POST["task"]; 
$c_id = $_GET["c_id"] ? $_GET["c_id"]:$_POST["c_id"]; 
$e_id = $_SESSION["employee"]; 	

$xajax = new xajax();
//$xajax->configure('debug',true);
if( XajaxVer == "05" ){
	$xajax->register( XAJAX_FUNCTION,"SucheFin" );
	$xajax->register( XAJAX_FUNCTION,"SucheMkb" );
	$xajax->register( XAJAX_FUNCTION,"UniqueKz" );
	$xajax->register( XAJAX_FUNCTION,"UniqueFin" );
}
else{
	$xajax->registerFunction( "SucheFin" );
	$xajax->registerFunction( "SucheMkb" );
	$xajax->registerFunction( "UniqueKz" );
	$xajax->registerFunction( "UniqueFin" );
}

$xajax->processRequest();

$t = new Template( $base );
$menu =  $_SESSION['menu']; 
$t->set_var( array(
    JAVASCRIPTS   => $menu['javascripts'],
    STYLESHEETS   => $menu['stylesheets'],
    PRE_CONTENT   => $menu['pre_content'],
    START_CONTENT => $menu['start_content'],
    END_CONTENT   => $menu['end_content'],
    BASEPATH      => $_SESSION['basepath'] ) );

$chk_c_ln = $_POST[chk_c_ln] ? "true" : "false" ;
$chk_c_2 = $_POST[chk_c_2] ? "true" : "false" ;
$chk_c_3 = $_POST[chk_c_3] ? "true" : "false" ;
$chk_c_em = $_POST[chk_c_em] ? "true" : "false" ;
$chk_c_hu = $_POST[chk_c_hu] ? "true" : "false" ;
$chk_fin = $_POST[chk_fin] ? "true" : "false" ;
$c_d = $_POST[c_d] == "" ? "1900-01-01" : date2db( $_POST[c_d] );
$c_hu = $_POST[c_hu] == "" ? "1900-01-01" : date2db( $_POST[c_hu] );
$fincn = $_POST[fin].$_POST[cn];
$mytimestamp = mktime();
$c_mkb = $_POST[mkbdrop] == "0" ? $_POST[mkb] : $_POST[mkbdrop];
$cardata = array( "owner" => $owner, "c_ln" => $_POST[c_ln], "c_2" => $_POST[c_2], "c_3" => $_POST[c_3], "c_em" => $_POST[c_em], "c_d" => $c_d , "c_hu" => $c_hu , "c_fin" => $fincn, "c_st" => $_POST[c_st], "c_wt" => $_POST[c_wt], "c_st_l" => $_POST[c_st_l], "c_wt_l" => $_POST[c_wt_l], "c_st_z" => $_POST[c_st_z], "c_wt_z" => $_POST[c_wt_z], "c_color" => $_POST[c_color], "c_gart" => $_POST[c_gart], "c_text" => $_POST[c_text], "c_mt" => $mytimestamp, "c_e_id" => $e_id, "chk_c_ln" => $chk_c_ln, "chk_c_2" => $chk_c_2, "chk_c_3" => $chk_c_3, "chk_c_em" => $chk_c_em, "chk_c_hu" => $chk_c_hu, "chk_fin" => $chk_fin );
$cardata_anlegen = array( "c_ow" => $owner, "c_ln" => $_POST[c_ln], "c_2" => $_POST[c_2], "c_3" => $_POST[c_3], "c_em" => $_POST[c_em], "c_mkb"=> $c_mkb, "c_d" => $c_d , "c_hu" => $c_hu , "c_fin" => $fincn,  "c_st" => $_POST[c_st], "c_wt" => $_POST[c_wt], "c_st_l" => $_POST[c_st_l], "c_wt_l" => $_POST[c_wt_l], "c_st_z" => $_POST[c_st_z], "c_wt_z" => $_POST[c_wt_z], "c_color" => $_POST[c_color], "c_gart" => $_POST[c_gart], "c_text" => $_POST[c_text], "c_mt" => $mytimestamp, "c_e_id" => $e_id, "chk_c_ln" => $chk_c_ln, "chk_c_2" => $chk_c_2, "chk_c_3" => $chk_c_3, "chk_c_em" => $chk_c_em, "chk_c_hu" => $chk_c_hu, "chk_fin" => $chk_fin);

//prüfen ob der User zur Gruppe Admin gehört
$gruppen = getGruppen();
foreach($gruppen as $key=>$value){
	if( $gruppen[$key]['grpname'] == "Admin" ){
		$admin = getMitglieder( $gruppen[$key]['grpid'] );
	}
}
if( !$admin ){
	echo "<b>Gruppe Admin nicht angelegt oder ihr keine  Mitglieder zugewiesn. install.txt lesn!!</br>CRM->Admin->Gruppen</b>";
}
$visibility = 'style="visibility:hidden"';
$readonly = 'readonly="readonly"';
foreach( $admin as $value ){
	if( $_SESSION["login"] == $value["login"] ){
		$visibility = 'style="visibility:visible"';
		$readonly = "";
	}
}

switch( $task ){
	case 1:	
		GetCars( $owner, $tbname );
	break;      	//Autos des Owners darstellen
	case 2:	
		if( $_POST[anlegen] ){
			if( $_POST[g_art_drop] != -1 ){
				$cardata_anlegen[c_gart] = $_POST[g_art_drop];
			}
			$c_id = NeuesAuto( $cardata_anlegen );
			header( "Location: lxcmain.php?owner=$owner&task=3&c_id=$c_id" );
		}
		else{     //Dateneingabe
			$g_art_drop = '<select tabindex="13" name="g_art_drop"><option value="-1" selected>Getriebeart';	
			$sql = "SELECT c_gart, count(c_gart) FROM lxc_cars WHERE c_gart != '' GROUP BY c_gart ORDER BY count DESC";
			$rs_g_art = $db->getall($sql);
			foreach( $rs_g_art as $value ){
				$g_art_drop.='<option value="'.$value['c_gart'].'" > '.$value['c_gart'];
			}
			$g_art_drop.='</select>';
			$msg = "<p class=\"listtop\">Fahrzeug anlegen durch: ".$e_id."</p>";
			$t->set_var( array( owner => $owner, c_text =>"Kommentar eingeben", xajax_out => $xajax->printJavascript("../".XajaxPath), g_art_drop=>$g_art_drop, ERPCSS=>$_SESSION["stylesheet"], MSG=>$msg ) );	//$formdata['ERPCSS'] = $_SESSION["stylesheet"];
			
			$t->set_file(array("tpl-file" => "lxcmain$task.tpl"));
			$t->pparse("out",array("tpl-file"));
		}
	break;
	case 3 :	
		if( $_POST[update] ){
			$cardata[mkb] = $_POST[mkb];
			$cardata[mkbwahl] = $_POST[mkbwahl];
			$cardata[typnummer] = $_POST[c_t];
			$cardata[chown] = $_POST[chown];
			if( $pos = strpos( $cardata[chown], " -> " ) ){
				$cardata[chown] = substr( $cardata[chown], 0, $pos );
			}
			if( $_POST[g_art_drop] != -1 ){
				$cardata[c_gart] = $_POST[g_art_drop];
			}
			UpdateCar( $c_id, $cardata );
		}
		if( $_GET["c_t"] ){
					UpdateTypNr( $c_id, $_GET[c_t] );
		}
		$miscarray = ShowCar( $c_id );
		$miscarray["visibility"] = $visibility;
		$miscarray["readonly"] = $readonly;
		$miscarray['FhzTypVis'] = 'hidden';
		if( $readonly != "" ){
			$miscarray["mkbdrop"] = "";
		}
		if($miscarray['kba']){
		    $miscarray['msg'] = "Fahrzeugdaten ".$miscarray['c_ln'];
		}
		else{
		    $miscarray['msg'] = "FahrzeugTyp existiert nicht in der KBA-Datenbank! Kann jedoch angelegt werden.";
		    $miscarray['FhzTypVis'] = 'visible';
		}
		$miscarray['ERPCSS'] = $_SESSION["stylesheet"];
        $$miscarray['xajax_out'] = $xajax->printJavascript("../".XajaxPath); //Doppelt $$ RICHTIG??
		$t->set_var($miscarray);						
		$t->set_file(array("tpl-file" => "lxcmain$task.tpl"));
		$t->pparse("out",array("tpl-file"));
	break;
}
ob_end_flush();
?>
