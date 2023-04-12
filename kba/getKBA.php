<?php
    require_once __DIR__.'/../inc/ajax2function.php';

    $debug = true;
    $dl = 2; //download

    function strposArray( $haystack, $needle, $offset = 0 ){
      if( !is_array( $needle) ) return false;
      foreach( $needle as $query ){
          $result  = strpos( $haystack, $query, $offset );
          if( $result !== false ) return $result;
      }
      return false;
    }

    function getKBACars(){
        $carTypesArray = array( 'BEETLE', 'CALIFORNIA', 'CADDY', 'Club', 'Crafter', 'GOLF', 'EOS', 'FOX', 'Hymer', 'JETTA', 'KOMBI', 'PASSAT', 'PHAETON', 'POLO', 'SCIROCCO', 'SHARAN', 'TIGUAN', 'TOUAREG', 'TOURAN', 'VWUP!', 'XL1', 'CC' );
        system( 'rm sv42.*' );
        system( "wget 'https://www.kba.de/SharedDocs/Downloads/DE/SV/sv42_pdf.pdf?__blob=publicationFile' -O sv42.pdf" );
        system( 'pdftotext -layout sv42.pdf' );
        //// Alternativ: python3 /usr/local/bin/pdf2txt.py   sv42.pdf > test.txt
        $allLines = file( 'sv42.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
        foreach( $allLines as $key => $value ){
            $goodLine = TRUE;

            if( !ctype_digit( substr( $value, 0, 4) ) ) $goodLine = FALSE;  //// Prüfen ob die ersten vier Zeichen numerisch sind
            if( !ctype_space(substr( $value, 4, 5 ) ) ) $goodLine = FALSE;  //// Prüft ob 5 Zeichen ein Leerzeichen ist
            if( !$goodLine ) unset( $allLines[$key] );
            //print_r( $allLines[$key] );
        }
        if( $debug ) $formatedLines = array();
        foreach( $allLines as $key => $value ){
            if( substr( $value, 74, 12 ) && !ctype_space( substr( $value, 74, 8 ) ) ){ //sind Kfz-Typen zu weit nach lins verschoben?
                $posBegin = strposArray( $value, $carTypesArray, 74 );
                if( $posBegin === false || $posBegin >= 84 ) continue;
                $posEnd = strpos( $value, '   ', $posBegin );
                $length = $posEnd - $posBegin;
                $stringToShift = substr( $value, $posBegin, $length );
                $replaceStr = str_repeat( ' ', $length );
                $value = substr_replace( $value, $replaceStr, $posBegin, $length );
                $value = substr_replace( $value, $stringToShift, 84, $length );
                if( $debug ) $formatedLines[$key] = substr( $value, 52, 58 );
                $allLines[$key] = $value;
                if( $debug ) echo "posBegin: ".$posBegin." PosEnd: ".$posEnd." String: ".$stringToShift." \n";
            }
            //if( !ctype_space( substr( $value, 53, 2 ) ) ) $formatedLines[$key] = substr( $value, 52, 45 );//52,32
            //if( !ctype_space( substr( $value, 53, 2 ) ) ) $formatedLines[$key] = substr( $value, 74, 12 );//52,32
            //file_put_contents( 'mykba.txt', implode( PHP_EOL, $value ), FILE_APPEND );


        }
        $posSeperator = array( 13, 24, 54, 87, 123, 151, 170, 179, 190, 198, 215, 225, 235, 243 );
        foreach( $allLines as $key => $value ){
            foreach( $posSeperator as $posKey => $posValue ){
                $value = substr_replace( $value, '|', $posValue, 0 );
            }

            $allLines[$key] = $value;
        }
        file_put_contents( 'kbacars.csv', implode( PHP_EOL, $allLines ) );
        if( $debug ) file_put_contents('formatedLines.txt', implode( PHP_EOL, $formatedLines ) );

        //Datenbank anlegen
        //writeLog( 'getKBACars ausgeführt');

        //// test with SELECT * FROM carskba WHERE klasse ILIKE '%11%';
        $sql = 'DROP TABLE IF EXISTS kbacars';
        $GLOBALS['dbh']->query( $sql );
        $sql = 'CREATE TABLE kbacars(
            hsn TEXT,
            tsn TEXT,
            hersteller TEXT,
            marke TEXT,
            name TEXT,
            datum TEXT,
            klasse TEXT,
            aufbau TEXT,
            kraftstoff TEXT,
            leistung TEXT,
            hubraum TEXT,
            achsen TEXT,
            antrieb TEXT,
            sitze TEXT,
            masse TEXT
        )';
        $GLOBALS['dbh']->query( $sql );
        $sql = "COPY kbacars( hsn, tsn, hersteller, marke, name, datum, klasse, aufbau, kraftstoff, leistung, hubraum, achsen, antrieb, sitze, masse ) FROM '".__DIR__."/carskba.csv' DELIMITER '|' CSV";
        $GLOBALS['dbh']->query( $sql );

        //Prepare statement for UPDATE with btrim(), UPDATE kbatrailer SET * btrim( * );
        $sql = "SELECT 'UPDATE kbacars SET '||string_agg( concat( c.column_name, ' = btrim( ', c.column_name, ' ) '), ', ') AS updatequery FROM information_schema.columns c WHERE table_name = 'kbacars'";
        //writeLog( $sql );
        $rs = $GLOBALS['dbh']->getOne( $sql );
        //writeLog( $rs );

        /*********************** btrim() for all columns ******************************/
        $GLOBALS['dbh']->query( $rs['updatequery'] );

        echo file_get_contents( __DIR__.'/kbacars.csv' ) ? 1 : false;

    }//function getKBA

    function getKBAtrailer(){
        //writeLog( __FUNCTION__ );
        system( 'rm sv45*' );
        system( "wget 'https://www.kba.de/SharedDocs/Downloads/DE/SV/sv45_pdf.pdf?__blob=publicationFile' -O sv45.pdf" );
        system( 'pdftotext -layout sv45.pdf' );
        $allLines = file( 'sv45.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );

        foreach( $allLines as $key => $value ){
            $goodLine = TRUE;
            if( !ctype_digit( substr( $value, 0, 4) ) ) $goodLine = FALSE;  //// Prüfen ob die ersten vier Zeichen numerisch sind
            if( !ctype_space(substr( $value, 4, 5 ) ) ) $goodLine = FALSE;  //// Prüft ob 5 Zeichen ein Leerzeichen ist
            if( !$goodLine ) unset( $allLines[$key] );

        }
        // Doppelte Anführungszeichen entfernen
        foreach( $allLines as $key => $value ){
            $allLines[$key] = str_replace(  '"', '', $value );
        }
        //DELIMITER einfügen
        $posSeperator = array( 13, 24, 54, 87, 123, 151, 170, 214, 240 );
        foreach( $allLines as $key => $value ){
            foreach( $posSeperator as $posKey => $posValue ){
                $value = substr_replace( $value, '|', $posValue, 0 );
            }

            $allLines[$key] = $value;
        }
        file_put_contents( 'kbatrailer.csv', implode( PHP_EOL, $allLines ) );

        $sql = 'DROP TABLE IF EXISTS kbatrailer';
        $GLOBALS['dbh']->query( $sql );

        $sql = "CREATE TABLE kbatrailer(
            hsn TEXT,
            tsn TEXT,
            hersteller TEXT,
            marke TEXT,
            name TEXT,
            datum TEXT,
            klasse TEXT,
            aufbau TEXT,
            achsen TEXT,
            masse TEXT )";
        $GLOBALS['dbh']->query( $sql );

        $sql = "COPY kbatrailer( hsn, tsn, hersteller, marke, name, datum, klasse, aufbau, achsen, masse ) FROM '".__DIR__."/kbatrailer.csv' DELIMITER '|' CSV";
        $GLOBALS['dbh']->query( $sql );

        //Prepare statement for UPDATE with btrim(), UPDATE kbatrailer SET * btrim( * );
        $sql = "SELECT 'UPDATE kbatrailer SET '||string_agg( concat( c.column_name, ' = btrim( ', c.column_name, ' ) '), ', ') AS updatequery FROM information_schema.columns c WHERE table_name = 'kbatrailer'";
        writeLog( $sql );
        $rs = $GLOBALS['dbh']->getOne( $sql );
        //writeLog( $rs );

        /*********************** btrim() for all columns ******************************/
        $GLOBALS['dbh']->query( $rs['updatequery'] );

        echo file_get_contents( __DIR__.'/kbatrailer.csv' ) ? 1 : false;

    }

    function getKBAbike(){
        //writeLog( __FUNCTION__ );
        system( 'rm sv41.*' );
        $bikeTypesArray = array( 'KREIDLER FLORETT 50 XL;SM', 'SMC', 'KAESA SKY-5', 'RAM', 'KASEA SKY-5', 'F KART', 'CITY ATV', 'G KART', 'REX 50 SILVERSTAR,SILVERS', 'FLORETT 50', 'FLORETT 25', 'REXY 25', 'FLORY 25', 'SCOOTER,MK,CAPRIOLO,CLIPP', 'REXY 50', 'FLORY 50', 'SILVERSTAR,SILVERSTREET', 'M Kart', 'Quadzilla', 'SKY,BLAST,SL', 'City ATV', 'F Kart'  );
        system( "wget 'https://www.kba.de/SharedDocs/Downloads/DE/SV/sv41_pdf.pdf?__blob=publicationFile' -O sv41.pdf" );
        system( 'pdftotext -layout sv41.pdf' );
        $allLines = file( 'sv41.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );

        foreach( $allLines as $key => $value ){
          $goodLine = TRUE;
          if( !ctype_digit( substr( $value, 0, 4) ) ) $goodLine = FALSE;  //// Prüfen ob die ersten vier Zeichen numerisch sind
          if( !ctype_space(substr( $value, 4, 5 ) ) ) $goodLine = FALSE;  //// Prüft ob 5 Zeichen ein Leerzeichen ist
          if( !$goodLine ) unset( $allLines[$key] );
        }

        foreach( $allLines as $key => $value ){
            if( substr( $value, 74, 12 ) && !ctype_space( substr( $value, 74, 8 ) ) ){ //sind Kfz-Typen zu weit nach lins verschoben?
                $posBegin = strposArray( $value, $bikeTypesArray, 74 );
                if( $posBegin === false || $posBegin >= 84 ) continue;
                $posEnd = strpos( $value, '   ', $posBegin );
                $length = $posEnd - $posBegin;
                $stringToShift = substr( $value, $posBegin, $length );
                $replaceStr = str_repeat( ' ', $length );
                $value = substr_replace( $value, $replaceStr, $posBegin, $length );
                $value = substr_replace( $value, $stringToShift, 89, $length );//89 ist die Pos wo sie hingeschoben werden
                //if( $debug ) $formatedLines[$key] = substr( $value, 52, 58 );
                $allLines[$key] = $value;
                //if( $debug ) echo "posBegin: ".$posBegin." PosEnd: ".$posEnd." String: ".$stringToShift." \n";
            }
        }

        //DELIMITER einfügen
        $posSeperator = array( 13, 24, 56, 91, 129, 151, 170, 181, 192, 206 , 217, 230, 240, 253 );
        foreach( $allLines as $key => $value ){
            foreach( $posSeperator as $posKey => $posValue ){
                $value = substr_replace( $value, '|', $posValue, 0 );
            }

            $allLines[$key] = $value;
        }

        file_put_contents( 'kbabikes.csv', implode( PHP_EOL, $allLines ) );

        $sql = 'DROP TABLE IF EXISTS kbabikes';
        $GLOBALS['dbh']->query( $sql );
        $sql = 'CREATE TABLE kbabikes(
            hsn TEXT,
            tsn TEXT,
            hersteller TEXT,
            marke TEXT,
            name TEXT,
            datum TEXT,
            klasse TEXT,
            aufbau TEXT,
            kraftstoff TEXT,
            leistung TEXT,
            hubraum TEXT,
            achsen TEXT,
            antrieb TEXT,
            sitze TEXT,
            masse TEXT
        )';
        $GLOBALS['dbh']->query( $sql );

        $sql = "COPY kbabikes( hsn, tsn, hersteller, marke, name, datum, klasse, aufbau, kraftstoff, leistung, hubraum, achsen, antrieb, sitze, masse ) FROM '".__DIR__."/kbabikes.csv' DELIMITER '|' CSV";
        $GLOBALS['dbh']->query( $sql );

        //Prepare statement for UPDATE with btrim(), UPDATE kbatractors SET * btrim( * );
        $sql = "SELECT 'UPDATE kbabikes SET '||string_agg( concat( c.column_name, ' = btrim( ', c.column_name, ' ) '), ', ') AS updatequery FROM information_schema.columns c WHERE table_name = 'kbabikes'";
        $rs = $GLOBALS['dbh']->getOne( $sql );
        //writeLog( $rs );

        /*********************** btrim() for all columns ******************************/
        $GLOBALS['dbh']->query( $rs['updatequery'] );

        echo file_get_contents( __DIR__.'/kbabikes.csv' ) ? 1 : false;

    }

    function getKBAtruck(){
        //writeLog( __FUNCTION__ );
        $truckTypesArray = array( 'Algema/Crafter Blitzlader', 'Fitzel Speeder T5, 46-20', 'PanelVAN', 'AMAROK', 'TIGUAN', 'PASSAT', 'VWUP!', 'Crafter', 'SHARAN', 'TOURAN', 'Transporter', 'POLO' );
        system( 'rm sv43.*' );
        system( "wget 'https://www.kba.de/SharedDocs/Downloads/DE/SV/sv43_pdf.pdf?__blob=publicationFile' -O sv43.pdf" );
        system( 'pdftotext -layout sv43.pdf' );
        $allLines = file( 'sv43.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );

        foreach( $allLines as $key => $value ){
            $goodLine = TRUE;
            if( !ctype_digit( substr( $value, 0, 4) ) ) $goodLine = FALSE;  //// Prüfen ob die ersten vier Zeichen numerisch sind
            if( !ctype_space(substr( $value, 4, 5 ) ) ) $goodLine = FALSE;  //// Prüft ob 5 Zeichen ein Leerzeichen ist
            if( !$goodLine ) unset( $allLines[$key] );
        }

        //Pipes entfernen
        foreach( $allLines as $key => $value ){
            $allLines[$key] = str_replace(  '|', '', $value );
        }

        foreach( $allLines as $key => $value ){
            if( substr( $value, 74, 12 ) && !ctype_space( substr( $value, 74, 8 ) ) ){ //sind Kfz-Typen zu weit nach links verschoben?
                $posBegin = strposArray( $value, $truckTypesArray, 74 );
                if( $posBegin === false || $posBegin >= 84 ) continue;
                $posEnd = strpos( $value, '   ', $posBegin );
                $length = $posEnd - $posBegin;
                $stringToShift = substr( $value, $posBegin, $length );
                $replaceStr = str_repeat( ' ', $length );
                $value = substr_replace( $value, $replaceStr, $posBegin, $length );
                $value = substr_replace( $value, $stringToShift, 85, $length );//85 ist die Pos wo sie hingeschoben werden
                //if( $debug ) $formatedLines[$key] = substr( $value, 52, 58 );
                $allLines[$key] = $value;
                //if( $debug ) echo "posBegin: ".$posBegin." PosEnd: ".$posEnd." String: ".$stringToShift." \n";
            }
        }

        $posSeperator = array( 13, 24, 54, 88, 124, 151, 170, 179, 191, 199, 217, 226, 240, 248 );
        foreach( $allLines as $key => $value ){
            foreach( $posSeperator as $posKey => $posValue ){
                $value = substr_replace( $value, '|', $posValue, 0 );
            }

            $allLines[$key] = $value;
        }

        file_put_contents( 'kbatrucks.csv', implode( PHP_EOL, $allLines ) );

        $sql = 'DROP TABLE IF EXISTS kbatrucks';
        $GLOBALS['dbh']->query( $sql );
        $sql = 'DROP TABLE IF EXISTS kbatrucks';
        $GLOBALS['dbh']->query( $sql );
        $sql = 'CREATE TABLE kbatrucks(
            hsn TEXT,
            tsn TEXT,
            hersteller TEXT,
            marke TEXT,
            name TEXT,
            datum TEXT,
            klasse TEXT,
            aufbau TEXT,
            kraftstoff TEXT,
            leistung TEXT,
            hubraum TEXT,
            achsen TEXT,
            antrieb TEXT,
            sitze TEXT,
            masse TEXT
        )';
        $GLOBALS['dbh']->query( $sql );

        $sql = "COPY kbatrucks( hsn, tsn, hersteller, marke, name, datum, klasse, aufbau, kraftstoff, leistung, hubraum, achsen, antrieb, sitze, masse ) FROM '".__DIR__."/kbatrucks.csv' DELIMITER '|' CSV";
        $GLOBALS['dbh']->query( $sql );

        //Prepare statement for UPDATE with btrim(), UPDATE kbatractors SET * btrim( * );
        $sql = "SELECT 'UPDATE kbatrucks SET '||string_agg( concat( c.column_name, ' = btrim( ', c.column_name, ' ) '), ', ') AS updatequery FROM information_schema.columns c WHERE table_name = 'kbatrucks'";
        $rs = $GLOBALS['dbh']->getOne( $sql );
        //writeLog( $rs );

        /*********************** btrim() for all columns ******************************/
        $GLOBALS['dbh']->query( $rs['updatequery'] );

        echo file_get_contents( __DIR__.'/kbatrucks.csv' ) ? 1 : false;
    }

    function getKBATractor(){
        //writeLog( __FUNCTION__ );
        system( 'rm sv44.*' );
        system( "wget 'https://www.kba.de/SharedDocs/Downloads/DE/SV/sv44_pdf.pdf?__blob=publicationFile' -O sv44.pdf" );
        system( 'pdftotext -layout sv44.pdf' );
        $allLines = file( 'sv44.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
        foreach( $allLines as $key => $value ){
            $goodLine = TRUE;
            if( !ctype_digit( substr( $value, 0, 4) ) ) $goodLine = FALSE;  //// Prüfen ob die ersten vier Zeichen numerisch sind
            if( !ctype_space(substr( $value, 4, 5 ) ) ) $goodLine = FALSE;  //// Prüft ob 5 Zeichen ein Leerzeichen ist
            if( !$goodLine ) unset( $allLines[$key] );
        }

        $posSeperator = array( 13, 24, 54, 87, 123, 151, 170, 179, 191, 199, 217, 226, 240, 248 );
        foreach( $allLines as $key => $value ){
            foreach( $posSeperator as $posKey => $posValue ){
                $value = substr_replace( $value, '|', $posValue, 0 );
            }

            $allLines[$key] = $value;
        }

        file_put_contents( 'sv44.csv', implode( PHP_EOL, $allLines ) );

        $sql = 'DROP TABLE IF EXISTS kbatractors';
        $GLOBALS['dbh']->query( $sql );
        $sql = 'DROP TABLE IF EXISTS kbatractors';
        $GLOBALS['dbh']->query( $sql );
        $sql = 'CREATE TABLE kbatractors(
            hsn TEXT,
            tsn TEXT,
            hersteller TEXT,
            marke TEXT,
            name TEXT,
            datum TEXT,
            klasse TEXT,
            aufbau TEXT,
            kraftstoff TEXT,
            leistung TEXT,
            hubraum TEXT,
            achsen TEXT,
            antrieb TEXT,
            sitze TEXT,
            masse TEXT
        )';
        $GLOBALS['dbh']->query( $sql );

        $sql = "COPY kbatractors( hsn, tsn, hersteller, marke, name, datum, klasse, aufbau, kraftstoff, leistung, hubraum, achsen, antrieb, sitze, masse ) FROM '".__DIR__."/sv44.csv' DELIMITER '|' CSV";
        $GLOBALS['dbh']->query( $sql );

        //Prepare statement for UPDATE with btrim(), UPDATE kbatractors SET * btrim( * );
        $sql = "SELECT 'UPDATE kbatractors SET '||string_agg( concat( c.column_name, ' = btrim( ', c.column_name, ' ) '), ', ') AS updatequery FROM information_schema.columns c WHERE table_name = 'kbatractors'";
        $rs = $GLOBALS['dbh']->getOne( $sql );
        //writeLog( $rs );
        /*********************** btrim() for all columns ******************************/
        $GLOBALS['dbh']->query( $rs['updatequery'] );

        echo file_get_contents( __DIR__.'/sv44.csv' ) ? 1 : false; //Vielleicht sollte man hier zusätlich noch prüfen ob es mindestens n Datensätze in der Tabelle gibt
    }
?>
