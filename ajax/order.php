<?php
//writeLog(__DIR__);
require_once __DIR__.'/../inc/ajax2function.php';

function newEntry( $data ){
    //writeLog( 'Ronny' );
    //writeLog( $data );
    $data = json_decode( $data );
    //writeLog($data);
    $data = ( array ) $data;
    writeLog( $data );
    $rs = $GLOBALS[ 'dbh' ]->insert( 'lxc_a_pos', array( 'lxc_a_pos_aid', 'lxc_a_pos_order_nr', 'lxc_a_pos_todo', 'lxc_a_pos_emp', 'lxc_a_pos_status' ), array( $data['lxc_a_pos_aid'], $data['lxc_a_pos_order_nr'], $data['lxc_a_pos_todo'],$data['lxc_a_pos_emp'], $data['lxc_a_pos_status']), 'lxc_a_pos_id' );
    writelog( $rs );
    echo 1;
}

function updatePositions( $data) {
    writeLog( $data );
    //$GLOBALS['dbh']->begin();

    foreach( $data as $key => $value ){
        writeLog( $value );
        $GLOBALS['dbh']->update( 'lxc_a_pos', array('lxc_a_pos_order_nr', 'lxc_a_pos_todo', 'lxc_a_pos_emp', 'lxc_a_pos_status'), array($value['lxc_a_pos_order_nr'], $value['lxc_a_pos_todo'], $value['lxc_a_pos_emp'], $value['lxc_a_pos_status']), 'lxc_a_pos_id = '.$value['lxc_a_pos_id'] );
    }

    //$data = ( array ) $data;
    //writeLog( $data );

    //$rs = $GLOBALS['dbh']->update( 'lxc_a_pos', array( 'lxc_a_pos_todo' ), array( $data['lxc_a_pos_todo'] ), "lxc_a_pos_id = ".$data['lxc_a_pos_id'] );


    //Begin


        //$result = parent::beginTransaction();
        //if( $this->logAll ) $this->writeLog( "PDO::beginTransaction() returns: ".$result );
            //return $result;
    //Commit
        //$result = parent::commit();
        //if( $this->logAll ) $this->writeLog( "PDO::commit() returns: ".$result );
            //return $result;
    //$data = json_decode($data);
    //writeLog($data);
    //$data = (array) $data;
    //writeLog($data);
     echo 1;
}

function getOrder($id){
     writeLog($id);
     $rs = $GLOBALS['dbh']->getOne( 'SELECT lxc_a_id, name AS kundenname, lxc_a_finish_time, lxc_a_init_time, lxc_a_km, lxc_a_modified_on, c_ln FROM lxc_a, lxc_cars, customer WHERE lxc_a_id = '.$id.' AND lxc_a_c_id = c_id AND c_ow = id', true );
     //echo json_encode( $rs['json_agg'] );
     //echo json_encode( $rs );
     echo $rs;
}

function getPosition($id){
     $rs = $GLOBALS['dbh']->getAll( 'SELECT * FROM lxc_a_pos WHERE lxc_a_pos_aid = '.$id.'ORDER BY lxc_a_pos_id', true );
         //echo json_encode( $rs['json_agg'] );
         //echo json_encode( $rs );
     echo $rs;
}

function delPosition( $data ){
    writeLog($data);
    //$sql = "DELETE FROM lxc_a_pos WHERE lxc_a_pos_id = '".$data."'";
    //$rs = $GLOBALS['dbh']->query( $sql );
    echo 1;
}



?>