<!-- $Id$ -->
<html>
	<head><title></title>
	{STYLESHEETS}
	<link type="text/css" REL="stylesheet" HREF="../css/{ERPCSS}/main.css"></link>
	<link rel="stylesheet" type="text/css" href="./css/lxcjquery.autocomplete.css">	
	{JAVASCRIPTS}
	<script type='text/javascript' src='inc/help.js'></script>
	<script type="text/javascript" src="./inc/lxcjquery.js"></script>
	<script type="text/javascript" src="./inc/lxcjquery.autocomplete.js"></script>
   <script language="JavaScript">
	<!--
    $(function(){
		var owner = '1';
		$("#ac1").autocomplete({
			url: 'lxc_ac.php',
			inputClass: 'acInputOwner',
			extraParams: { owner: owner },
			maxItemsToShow: 9,
			minChars: 3,
			onItemSelect: function(){
				$("#suchen").focus(); 
			}
		});
	});		
    		
        $(function(){
		var kz = '1';
		$("#ac0").autocomplete({
			url: 'lxc_ac.php',
			inputClass: 'acInputOwner',
			extraParams: { kz: kz },
			maxItemsToShow: 9,
			minChars: 1,
			onItemSelect: function(){
				$("#suchen").focus(); 
			}
		});
	});	


				 
	function call_lxc_auf (owner,c_id,a_id,b) {
		Frame=eval("parent.main_window");
		uri1="lxcauf.php?owner=" + owner;
		uri2="&c_id=" + c_id;
		uri3="&task=3"
		uri4="&a_id=" + a_id;
		uri5="&b=" + b;
		uri=uri1+uri2+uri3+uri4+uri5;
		location.href=uri;
	}		
	//-->
	</script>
		
	</head>
   <body>
   {PRE_CONTENT}
   {START_CONTENT}
   <p class="listtop"  onClick="help('SuchFirma');"> Suche nach Aufträgen und verknüpften Daten</p>
   <span style="position:absolute; left:1em; top:3.0em; border: 0px solid black;">
  	<form name="erwsuche" enctype='multipart/form-data' action="{action}" method="get">
  	<input type="hidden" name="felder" value="">
   <div class="zeile">
		<span class="label">Kennzeichen</span>
		<span class="leftfeld"><input type="text" name="c_ln" size="22" maxlength="10" value="{c_ln}" tabindex="1" id="ac0" autocomplete="off" ></span>
	</div>
	 <div class="zeile">
		<span class="label">Kundenname</span>
		<span class="leftfeld"><input type="text" name="c_ow" size="22" maxlength="22" value="{c_ow}" tabindexx="2" id="ac1" autocomplete="off" ></span>
	</div>
	
	 <div class="zeile">
		<span class="label">Arbeitstext</span>
		<span class="leftfeld"><input type="text" name="lxc_a_pos_todo" size="22" maxlength="10" value="{lxc_a_pos_todo}" tabindex="3"></span>
	</div>
	 <div class="zeile">
		<span class="label">Antworttext</span>
		<span class="leftfeld"><input type="text" name="lxc_a_pos_doing" size="22" maxlength="10" value="{lxc_a_pos_doing}" tabindex="4"></span>
	</div>
	<div class="zeile">
		<span class="label">Datum von</span>
		<span class="leftfeld"><input type="text" name="c_d_gg" size="22" maxlength="10" value="{c_d_gg}" tabindex="6"> bis 
		<input type="text" name="c_d_kg" size="22" maxlength="10" value="{c_d_kg}" tabindex="7"></span>
	<div class="zeile">
		<span class="label">Bemerkungen</span>
		<span class="leftfeld"><input type="text" name="lxc_a_text" size="22" maxlength="22" value="{lxc_a_text}" tabindex="15"></span>
	</div>
	<div class="zeile">
			<span class="label">Status</span>
			<span class="leftfeld">
			<select name="lxc_a_status"><option value="0" {lxc_a_status0}>alle
												 <option value="1" {lxc_a_status1}>angenommen
												 <option value="2" {lxc_a_status2}>bearbeitet
												 <option value="3" {lxc_a_status3}>abgerechnet
												 <option value="4" {lxc_a_status4}>nicht abgerechnet</select><br><br>
			<input type="checkbox" name="selbstgeschrieben" value="checked='checked'" {selbstgeschrieben} tabindex="42">selbst geschrieben<br>
			<input type="checkbox" name="selbstgeschraubt" value="checked='checked'" {selbstgeschraubt} tabindex="42">selbst geschraubt<br><br>
			<input type="submit" class="anzeige" name="suche" value="suchen" tabindex="43" id="suchen">&nbsp;&nbsp;
			<input type="submit" class="clear" name="reset" value="löschen" tabindex="44"> &nbsp;</span>
			
	</div>
</form>
</span>
&nbsp;&nbsp;
<table class="mini">
	<tr class='bgcol3'><th>Kennzeichen</th><th>Besitzer</th><th>Auftragsdatum</th><th>Erster Arbeitstext</th><th>Auftr.-Nr.</th></tr>
	<!-- BEGIN Liste -->
	<tr onMouseover="this.bgColor='#0033FF';" onMouseout="this.bgColor='{LineCol}';" bgcolor="{LineCol}"><td onClick="call_lxc_auf({a_c_ow},{a_c_id},{lxc_a_id},1);" class="liste"  >{rs_c_ln}</td><td onClick="call_lxc_auf({a_c_ow},{a_c_id},{lxc_a_id},1);" class="liste"> {kdname}</td><td onClick="call_lxc_auf({a_c_ow},{a_c_id},{lxc_a_id},1);" class="liste"> {adate}</td><td onClick="call_lxc_auf({a_c_ow},{a_c_id},{lxc_a_id},1);" class="liste"> {todo}</td><td onClick="call_lxc_auf({a_c_ow},{a_c_id},{lxc_a_id},1);" class="liste" align=right>{lxc_a_id}</td></tr>
	<!-- END Liste -->
</table>
</div>
</form>
</span>
{END_CONTENT}
</body>



	