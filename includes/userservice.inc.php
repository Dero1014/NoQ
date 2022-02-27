<?php

include 'connect.inc.php';
include 'user.inf.php';
include 'user.fnc.php';

$uId = (int)$_POST['userId'];

checkQueue($conn, $uId);

if (isset($_SESSION['queue'])) {
    echo "<p >My Current queue is $qNumber</p>";
    $avgTime = getAvgTime($conn, $uId);
    echo "<p >Average wait time is: $avgTime</p>";
}
