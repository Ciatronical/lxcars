<?php

//require_once __DIR__.'/../../inc/crmLib.php';
require_once __DIR__.'/../inc/ajax2function.php';


function newEntry( $data ){
    writeLog( $data );
    $data = json_decode( $data );
    $data = ( array ) $data;
    writeLog($data);
    $rs = $GLOBALS[ 'dbh' ]->insert( 'example', array( 'date_time', 'c_name', 'c_age', 'c_comments' ), array( $data['datetime'], $data['name'],$data['age'], $data['comments']) );
    echo 1;
}


function getPerformanceData(){
    //alle Datensätze bereitstellen
    $rs = $GLOBALS[ 'dbh' ]->getAll( "SELECT REPLACE( u_id, '0', 'Nicht zugeordnet' ) AS name, round( sum( qty )::numeric, 2 )::text AS hours, sum( qty ) FROM orderitems WHERE unit = 'Std' AND u_id != '' AND itime > '2018-07-01' GROUP BY u_id ORDER BY sum DESC", true );
    echo $rs;

}

?>