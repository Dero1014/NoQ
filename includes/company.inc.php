<?php
// THIS PAGE IS USED TO ADD SERVICES TO THE COMPANIES SERVICE DB  //
// USING THE PREFIX OF COMPANY_ + [COMPANY] NAME TO FIND THE DB   //

include 'connect.inc.php';
include 'company.fnc.php';
include 'common.fnc.php';
include 'user.inf.php';


if (!isset($_POST["addService"])) {
    header("Location: ../company.site.php?error=hacktry");
    exit();
}

$page = "page=service";

// get values //
$sName = $_POST['serviceName'];
$words = array($sName);

// check if empty //
if (areEmpty($words)) {
    header("Location: ../sites/company.site.php?$page&error=empty");
    exit();
}

// check if the inputs are valid
if (invalidInput($words)) {
    header("Location: ../sites/company.site.php?$page&error=invalidname");
    exit();
}

// check if service exists
if (serviceExists($conn, $sName, $cName)) {
    header("Location: ../sites/company.site.php?$page&error=serviceexists");
    exit();
}

// add service to db
addService($sName, $conn, $cDbName);
header("Location: ../sites/company.site.php?$page&service=success");