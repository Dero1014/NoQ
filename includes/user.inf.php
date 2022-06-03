<?php
session_start();

$_SESSION["gotInQueue"];

$uId = $_SESSION["userid"];
$uName = $_SESSION["username"];
$uComp = $_SESSION["companyTag"];

$cName; 
$xcName;
$cDbName;
$qNumber;

if (isset($_SESSION["queue"])) {
    $qNumber = $_SESSION["queue"];
}

if ($uComp === 1) {
    $cName = $_SESSION["companyname"];
    $xcName = $_SESSION["companynamewithoutspaces"];
    $cDbName = "COMPANY_" . $xcName;
}
