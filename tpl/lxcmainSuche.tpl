<!-- $Id$ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="../css/main.css"></link>

	<link rel="stylesheet" type="text/css" href="./css/lxcjquery.autocomplete.css">
	<script type="text/javascript" src="./inc/lxcjquery.js"></script>
	<script type="text/javascript" src="./inc/lxcjquery.autocomplete.js"></script>
	<script language="JavaScript" type="text/javascript">
  		function report() {
  			f1=open("report.php?tab={Q}","Report","width=600; height=300; left=100; top=100");
  		}
	</script>
   <script type='text/javascript' src='../inc/help.js'></script>
   <script language="JavaScript">
	<!--
	$(function() {
	$("#ac0").autocomplete('lxcHoleKZ.php', { minChars:2 });
	$("#ac1").autocomplete('lxcHoleKunden.php', { minChars:3 });
	

	$("#flush").click(function() {
		var ac = $("#ac1").data('autocompleter');
		if (ac && $.isFunction(ac.cacheFlush)) {
			ac.cacheFlush();
		} else {
			alert('Error flushing cache');
		}
	});

	$("#ac3").autocomplete({
		url: 'lxcHoleKunden.php',
		sortFunction: function(a, b, filter) {
			var f = filter.toLowerCase();
			var fl = f.length;
			var a1 = a.value.toLowerCase().substring(0, fl) == f ? '0' : '1';
			var a1 = a1 + String(a.data[0]).toLowerCase();
			var b1 = b.value.toLowerCase().substring(0, fl) == f ? '0' : '1';
			var b1 = b1 + String(b.data[0]).toLowerCase();
			if (a1 > b1) {
				return 1;
			}
			if (a1 < b1) {
				return -1;
			}
			return 0;
		},
		showResult: function(value, data) {
			return '<span style="color:red">' + value + '</span>';
		},
		onItemSelect: function(item) {
		    var text = 'You selected <b>' + item.value + '</b>';
		    if (item.data.length) {
		        text += ' <i>' + item.data.join(', ') + '</i>';
		    }
		    $("#last_selected").html(text);
		},
		maxItemsToShow: 5
		});
	});
				 		 
	function call_lxc_auf (owner,c_id,a_id) {
		Frame=eval("parent.main_window");
		uri1="lxcauf.php?owner=" + owner;
		uri2="&c_id=" + c_id;
		uri3="&task=3"
		uri4="&a_id=" + a_id;
		uri=uri1+uri2+uri3+uri4;
		location.href=uri;
	}
	function showK (id) {
		if (id) {
			Frame=eval("parent.main_window");
			uri="../firma1.php?Q={Q}&id=" + id;
			Frame.location.href=uri;
		}
	}
	function chngSerial(site) {
		etikett.document.location.href = site + ".php?src=F";
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
   <body onLoad="document.erwsuche.c_hu_gg.focus();">
   <p class="listtop" onClick="help('SuchFirma');"> Suche nach Fahrzeugen und verkn&uuml;pften Daten</p>
   <span style="position:absolute; left:1em; top:3.0em; border: 0px solid black;">
  	<form name="erwsuche" enctype='multipart/form-data' action="{action}" method="post">
  	<input type="hidden" name="felder" value="">
   <div class="zeile">
		<span class="label">Kennzeichen</span>
		<span class="leftfeld"><input type="text" name="c_ln" size="22" maxlength="10" value="{c_ln}" tabindex="1" id="ac0" autocomplete="off"></span>
	</div>
	 <div class="zeile">
		<span class="label">Kundenname</span>
		<span class="leftfeld"><input type="text" name="c_ow" size="22" maxlength="16" value="{c_ow}" tabindex="2" id="ac1" autocomplete="off"></span>
	</div>
	 <div class="zeile">
		<span class="label">HSN (2.1)</span>
		<span class="leftfeld"><input type="text" name="c_2" size="22" maxlength="4" value="{c_2}" tabindex="3"></span>
	</div>
	 <div class="zeile">
		<span class="label">TSN (2.2)</span>
		<span class="leftfeld"><input type="text" name="c_3" size="22" maxlength="10" value="{c_3}" tabindex="4"></span>
	</div>
	 <div class="zeile">
		<span class="label">Emissionsklasse</span>
		<span class="leftfeld"><input type="text" name="c_em" size="22" maxlength="6" value="{c_em}" tabindex="5"></span>
	</div>
	 <div class="zeile">
		<span class="label">Baujahr von</span>
		<span class="leftfeld"><input type="text" name="c_d_gg" size="8" maxlength="10" value="{c_d_gg}" tabindex="6"> bis 
		<input type="text" name="c_d_kg" size="8" maxlength="10" value="{c_d_kg}" tabindex="7"></span>
	</div>
	 <div class="zeile">
		<span class="label">HU+AU von</span>
		<span class="leftfeld"><input type="text" name="c_hu_gg" size="8" maxlength="10" value="{c_hu_gg}" tabindex="8"> bis 
		<input type="text" name="c_hu_kg" size="8" maxlength="10" value="{c_hu_kg}" tabindex="9"></span>
	</div>
	<div class="zeile">
		<span class="label">FIN FahrzeugIdentNr</span>
		<span class="leftfeld"><input type="text" name="c_fin" size="22" maxlength="16" value="{c_fin}" tabindex="10"></span>
	</div>
	<div class="zeile">
		<span class="label">Sommerreifen</span>
		<span class="leftfeld"><input type="text" name="c_st" size="22" maxlength="16" value="{c_st}" tabindex="11"></span>
	</div>
	<div class="zeile">
		<span class="label">Winterreifen</span>
		<span class="leftfeld"><input type="text" name="c_wt" size="22" maxlength="16" value="{c_wt}" tabindex="12"></span>
	</div>
	<div class="zeile">
		<span class="label">Lagerort Sommerreifen</span>
		<span class="leftfeld"><input type="text" name="c_st_l" size="22" maxlength="16" value="{c_st_l}" tabindex="13"></span>
	</div>
	<div class="zeile">
		<span class="label">Lagerort Winterreifen</span>
		<span class="leftfeld"><input type="text" name="c_wt_l" size="22" maxlength="16" value="{c_wt_l}" tabindex="14"></span>
	</div>
	<div class="zeile">
		<span class="label">Bemerkungen</span>
		<span class="leftfeld"><input type="text" name="c_text" size="22" maxlength="16" value="{c_text}" tabindex="15"></span>
	</div>
	
			<div class="zeile">
			<b>{Msg}</b><br>
			<input type="checkbox" name="filter" value="1" tabindex="42">Erzeuge Datei mit Mann-Filter-Nummern<br>
			<input type="submit" class="anzeige" name="suche" value="suchen" tabindex="43">&nbsp;
			<input type="submit" class="clear" name="reset" value="löschen" tabindex="44"> &nbsp;
			<input type="button" name="rep" value="Report" onClick="report()" tabindex="45"> &nbsp;
			<input type="button" name="geo" value="GeoDB" onClick="surfgeo()" tabindex="46" style="visibility:{GEOS}"> &nbsp;
  			<br>
			{report}
			</div>
		</form>
	</span>
	 <span style="position:absolute; left:28em; top:3.0em; border: 0px solid black;">
	<table><tr><td valign="top">

<table>
<!-- BEGIN Liste -->
	<tr>
		<td onMouseover="this.bgColor='#FF0000';" onMouseout="this.bgColor='{LineCol}';" bgcolor="{LineCol}" onClick="showK({ID});" class="mini" > {Name} {Ort}</td><td onMouseover="this.bgColor='#FF0000';" onMouseout="this.bgColor='{LineCol}';" bgcolor="{LineCol}" onClick="showCar({CarID});" class="mini">{KZ} {Herst} {CarTyp}</td></tr>
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
	</body>
	