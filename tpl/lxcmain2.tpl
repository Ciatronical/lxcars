<html>
<head><title>Neues Fahrzeug </title>
    {STYLESHEETS}
	<link type="text/css" REL="stylesheet" HREF="../../css/{ERPCSS}"></link>
    <link rel="stylesheet" type="text/css" href="{BASEPATH}crm/jquery-ui/themes/base/jquery-ui.css"> 
	<link href="./css/lxcalert.css" rel="stylesheet" type="text/css" media="screen" />
	{JAVASCRIPTS}
	<script type="text/javascript" src="./inc/lxccheckfelder.js"></script>
    <script type="text/javascript" src="{BASEPATH}crm/jquery-ui/jquery.js"></script> 
    <script type="text/javascript" src="{BASEPATH}crm/jquery-ui/ui/jquery-ui.js"></script>
    <script type="text/javascript" src="{BASEPATH}crm/lxcars/jQueryAddOns/german-date-time-picker.js"></script>
	{xajax_out}
	<script type="text/javascript">
	$(function() {
        $("#ac2").autocomplete({                          
            source: "lxc_ac.php?case=g_art",                                                        
            delay: '0',
            select: function(e,ui) {
                $("#c_st").focus();
            }
        });
    });
    var FinGeholt = 0;
    function HoleFin( zu2, zu3 ){
        if( FinGeholt == 0 ){
            xajax_SucheFin( zu2, zu3 );
            xajax_SucheMkb( zu2, zu3 );
            FinGeholt = 1;
		}
	}
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
    </script>
