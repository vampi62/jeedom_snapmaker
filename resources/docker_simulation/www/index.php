<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
date_default_timezone_set('Europe/Paris');
setlocale(LC_TIME, "fr_FR");

define("URL", str_replace("index.php","",(isset($_SERVER['HTTPS'])? "https" : "http").
"://".$_SERVER['HTTP_HOST'].$_SERVER["PHP_SELF"]));

$url = explode("/", filter_var($_GET['demande'],FILTER_SANITIZE_URL));

require ('controleur/config.php');
require ('controleur/connection_base.php');
require ('controleur/action.php');
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');
if (isset($datareturn)){
	echo json_encode($datareturn);
}
?>