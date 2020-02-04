<?php
use setasign\Fpdi\Fpdi;
require_once __DIR__.'/../../inc/stdLib.php'; // for debug
require_once __DIR__.'/../../inc/crmLib.php';
require_once __DIR__.'/../inc/ajax2function.php';

function getData( $data ){
  $sql = "SELECT  c_id, c_hu, c_ln, name, street, zipcode, city FROM lxc_cars JOIN customer ON( lxc_cars.c_ow = customer.id ) WHERE EXTRACT( YEAR FROM c_hu ) = ".$data['year']." AND EXTRACT( MONTH FROM c_hu ) = ".$data['month']." + 1 AND zipcode != '00000'";
  echo $GLOBALS['dbh']->getALL( $sql, true );
}

function generatePdf( $data ){
  $debug = 1;

  file_exists( __DIR__.'/../custom/data.php' ) ? require_once( __DIR__.'/../custom/data.php' ) : require_once( __DIR__.'/../default/data.php' );

  require_once( "fpdf.php" );

  //$sql = "SELECT * FROM lxc_cars WHERE c_id IN( ".implode( ',', $data )." )";
  $sql = "SELECT c_id, c_hu, c_ln, name, street, zipcode, city FROM lxc_cars JOIN customer ON( lxc_cars.c_ow = customer.id ) WHERE c_id IN( ".implode( ',', $data )." )";
  //writeLog( $sql );
  $result = $GLOBALS['dbh']->getALL( $sql );



  //Mem Cell

  class PDF extends FPDF{
    public $debug = 1;
    public $left = 20;
    public $head_margin_top = 15;
    public $mydata;
    public $test = 'gsgsgg';
    //foreach( $data as $name => $value ) public { $name } = $value;
    function Header(){ //Head

      //$head_margin_top = 15;
      $logo = file_exists( __DIR__.'/../custom/logo.png' ) ? __DIR__.'/../custom/logo.png' : __DIR__.'/../default/LxCars-Logo.png';

      $this->Image( $logo, $this->left, $this->head_margin_top, 70 );

      $this->SetFont( 'Arial', '', 10 );
      $this->setXY( 160, $this->head_margin_top );
      $this->SetLineWidth( 0 );
      $this->MultiCell( 50, 4, $this->mydata['headright'], $this->debug, 'L' );
      $this->MultiCell( 50, 4, $this->test, $this->debug, 'L' );
    }

    // Fusszeile
    function Footer(){
      $this->SetFont('Arial','I',8);
      $this->SetY(-15);
      // Arial kursiv 8

      // Seitenzahl
      $this->Cell(0,10,'Seite ');
    }
  }

  $pdf = new PDF();
  $pdf->mydata = $externaldata;
  //$pdf->AddPage();
  $pdf->test = "tetsttst";

  writeLog( $pdf->mydata );

  foreach( $result as $customer ){
    //writelog( $customer );
    $pdf->AddPage();
    $pdf->setXY( $pdf->left, 40 );
    $pdf->SetFont( 'Arial', '', 7 );
    $pdf->Cell( 20, 10, $customer['name'] );
  }
  $pdf->Output( __DIR__.'/../seriesletter.pdf',"F" );

  echo 1;
}

?>