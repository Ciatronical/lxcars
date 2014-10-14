<?php

function lxc2db( $parastr ){
    $rsdata = array();
    $ret = -2;
    $db_name = "lxcars";
    $command = "../lxc2db -d ".$db_name." ".$parastr;
    exec( $command, $rsdata, $ret );
    switch ( $ret ){
        case 0:
            foreach( $rsdata as $key => $value ) {
                $rs[$key] = explode ( ';', $value );
            }
            return $rs;
        break;
        case 2://no Data
            return -1;
        break;
        default:
            return -1;
    }
}
?>