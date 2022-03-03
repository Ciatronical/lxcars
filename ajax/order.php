<?php

require_once __DIR__.'/../../inc/stdLib.php'; // for debug
require_once __DIR__.'/../../inc/crmLib.php';
require_once __DIR__.'/../inc/ajax2function.php';


function getOrderlist( $data ){
    require_once __DIR__.'/../inc/lxcLib.php';

    $where = '';
    if( $data['customerName'] != '' )
        $where .= "customer.name = '".$data['customerName']."' AND ";

    if( $data['license_plate'] != '' )
        $where .= "lxc_cars.c_ln = '".$data['license_plate']."' AND ";

    if( $data['dateFrom'] != '' )
        $where .= "oe.transdate >= '".$data['dateFrom']."' AND ";

    if( $data['dateTo'] != '' )
        $where .= "oe.transdate <= '".$data['dateTo']."' AND ";

    if( $data['statusSearch'] != 'alle' && $data['statusSearch'] != 'nicht abgerechnet' )
        $where .= "oe.status = '".$data['statusSearch']."' AND ";

    if( $data['statusSearch'] == 'nicht abgerechnet' )
        $where = " oe.status != 'abgerechnet'  AND ";

    $sql = "SELECT distinct on ( init_ts, internal_order ) * FROM ( ";
    $sql.= "SELECT distinct on ( oe.id, internal_order ) 'true' ::BOOL AS instruction, oe.id,lxc_cars.c_ln, to_char( oe.transdate, 'DD.MM.YYYY') AS transdate, oe.ordnumber, instructions.description, oe.car_status, oe.status, oe.finish_time, customer.name AS owner, oe.c_id AS c_id, oe.customer_id, lxc_cars.c_2 AS c_2, lxc_cars.c_3 AS c_3, oe.car_manuf AS car_manuf, oe.car_type AS car_type, oe.internalorder AS internal_order, oe.itime AS init_ts FROM oe, instructions, parts, lxc_cars, customer WHERE ".$where." instructions.trans_id = oe.id AND parts.id = instructions.parts_id AND lxc_cars.c_id = oe.c_id AND customer.id = oe.customer_id UNION ";
    $sql.= "SELECT distinct on ( oe.id, internal_order ) 'false'::BOOL AS instruction, oe.id,lxc_cars.c_ln, to_char( oe.transdate, 'DD.MM.YYYY') AS transdate, oe.ordnumber, orderitems.description, oe.car_status, oe.status, oe.finish_time, customer.name AS owner, oe.c_id AS c_id, oe.customer_id, lxc_cars.c_2 AS c_2, lxc_cars.c_3 AS c_3, oe.car_manuf AS car_manuf, oe.car_type AS car_type, oe.internalorder AS internal_order, oe.itime AS init_ts FROM oe, orderitems, parts, lxc_cars, customer WHERE ".$where." orderitems.trans_id = oe.id AND parts.id = orderitems.parts_id AND orderitems.position = 1 AND lxc_cars.c_id = oe.c_id AND customer.id = oe.customer_id ORDER BY instruction ASC";
    $sql.= ") AS myTable ORDER BY internal_order ASC, init_ts DESC";
    //writeLog( $sql );
    echo $GLOBALS['dbh']->getALL( $sql, true );
}

function getAutocompleteLicensePlates(){
    echo $GLOBALS['dbh']->getAll( "SELECT distinct c_ln FROM lxc_cars, oe WHERE lxc_cars.c_id = oe.c_id", true );
}

function getAutocompleteCustomer(){
    echo $GLOBALS['dbh']->getAll( "SELECT distinct name FROM customer, oe, lxc_cars WHERE customer.id = oe.customer_id AND oe.c_id = lxc_cars.c_id",true );
}

function autocompletePart( $term ){
    //Index installieren create index idx_orderitems on orderitems ( parts_id );
    $sql = "SELECT description, partnumber, id, partnumber || ' ' || description AS value, part_type, unit,  partnumber || ' ' || description AS label, instruction FROM parts ";
    $sql.= "WHERE ( description ILIKE '%$term%' OR partnumber ILIKE '$term%' ) AND obsolete = FALSE ORDER BY ( SELECT ( SELECT count( qty ) FROM orderitems WHERE parts_id = parts.id ) ) ";
    $sql.= "DESC NULLS LAST LIMIT 20";
    echo $GLOBALS['dbh']->getAll( $sql, true );
}

