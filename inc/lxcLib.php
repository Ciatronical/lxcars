<?php
/******************************************************************************************  
**** lxcLib.php     Library for LxCars                                                  ***
**** erdacht und geschrieben von Ronny Kumke ronny@lxcars.de                            ***
**** Lizenz 	GPL, Artistic License                                                    ***
******************************************************************************************/  
include_once("../inc/stdLib.php");
include_once("../inc/FirmenLib.php");
include_once("inc/config.php");


function  CheckLxCars(){ //Legt beim ersten Aufruf der Datenbank die benötigten Tabellen an und installiert lxc2db
	global $db;
	$sql = "SELECT version, id FROM lxc_ver ORDER BY datum ASC";
	$rs = $db->getall( $sql );
	
	if( !$rs ){
		echo "Tabellen nicht vorhanden";
		$sql = file_get_contents("lxc-misc/lxc-install.sql");
		$statement = explode(";", $sql );
		$sm0 = '/\/\*.{0,}\*\//';// SuchMuster ' /* bla */ '
		$sm1 = '/--.{0,}\n/';    // SuchMuster ' --bla \n '
		echo "Tabellen werden angelegt ......<\br>";
		foreach( $statement as $key=>$value ){
			$sok0 = preg_replace( $sm0, '',$statement[$key] );
			$sok1 = preg_replace( $sm1, '',$sok0 );
			$rc=$db->query( $sok1 );
			echo "Statement: ".$sok1."</br>";
		}
		echo "....fertig.";
		echo "lxc2db nicht installiert </br> hole dies nun nach ...... </br> Dauert ca 8 Minuten";
		flush();
	}
	/* Erstes Update */
	$last = -1;
	foreach( $rs as $key => $value ){
		$last ++;
	}
	if( $rs[$last]['version'] == "1.4.3-0" ){
		echo "Update erfolgt </br>";
		echo "Zusätzliche Tabellen werden angelegt </br>";
		$sql = file_get_contents("lxc-misc/lxc-update-01.sql");
		$statement = explode(";", $sql );
		$sm0 = '/\/\*.{0,}\*\//';// SuchMuster ' /* bla */ '
		$sm1 = '/--.{0,}\n/';    // SuchMuster ' --bla \n '
		foreach( $statement as $key=>$value ){
			$sok0 = preg_replace( $sm0, '',$statement[$key] );
			$sok1 = preg_replace( $sm1, '',$sok0 );
			$rc=$db->query( $sok1 );
			echo "Statement: ".$sok1."</br>";
		}
	}
	if( $rs[$last]['version'] == "1.4.3-1" ){
		echo "Update erfolgt </br>";
		echo "Zusätzliche Tabellen werden angelegt </br>";
		$sql = file_get_contents("lxc-misc/lxc-update-02.sql");
		$statement = explode(";", $sql );
		$sm0 = '/\/\*.{0,}\*\//';// SuchMuster ' /* bla */ '
		$sm1 = '/--.{0,}\n/';    // SuchMuster ' --bla \n '
		foreach( $statement as $key=>$value ){
			$sok0 = preg_replace( $sm0, '',$statement[$key] );
			$sok1 = preg_replace( $sm1, '',$sok0 );
			$rc=$db->query( $sok1 );
			echo "Statement: ".$sok1."</br>";
		}
	}
		if( $rs[$last]['version'] == "1.4.3-2" ){
		echo "Update erfolgt </br>";
		echo "Zusätzliche Tabellen werden angelegt </br>";
		$sql = file_get_contents("lxc-misc/lxc-update-03.sql");
		$statement = explode(";", $sql );
		$sm0 = '/\/\*.{0,}\*\//';// SuchMuster ' /* bla */ '
		$sm1 = '/--.{0,}\n/';    // SuchMuster ' --bla \n '
		foreach( $statement as $key=>$value ){
			$sok0 = preg_replace( $sm0, '',$statement[$key] );
			$sok1 = preg_replace( $sm1, '',$sok0 );
			$rc=$db->query( $sok1 );
			echo "Statement: ".$sok1."</br>";
		}
	}
}


CheckLxCars();

function NeuerAuftrag($c_id){//erzeugt einen neuen Auftrag,eine neuePosition und gibt die AuftragsID zuruck
	global $db;
	global $tbauf;
	global $tbpos;
	$sql="insert into $tbauf (lxc_a_c_id, lxc_a_text)  values ($c_id, 'Bemerkungen zum Auftrag')";
	$rc=$db->query($sql);
	$sql = "select MAX(lxc_a_id) from ".$tbauf;//die letzte Auftrags_id auswählen
	//ToDo!! kann das insert gleich zurückgeben!!!
	$rsid=$db->getall($sql);
	$a_id=$rsid[0]['max'];
	$sql="insert into $tbpos (lxc_a_pos_aid, lxc_a_pos_todo, lxc_a_pos_doing, lxc_a_pos_parts)  values ($a_id, 'Arbeitstext', 'Antworttext Werkstatt', 'Ersatzteile')";
	$rc=$db->query($sql);
	return $a_id; 
}

function NeuePosition($a_id){
		global $db;
		global $tbpos;
		$sql="insert into $tbpos (lxc_a_pos_aid)  values ($a_id )";
		$rc=$db->query($sql);
		return;
}	
	
function HoleAuftraege( $c_id ){//sucht alle Auftrage zum Fhz mit der CarID und gibt die AuftragsID,... zuruck
	
	global $db;
	global $tbauf;
	global $tbpos;
	$sql = "select lxc_a_id, EXTRACT(EPOCH FROM TIMESTAMPTZ(lxc_a_init_time)),lxc_a_status from $tbauf where lxc_a_c_id = $c_id ORDER BY lxc_a_id" ;
	$rs = $db->getall($sql);
	foreach($rs as $key=>$value){
		$sql="select lxc_a_pos_todo from ".$tbpos." where lxc_a_pos_aid = ".$rs[$key]['lxc_a_id']." ORDER BY lxc_a_pos_id ";
		$rspos=$db->getall($sql);	
		if($rspos[0]){
			$rs[$key]+= $rspos[0];
		}
	$rs[$key]['to_char'] = 	ts2gerdate( $rs[$key]['date_part'] );
	}	
	//$rs['to_char'] = 	ts2gerdate( $rs['lxc_a_init_time'] );	
	return $rs;
}

