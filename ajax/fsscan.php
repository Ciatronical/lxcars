
<?php
    require_once __DIR__.'/../../inc/stdLib.php'; // for debug
    require_once __DIR__.'/../inc/ajax2function.php';

    function getScans( $data ){
        /***********************************************************************************************************************************************************
         ********* follow lines gerate the $colArray ***************************************************************************************************************
        $rsFsData = file_get_contents( 'https://fahrzeugschein-scanner.de/api/Scans/ScanDetails/'.$apiKeyArray['lxcarsapi'].'/b7ef0bdf-0063-41f4-be05-8d9d5f0809ca/false' );
        $rsFsDataArray = json_decode( $rsFsData, TRUE ); //JSON to Array
        foreach( $rsFsDataArray AS $key => $value ) if( strpos( $key, 'img')) unset( $rsFsDataArray[$key]); // remove *_img
        $rsFsDataArrayKeys = array_keys( $rsFsDataArray );
        $col = '';
        foreach( $rsFsDataArrayKeys AS $key => $value ) $col .= "'$value', ";
        writeLog( $col );
        */
        $colArray = array( 'scan_detail_id', 'scan_id', 'ez', 'ez_string', 'hsn', 'tsn', 'vsn', 'field_2_2', 'vin', 'd3', 'registrationNumber', 'name1', 'name2', 'firstname', 'address1', 'address2', 'j', 'field_4', 'field_3', 'd1', 'd2_1', 'd2_2', 'd2_3', 'd2_4', 'field_2', 'field_5_1', 'field_5_2', 'v9', 'field_14', 'p3', 'field_10', 'field_14_1', 'p1', 'l', 'field_9', 'p2_p4', 't', 'field_18', 'field_19', 'field_20', 'g', 'field_12', 'field_13', 'q', 'v7', 'f1', 'f2', 'field_7_1', 'field_7_2', 'field_7_3', 'field_8_1', 'field_8_2', 'field_8_3', 'u1', 'u2', 'u3', 'o1', 'o2', 's1', 's2', 'field_15_1', 'field_15_2', 'field_15_3', 'r', 'field_11', 'k', 'field_6', 'field_17', 'field_16', 'field_21', 'field_22', 'hu', 'creation_date', 'creation_city', 'document_id', 'Maker', 'Model', 'PowerKw', 'PowerHpKw', 'Ccm', 'Fuel', 'FuelCode', 'Filename' );

        //get last timestamp from database
        $lastTimespamp = $GLOBALS['dbh']->getOne( 'SELECT itime FROM lxc_fs_scans ORDER BY itime DESC LIMIT 1' )['itime'];
        $lastTimeDB = (int) strtotime( $lastTimespamp ); //seconds from unix epoch

        $apiKeyArray = getDefaultsByArray( array( 'lxcarsapi') );
        $rs = file_get_contents( 'https://fahrzeugschein-scanner.de/api/Scans/GetScans/'.$apiKeyArray['lxcarsapi'].'/?take='.$data['fsmax'] );
        $rsArray = json_decode( $rs, TRUE ); //JSON to Array
        
        foreach( $rsArray AS $key => $value ){
            //writeLog( 'scanTime: '.strtotime( $value['timestamp'] ) );
            //WriteLog( 'dbTime: '.$lastTimeDB );
            //WriteLog( 'Diff: '.($lastTimeDB - (int) strtotime( $value['timestamp'] ) ) );
            if( $lastTimeDB < (int) strtotime( $value['timestamp'] ) ){
                //writeLog( ' we get a scan with id' );
                $rsFsData = file_get_contents( 'https://fahrzeugschein-scanner.de/api/Scans/ScanDetails/'.$apiKeyArray['lxcarsapi'].'/'.$value['id'].'/false' );
                $rsFsDataArray = json_decode( $rsFsData, TRUE ); //JSON to Array
                foreach( $rsFsDataArray AS $key => $value ){
                    if( strpos( $key, 'img')) unset( $rsFsDataArray[$key]); // remove *_img data
                }
                $GLOBALS['dbh']->insert( 'lxc_fs_scans', $colArray, array_values( $rsFsDataArray ) );
            }
        }
        echo $rs;
    }

    function getFsData( $data ){
        $apiKeyArray = getDefaultsByArray( array( 'lxcarsapi') );
        $rs = file_get_contents( 'https://fahrzeugschein-scanner.de/api/Scans/ScanDetails/'.$apiKeyArray['lxcarsapi'].'/'.$data['id'].'/false' );
        echo $rs;
    }

    function getDocument( $data ){
        $apiKeyArray = getDefaultsByArray( array( 'lxcarsapi') );
        $fileName = "testFile.jpg";
        if( file_put_contents( $fileName, file_get_contents( 'https://fahrzeugschein-scanner.de/api/Scans/Document/'.$apiKeyArray['lxcarsapi'].'/'.$data['id'] ) ) ){
           echo json_encode( "File successfully saved" );
        }
        else{
            echo json_encode( "Error save file." );
        }
    }
?>
    

