<?php
//header('Content-type: text/plain');
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

// Everything commented is what we are trying to replace
$pathAuto = "autoloader.inc.php";
$pathUser = "user.class.php";
$pathSQL = "sql.class.php";
$pathError = "errorinfo.class.php";
$var = getcwd();

if (strpos($var, 'sites')) {
    $pathAuto = "../includes/" . $pathAuto;
    $pathUser = "../classes/" . $pathUser;
    $pathSQL = "../classes/" . $pathSQL;
    $pathError = "../classes/" . $pathError;
}else if (strpos($var, 'includes')) {
    $pathUser = "../classes/" . $pathUser;
    $pathSQL = "../classes/" . $pathSQL;
    $pathError = "../classes/" . $pathError;
}else {
    $pathUser = "classes/" . $pathUser;
    $pathSQL = "classes/" . $pathSQL;
    $pathError = "classes/" . $pathError;
}
//echo "$pathUser ";
//echo "$pathSQL ";
//echo "$pathError ";

include_once $pathAuto;
//include_once $pathError;
//include_once $pathSQL;
//include_once $pathUser;

$user = new User(-1, 'template', 'template', 0);

session_start();

$_SESSION["gotInQueue"];

$user = $_SESSION["User"];
$company;
//var_dump($user);
if ($user != NULL) {
    $company = $user->getCompany();
    if (is_a($company, "Company")) {
        $company = clone $user->getCompany();
        $company->fetchServices();
    }
}

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






/*
$classUser = "/user.class.php";
$pathUser = "";
if (str_contains(getcwd(), 'sites') || str_contains(getcwd(), 'includes')) {
    $pathUser = "../classes";
}
$pathUser = $pathUser . $classUser;
echo $pathUser;
*/