function HoleAuftragsDaten($a_id) {//Gibt alle Daten des Auftrags mit der a_id zuruck
	global $db;
	global $tbauf;
	global $tbpos;
	$selectstring = "lxc_a_c_id,EXTRACT(EPOCH FROM TIMESTAMPTZ(lxc_a_init_time)), lxc_a_finish_time, lxc_a_modified_from, lxc_a_km, lxc_a_status, lxc_a_text, EXTRACT(EPOCH FROM TIMESTAMPTZ(lxc_a_modified_on))AS modified_time";
	$sql = "select $selectstring from $tbauf where lxc_a_id = $a_id ORDER BY lxc_a_id ";
	$rs=$db->getall($sql);
	$rs[0]['lxc_a_modified_on'] = ts2gerdate($rs[0]['modified_time']);
	$rs[0]['lxc_a_init_time'] = ts2gerdate( $rs[0]['date_part'] );
	return $rs;
} 

function HoleAuftragsPositionen($a_id){//
	global $db;
	global $tbauf;
	global $tbpos;
	$selectstring ="lxc_a_pos_id, lxc_a_pos_todo, lxc_a_pos_doing, lxc_a_pos_parts, lxc_a_pos_time, lxc_a_pos_ctime, lxc_a_pos_emp, lxc_a_pos_status";
	$sql = "select $selectstring from $tbpos where lxc_a_pos_aid = $a_id ORDER BY lxc_a_pos_id ";
	$rspos = $db->getall($sql);
	foreach( $rspos as $key => $value ){
		$rspos[$key]['lxc_a_pos_time'] = DB2Float( $rspos[$key]['lxc_a_pos_time'] ); 
		$rspos[$key]['lxc_a_pos_ctime'] = DB2Float( $rspos[$key]['lxc_a_pos_ctime'] ); 
	}
	return $rspos;
}

function UpdateAuftragsDaten($a_id,$a_data) {
	global $db;
	global $tbauf;
	$a_dbarray=array('lxc_a_finish_time', 'lxc_a_km', 'lxc_a_modified_from', 'lxc_a_modified_on', 'lxc_a_status', 'lxc_a_text');
	$wherestring = "lxc_a_id = $a_id"; 
	$rs=$db->update($tbauf,$a_dbarray,$a_data,$wherestring);
}

function UpdatePosition($pos_id,$posdata){	
	global $db;
	global $tbpos;
	$posdata[4] = Float2DB( $posdata[4] );
	$posdata[6] = Float2DB( $posdata[6] );
	$tmp = array_shift($posdata);
	array_unshift($posdata,$tmp);  // Index muss mit 0 beginnen
	$p_dbarray=array('lxc_a_pos_todo','lxc_a_pos_doing','lxc_a_pos_parts','lxc_a_pos_ctime','lxc_a_pos_status','lxc_a_pos_time','lxc_a_pos_emp');
	$wherestring = "lxc_a_pos_id = $pos_id"; 
       $rs=$db->update($tbpos,$p_dbarray,$posdata,$wherestring);
}

function lxc2db( $parastr ){	
	$rsdata = array();
	$ret = -10;
	$db_name = "lxcars";
	//$command = "./lxc2db -d ".$_SESSION['dbname']." ".$parastr;
	$command = "./lxc2db -d ".$db_name." ".$parastr;	
	exec($command, $rsdata, $ret );
	switch( $ret ){
		case 0: 
			foreach($rsdata as $key=>$value){
				$rs[$key] = explode( ';',$value );
			}
			return $rs;
		break;
		case 2:
			//echo "Keine Daten gefunden... </br>";
			return -1;	
		break;
		default: ;//echo "Konfigurationsfehler! </br> 'less /var/log/apache2/error.log' sollte weiter helfen </br> '/usr/lib/lx-office-crm/lxc-misc/lxc.conf' ansehen </br> (falls vorhanden..)";
	}	
}

function NeuesAuto( $cardata ){ //erzeugt einen neuen Datensatz in der Tabelle lxc_cars
	global $tbname;
	global $db;
	/*
	1. Von zu3 ersten drei Stellen nutzen und Typnr abfragen
	2. Wenn keine Typnummer zurück kommt das geleiche jedoch nur ohne Baujahr
	3. Wenn mehere Typennummern zurückkommen dann Frage nach dem Fahrzeug (macht ShowCar)
	*/	
	$rs = lxc2db( ($cardata['c_d'] == "1900-01-01")?(" -C ".$cardata['c_2']." ".substr($cardata['c_3'],0,3)):(" -CJ ".$cardata['c_2']." ".substr($cardata['c_3'],0,3)." ".$cardata['c_d']) );	
	if( $rs == -1 ){// wenn beispielsweise das Bj nicht passt
		$rs = lxc2db( " -C ".$cardata['c_2']." ".substr( $cardata['c_3'],0,3 ) );
	}		
	if( !$rs[1] ){
		$cardata['c_t'] = $rs[0][0];
	}
	else { //Mehere Fahrzeuge kommen
		$cardata['c_t'] = "";
	// Wird beim Aufruf von ShowCar und anschließenden Update behandelt
	}
	if( $cardata["c_fin"] == "" ) unset( $cardata["c_fin"] );
	$fields = array_keys( $cardata );	
	$values = array_values( $cardata );
	$rc = $db->insert( $tbname, $fields, $values );
	//Kann insert gleich zurückgeben
	$sql = "select MAX(c_id) from $tbname ";//die letzte id auswählen
	$rsid = $db->getall( $sql );
	return $rsid[0]['max'];	
}
	
