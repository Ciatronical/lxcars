<!-- $Id$ -->
<html>
<head><title>Typndaten  anzeigen von {ln} mit der ID {c_id}</title>
	<link type="text/css" REL="stylesheet" HREF="../../css/{ERPCSS}"></link>
	<link rel="stylesheet" type="text/css" href="./css/lxcjquery.autocomplete.css">	
	<link href="./css/lxcalert.css" rel="stylesheet" type="text/css" media="screen" />

	<script type="text/javascript" src="./inc/lxccheckfelder.js"></script>
	<script type="text/javascript" src="./inc/lxcjquery.js"></script>
	<script type="text/javascript" src="./inc/lxcjquery.autocomplete.js"></script>
    <link href="./css/Tooltip-pop-up-FhzSchein.css" rel="stylesheet" type="text/css" media="screen" />
	
	
	{xajax_out}
	<script language="JavaScript">
	<!--
	$(function(){
		var owner = '1';
		$("#ac1").autocomplete({
			url 'lxc_ac.php',
			inputClass 'acInputOwner',
			extraParams { owner owner },
			maxItemsToShow 9,
			minChars 3,
			onItemSelect function(){
				$("#speichern").focus(); 
			}
		});
	});
	$(function(){
		var g_art = '1';
		$("#ac2").autocomplete({
			url 'lxc_ac.php',
			inputClass 'acInputG_art',
			extraParams { g_art g_art },
			onItemSelect function(){
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


</head>
<body onload="checkhu(document.car.c_hu.value)">
<p class="listtop">{msg}</p>

<left>





<form name="car" action="FhzTyp.php" method="post" onSubmit="return checkfelder();">
<input type="hidden" name="owner" value="{owner}">
<input type="hidden" name="c_id" value="{c_id}">



<table>
<tr><td class="info infoleft FahrzeuscheinZu2">HSN (2.1)<span></span></td><td><input tabindex="2" type="text" name="hsn" size="16" maxlength="4" value="{hsn}" title="Herstellerschlüssel aus dem Fahrzeugschein" readonly="readonly"></td><td class="info inforight FahrzeuscheinHerst">  Hersteller D.1<span></span></td><td><input name="hersteller" tabindex="-1" type="text" size="16" value="{hersteller}" title="Fahrzeugmarkemarke" {readonly}></td></tr>
<tr><td class="info infoleft FahrzeuscheinZu3">TSN (2.2)<span></span></td><td><input  tabindex="3" type="text" name="tsn" size="16" maxlength="9" value="{tsn}" title="Typschlüssel aus dem Fahrzeugschein" readonly="readonly"></td><td class="info inforight FahrzeuscheinTyp">  Typ/Variante D.2<span></span></td><td><input name="typ" tabindex="-1" type="text" size="16" value="{typ}" title="Typ/Variante" {readonly}></td></tr>
<tr><td class="info infoleft FahrzeuscheinAbg">Abgasschlüssel<span></span></td><td><input tabindex="4" type="text" name="na_schad_klasse" size="16" maxlength="6" value="{abgas}" title="Fahrzeugschein Seite zwei, mitte,Feld 14" readonly="readonly"></td><td class="info inforight FahrzeuscheinBez">  Bezeichnung D.3<span></span></td><td><input name="bezeichung" tabindex="-1" type="text" size="16" value="{bezeichung}" title="Zylindervolumen" {readonly}></td></tr>
<tr><td class="info infoleft FahrzeuscheinHub">Hubraum in cm³ P.1 <span></span></td><td><input tabindex="6" type="text" name="hubraum_ccm" size="16" maxlength="10" value="{hubraum_ccm}" title="Stempel Fahrzeugscheinrückseite oder vom Fahrzeug ablesen" {readonly}></td><td class="info inforight FahrzeuscheinLeist">Leistung P.2<span></span></td><td><input name="leistung_drehz" tabindex="-1" type="text" size="16" value="{leistung_drehz}" title="Leistung in KW" {readonly}></td></tr>
<tr><td>Anzahl der Zylinder</td><td><input tabindex="11" type="text" name="zylinder" size="16" maxlength="22" value="{zylinder}" title="Fahrzeugschein Seite 3, Feld R" {readonly}></td><td>Anzahl der Ventile</td><td title="Abstand zwischen den Achsen"><input name="ventile" tabindex="-1" type="text" size="16" value="{ventile}" title="Anzahl der Ventile" {readonly}></td></tr>
<tr><td class="info infoleft FahrzeuscheinArt">Fahrzeugart 5  <span></span></td><td><input id="c_st" tabindex="13" type="text" name="klasse_aufbau" size="16" value="{klasse_aufbau}" title="Pkw,Lkw,Hänger"></td><td class="info inforight FahrzeuscheinKraftstoff">Kraftstoffart P.3<span></span></td><td  title="Benzin, Diesel, Gas"><input name="kraftstoff" tabindex="-1" type="text" size="16" value="{kraftstoff}" title="Benzin, Diesel..." {readonly}"></td></tr>
<tr><td class="info infoleft FahrzeuscheinMasse">Gesammtmasse G<span></span></td><td><input tabindex="16" type="text" name="masse_leer" size="16" value="{masse_leer}" title="Gesammtmasse"></td><td>Radstand</td><td title="Abstand zwischen den Achsen"><input name="radstand" tabindex="-1" type="text" size="16" value="{radstand}" title="Abstand zwischen den Achsen" {readonly}></td></tr>
<tr><td class="info infoleft FahrzeuscheinReifen">Bereifung 15.1<span></span></td><td><input name="bereifung_achs1" tabindex="15" type="text" name="c_st_l" size="16" value="{bereifung_achs1}" title="Reifendimension"></td><td class="info inforight FahrzeuscheinVmax">Geschwindigkeit T<span></span></td><td><input name="geschwindigkeit" tabindex="-1" type="text" size="16" value="{geschwindigkeit}" title="H&ouml;chstgeschwindigkeit" {readonly}></td></tr>
<tr><td>bearbeitet am</td><td><input tabindex="18" type="text" size="16" value="{bearbeitet_am}" title="Wann wurde der Datensatz zuletzt bearbeitet" readonly="readonly"></td><td>bearbeitet von</td><td><input tabindex="-1" type="text" size="16" value="{emp}" title="dein Name" readonly="readonly"></td></tr>
</table>
<h4>Interne Bemerkungen</h4>
<table summary="Interne Bemerkungen">
<tr><td><textarea tabindex="19" name="bemerkungen" cols="92" rows="5">{bemerkungen}</textarea></td></tr>
</table>

<input id="speichern" tabindex="20" type="submit" name="update" value="speichern">&nbsp;&nbsp;&nbsp;
<input tabindex="21" type="button" name="close" onClick="typclose(document.car.owner.value,document.car.c_id.value,3);" value="schlie&szlig;en">&nbsp;&nbsp;&nbsp;


</form>
</left>

</body>
</html>