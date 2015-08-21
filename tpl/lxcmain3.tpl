<!-- $Id$ -->
<html>
<head><title>Fahrzeudaten  anzeigen von {ln} mit der ID {c_id}</title>
{STYLESHEETS}
<link href="./css/Tooltip-pop-up-FhzSchein.css" rel="stylesheet" type="text/css" media="screen" />
<script type="text/javascript" src="./inc/lxccheckfelder.js"></script>
{JAVASCRIPTS}
{CRMCSS}
{JQUERY}
{JQUERYUI}
<script type="text/javascript" src="{BASEPATH}crm/lxcars/jQueryAddOns/german-date-time-picker.js"></script>

{THEME}

<script language="JavaScript">
    $(function() {
        $("#ac1").autocomplete({                          
            source: "lxc_ac.php?case=owner",                            
            minLength: '3',                            
            delay: '0',
            select: function(e,ui) {
                $("#speichern").focus();
            }
        });
    });
    $(function() {
        $("#ac2").autocomplete({                          
            source: "lxc_ac.php?case=g_art",                                                        
            delay: '0',
            select: function(e,ui) {
                $("#c_st").focus();
            }
        });
    });	
    /*	 
	function call_lxc_auf( owner,c_id,a_id ){
		Frame=eval("parent.main_window");
		uri1="lxcauf.php?owner=" + owner;
		uri2="&c_id=" + c_id;
		uri3="&task=3"
		uri4="&a_id=" + a_id;
		uri=uri1+uri2+uri3+uri4;
		location.href=uri;
	}	
	*/
    $(function() {
        $("#c_d").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange: "-20:-0" ,
            dateFormat: "dd.mm.yy"
        });
        
    });	
	$(function() {
        $("#c_hu").datepicker({
            changeMonth: true,
            changeYear: true,
            changeDay: false,
            minDate: '+0m -1y',
            maxDate: '+0m +3y',
            dateFormat: "dd.mm.yy"
        });
    });	
    
$(function() {
    $( "#dialog" ).dialog({ autoOpen: false });
});
    
$(function() {
    $( "#hu_dialog" ).dialog({ autoOpen: false });
});

$(function() {
    $("#c_ln").change(UniqueKz);
});




</script>
<style type="text/css">
	#mann { position:absolute; top:60px; right:15px; border:1px solid #000;  }
	#bmw { position:absolute; top:130px; right:15px; border:1px solid #000;  }
</style>

</head>
<body onload="checkhu(document.car.c_hu.value)">
{PRE_CONTENT}
{START_CONTENT}
<p class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0.6em;">{msg}</p>

<div id="dialog" title="LxCars Fehler" >
  <p></p>
</div>
<div id="hu_dialog" title="Hauptuntersuchung fällig" >
  <p></p>
</div>
<left>
<div id="mann">
<img src="image/lxcMann.gif" width="65" height="55" alt="Mann"  onclick="mann(document.car.c_2.value, document.car.c_3.value);">
</div>

<div id="bmw">
<img src="image/lxcBMW.jpg" width="65" height="55" alt="BMW"  onclick="bmw(document.car.c_2.value, document.car.fin.value);">
</div>

<form name="car" id="car" action="lxcmain.php?task=3&owner={owner}&c_id={c_id}" method="post" onsubmit="return checkFelder();">
<input type="hidden" name="owner" value="{owner}">
<input type="hidden" name="c_id" id="c_id" value="{c_id}">
<input type="hidden" name="c_t" value="{c_t}">
<input type="hidden" name="c_m" value="{c_m}">

