<?php
/***********************************************************************************************************
lxcaufSuche Sucht nach Aufträgen
angefangen im Dezember 2010 von Ronny
************************************************************************************************************/
include_once("../inc/template.inc");
require_once("./inc/lxcLib.php");
include_once("../inc/UserLib.php");
require_once("../inc/stdLib.php");
include_once("../inc/crmLib.php");
$gruppen = getGruppen();
foreach($gruppen as $key=>$value){
    if($gruppen[$key]['grpname']=="Werkstatt") {$schrauber=getMitglieder($gruppen[$key]['grpid']);}
}

if( !$schrauber ){
    echo "<b>Gruppe Werkstatt nicht angelegt oder ihr keine  Mitglieder zugewiesen. install.txt lesen!!</br>CRM->Admin->Gruppen</b>";
}
$emp = -1;
foreach( $schrauber as $key => $value ){
    if( $schrauber[$key]['login'] == $_SESSION['login'] ){
        $lxc_a_pos_emp = $key;
    }
}
$formdata = $_GET;

// " -> Berlin " entfernen
if( $pos = strpos( $formdata['c_ow'], " -> " ) ){
    $formdata['c_ow'] = substr( $formdata['c_ow'], 0, $pos );
}

if( $pos = strpos( $formdata['c_ln'], " -> " ) ){
    $formdata['c_ln'] = substr( $formdata['c_ln'], 0, $pos );
}

$formdata['c_ow_id'] = $c_ow;

$formdata["lxc_a_status".$formdata['lxc_a_status']] = "selected";
$formdata['emp'] = $_SESSION['employee'];
if( $formdata['selbstgeschraubt'] ){
    $formdata['lxc_a_pos_emp'] = $lxc_a_pos_emp;
}
if( $formdata[lxc_a_status] == "selected" ){
    $formdata[lxc_a_status] = 4;
    $formdata[lxc_a_status4] = "selected";
}


$rs = SucheAuftrag( $formdata );

$formdata['ERPCSS'] = $_SESSION["stylesheet"];
$t = new Template($base);
$menu =  $_SESSION['menu'];
doHeader($t);
/*
$t->set_var( array(
    JAVASCRIPTS   => $menu['javascripts'],
    STYLESHEETS   => $menu['stylesheets'],
    PRE_CONTENT   => $menu['pre_content'],
    START_CONTENT => $menu['start_content'],
    END_CONTENT   => $menu['end_content'] ) );
*/
$t->set_file(array("tpl-file" => "lxcaufSuche.tpl"));
$t->set_block("tpl-file","Liste","Block");
if( !$formdata[reset] ){
    $t->set_var($formdata);
}
$i = 0;

