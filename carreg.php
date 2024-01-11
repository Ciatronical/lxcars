<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8' />

<?php
    require_once '../inc/stdLib.php';
    $menu = $_SESSION['menu'];
    echo $menu['stylesheets'];
    echo $menu['javascripts'];
    echo $head['IBAN'];
    echo '<script type="text/javascript" src="js/carreg.js"></script>';
    echo $head['JQTABLE'];
    echo $head['THEME'];
    echo $head['T8'];// Übersetzung mit kivi.t8

    /*****************************************************************************************************************************
    Grundsätze: Content wird via Ajax im Json-Format geholt und an die entsprechenden Container verteilt
                Daten werden ohne Reload einfach via Ajax gespeichert.
                Fürs Holen und Schreiben von Daten befindet sich unter ajax eine gleichnamige Datei mit der Extension ".php"
                Auf das Benutzen der Variable $_SESSION sollte weitestgehend verzichtet werden.
                Statt JS einzusetzen sollte auf die jQuery-Methoden zurückgegriffen werden.
                Url-Parameter werden als hash gefolgt von einem JSON übergeben.
                Vorteil: Browserhistory funktioniert, F5 funktioniert,
                Test1: http://localhost/kivitendo/crm/example.phtml
                Test2: http://localhost/kivitendo/crm/example.phtml#{"name":"Widukind","age":30,"cars":{"Ford":3,"BMW":6,"Fiat":19}}
    ******************************************************************************************************************************/

$owner = isset( $_GET["owner"] )? $_GET["owner"] : $_POST["owner"];
$task  = isset( $_GET["task"] ) ? $_GET["task"] : $_POST["task"];
$c_id  = isset( $_GET["c_id"] ) ? $_GET["c_id"] : "";

$data = $GLOBALS['dbh']->getAll("select * from public.customer join public.lxc_cars on public.customer.id = $owner and public.lxc_cars.c_id = $c_id");

?>


<script>
$(document).ready(function()
    {
        $( '#headline' ).html( kivi.t8( 'Zulassung / Umschreibung' ) );
    });

function carregClose(owner, c_id, task)
{
    uri1="lxcmain.php?owner=" + owner;
    uri2="&c_id=" + c_id;
    uri3="&task=" + task;
    uri=uri1+uri2+uri3;
    location.href=uri;
}

</script>

<style>

