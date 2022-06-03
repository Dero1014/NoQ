<?php
include 'connect.inc.php';
include 'worker.inf.php';
include 'worker.fnc.php';
session_start();

$userInWorker = false;
$sName = $_POST['servName'];

//Service name
$xsName = str_replace(' ', '', $sName);
echo "<p> You are working on : $sName</p>";

// name the queue
$qDbName = "QUEUE_" . $xwComp . "_" . $xsName;

// grab user names
$sql = "SELECT * FROM $qDbName JOIN Users ON userId = Users.uId";

// grab user from worker
$userInWorker = userInProcess($conn);

// check if there is a line 
if (!$conn->query($sql) && $userInWorker == false) {
    $_SESSION["tStart"] = microtime(true);
    echo "<p>There is nobody in the line</p>";
    exit();
} 

if ($userInWorker) { // if there is a user being served display it
    $mysql = "SELECT uName FROM Workers JOIN Users ON userId = Users.uId WHERE wId = $wId;";
    $result = mysqli_query($conn, $mysql);
    $row = mysqli_fetch_assoc($result);
    $uName = $row['uName'];
    echo"<p>Currently serving $uName<p>";
}else {
    echo"<p>Press Next to advance the line<p>";
}



$result = mysqli_query($conn, $sql);

// show queue to worker
while ($row = mysqli_fetch_assoc($result)) {
    $uName = $row['uName'];

    if ($row['queue'] != 0)
        echo "<p>User: $uName</p>";
    else {
        $_SESSION["tEnd"] = microtime(true);
        $_SESSION["time"] = (int)($_SESSION["tEnd"] - $_SESSION["tStart"]);
        echo $_SESSION["time"];
    }
}

// advance the queue
if (isset($_POST['next'])) {
    $_SESSION["tStart"] = microtime(true);
    nextInQueue($conn, $sName, $wComp);

    // send time to COMPANY db
    setCurrentAvgTime($conn, $sName, $wComp, (int)$_SESSION["time"]);
}
