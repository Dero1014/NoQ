<?php
include 'user.inf.php';

$sId = $_POST['delete'];
$service = $company->getServiceById($sId);
$sName = $service->getServiceName();

$queue = new Queue();
$inspector = new Inspector();

// Company table name
$cDbName = $company->getCompanyTableName();

// Queue table name
$qDbName = $company->getServiceQueueTableName($sId);

// Remove any data about the queue
if ($inspector->serviceQueueDeletionReady($qDbName)) {
    $queue->dropQueueTable($qDbName);
}

// Remove service
$company->removeService($sId);

$page = "page=service";

header("Location: ../sites/company.site.php?$page");
