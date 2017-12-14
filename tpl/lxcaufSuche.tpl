<!-- $Id$ -->
<html>
    <head><title></title>
       {STYLESHEETS}
       {JAVASCRIPTS}
        {CRMCSS}
        {JQUERY}
        {JQUERYUI}
        {THEME}
   <script language="JavaScript">
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

     function call_lxc_auf (owner,c_id,a_id,b,id) {


        alert(id);

        Frame=eval("parent.main_window");

        if (id == 0) {
         uri1="lxcauf.php?owner=" + owner;
         uri2="&c_id=" + c_id;
         uri3="&task=3"
         uri4="&a_id=" + a_id;
         uri5="&b=" + b;
         uri=uri1+uri2+uri3+uri4+uri5;

        location.href=uri;
        }else {
         location.href="order.phtml?owner=" + owner +"&id=" + id + "&c_id=" +c_id;
        }

    }
    //-->
    </script>

    </head>
   <body>
   {PRE_CONTENT}
   {START_CONTENT}
   <p class="ui-state-highlight ui-corner-all tools" style="margin-top: 20px; padding: 0.6em;"> Suche nach Aufträgen und verknüpften Daten</p>

      <form name="erwsuche" enctype='multipart/form-data' action="{action}" method="get">
      <input type="hidden" name="felder" value="">
   <div class="zeile">
        <span class="label">Kennzeichen</span>
        <span class="leftfeld"><input type="text" name="c_ln" size="16" maxlength="10" value="{c_ln}" tabindex="1" id="ac0" autocomplete="off" ></span>
    </div>
     <div class="zeile">
        <span class="label">Kundenname</span>
        <span class="leftfeld"><input type="text" name="c_ow" size="16" maxlength="22" value="{c_ow}" tabindexx="2" id="ac1" autocomplete="off" ></span>
    </div>

     <div class="zeile">
        <span class="label">Arbeitstext</span>
        <span class="leftfeld"><input type="text" name="lxc_a_pos_todo" size="16" maxlength="32" value="{lxc_a_pos_todo}" tabindex="3"></span>
    </div>
     <div class="zeile">
        <span class="label">Antworttext</span>
        <span class="leftfeld"><input type="text" name="lxc_a_pos_doing" size="16" maxlength="32" value="{lxc_a_pos_doing}" tabindex="4"></span>
    </div>
    <div class="zeile">
        <span class="label">Datum von / bis</span>
        <span class="leftfeld"><input type="text" name="c_d_gg" size="4" maxlength="10" value="{c_d_gg}" tabindex="6"> &nbsp;/&nbsp;
        <input type="text" name="c_d_kg" size="4" maxlength="10" value="{c_d_kg}" tabindex="7"></span>
    <div class="zeile">
        <span class="label">Bemerkungen</span>
        <span class="leftfeld"><input type="text" name="lxc_a_text" size="16" maxlength="22" value="{lxc_a_text}" tabindex="15"></span>
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
    <table class="mini">
    <!-- class='bgcol3'-->
    <thead><tr bgcolor="lightblue"><th>Status</th><th>Kennzeichen</th><th>Besitzer</th><th>Auftragsdatum</th><th>Erster Arbeitstext</th><th>Auftr.-Nr.</th></tr>
    </thead>
    <!-- BEGIN Liste -->
    <tr onMouseover="this.bgColor='#0033FF';" onMouseout="this.bgColor='{LineCol}';" bgcolor="{LineCol}"><td bgcolor="{SpCol}">{Statustext}</td><td onClick="call_lxc_auf({a_c_ow},{a_c_id},{lxc_a_id},1,{id});" class="liste"  >{rs_c_ln}</td><td onClick="call_lxc_auf({a_c_ow},{a_c_id},{lxc_a_id},1,{id});" class="liste"> {kdname}</td><td onClick="call_lxc_auf({a_c_ow},{a_c_id},{lxc_a_id},1,{id});" class="liste"> {adate}</td><td onClick="call_lxc_auf({a_c_ow},{a_c_id},{lxc_a_id},1,{id});" class="liste"> {todo}</td><td onClick="call_lxc_auf({a_c_ow},{a_c_id},{lxc_a_id},1,{id});" class="liste" align=right>{lxc_a_id}</td></tr>
    <!-- END Liste -->
</table>
</div>

{END_CONTENT}
{TOOLS}
</body>
