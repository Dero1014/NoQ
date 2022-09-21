<?php

if (!isset($_POST["queueDrop"])) {
    echo "Queue drop denied\n";
    exit();
}
echo "Queue drop granted\n";
include '../includes/autoloader.inc.php';

$queue = new Queue();

$cName = $_POST["cName"];
$sName = $_POST["sName"];
$uId = (int)$_POST["uId"];
$json = new stdClass();
$json->drop = false;

if ($queue->inQueue($uId)) 
    $json->drop = $queue->dropFromQueue($cName, $sName, $uId);

echo json_encode($json);