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

  require_once( "fpdf.php" );
  
  $sql = "SELECT * FROM lxc_cars WHERE c_id IN( ".implode( ',', $data )." )";
  //writeLog( $sql );
  $result = $GLOBALS['dbh']->getALL( $sql );

  $pdf = new FPDI( 'P','mm','A4' );
  $pdf->SetFont('Arial','B',16);
  $pdf->addPage();
  $pdf->setSourceFile( __DIR__.'/../serieslettertemplate.pdf' );
  $pdf->Cell(40,10,'Hallo Welt!');
  //$pdf->Output();
  $imp = $pdf->ImportPage(1);
  $pdf->useTemplate( $imp, 1, 1 );

  $pdf->OutPut( __DIR__.'/../test.pdf', 'F' );


  echo 1;
}

?>