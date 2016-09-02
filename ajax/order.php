<?php
//writeLog(__DIR__);
require_once __DIR__.'/../inc/ajax2function.php';

function newEntry( $data ){
    //writeLog( 'Ronny' );
    writeLog( $data );
    //$data = json_decode($data);
    //$data = (array) $data;
    //writeLog($data);
    //$rs = $GLOBALS['dbh']->insert( 'example', array( 'date_time', 'c_name', 'c_age', 'c_comments' ), array( $data['datetime'], $data['name'],$data['age'], $data['comments']) );
    //writelog($rs);
    echo 1;
}

function getOrder($id){
writeLog($id);
     $rs = $GLOBALS['dbh']->getOne( 'SELECT * FROM lxc_a WHERE lxc_a_id = '.$id, true );
     //echo json_encode( $rs['json_agg'] );
     //echo json_encode( $rs );
     echo $rs;
}


function getArtikel($id){
     $rs = $GLOBALS['dbh']->getAll( 'SELECT * FROM lxc_a_pos WHERE lxc_a_pos_aid = '.$id, true );
         //echo json_encode( $rs['json_agg'] );
         //echo json_encode( $rs );
     echo $rs;
}

/*
// Gibt ein Array mit allen Nutzern einer angegebenen Grupppe zurück
function ERPUsersfromGroup($grp_name) {
    $rueck;
    $i = 0;
    $grp_id = '';
    //Gruppen ID herausfiltern
    $allERPusers = getAllERPusers();
    $allERPgroups = getAllERPgroups();
    $sql = "SELECT usrg.user_id AS user_id, usrg.group_id AS group_id FROM auth.user_group AS usrg ORDER by usrg.user_id";
    $allAssignments = $GLOBALS['dbh_auth']->getAll( $sql );
    foreach ( $allERPgroups as $key => $gruppe ) {
        if($gruppe['name'] == $grp_name) {
            $grp_id = $gruppe['id'];
        }
    }
    //Rückgabe-Array zusammensetzeng
    foreach ( $allAssignments as $key => $zuordnung ) {
        if($zuordnung['group_id'] == $grp_id) {
            $user_id = $zuordnung['user_id'];
            foreach ( $allERPusers as $key => $nutzer ) {
                    if($nutzer['id'] == $user_id) {
                        $rueck[$i] = array("id"=>$user_id,"login"=>$nutzer['login'],"name"=>$nutzer['name']);
                        $i++;
                    }
            }
        }
    }
    return $rueck;
}
*/

/*
function getArtikel($artikel){
     $rs = $GLOBALS['dbh']->getAll( 'SELECT * FROM lxc_a_pos WHERE lxc_a_pos_todo = '.$artikel, true );
     //echo json_encode( $rs['json_agg'] );
     //echo json_encode( $rs );
     echo $rs;
}
*/

?>