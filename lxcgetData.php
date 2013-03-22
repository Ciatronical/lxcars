<?php
ob_start(); 
require_once("../inc/stdLib.php");
include("../inc/crmLib.php");
$menu = $_SESSION['menu'];
$head = mkHeader();
?>
<html>
<head><title></title>
    <script language="JavaScript">

	function showD (src,id) {
		if      (src=="C") {	uri="../firma1.php?Q=C&id=" + id }
		else if (src=="V") {	uri="../firma1.php?Q=V&id=" + id; }
		else if (src=="E") {	uri="../user1.php?id=" + id; }
		else if (src=="K") {	uri="../kontakt.php?id=" + id; }
		window.location.href=uri;
	}
	function showCar (c_id){
		if (c_id) {
			uri="lxcmain.php?task=3&c_id=" + c_id;
			window.location.href=uri;
		}
	}
   	</script>
<?php 
	 echo $menu['stylesheets'].'
    <link type="text/css" REL="stylesheet" HREF="'.$_SESSION["basepath"].'crm/css/'.$_SESSION["stylesheet"].'/main.css">
    <link rel="stylesheet" type="text/css" href="'.$_SESSION['basepath'].'crm/jquery-ui/themes/base/jquery-ui.css"> 
    <script type="text/javascript" src="'.$_SESSION['basepath'].'crm/jquery-ui/jquery.js"></script> 
    <script type="text/javascript" src="'.$_SESSION['basepath'].'crm/jquery-ui/ui/jquery-ui.js"></script>'; 
    echo $head['CRMCSS'];
	echo $head['JQUERY']; 
	echo $head['JQUERYUI']; 
	echo $head['THEME'];
    echo $head['JQTABLE'];
    if ($_SESSION['feature_ac']) { 
        echo '   
    <style>
        .ui-autocomplete-category {
            font-weight: bold;
            padding: .2em .4em;
            margin: .8em 0 .2em;
            line-height: 1.5;
        }
    </style>
    <script>
        $(function() {
            $("#dialog").dialog();
        });
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
                    if(ui.item.src==\'CAR\'){
                        showCar(ui.item.c_id);
                    }
                    else{
                        showD(ui.item.src,ui.item.id);
                    }
                }
            });
        });
    </script>'; 
    }//end feature_ac 
    ?> 
    <style>
    table.tablesorter {
	   width: 900;
    }    
    </style>
