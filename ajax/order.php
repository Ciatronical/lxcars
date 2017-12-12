<?php

//require_once __DIR__.'/../../inc/stdLib.php'; // for debug
require_once __DIR__.'/../../inc/crmLib.php';
require_once __DIR__.'/../inc/ajax2function.php';


function autocompletePart( $term ){
    echo $GLOBALS['dbh']->getAll( "SELECT description, partnumber, id, partnumber || ' ' || description AS value, part_type, unit,  partnumber || ' ' || description AS label, instruction FROM parts WHERE ( description ILIKE '%$term%' OR partnumber ILIKE '$term%' ) AND obsolete = FALSE LIMIT 20", true );
}

function getOrder( $id ){
    require_once __DIR__.'/../inc/lxcLib.php';
    $orderData = $GLOBALS['dbh']->getOne( "SELECT oe.amount, oe.netamount, oe.ordnumber AS ordnumber, oe.id AS oe_id,  to_char(oe.transdate, 'DD.MM.YYYY') AS transdate, to_char( oe.reqdate, 'DD.MM.YYYY') AS reqdate,  oe.finish_time AS finish_time, oe.km_stnd, oe.c_id, oe.status AS order_status, oe.customer_id AS customer_id, oe.car_status, customer.name AS customer_name, lxc_cars.* FROM oe, customer, lxc_cars WHERE oe.id = '".$id."' AND customer.id = oe.customer_id AND oe.c_id = lxc_cars.c_id" );
    echo json_encode( array_merge( $orderData, lxc2db( '-C '.$orderData['c_2'].' '.substr( $orderData['c_3'], 0, 3 ) )['0'] ) );
}


function getPositions( $orderID, $json = true ){
    $sql = "SELECT 'true'::BOOL AS instruction,parts.instruction, instructions.id, instructions.parts_id, instructions.qty, instructions.description, instructions.position, instructions.unit, instructions.sellprice, instructions.marge_total, instructions.discount, instructions.u_id, instructions.status, parts.partnumber, parts.part_type FROM instructions, parts WHERE instructions.trans_id = '".$orderID."'AND parts.id = instructions.parts_id UNION ";
    $sql.= "SELECT 'false'::BOOL AS instruction,parts.instruction, orderitems.id, orderitems.parts_id, orderitems.qty, orderitems.description, orderitems.position, orderitems.unit, orderitems.sellprice, orderitems.marge_total, orderitems.discount, orderitems.u_id, orderitems.status, parts.partnumber, parts.part_type FROM orderitems, parts WHERE orderitems.trans_id = '".$orderID."' AND parts.id = orderitems.parts_id ORDER BY position DESC";
    $rs = $GLOBALS['dbh']->getAll( $sql, $json );
    if( $json ) echo $rs;
    else return $rs;
}

function insertRow( $data ){
    if( $data['instruction'] == 'true' )
        echo $GLOBALS['dbh']->insert( 'instructions', array( 'position', 'trans_id', 'description', 'sellprice', 'discount', 'marge_total','qty','ordnumber','unit', 'status', 'parts_id'), array( $data['position'], $data['order_id'], $data['description'], $data['sellprice'], $data['discount'], $data['linetotal'],$data['qty'],$data['ordernumber'],$data['unit'], $data['status'], $data['parts_id']), 'id', 'orderitemsid');
    else
        echo $GLOBALS['dbh']->insert( 'orderitems', array( 'position', 'trans_id', 'description', 'sellprice', 'discount', 'marge_total','qty','ordnumber','unit', 'status', 'parts_id'), array( $data['position'], $data['order_id'], $data['description'], $data['sellprice'], $data['discount'], $data['linetotal'],$data['qty'],$data['ordernumber'],$data['unit'], $data['status'], $data['parts_id']), 'id', 'orderitemsid');
}

function updatePositions( $data) {
    $GLOBALS['dbh']->begin();
    foreach( $data as $key => $value ){
        $GLOBALS['dbh']->update( $value['pos_instruction'] == 'true' ? 'instructions' : 'orderitems', array( 'position', 'parts_id', 'description', 'unit', 'qty', 'sellprice', 'discount', 'marge_total', 'u_id', 'status'), array($value['order_nr'], $value['parts_id'], $value['pos_description'], $value['pos_unit'], $value['pos_qty'], $value['pos_price'], $value['pos_discount'], $value['pos_total'], $value['pos_emp'], $value['pos_status']), 'id = '.$value['pos_id'] );

    }
    echo $GLOBALS['dbh']->commit();
}

