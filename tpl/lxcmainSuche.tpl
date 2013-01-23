<!-- $Id$ -->
<html>
	<head><title>LxCars - Kfz suchen</title>
	{STYLESHEETS}
    <link type="text/css" REL="stylesheet" HREF="../css/{ERPCSS}/main.css"></link>
    {JAVASCRIPTS}
    <link rel="stylesheet" type="text/css" href="../jquery-ui/themes/base/jquery-ui.css"> 
    <script type="text/javascript" src="../jquery-ui/jquery.js"></script> 
    <script type="text/javascript" src="../jquery-ui/ui/jquery-ui.js"></script> 
	<script language="JavaScript" type="text/javascript">
  		function report() {
  			f1=open("report.php?tab={Q}","Report","width=600; height=300; left=100; top=100");
  		}
	</script>
   <script type='text/javascript' src='../inc/help.js'></script>
   <script language="JavaScript">
	<!--
	
    $(function() {
        $("#ac1").autocomplete({                          
            source: "lxc_ac.php?case=owner",                            
            minLength: '3',                            
            delay: '0',
            select: function(e,ui) {
                $("#suchen").focus();
            }
        });
    });
    $(function() {
        $("#ac0").autocomplete({                          
            source: "lxc_ac.php?case=kz",                            
            minLength: '1',                            
            delay: '0',
            select: function(e,ui) {
                $("#suchen").focus();
            }
        });
    });		
				 		 
	function call_lxc_auf (owner,c_id,a_id) {
        uri1="lxcauf.php?owner=" + owner;
		uri2="&c_id=" + c_id;
		uri3="&task=3"
		uri4="&a_id=" + a_id;
		uri=uri1+uri2+uri3+uri4;
		location.href=uri;
	}
	function showK (id) {
		if (id) {
			uri="../firma1.php?Q={Q}&id=" + id;
			location.href=uri;
		}
	}
	function chngSerial(site) {
		etikett.document.location.href = site + ".php?src=F";
	}
	function showCar (c_id){
		if (c_id) {
			uri="lxcmain.php?task=3&c_id=" + c_id;
			location.href=uri;
		}
	}
    //-->
	</script>
   <body onLoad="document.erwsuche.c_hu_gg.focus();">
   {PRE_CONTENT}
   {START_CONTENT}
   <p class="listtop" onClick="help('SuchFirma');"> Suche nach Fahrzeugen und verkn&uuml;pften Daten</p>
   <span style="position:absolute; left:1em; top:5.0em; border: 0px solid black;">
  	<form name="erwsuche" enctype='multipart/form-data' action="{action}" method="post">
  	<input type="hidden" name="felder" value="">
   <div class="zeile">
		<span class="label">Kennzeichen</span>
		<span class="leftfeld"><input type="text" name="c_ln" size="16" maxlength="10" value="{c_ln}" tabindex="1" id="ac0" autocomplete="off"></span>
	</div>
	 <div class="zeile">
		<span class="label">Kundenname </span>
		<span class="leftfeld"><input type="text" name="c_ow" size="16" maxlength="16" value="{c_ow}" tabindex="2" id="ac1" autocomplete="off"></span>
	</div>
	 <div class="zeile">
		<span class="label">HSN (2.1)</span>
		<span class="leftfeld"><input type="text" name="c_2" size="6" maxlength="4" value="{c_2}" tabindex="3"></span>
	</div>
	 <div class="zeile">
		<span class="label">TSN (2.2)</span>
		<span class="leftfeld"><input type="text" name="c_3" size="6" maxlength="10" value="{c_3}" tabindex="4"></span>
	</div>
	 <div class="zeile">
		<span class="label">Emissionsklasse</span>
		<span class="leftfeld"><input type="text" name="c_em" size="6" maxlength="6" value="{c_em}" tabindex="5"></span>
	</div>
	 <div class="zeile">
		<span class="label">Baujahr von / bis</span>
		<span class="leftfeld"><input type="text" name="c_d_gg" size="4" maxlength="10" value="{c_d_gg}" tabindex="6"> &nbsp;/&nbsp;
		<input type="text" name="c_d_kg" size="4" maxlength="10" value="{c_d_kg}" tabindex="7"></span>
	</div>
	 <div class="zeile">
		<span class="label">HU+AU von / bis</span>
		<span class="leftfeld"><input type="text" name="c_hu_gg" size="4" maxlength="10" value="{c_hu_gg}" tabindex="8"> &nbsp;/&nbsp;
		<input type="text" name="c_hu_kg" size="4" maxlength="10" value="{c_hu_kg}" tabindex="9"></span>
	</div>
	<div class="zeile">
		<span class="label">FIN FahrzeugIdentNr</span>
		<span class="leftfeld"><input type="text" name="c_fin" size="16" maxlength="16" value="{c_fin}" tabindex="10"></span>
	</div>
	<div class="zeile">
		<span class="label">Sommerreifen</span>
		<span class="leftfeld"><input type="text" name="c_st" size="16" maxlength="16" value="{c_st}" tabindex="11"></span>
	</div>
	<div class="zeile">
		<span class="label">Winterreifen</span>
		<span class="leftfeld"><input type="text" name="c_wt" size="16" maxlength="16" value="{c_wt}" tabindex="12"></span>
	</div>
	<div class="zeile">
		<span class="label">Lagerort Sommerreifen</span>
		<span class="leftfeld"><input type="text" name="c_st_l" size="16" maxlength="16" value="{c_st_l}" tabindex="13"></span>
	</div>
	<div class="zeile">
		<span class="label">Lagerort Winterreifen</span>
		<span class="leftfeld"><input type="text" name="c_wt_l" size="16" maxlength="16" value="{c_wt_l}" tabindex="14"></span>
	</div>
	<div class="zeile">
		<span class="label">Bemerkungen</span>
		<span class="leftfeld"><input type="text" name="c_text" size="16" maxlength="16" value="{c_text}" tabindex="15"></span>
	</div>
	
			<div class="zeile">
			<b>{Msg}</b><br>
			<input type="checkbox" name="filter" value="1" tabindex="42">Erzeuge Datei mit Mann-Filter-Nummern<br>
			<input type="submit" class="anzeige" name="suche" value="suchen" tabindex="43" id="suchen">&nbsp;
			<input type="submit" class="clear" name="reset" value="löschen" tabindex="44"> &nbsp;
			<input type="button" name="rep" value="Report" onClick="report()" tabindex="45"> &nbsp;
			<input type="button" name="geo" value="GeoDB" onClick="surfgeo()" tabindex="46" style="visibility:{GEOS}"> &nbsp;
  			<br>
			{report}
			</div>
		</form>
	</span>
	 <span style="position:absolute; left:28em; top:5.0em; border: 0px solid black;">
	<table><tr><td valign="top">

