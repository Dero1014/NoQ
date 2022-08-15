<?php
// THIS PAGE IS USED TO ADD SERVICES TO THE COMPANIES SERVICE DB  //
// USING THE PREFIX OF COMPANY_ + [COMPANY] NAME TO FIND THE DB   //
header('Content-type: text/plain');

include 'connect.inc.php';
include 'company.fnc.php';
include 'common.fnc.php';
include 'user.inf.php';
include 'autoloader.inc.php';


if (!isset($_POST["addService"])) {
    header("Location: ../company.site.php?error=hacktry");
    exit();
}

$inspector = new Inspector();

$page = "page=service";

// get values //
$sName = $_POST['serviceName'];
$words = array($sName);

if ($inspector->serviceReady($sName, $company->getCompanyTableName())) {
    // $user->fetchCompany($user->getUId());
    // var_dump($user->getCompany());
    // add service to db
    $user->getCompany()->setService($sName);
    //addService($sName, $conn, $user->getCompany()->getCompanyTableName());
    header("Location: ../sites/company.site.php?$page&service=success");
}

