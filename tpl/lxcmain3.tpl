<!-- $Id$ -->
<html>
<head><title>Fahrzeudaten  anzeigen von {ln} mit der ID {c_id}</title>
{STYLESHEETS}
<link href="./css/Tooltip-pop-up-FhzSchein.css" rel="stylesheet" type="text/css" media="screen" />

{JAVASCRIPTS}
<script type="text/javascript" src="./inc/lxccheckfelder.js"></script>
<script type="text/javascript" src="{BASEPATH}crm/lxcars/jQueryAddOns/german-date-time-picker.js"></script>

{THEME}

<script language="JavaScript">

    $(document).ready(function(){
        $("#ac1").autocomplete({
        source: "lxc_ac.php?case=owner",
            minLength: '3',
            delay: '0',
            select: function(e,ui) {
                $("#speichern").focus();
            }
        });

        $("#ac2").autocomplete({
            source: "lxc_ac.php?case=g_art",
            delay: '0',
            select: function(e,ui) {
                $("#c_st").focus();
            }
        });

        $("#c_d").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange: "-20:-0" ,
            dateFormat: "dd.mm.yy"
        });

        $("#c_hu").datepicker({
            changeMonth: true,
            changeYear: true,
            changeDay: false,
            minDate: '+0m -1y',
            maxDate: '+0m +3y',
            dateFormat: "dd.mm.yy"
        });

        $( "#dialog" ).dialog({
            autoOpen: false,
            title: "Kennzeichen fehlerhaft"
        });
        $( "#hu_dialog" ).dialog({
            autoOpen: false,
            title: "Hauptuntersuchung fällig"
        });
        $( "#c_ln" ).change(UniqueKz);
        if( false ){
            $( 'form input:text' ).on( "click", function() {
                var pos = this.selectionStart;
                $( this ).select();
                document.execCommand("copy");
                this.selectionStart = this.selectionEnd = pos;;
            });
        }
        else{
            $( 'form input:text' ).mousedown(function() {
                $( this ).select();
                document.execCommand( "copy" );
            });
        }

        $("#c_bf, #c_wd, #c_zrd").datepicker({
             changeMonth: true,
             changeYear: true,
             minDate: '+0m +0y',
             maxDate: '+0m +5y',
             dateFormat: "dd.mm.yy"
        });

        $( 'form input:text' ).button().addClass( 'ui-textfield' );
        $( 'button' ).button();
        $( '#mkbwahl' ).selectmenu({ width: 110 });
        $( '#g_art_drop' ).selectmenu({ width: 140 });
    });

</script>
<style type="text/css">
    #mann { position:absolute; top:10em; left:78em; border:1px solid #000;  }
    #bmw { position:absolute; top:15em; left:78em; border:1px solid #000;  }
    .ui-textfield {
            font: inherit;
            color: inherit;
            background: #FFFFEF !important;
            text-align: inherit;
            outline: none;
        }
    .ui-selectmenu-button {
            vertical-align : middle;
            margin-left: 1em;
        }
    td:nth-child(odd) {font-size: 0.9em; }
</style>

</head>
<body onload="checkhu(document.car.c_hu.value)">
{PRE_CONTENT}
{START_CONTENT}
<div class="ui-widget-content">
<p class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0.6em;">{msg}</p>