function getOrder( $id ){
    require_once __DIR__.'/../inc/lxcLib.php';
    $orderData = $GLOBALS['dbh']->getOne( "SELECT oe.amount, oe.netamount, oe.ordnumber AS ordnumber, oe.id AS oe_id,  to_char(oe.transdate, 'DD.MM.YYYY') AS transdate, to_char( oe.reqdate, 'DD.MM.YYYY') AS reqdate, to_char( oe.mtime, 'DD.MM.YYYY') AS mtime,  oe.finish_time AS finish_time, oe.km_stnd, oe.c_id, oe.status AS order_status, oe.customer_id AS customer_id, oe.car_status, customer.name AS customer_name, customer.street AS customer_street, customer.zipcode AS customer_zipcode, customer.city AS customer_city, customer.phone AS customer_phone1, customer.fax AS customer_phone2, customer.email AS customer_email, to_char( customer.itime, 'DD.MM.YYYY') AS customer_itime, customer.notes AS customer_notes, oe.internalorder AS internalorder, lxc_cars.*, to_char( lxc_cars.c_d, 'DD.MM.YYYY') AS c_d_de FROM oe, customer, lxc_cars WHERE oe.id = '".$id."' AND customer.id = oe.customer_id AND oe.c_id = lxc_cars.c_id" );

    $test = lxc2db( '-c '.$orderData['c_2'].' '.substr( $orderData['c_3'], 0, 3 ) );
    //writeLog($test);
    //writeLog(json_encode( array_merge( $orderData, $GLOBALS['dbh']->getALL( "SELECT * FROM lxc_mykba WHERE hsn ='".$orderData['c_2']."' AND tsn ='".substr($orderData['c_3'], 0, 3 )."'" )) ));

    if( json_encode( array_merge( $orderData, lxc2db( '-C '.$orderData['c_2'].' '.substr( $orderData['c_3'], 0, 3 ) )['0'] ) ) == 'null' ){
        if( $test[0][0] == '' ){
            $sql = "SELECT marke AS \"0\", typ AS \"1\", hersteller AS \"2\", bezeichung AS \"3\" FROM lxc_mykba WHERE hsn ='".$orderData['c_2']."' AND tsn ='".substr($orderData['c_3'], 0, 3 )."'";

            $data = array_merge( $orderData, $GLOBALS['dbh']->getALL( $sql ) ) ;

              $orderData = array_merge($orderData, $data[0]);
              //writeLog($orderData);
              if($orderData == "") {
                $orderData = $GLOBALS['dbh']->getOne( "SELECT oe.amount, oe.netamount, oe.ordnumber AS ordnumber, oe.id AS oe_id,  to_char(oe.transdate, 'DD.MM.YYYY') AS transdate, to_char( oe.reqdate, 'DD.MM.YYYY') AS reqdate, to_char( oe.mtime, 'DD.MM.YYYY') AS mtime,  oe.finish_time AS finish_time, oe.km_stnd, oe.c_id, oe.status AS order_status, oe.customer_id AS customer_id, oe.car_status, customer.name AS customer_name, customer.street AS customer_street, customer.zipcode AS customer_zipcode, customer.city AS customer_city, oe.internalorder AS internalorder, lxc_cars.*, to_char( lxc_cars.c_d, 'DD.MM.YYYY') AS c_d_de FROM oe, customer, lxc_cars WHERE oe.id = '".$id."' AND customer.id = oe.customer_id AND oe.c_id = lxc_cars.c_id" );
                //writeLog($orderData);
                echo json_encode($orderData);
              }else
                echo json_encode($orderData);

        }else{

            echo json_encode( array_merge( $orderData, lxc2db( '-c '.$orderData['c_2'].' '.substr( $orderData['c_3'], 0, 3 ) )['0'] ) );
        }
     }
     else{

        echo json_encode( array_merge( $orderData, lxc2db( '-C '.$orderData['c_2'].' '.substr( $orderData['c_3'], 0, 3 ) )['0'] ) );
     }

}

function getPartCount( $parts_id ){
    $sql = "select count(*) from (select * from oe left join orderitems on orderitems.trans_id = oe.id where orderitems.parts_id = ".$parts_id." UNION select * from oe left join instructions on instructions.trans_id = oe.id where instructions.parts_id = ".$parts_id." ) AS count";
    $count = $GLOBALS['dbh']->getOne( $sql, true );
    //$count = $GLOBALS['dbh']->getOne( "select count(*) from invoice  where parts_id = ".$parts_id );
    //writeLog( $count );
    echo $count;
}

