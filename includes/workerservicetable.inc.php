<?php
header('Content-type: text/plain');
include 'worker.inf.php';
include 'autoloader.inc.php';

session_start();
$inspector = new Inspector();
$queue = new Queue();
$sName = $_POST['servName']; // remember to change it back to servName

$queue->queueSetup($worker->getWorkerCompanyName(), $sName, -1);

$xsName = str_replace(' ', '', $sName);

if ($sName != '-----') {
    echo "<p> You are working on $sName service</p>";
} else {
    echo "<p> Pick a service to start working on</p>";
}

if ($inspector->tableExists($queue->getQueueName())) {
    if ($worker->getMyUser() != NULL) {
        $myUser = $worker->getMyUser();
        $uName = $myUser->getUsername();
        $time = $worker->getCurrentTime();
        echo "<p> My User : $uName  </p>";
        echo "<p> Time elpased : $time  </p>";
    }
    
    $worker->showQueue($sName);

} else {
    if ($worker->getMyUser() != NULL) {
        $myUser = $worker->getMyUser();
        $uId = $myUser->getUId();
        $worker->dropOut($uId);
    }
    echo "<p> This queue is empty </p>";
}
