<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
date_default_timezone_set('Europe/Paris');
setlocale(LC_TIME, "fr_FR");

require ('controleur/config.php');
require ('controleur/connection_base.php');
require ('controleur/action.php');
if (isset($datastation)){
	echo json_encode($datastation);
}
?>