td { font-size: 16px; padding: 0.5em; }
label { font-size: 16px; }
input[type=text] { font-size: 16px; }
input:read-only { background-color: #d0cfc9; }

</style>
</head>

<body>
<?php
    echo $menu['pre_content'];
    echo $menu['start_content'];

    //var_dump($data);

    $c_ln = "";
    $name = "";
    $vorname = "";
    $street = "";
    $house_nr = "";
    $zipcode = "";
    $city = "";
    $c_fin = "";
    $iban = "";
    $bic = "";
    $bank = "";

    if(is_array($data) && count($data) > 0)
    {
        $c_ln = (array_key_exists('c_ln', $data[0]) && $data[0]['c_ln'] != NULL)? $data[0]['c_ln'] : "";
        if(array_key_exists('name', $data[0]) && $data[0]['name'] != NULL)
        {
            $name = $data[0]['name'];
            $name_parts = explode(' ', $name);
            $name_parts = array_reverse($name_parts);
            $name = $name_parts[0];
            for($i = 1; $i < count($name_parts); $i++) $vorname .= $name_parts[$i] .' ';
        }
        if(array_key_exists('street', $data[0]) && $data[0]['street'] != NULL)
        {
            $street = $data[0]['street'];
            $street_parts = explode(' ', $street);
            $street_parts = array_reverse($street_parts);
            $house_nr = $street_parts[0];
            $street = "";
            for($i = 1; $i < count($street_parts); $i++) $street .= $street_parts[$i] .' ';
        }
        $zipcode = (array_key_exists('zipcode', $data[0]) && $data[0]['zipcode'] != NULL)? $data[0]['zipcode'] : "";
        $city = (array_key_exists('city', $data[0]) && $data[0]['city'] != NULL)? $data[0]['city'] : "";
        if(array_key_exists('c_fin', $data[0]) && $data[0]['c_fin'] != NULL)
        {
            $c_fin = $data[0]['c_fin'];
            if(strlen($c_fin) > 17) $c_fin = substr($c_fin, 0, 17);
        }
        $iban = (array_key_exists('iban', $data[0]) && $data[0]['iban'] != NULL)? $data[0]['iban'] : "";
        $bic = (array_key_exists('bic', $data[0]) && $data[0]['bic'] != NULL)? $data[0]['bic'] : "";
        $bank = (array_key_exists('bank', $data[0]) && $data[0]['bank'] != NULL)? $data[0]['bank'] : "";
    }
?>

<p id="headline" class="ui-state-highlight ui-corner-all tools" style="margin-top: 20px; padding: 0.6em;"></p>
<div class="ui-content" style="background-color: #fcfdfd; padding: 0.5em;">
<h2>Auftrag auf Zulassung / Umschreibung</h2>
<form method="post" action="carreg_pdf.php" target="_blank" onsubmit="return validateForm()">
    <div>
        <input type="radio" id="zulassung" name="auftragsart" value="zulassung" onclick="auswahlAuftrag()" checked aria-label="Zulassung" />
            <label for="zulassung">Zulassung</label>
        <input type="radio" id="umschreibung" name="auftragsart" value="umschreibung" onclick="auswahlAuftrag()" aria-label="Umschreibung" />
            <label for="umschreibung">Umschreibung</label>
        <input type="radio" id="abmeldung" name="auftragsart" value="abmeldung" onclick="auswahlAuftrag()" aria-label="Abmeldung" />
            <label for="abmeldung">Abmeldung</label>
        <input type="radio" id="aenderung" name="auftragsart" value="aenderung" onclick="auswahlAuftrag()" aria-label="Änderung" />
            <label for="aenderung">Änderung</label>
        <input type="radio" id="ersatz" name="auftragsart" value="ersatz" onclick="auswahlAuftrag()" aria-label="Ersatz" />
            <label for="ersatz">Ersatz</label>
    </div>

    <table>
    <tr>
        <td><label for"kennzeichen">Kennzeichen:<label></td>
        <td><input  type="text" id="kennzeichen" name="kennzeichen" aria-label="Kennzeichen" placeholder="z.B. SRB-AB123" value="<?=$c_ln?>" /></td>
    </tr>
    <tr>
        <td><label for="vorname">Vorname*:</label></td>
        <td><input class="required" type="text" id="vorname" name="vorname" aria-label="Vorname" value="<?=$vorname?>" /></td>
    </tr>
    <tr>
        <td><label for="name">Name*:</label></td>
        <td><input class="required" type="text" id="name" name="name" aria-label="Name" value="<?=$name?>" /></td>
    </tr>
    <tr>
        <td><label for="gebname">Geburtsname:</label></td>
        <td><input type="text" id="gebname" name="gebname" aria-label="Geb.-Name" value="<?=$vorname?> <?=$name?>"/></td>
    </tr>
    <tr>
        <td><label for="gebdatum">Geburtsdatum*:</label></td>
        <td><input class="required" type="text" id="gebdatum" name="gebdatum" aria-label="Geb.-Datum" /></td>
    </tr>
    <tr>
        <td><label for="gebort">Geburtsort*:</label></td>
        <td><input class="required" type="text" id="gebort" name="gebort" aria-label="Geb.-Ort" /></td>
    </tr>
    <tr>
        <td><label for="strasse">Straße*:</label></td>
        <td><input class="required" type="text" id="strasse" name="strasse" aria-label="Straße" value="<?=$street?>" /></td>
    </tr>
    <tr>
        <td><label for="hsnr">Hausnummer*:</label></td>
        <td><input class="required" type="text" id="hsnr" name="hsnr" aria-label="Hausnummer" value="<?=$house_nr?>" /></td>
    </tr>
    <tr>
        <td><label for="plz">PLZ*:</label></td>
        <td><input class="required" type="text" id="plz" name="plz" aria-label="PLZ" value="<?=$zipcode?>" /></td>
    </tr>
    <tr>
        <td><label for="ort">Ort*:</label></td>
        <td><input class="required" type="text" id="ort" name="ort" aria-label="Ort" value="<?=$city?>" /></td>
    </tr>
    <tr>
        <td><label for="fahrzeug-id">Fahrzeugidentifikationsnummer*:</label></td>
        <td><input class="required" type="text" id="fahrzeug-id" name="fahrzeug-id" onkeyup="toUpperCase('fahrzeug-id')" aria-label="Fahrzeugidentifikationsnummer" value="<?=$c_fin?>" /></td>
    </tr>
    </table>


    <div id="evb-nummer-div">
        <table>
        <tr>
            <td><label for="evb-nummer">eVB-Nummer*:</label></td>
            <td><input class="required" type="text" id="evb-nummer" name="evb-nummer" onkeyup="onkeyupEVB()" aria-label="eVB-Nummer" /></td>
            <td><a onclick="checkEVB()" class="ui-button ui-corner-all ui-widget" style="font-size: 14px" aria-label="eVB-Nummer überprüfen">Überprüfen</a> </td>
            <td><div id="check-evb"></div></td>
        </tr>
        </table>
    </div>

    <div>
        <input type="radio" id="weiblich" name="geschlecht" value="weiblich" onclick="hideShowGewerbeanschrift()" aria-label="weiblich" />
            <label for="weiblich">weiblich</label>
        <input type="radio" id="maennlich" name="geschlecht" value="maennlich" onclick="hideShowGewerbeanschrift()" aria-label="männlich" />
            <label for="maennlich">männlich</label>
        <input type="radio" id="divers" name="geschlecht" value="divers" onclick="hideShowGewerbeanschrift()" aria-label="divers" />
            <label for="divers">divers</label>
        <input type="radio" id="firma-radio" name="geschlecht" value="firma" onclick="hideShowGewerbeanschrift()" aria-label="Firma" />
            <label for="firma-radio">Firma</label>
    </div>

    <div id="gewerbeanschrift" style="display:none">
        <table>
        <tr>
            <td><label for="beruf">Beruf/Gewerbe (nur bei Firma)*:</label></td>
            <td><input class="gewerbe" type="text" id="beruf" name="beruf" aria-label="Beruf/Gewerbe" /></td>
        </tr>
        <tr>
            <td><label for="firma">Firma*:</label></td>
            <td><input class="gewerbe" type="text" size="50" maxlength="70" id="firma" name="firma" aria-label="Firma" /></td>
        </tr>
        <tr>
            <td><label for="gewerbe-strasse">Straße*:</label></td>
            <td><input class="gewerbe" type="text" id="gewerbe-strasse" name="gewerbe-strasse" aria-label="Straße (Gewerbanschrift)" value="<?=$street?>"/></td>
        </tr>
        <tr>
            <td><label for="gewerbe-hsnr">Hausnummer*:</label></td>
            <td><input class="gewerbe" type="text" id="gewerbe-hsnr" name="gewerbe-hsnr" aria-label="Hausnummer (Gewerbanschrift)" value="<?=$house_nr?>"/></td>
        </tr>
        <tr>
            <td><label for="gewerbe-plz">PLZ*:</label></td>
            <td><input class="gewerbe" type="text" id="gewerbe-plz" name="gewerbe-plz" aria-label="PLZ (Gewerbanschrift)" value="<?=$zipcode?>"/></td>
        </tr>
        <tr>
            <td><label for="gewerbe-ort">Ort*:</label></td>
            <td><input class="gewerbe" type="text" id="gewerbe-ort" name="gewerbe-ort" aria-label="Ort (Gewerbanschrift)" value="<?=$city?>"/></td>
        </tr>
        </table>
    </div>

    <div id="eidstatt-div" style="display:none">
        <h2>Versicherung an Eides Statt</h2>
        <div style="font-size: 16px;">Welches Dokument ist Ihnen abhandengekommen?</div>
        <table>
        <tr>
            <td><input class="eidstatt-chbx" type="checkbox" id="fahrzeugschein" name="fahrzeugschein" value="true" aria-label="Zulassungsbescheinigung Teil I / Fahrzeugschein" /></td>
            <td><label for="fahrzeugschein">Zulassungsbescheinigung Teil I / Fahrzeugschein</label></td>
        </tr>
        <tr>
            <td><input class="eidstatt-chbx" type="checkbox" id="fahrzeugbrief" name="fahrzeugbrief" value="true" aria-label="Zulassungsbescheinigung Teil II / Fahrzeugbrief" /></td>
            <td><label for="fahrzeugbrief">Zulassungsbescheinigung Teil II / Fahrzeugbrief</label></td>
        </tr>
        <tr>
            <td><input class="eidstatt-chbx" type="checkbox" id="amtlicheskenn" name="amtlicheskenn" value="true" aria-label="Amtliches, abgestempeltes Kennzeichen" /></td>
            <td><label for="amtlicheskenn">Amtliches, abgestempeltes Kennzeichen</label></td>
        </tr>
        <tr>
            <td><input class="eidstatt-chbx" type="checkbox" id="roterschein" name="roterschein" value="true" aria-label="Das rote Fahrzeugscheinheft" /></td>
            <td><label for="roterschein">Das rote Fahrzeugscheinheft</label></td>
        </tr>
        <tr>
            <td><input class="eidstatt-chbx" type="checkbox" id="fuehrerschein" name="fuehrerschein" value="true" aria-label="Führerschein" /></td>
            <td><label for="fuehrerschein">Führerschein</label></td>
        </tr>
        <tr>
            <td><input class="eidstatt-chbx" type="checkbox" id="betriebserlaubnis" name="betriebserlaubnis" value="true" aria-label="Betriebserlaubnis" /></td>
            <td><label for="betriebserlaubnis">Betriebserlaubnis</label></td>
        </tr>
        <tr>
            <td><input class="eidstatt-chbx" type="checkbox" id="sonstiges" name="sonstiges" value="true" aria-label="Sonstiges" /></td>
            <td><label for="sonstiges">Sonstiges:</label> <input type="text" id="sonstiges-text" name="sonstiges-text" aria-label="Sonstiges" ></td>
        </tr>
        <tr>
            <td><label for="erklaerung">Erklärung:</label></td>
            <td>
                <textarea id="erklaerung" name="erklaerung" rows="9" cols="60" maxlength="200" aria-label="Erklärung zur Versicherung an Eides Statt" ></textarea>
            </td>
        </tr>
        <tr>
            <td></td>
            <td><a href="#" onclick="textVorlage()" class="ui-button ui-corner-all ui-widget" style="font-size: 12px;">Textvorlage</a></td>
        </tr>
        </table>
        </div>

     <div id="sepa-mandat-div">
        <h2>SEPA-Lastschriftmandat zum Einzug der Kraftfahrzeugsteuer</h2>
        <table>
        <tr>
            <td><label for="mandat-identisch">Fahrzeughalter und Kontoinhaber sind identisch:</label></td>
            <td><input  type="checkbox" id="mandat-identisch" name="mandat-identisch" onclick="hideShowSepaMandant()" value="true" aria-label="Fahrzeughalter und Kontoinhaber sind identisch" checked/></td>
        </tr>
        </table>
        <div id="sepa-mandat" style="display:none">
            <table>
            <tr>
                <td><label for="mandats-name">Vorname und Nachname oder Firma*:</label></td>
                <td><input class="sepa-mandat" type="text" id="mandats-name" name="mandats-name" aria-label="Vorname und Nachname oder Firma (SEPA-Lastschriftmandat)" value="<?=$vorname?> <?=$name?>"/></td>
            </tr>
            <tr>
                <td><label for="mandats-strasse">Straße und Hausnummer*:</label></td>
                <td><input class="sepa-mandat" type="text" id="mandats-strasse" name="mandats-strasse" aria-label="Straße und Hausnummer (SEPA-Lastschriftmandat)" value="<?=$street?> <?=$house_nr?>"/></td>
            </tr>
            <tr>
                <td><label for="mandats-plz">PLZ*:</label></td>
                <td><input class="sepa-mandat" type="text" id="mandats-plz" name="mandats-plz" aria-label="PLZ (SEPA-Lastschriftmandat)" value="<?=$zipcode?>"/></td>
            </tr>
            <tr>
                <td><label for="mandats-ort">Ort*:</label></td>
                <td><input class="sepa-mandat" type="text" id="mandats-ort" name="mandats-ort" aria-label="Ort (SEPA-Lastschriftmandat)" value="<?=$city?>"/></td>
            </tr>
            </table>
        </div>
        <table>
        <tr>
            <td><label for="mandats-land">Land*:</label></td>
            <td><input class="sepa-required" type="text" id="mandats-land" name="mandats-land" aria-label="Land (SEPA-Lastschriftmandat)" value="Deutschland" /></td>
        </tr>
        <tr>
            <td><label for="mandats-iban">IBAN*:</label></td>
            <td><input class="sepa-required" type="text" size="32" id="mandats-iban" name="mandats-iban" aria-label="IBAN" value="<?=$iban?>"/></td>
        </tr>
        <tr>
            <td><label for="mandats-bic">BIC*:</label></td>
            <td><input class="sepa-required" type="text" id="mandats-bic" name="mandats-bic" aria-label="BIC" value="<?=$bic?>"/></td>
        </tr>
        <tr>
            <td><label for="mandats-bank">Name der Bank*:</label></td>
            <td><input class="sepa-required" type="text" id="mandats-bank" name="mandats-bank" aria-label="Name der Bank" value="<?=$bank?>"/></td>
        </tr>
        </table>
    </div>

    <p><input type="checkbox" id="print-template" name="print-template" value="true" aria-label="Als Vorlage drucken" /><label for="print-template"> Als Vorlage drucken</label></p>

    <div id="response" style="font-weight: bold; font-size: 16px; color: red; padding: 0.5em;" ></div>
    <button type="submit" class="ui-button ui-corner-all ui-widget">PDF herunterladen</button>
    <a href="../firmen3.php?Q=C&id=<?=$owner?>&edit=1" target="_blank" class="ui-button ui-corner-all ui-widget">Kunde bearbeiten</a>
    <button type="button" name="close" onClick="carregClose(<?=$owner?>, <?=$c_id?>, <?=$task?>);" class="ui-button ui-corner-all ui-widget">Schließen</button>&nbsp;&nbsp;&nbsp;
</form>
</div>

<?php echo $menu['end_content']; ?>

</html>
