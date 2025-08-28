<?php
require_once __DIR__.'/../../inc/stdLib.php'; // for debug
require_once __DIR__.'/../../inc/crmLib.php';
require_once __DIR__.'/../inc/ajax2function.php';


/*apt-get install cm-super texlive-fonts-recommended texlive-fonts-extra*/

function getData( $data ){
  $sql = "SELECT  c_id, c_hu, c_ln, name, street, zipcode, city FROM lxc_cars JOIN customer ON( lxc_cars.c_ow = customer.id ) WHERE EXTRACT( YEAR FROM c_hu ) = ".$data['year']." AND EXTRACT( MONTH FROM c_hu ) = ".$data['month']." + 1 AND zipcode != '00000'";
  echo $GLOBALS['dbh']->getALL( $sql, true );
}

function updateNotSelectedCars(){
  echo '1';
  //return 1;
}

function generatePdf( $data ){
  //$qrcode = 'https://www.google.com/search?q=autoprofis+tüv+109€'; //eventuell in die DB speichern oder in config.php
  $qrcode = 'https://www.google.com/search?q=autoprofis+t%C3%BCv+109%E2%82%AC';
  $google_url = 'https://g.page/r/CZIH73280l-1EAE/review';
  $date   = array_pop( $data );
  $button = array_pop( $data );
  $fileName = $date.'.tex';

  $pdfFileName = $date.'.pdf';
  //welches Template wird verwendet
  $sql = 'SELECT templates FROM defaults';
  $template = $GLOBALS['dbh']->getOne( $sql );

  $outputPath = __DIR__.'/../../../kivitendo-erp/'.$template['templates'].'/';
  $templateSourceTexFile = $outputPath.'hu-serienbrief.tex';

  // Vorlage einlesen
  $template = file_get_contents($templateSourceTexFile);

  //Kundendaten, Mitarbeiterdaten und Anrede holen. Als Ansprechpartner wird der Mitarbeiter genommen, der den letzten Auftrag für den Kunden gemacht hat.
  //Wenn kein Auftrag gefunden wird, wird 'Ronny Zimmermann' genommen.
  $sql = "
    SELECT
        c.c_id,
        c.c_hu,
        c.c_ln,
        cu.greeting,
        cu.name,
        cu.country,
        cu.street,
        cu.zipcode,
        cu.city,
        cu.customernumber,
        cu.email,
        COALESCE(oe_employee.name, e.name, 'Ronny Zimmermann') AS employee_name, -- Fallback zu 'Ronny Zimmermann' wenn kein Mitarbeiter gefunden wird
        CASE
            WHEN cu.greeting = 'Herr' THEN gt_male.translation
            WHEN cu.greeting = 'Frau' THEN gt_female.translation
            ELSE gt_general.translation
        END AS salutation
    FROM
        lxc_cars c
    JOIN
        customer cu ON c.c_ow = cu.id
    LEFT JOIN
        generic_translations gt_male ON gt_male.translation_type = 'salutation_male'
    LEFT JOIN
        generic_translations gt_female ON gt_female.translation_type = 'salutation_female'
    LEFT JOIN
        generic_translations gt_general ON gt_general.translation_type = 'salutation_general'
    LEFT JOIN
        (
            SELECT
                o.customer_id,
                o.employee_id,
                e.name,
                ROW_NUMBER() OVER (PARTITION BY o.customer_id ORDER BY o.itime DESC, o.id DESC) AS rn
            FROM
                oe o
            LEFT JOIN
                employee e ON o.employee_id = e.id
        ) oe_employee
        ON oe_employee.customer_id = cu.id AND oe_employee.rn = 1 -- Neuester Auftrag pro Kunde
    LEFT JOIN
        employee e ON cu.employee = e.id -- Fallback Verknüpfung mit employee, wenn kein Eintrag in der oe-Tabelle vorhanden ist
    WHERE
          c.c_id IN (" . implode(',', $data) . ")
  ";


  $result = $GLOBALS['dbh']->getALL( $sql );

  //writeLogR( $result );
  //writeLogR( $sql );
  $latexContent = '';

  $latexContent .= "\\newcommand{\\lxlangcode} {DE}\n";
  $latexContent .= "\\newcommand{\\lxmedia} {print}\n";
  $latexContent .= "\\newcommand{\\lxcurrency} {EUR}\n";
  $latexContent .= "\\newcommand{\\kivicompany} {employee_company}\n";

  // LaTeX-Datei erstellen
  $latexContent .= "\\input{inheaders.tex}\n"; // ToDo!!!
  $latexContent .= "\\input{insettings.tex}\n";

  $latexContent .= "\\usepackage{graphicx}\n";
  $latexContent .= "\\usepackage{longtable}\n";
  $latexContent .= "\\usepackage{xcolor}\n";
  $latexContent .= "\\usepackage{qrcode}\n";
  $latexContent .= "\\usepackage{url}\n";
  $latexContent .= "\\usepackage{hyperref}\n";
  $latexContent .= "\\usepackage[utf8]{inputenc}\n";
  $latexContent .= "\\begin{document}\n";
  $latexContent .= "\\ourfont\n";

  foreach($result as $row) {
    foreach($row as $key => $value) {
      $row[$key] = str_replace('&', '\\&', $value);
    }
    $filledTemplate = $template;
    $filledTemplate = str_replace('<%name%>', $row['name'], $filledTemplate);
    $filledTemplate = str_replace('<%street%>', $row['street'], $filledTemplate);
    $filledTemplate = str_replace('<%zipcode%>', $row['zipcode'], $filledTemplate);
    $filledTemplate = str_replace('<%city%>', $row['city'], $filledTemplate);
    $filledTemplate = str_replace('<%greeting%>', $row['greeting'], $filledTemplate);
    $filledTemplate = str_replace('<%date%>', $row['c_hu'], $filledTemplate);
    $filledTemplate = str_replace('<%car%>', $row['c_ln'], $filledTemplate);
    $filledTemplate = str_replace('<%template_meta.language.template_code%>', 'de', $filledTemplate);
    $filledTemplate = str_replace('<%media%>', 'print', $filledTemplate);
    $filledTemplate = str_replace('<%currency%>', 'EUR', $filledTemplate);
    $filledTemplate = str_replace('<%employee_company%>', 'employee_company', $filledTemplate);  //ToDo!!! für was ist das???
    $filledTemplate = str_replace('<%country%>', $row['country'], $filledTemplate);
    $filledTemplate = str_replace('<%customernumber%>', $row['customernumber'], $filledTemplate);
    $filledTemplate = str_replace('<%employee_name%>', $row['employee_name'], $filledTemplate);
    $filledTemplate = str_replace('<%salutation%>', $row['salutation'], $filledTemplate);
    $filledTemplate = str_replace('<%qrcode%>', $qrcode, $filledTemplate);
    $filledTemplate = str_replace('<%google_url%>', $google_url, $filledTemplate);

    $latexContent .= $filledTemplate . "\n\n";
  }
  //writeLogR( $qrcode_text  );
  $latexContent .= "\\end{document}";

  $outputFileName = $outputPath . $fileName; // Definiere den vollen Pfad zur Datei
  file_put_contents($outputFileName, $latexContent);

  // PDF aus LaTeX-Datei erstellen
  $command = "cd " . escapeshellarg($outputPath) . " && pdflatex -output-directory=" . escapeshellarg(__DIR__.'/../seriesLetter') . " " . $outputFileName; //$outputPath
  writeLogR( $command );
  shell_exec($command);

  // Optional: LaTeX-Datei löschen
  //unlink($outputFileName);

  // Rest des Codes
  if( $button == 'sendPIN' ){
    $ftpDefaults = getDefaultsByArray( array( 'eletter_hostname', 'eletter_username', 'eletter_folder', 'eletter_passwd') );
    require_once( __DIR__.'/../../inc/sftpClient.php' );

    try{
      $sftp = new SFTPConnection( $ftpDefaults['eletter_hostname'], 22 );
      $sftp->login( $ftpDefaults['eletter_username'], $ftpDefaults['eletter_passwd'] );
      $srcFile = __DIR__.'/../seriesLetter/'.$pdfFileName;
      $dstFile = '/'.$ftpDefaults['eletter_folder'].'/'.$pdfFileName;
      $sftp->uploadFile( $srcFile,  $dstFile );
    }
    catch( Exception $e ){
      // Fehlerbehandlung
    }
  }
  /*******************************************************************************************************************************
  * 1. PDF-Datei erstellen
  * 2. PDF-Datei auf den Server laden
  * 3. Twillo-Code ausführen
  *********************************************************************************************************************************/
  /*
  require_once __DIR__ . '/twilio-sdk/src/Twilio/Rest/Client.php';
  require_once __DIR__ . '/twilio-sdk/src/Twilio/Http/Client.php';
  require_once __DIR__ . '/twilio-sdk/src/Twilio/Http/CurlClient.php';
  require_once __DIR__ . '/twilio-sdk/src/Twilio/Http/Request.php';
  require_once __DIR__ . '/twilio-sdk/src/Twilio/Http/Response.php';
  require_once __DIR__ . '/twilio-sdk/src/Twilio/Exceptions/TwilioException.php';
  require_once __DIR__ . '/twilio-sdk/src/Twilio/Values.php';
  use Twilio\Rest\Client;

  // Twilio-Anmeldeinformationen
  $account_sid = 'XXXXXXXXXXXXXXXXXXXXXXX';
  $auth_token = 'your_auth_token_here';

  try {
      // Twilio-Client initialisieren
      $client = new Client($account_sid, $auth_token);

      // Nachricht mit Bildanhang senden
      $message = $client->messages->create(
          'whatsapp:+491234567890', // Zielnummer
          [
              'from' => 'whatsapp:+14155238886', // Twilio-Nummer
              'body' => 'Hier ist dein Bild 😎',
              'mediaUrl' => ['https://via.placeholder.com/150'] //URL zum Bild auf dem Autoprofis-Server          ]
      );

      echo "Nachricht gesendet! SID: " . $message->sid;
  } catch (Exception $e) {
      echo "Fehler: " . $e->getMessage();
  }
  */

  //Ok wir erstellen jetzt die einzelnen PDF-Dateien und laden sie auf den Server




  echo '1';
}

?>