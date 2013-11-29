<?php
include("inc/lxcLib.php");
if ( isset($_GET['kz']) && isset($_GET['id']) && $_GET['kz']  ) {  
  echo UniqueKz($_GET['kz'], $_GET['id']);
}
?>