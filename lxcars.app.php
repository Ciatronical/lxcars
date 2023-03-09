<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8' />

<link href="css/bootstrap/bootstrap.min.css" rel="stylesheet">
<?php
    require_once '../inc/stdLib.php';
    $menu = $_SESSION['menu'];
    echo $menu['stylesheets'];
    echo $menu['javascripts'];
    echo $head['IBAN'];
    echo $head['JQTABLE'];
    echo $head['THEME'];
    echo $head['T8'];// Übersetzung mit kivi.t8
?>

<style>
	.bs {
		z-index: 1;
	}
	.form-control {
		font-size: 14px;
	}
	.nav-item {
		font-size: 14px;
	}
	.ui-autocomplete-category {
		font-weight: bold;
	}
	.lxc-fs-normal {
		font-size: 14px;
	}
</style>

</head>

<body>
<?php
    echo $menu['pre_content'];
    echo $menu['start_content'];
?>

<main>

<nav class="navbar navbar-expand-lg pt-2"  style="background-color: #d0cfc9;" aria-label="Eighth navbar example">
<div class="container-fluid">
	<span class="navbar-brand">Lxcars</span>
	<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample07" aria-controls="navbarsExample07" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>
	<div class="collapse navbar-collapse" id="navbarsExample07">
		<ul class="navbar-nav me-auto mb-2 mb-lg-0">
			<li class="nav-item">
				<a class="nav-link active" aria-current="page" href="#">Firmenstammdaten</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="#">Ansprechpartner</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="#">Umsätze</a>
			</li>
   			<li class="nav-item">
				<a class="nav-link" href="#">Dokumente</a>
			</li>
	     </ul>
		<form role="search">
			<input class="form-control" id="lxc-id-fast-search" type="search" placeholder="Schnellsuche" aria-label="Search">
		</form>
	</div>
</div>
</nav>

<div class="container-fluid">

<div id="lxc-id-title" class="pe-2 pt-3 lxc-fs-normal">
	<strong>Detailansicht <span id="lxc-id-title-typ">Kunde</span></strong>
</div>
<hr />

