<?php

if( !isset( $_SESSION ) ) session_start();

require_once __DIR__.'/../inc/stdLib.php';
require_once __DIR__.'/../inc/crmLib.php';
$menu = $_SESSION['menu'];

//$head = mkHeader();
?>
<html>
<head><title>Fahrzeuge, Kunden, Lieferante und Personen suchen </title>
    <script language="JavaScript">
    function showD( src, id ){
        if      (src=="C") { uri="../firma1.php?Q=C&id=" + id }
        else if (src=="V") { uri="../firma1.php?Q=V&id=" + id; }
        else if (src=="E") { uri="../user1.php?id=" + id; }
        else if (src=="K") { uri="../kontakt.php?id=" + id; }
        else if (src=="A") { uri="lxcmain.php?task=3&c_id=" + id; }
        window.location.href=uri;
    }

       </script>
<?php
    echo $menu['stylesheets'];
    echo $menu['javascripts'];
    echo $head['THEME'];
    echo $head['JQTABLE'];

    echo '
    <style>
        .ui-autocomplete-category {
            font-weight: bold;
            padding: .2em .4em;
            margin: .8em 0 .2em;
            line-height: 1.5;
        }
        .ui-textfield {
            font: inherit;
            color: inherit;
            background: #FFFFEF !important;
            text-align: inherit;
            outline: none;
        }
    </style>
    <script>

        $.widget("custom.catcomplete", $.ui.autocomplete, {
            _renderMenu: function(ul,items) {
                var that = this,
                currentCategory = "";
                $.each( items, function( index, item ) {
                    if ( item.category != currentCategory ) {
                        ul.append( "<li class=\'ui-autocomplete-category\'>" + item.category + "</li>" );
                        currentCategory = item.category;
                    }
                    that._renderItemData(ul,item);
                });
             }
         });
    </script>
    <script language="JavaScript">
        $(function() {
            $("#ac0").catcomplete({
                source: "lxc_ac.php?case=fastsearch",
                minLength: '.$_SESSION['feature_ac_minlength'].',
                delay: '.$_SESSION['feature_ac_delay'].',
                select: function(e,ui) {
                    showD(ui.item.src,ui.item.id);
                }
            });
        });
    </script>';


    ?>
    <script>

    $(document).ready( function(){

        $('#ac0').button().addClass('ui-textfield');
        $( "input[type=submit]" )
            .button();
        $("#dialog").dialog();

        $("#drop").selectmenu({
            //style: 'dropdown',
            select: function( event, data ){
                showD( data.item.value.substr( 0, 1 ), data.item.value.substr( 1 ) );
            }
        })

        $.ajax({
            url: "../ajax/getData.php",
            type: "POST",
            data: { action: "getHistory" },
            success: function( data){
                $.each( data, function( index, itemData ){
                    var selected = !index ? "selected='selected'" : "";
                    $("<option value='" + itemData[2] + itemData[0] + "'" + selected + " >" + itemData[1] + "</option>").appendTo("#drop");
                 });
                 //$("#drop").selectmenu("refresh");
                 //$("#drop").selectmenu("destroy").selectmenu({ style: "dropdown" });
                 $( "#drop" ).selectmenu( "open" );

            },
            error: function(){
                alert( 'Error: getHistory() in ajax/getData.php' );
            }

        })
    });
    </script>
    <style>

    table.tablesorter {
       width: 1000;
    }

    </style>
