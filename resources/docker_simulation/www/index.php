<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
date_default_timezone_set('Europe/Paris');
setlocale(LC_TIME, "fr_FR");

require ('controleur/config.php');
require ('controleur/connection_base.php');
if (!$_Serveur_['Install']) header('Location: installation/');
if (isset($_GET['action']))
{
	require ('controleur/action.php');
}
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');
if (isset($datareturn)){
	echo json_encode($datareturn);
}
?>