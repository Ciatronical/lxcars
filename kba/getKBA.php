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
        system( "wget 'https://www.kba.de/SharedDocs/Publikationen/DE/Fahrzeugtechnik/SV/sv42_pdf.pdf?__blob=publicationFile&v=21' -O sv42.pdf" );

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
        file_put_contents( 'carskba.csv', implode( PHP_EOL, $allLines ) );
        if( $debug ) file_put_contents('formatedLines.txt', implode( PHP_EOL, $formatedLines ) );

        //Datenbank anlegen
        //writeLog( 'getKBACars ausgeführt');

        //// test with SELECT * FROM carskba WHERE klasse ILIKE '%11%';
        $sql = 'DROP TABLE IF EXISTS carskbatmp';
        echo $GLOBALS['dbh']->query( $sql );
        $sql = 'CREATE TABLE carskbatmp(
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
        echo $GLOBALS['dbh']->query( $sql );
        $sql = "COPY carskbatmp( hsn, tsn, hersteller, marke, name, datum, klasse, aufbau, kraftstoff, leistung, hubraum, achsen, antrieb, sitze, masse )
                FROM '".__DIR__."/carskba.csv' DELIMITER '|' CSV";
        echo $GLOBALS['dbh']->query( $sql );

        $sql = 'DROP TABLE IF EXISTS carskba';
        echo $GLOBALS['dbh']->query( $sql );

        $sql = 'CREATE TABLE carskba AS TABLE carskbatmp WITH NO DATA';
        echo $GLOBALS['dbh']->query( $sql );

        $sql = 'INSERT INTO carskba SELECT BTRIM( hsn ), BTRIM( tsn ), BTRIM(hersteller ), BTRIM( marke ), BTRIM( name ), BTRIM( datum ), BTRIM( klasse ), BTRIM( aufbau ), BTRIM( kraftstoff ), BTRIM( leistung ), BTRIM( hubraum ), BTRIM( achsen ), BTRIM( antrieb ), BTRIM( sitze ), BTRIM( masse ) FROM carskbatmp';
        echo $GLOBALS['dbh']->query( $sql );

        $sql = 'DROP TABLE IF EXISTS carskbatmp';
        echo $GLOBALS['dbh']->query( $sql );
    }//funntion getKBA

    function getKBAtrailer(){
        //writeLog( __FUNCTION__ );

        //system( "wget 'https://www.kba.de/SharedDocs/Publikationen/DE/Fahrzeugtechnik/SV/sv45_pdf.pdf?__blob=publicationFile&v=23' -O sv45.pdf" );
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

        $sql = 'DROP TABLE IF EXISTS kbatrailertmp';
        echo $GLOBALS['dbh']->query( $sql );

        $sql = "CREATE TABLE kbatrailertmp(
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
        echo $GLOBALS['dbh']->query( $sql );

        $sql = "COPY kbatrailertmp( hsn, tsn, hersteller, marke, name, datum, klasse, aufbau, achsen, masse ) FROM '".__DIR__."/kbatrailer.csv' DELIMITER '|' CSV";
        echo $GLOBALS['dbh']->query( $sql );

        $sql = 'DROP TABLE IF EXISTS kbatrailer';
        echo $GLOBALS['dbh']->query( $sql );

        $sql = 'CREATE TABLE kbatrailer AS TABLE kbatrailertmp WITH NO DATA';
        echo $GLOBALS['dbh']->query( $sql );

        $sql = 'INSERT INTO kbatrailer SELECT BTRIM( hsn ), BTRIM( tsn ), BTRIM( hersteller ), BTRIM( marke ), BTRIM( name ), BTRIM( datum ), BTRIM( klasse ), BTRIM( aufbau ), BTRIM( achsen ), BTRIM( masse ) FROM kbatrailertmp';
        echo $GLOBALS['dbh']->query( $sql );

        $sql = 'DROP TABLE IF EXISTS kbatrailertmp';
        echo $GLOBALS['dbh']->query( $sql );

        echo 1;
    }

    function getKBAbike(){
        writeLog( __FUNCTION__ );
        $bikeTypesArray = array( 'KREIDLER FLORETT 50 XL;SM', 'SMC', 'KAESA SKY-5', 'RAM', 'KASEA SKY-5', 'F KART', 'CITY ATV', 'G KART', 'REX 50 SILVERSTAR,SILVERS', 'FLORETT 50', 'FLORETT 25', 'REXY 25', 'FLORY 25', 'SCOOTER,MK,CAPRIOLO,CLIPP', 'REXY 50', 'FLORY 50', 'SILVERSTAR,SILVERSTREET', 'M Kart', 'Quadzilla', 'SKY,BLAST,SL', 'City ATV', 'F Kart'  );
        system( "wget 'https://www.kba.de/SharedDocs/Publikationen/DE/Fahrzeugtechnik/SV/sv41_pdf.pdf?__blob=publicationFile&v=25' -O sv41.pdf" );
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


        $sql = 'DROP TABLE IF EXISTS kbabikestmp';
        echo $GLOBALS['dbh']->query( $sql );
        $sql = 'CREATE TABLE kbabikestmp(
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
        echo $GLOBALS['dbh']->query( $sql );
        $sql = "COPY kbabikestmp( hsn, tsn, hersteller, marke, name, datum, klasse, aufbau, kraftstoff, leistung, hubraum, achsen, antrieb, sitze, masse )
                FROM '".__DIR__."/kbabikes.csv' DELIMITER '|' CSV";
        echo $GLOBALS['dbh']->query( $sql );

        $sql = 'DROP TABLE IF EXISTS kbabikes';
        echo $GLOBALS['dbh']->query( $sql );

        $sql = 'CREATE TABLE kbabikes AS TABLE kbabikestmp WITH NO DATA';
        echo $GLOBALS['dbh']->query( $sql );

        $sql = 'INSERT INTO kbabikes SELECT BTRIM( hsn ), BTRIM( tsn ), BTRIM(hersteller ), BTRIM( marke ), BTRIM( name ), BTRIM( datum ), BTRIM( klasse ), BTRIM( aufbau ), BTRIM( kraftstoff ), BTRIM( leistung ), BTRIM( hubraum ), BTRIM( achsen ), BTRIM( antrieb ), BTRIM( sitze ), BTRIM( masse ) FROM kbabikestmp';
        echo $GLOBALS['dbh']->query( $sql );

        $sql = 'DROP TABLE IF EXISTS kbabikestmp';
        echo $GLOBALS['dbh']->query( $sql );


        echo 1;
    }

    function getKBAtruck(){
        writeLog( __FUNCTION__ );
        $truckTypesArray = array( 'Algema/Crafter Blitzlader', 'Fitzel Speeder T5, 46-20', 'PanelVAN', 'AMAROK', 'TIGUAN', 'PASSAT', 'VWUP!', 'Crafter', 'SHARAN', 'TOURAN', 'Transporter', 'POLO' );
        //system( "wget 'https://www.kba.de/SharedDocs/Publikationen/DE/Fahrzeugtechnik/SV/sv43_pdf.pdf?__blob=publicationFile&v=24' -O sv43.pdf" );
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
            if( substr( $value, 74, 12 ) && !ctype_space( substr( $value, 74, 8 ) ) ){ //sind Kfz-Typen zu weit nach lins verschoben?
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

        $sql = 'DROP TABLE IF EXISTS kbatruckstmp';
        echo $GLOBALS['dbh']->query( $sql );
        $sql = 'DROP TABLE IF EXISTS kbatruckstmp';
        echo $GLOBALS['dbh']->query( $sql );
        $sql = 'CREATE TABLE kbatruckstmp(
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
        echo $GLOBALS['dbh']->query( $sql );
        $sql = "COPY kbatruckstmp( hsn, tsn, hersteller, marke, name, datum, klasse, aufbau, kraftstoff, leistung, hubraum, achsen, antrieb, sitze, masse )
                FROM '".__DIR__."/kbatrucks.csv' DELIMITER '|' CSV";
        echo $GLOBALS['dbh']->query( $sql );

        $sql = 'DROP TABLE IF EXISTS kbatrucks';
        echo $GLOBALS['dbh']->query( $sql );

        $sql = 'CREATE TABLE kbatrucks AS TABLE kbatruckstmp WITH NO DATA';
        echo $GLOBALS['dbh']->query( $sql );

        $sql = 'INSERT INTO kbatrucks SELECT BTRIM( hsn ), BTRIM( tsn ), BTRIM(hersteller ), BTRIM( marke ), BTRIM( name ), BTRIM( datum ), BTRIM( klasse ), BTRIM( aufbau ), BTRIM( kraftstoff ), BTRIM( leistung ), BTRIM( hubraum ), BTRIM( achsen ), BTRIM( antrieb ), BTRIM( sitze ), BTRIM( masse ) FROM kbatruckstmp';
        echo $GLOBALS['dbh']->query( $sql );

        $sql = 'DROP TABLE IF EXISTS kbatruckstmp';
        echo $GLOBALS['dbh']->query( $sql );


        echo 1;
    }

    function getKBATractor(){
        writeLog( __FUNCTION__ );
        //system( "wget 'https://www.kba.de/SharedDocs/Publikationen/DE/Fahrzeugtechnik/SV/sv44_pdf.pdf?__blob=publicationFile&v=24' -O sv44.pdf" );
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

        file_put_contents( 'kbatractors.csv', implode( PHP_EOL, $allLines ) );

        $sql = 'DROP TABLE IF EXISTS kbatractorstmp';
        echo $GLOBALS['dbh']->query( $sql );
        $sql = 'DROP TABLE IF EXISTS kbatractorstmp';
        echo $GLOBALS['dbh']->query( $sql );
        $sql = 'CREATE TABLE kbatractorstmp(
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
        echo $GLOBALS['dbh']->query( $sql );
        $sql = "COPY kbatractorstmp( hsn, tsn, hersteller, marke, name, datum, klasse, aufbau, kraftstoff, leistung, hubraum, achsen, antrieb, sitze, masse )
                FROM '".__DIR__."/kbatractors.csv' DELIMITER '|' CSV";
        echo $GLOBALS['dbh']->query( $sql );

        $sql = 'DROP TABLE IF EXISTS kbatractors';
        echo $GLOBALS['dbh']->query( $sql );

        $sql = 'CREATE TABLE kbatractors AS TABLE kbatractorstmp WITH NO DATA';
        echo $GLOBALS['dbh']->query( $sql );

        $sql = 'INSERT INTO kbatractors SELECT BTRIM( hsn ), BTRIM( tsn ), BTRIM(hersteller ), BTRIM( marke ), BTRIM( name ), BTRIM( datum ), BTRIM( klasse ), BTRIM( aufbau ), BTRIM( kraftstoff ), BTRIM( leistung ), BTRIM( hubraum ), BTRIM( achsen ), BTRIM( antrieb ), BTRIM( sitze ), BTRIM( masse ) FROM kbatractorstmp';
        echo $GLOBALS['dbh']->query( $sql );

        $sql = 'DROP TABLE IF EXISTS kbatractorstmp';
        echo $GLOBALS['dbh']->query( $sql );


        echo 1;
    }
?>