function UpdateCar( $c_id, $u ){ //Total neu schreiben
	global $db;
	global $tbname;
	$mywahl = $u[mkbwahl];
	$i = 0;
	foreach($u as $key => $value){  // das geht sicher auch ohne i ...
		if( $i == 7  && $u['c_fin'] === '' ){
			$u[c_fin] = "NULL";
		}
		else{
			$u[$key]='\''.$value.'\'';
		}
		$i++;	
	}
	if( $mywahl > 1	){
		$lxc_data = lxc2db( "-T ".$u['typnummer']);	
		foreach( $lxc_data as $key => $value ){
			$mkb[$key] = $value[29];
			$motnr[$key] = $value[3];
		}
		$wmkb = $mkb[$mywahl-2];
		$wmotnr = $motnr[mywahl-2];
		$upmkb = " c_mkb = '$wmkb', ";
		$upc_m = "c_m = '$wmotnr', ";
	}
	else{
		$upmkb = " c_mkb = $u[mkb], ";
		$upc_m = "c_m = '', ";
	}
	$c_t = ( $u[c_t] )?( ", c_t = '".$u[c_t]."' "):(" ");
	$sql="update $tbname SET c_ln = $u[c_ln], c_2 = $u[c_2], c_3 = $u[c_3], c_em = $u[c_em], c_d = $u[c_d], c_hu = $u[c_hu], c_fin = $u[c_fin], $upmkb $upc_m c_color = $u[c_color], c_gart = $u[c_gart], c_st = $u[c_st], c_wt = $u[c_wt], c_st_l = $u[c_st_l], c_wt_l = $u[c_wt_l], c_st_z = $u[c_st_z], c_wt_z = $u[c_wt_z], c_mt = $u[c_mt], c_e_id = $u[c_e_id], c_text = $u[c_text], chk_c_ln = $u[chk_c_ln], chk_c_2 = $u[chk_c_2], chk_c_3 = $u[chk_c_3], chk_c_em = $u[chk_c_em], chk_c_hu = $u[chk_c_hu], chk_fin = $u[chk_fin], c_ow = (SELECT id FROM customer WHERE name ilike $u[chown])  $c_t WHERE c_id = $c_id ";
	//echo "sql: ".$sql;	
	$rc=$db->query($sql);
}

function UpdateTypNr( $c_id, $c_t ){
	global $tbname;	
	global $db;
	$sql="update $tbname SET c_t = '$c_t' WHERE c_id = $c_id";
	$rc=$db->query($sql);
}
	 
function GetCars( $owner ){	//Zeigt Fahrzeuge des Owners ("Kennzeichen", "Hersteller", "Typ","c_id") enthaelt
    global $db;	
    global $bgcol;
    global $tbkba;
    global $tbname;
    $menu =  $_SESSION['menu'];
	?>
	<html>
	<head><title>Fahrzeug auswaehlen</title>
	    <?php echo $menu['stylesheets']; ?> 
        <link type="text/css" REL="stylesheet" HREF="../css/<?php echo $_SESSION["stylesheet"]; ?>/main.css"></link>
        <?php echo $menu['javascripts']; ?>
	</head>
	<body>
	<?php echo $menu['pre_content']; ?> 
	<?php echo $menu['start_content']; ?> 
   <script language="JavaScript">
	<!--
	function showD( owner, c_id ){
		uri1="lxcmain.php?owner=" + owner;
		uri2="&c_id=" + c_id;
		uri3="&task=3"
		uri=uri1+uri2+uri3;
		location.href=uri;
	}
	//-->
	</script>
	<p class="listtop">Fahrzeuge des Kunden <?echo $owner;?></p>

	<?php
	$sql="select c_ln, c_2, c_3, c_id, c_t from $tbname where c_ow = $owner ORDER BY c_id ";//
	$rs=$db->getAll($sql);
	
	echo "<table class=\"liste\">\n";
	echo "<tr class='bgcol3'><th>Kennzeichen</th><th class=\"liste\">Hersteller</th><th class=\"liste\">Fahrzeugtyp</th><th class=\"liste\">Fhz Art</th><th></th></tr>\n";
	$i=0;
	if( $rs ){ //ToDo Lesbarkeit verbessern!!
        foreach($rs as $row) {
            $z2=$rs[$i]['c_2'];//echo " z2 = ".$z2;
			$z3=$rs[$i]['c_3'];//echo " z3 = ".$z3;
			$z3=substr($z3,0,3);
			$art = "Pkw";
            if( $rs[$i]['c_t'] == "" ){
                $rskba = lxc2db( "-C ".$z2." ".$z3 );
				$herst = ($rskba != -1)?($rskba[0][1]):("Keine Daten gefunden");
				$typ = ($rskba != -1)?($rskba[0][2]):("Keine Daten gefunden");
				$name = ($rskba != -1)?($rskba[0][3]):("Keine Daten gefunden");		
				if( $rskba == -1 ){
				    $rs_mykba = GetFhzTyp( $z2, $z3 );
				    $herst = $rs_mykba['hersteller'] != '' ? $rs_mykba['hersteller'] : "Keine Daten eingetragen";
				    $art = $rs_mykba['klasse_aufbau'] != '' ? $rs_mykba['klasse_aufbau'] : "???";;
				    $name = $rs_mykba['typ'] != '' ?  $rs_mykba['typ'] : "Keine Daten eingetragen";
				    $typ = $rs_mykba['bezeichung'];
				}		
			}		
			else{
				//echo "</br> Suche nach Typnr  </br>";
				$rskba = lxc2db( "-T ".$rs[$i]['c_t']	);	
				$herst = ($rskba != -1)?($rskba[0][4]):("Keine Daten gefunden");
				$typ = ($rskba != -1)?($rskba[0][5]):("Keine Daten gefunden");
				$name = ($rskba != -1)?($rskba[0][6]):("Keine Daten gefunden");	
	 	
			}
			echo 	"<tr onMouseover=\"this.bgColor='#0033FF';\" onMouseout=\"this.bgColor='".$bgcol[($i%2+1)]."';\" bgcolor='".$bgcol[($i%2+1)]."'onClick='showD(\"$owner\",".$row["c_id"].");'>".
					"<td class=\"liste\">".$row["c_ln"]."</td><td class=\"liste\">".$herst."</td>".                                           
					"<td class=\"liste\">".$typ." ".$name."</td><td class=\"liste\">".$art."</td></tr>\n";
			$i++;
		}
		echo "</table>\n";
	}
	else{ //echo "Kein Fahrzeug vorhanden..  starte cars.php?task=2 Fahrzeug anlegen";
	    header("Location: lxcmain.php?owner=$owner&task=2");//bei ev. Problemen ganzen Pfad angeben
	}	
	?>	
	<form name="extra" action="lxcmain.php?task=2&owner=<?echo $owner;?>" method="post" >
	   <input type="submit" name="newcar" value="Neues Auto">
	</form>
	<form name="close" action="../firma1.php?Q=C&id=<?echo $owner;?>" method="post">
	   <input type="submit" name="back" value="Zurück">
	</form>
	<?php echo $menu['end_content']; ?> 
	</body>
	</html>
	<?php	
}	

