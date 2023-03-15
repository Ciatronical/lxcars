<?php
	session_start();
    $baseUrl = isset( $_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http';
    $baseUrl.= '://'.$_SERVER['SERVER_NAME'].preg_replace( "^crm/.*^", "", $_SERVER['REQUEST_URI'] );
    $url = $baseUrl.'/controller.pl?action=Layout/empty&format=json';
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, CURLOPT_TIMEOUT, 1 );
    curl_setopt( $ch, CURLOPT_ENCODING, 'gzip,deflate' );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array (
                "Connection: keep-alive",
                "Cookie: ".$_SESSION["cookie"]."=".$_SESSION['sessid']."; ".$_SESSION["cookie"]."_api_token=".$_SESSION["token"]['api_token']
                ));
    $result = curl_exec( $ch );

    if( $result === false || curl_errno( $ch )){
        die( 'Curl-Error: ' .curl_error($ch).' </br> $ERP_BASE_URL in "inc/conf.php" richtig gesetzt??' );
    }
    curl_close( $ch );

    $objResult = json_decode( $result );
    $vars = get_object_vars( $objResult );

	//var_dump($vars);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8' />

<?php
	foreach($objResult->{'stylesheets'} as $style) echo '<link rel="stylesheet" href="'.$baseUrl.$style.'" type="text/css">'."\n";
?>

</head>

<body>

<?php
	$suche = '^([/a-zA-Z_0-9]+)\.(pl|php|phtml)^';
	$ersetze = $baseUrl.'${1}.${2}';
	$tmp = preg_replace($suche, $ersetze, $objResult->{'pre_content'} );
	$tmp = str_replace( 'itemIcon="', 'itemIcon="'.$baseUrl, $tmp );
	echo str_replace( 'src="', 'src="'.$baseUrl, $tmp );
	echo $objResult->{'start_content'};
?>
<ul width="11"></ul></li></ul></li></ul><div class="layout-actionbar"><div class="layout-actionbar-combobox" id="action2753260"><div class="layout-actionbar-combobox-head"><div class="layout-actionbar-action layout-actionbar-submit" id="action2753258">Speichern</div><span></span></div><div class="layout-actionbar-combobox-list"><div class="layout-actionbar-action layout-actionbar-submit" id="action2753259">Speichern und schließen</div></div></div><div id="action2753266" class="layout-actionbar-combobox"><div class="layout-actionbar-combobox-head"><div class="layout-actionbar-action layout-actionbar-submit" id="action2753261">Workflow</div><span></span></div><div class="layout-actionbar-combobox-list"><div class="layout-actionbar-action layout-actionbar-submit" id="action2753262">Speichern und Debitorenbuchung erfassen</div><div id="action2753263" class="layout-actionbar-action layout-actionbar-submit">Speichern und Rechnung erfassen</div><div id="action2753264" class="layout-actionbar-action layout-actionbar-submit">Speichern und Auftrag erfassen</div><div id="action2753265" class="layout-actionbar-action layout-actionbar-submit">Speichern und Angebot</div></div></div><div class="layout-actionbar-action layout-actionbar-submit" id="action2753267">Löschen</div><div class="layout-actionbar-separator"></div><div id="action2753268" class="layout-actionbar-action layout-actionbar-submit">Historie</div></div><div id='content'>
<h1>Kunde erfassen </h1>
<div class="layout-actionbar">
	<div class="layout-actionbar-combobox" id="action2753260"><div class="layout-actionbar-combobox-head">
		<div class="layout-actionbar-action layout-actionbar-submit" id="action2753258">Speichern</div>
		<span></span>
	</div>
	<div class="layout-actionbar-combobox-list">
		<div class="layout-actionbar-action layout-actionbar-submit" id="action2753259">Speichern und schließen</div>
	</div>
	</div>
</div>
<main id="#lxcars-app">

</main>

<?php
	foreach($objResult->{'javascripts'} as $js) echo '<script type="text/javascript" src="'.$baseUrl.$js.'"></script>'."\n";
?>
<script src="js/lxcars.app.js"></script>
<script>
<?php
	foreach($objResult->{'javascripts_inline'} as $js) echo $js."\n";
?>
</script>

</body>
</html>
