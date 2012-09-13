<html>
<head><title>Auftragb {ln} mit der ID {c_id}</title>
	<link type="text/css" REL="stylesheet" HREF="../../css/{ERPCSS}"></link>

	<script type="text/javascript" src="./inc/lxccheckfelder.js"></script>
	<style type="text/css">
	.inp { width:130px }	
	.zeit { width:200pt;border: 1px solid #000000; margin-right: 5px }	

</style>
</head>
<body>
<left>

<h4></h4>
<p class="listtop">Auftrag mit dem Kennzeichen: {ln}</p>
<form name="lxcauf" action="lxcauf.php?task=3" method="post" onSubmit="">
<input type="hidden" name="c_id" value="{c_id}">
<input type="hidden" name="a_id" value="{a_id}">
<input type="hidden" name="owner" value="{owner}">
<input type="hidden" name="b" value="{b}">


<table class="klein">
<tr><td>Auftragnummer:</td><td>{a_id}</td><td>Auftraggeber:</td><td>{ownerstring}</td></tr>
<tr><td>Fertigstellung:</td><td><input type="text" name="lxc_a_finish_time" size="22" value="{lxc_a_finish_time}"></td><td>KM-Stand</td><td><input type="text" name="lxc_a_km" size="8" value="{lxc_a_km}"></td></tr>
<tr><td>bearbeitet von:</td><td>{lxc_a_modified_from}</td><td>bearbeitet am:</td><td>{lxc_a_modified_on}</td></tr>
<tr><td>erstellt am:</td><td>{lxc_a_init_time}</td><td>Status:</td><td><select name="lxc_a_status">
																						<option value="1" {lxc_a_status1}>angenommen
																						<option value="2" {lxc_a_status2}>bearbeitet
																						<option value="3" {lxc_a_status3}>abgerechnet
																						</select></td></tr>
</table>

<table class="klein">




<!-- BEGIN pos_block -->
<tr><th colspan="4"><textarea name="lxc_a_pos_todo___{posid}" cols="22" rows="2">{lxc_a_pos_todo}</textarea></th><th colspan="3"><textarea name="lxc_a_pos_doing___{posid}" cols="22" rows="2">{lxc_a_pos_doing}</textarea></th><th colspan="3"><textarea name="lxc_a_pos_parts___{posid}" cols="22" rows="2">{lxc_a_pos_parts}</textarea></th><td>
<b class="zeit"> Gebr. Zeit </b><input type="text" name="lxc_a_pos_finish_time___{posid}" size="1" value="{lxc_a_pos_time}">
<select class="inp" name="lxc_a_pos_status___{posid}">
<option value="1" {lxc_a_pos_status1{posid}}>gelesen
<option value="2" {lxc_a_pos_status2{posid}}>bearbeitet
<option value="3" {lxc_a_pos_status3{posid}}>geprüft
</select></br><b class="zeit">Vorg. Zeit </b><input type="text" name="lxc_a_pos_finish_ctime___{posid}" size="1" value="{lxc_a_pos_ctime}">

<select class="inp" name="lxc_a_pos_emp___{posid}">
	{lxc_schauber_auswahl}
</select></td></tr>

<!-- END pos_block -->
</table>
<table>
<tr><th colspan="4"><textarea name="lxc_a_text" cols="59" rows="3">{lxc_a_text}</textarea></th></tr>

</table>




<input type="submit" name="update" value="speichern">&nbsp;&nbsp;&nbsp;
<input type="submit" name="printa" value="Pdf">&nbsp;&nbsp;&nbsp;
<input type="submit" name="printa" value="drucken">&nbsp;&nbsp;&nbsp;
<input type="button" name="close" onClick="lxcaufschliessen(document.lxcauf.owner.value,document.lxcauf.c_id.value,3,{b});" value="schlie&szlig;en">

</form>
</left>
</body>
</html>
