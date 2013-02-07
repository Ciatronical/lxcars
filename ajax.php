<?php
include("inc/lxcLib.php");
if ( isset($_GET['kz']) && isset($_GET['id']) && $_GET['kz'] && $_GET['id'] ) {  
    echo UniqueKz($_GET['kz'], $_GET['id']);
}
?>