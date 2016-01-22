<?php
/***********************************************************************************************
***                        +++ LxCars - Werkstattsoftware +++                                ***
***             geschrieben von Ronny Kumke ronny@lxcars.de Artistic License 2               ***
***********************************************************************************************/
ob_start();

include_once( "../inc/stdLib.php" );
include_once( "../inc/crmLib.php" );
include_once( "../inc/template.inc" );
include_once( "./inc/lxcLib.php" );
include_once( "../inc/conf.php" );
include_once( "../inc/UserLib.php" );
include_once( "../inc/FirmenLib.php" );

$owner = isset( $_GET["owner"] )? $_GET["owner"] : $_POST["owner"];
$task  = isset( $_GET["task"] ) ? $_GET["task"] : $_POST["task"];
$c_id  = isset( $_GET["c_id"] ) ? $_GET["c_id"]: "";
$e_name  = $_SESSION["name"];



$t = new Template( $base );
doHeader($t);
$t->set_var( array( 'BASEPATH' => $_SESSION['baseurl'] ) );

$chk_c_ln = isset($_POST['chk_c_ln']) ? "true" : "false" ;
$chk_c_2  = isset($_POST['chk_c_2'])  ? "true" : "false" ;
$chk_c_3  = isset($_POST['chk_c_3'])  ? "true" : "false" ;
$chk_c_em = isset($_POST['chk_c_em']) ? "true" : "false" ;
$chk_c_hu = isset($_POST['chk_c_hu']) ? "true" : "false" ;
$chk_fin  = isset($_POST['chk_fin']) ? "true" : "false" ;
$c_d       = ( !isset($_POST['c_d']) ||$_POST['c_d'] == "" )? "1900-01-01" : date2db( $_POST['c_d'] );
$c_hu      = ( !isset($_POST['c_hu'])||$_POST['c_hu']== "" )? "1900-01-01" : date2db( $_POST['c_hu'] );
$c_zrk       = $_POST['c_zrk'];
if(!isset($_POST['c_zrk'])||$_POST['c_zrk']== "" ) {
    $c_zrk = 0;
}
$fincn     = isset($_POST['fin'])?$_POST['fin']:'';
$fincn    .= isset($_POST['cn'])?$_POST['cn']:'';
$mytimestamp = mktime();
if(varExist($owner)) {
    $owner_data = getFirmenStamm( $owner );
}
//print_r($owner_data);

$mkb = ( isset($_POST['mkb'] ) ) ? $_POST['mkb'] : '';
$c_mkb = ( !isset($_POST['mkbdrop'] ) || $_POST['mkbdrop'] == "0" )? $mkb : $_POST['mkbdrop'];

