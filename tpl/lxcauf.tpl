<html>
<head><title>Auftragb {ln} mit der ID {c_id}</title>
      {STYLESHEETS}
       {JAVASCRIPTS}
        {CRMCSS}
        {JQUERY}
        {JQUERYUI}
        {THEME}

    <script type="text/javascript" src="{BASEPATH}crm/jquery-ui/jquery.js"></script>
    <script type="text/javascript" src="{BASEPATH}crm/jquery-ui/ui/jquery-ui.js"></script>
    <script type="text/javascript" src="{BASEPATH}crm/lxcars/jQueryAddOns/date-time-picker.js"></script>
    <script type="text/javascript" src="{BASEPATH}crm/lxcars/jQueryAddOns/german-date-time-picker.js"></script>
    <link type="text/css" REL="stylesheet" HREF="../../css/{ERPCSS}"></link>
    <link rel="stylesheet" type="text/css" href="{BASEPATH}crm/jquery-ui/themes/base/jquery-ui.css">
    <script>
        $(function() {
            $("#dialog").dialog();
        });
        function AddButton(input){
            setTimeout(function(){
                var buttonPane = $(input).datepicker("widget").find( ".ui-datepicker-buttonpane" );
                var btn = $('<button class="ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all" type="button"> Wartet</button>');
                btn.appendTo( buttonPane );
                btn.bind("click", function () {
                    document.getElementById("lxc_a_finish_time").value = "Kunde wartet! SOFORT anfangen!!!";
                });

            }, 1 );
        }
        $(function() {
            $("#lxc_a_finish_time").datetimepicker({
                beforeShow: function(input){
                    AddButton(input);
                 },
                 onChangeMonthYear:function( year, month, inst ) {
                       AddButton(inst.input);
                },
                stepMinute: 5,
                hour: 1,
                hourMin: 6,
                hourMax: 19,
                timeSuffix: ' Uhr',
                timeText: 'Zeit',
                hourText: 'Stunde',
                closeText: 'Fertig',
                currentText: 'Jetzt'
            });
            $( "#service" ).button()
                .click(function() {
                    $( ".todo:last" ).val( "Liquide, Wischer, Licht- und Wischwaschanlage prüfen" );
                    return false;
            });
            $( "#huau" ).button()
                .click(function() {
                    $( ".todo:first" ).val( "HU/AU und Verbandskasten überprüfen" );
                    $.ajax({
                        url: 'lxchuau.php',
                        data: {
                            huau: $("#car_id").val(),
                        },
                        type: "POST",
                            success: function( json ) {
                            }
                    });
                    return false;
            });
            $( "#testfelder" ).button()
               .click(function() {
               var posi = (parseInt({posid})+1);
                alert('PosID alt: {posid} -> PosID neu: '+posi);
                    $("#erw").append('<tr><th colspan="4"><textarea class="todo" name="lxc_a_pos_todo___'+ posi +'" cols="22" rows="2">{lxc_a_pos_todo}</textarea></th><th colspan="3"><textarea name="lxc_a_pos_doing___'+ posi +'" cols="22" rows="2">{lxc_a_pos_doing}</textarea></th><th colspan="3"><textarea name="lxc_a_pos_parts___'+ posi +'" cols="22" rows="2">{lxc_a_pos_parts}</textarea></th><td>'+
'<b class="zeit">Vorg. Zeit </b><input type="text" name="lxc_a_pos_finish_ctime___'+ posi +'" size="1" value="{lxc_a_pos_ctime}">'+
'<select class="inp" name="lxc_a_pos_status___'+ posi +'">'+
'<option value="1" {lxc_a_pos_status1'+ posi +'}>gelesen'+
'<option value="2" {lxc_a_pos_status2'+ posi +'}>bearbeitet'+
'<option value="3" {lxc_a_pos_status3'+ posi +'}>geprüft'+
'</select></br>'+
'<b class="zeit"> Gebr. Zeit </b><input type="text" name="lxc_a_pos_finish_time___'+ posi +'" size="1" value="{lxc_a_pos_time}">'+
'<select class="inp" name="lxc_a_pos_emp___'+ posi +'">{lxc_schauber_auswahl}'+
'</select></td></tr>');
                    return false;
            });
            $( "#carData" ).button()
                .click(function() {
                    lxcaufschliessen2( document.lxcauf.c_id.value, document.lxcauf.owner.value, 3);
                    return false;
            });
            $( "input[type=submit],input[type=button]" ).button()
            $( "#backToCRM" ).button().click(function () {
            //alert( document.lxcauf.owner.value );
                location.href = 'http://192.168.178.25/kivitendo-dev-jens/crm/firma1.php?Q=C&id=' + document.lxcauf.owner.value;
                return false;
            });
        });
    </script>
    <script type="text/javascript" src="./inc/lxccheckfelder.js"></script>
    <style type="text/css">
        .inp { width:130px }
        .zeit { width:200pt;border: 1px solid #000000; margin-right: 5px }
        /* css for timepicker */
        .ui-timepicker-div .ui-widget-header { margin-bottom: 8px; }
        .ui-timepicker-div dl { text-align: left; }
        .ui-timepicker-div dl dt { height: 25px; margin-bottom: -25px; }
        .ui-timepicker-div dl dd { margin: 0 10px 10px 65px; }
        .ui-timepicker-div td { font-size: 90%; }
        .ui-tpicker-grid-label { background: none; border: none; margin: 0; padding: 0; }
        .ui-timepicker-rtl{ direction: rtl; }
        .ui-timepicker-rtl dl { text-align: right; }
        .ui-timepicker-rtl dl dd { margin: 0 65px 10px 10px; }
    </style>
</head>
<body>
{JQMSG}
{PRE_CONTENT}
{START_CONTENT}
<left>
<p class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0.6em;">{msg} Auftrag mit dem Kennzeichen: {ln} || {cm} {ct}</p>
<form name="lxcauf" action="lxcauf.php?task=3" method="post" onSubmit="">
<input type="hidden" id="car_id" name="c_id" value="{c_id}">
<input type="hidden" name="a_id" value="{a_id}">
<input type="hidden" name="owner" value="{owner}">
<input type="hidden" name="b" value="{b}">

<table class="klein">
<tr><td>Auftragnummer:</td><td>{a_id}</td><td>Auftraggeber:</td><td>{ownerstring}</td></tr>
<tr><td>Fertigstellung:</td><td><input type="text" name="lxc_a_finish_time" id="lxc_a_finish_time" size="22" value="{lxc_a_finish_time}"></td><td>KM-Stand</td>
<td><input type="text" name="lxc_a_km" size="8" value="{lxc_a_km}"></td></tr>
<tr><td>bearbeitet von:</td><td>{lxc_a_modified_from}</td><td>bearbeitet am:</td><td>{lxc_a_modified_on}</td></tr>
<tr><td></td><td></td><td>KfZ:</td><td><select name="lxc_a_car_status">
                                                    <option value="1" {lxc_a_car_status1}>Auto nicht hier
                                                    <option value="2" {lxc_a_car_status2}>Auto hier
                                                    <option value="3" {lxc_a_car_status3}>Sonstiges zur Reparatur gebracht
                                                    <option value="4" {lxc_a_car_status4}>Bestellung
                                                    </select></td></tr>
<tr><td>erstellt am:</td><td>{lxc_a_init_time}</td><td>Status:</td><td><select name="lxc_a_status">
                                                                                        <option value="1" {lxc_a_status1}>angenommen
                                                                                        <option value="2" {lxc_a_status2}>bearbeitet
                                                                    lxcauf.php?                    <option value="3" {lxc_a_status3}>abgerechnet
                                                                                        </select></td></tr>
</table>

<table class="klein">
<!-- BEGIN pos_block -->
<tr><th colspan="4"><textarea class="todo" name="lxc_a_pos_todo___{posid}" cols="22" rows="2">{lxc_a_pos_todo}</textarea></th><th colspan="3"><textarea name="lxc_a_pos_doing___{posid}" cols="22" rows="2">{lxc_a_pos_doing}</textarea></th><th colspan="3"><textarea name="lxc_a_pos_parts___{posid}" cols="22" rows="2">{lxc_a_pos_parts}</textarea></th><td>
<b class="zeit">Vorg. Zeit </b><input type="text" name="lxc_a_pos_finish_ctime___{posid}" size="1" value="{lxc_a_pos_ctime}">
<select class="inp" name="lxc_a_pos_status___{posid}">
<option value="1" {lxc_a_pos_status1{posid}}>gelesen
<option value="2" {lxc_a_pos_status2{posid}}>bearbeitet
<option value="3" {lxc_a_pos_status3{posid}}>geprüft
</select></br>
<b class="zeit"> Gebr. Zeit </b><input type="text" name="lxc_a_pos_finish_time___{posid}" size="1" value="{lxc_a_pos_time}">

<select class="inp" name="lxc_a_pos_emp___{posid}">
    {lxc_schauber_auswahl}
</select></td></tr>

<!-- END pos_block -->
</table>
<table>
<tr><th colspan="4"><textarea name="lxc_a_text" id="lxc_a_text" cols="59" rows="3">{lxc_a_text}</textarea></th></tr>
</table>
<input type="submit" name="update" value="speichern">&nbsp;&nbsp;&nbsp;
<input type="submit" name="printa" value="Pdf">&nbsp;&nbsp;&nbsp;
<input type="submit" name="printa" value="drucken">&nbsp;&nbsp;&nbsp;
<input type="button" name="close" onClick="lxcaufschliessen(document.lxcauf.c_id.value,document.lxcauf.owner.value,1,{b});" value="schlie&szlig;en">
<button id="service">Service</button>
<button id="huau">HU/AU</button>
<button id="carData">Fahrzeug</button>
<button id="backToCRM">CRM</button>
</form>
</left>
{END_CONTENT}
</body>
</html>
