<?php

    $debug = true;

    function strposArray( $haystack, $needle, $offset = 0 ){
      if( !is_array( $needle) ) return false;
      foreach( $needle as $query ){
          $result  = strpos( $haystack, $query, $offset );
          if( $result !== false ) return $result;
      }
      return false;
    }

    $carTypesArray = array( 'BEETLE', 'CALIFORNIA', 'CADDY', 'Club', 'Crafter', 'GOLF', 'EOS', 'FOX', 'Hymer', 'JETTA', 'KOMBI', 'PASSAT', 'PHAETON', 'POLO', 'SCIROCCO', 'SHARAN', 'TIGUAN', 'TOURAN', 'VWUP!', 'XL1', 'CC' );

    echo " \n";
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
        if( !ctype_space( substr( $value, 53, 2 ) ) ){ //Kfz-Typen sind vorhanden
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
        }
        //if( !ctype_space( substr( $value, 53, 2 ) ) ) $formatedLines[$key] = substr( $value, 52, 45 );//52,32
        //if( !ctype_space( substr( $value, 53, 2 ) ) ) $formatedLines[$key] = substr( $value, 74, 12 );//52,32
        //file_put_contents( 'mykba.txt', implode( PHP_EOL, $value ), FILE_APPEND );

        ////// also der Trick ist gezielt nach Fhz-Typen zu suchen, Fhz-Typen befinden sich in einem Array
        //////Zum debuggen soll die Position des Zeichens in der Zeile darüber stehen 1..\n  str_split ( string $string , int $length = 1 ) : array dann index zeigen

    }
    file_put_contents('mykba.txt', implode( PHP_EOL, $allLines ) );
    if( $debug ) file_put_contents('formatedLines.txt', implode( PHP_EOL, $formatedLines ) );
    echo "\n\n";
    //var_dump( $lines );
?>
