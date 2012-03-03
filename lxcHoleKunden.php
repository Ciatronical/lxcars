<?php
include("inc/lxcLib.php");
require_once("inc/stdLib.php");
include_once("inc/crmLib.php");

$suchwort = mkSuchwort( "%".$_GET['q'] );
$rsC = getAllFirmen( $suchwort, true,"C" );
foreach( $rsC as $key => $value ){
		echo $value['name'].' -> '.$value['city']."\n";
}
?>