$cardata = array(  "owner" => $owner,
                    "c_ln"      => (isset($_POST['c_ln'])) ? $_POST['c_ln']:'',
                    "c_ln"      => (isset($_POST['c_ln'])) ? $_POST['c_ln']:'',
                    "c_2"       => (isset($_POST['c_2'])) ? $_POST['c_2']:'',
                    "c_3"       => (isset($_POST['c_3'])) ? $_POST['c_3']:'',
                    "c_em"      => (isset($_POST['c_em'])) ? $_POST['c_em']:'',
                    "c_d"       => (isset($_POST['c_d'])) ? $_POST['c_d']:'',
                    "c_hu"      => $c_hu ,
                    "c_fin"     => $fincn,
                    "c_st"      => (isset($_POST['c_st'])) ? $_POST['c_st']:'',
                    "c_wt"      => (isset($_POST['c_wt'])) ? $_POST['c_wt']:'',
                    "c_st_l"    => (isset($_POST['c_st_l'])) ? $_POST['c_st_l']:'',
                    "c_wt_l"    => (isset($_POST['c_wt_l'])) ? $_POST['c_wt_l']:'',
                    "c_st_z"    => (isset($_POST['c_st_z'])) ? $_POST['c_st_z']:'',
                    "c_wt_z"    => (isset($_POST['c_wt_z'])) ? $_POST['c_wt_z']:'',
                    "c_color"   => (isset($_POST['c_color'])) ? $_POST['c_color']:'',
                    "c_gart"    => (isset($_POST['c_gart'])) ? $_POST['c_gart']:'',
                    "c_text"    => (isset($_POST['c_text'])) ? $_POST['c_text']:'',
                    "c_flx"     => (isset($_POST['c_flx'])) ? $_POST['c_flx']:'',
                    "c_zrd"     => (isset($_POST['c_zrd'])) ? $_POST['c_zrd']:'',
                    "c_zrk"     => $c_zrk,
                    "c_bf"      => (isset($_POST['c_bf'])) ? $_POST['c_bf']:'',
                    "c_wd"      => (isset($_POST['c_wd'])) ? $_POST['c_wd']:'',
                    "c_hubr"    => $_POST['c_hubr'],
                    "c_leist"   => $_POST['c_leist'],
                    "c_bauj"    => $_POST['c_bauj'],
                    "c_mt"      => $mytimestamp, "c_e_id" => $e_name,
                    "chk_c_ln"  => $chk_c_ln, "chk_c_2" => $chk_c_2,
                    "chk_c_3"   => $chk_c_3, "chk_c_em" => $chk_c_em,
                    "chk_c_hu"  => $chk_c_hu, "chk_fin" => $chk_fin,
                    );

$cardata_anlegen = array( "c_ow"     => $owner,
                            "c_ln"     => (isset($_POST['c_ln'])) ? $_POST['c_ln']:'',
                            "c_2"      => (isset($_POST['c_2'])) ? $_POST['c_2']:'',
                            "c_3"      => (isset($_POST['c_3'])) ? $_POST['c_3']:'',
                            "c_em"     => (isset($_POST['c_em'])) ? $_POST['c_em']:'',
                            "c_mkb"    => $c_mkb,
                            "c_d"      => $c_d,
                            "c_hu"     => $c_hu ,
                            "c_fin"    => $fincn,
                            "c_st"     => (isset($_POST['c_st'])) ? $_POST['c_st']:'',
                            "c_wt"     => (isset($_POST['c_wt'])) ? $_POST['c_wt']:'',
                            "c_st_l"   => (isset($_POST['c_st_l'])) ? $_POST['c_st_l']:'',
                            "c_wt_l"   => (isset($_POST['c_wt_l'])) ? $_POST['c_wt_l']:'',
                            "c_st_z"   => (isset($_POST['c_st_z'])) ? $_POST['c_st_z']:'',
                            "c_wt_z"   => (isset($_POST['c_wt_z'])) ? $_POST['c_wt_z']:'',
                            "c_color"  => (isset($_POST['c_color'])) ? $_POST['c_color']:'',
                            "c_gart"   => (isset($_POST['c_gart'])) ? $_POST['c_gart']:'',
                            "c_text"   => (isset($_POST['c_text'])) ? $_POST['c_text']:'',
                            "c_mt"     => $mytimestamp,
                            "c_e_id"   => $e_name,
                            "chk_c_ln" => $chk_c_ln,
                            "chk_c_2"  => $chk_c_2,
                            "chk_c_3"  => $chk_c_3,
                            "chk_c_em" => $chk_c_em,
                            "chk_c_hu" => $chk_c_hu,

                            "chk_fin"  => $chk_fin);

//Benutzer in Gruppe Spezial ?
$tempass = ERPUsersfromGroup("Spezial");
$special = false;
foreach ($tempass as $value) {
    if( $value['login'] == $_SESSION['userConfig']['login'] ) {
      $special = true;
    }
}
//prüfen ob der User zur Gruppe Admin oder Special gehört
$gruppen = getGruppen($_SESSION["login"]);
$admin = false;
foreach($gruppen as $value){
    if( $value['name'] == "Admin" ) $admin = true;
}

$visibility = $admin ? 'style="visibility:visible"' : 'style="visibility:hidden"';
$readonly   = $admin ? "" : 'readonly="readonly"';
$grp = getGruppen();

