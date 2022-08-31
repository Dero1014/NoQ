<?php

if (!isset($_POST["queueUp"])) {
    echo "Queue denied\n";
    exit();
}
echo "Queue granted\n";
include '../includes/autoloader.inc.php';

$queue = new Queue();

$cName = $_POST["cName"];
$sName = $_POST["sName"];
$uId = (int)$_POST["uId"];
$json = new stdClass();
$json->result = false;

if (!$queue->inQueue($uId)) {
    $json->result = $queue->queueUp($cName, $sName, $uId);
}
echo json_encode($json);