function ShowCar( $c_id ){  //fragt die DB an, schreibt, die Daten nach c_t und zeigt diese im tpl an	
	global $t;	
	global $tbname;/* ToDOTODO Motorcode vorausfüllen!!!!!*/
	global $owner;
	global $tbkba; 	
	
	//echo "SchowCar wird mit der Car ID = ".$c_id." und mit tbname = ".$tbname."  ausgefuehrt";
	global $db;
	$sql="select c_ow, c_ln, c_2, c_3, c_em, c_mkb, c_t, c_d, c_hu, c_fin, c_st, c_wt, c_st_l, c_wt_l, c_mt, c_e_id, c_text,c_st_z, c_wt_z, c_color, c_gart, c_m, chk_c_ln, chk_c_2, chk_c_3, chk_c_em, chk_c_hu,  chk_fin from $tbname where c_id = $c_id ";	
	$rs=$db->getall($sql);
	//print_r($rs);
	$z2=$rs[0]['c_2'];//ToDo wird noch gebraucht
	$z3=$rs[0]['c_3'];
	$z3=substr($z3,0,3);//echo "TEST".$z3;
	/*
	$sql="select amther, amttyp, name, vh, peff, mottyp, energqu, vmaxmax, gmmax, radstand from $tbkba where zu2 = '$z2'  AND zu3 = '$z3' ";
	$rskba=$db->getall($sql);
	Die folgenden Zeilen sind nur bei einem Umstieg von LxCars1.0 auf LxCars1.2 nötig 
	und können bei einer Neuinstallation von Vers. 1.1 kommentiert werden 
	Ist nicht c_t vorhanden? Dann ermitteln und in 
	Ermitteln ob TypNr vergeben wenn nicht dieses tun sonst prüfen ob Typnummer zur KBA passt (Fahrzeugwechsel)	
	*/
	$lxcrs = lxc2db( ($rs[0]['c_d'] == "1900-01-01")?(" -C ".$rs[0]['c_2']." ".substr($rs[0]['c_3'],0,3)):(" -CJ ".$rs[0]['c_2']." ".substr($rs[0]['c_3'],0,3)." ".$rs[0]['c_d']) );	
	if( $lxcrs == -1 ){
		// wenn beispielsweise das Bj nicht passt
		$lxcrs = lxc2db( " -C ".$rs[0]['c_2']." ".substr( $rs[0]['c_3'],0,3 ) );
	}	
	
	// ALso Schleife durch lxcrs prüfen ob Typnr o.k.
	//echo "</br> !!!!!!lxcrs[][]!!!!  </br>";
	//print_r($lxcrs);
	$index = -1;
	if( $lxcrs[0] ){
		foreach( $lxcrs as $key => $value ){
		//echo "</br> value:".$value[0]."  rs[c_t] ".$rs[0]["c_t"];
			if( $value[0] === $rs[0]["c_t"] ){
				$index = $key;
				break;
			}
		}
	}	
	if( $index == -1 ){ //Typnummer nicht oder falsch gespeichert
		//echo "Typnummer nicht gespeichert </br>";
		if( $lxcrs[1] ){		
			$diff = call_user_func_array(array_diff_assoc, $lxcrs );
		}
		if( $diff[0] ){// So hier muss dann wohl was ausgesucht werden
			//print_r( $lxcrs );// 
			//echo "Typnr ".$lxcrs[$key][0]." </br>";			
			//echo "Verschiedene Typnummern !!! Auswahl treffen !!!!! </br>";
	/************Unterbrechung****************/
	?>
	<html>
	<head><title>Fahrzeug auswaehlen</title>
	<link REL="stylesheet" HREF="css/main.css" TYPE="text/css" TITLE="Lx-System stylesheet">
	</head>
	<body >
   <script language="JavaScript">
	<!--
	function SaveTypNr (owner,c_id, c_t) {
		Frame=eval("parent.main_window");
		uri1="lxcmain.php?owner=" + owner;
		uri2="&c_id=" + c_id;
		uri3="&c_t=" + c_t;
		uri4="&task=3";
		uri=uri1+uri2+uri3+uri4;
		location.href=uri;
	}
	//-->
	</script>
	<p class="listtop">Folgende Fahrzeuge stehen zur KBA  <?echo $rs[0]['c_2']." ".substr( $rs[0]['c_3'],0,3 ) ;?> zur Auswahl. Wähle ein Fahrzeug! </p>

	<?php
	echo "<table class=\"liste\">\n";
	echo "<tr class='bgcol3'><th>Hersteller</th><th class='bgcol3'>Baureihe   </th><th class='bgcol3'>Typ    </th><th class='bgcol3'>von Bj   </th><th class='bgcol3'>bis Bj</th></tr>\n";		
	$i = 0;	
	foreach( $lxcrs as $key=>$row ){
		echo 	"<tr onMouseover=\"this.bgColor='#0033FF';\" onMouseout=\"this.bgColor='".$bgcol[($i%2+1)]."';\" bgcolor='".$bgcol[($i%2+1)]."'onClick='SaveTypNr(\"$owner\",".$c_id.",\"".$lxcrs[$key][0]."\");'>".
					"<td class=\"liste\">".$lxcrs[$key][1]."</td><td class=\"liste\">".$lxcrs[$key][2]."</td>".                                           
					"<td class=\"liste\">".$lxcrs[$key][3]."</td><td class=\"liste\">".$lxcrs[$key][7]."</td><td class=\"liste\">".$lxcrs[$key][8]."</td></tr>\n";	
		$i++;
	}
	echo "</table>\n";
		
	?>	
	
	
		</html>
	<?php		
		
		/******************Ende der Unterbrechung*******************/
		}
		else{ //Wenn typnummer einmalig, kann gespeichert werden
			//echo "Suche Datensatz ..........";
			$sql = "update ".$tbname." SET c_t = '".$lxcrs[0][0]."' WHERE c_id = ".$c_id;
			$db->query($sql);
			$rs[0]['c_t'] = $lxcrs[0][0];// wichtig
		}
		
		
	}
	$lxc_data = lxc2db( "-T ".$rs[0]['c_t']);	
	// Motorkennbuchstabenauswahl ... 	

	if( $lxc_data ){//ToDo geht einfacher??	
		foreach( $lxc_data as $key => $value ){
			$mkb[$key] = $value[29];
		}
	}
	$drop = '<select tabindex="10" name="mkbwahl"><option value="1" selected>&#160;Motor&#160;&#160;';
	if( $mkb ){	
		foreach( $mkb as $key => $value ){
			$key+=2;
			$drop.='<option value="'.$key.'" > '.$value;
		}
	}
	$drop.='</select>';
	// Wenn nur ein Motor in Frage kommt und kein MKB in der DB gespeichert dann Feld für MKB mit diesem Wert vobelegen
	$mkbtpl = $rs[0]['c_mkb'];	
	if( !$mkb[1] && $rs[0]["c_mkb"] == "" ){
		$mkbtpl = $mkb[0];
	}
	$g_art_drop = '<select name="g_art_drop"><option value="-1" selected>Getriebeart&#160;';	
				$sql = "SELECT c_gart, count(c_gart) FROM lxc_cars WHERE c_gart != '' GROUP BY c_gart ORDER BY count DESC";
			//echo $sql;			
			$rs_g_art = $db->getall($sql);
			foreach( $rs_g_art as $value ){
				$g_art_drop.='<option value="'.$value['c_gart'].'" > '.$value['c_gart'];
			}
	$g_art_drop.='</select>';
	$rskba = lxc2db( "-KBA ".$rs[0]['c_2']." ".substr( $rs[0]['c_3'],0,3 ) );
	$kba = true;
	$vmax = $rskba[0][0];
	$radstand = $rskba[0][2];
	$masse = $rskba[0][1];
	if( $rskba == -1 ){
	    $kba = false;//für den MyKBA Button
	    $mykba = GetFhzTyp( $rs[0]['c_2'], substr( $rs[0]['c_3'],0,3 ) );
	    $lxc_data[0][4] = $mykba['hersteller'];
	    $lxc_data[0][5] = $mykba['bezeichung'];
	    $lxc_data[0][6] = $mykba['typ'];
	    $lxc_data[0][30] = $mykba['ventile'];
	    $ks = $mykba['kraftstoff'];
	    $lxc_data[0][13] = $mykba['zylinder'];
	    $vmax = $mykba['geschwindigkeit'];
	    $lxc_data[0][9] = $mykba['leistung_drehz'];
	    $lxc_data[0][12] = $mykba['hubraum_ccm'];
	    $radstand = $mykba['radstand'];
	    $masse = $mykba['masse_leer'];
	    
	    
	    
	};
	$c_fin=substr($rs[0]['c_fin'],0,17);
	$c_d=db2date($rs[0]['c_d']);
	$c_hu=db2date($rs[0]['c_hu']);
	
	$chk_c_ln = ( $rs[0]['chk_c_ln'] == 't' ) ? ( 'checked="checked"' ) : ( '' );
	$chk_c_2 = ( $rs[0]['chk_c_2'] == 't' ) ? ( 'checked="checked"' ) : ( '' );
	$chk_c_3 = ( $rs[0]['chk_c_3'] == 't' ) ? ( 'checked="checked"' ) : ( '' );
	$chk_c_em = ( $rs[0]['chk_c_em'] == 't' ) ? ( 'checked="checked"' ) : ( '' );
	$chk_c_hu = ( $rs[0]['chk_c_hu'] == 't' ) ? ( 'checked="checked"' ) : ( '' );
	$chk_fin = ( $rs[0]['chk_fin'] == 't' ) ? ( 'checked="checked"' ) : ( '' );
	 
	//print_r($rs);
	$c_mt=date("d.m.Y H:i:s",$rs[0]['c_mt']);	
	$ownerarray=getFirmenStamm($rs[0]['c_ow']);
	$retarray = array(ownerstring=>$ownerarray['name'], street=>$ownerarray['street'], city=>$ownerarray['city'],phone=>$ownerarray['phone'], mobile=>$ownerarray['fax'],
							owner=>$rs[0]['c_ow'], task=>$task, c_id=>$c_id, c_ln=>$rs[0]['c_ln'],c_2=>$rs[0]['c_2'],c_3=>$rs[0]['c_3'],c_em=>$rs[0]['c_em'],c_color=>$rs[0]['c_color'],c_gart=>$rs[0]['c_gart'],
							cm=>$lxc_data[0][4],ct=>$lxc_data[0][5]." ".$lxc_data[0][6], c_d=>$c_d, c_hu=>$c_hu, fin=>$c_fin,cn=>$rs[0]['c_fin'][17],c_st=>$rs[0]['c_st'],c_t => $rs[0]['c_t'],
							bj=>$lxc_data[0][7]." - ".$lxc_data[0][8],vent=>$lxc_data[0][30],drehm=>$lxc_data[0][33],verd=>$lxc_data[0][32],mkbdrop=>$drop, g_art_drop=>$g_art_drop,
							c_wt=>$rs[0]['c_wt'],c_st_l=>$rs[0]['c_st_l'],c_wt_l=>$rs[0]['c_wt_l'],c_st_z=>$rs[0]['c_st_z'],c_wt_z=>$rs[0]['c_wt_z'],c_text=>$rs[0]['c_text'],peff=>$lxc_data[0][9]." kW / ".$lxc_data[0][10]." PS",vh=>$lxc_data[0][12]." ccm",
							mottyp=>"reihe",zyl=>$lxc_data[0][13],ks=>$ks,vmax=>$vmax." km/h",mmax=>$masse." kg",c_e_string=>$rs[0]['c_e_id'],mdate=>$c_mt,mkb=>$mkbtpl,radstand=>$radstand, chk_c_ln=>$chk_c_ln, chk_c_2=>$chk_c_2, chk_c_3=>$chk_c_3,
							chk_c_em=>$chk_c_em, chk_c_hu=>$chk_c_hu, chk_fin=>$chk_fin, kba => $kba );
	return $retarray;
}   
   
