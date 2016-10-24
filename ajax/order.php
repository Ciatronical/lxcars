<?php
require_once __DIR__.'/../../inc/crmLib.php';
require_once __DIR__.'/../inc/ajax2function.php';

function getOrder( $id ){
    //writeLog( $id );
    $rs = $GLOBALS['dbh']->getOne( "SELECT oe.ordnumber AS order_id, oe.id AS oe_id, customer.name AS customer_name FROM oe, customer WHERE oe.ordnumber = '".$id."' AND customer.id = oe.customer_id", true);
    //writeLog($rs);
    echo $rs;
}

function getPositions( $orderID ){
    //writeLog( $orderID );
    $rs = $GLOBALS['dbh']->getAll( "SELECT orderitems.id AS position_id, orderitems.parts_id, orderitems.qty, orderitems.description, orderitems.position AS item_position, orderitems.unit, orderitems.sellprice, orderitems.marge_total, orderitems.discount, orderitems.u_id, orderitems.status, parts.id AS partid, parts.partnumber FROM orderitems, parts WHERE trans_id = '".$orderID."'AND parts.id = orderitems.parts_id ORDER BY item_position", true );
    //$rs = $GLOBALS['dbh']->getAll( "SELECT id AS position_id, parts_id, description, position AS item_position, unit, sellprice, marge_total, discount FROM orderitems WHERE trans_id = '".$orderID."' ORDER BY item_position", true );
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
        //writeLog($data);
        //$GLOBALS['dbh']->update( 'orderitems', array('position', 'trans_id', 'description'), array($value['order_nr'], $value['item_nr'], $value['pos_description']), 'id = '.$value['pos_id'] );
        $GLOBALS['dbh']->update( 'orderitems', array('position', 'parts_id', 'description', 'unit', 'qty', 'sellprice', 'discount', 'marge_total', 'u_id', 'status'), array($value['order_nr'], $value['partID'], $value['pos_description'], $value['pos_unit'], $value['pos_qty'], $value['pos_price'], $value['pos_discount'], $value['pos_total'], $value['pos_emp'], $value['pos_status']), 'id = '.$value['pos_id'] );
        //$GLOBALS['dbh']->update( 'lxc_a_pos', array('lxc_a_pos_order_nr', 'lxc_a_pos_todo', 'lxc_a_pos_emp', 'lxc_a_pos_status'), array($value['lxc_a_pos_order_nr'], $value['lxc_a_pos_todo'], $value['lxc_a_pos_emp'], $value['lxc_a_pos_status']), 'lxc_a_pos_id = '.$value['lxc_a_pos_id'] );
    }
    $GLOBALS['dbh']->commit();
    echo 1;
}

function delPosition( $data ){
    //writeLog($data);
    //writeLog('hello');
    echo $GLOBALS['dbh']->query( "DELETE FROM orderitems WHERE id = ".$data );
}

function getUsersFromGroup( $data ){
    echo json_encode( ERPUsersfromGroup( $data ) );
}




/*
function getArticle( $articleDescription ) {
    //writeLog( $articleDescription );
    $rs = $GLOBALS['dbh']->getOne( "SELECT id, description, unit, sellprice FROM parts WHERE description = '".$articleDescription."'", true );
    writeLog( $rs );
    echo $rs;
}
*/









?>