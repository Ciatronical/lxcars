
<?php
    require_once __DIR__.'/../../inc/stdLib.php'; // for debug
    require_once __DIR__.'/../inc/ajax2function.php';



    function getScans( $data ){
        $apiKeyArray = getDefaultsByArray( array( 'lxcarsapi') );

        $rs = file_get_contents( 'https://fahrzeugschein-scanner.de/api/Scans/GetScans/'.$apiKeyArray['lxcarsapi'].'/?take='.$data['fsmax'] );

        //$dbFsScans = $GLOBALS['dbh']->getALL( 'SELECT * FROM lxc_fs_scans' );

        //Lezten Datensatz holen, nur neuere FS werdeb gespeichert
        $lastTimespamp = $GLOBALS['dbh']->getOne( 'SELECT itime FROM lxc_fs_scans ORDER BY itime DESC LIMIT 1' );
        writeLog( $lastTimespamp );
     
        
        
        $rsArray = json_decode( $rs, TRUE ); //JSON to Array
        
        foreach( $rsArray AS $key => $value ){ //Remove images-keys from array
            if( strpos( $key, 'img' ) ) unset( $rsArray[$key] );
        }

        /*
        Nun holen wir sämtliche FS-Scans ab und schauen mit dem letzten Scan beginnend ob der TS größer ist ( neuer ist ) als der in der DB gespeicherte.
        Ist dies der Fall, so wird ein neuer Datensatz in lxc_fs_scans abgelegt und der Array weiter durchgegangen
        */

        //Array rückwärts durchlaufen
        /*
        for (end(rsArray); key(rsArray)!==null; prev(rsArray)){
            $currentElement = current(rsArray);
            writeLog(  $currentElement );
            //if timestamp <= $currentElement['timestamp'] ){
                //insertIn Database
            }
            }
        }
       */
        writeLog( $rsArray['0'][timestamp]); 

        //$keys = array_keys( $rsArray );
        //$dbrs = $GLOBALS['dbh']->insert( 'lxc_fs_scans', array( 'scan_detail_id' ), array( 'gfsdfgsdhjgfasgahj' ) );
        //writeLog( $keys );
        //writeLog( array_values( $rsArray ) );
        //  $GLOBALS['dbh']->insert( 'lxc_fs_scans', $keys, array_values( $rsArray ) );
        echo $rs;
    }

    function getFsData( $data ){
        $apiKeyArray = getDefaultsByArray( array( 'lxcarsapi') );
        $rs = file_get_contents( 'https://fahrzeugschein-scanner.de/api/Scans/ScanDetails/'.$apiKeyArray['lxcarsapi'].'/'.$data['id'].'/false' );
        //writeLog( $rs );
        
        //writeLog( $rsArray );
        writeLog( array_keys( $rsArray ) );


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
    

