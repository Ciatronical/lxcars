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
</style>

</head>

<body>
<?php
    echo $menu['pre_content'];
    echo $menu['start_content'];
?>

<main>

<p>
	<?php
		$term = "MOL";
		//var_dump($GLOBALS['dbh']->getAll("SELECT 'C' AS type, '', id, name FROM customer WHERE name ILIKE '%".$term."%';"));
		//var_dump($GLOBALS['dbh']->getAll("SELECT 'V' AS type, id, name FROM vendor WHERE name ILIKE '%".$term."%';"));
		//var_dump($GLOBALS['dbh']->getAll("SELECT c_ln, c_id, name FROM lxc_cars JOIN customer ON c_ow = id WHERE c_ln ILIKE '%".$term."%' AND obsolete = false;"));
	?>
</p>

<nav class="navbar navbar-expand-lg"  style="background-color: #d0cfc9;" aria-label="Eighth navbar example">
    <div class="container-fluid">
      <span class="navbar-brand">Lxcars</span>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample07" aria-controls="navbarsExample07" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarsExample07">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="#">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Link</a>
          </li>
          <li class="nav-item">
            <a class="nav-link disabled">Disabled</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">Dropdown</a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="#">Action</a></li>
              <li><a class="dropdown-item" href="#">Another action</a></li>
              <li><a class="dropdown-item" href="#">Something else here</a></li>
            </ul>
          </li>
        </ul>
        <form role="search">
          <input class="form-control" type="search" placeholder="Schnellsuche" aria-label="Search">
        </form>
      </div>
    </div>
  </nav>
</main>

<?php echo $menu['end_content']; ?>
<script src="js/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>