function getPositions( $orderID, $json = true ){
    $taxzone_id = 4; //ToDo: rewrite getPosionen(), use array
    $sql  = "SELECT  item_id as id, parts_id, position, instruction, qty, description, unit, sellprice, marge_total, discount, u_id, partnumber, part_type, longdescription, status, rate ";
    $sql .= "FROM ( SELECT parts.instruction, parts.buchungsgruppen_id, instructions.id AS item_id, instructions.parts_id, instructions.qty, instructions.description, instructions.position, instructions.unit, instructions.sellprice, instructions.marge_total, instructions.discount, instructions.u_id, instructions.status, parts.partnumber, parts.part_type, instructions.longdescription FROM instructions INNER JOIN  parts  ON ( parts.id = instructions.parts_id ) WHERE instructions.trans_id = '".$orderID."' ";
    $sql .= "UNION SELECT  parts.instruction, parts.buchungsgruppen_id, orderitems.id AS item_id, orderitems.parts_id, orderitems.qty, orderitems.description, orderitems.position, orderitems.unit, orderitems.sellprice, orderitems.marge_total, orderitems.discount, orderitems.u_id, orderitems.status, parts.partnumber, parts.part_type, orderitems.longdescription FROM orderitems INNER JOIN parts ON ( parts.id = orderitems.parts_id ) WHERE orderitems.trans_id = '".$orderID."' ORDER BY position ) AS mysubquery ";
    $sql .= "JOIN taxzone_charts c ON ( mysubquery.buchungsgruppen_id = c.buchungsgruppen_id )  JOIN taxkeys k ON ( c.income_accno_id = k.chart_id AND k.startdate = ( SELECT max(startdate) FROM taxkeys tk1 WHERE c.income_accno_id = tk1.chart_id AND tk1.startdate::TIMESTAMP <= NOW()  ) ) JOIN tax ON (k.tax_id = tax.id ) WHERE taxzone_id = ".$taxzone_id." GROUP BY item_id, parts_id, position, instruction, qty, description, unit, sellprice, marge_total, discount, u_id, partnumber, part_type, longdescription, status, rate ORDER BY position ASC";
    //writeLog( $sql );
    $rs = $GLOBALS['dbh']->getAll( $sql, $json );
    //writeLog( $rs );
    if( $json ) echo $rs;
    else return $rs; // for printOrder()!!!
}

function insertRow( $data ){
    if( $data['instruction'] == 'true' )
        echo $GLOBALS['dbh']->insert( 'instructions', array( 'position', 'trans_id', 'description', 'sellprice', 'discount', 'marge_total', 'qty', 'ordnumber', 'unit', 'status', 'parts_id' ), array( $data['position'], $data['order_id'], $data['description'], $data['sellprice'], $data['discount'], $data['linetotal'], $data['qty'], $data['ordernumber'], $data['unit'], $data['status'], $data['parts_id'] ), TRUE, 'orderitemsid' );
    else
        echo $GLOBALS['dbh']->insert( 'orderitems', array( 'position', 'trans_id', 'description', 'sellprice', 'discount', 'marge_total', 'qty', 'ordnumber', 'unit', 'status', 'parts_id' ), array( $data['position'], $data['order_id'], $data['description'], $data['sellprice'], $data['discount'], $data['linetotal'], $data['qty'], $data['ordernumber'], $data['unit'], $data['status'], $data['parts_id'] ), TRUE, 'orderitemsid' );
}

function updatePositions( $data ){
    $GLOBALS['dbh']->begin();
    foreach( $data as $key => $value ){
        //writeLog($value);
        $GLOBALS['dbh']->update( $value['pos_instruction'] == 'true' ? 'instructions' : 'orderitems', array( 'position', 'parts_id', 'description', 'unit', 'qty', 'sellprice', 'discount', 'marge_total', 'u_id', 'status', 'longdescription'), array($value['order_nr'], $value['parts_id'], $value['pos_description'], $value['pos_unit'], $value['pos_qty'], $value['pos_price'], $value['pos_discount'], $value['pos_total'], $value['pos_emp'], $value['pos_status'], $value['longdescription']), 'id = '.$value['pos_id'] );
    }
    echo $GLOBALS['dbh']->commit();
}

function delPosition( $data ){
    echo $GLOBALS['dbh']->query( "DELETE FROM ".( $data['instruction'] == 'true' ? 'instructions' : 'orderitems' )." WHERE id = ".$data['id'] );
}

//function to get metadata (= the same for ALL orders) to reduce AJAX requests on page load
function getMetadata(){
    $sql = "SELECT json_agg (outputJSON) FROM ("
           ."SELECT row_to_json(x) as outputJSON FROM (SELECT json_agg(i) as units FROM (SELECT name,type FROM units) i) x UNION all "
           ."SELECT row_to_json(x) FROM (SELECT json_agg(i) as taxzones FROM (SELECT id,description FROM tax_zones ORDER BY sortkey ASC) i) x UNION all "
           ."SELECT row_to_json(x) FROM (SELECT json_agg(i) as accountingGroups FROM (SELECT id, description FROM buchungsgruppen ORDER BY id = "
             ."( SELECT buchungsgruppen_id FROM ( SELECT buchungsgruppen_id, count( buchungsgruppen_id ) "
             ."AS id FROM parts GROUP BY 1 ORDER BY id DESC LIMIT 1 ) AS nothing ) DESC) i) x UNION all  "
           ."SELECT row_to_json(x) FROM (SELECT json_agg(i) as customerHourlyRate FROM (SELECT customer_Hourly_Rate FROM defaults) i) x) output";
    //writeLog( $sql );
    echo $GLOBALS['dbh']->getOne( $sql )['json_agg'];
}

function getUsersFromGroup( $data ){
    echo json_encode( ERPUsersfromGroup( $data ) );
}

function newPart( $data ){
  //writeLog('newPart');
  echo $GLOBALS['dbh']->insert( 'parts', array( 'partnumber', 'description', 'unit', 'listprice', 'sellprice', 'buchungsgruppen_id', 'instruction','part_type'), array( $data['partnumber'], $data['description'], $data['unit'], $data['listprice'], $data['sellprice'], $data['buchungsgruppen_id'], $data['instruction'],$data['part_type']), TRUE, 'id' );

}

