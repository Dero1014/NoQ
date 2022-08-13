<?php
include 'autoloader.inc.php';
$_SESSION["gotInQueue"];
$user = $_SESSION["User"];

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
