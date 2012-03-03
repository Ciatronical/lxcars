<!-- $Id$ -->
<html>
<head><title>Fahrzeudaten  anzeigen von {ln} mit der ID {c_id}</title>
	<link type="text/css" REL="stylesheet" HREF="../css/main.css"></link>
	<script language="JavaScript">
	<!--
	 
	function motclose(owner,c_id,mkbinput){
		Frame=eval("parent.main_window");
		if ( c_id  ) {
			uri1="lxcmain.php?owner=" + owner;
			uri2="&c_id=" + c_id;
			uri3="&task=3";
			uri=uri1+uri2+uri3;
		}
		else{
			uri="lxcmotSuche.php?mkbinput=" + mkbinput;
		}
	location.href=uri;
	}		
	//-->
	</script>
	
	
	
</head>
<body >
<left>

<h4></h4>
<form  name="motor">
<input type="hidden" name="owner" value="{owner}">
<input type="hidden" name="c_id" value="{c_id}">
<input type="hidden" name="mkbinput" value="{mkbinput}">
<input type="hidden" name="c_m" value="{c_m}">


<table>
<tr><td>Motorkennbuchstabe MKB</td><td><input type="text" size="12" maxlength="9" value="{motbezei}" readonly="readonly"></td><td>Hersteller</td><td><input type="text" size="12" value="{herstell}" readonly="readonly"></td></tr>
<tr><td>Leistung in kW von</td><td><input type="text" size="12" value="{kwvon}" title="Minimum Effektive Leistung in kW" readonly="readonly"></td><td>Leistung in kW bis</td><td><input type="text" size="12" value="{kwbis}" title="Maximum Effektive Leistung in kW" readonly="readonly"="readonly="readonly""></td></tr>
<tr><td>Leistung in PS von</td><td><input type="text" size="12" value="{psvon}" title="Minimum Effektive Leistung in PS" readonly="readonly"></td><td>Leistung in PS bis</td><td><input type="text" size="12" value="{psbis}" title="Maximum Effektive Leistung in PS" readonly="readonly"="readonly="readonly""></td></tr>

<tr><td>Ventile</td><td><input type="text" size="12" value="{ventile}" title="Anzahl der Ventile" readonly="readonly"></td><td>Zylinder</td><td><input type="text" size="12" value="{zyl}" title="Anzahl der Zylinder" readonly="readonly"></td></tr>
<tr><td>Verdichtung von</td><td><input type="text" size="12" value="{verdvon}" title="" readonly="readonly"></td><td>Verdichtung bis</td><td><input type="text" size="12" value="{verdbis}" title="" readonly="readonly"></td></tr>
<tr><td>Drehmoment in Nm von</td><td><input type="text" size="12" value="{dehmvon}" title="" readonly="readonly"></td><td>Drehmoment in Nm bis</td><td><input type="text" size="12" value="{dehmbis}" title="" readonly="readonly"></td></tr>
<tr><td>Baujahr von</td><td><input type="text" size="12" value="{bjvon}" title="hergestellt von" readonly="readonly"></td><td>Baujahr bis</td><td><input type="text" size="12" value="{bjbis}" title="hersgestellt bis" readonly="readonly"></td></tr>
<tr><td>Hubraum steuerlich in cm&sup3; von</td><td><input type="text" size="12" value="{vhstvon}" title="" readonly="readonly"></td><td>Hubraum steuerlich in cm&sup3; bis</td><td title=""><input type="text" size="12" value="{vhstbis}" title="" readonly="readonly"></td></tr>
<tr><td>Hubraum technisch in cm&sup3; von</td><td><input type="text" size="12" value="{vhtevon}" title="Minimum Huubraum" readonly="readonly"></td><td>Hubraum technisch in cm&sup3; bis</td><td><input type="text" size="12" value="{vhtebis}" title="Maximum Hubraum" readonly="readonly"></td></tr>
<tr><td>Hubraum steuerlich in L von</td><td><input type="text" size="12" value="{litstvon}" title="" readonly="readonly"></td><td>Hubraum steuerlich in L bis</td><td><input type="text" size="12" value="{litstbis}" title="" readonly="readonly"></td></tr>
<tr><td>Hubraum technisch in L von</td><td><input type="text" size="12" value="{littevon}" title="" readonly="readonly"></td><td>Hubraum technisch in L bis</td><td title=""><input type="text" size="12" value="{littebis}" readonly="readonly"></td></tr>
<tr><td>Technische Verwendung</td><td><input type="text" size="12" value="{motverw}" title="" readonly="readonly"></td><td>Bauform</td><td title=""><input type="text" size="12" value="{bauform}" readonly="readonly"></td></tr>
<tr><td>Kraftstofffart</td><td><input type="text" size="12" value="{ksart}" title="" readonly="readonly"></td><td>Kraftstoffaufbereitung</td><td title=""><input type="text" size="12" value="{ksauf}" readonly="readonly"></td></tr>
<tr><td>Turbo</td><td><input type="text" size="12" value="{turbo}" title="" readonly="readonly"></td><td>Anzahl Kurbelwellenlagerung</td><td title=""><input type="text" size="12" value="{kurbella}" readonly="readonly"></td></tr>
<tr><td>Drehzahl von</td><td><input type="text" size="12" value="{umdrvon}" title="" readonly="readonly"></td><td>Drehzahl bis</td><td title=""><input type="text" size="12" value="{umdrbis}" readonly="readonly"></td></tr>
<tr><td>Drehzahl bei Mmax von</td><td><input type="text" size="12" value="{umdrdvon}" title="" readonly="readonly"></td><td>Drehzahl bei Mmax bis</td><td title=""><input type="text" size="12" value="{umdrdbis}" readonly="readonly"></td></tr>
<tr><td>Durchmesser Zylinderbohrung in mm</td><td><input type="text" size="12" value="{bohrung}" title="" readonly="readonly"></td><td>Kolbenhub in mm</td><td title=""><input type="text" size="12" value="{hub}" readonly="readonly"></td></tr>
<tr><td>Motorart</td><td><input type="text" size="12" value="{motart}" title="" readonly="readonly"></td><td>Motorform</td><td title=""><input type="text" size="12" value="{motform}" readonly="readonly"></td></tr>
<tr><td>Motorsteuerung</td><td><input type="text" size="12" value="{motsteu}" title="" readonly="readonly"></td><td>Ventilsteuerung</td><td title=""><input type="text" size="12" value="{ventsteu}" readonly="readonly"></td></tr>
<tr><td>Abgasnorm</td><td><input type="text" size="12" value="{abgnorm}" title="" readonly="readonly"></td><td>Art der Kühlung</td><td title=""><input type="text" size="12" value="{kuehlart}" readonly="readonly"></td></tr>
<tr><td>Verkaufsbezeichnung</td><td><input type="text" size="12" value="{vkbez}" title="" readonly="readonly"></td><td>TecDoc Motornummer</td><td title=""><input type="text" size="12" value="{motnr}" readonly="readonly"></td></tr>
</table>
</form>

<input type="button" name="close" onClick="motclose(document.motor.owner.value, document.motor.c_id.value, document.motor.mkbinput.value);" value="schlie&szlig;en">&nbsp;&nbsp;&nbsp;



</left>

</body>
</html>
