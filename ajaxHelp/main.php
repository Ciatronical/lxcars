<?php
    require_once( "../../inc/stdLib.php" ); 
    require_once( "../../inc/crmLib.php" );
    require_once( "../inc/lxcLib2.php" );  
    $task     = 'getCars';//array_shift( $_GET );



    
    switch( $task ){
        case "newCategory":
            //$sql="INSERT INTO event_category ( label, color, cat_order ) VALUES ( '$newCat', '$newColor', ( SELECT max( cat_order ) + 1 AS cat_order FROM event_category) )";
            //$rc=$_SESSION['db']->query($sql); 
        break;
        case "getCars":
            $sql = "SELECT c_ln, c_2, c_3, c_id, c_t FROM lxc_cars WHERE c_ow  = 1126 ORDER BY c_id";
            //echo $sql;            
            $rs = $_SESSION['db']->getAll( $sql );
            $json = '{ "Result":"OK", "Records":[';
            if( $rs ){
                $noData = "Keine Daten";
                //print_r( $rs );
                foreach( $rs as $key => $row ){
                    $z2 = $rs[$key]['c_2'];
                    $z3 = substr( $rs[$key]['c_3'], 0, 3 );
                    //echo $z3;
                    $c_ln = $rs[$key]['c_ln'];
                    $art="Pkw";
                    //echo $rs[$key]['c_t'].', '; 
                    if( $rs[$key]['c_t'] == "" ){
                        $rskba = lxc2db( "-C ".$z2." ".$z3 );
                        //print_r( $rskba );
                        $herst = $rskba != -1 ? $rskba[0][1] : $noData;
                        $typ = $rskba != -1  ? $rskba[0][2] : $noData;
                        $name = $rskba != -1 ? $rskba[0][3] : $noData;
                        if ( $rskba == -1 ){
                            $rs_mykba = GetFhzTyp( $z2, $z3 );
                            $herst = $rs_mykba['hersteller'] != '' ? $rs_mykba['hersteller'] : $noData;
                            $art = $rs_mykba['klasse_aufbau'] != '' ? $rs_mykba['klasse_aufbau'] : "???";
                            $name = $rs_mykba['typ'] != '' ? $rs_mykba['typ'] : $noData;
                            $typ = $rs_mykba['bezeichung'];
                        }
                    }
                    else{
                        $rskba = lxc2db( "-T ".$rs[$key]['c_t'] );
                        //echo $rs[$key]['c_t'];
                        //print_r( $rskba );
                        $herst = $rskba != -1 ? $rskba[0][4] : $noData;
                        $typ = $rskba != -1 ? $rskba[0][5] : $noData;
                        $name = $rskba != -1 ? $rskba[0][6] : $noData;
                    }
                    //echo $rs[$key].' ';
                    $pre = $key ? ',' : '';
                    $json .= $pre.'{"c_ln":"'.$c_ln.'","herst":"'.$herst.'","typ":"'.$typ.'","name":"'.$name.'","art":"'.$art.'","id":"'.$key.'"}';
                }//foreach
                $json .= ']}';
            }
            echo $json;
            //echo '{ "Result":"OK", "Records":'.$rs['json_agg'].' }';
            break; 
    }
 ?>