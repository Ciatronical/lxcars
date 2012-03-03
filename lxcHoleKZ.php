<?php
//lxcHoleKZ.php
include("inc/lxcLib.php");

	//echo "IF= TRUE";
	$sql = "SELECT c_ln, name FROM lxc_cars JOIN customer ON c_ow = id WHERE c_ln ilike '%".$_GET['q']."%'";
	$rs = $db->getAll( $sql );
	//print_r( $rs );
	foreach( $rs as $value ){
			echo $value['c_ln'].' -> '.$value['name']."\n";
	}



?>