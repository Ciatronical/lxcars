<?php
include("inc/lxcLib.php");

if ( isset($_GET['kz']) && isset($_GET['id']) && $_GET['kz']  ) {  
  echo UniqueKz($_GET['kz'], $_GET['id']);
}

if ( isset($_GET['fin']) && isset($_GET['id']) && $_GET['fin']  ) {  
  echo UniqueFin($_GET['fin'], $_GET['id']);
}

if ( isset($_GET['fin_zu2']) && isset($_GET['fin_zu3']) && $_GET['fin_zu2'] && $_GET['fin_zu3'] ) {  
  echo SucheFin($_GET['fin_zu2'], $_GET['fin_zu3']);
}

if ( isset($_GET['mkb_zu2']) && isset($_GET['mkb_zu3']) && $_GET['mkb_zu2'] && $_GET['mkb_zu3'] ) {  
  echo SucheMkb($_GET['mkb_zu2'], $_GET['mkb_zu3']);
}
?>