<table>
<tr><td>Kennzeichen</td><td><input tabindex="1" type="text" name="c_ln" id="c_ln" size="12" maxlength="9" value="{c_ln}" title="Kennzeichen!"  {readonly} ><input tabindex="-1" type="checkbox" name="chk_c_ln" value="true" {chk_c_ln} title="Eingabe prüfen"><input type="button" name="Info" value="Info" onclick="kz_to_lks(document.car.c_ln.value);"></td><td>Besitzer:</td><td> <input tabindex="23" type="text" name="chown" size="22" value="{ownerstring}" title="Fahrzeughalter" id="ac1" autocomplete="off" {readonly}></td></tr>
<tr><td class="info infoleft FahrzeuscheinZu2">HSN (2.1)<span></span></td><td><input tabindex="2" type="text" name="c_2" size="12" maxlength="4" value="{c_2}" title="Herstellerschlüssel aus dem Fahrzeugschein" onclick=select() {readonly}><input tabindex="-1" type="checkbox" name="chk_c_2" value="true" {chk_c_2}  title="Eingabe prüfen"></td><td class="info inforightright FahrzeuscheinHerst">Hersteller:<span></span></td><td><input tabindex="-1" type="text" size="29" value="{cm}" title="Automarke" readonly="readonly"></td></tr>
<tr><td class="info infoleft FahrzeuscheinZu3">TSN (2.2)<span></span></td><td><input  tabindex="3" type="text" name="c_3" size="12" maxlength="9" value="{c_3}" title="Typschlüssel aus dem Fahrzeugschein" onclick=select() {readonly} ><input tabindex="-1" type="checkbox" name="chk_c_3" value="true" {chk_c_3} title="Eingabe prüfen"></td><td class="info inforightright FahrzeuscheinTyp">Typ:<span></span></td><td><input tabindex="-1" type="text" size="29" value="{ct}" title="Typ/Variante" readonly="readonly"></td></tr>
<tr><td class="info infoleft FahrzeuscheinAbg">Emissionsklasse<span></span></td><td><input tabindex="4" type="text" name="c_em" size="12" maxlength="6" value="{c_em}" title="Fahrzeugschein Seite zwei, mitte,Feld 14" onclick=select() {readonly}><input tabindex="-1" type="checkbox" name="chk_c_em" value="true" {chk_c_em} title="Eingabe prüfen"><input type="button" name="Info" value="Info" onclick="feinstaub()"></td><td class="info inforightright FahrzeuscheinHub">Hubraum:<span></span></td><td><input tabindex="-1" type="text" size="29" value="{vh}" title="Zylindervolumen" readonly="readonly"></td></tr>
<tr><td class="info infoleft FahrzeuscheinBj">Datum Zulassung<span></span></td><td><input tabindex="5" type="text" id="c_d" name="c_d" size="12" maxlength="10" value="{c_d}" title="Fahrzeugschein Seite zwei, oben links, Feld B" onclick=select() {readonly}><input tabindex="-1" type="checkbox" name="chk_c_d" value="chk_c_d" checked="checked" readonly="readonly" title="Eingabe wird geprüft"></td><td>Bj. von - bis: </td><td><input tabindex="-1" type="text" size="29" value="{bj}" title="Zeitraum in dem das Fahrzeug produziert wurde" readonly="readonly"></td></tr>
<tr><td class="info infoleft FahrzeuscheinHu">Datum HU+AU<span></span></td><td><input tabindex="6" type="text" id="c_hu" name="c_hu" size="12" maxlength="10" value="{c_hu}" title="Stempel Fahrzeugscheinrückseite oder vom Fahrzeug ablesen" onclick=select() {readonly}></span><input tabindex="-1" type="checkbox" name="chk_c_hu" value="chk_c_hu" {chk_c_hu} title="Fälligkeit der HU wird geprüft"></td><td  class="info inforightright FahrzeuscheinLeist">Leistung:<span></span></td><td><input tabindex="-1" type="text" size="29" value="{peff}" title="Pferdchen" readonly="readonly"></td></tr>
<tr><td class="info infoleft FahrzeuscheinFin">FIN+Pr&uuml;fziffer<span></span><td> <input tabindex="7" type="text" name="fin" id="fin" size="24" maxlength="17" value="{fin}" title="Fahrzeugschein Seite 2 und im Fhz" onclick=select() onchange="UniqueFin(this.value,document.car.c_id.value)" {readonly}><input tabindex="8" type="text" name="cn" size="1" maxlength="1" value="{cn}" title="Fahrzeugschein Seite 2, Feld 3 (falls unbekannt - eingeben)" {readonly}><input tabindex="-1" type="checkbox" name="chk_fin" value="true" {chk_fin} title="Eingabe prüfen"></td><td>Drehmoment:</td><td><input tabindex="-1" type="text" size="29" value="{drehm}" title="falls bekannt" readonly="readonly"></td></tr>
<tr><td>Motorcode</td><td><input tabindex="9" type="text" name="mkb" size="12" maxlength="22" value="{mkb}" title="Steht meist auf dem Motor"  {readonly}>{mkbdrop} <input type="button" name="InfoMotor" value="Info" onclick="zeigeMotor(document.car.owner.value, document.car.c_id.value, document.car.mkb.value);"></td><td>Verdichtung:</td><td title="Kompressionsverh&auml;ltnis"><input tabindex="-1" type="text" size="29" value="{verd}" title="Kompressionsverh&auml;ltnis" readonly="readonly"></td></tr>
<tr><td>Farbnummer</td><td><input tabindex="11" tyu7pe="text" name="c_color" size="12" maxlength="22" value="{c_color}" title="Fahrzeugschein Seite 3, Feld R" {readonly}></td><td>Ventile:</td><td title="Abstand zwischen den Achsen"><input tabindex="-1" type="text" size="29" value="{vent}" title="Anzahl der Ventile" readonly="readonly"></td></tr>
<tr><td>Getriebeart</td><td><input id="ac2" tabindex="12" type="text" name="c_gart" size="12" value="{c_gart}" title="Schalter, Automatik, DSG" autocomplete="off" {readonly}>{g_art_drop}</td><td>Zylinder:</td><td><input tabindex="-1" type="text" size="29" value="{zyl}" title="Anzahl der Zylinder" readonly="readonly"></td></tr>
<tr><td class="info infoleft FahrzeuscheinReifen">Sommerr&auml;der<span></span></td><td><input id="c_st" tabindex="13" type="text" name="c_st" size="12" value="{c_st}" title="Vom Fahrzeug ablesen Format: 185-65R14 88H"></td><td class="info inforightright FahrzeuscheinKraftstoff">Kraftstoffart / Inhalt:<span></span></td><td title="Abstand zwischen den Achsen"><input tabindex="-1" type="text" size="29" value="{ks}" title="Benzin, Diesel..." readonly="readonly"></td></tr>
<tr><td class="info infoleft FahrzeuscheinReifen">Winterr&auml;der<span></span></td><td><input tabindex="14" type="text" name="c_wt" size="12" value="{c_wt}" title="Vom Fahrzeug ablesen Format: 175-70R14 82T"></td><td>Radstand:</td><td title="Abstand zwischen den Achsen"><input tabindex="-1" type="text" size="29" value="{radstand}" title="Abstand zwischen den Achsen" readonly="readonly"></td></tr>
<tr><td>LO Sommerr&auml;der</td><td><input tabindex="15" type="text" name="c_st_l" size="12" value="{c_st_l}" title="Lagerort der Sommerreifen, z.B. B1D4"></td><td  class="info inforightright FahrzeuscheinVmax">Vmax:<span></span></td><td><input tabindex="-1" type="text" size="29" value="{vmax}" title="H&ouml;chstgeschwindigkeit" readonly="readonly"></td></tr>
<tr><td>LO Winterr&auml;der</td><td><input tabindex="16" type="text" name="c_wt_l" size="12" value="{c_wt_l}" title="Lagerort der Winterreifen, z.B. B4D1"></td><td  class="info inforightright FahrzeuscheinMasse">Gesamtgewicht:  <span></span></td><td><input tabindex="-1" type="text" size="29" value="{mmax}" title="Fahrzeugschein Seite 3, oben Feld G" readonly="readonly"></td></tr>
<tr><td>Zustand Sommerreifen</td><td><input tabindex="17" type="text" name="c_st_z" size="12" value="{c_st_z}" title="gut, mittel, schlecht oder Profiltiefe angeben"></td><td>bearbeitet am:</td><td><input tabindex="-1" type="text" size="29" value="{mdate}" title="Datum und Zeit" readonly="readonly"></td></tr>
<tr><td>Zustand Winterreifen</td><td><input tabindex="18" type="text" name="c_wt_z" size="12" value="{c_wt_z}" title="gut, mittel, schlecht oder Profiltiefe angeben"></td><td>bearbeitet von:</td><td><input tabindex="-1" type="text" size="29" value="{c_e_string}" title="dein Name" readonly="readonly"></td></tr>
<tr><td>Flexrohrgröße:</td><td><input tabindex="19" type="text" id="c_flx" name="c_flx" size="12" value="{c_flx}" title="Druchmesser/Länge"></td></tr>
</table>
<h4>Interne Bemerkungen</h4>
<table summary="Interne Bemerkungen">
<tr><td><textarea tabindex="20" name="c_text" cols="92" rows="5">{c_text}</textarea></td></tr>
</table>

<input tabindex="21" type="submit" name="update" id="speichern" value="speichern">&nbsp;&nbsp;&nbsp;
<input tabindex="22" type="button" name="close" onClick="myclose(document.car.owner.value);" value="schlie&szlig;en">&nbsp;&nbsp;&nbsp;
<input tabindex="23" type="button" name="auftrag" onClick="lxc_auf(document.car.c_id.value, document.car.owner.value,1);" value="   Auftrag   ">&nbsp;&nbsp;&nbsp;
<input tabindex="24" type="button" name="auftrag" onClick="FhzTyp(document.car.c_id.value,document.car.owner.value,'{c_2}','{c_3}');" value="KBA DB bearbeiten" style="visibility:{FhzTypVis}">&nbsp;
{SPECIAL}
</form>
</left>
{END_CONTENT}

</body>
</html>