function GetOwner( $c_ln ){//sucht KZ und gibt einen Array(c_ln, c_m, c_t, c_id, owner) zurück
 	global $tbname;
 	global $tbkba;
	global $db;
   $sql="select c_ln, c_t, c_id, c_ow from $tbname where c_ln ILIKE '\%$c_ln\%' ORDER by c_ow";	
	$rs=$db->getall($sql);
   if($rs){
   	foreach($rs as $index=>$el){
   		//ToDo ToDo sql und ownerarry durch sql mit join ersetzen

			$ownerarray=getFirmenStamm($rs[$index]['c_ow']);
			$rst = lxc2db( " -T ".$rs[$index]['c_t'] );
			$rsall[$index]=array("c_ln"=>$rs[$index]['c_ln'],"c_m"=>$rst[0][4],"c_t"=>$rst[0][5]." ".$rst[0][6], "c_id"=>$rs[$index]['c_id'],"c_ow"=>$rs[$index]['c_ow'],"owner"=>$ownerarray['name']);
   	}	
  }
  return $rsall;
}
   
   
 /* ++++ Input :=	Assoziatives Array c_ln => "MOL-RK73", zu2 => "0039" ++++
   ++++ Output:=  Array mit Fahrzeugdaten und deren Besitzer 			  ++++     */   
