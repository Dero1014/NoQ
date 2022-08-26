<?php
header('Content-type: text/plain');
include 'connect.inc.php';
include 'worker.inf.php';
include 'worker.fnc.php';
include 'autoloader.inc.php';

session_start();

$userInWorker = false;
$sName = $_POST['servName']; // remember to change it back to servName
$inspector = new Inspector();
$queue = new Queue();
$queue->queueSetup($worker->getWorkerCompanyName(), $sName, -1);

if (!$inspector->tableExists($queue->getQueueName())) {
    exit();
}

if (isset($_POST['drop'])) {
    $worker->dropOut($worker->getMyUser()->getUId());
} else {
    $worker->processUser($sName);
    $worker->nextInQueue($sName);
}
