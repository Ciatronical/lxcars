<?php
if(isset($_POST['evbsearch']) && !empty($_POST['evbsearch']))
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"https://www.evb-nummer.com/evb-check/search.php");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "evbsearch=".$_POST['evbsearch']);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$server_output = curl_exec($ch);
	curl_close($ch);
	
	if(strpos($server_output, 'notfound') == false)
	{
		echo 'ok';
		return;
	}
}

echo 'notfound';