function SucheCars($was) { // c_id, c_ow, c_ln, c_2, c_3, c_em, c_d, c_hu, c_fin, c_st, c_wt, c_st_l, c_wt_l, c_text
	global $tbname;
	global $db;
	global $tbkba;
	$where = "";
	$i = 0;
	if( !$was ){exit;}
	//print_r($was);
	foreach($was as $key => $value) {
		if($value&&($key!='suche')&&($key!='filter')){
	   	if($i>0){$and = "AND";}
	   	$i++;//echo "<br>"."I=".$i;
	   	$opva = " ILIKE '\%$value\%' "; //Operand Value
			/*if($key == c_ow){
				$rs_all = getAllFirmen($value);
				$opva = "( ";
				foreach($rs_all as $firma){ weiter machen!!!
			*/		 	   	
	   	if($key == c_d_gg){
	   		$value = date2db($value);
	   		$opva = " >= '$value' ";
	   		$key = "c_d";
	   	}
	   	if($key == c_d_kg){
	   		$value = date2db($value);
	   		$opva = " <= '$value' ";
	   		$key = "c_d";
	   	}
	   	if($key == c_hu_gg){
	   		$value = date2db($value);
	   		$opva = " >= '$value' ";
	   		$key = "c_hu";
	   	}
	   	if($key == c_hu_kg){
	   		$value = date2db($value);
	   		$opva = " <= '$value' ";
	   		$key = "c_hu";
	   	}
	   	if($key == c_ow){
				$opva = " ilike '%".$value."%' ";
	   		$key = "name";
	   	}
	   	$where = $where." ".$and." ".$key.$opva;
	   	//echo "WHERE  ".$where;
	   }
	}
	if( $where ){
		$sql = "select c_ln, c_2, c_3, c_id, c_ow, name, city from lxc_cars JOIN customer ON c_ow = id where $where ORDER by c_ow"; //echo "SucheCars mit where-String: $where ";
		$rs = $db->getall($sql); //print_r($rs);
	}	
	if($rs){
   	foreach($rs as $key=>$vaule){
   		$z2=$rs[$key]['c_2'];
			$z3=substr($rs[$key]['c_3'],0,3);
			$lxcrs = lxc2db( " -C ".$z2." ".$z3 );
 			$rsall[$key]=array("z2"=>$z2,"z3"=>$z3,"c_ln"=>$rs[$key]['c_ln'],"c_m"=>$lxcrs[0][1],"c_t"=>$lxcrs[0][2]." ".$lxcrs[0][3], "c_id"=>$rs[$key]['c_id'],"c_ow"=>$rs[$key]['c_ow'],"Name"=>$rs[$key]['name'],"Ort"=>$rs[$key]['city']);
   	}
   }	
  	return $rsall;
}
/*Liest aus src die Zeile $was und gibt einen Array mit dazwischen zurück*/
function Lies($src, $was, $davor, $danach){
	global $filter;		
	$offset = 88500;
	$i = 0;
	while(stripos($src,$was,$offset)) {	
		$i++;
		//echo "Durchgang=".$i;	
		$pos = stripos($src,$was,$offset);
		$offset = $pos + 10;
		$lenge = 130;
		//echo "Position: ".$pos;
		$result = substr($src, $pos, $lenge);
		$pos1 = stripos($result, $davor);
		//if(!$pos){echo "Fehler bei KBA: ".$zu2." ".$zu3." Durchlauf: ".$i;} 
		$pos1 += strlen($davor);
		$pos2 = stripos($result, $danach);
		//echo "Position 1: ".$pos1." ";
		//echo "Position 2: ".$pos2." ";
		$len = $pos2 - $pos1;
		$result = substr($result, $pos1,$len );//
		$result = str_replace(' ','',$result);
		//echo stripos($result, "C");
		//if(stripos($result, "C")!==0){echo "Fehler bei KBA: ".$zu2." ".$zu3." Durchlauf: ".$i;} 
		//echo "Filter: ".$result." ";
		if(array_key_exists($result, $filter)){
			//echo "Filter schon vorhanden";
			$filter[$result]++;
		}
		else {
			$filter[$result] = 1;
		} 
				
	}
	return $filter;
}	
function SucheAuftrag( $was ){
	global $tbauf;
	global $db;
	if( $was['c_ln'] ){
		$where = " c.c_ln ilike '%".$was['c_ln']."%' "; 
		$and = "AND";
	}
	if( $was['c_ow'] ){
		$where = $where." ".$and." e.name ilike '%".$was['c_ow']."%' ";
		$and = "AND";
	}
	if( $was['lxc_a_pos_todo'] ){
		$where = $where." ".$and." d.lxc_a_pos_todo ilike '%".$was['lxc_a_pos_todo']."%' ";
		$and = "AND";
	}
	if( $was['lxc_a_pos_doing'] ){
		$where = $where." ".$and." d.lxc_a_pos_doing ilike '%".$was['lxc_a_pos_doing']."%' ";
		$and = "AND";
	}
	if( $was['c_d_gg'] ){
		$zeit = date2db( $was['c_d_gg'] );
		$where = $where." ".$and."  a.lxc_a_init_time >= '".$zeit."' ";
		$and = "AND";
	}
	if( $was['c_d_kg'] ){
		$zeit = date2db( $was['c_d_kg'] );
		$where = $where." ".$and."  a.lxc_a_init_time <= '".$zeit."' ";
		$and = "AND";
	}
	if( $was['lxc_a_text'] ){
		$where = $where." ".$and." a.lxc_a_text ilike '%".$was['lxc_a_text']."%' ";
		$and = "AND";
	}
	if( $was['lxc_a_status'] > 0 && $was['lxc_a_status'] != '4' ){
		$where = $where." ".$and." a.lxc_a_status = ".$was['lxc_a_status']." ";
		$and = "AND";
	}
		if( $was['lxc_a_status'] == '4' ){
		$where = $where." ".$and." a.lxc_a_status != '3' " ;
		$and = "AND";
	}
	if( $was['selbstgeschrieben'] ){
		$where = $where." ".$and." a.lxc_a_modified_from = '".$was['emp']."' ";
		$and = "AND";
	}
	if( $was['lxc_a_pos_emp'] ){
		$where = $where." ".$and." d.lxc_a_pos_emp = '".$was['lxc_a_pos_emp']."' ";
		$and = "AND";
	}
	
	//$sql = "SELECT  a.lxc_a_id, c.c_ln, d.lxc_a_pos_todo, to_char(a.lxc_a_init_time,'Day, DD Mon YYYY HH24:SS')  FROM lxc_a a JOIN lxc_cars c ON a.lxc_a_c_id = c.c_id JOIN lxc_a_pos d ON a.lxc_a_id = d.lxc_a_pos_aid JOIN customer e ON c.c_ow = e.id WHERE $where GROUP BY a.lxc_a_id SORT BY d.lxc_a_pos_id DESC, a.lex_a_id ASC ";
	$sql = "SELECT DISTINCT ON (a.lxc_a_id) a.lxc_a_id, c.c_ln, e.name, d.lxc_a_pos_todo, c.c_id, EXTRACT(EPOCH FROM TIMESTAMPTZ(a.lxc_a_init_time)), c.c_ow FROM lxc_a a JOIN lxc_cars c ON a.lxc_a_c_id = c.c_id JOIN lxc_a_pos d ON a.lxc_a_id = d.lxc_a_pos_aid JOIN customer e ON c.c_ow = e.id WHERE $where ORDER BY a.lxc_a_id ASC, d.lxc_a_pos_id ASC";	
	//echo "SQL: ".$sql;
	if( $where ){	
		$rs = $db->getall($sql);
	}
	if( $rs ){
		foreach( $rs as $key => $value ){
			$rs[$key]['to_char'] = 	ts2gerdate( $rs[$key]['date_part'] );
		}
	}
	//print_r( $rs );
	return $rs;
		
	
}	

