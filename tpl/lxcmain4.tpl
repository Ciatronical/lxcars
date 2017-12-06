<html>
<head><title>Besitzer ändern von {ln} mit der ID {c_id}</title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<link href="css/lxcalert.css" rel="stylesheet" type="text/css" media="screen" />
	
	<script type="text/javascript" src="inc/lxccheckfelder.js"></script>
</head>
<body onload="checkhu(document.car.c_hu.value)">
<left>

<h4></h4>
<form name="car" action="lxcmain.php?task=3&owner={owner}&c_id={c_id}" method="post" onSubmit="return checkfelder();">
<input type="hidden" name="owner" value="{owner}">
<input type="hidden" name="c_id" value="{c_id}">
<input type="hidden" name="c_t" value="{c_t}">
<input type="hidden" name="c_m" value="{c_m}">
<div style="position:absolute; top:60px; right:15px;"

<table>
<tr><td>Kennzeichen</td><td><input type="text" name="c_ln" size="10" value="{c_ln}" title="Kennzeichen darf nicht vorhanden sein!!!" readonly><b onclick="kz_to_lks(document.car.c_ln.value);" >&#063;</b></td><td>Besitzer:</td><td <input type="text" name="chown" size="22" value="{ownerstring}" title="Kennzeichen darf nicht vorhanden sein!!!" ></td></tr>
<tr><td>HSN (zu 2.1)</td><td><input type="text" name="c_2" size="4" value="{c_2}" title="Herstellerschlüssel aus dem Fahrzeugschein" readonly></td><td>Hersteller:</td><td>{cm}</td></tr>
<tr><td>TSN (zu 2.2)</td><td><input type="text" name="c_3" size="10" value="{c_3}" title="Typschlüssel aus dem Fahrzeugschein" readonly></td><td>Typ:</td><td>{ct}</td></tr>
<tr><td>Emissionsklasse ( zu 14.1)</td><td><input type="text" name="c_em" size="6" value="{c_em}" title="Fahrzeugschein Seite zwei, mitte" readonly></td><td>Hubraum:</td><td>{vh}</td></tr>
<tr><td>Datum Zulassung</td><td><input type="text" name="c_d" size="10" value="{c_d}" title="Fahrzeugschein Seite zwei, oben links" readonly></td><td>Bj. von - bis: </td><td>{bj}</td></tr>
<tr><td>Datum HU+AU</td><td><input type="text" name="c_hu" size="10" value="{c_hu}" title="Stempel Fahrzeugscheinrückseite oder vom Fahrzeug ablesen" readonly></td><td>Leistung:</td><td>{peff}</td></tr>
<tr><td>FIN+Pr&uuml;fziffer (zu E)</td><td><input type="text" name="fin" size="22" value="{fin}" title="Fahrzeugschein Seite zwei, oberes Drittel, steht auch im Fahrzeug - Fahrzeugidentitätsnummer" readonly><input type="text" name="cn" size="1" value="{cn}" title="Falls unbekannt - eingeben" readonly></td><td>Drehmoment:</td><td>{drehm}</td></tr>
<tr><td>Motorcode</td><td><input type="text" name="mkb" size="5" value="{mkb}" title="Steht meist auf dem Motor" readonly> </td><td>Verdichtung:</td><td title="Abstand zwischen den Achsen">{verd}</td></tr>
<tr><td>Farbnummer</td><td><input type="text" name="c_color" size="5" value="{c_color}" title="Steht meist auf dem Motor" readonly></td><td>Ventile:</td><td title="Abstand zwischen den Achsen">{vent}</td></tr>
<tr><td>Getriebeart</td><td><input type="text" name="c_gart" size="7" value="{c_gart}" title="Steht meist auf dem Motor" readonly></td><td>Zylinder:</td><td title="Abstand zwischen den Achsen">{zyl}</td></tr>
<tr><td>Sommerr&auml;der</td><td><input type="text" name="c_st" size="14" value="{c_st}" title="Vom Fahrzeug ablesen Format: 185-65R14 88H" readonly></td><td>Kraftstoffart / Inhalt:</td><td title="Abstand zwischen den Achsen">{ks}</td></tr>
<tr><td>Winterr&auml;der</td><td><input type="text" name="c_wt" size="14" value="{c_wt}" title="Vom Fahrzeug ablesen Format: 175-70R14 82T" readonly></td><td>Radstand:</td><td title="Abstand zwischen den Achsen">{radstand}</td></tr>
<tr><td>LO Sommerreifen</td><td><input type="text" name="c_st_l" size="14" value="{c_st_l}" title="Lagerort der Sommerreifen, Format 02-01-14" readonly></td><td>Vmax:</td><td>{vmax}</td></tr>
<tr><td>LO Winterreifen</td><td><input type="text" name="c_wt_l" size="14" value="{c_wt_l}" title="Lagerort der Winterreifen, Format 06-01-22" readonly></td><td>Gesamtgewicht:  </td><td>{mmax}</td></tr>
<tr><td>Zustand Winterreifen</td><td><input type="text" name="c_st_z" size="14" value="{c_st_z}" title="gut, mittel, schlecht oder Profiltiefe angeben" readonly></td><td>bearbeitet am:</td><td>{mdate}</td></tr>
<tr><td>Zustand Winterreifen</td><td><input type="text" name="c_wt_z" size="14" value="{c_wt_z}" title="gut, mittel, schlecht oder Profiltiefe angeben" readonly></td><td>bearbeitet von:</td><td>{c_e_string}</td></tr>

</table>
<table summary="Interne Bemerkungen" >
<tr><td>
<textarea name="c_text" cols="62" rows="5">{c_text}</textarea></td></tr>
</table>

<input type="submit" name="update" value="speichern">&nbsp;&nbsp;&nbsp;
<input type="button" name="close" onClick="myclose(document.car.owner.value);" value="schlie&szlig;en">&nbsp;&nbsp;&nbsp;
<input type="button" name="auftrag" onClick="lxc_auf(document.car.c_id.value, document.car.owner.value,1);" value="Auftrag">&nbsp;&nbsp;&nbsp;
<input type="button" name="chown" onClick="lxcchown(document.car.c_id.value);" value="Besitzer wechsel">

<!--input type="submit" name="suche" value="suchen"-->
</form>
</left>

</body>
</html>
