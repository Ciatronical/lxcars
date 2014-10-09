<?php
    require_once("../../inc/stdLib.php"); 
    require_once("../../inc/crmLib.php");  
    $task     = 'getCars';//array_shift( $_GET );

   // echo "Task: ".$task;
  
    
    switch( $task ){
        case "newCategory":
            //$sql="INSERT INTO event_category ( label, color, cat_order ) VALUES ( '$newCat', '$newColor', ( SELECT max( cat_order ) + 1 AS cat_order FROM event_category) )";
            //$rc=$_SESSION['db']->query($sql); 
        break;
        case "getCars":
           // $sql = "SELECT json_agg( json_cars )  FROM ( SELECT c_ln, c_2, c_3, c_id, c_t FROM lxc_cars WHERE c_ow  = 1126 ORDER BY c_id )  AS json_cars";
            //echo $sql;            
            //$rs = $_SESSION['db']->getOne( $sql );
            //echo $rs['json_agg'];
            echo '{
                    "Result":"OK",
                    "Record":{"c_ln":"MOL-NH125","c_2":"0600","c_3":"532145A","c_id":12,"c_t":"01774"}
                   }';/* 
 {"c_ln":"MOL-LX101","c_2":"0603","c_3":"012OJRO","c_id":14,"c_t":"08773"}, 
 {"c_ln":"MOL-SG65","c_2":"8253","c_3":"34300B4","c_id":79,"c_t":"16914"}, 
 {"c_ln":"MOL-EM52","c_2":"0928","c_3":"788","c_id":89,"c_t":"01078"}, 
 {"c_ln":"SRB-H263","c_2":"3004","c_3":"631002","c_id":220,"c_t":"02122"}, 
 {"c_ln":"MOL-TM33","c_2":"0588","c_3":"5910133","c_id":463,"c_t":"04613"}, 
 {"c_ln":"MOL-LX10","c_2":"0588","c_3":"AGF000071","c_id":493,"c_t":"22550"}, 
 {"c_ln":"MOL-SA88","c_2":"0005","c_3":"ABT","c_id":496,"c_t":"19949"}, 
 {"c_ln":"SRB-YJ22","c_2":"3003","c_3":"7000026","c_id":717,"c_t":"02464"}, 
 {"c_ln":"TST-SS11","c_2":"0600","c_3":"300","c_id":847,"c_t":"05278"}, 
 {"c_ln":"MOL-WM680","c_2":"4001","c_3":"1240073","c_id":1013,"c_t":"05776"}, 
 {"c_ln":"altKFZ","c_2":"0005","c_3":"5370056","c_id":1206,"c_t":"00050"}, 
 {"c_ln":"MOL-NN487","c_2":"3001","c_3":"6430056","c_id":1211,"c_t":"09096"}, 
 {"c_ln":"SRB-YD73","c_2":"0928","c_3":"788041","c_id":1235,"c_t":"01078"}, 
 {"c_ln":"MOL-NK94","c_2":"8004","c_3":"3110027","c_id":1261,"c_t":"04735"}, 
 {"c_ln":"N-PC318","c_2":"3003","c_3":"AFU000109","c_id":1321,"c_t":"23387"}, 
 {"c_ln":"MOL-IG245","c_2":"8255","c_3":"320006","c_id":1328,"c_t":"09032"}, 
 {"c_ln":"ohne","c_2":"4001","c_3":"827","c_id":1409,"c_t":"02560"}, 
 {"c_ln":"MOL-DH123","c_2":"9644","c_3":"352","c_id":1432,"c_t":"09540"}, 
 {"c_ln":"MOL-LD139","c_2":"0600","c_3":"911","c_id":1441,"c_t":"01870"}, 
 {"c_ln":"ohnexxx","c_2":"8004","c_3":"ABY","c_id":1449,"c_t":"16944"}]}';            
            
            */
               
        break; 

     }
 ?>