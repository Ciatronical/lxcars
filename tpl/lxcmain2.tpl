<html>
<head><title>LxCars - Neues Fahrzeug anlegen</title>
{STYLESHEETS}
<link href="./css/Tooltip-pop-up-FhzSchein.css" rel="stylesheet" type="text/css" media="screen" />
<script type="text/javascript" src="./inc/lxccheckfelder.js"></script>
{JAVASCRIPTS}
{CRMCSS}
<script type="text/javascript" src="{BASEPATH}crm/lxcars/jQueryAddOns/german-date-time-picker.js"></script>
{THEME}
<script type="text/javascript" src="./inc/lxccheckfelder.js"></script>
    <script type="text/javascript">
    var FinGeholt = 0;
    function HoleFin( zu2, zu3 ){
        if( FinGeholt == 0 ){
            SucheFin( zu2, zu3 );
            SucheMkb( zu2, zu3 );
            FinGeholt = 1;
        }
    }
    $(document).ready(function(){
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

        $( "#dialog" ).dialog({ autoOpen: false });

        $("#c_ln").change(UniqueKz);

        $( 'form input:text' ).css({
            'font' : 'inherit',
            'color' : 'inherit',
            'text-align' : 'left',
            'outline' : 'none',
            'cursor' : 'text'
        }).addClass( 'ui-corner-all' );
        $( 'form textarea' ).addClass( 'ui-corner-all' ).html( '{c_text}' );
        $( 'button' ).button();
        $( '#g_art_drop' ).selectmenu({ width: 140 });
        $( '#mkbdrop' ).selectmenu({ width: 110 });
    });

    </script>
    <style type="text/css">

    </style>
</head>
<body onload="document.car.c_ln.focus()">
{PRE_CONTENT}
{START_CONTENT}
<div class="ui-widget-content">
<div id="dialog" title="LxCars Fehler" ></div>

