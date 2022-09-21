<?php

if (!isset($_POST["inQueue"])) {
    echo "Request denied\n";
    exit();
}
echo "Request granted\n";
include '../includes/autoloader.inc.php';

$queue = new Queue();
$query = new SQL();
$json = new stdClass();
$json->result = false;
$uId = $_POST["uId"];

if($queue->inQueue($uId)){
    $cName = $queue->getCompanyName();
    $cTableName = $queue->getCompanyTableName();
    $sName = $queue->getServiceName();

    $sql = "SELECT * FROM Companies WHERE cName = '$cName';";
    $row = $query->getStmtRow($sql);
    $json->cId = $row["cId"];
    $json->cName = $row["cName"];
    $json->xcName = $row["xcName"];
    $json->cDesc = $row["cDesc"];
    $json->userId = $row["userId"];

    $sql = "SELECT * FROM $cTableName WHERE sName = '$sName';";
    $row = $query->getStmtRow($sql);

    $json->sId = $row["sId"];
    $json->sName = $row["sName"];
    $json->numberOfUsers = $row["numberOfUsers"];
    $json->avgTime = $row["avgTime"];
    $json->timeSum = $row["timeSum"];

    $json->result = true;
}
echo json_encode($json);