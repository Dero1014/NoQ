<?php
header('Content-type: text/plain');
include 'connect.inc.php';
include 'worker.inf.php';
include 'worker.fnc.php';
include 'autoloader.inc.php';

session_start();
$inspector = new Inspector();
$queue = new Queue();
$userInWorker = false;
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
        echo "<p> My User : $uName  </p>";
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

// Show users

// name the queue
$qDbName = "QUEUE_" . $xwComp . "_" . $xsName;

// grab user names
$sql = "SELECT * FROM $qDbName JOIN Users ON userId = Users.uId";

// grab user from worker
$userInWorker = userInProcess($conn);


if ($userInWorker) { // if there is a user being served display it
    $mysql = "SELECT uName FROM Workers JOIN Users ON userId = Users.uId WHERE wId = $wId;";
    $_SESSION["tEnd"] = time();
    $result = mysqli_query($conn, $mysql);
    $row = mysqli_fetch_assoc($result);
    $uName = $row['uName'];
    echo "<p>Currently serving $uName<p>";
    echo $_SESSION["tEnd"];
} else {
    echo "<p>Press Next to advance the line<p>";
}

$result = mysqli_query($conn, $sql);

// show queue to worker
while ($row = mysqli_fetch_assoc($result)) {
    $uName = $row['uName'];

    if ($row['queue'] != 0)
        echo "<p>User: $uName</p>";
}

// advance the queue
if (isset($_POST['next'])) {
    nextInQueue($conn, $sName, $wComp);
    $_SESSION["time"] = (int)($_SESSION["tEnd"] - $_SESSION["tStart"]);
    echo $_SESSION["time"];
    $_SESSION["tStart"] = time();

    // send time to COMPANY db
    setCurrentAvgTime($conn, $sName, $wComp, (int)$_SESSION["time"]);
}