function delPosition( $data ){
    echo $GLOBALS['dbh']->query( "DELETE FROM ".( $data['instruction'] == 'true' ? 'instructions' : 'orderitems' )." WHERE id = ".$data['id'] );
}

function getUsersFromGroup( $data ){
    echo json_encode( ERPUsersfromGroup( $data ) );
}

function getUnits(){
    echo $GLOBALS['dbh']->getAll( "SELECT name,type FROM units", true );
}

function getAccountingGroups(){
    $rs = $GLOBALS['dbh']->getAll( "SELECT id, description FROM buchungsgruppen", true );
    echo $rs;
}

function newPart( $data ){
      echo $GLOBALS['dbh']->insert( 'parts', array( 'partnumber', 'description', 'unit', 'listprice', 'sellprice', 'buchungsgruppen_id', 'instruction','part_type'), array( $data['partnumber'], $data['description'], $data['unit'], $data['listprice'], $data['sellprice'], $data['buchungsgruppen_id'], $data['instruction'],$data['part_type']), TRUE, 'id' );
}

function getPartJSON( $parts_id ){
    echo $GLOBALS['dbh']->getALL( "SELECT * FROM parts WHERE id = ".$parts_id." AND obsolete = false", TRUE );
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
    echo $GLOBALS['dbh']->update( 'oe', array( 'km_stnd', 'status', 'netamount', 'amount', 'car_status', 'finish_time' ), array( $data[0]['km_stnd'], $data[0]['status'], $data[0]['netamount'], $data[0]['amount'], $data[0]['car_status'], $data[0]['finish_time'] ), 'id = '.$data[0]['id'] );
}

function getCar( $c_id ){
   echo $GLOBALS['dbh']->getOne( "SELECT lxc_cars.c_ln AS amtl_kennz, lxc_cars.c_id AS car_id, customer.id AS customer_id, customer.name AS customer_name, customer.taxzone_id, customer.currency_id, defaults.sonumber AS last_order_nr, defaults.id AS defaults_id FROM lxc_cars, customer, defaults WHERE lxc_cars.c_id = '".$c_id."' AND customer.id = lxc_cars.c_ow", true);
}

function removeOrder( $orderID ){
    echo $Globals['dbh']->getOne( "DELETE from oe WHERE oe.id='".$orderID."'" );
}

