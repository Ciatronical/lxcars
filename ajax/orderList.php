<?php

//require_once __DIR__.'/../../inc/stdLib.php'; // for debug
require_once __DIR__.'/../../inc/crmLib.php';
require_once __DIR__.'/../inc/ajax2function.php';

function getOrderList( $data ){

    $format = "'DD.MM.YYYY HH24:MI'"; //ToDo lng

    $sql = "SELECT distinct on ( id ) * FROM ( SELECT distinct on ( oe.id ) 'true'::BOOL AS instruction, oe.id, to_char( oe.itime, ".$format." ) AS itime, to_char(oe.mtime, ".$format." ) AS mtime, oe.ordnumber, instructions.description, COALESCE( NULLIF( oe.amount, null ) , 0 ) AS amount ";
    $sql.= "FROM oe, instructions, parts WHERE instructions.trans_id = oe.id AND oe.c_id = ".$data['c_id']." AND parts.id = instructions.parts_id AND instructions.position = 1 UNION ";
    $sql.= "SELECT distinct on ( oe.ordnumber )'false'::BOOL AS instruction, oe.id, to_char( oe.itime, ".$format." ) AS itime, to_char( oe.mtime, ".$format." ) AS mtime, oe.ordnumber, orderitems.description, COALESCE( NULLIF( oe.amount, null ) , 0 ) AS amount FROM oe, orderitems, parts ";
    $sql.= "WHERE oe.c_id = ".$data['c_id']." AND orderitems.trans_id = oe.id AND parts.id = orderitems.parts_id AND orderitems.position = 1 ";
    $sql.= "ORDER BY instruction DESC ) AS testTable ORDER BY id DESC";
    writeLog( $sql );
    echo $GLOBALS['dbh']->getALL( $sql, true );
}

?>