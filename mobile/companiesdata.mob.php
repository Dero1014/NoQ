<?php

if (!isset($_POST["grabCompData"])) {
    echo "Request denied\n";
    exit();
}
echo "Request granted\n";
include '../includes/autoloader.inc.php';

$query = new SQL();
$sql = "SELECT * FROM Companies;";

$result = $query->getStmtAll($sql);
$length = sizeof($result);
$json = new stdClass();
$json->size = $length;
echo json_encode($json);

for ($i=0; $i < sizeof($result); $i++) { 
    $json = new stdClass();
    $json->cId = $result[$i][0];
    $json->cName = $result[$i][1];
    $json->xcName = $result[$i][2];
    $json->cDesc = $result[$i][3];
    $json->userId = $result[$i][4];
    echo json_encode($json);
}