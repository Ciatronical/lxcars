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
//		var_dump($GLOBALS['dbh']->getAll("SELECT 'C' AS type, '' AS c_ln, id, name FROM customer WHERE name ILIKE '%".$term."%' LIMIT 5;"));
//		var_dump($GLOBALS['dbh']->getAll("SELECT 'V' AS type, '' AS c_ln, id, name FROM vendor WHERE name ILIKE '%".$term."%' LIMIT 5;"));
//		var_dump($GLOBALS['dbh']->getAll("SELECT c_ln, c_id, name FROM lxc_cars JOIN customer ON c_ow = id WHERE c_ln ILIKE '%".$term."%' AND obsolete = false LIMIT 5;"));

		var_dump($GLOBALS['dbh']->getAll("(SELECT 'C' AS type, '' AS c_ln, id, name FROM customer WHERE name ILIKE '%".$term."%' LIMIT 5) UNION ALL (SELECT 'V' AS type, '' AS c_ln, id, name FROM vendor WHERE name ILIKE '%".$term."%' LIMIT 5) UNION ALL (SELECT 'P' AS type, '' AS c_ln, id, name FROM contacts WHERE name ILIKE '%".$term."%' LIMIT 5) UNION ALL (SELECT 'L' AS type, c_ln, c_id, name FROM lxc_cars JOIN customer ON c_ow = id WHERE c_ln ILIKE '%".$term."%' AND obsolete = false LIMIT 5)", true));
	?>
</p>

</main>

<?php echo $menu['end_content']; ?>
<script src="js/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>
