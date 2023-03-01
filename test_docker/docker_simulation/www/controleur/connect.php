<?php
require_once('class/simu.class.php');

$connection = new Simu($_GET['token'], $bddConnection);
if (!isset($_GET['token']) or empty($_GET['token'])){
    $datastation = $connection->setnewkey();
    $connection->setlogdb(200);
}
else
{
    if($connection->key_exist())
    {
        if($connection->key_valide())
        {
            http_response_code(200);
            $connection->setlogdb(200);
            $datastation = $connection->connectkey();
        }
        else
        {
            http_response_code(204);
            $connection->setlogdb(204);
        }
    }
    else
    {
        http_response_code(405);
        $connection->setlogdb(405);
    }
}
?>