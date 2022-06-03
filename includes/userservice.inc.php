<?php

include 'connect.inc.php';
include 'user.inf.php';
include 'user.fnc.php';

$uId = (int)$_POST['userId'];
checkQueue($conn, $uId);
$inQueue = $_SESSION["gotInQueue"] ;

echo("<p >Current $inQueue</p>");
if (isset($_SESSION['queue'])) {
    echo "<p >My Current queue is $qNumber</p>";
    $avgTime = getAvgTime($conn, $uId);
    echo "<p >Average wait time is: $avgTime</p>";
}else if (isset($_SESSION['inLine'])) {
    echo "<p >You are UP!</p>";
}else if($_SESSION["gotInQueue"] == 1){
    echo '<script type="text/JavaScript"> location.reload(); </script>';
    $_SESSION["gotInQueue"] = 0;
}