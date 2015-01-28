<?php
ob_start();//kann in neueren php-Versionen entfallen
include("../inc/stdLib.php");
$menu =  $_SESSION['menu'];
$head = mkHeader();
?>
<html>
<head><title></title>
<?php echo $menu['stylesheets']; ?>
<link type="text/css" REL="stylesheet" HREF="../css/<?php echo $_SESSION["stylesheet"]; ?>/main.css"></link>
<?php echo $menu['javascripts']; 
      echo $head['JQUERY']; 
	   echo $head['JQUERYUI']; 
	   echo $head['THEME'];
      echo $head['JQTABLE'];
      echo $head['JUI-DROPDOWN'];?>

</head>
<body onLoad="document.suche.mkbinput.focus()";>
        <?php echo $menu['pre_content']; ?>   
        <?php echo $menu['start_content']; ?> 
    <script language="JavaScript">
    <!--
    function showMot( motnr, mkbinput ){
        uri1="lxcmotSuche.php?motnr=" + motnr;
		uri2="&mkbinput=" + mkbinput;
		uri=uri1+uri2
		location.href=uri;
	}
//-->
</script>
<?php
/******************************************************************************
**** SucheMotor Zeigt eine Liste mit Motorkennbuchstaben an und zeigt nach ****
**** Auswahl die entstspechenden Daten an                                  ****
**** geschrieben von Ronny Kumke ronny@lxcars.de Artistic License 2        ****
******************************************************************************/
require_once("inc/lxcLib.php");

$owner = $_POST[owner] ? $_POST[owner] : $_GET[owner];
$c_id = $_POST[c_id] ? $_POST[c_id] : $_GET[c_id];
$mkbinput = $_POST[mkbinput] ? $_POST[mkbinput] : $_GET[mkbinput];
$mkbinput = strtoupper( $mkbinput );
$motnr = $_GET[motnr] ? $_GET[motnr] : $_POST[motnr];

if( $motnr ){
	$rs = lxc2db( "-MNT ".$motnr );
	include_once("../inc/template.inc");
	$t = new Template($base);
	$motData = array( "owner" => $owner, "c_id" => $c_id, "mkbinput" => $mkbinput, "motbezei" => $rs[0][0], "motnr" => $rs[0][1], "bjvon" => $rs[0][2], "bjbis" => $rs[0][3], "kwvon" => $rs[0][4], "kwbis" => $rs[0][5], "psvon" => $rs[0][6], "psbis" => $rs[0][7], "ventile" => $rs[0][8], "zyl" => $rs[0][9], "verdvon" => $rs[0][10], "verdbis" => $rs[0][11], "drehmvon" => $rs[0][12], "drehmbis" => $rs[0][13], "vhstvon" => $rs[0][14], "vhstbis" => $rs[0][15], "vhtevon" => $rs[0][16], "vhtebis" => $rs[0][17], "litstvon" => $rs[0][18], "litstbis" => $rs[0][19], "littevon" => $rs[0][20], "littebis" => $rs[0][21], "motverw" => $rs[0][22], "bauform" => $rs[0][23], "ksart" => $rs[0][24], "ksauf" => $rs[0][25], "turbo" => $rs[0][26], "umdrvon" => $rs[0][27], "umdrbis" => $rs[0][28], "umdrdvon" => $rs[0][29], "umdrdbis" => $rs[0][30], "kurbella" => $rs[0][31], "bohrung" => $rs[0][32], "hub" => $rs[0][33], "motart" => $rs[0][34], "abgnorm" => $rs[0][35], "motform" => $rs[0][36], "motsteu" => $rs[0][37], "ventsteu" => $rs[0][38], "kuehlart" => $rs[0][39], "vkbez" => $rs[0][40], "herstell" => $rs[0][41] );
	$t->set_var( $motData );						
	$t->set_file( array( "tpl-file" => "lxcmotSuche.tpl" ) );
	$t->pparse( "out", array( "tpl-file" ) );
}
else{
	$formular = '<p class="listtop">Suche nach Motoren</p>';
	$formular .= '<form name="suche" action="lxcmotSuche.php" method="post">';
	$formular .= '<input type="text" name="mkbinput" size="20" value="'.$mkbinput.'">';  
	$formular .= '<input type="submit" name="mkbform" value="Suche MKB"></form>';
	print $formular;

   if( $mkbinput )	$rs = lxc2db( "-M  \"".$mkbinput."\"" );
	$i = 0;
	if( $rs ){
		$mkbkurz = substr($mkbinput, 0, -1);
		if( $rs != -1 && !$rs[1][0] ) header("Location: lxcmotSuche.php?motnr=".$rs[0][1]."&mkbinput=".$mkbkurz."&owner=".$owner."&c_id=".$c_id);
		echo "<table class=\"liste\">\n";
		echo "<tr class='bgcol3'><th>MKB	&nbsp;&nbsp;&nbsp;</th><th class='bgcol3'> Hersteller &nbsp;&nbsp;&nbsp;</th><th class='bgcol3'> Peff in KW &nbsp;&nbsp;&nbsp;</th><th class='bgcol3'> Vh in ccm &nbsp;&nbsp;&nbsp;</th></tr>\n";
		if( $rs != -1 ){
			foreach( $rs as $row ){
				echo 	"<tr onMouseover=\"this.bgColor='#0033FF';\" onMouseout=\"this.bgColor='".$bgcol[($i%2+1)]."';\" bgcolor='".$bgcol[($i%2+1)]."' onClick='showMot(\"".$row[1]."\",\"".$mkbinput."\");'>".
						"<td class=\"liste\" >".$row[0]."</td><td class=\"liste\">".$row[41]."</td><td class=\"liste\">".$row[4]."</td><td class=\"liste\">".$row[10]."</td></tr>\n";
				$i++;
			}//end foreach
		}//endif
		echo "</table>\n";
	}//end if
}//else
?>
<?php echo $menu['end_content']; ?> 
</body>
<?php
ob_end_flush();//kann in neueren php-Versionen entfallen
?>