</head>
<body onload=$("#ac0").focus().val('<?php echo preg_replace("#[ ].*#",'',$_GET['swort']);?>');>
<?php //wichtig: focus().val('ohneLeerZeichen')
echo $menu['pre_content'];
echo $menu['start_content'];
    $formular = '<p class="listtop">Schnellsuche Kunde/Lieferant/Kontakte und Kontaktverlauf <?php echo ($telnum)?"Telefonunummer: ".$telnum:""; ?></p>';
    $formular .= '<form name="suche" action="lxcgetData.php?telnum='.$telnum.' method="get">';
    $formular .= '<input type="text" name="swort" size="20" id="ac0" autocomplete="off">';  
    $formular .= '<input type="submit" name="adress" id="adress" value="Kunde o. Lief.">';
    $formular .= '<input type="submit" name="sauto" value="Kennzeichen">';
    $formular .= '<input type="submit" name="kontakt" value="Kontaktverlauf"> <br>';
    $formular .= '<span class="liste">Suchbegriff</span></form>';
    print $formular;
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
	include("inc/FirmenLib.php");
	include("inc/persLib.php");
	include("inc/UserLib.php");    
	$found=false;
	$suchwort=mkSuchwort($_GET["swort"]);
	$anzahl=0;
	$db->debug=0;
    $rsE=getAllUser($suchwort);
	if (chkAnzahl($rsE,$anzahl) && $_GET["swort"]) {
		$rsV=getAllFirmen($suchwort,true,"V");	
		if (chkAnzahl($rsV,$anzahl)) {
			$rsC=getAllFirmen($suchwort,true,"C");
			if (chkAnzahl($rsC,$anzahl)) {
				$rsK=getAllPerson($suchwort);
				if (!chkAnzahl($rsK,$anzahl)) {
					$msg=$viele;
				} else {
					if ($anzahl===0) {$keineFirma=1;}
				} 
			} else {
				$msg=$viele;
			}
		} else {
			$msg=$viele;
		}
	} 
    if ($anzahl>0) {
        if ($anzahl==1 && $rsC) header("Location: ../firma1.php?Q=C&id=".$rsC[0]['id']); 
        if ($anzahl==1 && $rsV) header("Location: ../firma1.php?Q=V&id=".$rsV[0]['id']); 
        if ($anzahl==1 && $rsK) header("Location: ../kontakt.php?id=".$rsK[0]['id']); 
        if ($anzahl==1 && $rsE) header("Location: ../user.php?id=".$rsE[0]['id']); 

		echo "<table id='treffer' class='tablesorter'>";
		echo "<tr class='bgcol3'><th>KD-Nr</th><th class=\"liste\">Name</th><th class=\"liste\">Anschrift</th><th class=\"liste\">Telefon</th><th></th></tr>\n";
		$i=0;
		if ($rsC) foreach($rsC as $row) {
			echo "<tr  class='bgcol".($i%2+1)."' onClick='showD(\"C\",".$row["id"].");'>".
				"<td class=\"liste\">".$row["customernumber"]."</td><td class=\"liste\">".$row["name"]."</td>".
				"<td class=\"liste\">".$row["city"].(($row["street"])?",":"").$row["street"]."</td><td class=\"liste\">".$row["phone"]."</td><td class=\"liste\">K</td></tr>\n";
			$i++;
		}
		if ($rsV) foreach($rsV as $row) {
			echo "<tr  class='bgcol".($i%2+1)."' onClick='showD(\"V\",".$row["id"].");'>".
				"<td class=\"liste\">".$row["vendornumber"]."</td><td class=\"liste\">".$row["name"]."</td>".
				"<td class=\"liste\">".$row["city"].(($row["street"])?",":"").$row["street"]."</td><td class=\"liste\">".$row["phone"]."</td><td class=\"liste\">L</td></tr>\n";
			$i++;
		}
		if ($rsK) foreach($rsK as $row) {
			echo "<tr  class='bgcol".($i%2+1)."' onClick='showD(\"K\",".$row["id"].");'>".
				"<td class=\"liste\">".$row["cp_id"]."</td><td class=\"liste\">".$row["cp_name"].", ".$row["cp_givenname"]."</td>".
				"<td class=\"liste\">".$row["addr2"].(($row["addr1"])?",":"").$row["addr1"]."</td><td class=\"liste\">".$row["cp_phone1"]."</td><td class=\"liste\">P</td></tr>\n";
			$i++;
		}
		if ($rsE) foreach($rsE as $row) {
			echo "<tr  class='bgcol".($i%2+1)."' onClick='showD(\"E\",".$row["id"].");'>".
				"<td class=\"liste\">".$row["id"]."</td><td class=\"liste\">".$row["name"]."</td>".
				"<td class=\"liste\">".$row["addr2"].(($row["addr1"])?",":"").$row["addr1"]."</td><td class=\"liste\">".$row["workphone"]."</td><td class=\"liste\">U</td></tr>\n";
			$i++;
		}
        echo "</table>\n";
        }  
        echo "<br>"; 
    } else if ($_GET["kontakt"]) {
?>
<script language="JavaScript">
	sw="<?php echo  $_GET["swort"]; ?>";
	if (sw != "") 
		F1=open("suchKontakt.php?suchwort="+sw+"&Q=S","Suche","width=400, height=400, left=100, top=50, scrollbars=yes");
</script>	
<? }
if($_GET['sauto']){
	include("inc/lxcLib.php");
	$result=GetOwner($_GET['swort']);
    if ($result){
	    if (!chkAnzahl($result,$anzahl)) {
            echo $viele; 
        } else {
            if ($anzahl==0) {$msg = $keine;echo $msg;}
            if ($anzahl==1) header("Location: lxcmain.php?task=3&c_id=".$result[0]['c_id']);
 
		    echo "<table id='treffer' class='tablesorter'>\n";
		    echo "<tr class='bgcol3'><th>Kennzeichen</th><th class=\"liste\">Hersteller</th><th class=\"liste\">Fahrzeugtyp</th><th class=\"liste\">c_id</th><th class=\"liste\">Besitzer</th></tr>\n";
		    foreach($result as $row) {
			    echo 	"<tr class='bgcol".($i%2+1)."' onClick='showD(\"V\",".$row["id"].");'>". 
					    "<td onClick='showCar(".$row["c_id"].");' class=\"liste\" >".$row["c_ln"]."</td><td  onClick='showCar(".$row["c_id"].");' class=\"liste\">".$row["c_m"]."</td>".                                           
					    "<td onClick='showCar(".$row["c_id"].");' class=\"liste\">".$row["c_t"]."</td><td class=\"liste\">".$row["c_id"]."</td><td onMouseover=\"this.bgColor='#0066FF';\" onMouseout=\"this.bgColor='".$bgcol[($i%2+1)]."';\" class=\"liste\" onClick='showD(\"C\",".$row["c_ow"].");'>".$row["owner"]."</td></tr>\n";
			    $i++;
		    }//end foreach
		   echo "</table>\n";
        }
	}//end if
	else echo $keinFhz; 
}
if($keineFirma){
    include("lxcars/inc/lxcLib.php");
	$result=GetOwner($_GET['swort']);
    if ($result){
        if (!chkAnzahl($result,$anzahl)) {
            $msg=$viele; echo $msg;
        } else {
            if ($anzahl==0) {$msg = $keine;echo $msg;}
            if ($anzahl==1) header("Location: lxcmain.php?task=3&c_id=".$result[0]['c_id']);

		    echo "<table id='treffer' class='tablesorter'>\n";
		    echo "<tr class='bgcol3'><th>Kennzeichen</th><th class=\"liste\">Hersteller</th><th class=\"liste\">Fahrzeugtyp</th><th class=\"liste\">c_id</th><th class=\"liste\">Besitzer</th></tr>\n";
		    foreach( $result as $row ){
			    echo    "<tr  class='bgcol".($i%2+1)."' onClick='showD(\"C\",".$row["id"].");'>".
				    	"<td onClick='showCar(".$row["c_id"].");' class=\"liste\" >".$row["c_ln"]."</td><td  onClick='showCar(".$row["c_id"].");' class=\"liste\">".$row["c_m"]."</td>".                                           
					    "<td onClick='showCar(".$row["c_id"].");' class=\"liste\">".$row["c_t"]."</td><td class=\"liste\">".$row["c_id"]."</td><td onMouseover=\"this.bgColor='#0033FF';\" onMouseout=\"this.bgColor='".$bgcol[($i%2+1)]."';\" class=\"liste\" onClick='showD(\"C\",".$row["c_ow"].");'>".$row["owner"]."</td></tr>\n";
			    $i++;
	    	} 
		    echo "</table>\n";
		}
	}//end if
	else echo $keinFhz; 
}	
?>
   <span id="pager" class="pager">
        <form>
            <img src="<?php echo $_SESSION['baseurl']; ?>crm/jquery-ui/plugin/Table/addons/pager/icons/first.png" class="first"/>
            <img src="<?php echo $_SESSION['baseurl']; ?>crm/jquery-ui/plugin/Table/addons/pager/icons/prev.png" class="prev"/>
            <img src="<?php echo $_SESSION['baseurl']; ?>crm/jquery-ui/plugin/Table/addons/pager/icons/next.png" class="next"/>
            <img src="<?php echo $_SESSION['baseurl']; ?>crm/jquery-ui/plugin/Table/addons/pager/icons/last.png" class="last"/>
            <select class="pagesize" id='pagesize'>
                <option value="10">10</option>
                <option value="20" selected>20</option>
                <option value="30">30</option>
                <option value="40">40</option>
            </select>
        </form>
    </span>
<?php
    
    echo $menu['end_content'];
    ob_end_flush(); 
?>
