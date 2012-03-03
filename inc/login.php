<?php
while( list($key,$val) = each($_SESSION) ) {
	unset($_SESSION[$key]);
};
clearstatcache();
if (substr(getcwd(),-6)=="lxcars") {
	chdir('..');
	$lxcars = true;	
}
if ($_POST["erpname"]) {
	if (is_file("../".$_POST["erpname"]."/config/lx_office.conf")) {
		if (is_writable("inc/conf.php")) {
			$name=false;
			$configfile=file("inc/conf.php");
			$f=fopen("inc/conf.php","w");
			foreach($configfile as $row) {
				$tmp=trim($row);
				if (preg_match('/ERPNAME/',$tmp)) {
					fputs($f,'$ERPNAME="'.$_POST["erpname"]."\";\n");
					$name=true;
				} else {
					if (preg_match('/\?>/',$tmp) && !$name) fputs($f,'$ERPNAME="'.$_POST["erpname"].'";'."\n");
					fputs($f,$tmp."\n");
				}
			}
			fclose($f);
		} else {
			echo "inc/conf.php ist nicht beschreibbar";
		}
	}
	$ERPNAME=$_POST["erpname"];
}

if (substr(getcwd(),-3)=="inc") {
    $conffile="../../$ERPNAME/config/lx_office.conf";
} else {
    $conffile="../$ERPNAME/config/lx_office.conf";
}

//$conffile="/usr/lib/lx-office-erp/config/lx_office.conf";
/*if (!$login) {
	header("location: ups.html");
} else */
if (is_file($conffile)) {
	$tmp=anmelden();
	if ($tmp) {
		if (chkVer()) {
			$db=$_SESSION["db"];
			$_SESSION["loginok"]="ok";
			if (file_exists("crmajax/xajax/xajax.inc.php")) {
				$_SESSION["xajax"]='023';
				define("XajaxVer","");
				define("XajaxPath","./crmajax/xajax");
			} else {
				$_SESSION["xajax"]="05";
				define("XajaxVer","05");
				define("XajaxPath","./crmajax/");
			}
            $LOGIN=True;
            require ("update_neu.php");
		} else {
			echo "db-Version nicht ok";
			exit;
		}
	} else {
		echo $_SESSION["db"]." nicht erreichbar.";
		exit;
	}
} else {
	echo "Configfile nicht gefunden<br>$PHPSELF<br>";
	echo "Lx-Office ERP V 2.6.0 oder gr&ouml;&szlig;er erwartet!!!<br><br>";
	echo "<form name='erppfad' method='post' action='".$PHPSELF."'>";
	echo "Bitte den Verzeichnisnamen (nicht den Pfad) der ERP eingeben:<br>";
	echo "<input type='text' name='erpname'>";
	echo "<input type='submit' name='saveerp' value='sichern'>";
	echo "</form>";
	exit;
}
if ($lxcars) {
	chdir('lxcars');
}
?>