function updatePart( $data ) {
  //writeLog('updatePart');
  //writeLog($data['partID']);
  echo $GLOBALS['dbh']->update( 'parts', array( 'partnumber', 'description', 'unit', 'listprice', 'sellprice', 'buchungsgruppen_id', 'instruction','part_type'), array( $data['partnumber'], $data['description'], $data['unit'], $data['listprice'], $data['sellprice'], $data['buchungsgruppen_id'], $data['instruction'],$data['part_type']), 'id = '.$data['partID']);

}

function getPartJSON( $parts_id ){
    //JOIN with tax information:
    $taxzone_id = 4;
    $sql = "SELECT parts.*, tax.rate FROM parts "
        ."INNER JOIN taxzone_charts ON parts.buchungsgruppen_id = taxzone_charts.buchungsgruppen_id "
        ."INNER JOIN taxkeys ON taxzone_charts.income_accno_id = taxkeys.chart_id "
        ."INNER JOIN tax ON taxkeys.tax_id = tax.id "
        ."WHERE parts.id = ".$parts_id." AND parts.obsolete = false AND taxzone_charts.taxzone_id = ".$taxzone_id." "
        ."GROUP BY parts.id, tax.rate, taxkeys.startdate ORDER BY taxkeys.startdate DESC LIMIT 1";
    //writeLog( $sql );
    echo $GLOBALS['dbh']->getALL( $sql, TRUE );
}

function getArticleNumber( $unit ){
  $type = $GLOBALS['dbh']->getOne( "SELECT type FROM units WHERE name='".$unit."'" );
     //writeLog( $type );
     //print_r($type);
    if( $type[type] == "dimension" )
        $rs = $GLOBALS['dbh']->getOne( "SELECT id AS defaults_id, articlenumber::INT + 1 AS newnumber, 0 AS service FROM defaults");
    elseif( $type[type] == "service") //or instruction??
        $rs = $GLOBALS['dbh']->getOne( "SELECT id AS defaults_id, servicenumber::INT + 1 AS newnumber, customer_hourly_rate, 1 AS service FROM defaults");

    //increase partnumber if partnumber exists
    while( $GLOBALS['dbh']->getOne( "SELECT partnumber FROM parts WHERE partnumber = '".$rs['newnumber']."'" )['partnumber'] ) $rs['newnumber']++;
    //writeLog( $rs );
    echo  json_encode( $rs );//JS-friendly JSON, ToDo: get JSON from db
}

function saveLastArticleNumber( $data ){
    if( $data['service'] ) echo $GLOBALS['dbh']->update( 'defaults', array( 'servicenumber' ), array( $data['artNr'] ), 'id = '.$data['id'] );
    else echo $GLOBALS['dbh']->update( 'defaults', array( 'articlenumber' ), array( $data['artNr'] ), 'id = '.$data['id'] );
}

function updateOrder( $data) {
    echo $GLOBALS['dbh']->update( 'oe', array( 'km_stnd', 'status', 'netamount', 'amount', 'car_status', 'finish_time', 'internalorder' ), array( $data[0]['km_stnd'], $data[0]['status'], $data[0]['netamount'], $data[0]['amount'], $data[0]['car_status'], $data[0]['finish_time'], $data[0]['internalorder'] ), 'id = '.$data[0]['id'] );
}

function getCar( $c_id ){
    echo $GLOBALS['dbh']->getOne( "SELECT lxc_cars.c_ln AS amtl_kennz, lxc_cars.c_id AS car_id, customer.id AS customer_id, customer.name AS customer_name, customer.street AS customer_street, customer.zipcode AS customer_zipcode, customer.city AS customer_city, customer.taxzone_id, customer.currency_id, defaults.sonumber AS last_order_nr, defaults.id AS defaults_id FROM lxc_cars, customer, defaults WHERE lxc_cars.c_id = '".$c_id."' AND customer.id = lxc_cars.c_ow", true);
}

function removeOrder( $data ){
    echo $GLOBALS['dbh']->query( "DELETE from oe WHERE oe.id = ".$data['orderID'] );
}