<div id="dialog"></div>
<div id="hu_dialog" ></div>
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
<tr><td>Kennzeichen</td><td><input tabindex="1" type="text" name="c_ln" id="c_ln" size="12" maxlength="9" value="{c_ln}" title="Kennzeichen!"  {readonly} ><input tabindex="-1" type="checkbox" name="chk_c_ln" value="true" {chk_c_ln} title="Eingabe prüfen"><button type="button" name="Info" value="Info" onclick="kz_to_lks(document.car.c_ln.value);">Info</button></td><td>Besitzer:</td><td> <input tabindex="23" type="text" name="chown" size="22" value="{ownerstring}" title="Fahrzeughalter" id="ac1" autocomplete="off" {readonly}></td></tr>
<tr><td class="info infoleft FahrzeuscheinZu2">HSN (2.1)<span></span></td><td><input tabindex="2" type="text" name="c_2" id="c_2" size="12" maxlength="4" value="{c_2}" title="Herstellerschlüssel aus dem Fahrzeugschein" {readonly}><input tabindex="-1" type="checkbox" name="chk_c_2" value="true" {chk_c_2}  title="Eingabe prüfen"></td><td class="info inforightright FahrzeuscheinHerst">Hersteller:<span></span></td><td><input tabindex="-1" type="text" size="29" value="{cm}" title="Automarke" readonly="readonly"></td></tr>
<tr><td class="info infoleft FahrzeuscheinZu3">TSN (2.2)<span></span></td><td><input  tabindex="3" type="text" name="c_3" id="c_3" size="12" maxlength="9" value="{c_3}" title="Typschlüssel aus dem Fahrzeugschein"  {readonly} ><input tabindex="-1" type="checkbox" name="chk_c_3" value="true" {chk_c_3} title="Eingabe prüfen"></td><td class="info inforightright FahrzeuscheinTyp">Typ:<span></span></td><td><input tabindex="-1" type="text" size="29" value="{ct}" title="Typ/Variante" readonly="readonly"></td></tr>
<tr><td class="info infoleft FahrzeuscheinAbg">Emissionsklasse (14.1)<span></span></td><td><input tabindex="4" type="text" name="c_em" id="c_em" size="12" maxlength="6" value="{c_em}" title="Fahrzeugschein Seite zwei, mitte,Feld 14"  {readonly}><input tabindex="-1" type="checkbox" name="chk_c_em" value="true" {chk_c_em} title="Eingabe prüfen"><button type="button"  onclick="feinstaub()">Info</button></td><td class="info inforightright FahrzeuscheinHub">Hubraum:<span></span></td><td><input id="c_hubr" name="c_hubr" tabindex="-1" type="text" size="29" value="{vh}" title="Zylindervolumen" readonly="readonly"></td></tr>
<tr><td class="info infoleft FahrzeuscheinBj">Datum Zulassung<span></span></td><td><input tabindex="5" type="text" id="c_d" name="c_d" size="12" maxlength="10" value="{c_d}" title="Fahrzeugschein Seite zwei, oben links, Feld B"  {readonly}><input tabindex="-1" type="checkbox" name="chk_c_d" value="chk_c_d" checked="checked" readonly="readonly" title="Eingabe wird geprüft"></td><td>Bj. von - bis: </td><td><input id="c_bauj" name="c_bauj" tabindex="-1" type="text" size="29" value="{bj}" title="Zeitraum in dem das Fahrzeug produziert wurde" readonly="readonly"></td></tr>
<tr><td class="info infoleft FahrzeuscheinHu">Datum HU+AU<span></span></td><td><input tabindex="6" type="text" id="c_hu" name="c_hu" size="12" maxlength="10" value="{c_hu}" title="Stempel Fahrzeugscheinrückseite oder vom Fahrzeug ablesen"  {readonly}></span><input tabindex="-1" type="checkbox" name="chk_c_hu" value="chk_c_hu" {chk_c_hu} title="Fälligkeit der HU wird geprüft"></td><td  class="info inforightright FahrzeuscheinLeist">Leistung:<span></span></td><td><input id="c_leist" name="c_leist" tabindex="-1" type="text" size="29" value="{peff}" title="Pferdchen" readonly="readonly"></td></tr>
<tr><td class="info infoleft FahrzeuscheinFin">FIN+Pr&uuml;fziffer<span></span><td> <input tabindex="7" type="text" name="fin" id="fin" size="24" maxlength="17" value="{fin}" title="Fahrzeugschein Seite 2 und im Fhz"  onchange="UniqueFin(this.value,document.car.c_id.value)" {readonly}><input tabindex="8" type="text" name="cn" size="1" maxlength="1" value="{cn}" title="Fahrzeugschein Seite 2, Feld 3 (falls unbekannt - eingeben)" {readonly}><input tabindex="-1" type="checkbox" name="chk_fin" value="true" {chk_fin} title="Eingabe prüfen"></td><td>Drehmoment:</td><td><input tabindex="-1" type="text" size="29" value="{drehm}" title="falls bekannt" readonly="readonly"></td></tr>
<tr><td>Motorcode</td><td><input tabindex="9" type="text" name="mkb" size="12" maxlength="22" value="{mkb}" title="Steht meist auf dem Motor"  {readonly}>{mkbdrop} <button type="button" name="InfoMotor" value="Info" onclick="zeigeMotor(document.car.owner.value, document.car.c_id.value, document.car.mkb.value);">Info</button></td><td>Verdichtung:</td><td title="Kompressionsverh&auml;ltnis"><input tabindex="-1" type="text" size="29" value="{verd}" title="Kompressionsverh&auml;ltnis" readonly="readonly"></td></tr>
<tr><td>Farbnummer</td><td><input tabindex="11" type="text" name="c_color" size="12" maxlength="22" value="{c_color}" title="Fahrzeugschein Seite 3, Feld R" {readonly}></td><td>Ventile:</td><td title="Abstand zwischen den Achsen"><input tabindex="-1" type="text" size="29" value="{vent}" title="Anzahl der Ventile" readonly="readonly"></td></tr>
<tr><td>Getriebeart</td><td><input id="ac2" tabindex="12" type="text" name="c_gart" size="12" value="{c_gart}" title="Schalter, Automatik, DSG" autocomplete="off" {readonly}>{g_art_drop}</td><td>Zylinder:</td><td><input tabindex="-1" type="text" size="29" value="{zyl}" title="Anzahl der Zylinder" readonly="readonly"></td></tr>
<tr><td class="info infoleft FahrzeuscheinReifen">Sommerr&auml;der<span></span></td><td><input id="c_st" tabindex="13" type="text" name="c_st" size="12" value="{c_st}" title="Vom Fahrzeug ablesen Format: 185-65R14 88H"></td><td class="info inforightright FahrzeuscheinKraftstoff">Kraftstoffart / Inhalt:<span></span></td><td title="Abstand zwischen den Achsen"><input tabindex="-1" type="text" size="29" value="{ks}" title="Benzin, Diesel..." readonly="readonly"></td></tr>
<tr><td class="info infoleft FahrzeuscheinReifen">Winterr&auml;der<span></span></td><td><input tabindex="14" type="text" name="c_wt" size="12" value="{c_wt}" title="Vom Fahrzeug ablesen Format: 175-70R14 82T"></td><td>Radstand:</td><td title="Abstand zwischen den Achsen"><input tabindex="-1" type="text" size="29" value="{radstand}" title="Abstand zwischen den Achsen" readonly="readonly"></td></tr>
<tr><td>LO Sommerr&auml;der</td><td><input tabindex="15" type="text" name="c_st_l" size="12" value="{c_st_l}" title="Lagerort der Sommerreifen, z.B. B1D4"></td><td  class="info inforightright FahrzeuscheinVmax">Vmax:<span></span></td><td><input tabindex="-1" type="text" size="29" value="{vmax}" title="H&ouml;chstgeschwindigkeit" readonly="readonly"></td></tr>
<tr><td>LO Winterr&auml;der</td><td><input tabindex="16" type="text" name="c_wt_l" size="12" value="{c_wt_l}" title="Lagerort der Winterreifen, z.B. B4D1"></td><td  class="info inforightright FahrzeuscheinMasse">Gesamtgewicht:  <span></span></td><td><input tabindex="-1" type="text" size="29" value="{mmax}" title="Fahrzeugschein Seite 3, oben Feld G" readonly="readonly"></td></tr>
<tr><td>Zustand Sommerreifen</td><td><input tabindex="17" type="text" name="c_st_z" size="12" value="{c_st_z}" title="gut, mittel, schlecht oder Profiltiefe angeben"></td><td>Nächster ZR-Wechsel am:</td><td><input type="text" id="c_zrd" name="c_zrd" size="29" maxlength="29" value="{c_zrd}" title="" ></td></tr>
<tr><td>Zustand Winterreifen</td><td><input tabindex="18" type="text" name="c_wt_z" size="12" value="{c_wt_z}" title="gut, mittel, schlecht oder Profiltiefe angeben"></td><td>Nächster ZR-Wechsel bei KM:</td><td><input type="text" id="c_zrk" name="c_zrk" size="29" maxlength="10" value="{c_zrk}" title="" ></td></tr>
<tr><td>Flexrohrgröße:</td><td><input tabindex="19" type="text" id="c_flx" name="c_flx" size="12" value="{c_flx}" title="Druchmesser/Länge"></td><td>Nächster Bremsflüssigkeitsw.:</td><td><input type="text" id="c_bf" name="c_bf" size="29" maxlength="29" value="{c_bf}" title="" ></td></tr>
<tr><td>bearbeitet am:</td><td><input tabindex="-1" type="text" size="20" value="{mdate}" title="Datum und Zeit" readonly="readonly"></td><td>Nächster Wartungsdienst:</td><td><input type="text" id="c_wd" name="c_wd" size="29" maxlength="29" value="{c_wd}" title="" ></td></tr>
<tr><td>bearbeitet von:</td><td><input tabindex="-1" type="text" size="20" value="{c_e_string}" title="dein Name" readonly="readonly"></td><td></td><td></td></tr>
</table>
<h4>Interne Bemerkungen</h4>
<table summary="Interne Bemerkungen">
<tr><td><textarea tabindex="20" name="c_text" cols="92" rows="5" class="ui-corner-all  ui-textfield">{c_text}</textarea></td></tr>
</table>

<button tabindex="21" type="submit" name="update" id="speichern" value="speichern">speichern</button>&nbsp;&nbsp;&nbsp;
<button tabindex="22" type="button" name="close" onClick="myclose(document.car.owner.value);">schließen</button>&nbsp;&nbsp;&nbsp;
<button tabindex="23" type="button" name="auftrag" onClick="lxc_auf(document.car.c_id.value, document.car.owner.value,1);">Auftrag</button>&nbsp;&nbsp;&nbsp;
<button tabindex="24" type="button" name="auftrag" onClick="FhzTyp(document.car.c_id.value,document.car.owner.value,'{c_2}','{c_3}');"  style="visibility:{FhzTypVis}">KBA DB bearbeiten</button>&nbsp;
{SPECIAL}
</form>
</left>
</div>
{END_CONTENT}

</body>

</html>