function Float2DB( $wert ){
	$pos = strpos( $wert, "," );
  	if( $pos ){
		$tmp = substr_replace( $wert, ".", $pos, 1 );
  	}	
  	else{
    	$tmp = $wert;
  	}
  	$tmp = (float)$tmp;
  	if( $tmp > 50 ){
		return 0;
	}
  	else{
    	return $tmp;
  	}
}
// wandelt 2.4 in 2,4    
function DB2Float($wert){
  	$pos = strpos( $wert, "." ); 
 	if( $pos ){
	 	return substr_replace( $wert, ",", $pos, 1 );
  	}	
  	else{
   	return $wert;
  	}
}

function ts2gerdate( $myts ){	
	if( $myts ){		
		$wochentag = array( "Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag" );
		$Monate = array( "Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember" );
		$wt = $wochentage[date( "w",$myts )];
		$day = date( "d",$myts );			
		$m = $Monate[date( "m",$myts )-1];
		$year = date( "Y", $myts );
		$hour = date( "H", $myts );
		$minute = date( "i", $myts );
		return $wt." ".$day.".".$m." ".$year." ".$hour.":".$minute;
	}
}


function SucheFin( $zu2, $zu3 ){ //Sucht mit der KBA einen passenden Anfang fur die FIN 
	global $db;
	global $tbname;
	$erfolg = 0;
	$zu3 = substr( $zu3, 0, 3 );
	for ($i = 11; $i >= 3; $i--) {
		$sql = "SELECT MAX( fin ) AS fin, COUNT( fin )FROM( SELECT DISTINCT SUBSTR( c_fin, 1, ".$i." ) AS fin FROM $tbname WHERE c_2 = '".$zu2."' AND c_3 LIKE '".$zu3."%' ORDER BY fin ) AS rs";    		
		$rs = $db->getall($sql);    		
		if( $rs[0]['count'] == "1" ){
			$fin = $rs[0]['fin'];
			$erfolg = 1; 
			break;				
		}
	}
	if( $erfolg == 0 ){	
		$sql = "SELECT  SUBSTR( c_fin, 1, 3 ) AS fin , count( SUBSTR( c_fin, 1, 3 )) AS c FROM lxc_cars WHERE c_2 = '".$zu2."' GROUP BY SUBSTR( c_fin, 1, 3 ) ORDER BY c DESC LIMIT 1";
		$rs = $db->getall($sql);
		$fin = $rs[0]['fin'];
	}
	$objResponse = new xajaxResponse();
	$objResponse->assign("idfin", "value", $fin);
	return $objResponse;	
}


