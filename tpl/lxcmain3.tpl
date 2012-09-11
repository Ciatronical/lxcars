<!-- $Id$ -->
<html>
<head><title>Fahrzeudaten  anzeigen von {ln} mit der ID {c_id}</title>
	<link type="text/css" REL="stylesheet" HREF="../css/{ERPCSS}"></link>
	<link rel="stylesheet" type="text/css" href="./css/lxcjquery.autocomplete.css">	
	<link href="./css/lxcalert.css" rel="stylesheet" type="text/css" media="screen" />

	<script type="text/javascript" src="./inc/lxccheckfelder.js"></script>
	<script type="text/javascript" src="./inc/lxcjquery.js"></script>
	<script type="text/javascript" src="./inc/lxcjquery.autocomplete.js"></script>
	{xajax_out}
	<script language="JavaScript">
	<!--
	$(function(){
		var owner = '1';
		$("#ac1").autocomplete({
			url: 'lxc_ac.php',
			inputClass: 'acInputOwner',
			extraParams: { owner: owner },
			maxItemsToShow: 9,
			minChars: 3,
			onItemSelect: function(){
				$("#speichern").focus(); 
			}
		});
	});
	$(function(){
		var g_art = '1';
		$("#ac2").autocomplete({
			url: 'lxc_ac.php',
			inputClass: 'acInputG_art',
			extraParams: { g_art: g_art },
			onItemSelect: function(){
				$("#c_st").focus(); 
			}
		});
	});			 
	function call_lxc_auf( owner,c_id,a_id ){
		Frame=eval("parent.main_window");
		uri1="lxcauf.php?owner=" + owner;
		uri2="&c_id=" + c_id;
		uri3="&task=3"
		uri4="&a_id=" + a_id;
		uri=uri1+uri2+uri3+uri4;
		location.href=uri;
	}		
	//-->
	</script>
<style type="text/css">
	#mann { position:absolute; top:60px; right:15px; border:1px solid #000;  }
	#bmw { position:absolute; top:130px; right:15px; border:1px solid #000;  }
</style>

</head>
<body onload="checkhu(document.car.c_hu.value)">
<left>




<div id="mann">
<img src="image/lxcMann.gif" width="65" height="55" alt="Mann"  onclick="mann(document.car.c_2.value, document.car.c_3.value);">
</div>

<div id="bmw">
<img src="image/lxcBMW.jpg" width="65" height="55" alt="BMW"  onclick="bmw(document.car.c_2.value, document.car.fin.value);"></div>
<form name="car" action="lxcmain.php?task=3&owner={owner}&c_id={c_id}" method="post" onSubmit="return checkfelder();">
<input type="hidden" name="owner" value="{owner}">
<input type="hidden" name="c_id" value="{c_id}">
<input type="hidden" name="c_t" value="{c_t}">
<input type="hidden" name="c_m" value="{c_m}">