</head>
<body onload=$("#ac0").focus().val('<?php echo preg_replace("#[ ].*#",'',$_GET['swort']);?>');>
<div class="ui-widget-content" style="height:600px">
<?php //wichtig: focus().val('ohneLeerZeichen')
echo $menu['pre_content'];
echo $menu['start_content'];
    $formular = '<p class="ui-state-highlight ui-corner-all tools" style="margin-top: 20px; padding: 0.6em;">Schnellsuche Kunde/Lieferant/Kontakte und Kontaktverlauf <?php echo ($telnum)?"Telefonunummer: ".$telnum:""; ?></p>';
    $formular .= '<form name="suche" action="lxcgetData.php?telnum='.$telnum.' method="get">';
    $formular .= '<input type="text" name="swort" size="20" id="ac0" autocomplete="off">';
    $formular .= '<input type="submit" name="adress" id="adress" value="Kunde o. Lief.">';
    $formular .= '<input type="submit" name="sauto" value="Kennzeichen">';
    $formular .= '<input type="submit" name="kontakt" value="Kontaktverlauf"> <br>';
    $formular .= '<span class="liste">Suchbegriff</span></form>';
    echo $formular;
    $history = '<select name="drop" id="drop"></select>';
  echo $history;
?>


<?php

$msg=    '<div id="dialog" title="Kein Suchbegriff eingegeben">
              <p>Bitte geben Sie mindestens ein Zeichen ein.</p>
          </div>';
$viele=  '<div id="dialog" title="Zu viele Suchergebnisse">
              <p>Die Suche ergibt zu viele Resultate.</br> Bitte geben mehr Zeichen ein.</p>
          </div>';
$keine=  '<div id="dialog" title="Nichts gefunden">
              <p>Dieser Suchbegriff ergibt kein Resultat.</br>Bitte überprüfen Sie die Schreibweise!</p>
          </div>';
$keinFhz='<div id="dialog" title="Fahrzeug nicht gefunden">
              <p>Es wurde kein Fahrzeug gefunden.</br>Bitte überprüfen Sie die Schreibweise!</p>
          </div>';
if ($_GET["adress"]) {

    require_once __DIR__.'/../inc/FirmenLib.php';
    require_once __DIR__.'/../inc/persLib.php';
    require_once __DIR__.'/../inc/UserLib.php';
    $found=false;
    $suchwort=mkSuchwort($_GET["swort"]);
    $anzahl=0;

    $rsE = $rsV = $rsC = $rsK = false;

    if ( $_GET['swort'] != '') {
        $rsC = getAllFirmen($suchwort,true,"C");
        if ( $rsC ) $anzahl = count($rsC);
        if ( $anzahl <= $_SESSION['listLimit'] ) {
            $rsV = getAllFirmen($suchwort,true,"V");
            if ( $rsV ) $anzahl += count($rsV);
            if ( $anzahl <= $_SESSION['listLimit'] ) {
                $rsK = getAllPerson($suchwort);
                if ( $rsK ) $anzahl += count($rsK);
                if ( $anzahl <= $_SESSION['listLimit'] ) {
                    $rsE = getAllUser($suchwort);
                    if ( $rsE ) $anzahl += count($rsE);
                    //echo "Anzahl: ".$anzahl;
                    if ( $anzahl >= $_SESSION['listLimit'] ) {
                        $msg = $viele;
                    } else {
                        if ($anzahl === 0) {
                            //$msg = $keine;
                            $keineFirma = 1;
                            //echo "Keine Firma = ".$keineFirma;
                        }
                    }
                } else {
                    $msg = $viele;
                }
            } else {
                $msg = $viele;
            }
        } else {
                $msg = $viele;
        }
    }

    if ($anzahl>0) {
        if ($anzahl==1 && $rsC) header("Location: ../firma1.php?Q=C&id=".$rsC[0]['id']);
        if ($anzahl==1 && $rsV) header("Location: ../firma1.php?Q=V&id=".$rsV[0]['id']);
        if ($anzahl==1 && $rsK) header("Location: ../kontakt.php?id=".$rsK[0]['id']);
        if ($anzahl==1 && $rsE) header("Location: ../user.php?id=".$rsE[0]['id']);

        echo "<table id='treffer' class='tablesorter'>";
        echo "<thead><tr ><th>KD-Nr</th><th>Name</th><th>Anschrift</th><th>Telefon</th><th></th></tr></thead>\n<tbody>\n";
        $i=0;
        if ($rsC) foreach($rsC as $row) {
            echo "<tr onClick='showD(\"C\",".$row["id"].");'>".
                "<td>".$row["customernumber"]."</td><td >".$row["name"]."</td>".
                "<td >".$row["city"].(($row["street"])?", ":" ").$row["street"]."</td><td >".$row["phone"]."</td><td>K</td></tr>\n";
            $i++;
        }

        if ($rsV) foreach($rsV as $row) {
            echo "<tr onClick='showD(\"V\",".$row["id"].");'>".
                "<td>".$row["vendornumber"]."</td><td >".$row["name"]."</td>".
                "<td>".$row["city"].(($row["street"])?", ":" ").$row["street"]."</td><td>".$row["phone"]."</td><td>L</td></tr>\n";
            $i++;
        }

        if ($rsK) foreach($rsK as $row) {
            echo "<tr  class='bgcol".($i%2+1)."' onClick='showD(\"K\",".$row["id"].");'>".
                "<td >".$row["cp_id"]."</td><td >".$row["cp_name"].", ".$row["cp_givenname"]."</td>".
                "<td >".$row["cp_city"].(($row["cp_street"])?", ":" ").$row["cp_street"]."</td><td >".$row["cp_phone1"]."</td><td >P</td></tr>\n";
            $i++;
        }
        if ($rsE) foreach($rsE as $row) {
            echo "<tr  class='bgcol".($i%2+1)."' onClick='showD(\"E\",".$row["id"].");'>".
                "<td >".$row["id"]."</td><td >".$row["name"]."</td>".
                "<td >".$row["addr2"].(($row["addr1"])?", ":" ").$row["addr1"]."</td><td >".$row["workphone"]."</td><td >U</td></tr>\n";
            $i++;
        }
        echo "</tbody></table>\n";
        }
        echo "<br>";
}

    else if ($_GET["kontakt"]) {
}


