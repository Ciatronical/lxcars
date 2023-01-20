<?php
    require_once __DIR__.'/../../inc/stdLib.php'; // for debug
    require_once __DIR__.'/../inc/ajax2function.php';

    function getScans( $data ){
        $apiKeyArray = getDefaultsByArray( array( 'lxcarsapi') );
        echo file_get_contents( 'https://fahrzeugschein-scanner.de/api/Scans/GetScans/'.$apiKeyArray['lxcarsapi'].'/?take='.$data['fsmax'] );
    }

    function getFsData( $data ){
        $apiKeyArray = getDefaultsByArray( array( 'lxcarsapi') );
        echo file_get_contents( 'https://fahrzeugschein-scanner.de/api/Scans/ScanDetails/'.$apiKeyArray['lxcarsapi'].'/'.$data['id'].'/true' );
    }

    function getDocument( $data ){
        $apiKeyArray = getDefaultsByArray( array( 'lxcarsapi') );
        writeLog( 'https://fahrzeugschein-scanner.de/api/Scans/Document/'.$apiKeyArray['lxcarsapi'].'/'.$data['id'] );
        //echo file_get_contents( 'https://fahrzeugschein-scanner.de/api/Scans/ScanDetails/'.$apiKeyArray['lxcarsapi'].'/'.$data['id'].'/true' );
        /*
            if (file_put_contents($file_name, file_get_contents($url)))
    {
        echo "File downloaded successfully";
    }
    else
    {
        echo "File downloading failed.";
    }*/
    }

?>
