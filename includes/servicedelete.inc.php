<?php

include 'connect.inc.php';
include 'user.inf.php';
$sName = $_POST['delete'];

$xsName = str_replace(' ','',$sName);
$cDbName = $user->getCompany()->getCompanyTableName();
$xcName = $user->getCompany()->getNoSpaceCompanyName();

// queue db name
$qDbName = "QUEUE_" . $xcName . "_" . $xsName;

// remove users from queues
$sql = "DELETE FROM Queues WHERE queueName = '$qDbName'";
$result = mysqli_query($conn, $sql);

// remove queue if queue exists
$sql = "SELECT * FROM $qDbName;";

if ($conn->query($sql)) {
    $sql = "DROP TABLE $qDbName;";
    $result = mysqli_query($conn, $sql);
}

// remove service
$sql = "DELETE FROM $cDbName WHERE sName = '$sName'";
$result = mysqli_query($conn, $sql);

$page = "page=service";

header("Location: ../sites/company.site.php?$page");
