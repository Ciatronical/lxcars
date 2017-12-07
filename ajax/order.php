<?php

//require_once __DIR__.'/../../inc/stdLib.php'; // for debug
require_once __DIR__.'/../../inc/crmLib.php';
require_once __DIR__.'/../inc/ajax2function.php';


function autocompletePart( $term ){
    echo $GLOBALS['dbh']->getAll( "SELECT description, partnumber, id, partnumber || ' ' || description AS value, part_type, unit,  partnumber || ' ' || description AS label, instruction FROM parts WHERE ( description ILIKE '%$term%' OR partnumber ILIKE '$term%' ) AND obsolete = FALSE LIMIT 20", true );
}

function getOrder( $id ){
    echo $GLOBALS['dbh']->getOne( "SELECT oe.amount, oe.netamount, oe.ordnumber AS ordnumber, oe.id AS oe_id,  to_char(oe.transdate, 'DD.MM.YYYY') AS transdate, to_char( oe.reqdate, 'DD.MM.YYYY') AS reqdate,  oe.finish_time AS finish_time, oe.km_stnd, oe.c_id, oe.status AS order_status, oe.customer_id AS customer_id, oe.car_status, customer.name AS customer_name, lxc_cars.* FROM oe, customer, lxc_cars WHERE oe.id = '".$id."' AND customer.id = oe.customer_id AND oe.c_id = lxc_cars.c_id", true);
}


function getPositions( $orderID, $json = true ){
    $sql = "SELECT 'true'::BOOL AS instruction,parts.instruction, instructions.id, instructions.parts_id, instructions.qty, instructions.description, instructions.position, instructions.unit, instructions.sellprice, instructions.marge_total, instructions.discount, instructions.u_id, instructions.status, parts.partnumber, parts.part_type FROM instructions, parts WHERE instructions.trans_id = '".$orderID."'AND parts.id = instructions.parts_id UNION ";
    $sql.= "SELECT 'false'::BOOL AS instruction,parts.instruction, orderitems.id, orderitems.parts_id, orderitems.qty, orderitems.description, orderitems.position, orderitems.unit, orderitems.sellprice, orderitems.marge_total, orderitems.discount, orderitems.u_id, orderitems.status, parts.partnumber, parts.part_type FROM orderitems, parts WHERE orderitems.trans_id = '".$orderID."' AND parts.id = orderitems.parts_id ORDER BY position DESC";
    $rs = $GLOBALS['dbh']->getAll( $sql, $json );//testd
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
    //writeLog( $orderID );
    $rs = $GLOBALS['dbh']->getAll( "SELECT name,type FROM units", true );

    echo $rs;
}

function getAccountingGroups(){
    //writeLog( $orderID );
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
    elseif( $type[type] == "service")
        $rs = $GLOBALS['dbh']->getOne( "SELECT id AS defaults_id, servicenumber::INT + 1 AS newnumber, customer_hourly_rate, 1 AS service FROM defaults");

    //increase partnumber if partnumber exists
    while( $GLOBALS['dbh']->getOne( "SELECT partnumber FROM parts WHERE partnumber = '".$rs['newnumber']."'" )['partnumber'] ) $rs['newnumber']++;
    //writeLog( $rs );
    echo  json_encode( $rs );//JS-friendly JSON
    //echo  '['.json_encode( $rs ).']';//JS-friendly JSON
}

function saveLastArticleNumber( $data ){
    if( $data['service'] ) echo $GLOBALS['dbh']->update( 'defaults', array( 'servicenumber' ), array( $data['artNr'] ), 'id = '.$data['id'] );
    else echo $GLOBALS['dbh']->update( 'defaults', array( 'articlenumber' ), array( $data['artNr'] ), 'id = '.$data['id'] );
}

function updateOrder( $data) {
    writeLog($data[0]);
    echo $GLOBALS['dbh']->update( 'oe', array( 'km_stnd', 'status', 'netamount', 'amount', 'car_status', 'finish_time' ), array( $data[0]['km_stnd'], $data[0]['status'], $data[0]['netamount'], $data[0]['amount'], $data[0]['car_status'], $data[0]['finish_time'] ), 'id = '.$data[0]['id'] );
}

