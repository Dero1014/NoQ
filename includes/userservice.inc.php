<?php
include 'user.inf.php';

$uId = $user->getUId();

// Is user in queue
if ($queue->inQueue($uId)) {

    // Is user in turn
    if ($queue->getMyTurn()) {
        echo "<p >You are UP!</p>";
        exit();
    }

    // Show users position and average wait time
    $qPosition = $queue->getPositionNumber();
    echo "<p >My Current queue is $qPosition</p>";

    $avgTime = $queue->getAvgTime();
    if ($avgTime < 1) {
        $avgTime = 1;
        echo "<p >Average wait time is: <$avgTime mins</p>";
    } else {
        $avgTime *= $qPosition;
        echo "<p >Average wait time is: $avgTime mins</p>";
    }

} else if ($queue->getMyTurn() !== 1) {
    echo '<script type="text/JavaScript"> location.reload(); </script>';
}
