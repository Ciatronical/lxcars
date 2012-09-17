<!-- $Id$ -->
<html>
<head><title>Klassendaten  anzeigen von {ln} mit der ID {c_id}</title>
	<link type="text/css" REL="stylesheet" HREF="../../css/{ERPCSS}"></link>
	<link rel="stylesheet" type="text/css" href="./css/lxcjquery.autocomplete.css">	
	<link href="./css/lxcalert.css" rel="stylesheet" type="text/css" media="screen" />

	<script type="text/javascript" src="./inc/lxccheckfelder.js"></script>
	<script type="text/javascript" src="./inc/lxcjquery.js"></script>
	<script type="text/javascript" src="./inc/lxcjquery.autocomplete.js"></script>
<style type="text/css">

td.info1
{
position:relative;
z-index:1;

color:#2D006B;
text-decoration:none;
}

td.info1:hover
{
z-index:2;
background-color:#3466ee;
}

td.info1 span
{
display: none;
}

td.info1:hover span
{
display:block;
position:absolute;
top:60px;
left:300px;
width:600px;
height: 300px;
background: url(image/FahrzeugscheinZu2.gif);
  border-width:2px;
  border-style:solid;
  border-color:000;
}
</style>	
	
	
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





<form name="car" action="FhzKlasse.php?task=3&owner={owner}&c_id={c_id}" method="post" onSubmit="return checkfelder();">
<input type="hidden" name="owner" value="{owner}">
<input type="hidden" name="c_id" value="{c_id}">
<input type="hidden" name="c_t" value="{c_t}">
<input type="hidden" name="c_m" value="{c_m}">

<table>

<tr><td class="info1" href="#">HSN (2.1)<span></span></td><td><input tabindex="2" type="text" name="c_2" size="16" maxlength="4" value="{c_2}" title="Herstellerschlüssel aus dem Fahrzeugschein" readonly="readonly"></td><td>  Hersteller D.1</td><td><input tabindex="-1" type="text" size="16" value="{cm}" title="Fahrzeugmarkemarke" {readonly}></td></tr>
<tr><td>TSN (2.2)</td><td><input  tabindex="3" type="text" name="c_3" size="16" maxlength="9" value="{c_3}" title="Typschlüssel aus dem Fahrzeugschein" readonly="readonly"></td><td>  Typ/Variante D.2</td><td><input tabindex="-1" type="text" size="16" value="{ct}" title="Typ/Variante" {readonly}></td></tr>
<tr><td>Abgasschlüssel</td><td><input tabindex="4" type="text" name="c_em" size="16" maxlength="6" value="{c_em}" title="Fahrzeugschein Seite zwei, mitte,Feld 14" readonly="readonly"></td><td>  Bezeichnung D.3</td><td><input tabindex="-1" type="text" size="16" value="{vh}" title="Zylindervolumen" {readonly}></td></tr>
<tr><td>Hubraum in cm³ P.1 </td><td><input tabindex="6" type="text" name="c_hu" size="16" maxlength="10" value="{c_hu}" title="Stempel Fahrzeugscheinrückseite oder vom Fahrzeug ablesen" {readonly}></td><td>Leistung P.2</td><td><input tabindex="-1" type="text" size="16" value="{peff}" title="Leistung in KW" {readonly}></td></tr>
<tr><td>Anzahl der Zylinder</td><td><input tabindex="11" type="text" name="c_color" size="16" maxlength="22" value="{c_color}" title="Fahrzeugschein Seite 3, Feld R" {readonly}></td><td>Anzahl der Ventile</td><td title="Abstand zwischen den Achsen"><input tabindex="-1" type="text" size="16" value="{vent}" title="Anzahl der Ventile" {readonly}></td></tr>
<tr><td>Fahrzeugart 5  </td><td><input id="c_st" tabindex="13" type="text" name="c_st" size="16" value="{c_st}" title="Pkw,Lkw,Hänger"></td><td>Kraftstoffart P.3</td><td title="Benzin, Diesel, Gas"><input tabindex="-1" type="text" size="16" value="{ks}" title="Benzin, Diesel..." {readonly}"></td></tr>
<tr><td>Gesammtmasse G</td><td><input tabindex="16" type="text" name="c_wt" size="16" value="{c_wt}" title="Gesammtmasse"></td><td>Radstand</td><td title="Abstand zwischen den Achsen"><input tabindex="-1" type="text" size="16" value="{radstand}" title="Abstand zwischen den Achsen" {readonly}></td></tr>
<tr><td>Bereifung 15.1</td><td><input tabindex="15" type="text" name="c_st_l" size="16" value="{c_st_l}" title="Reifendimension"></td><td>Geschwindigkeit T</td><td><input tabindex="-1" type="text" size="16" value="{vmax}" title="H&ouml;chstgeschwindigkeit" {readonly}></td></tr>
<tr><td>bearbeitet am</td><td><input tabindex="18" type="text" name="c_wt_z" size="16" value="{c_wt_z}" title="Wann wurde der Datensatz zuletzt bearbeitet" readonly="readonly"></td><td>bearbeitet von</td><td><input tabindex="-1" type="text" size="16" value="{c_e_string}" title="dein Name" readonly="readonly"></td></tr>
</table>
<h4>Interne Bemerkungen</h4>
<table summary="Interne Bemerkungen">
<tr><td><textarea tabindex="19" name="c_text" cols="92" rows="5">{c_text}</textarea></td></tr>
</table>

<input id="speichern" tabindex="20" type="submit" name="update" value="speichern">&nbsp;&nbsp;&nbsp;
<input tabindex="21" type="button" name="close" onClick="myclose(document.car.owner.value);" value="schlie&szlig;en">&nbsp;&nbsp;&nbsp;


</form>
</left>

</body>
</html>