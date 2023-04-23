<?php
require_once('class/simu.class.php');
if (!isset($_GET['token']) || empty($_GET['token'])) {
    $_GET['token'] = "";
}
$connection = new Simu($_GET['token'], $bddConnection);
if ($_GET['token'] == ""){
    $datastation = $connection->setnewkey();
    $connection->setlogdb(200);
}
else
{
    if($connection->key_exist())
    {
        http_response_code(200);
        $connection->setlogdb(200);
        $datastation = $connection->connectkey();
    }
    else
    {
        http_response_code(405);
        $connection->setlogdb(405);
    }
}
?>