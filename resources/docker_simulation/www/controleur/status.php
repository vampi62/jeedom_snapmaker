<?php
require_once('class/simu.class.php');

$connection = new Simu($_GET['token'], $bddConnection);
if($connection->key_exist())
{
    if($connection->key_connect())
    {
        if($connection->key_valide())
        {
            http_response_code(200);
            $connection->setlogdb(200);
            $datastation = $connection->getstatus();
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
else
{
    http_response_code(405);
    $connection->setlogdb(405);
}
?>