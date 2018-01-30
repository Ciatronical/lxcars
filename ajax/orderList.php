<?php

//require_once __DIR__.'/../../inc/stdLib.php'; // for debug
require_once __DIR__.'/../../inc/crmLib.php';
require_once __DIR__.'/../inc/ajax2function.php';

function getOrderList( $data ){
  
    $format = "'DD.MM.YYYY HH24:MI'";

    $sql = "SELECT distinct on ( ordnumber ) * FROM ( SELECT distinct on (oe.ordnumber) 'true'::BOOL AS instruction,oe.id, to_char(oe.itime, ".$format.") AS itime, to_char(oe.mtime, ".$format.") AS mtime, oe.ordnumber, instructions.description, oe.amount ";
    $sql.= "FROM oe, instructions, parts WHERE oe.customer_id = ".$data['owner']." AND instructions.trans_id = oe.id AND oe.c_id = ".$data['c_id']." AND parts.id = instructions.parts_id AND instructions.position = 1 UNION ";
    $sql.= "SELECT distinct on (oe.ordnumber)'false'::BOOL AS instruction,oe.id, to_char(oe.itime, ".$format.") AS itime, to_char(oe.mtime, ".$format.") AS mtime, oe.ordnumber, orderitems.description, oe.amount FROM oe, orderitems, parts ";
    $sql.= "WHERE oe.customer_id = ".$data['owner']." AND oe.c_id = ".$data['c_id']." AND orderitems.trans_id = oe.id AND parts.id = orderitems.parts_id AND orderitems.position = 1 ";
    $sql.= "ORDER BY instruction DESC ) AS testTable ORDER BY ordnumber DESC";  
    
    echo $GLOBALS['dbh']->getALL( $sql, true );
  
  }

?>