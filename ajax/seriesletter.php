<?php
use setasign\Fpdi\Fpdi;
require_once __DIR__.'/../../inc/stdLib.php'; // for debug
require_once __DIR__.'/../../inc/crmLib.php';
require_once __DIR__.'/../inc/ajax2function.php';

function getData( $data ){
  $sql = "SELECT  c_id, c_hu, c_ln, name, street, zipcode, city FROM lxc_cars JOIN customer ON( lxc_cars.c_ow = customer.id ) WHERE EXTRACT( YEAR FROM c_hu ) = ".$data['year']." AND EXTRACT( MONTH FROM c_hu ) = ".$data['month']." + 1 AND zipcode != '00000'";
  echo $GLOBALS['dbh']->getALL( $sql, true );
}

function updateNotSelectedCars(){
  echo '1';
  //return 1;
}

function generatePdf( $data ){
  $date   = array_pop( $data );
  $button = array_pop( $data );
  $fileName = $date.'.pdf';
  unlink( __DIR__.'/../seriesLetter/'.$fileName );
  file_exists( __DIR__.'/../custom/data.php' ) ? require_once( __DIR__.'/../custom/data.php' ) : require_once( __DIR__.'/../default/data.php' );
  require_once( "fpdf.php" );
  //require_once( __DIR__.'/../../inc/ftpClient.php' );

  $sql = "SELECT c_id, c_hu, c_ln, greeting, name, street, zipcode, city FROM lxc_cars JOIN customer ON( lxc_cars.c_ow = customer.id ) WHERE c_id IN( ".implode( ',', $data )." )";
  $result = $GLOBALS['dbh']->getALL( $sql );

  class PDF extends FPDF{
    public $debug = 0;
    public $left = 20;
    public $head_margin_top = 20;
    public $footer = 260;
    public $mydata;
    function Header(){ //Head
      $logo = file_exists( __DIR__.'/../custom/logo.png' ) ? __DIR__.'/../custom/logo.png' : __DIR__.'/../default/LxCars-Logo.png';
      $this->Image( $logo, $this->left, $this->head_margin_top, 70 );
      $this->SetFont( 'Arial', '', 10 );
      $this->setXY( 160, $this->head_margin_top );
      $this->MultiCell( 50, 4, $this->mydata['headright'], $this->debug, 'L' );
      $this->SetFont( 'Arial', '', 5 );
      $this->setXY( $this->left, 62 ); //height address retur
      $this->Cell( 80, 3, $this->mydata['retur'], $this->debug, 'L' );
    }
    function Footer(){
      $this->SetFont( 'Arial','I', 8 );
      $this->setXY( $this->left, $this->footer );
      $this->MultiCell( 50, 4, utf8_decode( $this->mydata['footerleft'] ), $this->debug, 'L' );
      $this->setXY( $this->left + 60, $this->footer );
      $this->MultiCell( 50, 4, utf8_decode( $this->mydata['footermiddle'] ), $this->debug, 'L' );
      $this->setXY( $this->left + 120, $this->footer );
      $this->MultiCell( 50, 4, utf8_decode( $this->mydata['footerright'] ), $this->debug, 'L' );
    }
  }
  
  $pdf = new PDF();
  $pdf->mydata = $externaldata;
  foreach( $result as $customer ){
    switch( $customer['greeting'] ){
      case 'Herr' :  $salutation = $externaldata['male'];
        break;
      case 'Frau' : $salutation = $externaldata['female'];
        break;
      default : $salutation = $externaldata['other'];
    }
    $pdf->AddPage();
    $pdf->setXY( $pdf->left, 67 ); //height address block
    $pdf->SetFont( 'Arial', '', 11 );
    $pdf->MultiCell( 170, 5, $customer['name']."\n".$customer['street']."\n".$customer['zipcode']." ".$customer['city'], $pdf->debug, 'L' );
    // Betreff
    $pdf->SetXY( $pdf->left, 100 );
    $pdf->SetFont( 'Arial', 'B', 11 );
    $pdf->Cell( 0, 5, utf8_decode( $externaldata['subject0'].$customer['c_ln'].$externaldata['subject1'] ), 0, 1, 'L' );

    //Anrede
    $pdf->SetFont( 'Arial', '', 10 );
    $pdf->setXY( $pdf->left, 113 );
    $pdf-> Cell( 170, 5, utf8_decode( $salutation.$customer['name'].',' ), $pdf->debug, 'L' );
    
    //Text1
    $pdf->setXY( $pdf->left, 120 );
    $pdf-> MultiCell( 170, 4.5, utf8_decode( $externaldata['text0'].$customer['c_ln'].$externaldata['text1'] ), $pdf->debug, 'L' );

    //Angebot fett
    $pdf->SetFont( 'Arial', 'B', 10 );
    $pdf->setXY( $pdf->left, 140 );
    $pdf-> MultiCell( 170, 4.5, utf8_decode( $externaldata['text_fett'] ), $pdf->debug, 'L' );

    //text2
    $pdf->SetFont( 'Arial', '', 10 );
    $pdf->setXY( $pdf->left, 149 );
    $pdf-> MultiCell( 170, 4.5, utf8_decode( $externaldata['text2'] ), $pdf->debug, 'L' );

    //text3
    $pdf->setXY( $pdf->left, 162 );
    $pdf-> MultiCell( 170, 4.5, utf8_decode( $externaldata['text3'] ), $pdf->debug, 'L' );



    //text_klein
    $pdf->SetFont( 'Arial', '', 5 );
    $pdf->setXY( $pdf->left, 190 );
    $pdf-> MultiCell( 170, 4.5, utf8_decode( $externaldata['text_klein'] ), $pdf->debug, 'L' );

            //text_url
    $pdf->SetFont( 'Arial', '', 8 );
    $pdf->setXY( $pdf->left + 112, 232 );
    $pdf-> MultiCell( 170, 4.5, utf8_decode( $externaldata['text_url'] ), $pdf->debug, 'L' );



     // QR-Code Bild unten rechts einfügen
    $qrCodePath = __DIR__.'/../image/QR-Code-HU-AU-109Euro.png'; // Pfad zum QR-Code Bild
    $qrCodeWidth = 40;  // Breite des QR-Codes in mm
    $qrCodeHeight = 40; // Höhe des QR-Codes in mm
    $xPosition = $pdf->GetPageWidth() - $qrCodeWidth - 20; // x-Position unten rechts (20 mm Seitenrand)
    $yPosition = $pdf->GetPageHeight() - $qrCodeHeight - 65; // y-Position unten rechts (40 mm Seitenrand)

    $pdf->Image( $qrCodePath, $xPosition, $yPosition, $qrCodeWidth, $qrCodeHeight );
    //Verabschiedung
    //$pdf->setXY( $pdf->left, 180 );
    //$pdf-> MultiCell( 170, 5, utf8_decode( $externaldata['goodbye']."\n".$_SESSION['userConfig']['name'] ), $pdf->debug, 'L' );
    //update timestamp in lxc_cars
  }

  $pdf->Output( __DIR__.'/../seriesLetter/'.$fileName, "F" );
  $pdf->Output( __DIR__.'/../custom/seriesletter_'.date( 'F_Y' ).'.pdf',"F" );
  if( $button == 'sendPIN' ){
    $ftpDefaults = getDefaultsByArray( array( 'eletter_hostname', 'eletter_username', 'eletter_folder', 'eletter_passwd') );
    require_once( __DIR__.'/../../inc/sftpClient.php' );

    try{
      $sftp = new SFTPConnection( $ftpDefaults['eletter_hostname'], 22 );
      $sftp->login( $ftpDefaults['eletter_username'], $ftpDefaults['eletter_passwd'] );
      //$sftp->uploadFile( __DIR__.'/../testFile.txt', $ftpDefaults['eletter_folder'] );
      $srcFile = __DIR__.'/../seriesLetter/'.$fileName;
      $dstFile = '/'.$ftpDefaults['eletter_folder'].'/'.$fileName;
      //writeLogR( 'Src: '.$srcFile );
      //writeLogR( 'Dst: '.$dstFile );
      $sftp->uploadFile( $srcFile,  $dstFile );
    }
    catch( Exception $e ){
        writeLog( $e->getMessage() );
    }

  }//if

  echo 1;
}

?>