function newOrder( $data ){
    //increase last ordernumber, insert data, returning order-id
    //writeLog( $data );
    //UPDATE defaults SET sonumber = sonumber::INT + 1 RETURNING sonumber //increase last ordernumber and return them
    echo $GLOBALS['dbh']->getOne( "WITH tmp AS ( UPDATE defaults SET sonumber = sonumber::INT + 1 RETURNING sonumber) INSERT INTO oe ( ordnumber, customer_id, employee_id, taxzone_id, currency_id, c_id )  SELECT ( SELECT sonumber FROM tmp), ".$data['owner_id'].", ".$_SESSION['id'].",  customer.taxzone_id, customer.currency_id, ".$data['car_id']." FROM customer WHERE customer.id = ".$data['owner_id']." RETURNING id ")['id'];
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
    $orderData = array_merge( $orderData, lxc2db( '-C '.$orderData['c_2'].' '.substr( $orderData['c_3'], 0, 3 ) )['0'] );

    //writeLog( $orderData );

    define( 'FPDF_FONTPATH', '../font/');
    define( 'x', 0 );
    define( 'y', 1 );

    $pdf = new FPDF( 'P','mm','A4' );
    $pdf->AddPage();

    $fontsize = '9';
    $textPosX = '112';
    $textPosY = '20';
    $textPosX_2 = '12';

    if( $orderData['printed'] ){
        $pdf->SetFont( 'Helvetica', 'B', '10' );
        $pdf->SetTextColor( 255, 0, 0 );
        $pdf->Text( '10','7','Kopie' );
        $pdf->SetTextColor( 0, 0, 0 );
    }

    $pdf->SetFont( 'Helvetica', 'B', '14' ); //('font_family','font_weight','font_size')
    $pdf->Text( '10','12','Autoprofis Reparaturauftrag '.' '.$orderData['1'].' '.$orderData['2'].' '.$orderData['3'].' '.$orderData['c_ln'] );
    $pdf->SetFont( 'Helvetica', '', '14' );

    //fix values
    $pdf->SetFont( 'Helvetica', 'B', $fontsize ) ;
    $pdf->Text( $textPosX_2, $textPosY,'Kunde:' );
    $pdf->Text( $textPosX_2, $textPosY + 5, utf8_decode( 'Straße' ).':' );
    $pdf->Text( $textPosX_2, $textPosY + 10, 'Ort:' );
    $pdf->Text( $textPosX_2, $textPosY + 15, 'Tele.:' );
    $pdf->Text( $textPosX_2, $textPosY + 20, 'Mobil:' );
    $pdf->Text( $textPosX_2, $textPosY + 25, 'Bearb.:' );

    $pdf->Text( $textPosX_2, $textPosY + 35, 'Farbe:' );
    $pdf->Text( $textPosX_2, $textPosY + 40, 'Hubr.:' );
    $pdf->Text( $textPosX_2, $textPosY + 45, 'Zr. Km:' );

    $pdf->Text( $textPosX, $textPosY, 'KBA:' );
    $pdf->Text( $textPosX, $textPosY + 5, 'Baujahr:' );
    $pdf->Text( $textPosX, $textPosY + 10,' HU/AU:' );
    $pdf->Text( $textPosX, $textPosY + 15, 'FIN:' );
    $pdf->Text( $textPosX, $textPosY + 20, 'MK:' );
    $pdf->Text( $textPosX, $textPosY + 25, 'KM:' );

    $pdf->Text( $textPosX, $textPosY + 35, 'Abgas.:' );
    $pdf->Text( $textPosX, $textPosY + 40, 'Peff:' );
    $pdf->Text( $textPosX, $textPosY + 45, 'Flexgr.:' );

    $pdf->Text( $textPosX, $textPosY + 55, utf8_decode( 'Lo Sommerräder.:' ) );
    $pdf->Text( $textPosX, $textPosY + 60, utf8_decode( 'Lo Winterräder.:' ) );

    $pdf->Text( $textPosX_2, $textPosY + 55, utf8_decode( 'nächst. ZR-Wechsel' ).':' );
    $pdf->Text( $textPosX_2, $textPosY + 65, utf8_decode( 'nächst. Bremsfl.' ).':' );
    $pdf->Text( $textPosX_2, $textPosY + 60, utf8_decode( 'nächst. WD' ).':' );

    $pdf->SetLineWidth( 0.2 );

    $pdf->SetFont( 'Helvetica', '', $fontsize );

    $pdf->Text( $textPosX_2 + 20, $textPosY, utf8_decode( substr( $orderData['name'], 0, 34 ) ) );
    $pdf->Text( $textPosX_2 + 20, $textPosY + 5, utf8_decode( $orderData['street'] ) );
    $pdf->Text( $textPosX_2 + 20, $textPosY + 10, $orderData['zipcode'].' '.utf8_decode( $orderData['city'] ) );
    $pdf->Text( $textPosX_2 + 20, $textPosY + 15, $orderData['phone'] );
    $pdf->Text( $textPosX_2 + 20, $textPosY + 20, $orderData['fax'] );
    $pdf->Text( $textPosX_2 + 20, $textPosY + 25, $orderData['employee_name'] );

    $pdf->Text( $textPosX_2 + 20, $textPosY + 35, $orderData['c_color'] );
    $pdf->Text( $textPosX_2 + 20, $textPosY + 40, $orderData['4'] );
    $pdf->Text( $textPosX_2 + 20, $textPosY + 45, $orderData['c_zrk'] );

    $pdf->Text( $textPosX + 20, $textPosY, $orderData['c_2'].' '.$orderData['c_3'] );
    $pdf->Text( $textPosX + 20, $textPosY + 5, db2date( $orderData['c_d'] ) );
    $pdf->Text( $textPosX + 20, $textPosY + 10, db2date( $orderData['c_hu'] ) );
    $pdf->Text( $textPosX + 20, $textPosY + 15, $orderData['c_fin'] );
    $pdf->Text( $textPosX + 20, $textPosY + 20, $orderData['c_mkb'] );

    $pdf->Text( $textPosX + 20, $textPosY + 25, $orderData['km_stnd'] );

    $pdf->Text( $textPosX + 20, $textPosY + 35, $orderData['c_em'] );
    $pdf->Text( $textPosX + 20, $textPosY + 40, $orderData['6'] );
    $pdf->Text( $textPosX + 20, $textPosY + 45, utf8_decode( $orderData['flxgr'] ) );

    $pdf->Text( $textPosX_2 + 40, $textPosY + 65, utf8_decode( $orderData['c_bf'] ) );
    $pdf->Text( $textPosX_2 + 40, $textPosY + 60, utf8_decode( $orderData['c_wd'] ) );
    $pdf->Text( $textPosX_2 + 40, $textPosY + 55, utf8_decode( $orderData['c_zrd'] ) );

    $pdf->Text( $textPosX + 40, $textPosY + 55, utf8_decode( $orderData['c_st_l'] ) );
    $pdf->Text( $textPosX + 40, $textPosY + 60, utf8_decode( $orderData['c_wt_l'] ) );

    $pdf->SetFont( 'Helvetica', 'B', '10' );
    $pdf->SetTextColor( 255, 0, 0 );
    $pdf->Text( '12', '94', 'Fertigstellung:' );
    $pdf->SetFont( 'Helvetica', '', '10' );
    $pdf->Text( '112', '94', utf8_decode( $orderData['finish_time'] ) );
    $pdf->SetTextColor( 0, 0, 0 );
    $pdf->SetFont( 'Helvetica', '', '10' );
    $pos_todo[x] = 20; $pos_todo[y] = 110;

    //get postions as assoz. array
    $positions = getPositions( $data['orderId'], false );

    //Instructions and positions
    $pdf->SetFont( 'Helvetica', '', '8' );
    $height = '95';

    $pdf->Text( '12','48', utf8_decode( '__________________________________________________________________________________________________________' ) );

    $pdf->Text( '12','68', utf8_decode( '__________________________________________________________________________________________________________' ) );

    $pdf->Text( '12','88', utf8_decode( '__________________________________________________________________________________________________________' ) );

    foreach( array_reverse( $positions ) as $index => $element ){
        //writeLog( $element['description'] );
        $height = $height + 8;
        $pdf->SetTextColor( 255, 0, 0 );
        $pdf->SetLineWidth( 0.1 );

        $pdf->Rect( '10', $height - 5, '170', '7' );
        if( $element['instruction'] ){
            $pdf->SetFont('Helvetica','B','10');
            $pdf->SetTextColor(255, 0, 0);
            $pdf->Text( '12',$height, utf8_decode( $element['description'] ) );
        }
        else{
            $pdf->SetFont( 'Helvetica', '', '8' );
            $pdf->SetTextColor( 0, 0, 0 );
            if( strlen( $element['description'] ) > 60 ){ //split long text
                $pdf->SetFont( 'Helvetica', '', '8' );
                $pdf->Text( '12',$height, utf8_decode( $element['qty']." ".$element['unit']."   ".$element['description'] ) );
            }
            else{
                $pdf->SetFont('Helvetica','','8');
                $pdf->Text( '12',$height, utf8_decode( $element['qty']." ".$element['unit']."   ".$element['description'] ) );
            }
        }
    }

    $pdf->SetFont( 'Helvetica', '', '10' );
    $pdf->Text( '22', '270', 'Datum:' );
    $pdf->Text( '45', '270', date( 'd.m.Y' ) );
    $pdf->Text( '105','270','Kundenunterschrift: __________________' );
    $pdf->SetTextColor( 255, 0, 0 );
    $pdf->Text( '22', '280', utf8_decode( 'Endkontrolle UND Probefahrt durchgeführt von: __________________' ) );
    $pdf->SetTextColor( 0, 0, 0 );
    $pdf->SetFont( 'Helvetica', '', '08' );
    $pdf->Text( '75', '290', 'Powered by lxcars.de - Freie Kfz-Werkstatt Software' );

    $pdf->OutPut( __DIR__.'/../out.pdf', 'F' );

    if( !$orderData['printed'] ) $GLOBALS['dbh']->update( 'oe', array( 'printed' ), array( 'TRUE' ), 'id = '.$data['orderId'] );

    if( $data['print'] ) system('lpr '.__DIR__.'/../out.pdf' );


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
    $rs = intval( $GLOBALS['dbh']->getOne( "SELECT qty, count( qty ) AS ct FROM orderitems WHERE description = '$description' GROUP BY 1 ORDER BY ct DESC LIMIT 1" )['qty'] );

    //Method 2: last modification
    //echo $GLOBALS['dbh']->getOne( "SELECT qty FROM orderitems WHERE description = '$description'  ORDER BY mtime DESC LIMIT 1" )['qty'];
    //writeLog( $rs );
    //writeLog( "SELECT qty, count( qty ) AS ct FROM orderitems WHERE description = '$description' GROUP BY 1 ORDER BY ct DESC LIMIT 1" );
    echo $rs? $rs : 1;
}

?>