$is_admin = false;
$is_werkstatt = false;
foreach( $grp as $value ){
    if( $value['grpname'] == "Admin") $is_admin = true;
    if( $value['grpname'] == "Werkstatt") $is_admin = true;

}
if( !$is_admin ) echo "<b>Gruppe Admin nicht angelegt oder ihr keine  Mitglieder zugewiesn. install.txt lesn!!</br>CRM->Admin->Gruppen</b>";
if( !$is_admin ) echo "<b>Gruppe Werkstatt nicht angelegt oder ihr keine  Mitglieder zugewiesn. install.txt lesn!!</br>CRM->Admin->Gruppen</b>";

switch( $task ){
    case 1:
        GetCars( $owner, $owner_data['name']);
    break;          //Autos des Owners darstellen
    case 2:
        if ( isset($_POST['anlegen']) ) {
            if( $_POST[g_art_drop] != -1 ){
                $cardata_anlegen['c_gart'] = $_POST['g_art_drop'];
            }
            $c_id = NeuesAuto( $cardata_anlegen );
            header( "Location: lxcmain.php?owner=$owner&task=3&c_id=$c_id" );
        }
        else{     //Dateneingabe
            $g_art_drop = '<select tabindex="13" name="g_art_drop" id="g_art_drop"><option value="-1" selected>Getriebeart';
            $sql = "SELECT c_gart, count(c_gart) FROM lxc_cars WHERE c_gart != '' GROUP BY c_gart ORDER BY count DESC";
            $rs_g_art = $GLOBALS['dbh']->getall($sql);
            foreach( $rs_g_art as $value ){
                $g_art_drop.='<option value="'.$value['c_gart'].'" > '.$value['c_gart'];
            }
            $g_art_drop.='</select>';
            $msg = "<p class=\"ui-state-highlight ui-corner-all tools\" style=\"margin-top: 20px; padding: 0.6em;\">Fahrzeug von <b> ".$owner_data['name']."</b> anlegen.</p>";
            $t->set_var( array( 'owner' => $owner, 'c_text' =>"Kommentar eingeben", 'g_art_drop' => $g_art_drop, 'ERPCSS' => $_SESSION["stylesheet"], 'MSG'=>$msg ) );    //$formdata['ERPCSS'] = $_SESSION["stylesheet"];

            $t->set_file(array("tpl-file" => "lxcmain$task.tpl"));
            $t->pparse("out",array("tpl-file"));
        }
    break;
    case 3 :
        if ( isset($_POST['update']) ) {
            //echo "<pre>";
            //print_r( $_POST);
            //echo "</pre>";
            $cardata['mkb'] = $_POST['mkb'];
            $cardata['mkbwahl'] = $_POST['mkbwahl'];
            $cardata['typnummer'] = $_POST['c_t'];
            $cardata['chown'] = $_POST['chown'];
            if( $pos = strpos( $cardata['chown'], " -> " ) ){
                $cardata[chown] = substr( $cardata[chown], 0, $pos );
            }
            if ( isset($_POST['g_art_drop']) != -1 ) {
                $cardata['c_gart'] = $_POST['g_art_drop'];
            }
            UpdateCar( $c_id, $cardata );
        }
        if( isset($_GET["c_t"]) ){
                    UpdateTypNr( $c_id, $_GET['c_t'] );
        }
        $miscarray = ShowCar( $c_id );
        accessHistory(array($miscarray['c_id'],$miscarray['c_ln'],'A'));
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
        $miscarray['SPECIAL'] = $special ? '<button tabindex="24" type="button"  onClick="special(document.car.c_id.value, document.car.owner.value,1);">Spezial</button>&nbsp;&nbsp;&nbsp;' : '';
        $miscarray['ERPCSS'] = $_SESSION["stylesheet"];
        $t->set_var($miscarray);
        $t->set_file(array("tpl-file" => "lxcmain$task.tpl"));
        $t->pparse("out",array("tpl-file"));
    break;
}
ob_end_flush();
?>