if ( $_GET['sauto'] || $keineFirma ) {
    include("inc/lxcLib.php");
    $result=GetOwner($_GET['swort']);
    if ( $result ){
        $anzahl = count($result);
        if ( $anzahl >= $_SESSION['listLimit'] ) {
            echo $viele;
        } else {

            if ( $anzahl === 0 ) {$msg = $keine;echo $msg;}
            if ( $anzahl == 1 ) header("Location: lxcmain.php?task=3&c_id=".$result[0]['c_id']);

            echo "<table id='treffer' class='tablesorter'>\n";
            //echo "<thead><tr ><th>KD-Nr</th><th>Name</th><th>Anschrift</th><th>Telefon</th><th></th></tr></thead>\n<tbody>\n";
            echo "<thead><tr><th>Kennzeichen</th><th >Hersteller</th><th >Fahrzeugtyp</th><th >Besitzer</th></tr></thead>\n<tbody>\n";
            foreach($result as $row) {
                echo     "<td onClick='showD(\"A\",".$row["c_id"].");'  >".$row["c_ln"]."</td><td  onClick='showD(\"A\",".$row["c_id"].");' >".$row["c_m"]."</td>".
                        "<td onClick='showD(\"A\",".$row["c_id"].");' >".$row["c_t"]."</td><td onMouseover=\"this.bgColor='#0066FF';\" onMouseout=\"this.bgColor='".$bgcol[($i%2+1)]."';\"  onClick='showD(\"C\",".$row["c_ow"].");'>".$row["owner"]."</td></tr>\n";
                $i++;
            }//end foreach
           echo "</tbody></table>\n";
        }
    }//end if
    else echo $keinFhz;
}
if ( $anzahl > 10 ) {
    echo $head['JQTABLE-PAGER'];
}



    echo $menu['end_content'];
    echo $head['TOOLS'];
    ob_end_flush();
?>
</div>
</body>
</html>