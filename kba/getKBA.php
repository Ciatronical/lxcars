<?php
    require_once __DIR__.'/../../inc/stdLib.php'; // for debug
    require_once __DIR__.'/../../inc/crmLib.php';
    require_once __DIR__.'/../inc/ajax2function.php';





    $debug = true;

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
        //system( "wget 'https://www.kba.de/SharedDocs/Publikationen/DE/Fahrzeugtechnik/SV/sv42_pdf.pdf?__blob=publicationFile&v=21' -O sv42.pdf" );

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
        writeLog( 'getKBACars ausgeführt');

        //// test with SELECT * FROM carskba WHERE klasse ILIKE '%11%';
        $sql = 'DROP TABLE IF EXISTS carskba';
        echo $GLOBALS['dbh']->query( $sql );
        $sql = 'CREATE TABLE carskba(
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

        $sql = "COPY carskba( hsn, tsn, hersteller, marke, name, datum, klasse, aufbau, kraftstoff, leistung, hubraum, achsen, antrieb, sitze, masse )
                FROM '/var/www/kivitendo-crm/lxcars/kba/carskba.csv' DELIMITER '|' CSV";
        echo $GLOBALS['dbh']->query( $sql );
    }//funntion getKBA

/*DROP TABLE IF EXISTS test;
CREATE TABLE test AS TABLE carskba WITH NO DATA;
INSERT INTO test SELECT BTRIM( hsn ), BTRIM( tsn ), BTRIM(hersteller ), BTRIM( marke ), BTRIM( name ), BTRIM( datum ), BTRIM( klasse ), BTRIM( aufbau ), BTRIM( kraftstoff ), BTRIM( leistung ), BTRIM( hubraum ), BTRIM( achsen ), BTRIM( antrieb ), BTRIM( sitze ), BTRIM( masse ) FROM carskba;
*/



?>