<p ></p>
<left>
<form name="car" action="lxcmain.php?task=2&owner={owner}&c_id={c_id}" method="post" onSubmit="return checkFelder();">
<input type="hidden" name="owner" value="{owner}">
<input type="hidden" name="c_id"  id="c_id" value="{c_id}">
{MSG}
<table>
<tr><td>Kennzeichen</td><td><input tabindex="1" type="text" name="c_ln" id="c_ln" size="22"  maxlength="9" value="{c_ln}" title="Kennzeichen eingeben"><input tabindex="-1" type="checkbox" name="chk_c_ln" value="true" checked="checked" title="Eingabe prüfen"><button type="button" id="infoLk" onclick="kz_to_lks(document.car.c_ln.value);">Info</button></td></tr>
<tr><td class="info infoleft FahrzeuscheinZu2">HSN (2.1)<span></span></td><td><input tabindex="2" type="text" name="c_2" size="22" maxlength="4" value="{c_2}" title="Herstellerschl&uuml;ssel aus dem Fahrzeugschein"><input tabindex="-1" type="checkbox" name="chk_c_2" value="true" checked="checked" title="Eingabe prüfen"></td></tr>
<tr><td class="info infoleft FahrzeuscheinZu3">TSN (2.2)<span></span></td><td><input tabindex="3" type="text" name="c_3" size="22" maxlength="9" value="{c_3}" title="Typschl&uuml;ssel aus dem Fahrzeugschein"><input tabindex="-1" type="checkbox" name="chk_c_3" value="true" checked="checked" title="Eingabe prüfen"></td></tr>
<tr><td class="info infoleft FahrzeuscheinAbg">Emissionsklasse (14.1)<span></span></td><td><input  tabindex="4" type="text" name="c_em" size="22" maxlength="6" value="{c_em}" title="Fahrzeugschein Seite zwei, mitte"><input tabindex="-1" type="checkbox" name="chk_c_em" value="true" checked="checked" title="Eingabe prüfen"></td></tr>
<tr><td class="info infoleft FahrzeuscheinBj">Datum Zulassung<span></span></td><td><input tabindex="5" type="text" name="c_d" id="c_d" size="22" maxlength="10" value="{c_d}" title="Fahrzeugschein Feld B (Seite zwei, oben links)"><input tabindex="-1" type="checkbox" name="chk_c_d" value="true" checked="checked" readonly="readonly" title="Eingabe wird geprüft"></td></tr>
<tr><td class="info infoleft FahrzeuscheinHu">Datum HU+AU<span></span></td><td><input tabindex="6" type="text" name="c_hu" id="c_hu" size="22" maxlength="10" value="{c_hu}" title="Stempel Fahrzeugscheinrückseite oder vom Fahrzeug ablesen"><input tabindex="-1" type="checkbox" name="chk_c_hu" value="true" checked="checked" title="Fälligkeit der HU wird geprüft"></td></tr>
<tr><td class="info infoleft FahrzeuscheinFin">FIN+Pr&uuml;fziffer <span></span></td><td><input id="fin" onfocus="HoleFin(document.car.c_2.value,document.car.c_3.value)" onblur="UniqueFin(this.value,document.car.c_id.value)" tabindex="7" type="text" name="fin" size="22" maxlength="17" title="Fahrzeugschein Seite zwei, Feld E oder im Fahrzeug (Motorraum, Frontscheibe)"><input tabindex="8" type="text" name="cn" size="1" maxlength="1" value="{cn}" title="Fahrzeugschein Seite 2 Feld 3; Falls unbekannt - eingeben"><input tabindex="-1" type="checkbox" name="chk_fin" value="true" checked="checked" title="Eingabe prüfen"></td></tr>
<tr><td>Motorcode</td><td><input tabindex="9" type="text" name="mkb" id="mkb"size="8" maxlength="22" value="{mkb}" title="Steht meist auf dem Motor"><select tabindex="10" name="mkbdrop" id="mkbdrop" </select></td></tr>
<tr><td>Farbe</td><td><input tabindex="11" type="text" name="c_color" size="22" maxlength="22" value="{c_color}" title="Fahrzeugschein Seite 3 Feld R"></td></tr>
<tr><td>Getriebeart</td><td><input id="ac2" tabindex="12" type="text" name="c_gart" size="22" maxlength="22" value="{c_gart}" title="Schalter, Automatik, DSG" autocomplete="off">{g_art_drop}</td></tr>
<tr><td class="info infoleft FahrzeuscheinReifen">Sommerr&auml;der<span></span></td><td><input id="c_st" tabindex="14" type="text" name="c_st" size="22" maxlength="22" value="{c_st}" title="Fahrzeugschein Seite 3 Feld 15.1/15.2 oder vom Fahrzeug ablesen Format: 185/65R14 88H"></td></tr>
<tr><td class="info infoleft FahrzeuscheinReifen">Winterr&auml;der<span></span></td><td><input tabindex="15" type="text" name="c_wt" size="22" maxlength="22" value="{c_wt}" title="Fahrzeugschein Seite 3 Feld 15.1/15.2 oder vom Fahrzeug ablesen Format: 175/70R14 82T"></td></tr>
<tr><td>LO Sommerreifen</td><td><input tabindex="16" type="text" name="c_st_l" size="22" maxlength="22" value="{c_st_l}" title="z.B. B1D4"></td></tr>
<tr><td>LO Winterreifen</td><td><input tabindex="17" type="text" name="c_wt_l" size="22" maxlength="22" value="{c_wt_l}" title="z.B. B4D1"></td></tr>
<tr><td>Zustand Sommerreifen</td><td><input tabindex="18" type="text" name="c_st_z" size="22" maxlength="22" value="{c_st_z}" title="gut, mittel, schlecht oder Profiltiefe angeben"></td></tr>
<tr><td>Zustand Winterreifen</td><td><input tabindex="19" type="text" name="c_wt_z" size="22" maxlength="22" value="{c_wt_w}" title="gut, mittel, schlecht oder Profiltiefe angeben"></td></tr>
</table>
<br>
<h4>Interne Bemerkungen</h4>
<table>
<textarea tabindex="20" name="c_text" cols="62" rows="5"></textarea></td></tr>
</table>
<button tabindex="21" type="submit" name="anlegen" value="anlegen">anlegen</button>&nbsp;&nbsp;&nbsp;
<button tabindex="22" type="button" onClick="myclose(document.car.owner.value);">schießen</button>
</form>
</left>
</div>
{END_CONTENT}
{TOOLS}
</body>
</html>
