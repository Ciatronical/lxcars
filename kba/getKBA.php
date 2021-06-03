<?php
    echo "\n";
    //system( "wget 'https://www.kba.de/SharedDocs/Publikationen/DE/Fahrzeugtechnik/SV/sv42_pdf.pdf?__blob=publicationFile&v=21' -O sv42.pdf" );
    system( 'pdftotext -layout sv42.pdf' );
    $allLines = file( 'sv42.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
    foreach( $allLines as $key => $value ){
        $goodLine = TRUE;

        if( !ctype_digit( substr( $value, 0, 4) ) ) $goodLine = FALSE;  //// Prüfen ob die ersten vier Zeichen numerisch sind
        if( !ctype_space(substr( $value, 4, 5 ) ) ) $goodLine = FALSE;  //// Prüft ob 5 Zeichen ein Leerzeichen ist
        if( !$goodLine ) unset( $allLines[$key] );
        //print_r( $allLines[$key] );
    }
    $formatedLines = array();
    foreach( $allLines as $key => $value ){
        //if(  !ctype_space( substr( $value, 52, 1 ) ) ) $value = substr_replace( $value, ' ', 52 );
        //if( !ctype_space( substr( $value, 53, 2 ) ) ) echo( substr( $value, 53, 26 )." key: ".$key."\n");
        // Submarke substr( $value, 52, 18 );
        if( !ctype_space( substr( $value, 53, 2 ) ) ) $formatedLines[$key] = substr( $value, 52, 32 );
        //file_put_contents( 'mykba.txt', implode( PHP_EOL, $value ), FILE_APPEND );
    }
    file_put_contents('mykba.txt', implode(PHP_EOL, $allLines ) );
    file_put_contents('formatedLines.txt', implode(PHP_EOL, $formatedLines ) );
    echo "\n\n";
    //var_dump( $lines );
?>
