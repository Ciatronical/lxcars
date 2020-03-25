<?php
echo "\n";
//system( "wget 'https://www.kba.de/SharedDocs/Publikationen/DE/Fahrzeugtechnik/SV/sv42_pdf.pdf?__blob=publicationFile&v=21' -O sv42.pdf" );
//system( 'pdftotext -layout sv42.pdf' );
$allLines = file( 'sv42.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
foreach( $allLines as $key => $value ){
    //if( $key == '44068' ){
        //echo substr( $value, 0, 200 );
        $goodLine = TRUE;
        // Prüfen ob die ersten vier Zeichen numerisch sind
        //echo "\n";
        if( !is_numeric( substr( $value, 0, 4) ) ) $goodLine = FALSE;
        $lba = unpack( "C*", $value ); // $LineBinarArray
        //9891         AAD       SMART                         smart                          fortwo coupe BRABUS
        //print_r( $lba );
        foreach( array_slice( $lba, 4, 9 ) as $firstPartValue ) if( $firstPartValue !=32 ) $goodLine = FALSE;
          //if( $lba['4'] == 32 && $lba['5'] == 32 && $lba['6'] == 32  && $lba['6'] == 32 && $lba['6'] == 32 && $lba['6'] == 32) echo "GoodLine";
        //echo "GoodLine: ".$goodLine."\n";  
    //}
    if( !$goodLine ) unset( $allLines[$key]);
}
foreach( $allLines as $key => $value ){
    $newString = substr_replace( $value, '"', 0, 0 ); 
    $newString = substr_replace( $newString, '";"', 5, 0 );
    $newString = substr_replace( $newString, '', 8, 9 );
    $newString = substr_replace( $newString, '";"', 11, 0 );
    $newString = substr_replace( $newString, '', 14, 7 );
    $newString = substr_replace( $newString, '";"', 44, 0 );


    echo substr( $newString, 0, 200 )."\n";
}
echo "\n\n";
//var_dump( $lines );
?>
