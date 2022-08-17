<?php
include 'connect.inc.php';
include 'user.inf.php';
include 'user.fnc.php';

$uId = $user->getUId();
if ($queue->inQueue($uId)) {
    $qNumber = $queue->getQueueNumber();
    echo "<p >My Current queue is $qNumber</p>";
    $avgTime = $queue->getAvgTime();
    if ($avgTime < 1) {
        $avgTime = 1;
        echo "<p >Average wait time is: <$avgTime mins</p>";
    }else {
        $avgTime *= $qNumber;
        echo "<p >Average wait time is: $avgTime mins</p>";
    }
}else if ($queue->getMyTurn() == 1) {
    echo "<p >You are UP!</p>";
}else if($_SESSION["gotInQueue"] == 1){
    echo '<script type="text/JavaScript"> location.reload(); </script>';
    $_SESSION["gotInQueue"] = 0;
}