//neue Aufträge
$rs = $GLOBALS['dbh']->getALL("SELECT distinct on (lxc_a_id) * FROM
                                              (
                                                SELECT distinct on (lxc_a_id) 'true'::BOOL AS instruction,oe.id,lxc_cars.c_ln, oe.transdate, oe.ordnumber AS lxc_a_id, instructions.description AS lxc_a_pos_todo, oe.car_status AS lxc_a_car_status, oe.status AS lxc_a_status, oe.finish_time AS to_char, customer.name AS name, oe.c_id AS c_id, oe.customer_id AS c_ow FROM oe, instructions, parts, lxc_cars, customer WHERE instructions.trans_id = oe.id AND parts.id = instructions.parts_id AND lxc_cars.c_id = oe.c_id AND customer.id = oe.customer_id UNION
                                                SELECT distinct on (lxc_a_id)'false'::BOOL AS instruction,oe.id,lxc_cars.c_ln, oe.transdate, oe.ordnumber AS lxc_a_id, orderitems.description AS lxc_a_pos_todo, oe.car_status AS lxc_a_car_status, oe.status AS lxc_a_status,oe.finish_time AS to_char, customer.name AS name, oe.c_id AS c_id, oe.customer_id AS c_ow FROM oe, orderitems, parts, lxc_cars, customer WHERE  orderitems.trans_id = oe.id AND parts.id = orderitems.parts_id AND orderitems.position = 1 AND lxc_cars.c_id = oe.c_id AND customer.id = oe.customer_id ORDER BY instruction DESC
                                              ) AS testTable ORDER BY lxc_a_id DESC;");
$array = SucheAuftrag( $formdata );

foreach( $array as $val ){
array_push( $rs,$val );
}


if( $rs && ( $i < $_SESSION['listLimit'] ) ){ //
    foreach( $rs as $zeile ){
        //Status Farben erstellen - für die spätere Markierung in der Auftragsauflistung
        //Fall 1: Auftrag angenommen, Auto nicht in der Werkstatt ( nicht auf dem Gelände )
        if( $zeile['lxc_a_car_status'] == 1 && $zeile['lxc_a_status'] == 1 || $zeile['lxc_a_car_status'] == 'Auto nicht hier' ) {
            $farbe='red';
            $statustext='Auto nicht hier';
        }
        //Fall 2: Auftrag angenommen, Auto in der Werkstatt ( auf dem Gelände ) - Reparatur im Gange
        elseif( $zeile['lxc_a_car_status'] == 2 && $zeile['lxc_a_status'] == 1 || $zeile['lxc_a_car_status'] == 'Auto hier' && $zeile['lxc_a_status'] == 'bearbeitet' ) {
            $farbe='yellow';
            $statustext='Auto hier / wird repariert';
        }
        //Fall 3: Auftrag angenommen, Gegenstand in der Werkstatt - Reparatur im Gange
        elseif($zeile['lxc_a_car_status'] == 3 && $zeile['lxc_a_status'] == 1) {
            $farbe='yellow';
            $statustext='Sonstiges / wird repariert';
        }
        //Fall 4: Auftrag angenommen, muss bestellt werden
        elseif($zeile['lxc_a_car_status'] == 4 && $zeile['lxc_a_status'] == 1) {
            $farbe='yellow';
            $statustext='Bestellung';
        }
        //Fall 5: Auftrag bearbeitet, Auto in der Werkstatt ( auf dem Gelände ) - Reparatur abgeschlossen
        elseif($zeile['lxc_a_car_status'] == 2 && $zeile['lxc_a_status'] == 2) {
            $farbe='green';
            $statustext='Auto hier / fertig';
        }
        //Fall 6: Auftrag bearbeitet, Gegenstand in der Werkstatt - Reparatur abgeschlossen
        elseif($zeile['lxc_a_car_status'] == 3 && $zeile['lxc_a_status'] == 2) {
            $farbe='green';
            $statustext='Sonstiges / fertig';
        }
        //Fall 7: Bestellung angekommen
        elseif($zeile['lxc_a_car_status'] == 4 && $zeile['lxc_a_status'] == 2) {
            $farbe='green';
            $statustext='Bestellung';
        }
        //Restliche Fälle
        else {
            $farbe='lightgrey';
            $statustext='Auto abgeholt / nicht abgerechnet';
        }
        if($zeile['id'] != null)
        $zeile['id']=0;

        $t->set_var(array(id => $zeile['id'], rs_c_ln => $zeile['c_ln'], lxc_a_id => $zeile['lxc_a_id'], lxc_a_car_status => $zeile['lxc_a_car_status'], Statustext => $statustext, SpCol => $farbe, kdname => $zeile['name'], LineCol => $bgcol[($i%2+1)], todo => $zeile['lxc_a_pos_todo'], adate => $zeile['to_char'], a_c_ow => $zeile['c_ow'], a_c_id => $zeile['c_id']));// end set_var
        $t->parse("Block","Liste",true);
         $i++;
    }
}

$t->pparse("out",array("tpl-file"),$_SESSION["lang"],"firma");
?>
