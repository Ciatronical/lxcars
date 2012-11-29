<?php
// $Id$
	require_once("../inc/stdLib.php");
	include_once("../inc/crmLib.php");
	$menu =  $_SESSION['menu'];
	
?>
<html>
<head><title></title>
<?php echo $menu['stylesheets']; ?>
<link type="text/css" REL="stylesheet" HREF="../css/<?php echo $_SESSION["stylesheet"]; ?>/main.css"></link>
<?php echo $menu['javascripts']; ?>
</head>
<body onLoad="document.suche.swort.focus()";>
<?php	
echo $menu['pre_content'];
echo $menu['start_content'];
$telnum=($_GET["telnum"])?$_GET["telnum"]:$_POST["telnum"];
if ($_POST["adress"]) {
	include("../inc/FirmenLib.php");
	include("../inc/persLib.php");
	include("../inc/UserLib.php");
	
	$msg="Leider nichts gefunden!";
	$viele="Zu viele Treffer. Bitte einschr&auml;nken.";
	$found=false;
	
	$suchwort=mkSuchwort($_POST["swort"]); 
	$anzahl=0;
	$db->debug=0;
	$keineFirma=0;
	$rsE=getAllUser($suchwort);
	if (chkAnzahl($rsE,$anzahl)) {
		$rsV=getAllFirmen($suchwort,true,"V");	
		if (chkAnzahl($rsV,$anzahl)) {
			$rsC=getAllFirmen($suchwort,true,"C");
			if (chkAnzahl($rsC,$anzahl)) {
				$rsK=getAllPerson($suchwort);
				if (!chkAnzahl($rsK,$anzahl)) {
					$msg=$viele;
				} else {
					if ($anzahl===0) {$msg="Kein Kunde gefunden! Suche Kennzeichen";$keineFirma=1;}
				} 
			} else {
				$msg=$viele;
			}
		} else {
			$msg=$viele;
		}
	} else {
			$msg=$viele;
	}
?>
<script language="JavaScript">
<!--
	function showD( src, id ){
		if      (src=="C") {	uri="../firma1.php?Q=C&id=" + id }
		else if (src=="V") {	uri="../firma1.php?Q=V&id=" + id; }
		else if (src=="E") {	uri="../user1.php?id=" + id; }
		else if (src=="K") {	uri="../kontakt.php?id=" + id; }
		location.href=uri;
	}
	function showCar( c_id ){
		if( c_id ){
			uri="lxcmain.php?task=3&c_id=" + c_id;
			location.href=uri;
		}
	}
//-->
</script>
<p class="listtop">Suchergebnis</p>
<?
	if ($anzahl>0) {
		echo "<table class=\"liste\">\n";
		echo "<tr class='bgcol3'><th>KD-Nr</th><th class=\"liste\">Name</th><th class=\"liste\">Anschrift</th><th class=\"liste\">Telefon</th><th></th></tr>\n";
		$i=0;
		if ($rsC) foreach($rsC as $row) {
			echo "<tr onMouseover=\"this.bgColor='#FF0000';\" onMouseout=\"this.bgColor='".$bgcol[($i%2+1)]."';\" bgcolor='".$bgcol[($i%2+1)]."' onClick='showD(\"C\",".$row["id"].");'>".
				"<td class=\"liste\">".$row["customernumber"]."</td><td class=\"liste\">".$row["name"]."</td>".
				"<td class=\"liste\">".$row["city"].(($row["street"])?",":"").$row["street"]."</td><td class=\"liste\">".$row["phone"]."</td><td class=\"liste\">K</td></tr>\n";
			$i++;
		}
		if ($rsV) foreach($rsV as $row) {
			echo "<tr onMouseover=\"this.bgColor='#FF0000';\" onMouseout=\"this.bgColor='".$bgcol[($i%2+1)]."';\" bgcolor='".$bgcol[($i%2+1)]."' onClick='showD(\"V\",".$row["id"].");'>".
				"<td class=\"liste\">".$row["vendornumber"]."</td><td class=\"liste\">".$row["name"]."</td>".
				"<td class=\"liste\">".$row["city"].(($row["street"])?",":"").$row["street"]."</td><td class=\"liste\">".$row["phone"]."</td><td class=\"liste\">L</td></tr>\n";
			$i++;
		}
		if ($rsK) foreach($rsK as $row) {
			echo "<tr onMouseover=\"this.bgColor='#FF0000';\" onMouseout=\"this.bgColor='".$bgcol[($i%2+1)]."';\" bgcolor='".$bgcol[($i%2+1)]."' onClick='showD(\"K\",".$row["cp_id"].");'>".
				"<td class=\"liste\">".$row["cp_id"]."</td><td class=\"liste\">".$row["cp_name"].", ".$row["cp_givenname"]."</td>".
				"<td class=\"liste\">".$row["addr2"].(($row["addr1"])?",":"").$row["addr1"]."</td><td class=\"liste\">".$row["cp_phone1"]."</td><td class=\"liste\">P</td></tr>\n";
			$i++;
		}
		if ($rsE) foreach($rsE as $row) {
			echo "<tr onMouseover=\"this.bgColor='#FF0000';\" onMouseout=\"this.bgColor='".$bgcol[($i%2+1)]."';\" bgcolor='".$bgcol[($i%2+1)]."' onClick='showD(\"E\",".$row["id"].");'>".
				"<td class=\"liste\">".$row["id"]."</td><td class=\"liste\">".$row["name"]."</td>".
				"<td class=\"liste\">".$row["addr2"].(($row["addr1"])?",":"").$row["addr1"]."</td><td class=\"liste\">".$row["workphone"]."</td><td class=\"liste\">U</td></tr>\n";
			$i++;
		}
		echo "</table>\n";
        } else {
		echo $msg;
	};
	echo "<br>";
} else if ($_POST["kontakt"]){
?>
<script language="JavaScript">
	sw="<?= $_POST["swort"]; ?>";
	if (sw != "") 
		F1=open("suchKontakt.php?suchwort="+sw+"&Q=S","Suche","width=400, height=400, left=100, top=50, scrollbars=yes");
</script>			

<? }
if($_POST['sauto']){
	?><script language="JavaScript">
	<!--
	function showD (src,id) {
		Frame=eval("parent.main_window");
		if      (src=="C") {	uri="firma1.php?Q=C&id=" + id }
		else if (src=="V") {	uri="firma1.php?Q=V&id=" + id; }
		else if (src=="E") {	uri="user1.php?id=" + id; }
		else if (src=="K") {	uri="kontakt.php?id=" + id; }
		Frame.location.href=uri;
	}
	function showCar (c_id){
		if (c_id) {
			Frame=eval("parent.main_window");
			uri="lxcmain.php?task=3&c_id=" + c_id;
			Frame.location.href=uri;
		}
	}
		
		//-->
	</script>
	<?
	//include("inc/FirmenLib.php");
	include("inc/lxcLib.php");
	$result=GetOwner($_POST['swort']);
	if ($result){
		echo "<table class=\"liste\">\n";
		echo "<tr class='bgcol3'><th>Kennzeichen</th><th class=\"liste\">Hersteller</th><th class=\"liste\">Fahrzeugtyp</th><th class=\"liste\">c_id</th><th class=\"liste\">Besitzer</th></tr>\n";
		foreach($result as $row) {
			echo 	"<tr onMouseover=\"this.bgColor='#0033FF';\"  onMouseout=\"this.bgColor='".$bgcol[($i%2+1)]."';\" bgcolor='".$bgcol[($i%2+1)]."'>".
					"<td onClick='showCar(".$row["c_id"].");' class=\"liste\" >".$row["c_ln"]."</td><td  onClick='showCar(".$row["c_id"].");' class=\"liste\">".$row["c_m"]."</td>".                                           
					"<td onClick='showCar(".$row["c_id"].");' class=\"liste\">".$row["c_t"]."</td><td class=\"liste\">".$row["c_id"]."</td><td onMouseover=\"this.bgColor='#0066FF';\" onMouseout=\"this.bgColor='".$bgcol[($i%2+1)]."';\" class=\"liste\" onClick='showD(\"C\",".$row["c_ow"].");'>".$row["owner"]."</td></tr>\n";
			$i++;
		}//end foreach
		echo "</table>\n";
	}//end if
	else {echo "!!!Kennzeichen nicht vergeben!!!";}
		//echo "Autosuche...".$_POST["sauto"];
}
if($keineFirma){
    include("lxcars/inc/lxcLib.php");
	$result=GetOwner($_POST['swort']);
	if ($result){
		echo "<table class=\"liste\">\n";
		echo "<tr class='bgcol3'><th>Kennzeichen</th><th class=\"liste\">Hersteller</th><th class=\"liste\">Fahrzeugtyp</th><th class=\"liste\">c_id</th><th class=\"liste\">Besitzer</th></tr>\n";
		foreach( $result as $row ){
			echo 	"<tr onMouseover=\"this.bgColor='#0033FF';\"  onMouseout=\"this.bgColor='".$bgcol[($i%2+1)]."';\" bgcolor='".$bgcol[($i%2+1)]."'>".
					"<td onClick='showCar(".$row["c_id"].");' class=\"liste\" >".$row["c_ln"]."</td><td  onClick='showCar(".$row["c_id"].");' class=\"liste\">".$row["c_m"]."</td>".                                           
					"<td onClick='showCar(".$row["c_id"].");' class=\"liste\">".$row["c_t"]."</td><td class=\"liste\">".$row["c_id"]."</td><td onMouseover=\"this.bgColor='#0066FF';\" onMouseout=\"this.bgColor='".$bgcol[($i%2+1)]."';\" class=\"liste\" onClick='showD(\"C\",".$row["c_ow"].");'>".$row["owner"]."</td></tr>\n";
			$i++;
		}
		echo "</table>\n";
	}//end if
	else {echo "!!!Kennzeichen nicht vergeben!!!";}
		//echo "Autosuche...".$_POST["sauto"];
}

$formular = '<p class="listtop">Schnellsuche Kunde/Lieferant/Kontakte und Kontaktverlauf <?php echo ($telnum)?"Telefonunummer: ".$telnum:""; ?></p>';
$formular .= '<form name="suche" action="lxcgetData.php?telnum='.$telnum.'</form>" method="post">';
$formular .= '<input type="text" name="swort" size="20">';  
$formular .= '<input type="submit" name="adress" value="Kunde o. Lief.">';
$formular .= '<input type="submit" name="sauto" value="Kennzeichen">';
$formular .= '<input type="submit" name="kontakt" value="Kontaktverlauf"> <br>';
$formular .= '<span class="liste">Suchbegriff</span></form>';
print $formular;
echo $menu['end_content'];	
?>	