function getCar( $c_id ){
   //writeLog( $c_id );
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
    //ToDo

}

function getOrderList( $data ) {
    //Hier muss natürlich noch die oe.id geholt werden, da sämtliche Aufträge via id gehändelt werden
    $statusSearchString = $data['statusSearch'] == 'alle' ? '' : " oe.status = '".$data['statusSearch']."' AND ";//tenärer Operator
    $dateStringFrom = varExist( $data['datum_von'] ) ? " oe.transdate BETWEEN  <= '".$data['datum_von']."' AND " : '';
    $dateStringTo   = varExist( $data['datum_bis'] ) ?  " oe.transdate BETWEEN  >= '".$data['datum_bis']."' AND " : '';
    //writeLog($data['kennzeichen'].', '.$data['kundenname'].', '.$data['datum_von'].', '.$data['datum_bis'].', '.$data['statusSearch']);
    //writeLog($data);
    $sql = "
        SELECT
            oe.id AS id,
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
            oe.ordnumber ASC";
    //writeLog( $sql );
    echo $rs = $GLOBALS['dbh']->getAll( $sql, true );

}

function printOrder( $data ){

    require("fpdf.php");
    require_once( __DIR__.'/../inc/lxcLib.php' );
    include_once( __DIR__.'/../inc/config.php' );

    $sql  = "SELECT oe.ordnumber, oe.transdate, oe.finish_time, oe.km_stnd, oe.employee_id, ";
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

    $pdf=new FPDF('P','mm','A4');
    $pdf->AddPage();

    $pdf->SetFont( 'Helvetica', 'B', '18' ); //('font_family','font_weight','font_size')
    $pdf->Text( '20','26','Autoprofis Reparaturauftrag' ); //('pos_left','pos_top','text')
    $pdf->Text( '135', '26', $orderData['c_ln'] ); //utf8_decode(
    $pdf->SetFont('Helvetica','','14');
    $pdf->Text('20','35',$orderData["cm"]."  ".$orderData["ct"]);

    //Feste Werte
    $pdf->SetFont('Helvetica','','12');
    $pdf->Text('22','45','Kunde:');
    $pdf->Text('22','52',utf8_decode('Straße').':');
    $pdf->Text('22','59','Ort:');
    $pdf->Text('22','66','Tele.:');
    $pdf->Text('22','73','Mobil:');
    $pdf->Text('22','80','Bearb.:');
    $pdf->Text('22','87','Farbe:');
    $pdf->Text('22','94','Hubr.:');
    $pdf->Text('22','100','Zr. Km:');
    $pdf->Text('22','106',utf8_decode('nächst. ZR-Wechsel').':');
    $pdf->Text('124','45','KBA:');
    $pdf->Text('124','51','Baujahr:');
    $pdf->Text('124','58','FIN:');
    $pdf->Text('124','64','MK:');
    $pdf->Text('124','69','AU/HU:');
    $pdf->Text('124','75','KM:');
    $pdf->Text('124','81','Abgas.:');
    $pdf->Text('124','88','Peff:');
    $pdf->Text('124','94','Flexgr.:');
    $pdf->Text('124','100',utf8_decode('nächst. Bremsfl.').':');
    $pdf->Text('124','106',utf8_decode('nächst. WD').':');

    $pdf->SetLineWidth(0.3);
    $pdf->Rect('20', '38', '100', '70');
    $pdf->Rect('122', '38', '84', '70');

    //Daten aus DB
    $pdf->SetFont('Helvetica','','14');

    $pdf->Text('43','45',utf8_decode( substr( $orderData["name"], 0, 34 ) ) );
    $pdf->Text('43','52',utf8_decode($orderData["street"]));
    $pdf->Text('43','59',utf8_decode($orderData["city"]));
    $pdf->Text('43','66',$orderData["phone"]);
    $pdf->Text('43','73',$orderData["fax"]);
    $pdf->Text('43','87',$orderData["c_color"]);
    $pdf->Text('43','94',$orderData["4"]);
    $pdf->Text('43','100',$orderData["c_zrk"]);
    $pdf->Text('68','106',utf8_decode($orderData["c_zrd"]));
    $pdf->Text('148','45',$orderData["c_2"]." ".$orderData["c_3"]);
    $pdf->Text( '148', '51', db2date( $orderData["c_d"] ) );
    $pdf->Text('148','58',$orderData["c_fin"]);
    $pdf->Text('148','63',$orderData["c_mkb"]);
    $pdf->Text('148','69',db2date( $orderData["c_hu"] ) );
    $pdf->Text('148','74',$orderData["km_stnd"]);
    $pdf->Text('148','81',$orderData["c_em"]);
    $pdf->Text('148','88',$orderData["6"]);
    $pdf->Text('148','94',utf8_decode($orderData["c_flx"]));
    $pdf->Text('157','100',utf8_decode($orderData["c_bf"]));
    $pdf->Text('151','106',utf8_decode($orderData["c_wd"]));
    $pdf->SetFont('Helvetica','B','16');
    $pdf->SetTextColor(255, 0, 0);
    $pdf->Text('20','115','Fertigstellung:');
    $pdf->SetFont('Helvetica','','16');
    $pdf->Text('75','115',utf8_decode($orderData['finish_time']));
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Helvetica','','10');
    $pos_todo[x] = 20;$pos_todo[y] = 110;


    //"Merk"-Variable ob es Positionen mit Absätzen gab
    $merke = 0;

    $positions = getPositions( $data['orderId'], false );
    //writeLog( $positions );
    foreach( $positions as $index => $element ){
        writeLog( $element['description'].' '.$index );
        $b = 10;
        $count = strlen( $positions[$index-1]['description'] ) - strlen( str_replace( "\n", "", $postions[$index-1]['description']));
        // Wenn die vorhergehende Position mehr als 3 Absätze hat, muss die nächste Position weiter nach unten verrückt werden
        if( $count >= 3 ) {
            $y = $pos_todo[y]+$b*($index+$merke+1);
            $merke++;
        }
        else {
            $y = $pos_todo[y]+$b*($index+$merke);
        }
        if($index >= 1) {
        $pdf->SetXY($pos_todo[x], $y);
        $pdf->Rect($pos_todo[x], $y-2, '185', '10');
        //writeLog( $data[$index]['pos_instruction'] );
        if( $element['instruction'] == 'true'  ){
             $pdf->SetTextColor( 255, 0, 0 );
             $pdf->SetFont( 'Arial', 'BI', 11 );
        }
        $pdf->Multicell( 0, 5, utf8_decode( $element['qty'].'  '.$element['unit'].'  '.$element['description'] ) );
        $pdf->Multicell( 0, 5, "\r\n" );
        $pdf->SetTextColor( 0, 0, 0 );
        $pdf->SetFont( '' );
        }
    }

    $pdf->SetFont('Helvetica','','14');
    $pdf->Text('22','270','Datum:');
    $pdf->Text('45','270',date('d.m.Y'));
    $pdf->Text('105','270','Kundenunterschrift: __________________');
    $pdf->SetTextColor(255, 0, 0);
    $pdf->Text('22','280',utf8_decode('Endkontrolle UND Probefahrt durchgeführt von: __________________'));
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Helvetica','','08');
    $pdf->Text('75','290','Powered by lxcars.de - Freie Kfz-Werkstatt Software');

    $pdf->OutPut( __DIR__.'/../out.pdf', 'F' );

    if( $data['print'] ) system( __DIR__.'/../out.pdf' );

    //writeLog( $data['print'] );

    echo 1;
}

function setHuAuDate( $c_id ){
    //writeLog($c_id);
    $today   = date( 'Y-m-d' );
    //writeLog($today);
    $newdate = date( 'Y-m-01', strtotime( $today.' + 2 year ' ) );
    //writeLog($newdate);
    return $GLOBALS['dbh']->update( 'lxc_cars', array( 'c_hu' ), array( $newdate ), 'c_id = '.$c_id );
}

function getQtyNewPart($description){
  //writeLog($description);
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