function newOrder( $data ){
    require_once __DIR__.'/../inc/lxcLib.php';
    $c_keys = $GLOBALS['dbh']->getAll("SELECT c_2 AS c_hsn, c_3 AS c_tsn FROM lxc_cars WHERE c_id = ".$data['car_id']);
    $carData = lxc2db( '-C '.$c_keys[0]['c_hsn'].' '.substr( $c_keys[0]['c_tsn'], 0, 3 ) );
    if( $carData == -1 )
        $carData = lxc2db( '-c '.$c_keys[0]['c_hsn'].' '.substr( $c_keys[0]['c_tsn'], 0, 3 ) );
    if( $carData == -1 || $carData[0][0] == "" || $carData =='null'){
        $car = $GLOBALS['dbh']->getALL( "SELECT id , hersteller , typ, bezeichung FROM lxc_mykba WHERE hsn ='".$c_keys[0]['c_hsn']."' AND tsn ='".substr($c_keys[0]['c_tsn'], 0, 3 )."'" );
        $carData[0][1] = $car[0]['hersteller'];
        $carData[0][2] = $car[0]['typ'];
        $carData[0][3] = $car[0]['bezeichung'];
    }

    $id = $GLOBALS['dbh']->getOne( "WITH tmp AS ( UPDATE defaults SET sonumber = sonumber::INT + 1 RETURNING sonumber) INSERT INTO oe ( ordnumber, customer_id, employee_id, taxzone_id, currency_id, c_id) SELECT ( SELECT sonumber FROM tmp), ".$data['owner_id'].", ".$_SESSION['id'].",  customer.taxzone_id, customer.currency_id, ".$data['car_id']." FROM customer WHERE customer.id = ".$data['owner_id']." RETURNING id ")['id'];

    $GLOBALS['dbh']->update( 'oe', array( 'car_manuf', 'car_type' ), array( $carData[0][1], $carData[0][2]." ".$carData[0][3] ), 'id = '.$id );
    echo $id;
}

