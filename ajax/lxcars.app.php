<?php

require_once __DIR__.'/../../inc/stdLib.php'; // for debug
require_once __DIR__.'/../../inc/crmLib.php';
require_once __DIR__.'/../inc/ajax2function.php';

function fastSearch($data)
{
	
	echo $GLOBALS['dbh']->getAll("SELECT c_ln, c_id, name FROM lxc_cars JOIN customer ON c_ow = id WHERE c_ln ilike '%".$_GET['term']."%' AND obsolete = false");
}

