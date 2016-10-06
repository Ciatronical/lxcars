<?php
require_once __DIR__.'/../../inc/crmLib.php';
require_once __DIR__.'/../inc/ajax2function.php';

function getOrder( $id ){
    //writeLog( $id );
    $rs = $GLOBALS['dbh']->getOne( "SELECT oe.ordnumber AS order_id, oe.id AS oe_id, customer.name AS customer_name FROM oe, customer WHERE oe.ordnumber = '".$id."' AND customer.id = oe.customer_id", true);
    //writeLog($rs);
    echo $rs;
}

function getPosition( $orderID ){
    writeLog( $orderID );
    $rs = $GLOBALS['dbh']->getAll( "SELECT id AS position_id, parts_id, description, position AS item_position, unit, sellprice, marge_total, discount FROM orderitems WHERE trans_id = '".$orderID."' ORDER BY item_position", true );
    //writeLog( $rs );
    echo $rs;
}




function newEntry( $data ){
    //writeLog($data);
       echo $GLOBALS['dbh']->insert( 'orderitems', array( 'position', 'trans_id', 'description', 'sellprice', 'discount', 'marge_total'), array( $data['order_nr'], $data['order_id'], $data['pos_description'], $data['pos_price'], $data['pos_discount'], $data['pos_total']), 'id', 'orderitemsid');
    //$rs = $GLOBALS[ 'dbh' ]->insert( 'orderitems', array( 'position', 'trans_id', 'description', 'unit', 'sellprice', 'marge_total', 'discount' ), array( $data['order_id'], $data['pos_description'], $data['order_nr'], $data['pos_unit'], $data['pos_price'], $data['pos_total'], $data['pos_discount']), 'id', 'orderitemsid' );
    //$rs = $GLOBALS[ 'dbh' ]->insert( 'lxc_a_pos', array( 'lxc_a_pos_aid', 'lxc_a_pos_order_nr', 'lxc_a_pos_todo', 'lxc_a_pos_emp', 'lxc_a_pos_status' ), array( $data['lxc_a_pos_aid'], $data['lxc_a_pos_order_nr'], $data['lxc_a_pos_todo'],$data['lxc_a_pos_emp'], $data['lxc_a_pos_status']), 'lxc_a_pos_id' );
    //echo 1;
}

function updatePositions( $data) {
    $GLOBALS['dbh']->begin();
    foreach( $data as $key => $value ){
        //$GLOBALS['dbh']->update( 'orderitems', array('position', 'trans_id', 'description', 'sellprice', 'discount', 'marge_total'), array($value['lxc_a_pos_order_nr'], $value['lxc_a_pos_todo'], $value['lxc_a_pos_emp'], $value['lxc_a_pos_status']), 'lxc_a_pos_id = '.$value['lxc_a_pos_id'] );
        //$GLOBALS['dbh']->update( 'lxc_a_pos', array('lxc_a_pos_order_nr', 'lxc_a_pos_todo', 'lxc_a_pos_emp', 'lxc_a_pos_status'), array($value['lxc_a_pos_order_nr'], $value['lxc_a_pos_todo'], $value['lxc_a_pos_emp'], $value['lxc_a_pos_status']), 'lxc_a_pos_id = '.$value['lxc_a_pos_id'] );
    }
    $GLOBALS['dbh']->commit();
    echo 1;
}

function delPosition( $data ){
    echo $GLOBALS['dbh']->query( "DELETE FROM lxc_a_pos WHERE lxc_a_pos_id = ".$data );
}

function getArticleDescription( $data ){
    //writeLog($data);
    echo $GLOBALS['dbh']->getAll( "SELECT id, description, sellprice, unit FROM parts", true );
}





/*
function getArticle( $articleDescription ) {
    //writeLog( $articleDescription );
    $rs = $GLOBALS['dbh']->getOne( "SELECT id, description, unit, sellprice FROM parts WHERE description = '".$articleDescription."'", true );
    writeLog( $rs );
    echo $rs;
}
*/






function getUsersFromGroup( $data ){
    echo json_encode( ERPUsersfromGroup( $data ) );
}




?>