function printOrder( $data ){

    require 'fpdf.php';
    require_once __DIR__.'/../inc/lxcLib.php';
    include_once __DIR__.'/../inc/config.php';

    $sql  = "SELECT oe.ordnumber, oe.transdate, oe.finish_time, oe.km_stnd, oe.employee_id, printed, ";
    $sql .= "customer.name, customer.street, customer.zipcode, customer.city, customer.phone, customer.fax, customer.notes, ";
    $sql .= "lxc_cars.c_ln, lxc_cars.c_2, lxc_cars.c_3, lxc_cars.c_mkb, lxc_cars.c_t, lxc_cars.c_fin, lxc_cars.c_st_l, lxc_cars.c_wt_l, ";
    $sql .= "lxc_cars.c_text, lxc_cars.c_color, lxc_cars.c_zrk, lxc_cars.c_zrd, lxc_cars.c_em, lxc_cars.c_bf, lxc_cars.c_wd, lxc_cars.c_d, lxc_cars.c_hu, employee.name AS employee_name, lxc_flex.flxgr ";
    $sql .= "FROM oe join customer on oe.customer_id = customer.id join lxc_cars on oe.c_id = lxc_cars.c_id join employee on oe.employee_id = employee.id ";
    $sql .= "left join lxc_flex on ( lxc_cars.c_2 = lxc_flex.hsn AND lxc_flex.tsn = substring( lxc_cars.c_3 from 1 for 3 ) ) WHERE oe.id = ".$data['orderId'];

    $orderData = $GLOBALS['dbh']->getOne( $sql );

    //Add Cardata from lxc2db
    //$orderData = array_merge( $orderData, lxc2db( '-C '.$orderData['c_2'].' '.substr( $orderData['c_3'], 0, 3 ) )['0'] );

     $test = lxc2db( '-c '.$orderData['c_2'].' '.substr( $orderData['c_3'], 0, 3 ) );
    //writeLog($test);
    //writeLog(json_encode( array_merge( $orderData, $GLOBALS['dbh']->getALL( "SELECT * FROM lxc_mykba WHERE hsn ='".$orderData['c_2']."' AND tsn ='".substr($orderData['c_3'], 0, 3 )."'" )) ));

    if( json_encode( array_merge( $orderData, lxc2db( '-C '.$orderData['c_2'].' '.substr( $orderData['c_3'], 0, 3 ) )['0'] ) ) == 'null' ){
        if( $test[0][0] == '' )
            $orderData = array_merge( $orderData, $GLOBALS['dbh']->getALL( "SELECT * FROM lxc_mykba WHERE hsn ='".$orderData['c_2']."' AND tsn ='".substr($orderData['c_3'], 0, 3 )."'" ));
        else
            $orderData = array_merge( $orderData, lxc2db( '-c '.$orderData['c_2'].' '.substr( $orderData['c_3'], 0, 3 ) )['0'] ) ;

     }
     else
        $orderData = array_merge( $orderData, lxc2db( '-C '.$orderData['c_2'].' '.substr( $orderData['c_3'], 0, 3 ) )['0'] );





    /*
    if( json_encode( array_merge( $orderData, lxc2db( '-C '.$orderData['c_2'].' '.substr( $orderData['c_3'], 0, 3 ) )['0'] ) )=='null' )
      $orderData = array_merge( $orderData, lxc2db( '-c '.$orderData['c_2'].' '.substr( $orderData['c_3'], 0, 3 ) )['0'] );
    else
      $orderData = array_merge( $orderData, lxc2db( '-C '.$orderData['c_2'].' '.substr( $orderData['c_3'], 0, 3 ) )['0'] );
  */

    //writeLog( $orderData );

    define( 'FPDF_FONTPATH', '../font/');
    define( 'x', 0 );
    define( 'y', 1 );

    $pdf = new FPDF( 'P','mm','A4' );
    $pdf->AddPage();

    $fontsize = 11;
    $textPosX_right = 120;
    $textPosY = 25;
    $textPosX_left = 12;

    if( $orderData['printed'] ){
        $pdf->SetFont( 'Helvetica', 'B', 10 );
        $pdf->SetTextColor( 255, 0, 0 );
        $pdf->Text( '10','7','Kopie' );
        $pdf->SetTextColor( 0, 0, 0 );
    }

    $pdf->SetFont( 'Helvetica', 'B', 14 ); //('font_family','font_weight','font_size')
    //if( strlen( $orderData['2'] ) < 26 ) {
    if($orderData['1'] != '')
    $pdf->Text( '10','12','Autoprofis Rep.-Auftrag '.' '.$orderData['1'].' '.$orderData['2'].' '.$orderData['3'] );
    else
    $pdf->Text( '10','12','Autoprofis Rep.-Auftrag '.' '.$orderData[0]['hersteller'].' '.$orderData[0]['typ'].' '.$orderData[0]['bezeichung'] );
    //else{
    //$pdf->Text( '10','12','Autoprofis Rep.-Auftrag '.' '.$orderData['1'].' '.$orderData['2'] );
    //$pdf->Text( '10','22',$orderData['3']);
    //}
    $pdf->Text( '10','18', $orderData['c_ln'] );
    $pdf->SetFont( 'Helvetica', '', 14 );

    //fix values
    $pdf->SetFont( 'Helvetica', 'B', $fontsize ) ;
    $pdf->Text( $textPosX_left, $textPosY,'Kunde:' );
    $pdf->Text( $textPosX_left, $textPosY + 5, utf8_decode( 'Straße:' ) );
    $pdf->Text( $textPosX_left, $textPosY + 10, 'Ort:' );
    $pdf->Text( $textPosX_left, $textPosY + 15, 'Tele.:' );
    $pdf->Text( $textPosX_left, $textPosY + 20, 'Tele2:' );
    $pdf->Text( $textPosX_left, $textPosY + 25, 'Bearb.:' );

    $pdf->SetFont( 'Helvetica', '', $fontsize );
    $pdf->Text( $textPosX_left + 20, $textPosY, utf8_decode( substr( $orderData['name'], 0, 34 ) ) );
    $pdf->Text( $textPosX_left + 20, $textPosY + 5, utf8_decode( $orderData['street'] ) );
    $pdf->Text( $textPosX_left + 20, $textPosY + 10, $orderData['zipcode'].' '.utf8_decode( $orderData['city'] ) );
    $pdf->Text( $textPosX_left + 20, $textPosY + 15, $orderData['phone'] );
    $pdf->Text( $textPosX_left + 20, $textPosY + 20, $orderData['fax'] );
    $pdf->Text( $textPosX_left + 20, $textPosY + 25, $orderData['employee_name'] );

    $pdf->SetFont( 'Helvetica', 'B', $fontsize );
    $pdf->Text( $textPosX_right, $textPosY, 'KBA:' );
    $pdf->Text( $textPosX_right, $textPosY + 5, 'Baujahr:' );
    $pdf->Text( $textPosX_right, $textPosY + 10,'HU/AU:' );
    $pdf->Text( $textPosX_right, $textPosY + 15, 'FIN:' );
    $pdf->Text( $textPosX_right, $textPosY + 20, 'MK:' );
    $pdf->Text( $textPosX_right, $textPosY + 25, 'KM:' );

    $pdf->SetFont( 'Helvetica', '', $fontsize );
    $pdf->Text( $textPosX_right + 20, $textPosY, $orderData['c_2'].' '.$orderData['c_3'] );
    $pdf->Text( $textPosX_right + 20, $textPosY + 5, db2date( $orderData['c_d'] ) );
    $pdf->Text( $textPosX_right + 20, $textPosY + 10, db2date( $orderData['c_hu'] ) );
    $pdf->Text( $textPosX_right + 20, $textPosY + 15, $orderData['c_fin'] );
    $pdf->Text( $textPosX_right + 20, $textPosY + 20, $orderData['c_mkb'] );
    $pdf->Text( $textPosX_right + 20, $textPosY + 25, $orderData['km_stnd'] );




    $pdf->Text( $textPosX_right, $textPosY + 45, 'Flexgr.:' );
    $pdf->Text( $textPosX_right, $textPosY + 50, 'Color.:' );

    $pdf->Text( $textPosX_right, $textPosY + 35, utf8_decode( 'Lo Sommerräder.:' ) );
    $pdf->Text( $textPosX_right, $textPosY + 40, utf8_decode( 'Lo Winterräder.:' ) );

    $pdf->Text( $textPosX_left, $textPosY + 35, utf8_decode( 'nächst. ZR-Wechsel KM:' ) );
    $pdf->Text( $textPosX_left, $textPosY + 40, utf8_decode( 'nächst. ZR-Wechsel:' ) );
    $pdf->Text( $textPosX_left, $textPosY + 45, utf8_decode( 'nächst. Bremsfl.:' ) );
    $pdf->Text( $textPosX_left, $textPosY + 50, utf8_decode( 'nächst. WD:' ) );

    $pdf->SetLineWidth( 0.2 );

    $pdf->SetFont( 'Helvetica', '', $fontsize );

    $pdf->Text( $textPosX_right + 45, $textPosY + 45, utf8_decode( $orderData['flxgr'] ) );
    $pdf->Text( $textPosX_right + 45, $textPosY + 50, utf8_decode( $orderData['c_color'] ) );

    //left side under line one
    $lsulo = 50;
    $pdf->Text( $textPosX_left + $lsulo, $textPosY + 35, $orderData['c_zrk'] );
    $pdf->Text( $textPosX_left + $lsulo, $textPosY + 40, utf8_decode( $orderData['c_zrd'] ) );
    $pdf->Text( $textPosX_left + $lsulo, $textPosY + 45, utf8_decode( $orderData['c_bf'] ) );
    $pdf->Text( $textPosX_left + $lsulo, $textPosY + 50, utf8_decode( $orderData['c_wd'] ) );


    $pdf->Text( $textPosX_right + 45, $textPosY + 35, utf8_decode( $orderData['c_st_l'] ) );
    $pdf->Text( $textPosX_right + 45, $textPosY + 40, utf8_decode( $orderData['c_wt_l'] ) );

    //Finish Time
    if( strpos( $orderData['finish_time'], 'wartet' ) ) $pdf->SetTextColor( 255, 0, 0 );
    $pdf->SetFont( 'Helvetica', 'B', '12' );
    $finishTimeHeight = 85;
    $pdf->Text( $textPosX_left, $finishTimeHeight, 'Fertigstellung:' );
    $pdf->SetFont( 'Helvetica', 'B', '12' );
    $pdf->Text( $textPosX_right, $finishTimeHeight, utf8_decode( $orderData['finish_time'] ) );
    $pdf->SetTextColor( 0, 0, 0 );


    $pdf->SetFont( 'Helvetica', '', '10' );
    $pos_todo[x] = 20; $pos_todo[y] = 110;

    //get postions as assoz. array
    $positions = getPositions( $data['orderId'], false );

    //Instructions and positions
    $pdf->SetFont( 'Helvetica', '', '8' );
    $height = '85';

    $pdf->SetLineWidth(0.4);

    //draw first Line x1, y1, x2, y2
    $lineHeight = 54;
    $lineWidth  = 180;
    $pdf->Line( $textPosX_left, $lineHeight, $textPosX_left + $lineWidth , $lineHeight );
    //draw second Line x1, y1, x2, y2
    $lineHeight = 78;
    $pdf->Line( $textPosX_left, $lineHeight, $textPosX_left + $lineWidth , $lineHeight );

    foreach( $positions as $index => $element ){
        if( $element['instruction'] ){
            //writeLog( $element );
            $height = $height + 8;
            //$pdf->SetTextColor( 255, 0, 0 );
            $pdf->SetLineWidth( 0.1 );
            $pdf->Line( 10, $height + 1.6 , 190, $height + 1.6 );
            $pdf->SetFont('Helvetica','B','12');
            //$pdf->SetTextColor( 100, 100, 100 );
            $pdf->Text( '12',$height, utf8_decode( $element['description'] ) );
            //Longdescriptions
            if( trim( $element['longdescription'] ) != '' ){
                $height = $height + 2;
                $pdf->SetFont( 'Helvetica', '', '10' );
                //$pdf->SetTextColor( 0, 0, 0 );
                //writeLog( strlen( $element['longdescription'] ) );//  intdiv
                $words = explode( ' ', $element['longdescription'] );
                $i = 0;
                $lineArray = array();
                foreach( $words as $value ){
                    if( strlen( $lineArray[$i].$value.' ' ) > 113 ) $i++;
                    $lineArray[$i] .= $value.' ';
                }
                //writeLog( $lineArray );
                foreach( $lineArray as $line ){
                    $height = $height + 4;
                    $pdf->Text( '16', $height, utf8_decode( $line ) );
                }
            }
        }
    }

    //writeLog( $height );
    while( $height < 250 ){
        $height = $height + 10;
        $pdf->Line( 10, $height + 1.6, 190, $height + 1.6 );
    }


    //Footer
    //$pdf->SetTextColor( 0, 0, 0 );
    $pdf->SetFont( 'Helvetica', '', '10' );
    $pdf->Text( '22', '270', 'Datum:' );
    $pdf->Text( '45', '270', date( 'd.m.Y' ) );
    $pdf->Text( '105','270','Kundenunterschrift: __________________' );
   // $pdf->SetTextColor( 255, 0, 0 );
    $pdf->Text( '22', '280', utf8_decode( 'Endkontrolle UND Probefahrt durchgeführt von: __________________' ) );
    //$pdf->SetTextColor( 0, 0, 0 );
    $pdf->SetFont( 'Helvetica', '', '08' );
    $pdf->Text( '75', '290', 'Powered by lxcars.de - Freie Kfz-Werkstatt Software' );


    //Backside
    //EPSON Printer print must rotate secound page
    $rotate = 0; //Degree --Only for Epson
    $pdf->AddPage( 'P', 'A4', $rotate );
    $pdf->SetFont( 'Helvetica', 'B', '16' );
    $pdf->Text( 10, 20, utf8_decode( 'Verbaute Ersazteile' ) );
    $height = 30;
    $pdf->SetFont( 'Helvetica', '', '10' );
    $pdf->line( 10, $height - 4.4,  190, $height - 4.4 );
    $totalLines = 22;
    foreach( $positions as $index => $element ){
        //writeLog( $element);
        if( ( $element['part_type']  == 'part' ) && !$element['instruction'] ){
            $totalLines--;
            //writeLog( 'part' );
            $pdf->line( 10, $height + 1.6,  190, $height + 1.6 );
            $pdf->Text( '12', $height, utf8_decode( $element['qty']." ".$element['unit'] ) );
            $pdf->Text( '26', $height, utf8_decode( $element['description'] ) );
            $height = $height + 6;
        }
    }
    while( $totalLines-- ){
        $pdf->line( 10, $height + 1.6, 190, $height + 1.6 );
        $height = $height + 6;
    }


    $pdf->SetFont( 'Helvetica', 'B', '16' );
    $pdf->Text( 10, $height + 10, utf8_decode( 'Ausgeführte Arbeiten' ) );
    $height = $height + 20;
    $pdf->SetFont( 'Helvetica', '', '10' );
    $totalLines = 16;
    $pdf->line( 10, $height - 4.4,  190, $height - 4.4 );
    foreach( $positions as $index => $element ){
        writeLog( $element);
        if( ( $element['part_type']  == 'service' ) && !$element['instruction'] ){
            $totalLines--;
            $pdf->line( 10, $height + 1.6,  190, $height + 1.6 );
            $pdf->Text( '12', $height, utf8_decode( $element['qty']." ".$element['unit'] ) );
            $pdf->Text( '26', $height, utf8_decode( $element['description'] ) );
            $height = $height + 6;
        }
    }
    while( $totalLines-- ){
        $pdf->line( 10, $height + 1.6,  190, $height + 1.6 );
        $height = $height + 6;
    }

    $pdf->Text( '10','290', utf8_decode( 'Ich habe sämtliche Ersazteile und ausgeführte Arbeiten i.d. obigen Liste notiert. Unterschrift: __________________' ) );
    $pdf->OutPut( __DIR__.'/../out.pdf', 'F' );


    if( $data['print'] == 'printOrder1' ){
      //system('lpr -P test '.__DIR__.'/../out.pdf' );
      system('lpr -P Buro1 '.__DIR__.'/../out.pdf' );
      if( !$orderData['printed'] )
        $GLOBALS['dbh']->update( 'oe', array( 'printed' ), array( 'TRUE' ), 'id = '.$data['orderId'] );
    }
    if( $data['print'] == 'printOrder2' ){
      //system('lpr -P test '.__DIR__.'/../out.pdf' );
      system('lpr -P HP_LASER '.__DIR__.'/../out.pdf' );
      if( !$orderData['printed'] )
        $GLOBALS['dbh']->update( 'oe', array( 'printed' ), array( 'TRUE' ), 'id = '.$data['orderId'] );
    }

    echo 1;
}