function SucheMkb( $zu2, $zu3 ){
	$lxcrs = lxc2db( " -C ".$zu2." ".substr( $zu3,0,3 ) );
	if( $lxcrs[0] ){
		$lxc_data = lxc2db( "-T ".$lxcrs[0][0]);
	}
	if( $lxc_data ){	
	
		$ret =  '<option value="0">Wähle Motor&#160;</option>';
		foreach( $lxc_data as $key => $value ){
		
			$ret .= '<option value="'.$value[29].'">'.$value[29].'</option>';
		}
	}	

	$objResponse = new xajaxResponse();
	$objResponse->assign('mkbdrop', 'innerHTML', $ret);
	return $objResponse;	
}


function UniqueKz( $kz, $c_id ){
	global $db;
	global $tbname;
	$sql = "SELECT name, c_id FROM customer CROSS JOIN $tbname WHERE c_ow = id AND c_ln =  '".$kz."'";
	$rs = $db->getall( $sql );
	$objResponse = new xajaxResponse();
	if( $rs[0] && $c_id != $rs[0][c_id] ){
		$objResponse->alert("Ein Datensatz mit dem Kennzeichen $kz existiert bereits! \nDas Fahrzeug gehört ".$rs[0][name].".");
		//$objResponse->alert("c_id: ".$c_id."sql_cid: ".$rs[0][c_id]);
		return $objResponse;
	}
	elseif( XajaxVer == "05" ){//Ist das so richtig?
		$objResponse->setReturnValue(true);
		return $objResponse;
	}
}

function UniqueFin( $fin, $c_id ){
	global $db;
	global $tbname;
	if( strlen( $fin ) == 17 ) $sql = "SELECT c_id, c_ln, name, c_fin FROM customer CROSS JOIN lxc_cars WHERE c_ow = id AND c_fin like '".$fin."_'";
	else $sql = "SELECT c_id, c_ln, name FROM customer CROSS JOIN $tbname WHERE c_ow = id AND c_fin = '".$fin."'";
	$rs = $db->getall( $sql );
	$objResponse = new xajaxResponse();
	if( $rs[0] && $c_id != $rs[0][c_id] && $fin != ""){	
		$objResponse->alert("Ein Datensatz mit dieer FIN existiert bereits!\nDas Fahrzeug gehört ".$rs[0][name].", das Kennzeichen lautet ".$rs[0][c_ln].".");
		return $objResponse;
	}
	elseif( XajaxVer == "05" ){//Ist das so richtig?
		$objResponse->setReturnValue(true);
		return $objResponse;
	}
}

function NewFhzTyp( $data ){
    global $db;
    $tb = "lxc_mykba";
    $fields = array_keys( $data );	
	$values = array_values( $data );
    $rc = $db->insert( $tb, $fields, $values );    
}

function GetFhzTyp( $hsn, $tsn ){
    global $db;
    $sql = "SELECT * from lxc_mykba WHERE hsn = '$hsn' AND tsn = '$tsn'";
    $rs = $db->getall( $sql );    
    return $rs[0];    
}

function UpdateFhzTyp( $data ){
    global $db;
	$tb = "lxc_mykba";
	unset( $data['owner'], $data['c_id'],$data['ERPCSS'], $data['update'], $data['msg'] );
    $wherestring = "id = ".array_shift( $data ); 
	$fields = array_keys( $data );	
	$values = array_values( $data );
	$rs = $db->update( $tb, $fields, $values, $wherestring );
}

function CleanArray( $array ){
    foreach($array as $key => $value) {
        if($value == '') {
            unset( $array[$key] );
        }   
    }   
    return $array;
} 


?>
