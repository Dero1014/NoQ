<?php
include 'connect.inc.php';
include 'worker.inf.php';
include 'worker.fnc.php';
session_start();

$sName = $_POST['servName'];

$xsName = str_replace(' ', '', $sName);
echo "<p> You are working on : $sName</p>";

// name the queue
$qDbName = "QUEUE_" . $xwComp . "_" . $xsName;

// grab user names
$sql = "SELECT * FROM $qDbName JOIN Users ON userId = Users.uId";

// check if there is a line 
if (!$conn->query($sql)) {
    $_SESSION["tStart"] = microtime(true);
    echo "<p>There is nobody in the line</p>";
    exit();
}


$result = mysqli_query($conn, $sql);
// show the rest of the queue
while ($row = mysqli_fetch_assoc($result)) {
    $uName = $row['uName'];
    if ($row['queue'] != 0)
        echo "<p>User: $uName</p>";
    else {
        echo "<p>You are now serving: $uName</p>";

        $_SESSION["tEnd"] = microtime(true);
        $_SESSION["time"] = (int)($_SESSION["tEnd"] - $_SESSION["tStart"]);
        echo $_SESSION["time"];
    }
}



if (isset($_POST['next'])) {
    $_SESSION["tStart"] = microtime(true);

    nextInQueue($conn, $sName, $wComp);
    
    // send time to COMPANY db
    setCurrentAvgTime($conn, $sName, $wComp, (int)$_SESSION["time"]);
}