<?php

/* HU / AU Datum neu setzen */

require_once("./inc/lxcLib.php");
require_once("../inc/stdLib.php");

$heute = date("Y-m-d");
$datum = date('Y-m-d',strtotime($heute." +2 year"));

$sql="update lxc_cars SET c_hu = '".$datum."' WHERE c_id = '".$_POST['huau']."'";
$rc=$GLOBALS['dbh']->query ( $sql );

echo $datum;

?>