</head>
<body onload="document.car.c_ln.focus()">
{PRE_CONTENT}
{START_CONTENT}
<p ></p>
<left>
<form name="car" action="lxcmain.php?task=2&owner={owner}&c_id={c_id}" method="post" onSubmit="return checkfelder();">
<input type="hidden" name="owner" value="{owner}">
<input type="hidden" name="c_id" value="{c_id}">
{MSG}
<table>
<tr><td>Kennzeichen</td><td><input tabindex="1" type="text" name="c_ln" size="22"  maxlength="9" value="{c_ln}" title="Kennzeichen eingeben" onblur="xajax_UniqueKz(this.value,document.car.c_id.value)"><input tabindex="-1" type="checkbox" name="chk_c_ln" value="true" checked="checked" title="Eingabe prüfen"><input type="button" name="Info" value="Info" onclick="kz_to_lks(document.car.c_ln.value);"></td></tr>
<tr><td>HSN (2.1)</td><td><input tabindex="2" type="text" name="c_2" size="22" maxlength="4" value="{c_2}" title="Herstellerschl&uuml;ssel aus dem Fahrzeugschein"><input tabindex="-1" type="checkbox" name="chk_c_2" value="true" checked="checked" title="Eingabe prüfen"></td></tr>
<tr><td>TSN (2.2)</td><td><input tabindex="3" type="text" name="c_3" size="22" maxlength="9" value="{c_3}" title="Typschl&uuml;ssel aus dem Fahrzeugschein"><input tabindex="-1" type="checkbox" name="chk_c_3" value="true" checked="checked" title="Eingabe prüfen"></td></tr>
<tr><td>Emissionsklasse (14.1)</td><td><input  tabindex="4" type="text" name="c_em" size="22" maxlength="6" value="{c_em}" title="Fahrzeugschein Seite zwei, mitte"><input tabindex="-1" type="checkbox" name="chk_c_em" value="true" checked="checked" title="Eingabe prüfen"></td></tr>
<tr><td>Datum Zulassung</td><td><input tabindex="5" type="text" name="c_d" id="c_d" size="22" maxlength="10" value="{c_d}" title="Fahrzeugschein Feld B (Seite zwei, oben links)"><input tabindex="-1" type="checkbox" name="chk_c_d" value="true" checked="checked" readonly="readonly" title="Eingabe wird geprüft"></td></tr>
<tr><td>Datum HU+AU</td><td><input tabindex="6" type="text" name="c_hu" id="c_hu" size="22" maxlength="10" value="{c_hu}" title="Stempel Fahrzeugscheinrückseite oder vom Fahrzeug ablesen"><input tabindex="-1" type="checkbox" name="chk_c_hu" value="true" checked="checked" title="Fälligkeit der HU wird geprüft"></td></tr>
<tr><td>FIN+Pr&uuml;fziffer </td><td><input id="idfin" onfocus="HoleFin(document.car.c_2.value,document.car.c_3.value)" onblur="xajax_UniqueFin(this.value,document.car.c_id.value)" tabindex="7" type="text" name="fin" size="22" maxlength="17" title="Fahrzeugschein Seite zwei, Feld E oder im Fahrzeug (Motorraum, Frontscheibe)"><input tabindex="8" type="text" name="cn" size="1" maxlength="1" value="{cn}" title="Fahrzeugschein Seite 2 Feld 3; Falls unbekannt - eingeben"><input tabindex="-1" type="checkbox" name="chk_fin" value="true" checked="checked" title="Eingabe prüfen"></td></tr>
<tr><td>Motorcode</td><td><input tabindex="9" type="text" name="mkb" size="8" maxlength="22" value="{mkb}" title="Steht meist auf dem Motor"><select tabindex="10" name="mkbdrop" id="mkbdrop" </select></td></tr>
<tr><td>Farbe</td><td><input tabindex="11" type="text" name="c_color" size="22" maxlength="22" value="{c_color}" title="Fahrzeugschein Seite 3 Feld R"></td></tr>
<tr><td>Getriebeart</td><td><input id="ac2" tabindex="12" type="text" name="c_gart" size="22" maxlength="22" value="{c_gart}" title="Schalter, Automatik, DSG" autocomplete="off">{g_art_drop}</td></tr>
<tr><td>Sommerr&auml;der</td><td><input id="c_st" tabindex="14" type="text" name="c_st" size="22" maxlength="22" value="{c_st}" title="Fahrzeugschein Seite 3 Feld 15.1/15.2 oder vom Fahrzeug ablesen Format: 185/65R14 88H"></td></tr>
<tr><td>Winterr&auml;der</td><td><input tabindex="15" type="text" name="c_wt" size="22" maxlength="22" value="{c_wt}" title="Fahrzeugschein Seite 3 Feld 15.1/15.2 oder vom Fahrzeug ablesen Format: 175/70R14 82T"></td></tr>
<tr><td>LO Sommerreifen</td><td><input tabindex="16" type="text" name="c_st_l" size="22" maxlength="22" value="{c_st_l}" title="z.B. B1D4"></td></tr>
<tr><td>LO Winterreifen</td><td><input tabindex="17" type="text" name="c_wt_l" size="22" maxlength="22" value="{c_wt_l}" title="z.B. B4D1"></td></tr>
<tr><td>Zustand Sommerreifen</td><td><input tabindex="18" type="text" name="c_st_z" size="22" maxlength="22" value="{c_st_z}" title="gut, mittel, schlecht oder Profiltiefe angeben"></td></tr>
<tr><td>Zustand Winterreifen</td><td><input tabindex="19" type="text" name="c_wt_z" size="22" maxlength="22" value="{c_wt_w}" title="gut, mittel, schlecht oder Profiltiefe angeben"></td></tr>
</table>
<br>		
<h4>Interne Bemerkungen</h4>		
<table>
<textarea tabindex="20" name="c_text" cols="62" rows="5">{c_text}</textarea></td></tr>
</table>
<input tabindex="21" type="submit" name="anlegen" value="anlegen">&nbsp;&nbsp;&nbsp;
<input tabindex="22" type="button" onClick="myclose(document.car.owner.value);" value="schlie&szlig;en">
</form>
</left>
{END_CONTENT}
</body>
</html>