<table>
<tr><td>Kennzeichen</td><td><input tabindex="1" type="text" name="c_ln" size="12" maxlength="9" value="{c_ln}" title="Kennzeichen!" onchange="xajax_UniqueKz(this.value,document.car.c_id.value)" {readonly} ><input tabindex="-1" type="checkbox" name="chk_c_ln" value="true" {chk_c_ln} title="Eingabe prüfen"><input type="button" name="Info" value="Info" onclick="kz_to_lks(document.car.c_ln.value);"></td><td>Besitzer:</td><td> <input tabindex="23" type="text" name="chown" size="22" value="{ownerstring}" title="Fahrzeughalter" id="ac1" autocomplete="off" {readonly}></td></tr>
<tr><td>HSN (2.1)</td><td><input tabindex="2" type="text" name="c_2" size="12" maxlength="4" value="{c_2}" title="Herstellerschlüssel aus dem Fahrzeugschein" {readonly}><input tabindex="-1" type="checkbox" name="chk_c_2" value="true" {chk_c_2}  title="Eingabe prüfen"></td><td>Hersteller:</td><td><input tabindex="-1" type="text" size="29" value="{cm}" title="Automarke" readonly="readonly"></td></tr>
<tr><td>TSN (2.2)</td><td><input  tabindex="3" type="text" name="c_3" size="12" maxlength="9" value="{c_3}" title="Typschlüssel aus dem Fahrzeugschein" {readonly} ><input tabindex="-1" type="checkbox" name="chk_c_3" value="true" {chk_c_3} title="Eingabe prüfen"></td><td>Typ:</td><td><input tabindex="-1" type="text" size="29" value="{ct}" title="Typ/Variante" readonly="readonly"></td></tr>
<tr><td>Emissionsklasse </td><td><input tabindex="4" type="text" name="c_em" size="12" maxlength="6" value="{c_em}" title="Fahrzeugschein Seite zwei, mitte,Feld 14" {readonly}><input tabindex="-1" type="checkbox" name="chk_c_em" value="true" {chk_c_em} title="Eingabe prüfen"><input type="button" name="Info" value="Info" onclick="feinstaub()"></td><td>Hubraum:</td><td><input tabindex="-1" type="text" size="29" value="{vh}" title="Zylindervolumen" readonly="readonly"></td></tr>
<tr><td>Datum Zulassung</td><td><input tabindex="5" type="text" name="c_d" size="12" maxlength="10" value="{c_d}" title="Fahrzeugschein Seite zwei, oben links, Feld B" {readonly}><input tabindex="-1" type="checkbox" name="chk_c_d" value="chk_c_d" checked="checked" readonly="readonly" title="Eingabe wird geprüft"></td><td>Bj. von - bis: </td><td><input tabindex="-1" type="text" size="29" value="{bj}" title="Zeitraum in dem das Fahrzeug produziert wurde" readonly="readonly"></td></tr>
<tr><td>Datum HU+AU</td><td><input tabindex="6" type="text" name="c_hu" size="12" maxlength="10" value="{c_hu}" title="Stempel Fahrzeugscheinrückseite oder vom Fahrzeug ablesen" {readonly}><input tabindex="-1" type="checkbox" name="chk_c_hu" value="chk_c_hu" {chk_c_hu} title="Fälligkeit der HU wird geprüft"></td><td>Leistung:</td><td><input tabindex="-1" type="text" size="29" value="{peff}" title="Pferdchen" readonly="readonly"></td></tr>
<tr><td>FIN+Pr&uuml;fziffer </td><td><input tabindex="7" type="text" name="fin" size="24" maxlength="17" value="{fin}" title="Fahrzeugschein Seite 2 und im Fhz" onchange="xajax_UniqueFin(this.value,document.car.c_id.value)" {readonly}><input tabindex="8" type="text" name="cn" size="1" maxlength="1" value="{cn}" title="Fahrzeugschein Seite 2, Feld 3 (falls unbekannt - eingeben)" {readonly}><input tabindex="-1" type="checkbox" name="chk_fin" value="true" {chk_fin} title="Eingabe prüfen"></td><td>Drehmoment:</td><td><input tabindex="-1" type="text" size="29" value="{drehm}" title="falls bekannt" readonly="readonly"></td></tr>
<tr><td>Motorcode</td><td><input tabindex="9" type="text" name="mkb" size="12" maxlength="22" value="{mkb}" title="Steht meist auf dem Motor"  {readonly}>{mkbdrop} <input type="button" name="InfoMotor" value="Info" onclick="zeigeMotor(document.car.owner.value, document.car.c_id.value, document.car.mkb.value);"></td><td>Verdichtung:</td><td title="Kompressionsverh&auml;ltnis"><input tabindex="-1" type="text" size="29" value="{verd}" title="Kompressionsverh&auml;ltnis" readonly="readonly"></td></tr>
<tr><td>Farbnummer</td><td><input tabindex="11" type="text" name="c_color" size="12" maxlength="22" value="{c_color}" title="Fahrzeugschein Seite 3, Feld R" {readonly}></td><td>Ventile:</td><td title="Abstand zwischen den Achsen"><input tabindex="-1" type="text" size="29" value="{vent}" title="Anzahl der Ventile" readonly="readonly"></td></tr>
<tr><td>Getriebeart</td><td><input id="ac2" tabindex="12" type="text" name="c_gart" size="12" value="{c_gart}" title="Schalter, Automatik, DSG" autocomplete="off" {readonly}>{g_art_drop}</td><td>Zylinder:</td><td><input tabindex="-1" type="text" size="29" value="{zyl}" title="Anzahl der Zylinder" readonly="readonly"></td></tr>
<tr><td>Sommerr&auml;der</td><td><input id="c_st" tabindex="13" type="text" name="c_st" size="12" value="{c_st}" title="Vom Fahrzeug ablesen Format: 185-65R14 88H"></td><td>Kraftstoffart / Inhalt:</td><td title="Abstand zwischen den Achsen"><input tabindex="-1" type="text" size="29" value="{ks}" title="Benzin, Diesel..." readonly="readonly"></td></tr>
<tr><td>Winterr&auml;der</td><td><input tabindex="14" type="text" name="c_wt" size="12" value="{c_wt}" title="Vom Fahrzeug ablesen Format: 175-70R14 82T"></td><td>Radstand:</td><td title="Abstand zwischen den Achsen"><input tabindex="-1" type="text" size="29" value="{radstand}" title="Abstand zwischen den Achsen" readonly="readonly"></td></tr>
<tr><td>LO Sommerr&auml;der</td><td><input tabindex="15" type="text" name="c_st_l" size="12" value="{c_st_l}" title="Lagerort der Sommerreifen, z.B. B1D4"></td><td>Vmax:</td><td><input tabindex="-1" type="text" size="29" value="{vmax}" title="H&ouml;chstgeschwindigkeit" readonly="readonly"></td></tr>
<tr><td>LO Winterr&auml;der</td><td><input tabindex="16" type="text" name="c_wt_l" size="12" value="{c_wt_l}" title="Lagerort der Winterreifen, z.B. B4D1"></td><td>Gesamtgewicht:  </td><td><input tabindex="-1" type="text" size="29" value="{mmax}" title="Fahrzeugschein Seite 3, oben Feld G" readonly="readonly"></td></tr>
<tr><td>Zustand Sommerreifen</td><td><input tabindex="17" type="text" name="c_st_z" size="12" value="{c_st_z}" title="gut, mittel, schlecht oder Profiltiefe angeben"></td><td>bearbeitet am:</td><td><input tabindex="-1" type="text" size="29" value="{mdate}" title="Datum und Zeit" readonly="readonly"></td></tr>
<tr><td>Zustand Winterreifen</td><td><input tabindex="18" type="text" name="c_wt_z" size="12" value="{c_wt_z}" title="gut, mittel, schlecht oder Profiltiefe angeben"></td><td>bearbeitet von:</td><td><input tabindex="-1" type="text" size="29" value="{c_e_string}" title="dein Name" readonly="readonly"></td></tr>
</table>
<h4>Interne Bemerkungen</h4>
<table summary="Interne Bemerkungen">
<tr><td><textarea tabindex="19" name="c_text" cols="92" rows="5">{c_text}</textarea></td></tr>
</table>

<input id="speichern" tabindex="20" type="submit" name="update" value="speichern">&nbsp;&nbsp;&nbsp;
<input tabindex="21" type="button" name="close" onClick="myclose(document.car.owner.value);" value="schlie&szlig;en">&nbsp;&nbsp;&nbsp;
<input tabindex="22" type="button" name="auftrag" onClick="lxc_auf(document.car.c_id.value, document.car.owner.value,1);" value="   Auftrag   ">&nbsp;&nbsp;&nbsp;

</form>
</left>

</body>
</html>
