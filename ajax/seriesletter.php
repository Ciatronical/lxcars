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
    $pdf->MultiCell( 170, 5, utf8_decode( $customer['name']."\n".$customer['street']."\n".$customer['zipcode']." ".$customer['city'] ), $pdf->debug, 'L' );
    $pdf->setXY( $pdf->left, 100 );
    $pdf-> Cell( 170, 5, utf8_decode( $externaldata['subject'].$customer['c_ln']   ), $pdf->debug, 'L' );
    $pdf->setXY( $pdf->left, 113 );
    $pdf-> Cell( 170, 5, utf8_decode( $salutation.$customer['name'].',' ), $pdf->debug, 'L' );
    $pdf->setXY( $pdf->left, 120 );
    $pdf-> MultiCell( 170, 5.6, utf8_decode( $externaldata['text0'].$customer['c_ln'].$externaldata['text1'] ), $pdf->debug, 'L' );
    $pdf->setXY( $pdf->left, 180 );
    $pdf-> MultiCell( 170, 5, utf8_decode( $externaldata['goodbye']."\n".$_SESSION['userConfig']['name'] ), $pdf->debug, 'L' );
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