function setHuAuDate( $c_id ){
    $today   = date( 'Y-m-d' );
    $newdate = date( 'Y-m-01', strtotime( $today.' + 2 year ' ) );
    return $GLOBALS['dbh']->update( 'lxc_cars', array( 'c_hu' ), array( $newdate ), 'c_id = '.$c_id );
}

function getQtyNewPart( $description ){
    $rs = intval( $GLOBALS['dbh']->getOne( "SELECT qty, count( qty ) AS ct FROM orderitems WHERE description ILIKE '%$description%' GROUP BY 1 ORDER BY ct DESC LIMIT 1" )['qty'] );
    echo $rs? $rs : 1;
}

function getQty( $description ){
    //Method 1: most popular
    $sql ="SELECT qty, count( qty ) AS ct FROM orderitems WHERE description = TRIM( '$description' ) GROUP BY 1 ORDER BY ct DESC LIMIT 1";
    //writeLog( $sql );
    $rs =  $GLOBALS['dbh']->getOne( $sql )['qty'];

    //Method 2: last modification
    //echo $GLOBALS['dbh']->getOne( "SELECT qty FROM orderitems WHERE description = '$description'  ORDER BY mtime DESC LIMIT 1" )['qty'];
    //writeLog( $rs );
    //writeLog( "SELECT qty, count( qty ) AS ct FROM orderitems WHERE description = '$description' GROUP BY 1 ORDER BY ct DESC LIMIT 1" );
    echo $rs? $rs : 1;
}

?>