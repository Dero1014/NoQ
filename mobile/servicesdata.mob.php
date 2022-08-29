<?php

if (!isset($_POST["grabServData"])) {
    echo "Request denied\n";
    exit();
}
echo "Request granted\n";
include '../includes/autoloader.inc.php';

$query = new SQL();
$xcName = $_POST["xcName"];
$cTableName = "COMPANY_" . $xcName;
$sql = "SELECT * FROM $cTableName;";

$result = $query->getStmtAll($sql);
$length = sizeof($result);
$json = new stdClass();
$json->size = $length;
echo json_encode($json);

for ($i=0; $i < sizeof($result); $i++) { 
    $json = new stdClass();
    $json->sId = $result[$i][0];
    $json->sName = $result[$i][1];
    $json->numberOfUsers = $result[$i][2];
    $json->avgTime = $result[$i][3];
    $json->timeSum = $result[$i][4];
    echo json_encode($json);
}