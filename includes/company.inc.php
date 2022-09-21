<?php
// THIS PAGE IS USED TO ADD SERVICES TO THE COMPANIES SERVICE DB  //
// USING THE PREFIX OF COMPANY_ + [COMPANY] NAME TO FIND THE DB   //
header('Content-type: text/plain');

include 'user.inf.php';
include 'autoloader.inc.php';

if (!isset($_POST["addService"])) {
    header("Location: ../company.site.php?error=invalidAccess");
    exit();
}

$inspector = new Inspector();

$page = "page=service";

// Get values
$sName = $_POST['serviceName'];

if ($inspector->serviceInsertReady($sName, $company->getCompanyTableName())) {
    $company->setService($sName);
    header("Location: ../sites/company.site.php?$page&service=success");
}