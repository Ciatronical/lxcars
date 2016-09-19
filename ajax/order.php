<?php
require_once __DIR__.'/../../inc/crmLib.php';
require_once __DIR__.'/../inc/ajax2function.php';

function getOrder( $id ){
    //writeLog( $id );
    $rs = $GLOBALS['dbh']->getOne( "SELECT oe.id AS order_id, customer.name AS customer_name FROM oe, customer WHERE oe.ordnumber = '".$id."' AND customer.id = oe.customer_id", true);
    //writeLog($rs);
    echo $rs;
}

function getPosition( $orderID ){
    //writeLog( $orderID );
    echo $GLOBALS['dbh']->getAll( "SELECT id AS position_id, parts_id, description, position AS item_position, unit, sellprice, marge_total, discount FROM orderitems WHERE trans_id = '".$orderID."' ORDER BY item_position", true );
}

function newEntry( $data ){
    $data = json_decode( $data );
    $data = ( array ) $data;
    writeLog($data);
    $rs = $GLOBALS[ 'dbh' ]->insert( 'orderitems', array( 'id', 'description', 'position', 'unit', 'sellprice', 'marge_total', 'discount' ), array( $data['order_id'], $data['pos_description'], $data['order_nr'], $data['pos_unit'], $data['pos_price'], $data['pos_total'], $data['pos_discount']), 'id' );
    //$rs = $GLOBALS[ 'dbh' ]->insert( 'lxc_a_pos', array( 'lxc_a_pos_aid', 'lxc_a_pos_order_nr', 'lxc_a_pos_todo', 'lxc_a_pos_emp', 'lxc_a_pos_status' ), array( $data['lxc_a_pos_aid'], $data['lxc_a_pos_order_nr'], $data['lxc_a_pos_todo'],$data['lxc_a_pos_emp'], $data['lxc_a_pos_status']), 'lxc_a_pos_id' );
    echo 1;
}

function updatePositions( $data) {
    $GLOBALS['dbh']->begin();
    foreach( $data as $key => $value ){
        $GLOBALS['dbh']->update( 'lxc_a_pos', array('lxc_a_pos_order_nr', 'lxc_a_pos_todo', 'lxc_a_pos_emp', 'lxc_a_pos_status'), array($value['lxc_a_pos_order_nr'], $value['lxc_a_pos_todo'], $value['lxc_a_pos_emp'], $value['lxc_a_pos_status']), 'lxc_a_pos_id = '.$value['lxc_a_pos_id'] );
    }
    $GLOBALS['dbh']->commit();
    echo 1;
}

function delPosition( $data ){
    echo $GLOBALS['dbh']->query( "DELETE FROM lxc_a_pos WHERE lxc_a_pos_id = ".$data );
}

function getArticleDescription( $data ){
    writeLog($data);
    echo $GLOBALS['dbh']->getAll( "SELECT description FROM parts", true );
    //writeLog($GLOBALS);
}

function getUsersFromGroup( $data ){
    echo json_encode( ERPUsersfromGroup( $data ) );
}




?>