<table>
<!-- BEGIN Liste -->
	<tr>
		<td onMouseover="this.bgColor='#FF0000';" onMouseout="this.bgColor='{LineCol}';" bgcolor="{LineCol}" onClick="showK({ID});" class="mini" > {Name} {Ort}</td><td onMouseover="this.bgColor='#FF0000';" onMouseout="this.bgColor='{LineCol}';" bgcolor="{LineCol}" onClick="showCar({CarID});" class="mini">{KZ} {Herst} {CarTyp}</td>
    </tr>
<!-- END Liste -->

</table>
{report}
</td>

<!-- BEGIN Rechts -->
<td class="mini">
<form>
	<input type="button" name="etikett" value="Etikett" onClick="chngSerial('../etiketten');">&nbsp;
	<a href="../sermail.php"><input type="button" name="email" value="Serienmail"></a>&nbsp;
	<input type="button" name="brief" value="Serienbrief" onClick="chngSerial('../serdoc');">
	<input type="button" name="vcard" value="Vcard" onClick="chngSerial('../servcard');">
</form>
	<br>
	<iframe src="../etiketten.php" name="etikett" width="300" height="380" scrolling="yes"> marginheight="0" marginwidth="0" align="left">
		<p>Ihr Browser kann leider keine eingebetteten Frames anzeigen</p>
	</iframe>
</td>
<!-- END Rechts -->

</tr>

</td></tr></table>	 
	 
	 </span>
	 {END_CONTENT}
	</body>
	