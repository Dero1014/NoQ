<?php

if (!isset($_POST["grabQueueData"])) {
    echo "Request denied\n";
    exit();
}
echo "Request granted\n";
include '../includes/autoloader.inc.php';

$queue = new Queue();
$json = new stdClass();

$cName = $_POST["cName"];
$sName = $_POST["sName"];
$uId = (int)$_POST["uId"];
$json->inQueue = false;
if ($queue->inQueue($uId)) {
    $json->position = $queue->getPositionNumber();
    $json->myTurn = $queue->getMyTurn();
    $json->averageTime = (int)$queue->getAvgTime();
    $json->inQueue = true;
}

echo json_encode($json);