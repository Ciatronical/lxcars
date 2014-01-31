<?php

include_once( "../inc/stdLib.php" );
include_once( "../inc/template.inc" );


$t = new Template( $base );
doHeader($t); 


$t->set_var( array( 'BASEPATH' => $_SESSION['basepath'], 'TEST' => '' ) );


    
$t->set_var($miscarray);						
$t->set_file(array("tpl-file" => "ebaysellerBearbeitung.tpl"));
$t->pparse("out",array("tpl-file"));

?>