<div id="lxc-id-base-data" class="row lxc-fs-normal">
	<div id="lxc-id-hq-view" class="col-lg-2 pt-4">
		<div><strong><span id="lxc-id-name">Maria Mustermann</span></strong></div>
		<div class="pt-1"><span id="lxc-id-street">Bahnhofsstrasse 23</span></div>
		<div><span id="lxc-id-place">D-15345 Rehfelde</span></div>
		<div class="pt-4"><strong>Kontakt</strong></div>
		<div class="pt-2"><span id="lxc-id-contact-person">Maria Mustermann</span></div>
		<div class="pt-2">
			Telefon: <button id="lxc-id-tel1">+49175-1234567</botton>
			<button id="lxc-id-tel1-t">T</botton>
			<button id="lxc-id-tel1-c">C</botton>
			<button id="lxc-id-tel1-w">W</botton>
		</div>
		<div class="pt-2">
			Telefon: <button id="lxc-id-tel2">033433-123456</button>
			<button id="lxc-id-tel2-t">T</botton>
			<button id="lxc-id-tel2-c">C</botton>
			<button id="lxc-id-tel2-w">W</botton>
		</div>
		<div class="pt-2">E-Mail: <button>example@googlemail.com</button></div>
	</div>
	<div id="lxc-id-contact-view" class="col-lg-6 pt-4">
		<ul class="nav nav-tabs">
			<li class="nav-item">
				<a class="nav-link" aria-current="page" href="#">Kontakte</a>
			</li>
			<li class="nav-item">
				<a class="nav-link active" aria-current="page" href="#">Angebote</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" aria-current="page" href="#">Aufträge</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" aria-current="page" href="#">Lieferscheine</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" aria-current="page" href="#">Rechnungen</a>
			</li>
		</ul>
		<table class="table table-striped">
			<thead>
			<tr>
				<th scope="col">Datum</th>
				<th scope="col">Erste Position</th>
				<th scope="col">Betrag</th>
				<th scope="col">Nummer</th>
			</tr>
			</thead>
			<tbody>
			<tr>
				<th scope="row">11.11.2011</th>
				<td>Langer Text wie zum Beispiel: Fehlerdiagnose, Gasanlag instandsetzen</td>
				<td>5000,00 EUR</td>
				<td>11111</td>
			</tr>
			<tr>
				<th scope="row">11.11.2011</th>
				<td>Langer Text wie zum Beispiel: Fehlerdiagnose, Gasanlag instandsetzen</td>
				<td>5000,00 EUR</td>
				<td>11111</td>
			</tr>
			<tr>
				<th scope="row">11.11.2011</th>
				<td>Langer Text wie zum Beispiel: Fehlerdiagnose, Gasanlag instandsetzen</td>
				<td>5000,00 EUR</td>
				<td>11111</td>
			</tr>
			<tr>
				<th scope="row">11.11.2011</th>
				<td>Langer Text wie zum Beispiel: Fehlerdiagnose, Gasanlag instandsetzen</td>
				<td>5000,00 EUR</td>
				<td>11111</td>
			</tr>
			<tr>
				<th scope="row">11.11.2011</th>
				<td>Langer Text wie zum Beispiel: Fehlerdiagnose, Gasanlag instandsetzen</td>
				<td>5000,00 EUR</td>
				<td>11111</td>
			</tr>
			<tr>
				<th scope="row">11.11.2011</th>
				<td>Langer Text wie zum Beispiel: Fehlerdiagnose, Gasanlag instandsetzen</td>
				<td>5000,00 EUR</td>
				<td>11111</td>
			</tr>
			<tr>
				<th scope="row">11.11.2011</th>
				<td>Langer Text wie zum Beispiel: Fehlerdiagnose, Gasanlag instandsetzen</td>
				<td>5000,00 EUR</td>
				<td>11111</td>
			</tr>
			<tr>
				<th scope="row">11.11.2011</th>
				<td>Langer Text wie zum Beispiel: Fehlerdiagnose, Gasanlag instandsetzen</td>
				<td>5000,00 EUR</td>
				<td>11111</td>
			</tr>
			<tr>
				<th scope="row">11.11.2011</th>
				<td>Langer Text wie zum Beispiel: Fehlerdiagnose, Gasanlag instandsetzen</td>
				<td>5000,00 EUR</td>
				<td>11111</td>
			</tr>
			<tr>
				<th scope="row">11.11.2011</th>
				<td>Langer Text wie zum Beispiel: Fehlerdiagnose, Gasanlag instandsetzen</td>
				<td>5000,00 EUR</td>
				<td>11111</td>
			</tr>
			<tr>
				<th scope="row">11.11.2011</th>
				<td>Langer Text wie zum Beispiel: Fehlerdiagnose, Gasanlag instandsetzen</td>
				<td>5000,00 EUR</td>
				<td>11111</td>
			</tr>
			<tr>
				<th scope="row">11.11.2011</th>
				<td>Langer Text wie zum Beispiel: Fehlerdiagnose, Gasanlag instandsetzen</td>
				<td>5000,00 EUR</td>
				<td>11111</td>
			</tr>
			<tr>
				<th scope="row">11.11.2011</th>
				<td>Langer Text wie zum Beispiel: Fehlerdiagnose, Gasanlag instandsetzen</td>
				<td>5000,00 EUR</td>
				<td>11111</td>
			</tr>
			<tr>
				<th scope="row">11.11.2011</th>
				<td>Langer Text wie zum Beispiel: Fehlerdiagnose, Gasanlag instandsetzen</td>
				<td>5000,00 EUR</td>
				<td>11111</td>
			</tr>
			<tr>
				<th scope="row">11.11.2011</th>
				<td>Langer Text wie zum Beispiel: Fehlerdiagnose, Gasanlag instandsetzen</td>
				<td>5000,00 EUR</td>
				<td>11111</td>
			</tr>
			</tbody>
		</table>
		<div id="lxc-id-subview" class="pt-4">
			<strong>Subview</strong>
		</div>
	</div>
	<div id="lxc-id-contact-view" class="col-lg-4 ps-5 pt-4">
		<ul class="nav nav-tabs">
			<li class="nav-item">
				<a class="nav-link" aria-current="page" href="#">Lieferanschrift</a>
			</li>
			<li class="nav-item">
				<a class="nav-link active" aria-current="page" href="#">Notizen</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" aria-current="page" href="#">Variablen</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" aria-current="page" href="#">Finanzinfos</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" aria-current="page" href="#">zusätzliche Infos</a>
			</li>
		</ul>
		<div class="pt-2">
			<textarea rows="10" cols="80">
				Hier stehen dann die vielen wichtigen Notizen.
				Zeile 2
			</textarea>
		</div>
	</div>
</div>

</div>
</main>

<?php echo $menu['end_content']; ?>
<script src="js/bootstrap/bootstrap.bundle.min.js"></script>
<script src="js/lxcars.app.js"></script>
</body>
</html>
