<?php
    require_once __DIR__.'/../../inc/stdLib.php'; // for debug
    require_once __DIR__.'/../inc/ajax2function.php';

    function getScans( $data ){
        $apiKeyArray = getDefaultsByArray( array( 'lxcarsapi') );
        echo file_get_contents( 'https://fahrzeugschein-scanner.de/api/Scans/GetScans/'.$apiKeyArray['lxcarsapi'].'/?take='.$data['fsmax'] );
    }

    function getFsData( $data ){
        //echo json_encode( 'hello world '.$data['id'] );// /api/Scans/ScanDetails/{accessKey}/{scanId}/{getImageCuts};
        $apiKeyArray = getDefaultsByArray( array( 'lxcarsapi') );
        writeLog( 'https://fahrzeugschein-scanner.de/api/Scans/ScanDetails/'.$apiKeyArray['lxcarsapi'].'/'.$data['id'].'/false' );
        echo file_get_contents( 'https://fahrzeugschein-scanner.de/api/Scans/ScanDetails/'.$apiKeyArray['lxcarsapi'].'/'.$data['id'].'/true' );
    }

?>
