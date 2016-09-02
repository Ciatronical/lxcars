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
     $rs = $GLOBALS['dbh']->getOne( 'SELECT lxc_a_id, name AS kundenname, lxc_a_finish_time, lxc_a_init_time, lxc_a_km, lxc_a_modified_on, c_ln FROM lxc_a, lxc_cars, customer WHERE lxc_a_id = '.$id.' AND lxc_a_c_id = c_id AND c_ow = id', true );
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
function getArtikel($artikel){
     $rs = $GLOBALS['dbh']->getAll( 'SELECT * FROM lxc_a_pos WHERE lxc_a_pos_todo = '.$artikel, true );
     //echo json_encode( $rs['json_agg'] );
     //echo json_encode( $rs );
     echo $rs;
}
*/

?>