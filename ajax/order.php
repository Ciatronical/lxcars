<?php

//require_once __DIR__.'/../../inc/stdLib.php'; // for debug
require_once __DIR__.'/../../inc/crmLib.php';
require_once __DIR__.'/../inc/ajax2function.php';

function getOrder( $id ){
    //writeLog( $id );
    $rs = $GLOBALS['dbh']->getOne( "SELECT oe.ordnumber AS order_id, oe.id AS oe_id, oe.transdate, oe.reqdate, oe.km_stnd, oe.c_id, oe.status AS order_status, oe.customer_id AS customer_id, oe.car_status, customer.name AS customer_name, lxc_cars.c_ln FROM oe, customer, lxc_cars WHERE oe.ordnumber = '".$id."' AND customer.id = oe.customer_id AND oe.c_id = lxc_cars.c_id", true);
    //writeLog($rs);
    echo $rs;
}

function getPositions( $orderID ){
    //writeLog( $orderID );
    $rs = $GLOBALS['dbh']->getAll( "SELECT orderitems.id AS position_id, orderitems.parts_id, orderitems.qty, orderitems.description, orderitems.position AS item_position, orderitems.unit, orderitems.sellprice, orderitems.marge_total, orderitems.discount, orderitems.u_id, orderitems.status, parts.id AS partid, parts.partnumber FROM orderitems, parts WHERE orderitems.trans_id = '".$orderID."'AND parts.id = orderitems.parts_id ORDER BY item_position", true );
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
    //writeLog($data);
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

function getUnits(){
    //writeLog( $orderID );
    $rs = $GLOBALS['dbh']->getAll( "SELECT name FROM units", true );
    echo $rs;
}

function getBuchungsgruppen(){
    //writeLog( $orderID );
    $rs = $GLOBALS['dbh']->getAll( "SELECT id, description FROM buchungsgruppen", true );
    echo $rs;
}

function newPart( $data ){
    //writeLog($data);
    $rs = $GLOBALS['dbh']->getAll( "select substring(partnumber from '[0-9]+'),articlenumber from parts, defaults where parts.partnumber = '".$data['part']."'" );

        if($rs[0][substring] != null ) {
            //writeLog($rs[0][substring]);
            $val = (intval($rs[0][substring])+1);
            //writeLog($val);
        } else {
            //writeLog('substring existiert nicht ...');
        }

    $GLOBALS['dbh']->insert( 'parts', array( 'partnumber', 'description', 'unit', 'listprice', 'sellprice', 'buchungsgruppen_id'), array( $data['part'], $data['description'], $data['unit'], $data['listprice'], $data['sellprice'], $data['buchungsgruppen_id']), FALSE);
    $rs = $GLOBALS['dbh']->getAll( "SELECT id FROM parts WHERE partnumber = '".$data['part']."'", true );
    echo $rs;
    //echo 1;
}

function getArticleNumber( $unit ){
    //writeLog( $unit );
    if($unit == 'Stck' || $unit == 't' || $unit == 'kg' || $unit == 'g' || $unit == 'mg' || $unit == 'L' || $unit == 'ml') {
        $rs = $GLOBALS['dbh']->getOne( "SELECT id AS defaults_id, articlenumber AS art_nr FROM defaults", true);
    }elseif($unit == 'psch' || $unit == 'Tag' || $unit == 'Std' || $unit == 'min') {
        $rs = $GLOBALS['dbh']->getOne( "SELECT id AS defaults_id, servicenumber AS art_nr FROM defaults", true);
    }
    echo $rs;
}

function increaseArticleNr( $updArtNr) {
    //writeLog( $updArtNr );
    $GLOBALS['dbh']->begin();
    foreach( $updArtNr as $key => $value ){
        if($value['unit'] == 'Stck' || $value['unit'] == 't' || $value['unit'] == 'kg' || $value['unit'] == 'g' || $value['unit'] == 'mg' || $value['unit'] == 'L' || $value['unit'] == 'ml') {
            $GLOBALS['dbh']->update( 'defaults', array('articlenumber'), array($value['artNr']), 'id = '.$value['id']);
            //writeLog($value['id']);
        }elseif($value['unit'] == 'psch' || $value['unit'] == 'Tag' || $value['unit'] == 'Std' || $value['unit'] == 'min') {
            $GLOBALS['dbh']->update( 'defaults', array('servicenumber'), array($value['artNr']), 'id = '.$value['id']);
        }
    }
    $GLOBALS['dbh']->commit();
    echo 1;
}

function updateOrder( $data) {
    //writeLog($data[0]);

    //$GLOBALS['dbh']->begin();
    //foreach( $data as $key => $value ){
        //writeLog($data);
        //$GLOBALS['dbh']->update( 'orderitems', array('position', 'trans_id', 'description'), array($value['order_nr'], $value['item_nr'], $value['pos_description']), 'id = '.$value['pos_id'] );
        //$GLOBALS['dbh']->update( 'oe', array('km_stnd', 'c_id', 'status'), array($data[0]['km_stnd'], $data[0]['c_id'], $data[0]['status']), 'id = '.$data[0]['ordnumber'] );
        $GLOBALS['dbh']->update( 'oe', array('km_stnd', 'status', 'netamount', 'amount', 'car_status'), array($data[0]['km_stnd'], $data[0]['status'], $data[0]['netamount'], $data[0]['amount'], $data[0]['car_status']), 'id = '.$data[0]['ordnumber'] );
        //$GLOBALS['dbh']->update( 'lxc_a_pos', array('lxc_a_pos_order_nr', 'lxc_a_pos_todo', 'lxc_a_pos_emp', 'lxc_a_pos_status'), array($value['lxc_a_pos_order_nr'], $value['lxc_a_pos_todo'], $value['lxc_a_pos_emp'], $value['lxc_a_pos_status']), 'lxc_a_pos_id = '.$value['lxc_a_pos_id'] );
    //}
    //$GLOBALS['dbh']->commit();
    echo 1;

}

function getCar( $c_id ){
    //writeLog( $c_id );
    //$rs = $GLOBALS['dbh']->getOne( "SELECT oe.ordnumber AS order_id, oe.id AS oe_id,oe.transdate, oe.reqdate, oe.km_stnd, oe.c_id, oe.status AS order_status , customer.name AS customer_name FROM oe, customer WHERE oe.ordnumber = '".$id."' AND customer.id = oe.customer_id", true);
    $rs = $GLOBALS['dbh']->getOne( "SELECT lxc_cars.c_ln AS amtl_kennz, lxc_cars.c_id AS car_id, customer.id AS customer_id, customer.name AS customer_name, customer.taxzone_id, customer.currency_id, defaults.sonumber AS last_order_nr, defaults.id AS defaults_id FROM lxc_cars, customer, defaults WHERE lxc_cars.c_id = '".$c_id."' AND customer.id = lxc_cars.c_ow", true);
    //writeLog($rs);
    echo $rs;
}

function getOrderNumber() {
    echo $rs = $GLOBALS['dbh']->getOne( "SELECT sonumber FROM defaults", true );
}

function getDataForNewOrder( $data ) {
    //writeLog( $data );
    $employee = $data[0]['employee'];
    echo $rs = $GLOBALS['dbh']->getOne( "SELECT customer.name AS customer_name, customer.taxzone_id, customer.currency_id, lxc_cars.c_ln, employee.id AS employee_id, employee.name AS employee_name FROM customer, lxc_cars, employee WHERE customer.id = '".$data[0]['customer']."' AND lxc_cars.c_id = '".$data[0]['c_id']."' AND lxc_cars.c_ow = '".$data[0]['customer']."' AND employee.name = '".$employee."'", true );
}


function newOrder( $data ) {
    //writeLog( $data );
    echo $GLOBALS['dbh']->insert( 'oe', array( 'ordnumber', 'customer_id', 'c_id', 'taxzone_id', 'currency_id', 'employee_id'), array( $data[0]['ordnumber'], $data[0]['customer_id'], $data[0]['c_id'], $data[0]['taxzone_id'], $data[0]['currency_id'], $data[0]['employee_id']), FALSE);

    $GLOBALS['dbh']->begin();
    foreach( $data as $key => $value ){
        $GLOBALS['dbh']->update( 'defaults', array('sonumber'), array($value['ordnumber']), 'id = 1');
    }
    $GLOBALS['dbh']->commit();

    echo 1;
}

function getOrderID( $newOrdNr ) {
    //writeLog( $newOrdNr );
    echo $rs = $GLOBALS['dbh']->getOne( "SELECT id AS auftrags_id FROM oe WHERE ordnumber = '".$newOrdNr."'", true );
}

function getOrderList( $data ) {
    $statusSearchString = $data['statusSearch'] == 'alle' ? '' : " oe.status = '".$data['statusSearch']."' AND ";//tenärer Operator
    $dateStringFrom = varExist( $data['datum_von'] ) ? " oe.transdate BETWEEN  <= '".$data['datum_von']."' AND " : '';
    $dateStringTo   = varExist( $data['datum_bis'] ) ?  " oe.transdate BETWEEN  >= '".$data['datum_bis']."' AND " : '';
    //writeLog($data['kennzeichen'].', '.$data['kundenname'].', '.$data['datum_von'].', '.$data['datum_bis'].', '.$data['statusSearch']);
    $sql = "
        SELECT
            oe.status AS auftragsstatus,
            oe.transdate AS auftragsdatum,
            oe.ordnumber AS auftragsnummer,
            oe.car_status AS car_status,
            orderitems.description AS ersteposition,
            customer.name AS besitzer,
            customer.id AS owner,
            lxc_cars.c_ln AS kennzeichen,
            lxc_cars.c_id AS c_id
        FROM
            oe,
            orderitems,
            customer,
            lxc_cars
        WHERE"
            .$statusSearchString
            .$dateStringFrom
            .$dateStringTo
            ." orderitems.trans_id = oe.id AND orderitems.position = 1 AND
             customer.name ILIKE '%".$data['kundenname']."%' AND customer.id = oe.customer_id AND
             lxc_cars.c_ln ILIKE '%".$data['kennzeichen']."%' AND lxc_cars.c_id = oe.c_id
        ORDER BY
            oe.ordnumber";
    //writeLog( $sql );
    echo $rs = $GLOBALS['dbh']->getAll( $sql, true );

}
?>