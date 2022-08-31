<?php
//header('Content-type: text/plain');

// Everything commented is what we are trying to replace
$pathAuto = "autoloader.inc.php";
$var = getcwd();

if (strpos($var, 'sites')) 
    $pathAuto = "../includes/" . $pathAuto;

include_once $pathAuto;

session_start();

$user = $_SESSION["User"];
$company;
$queue = new Queue();

//var_dump($user);
if ($user != NULL) {
    $company = $user->getCompany();
    if (is_a($company, "Company")) {
        $company = clone $user->getCompany();
        $company->fetchServices();
        $company->fetchWorkers();
    }else {
        $queue->inQueue($user->getUId());
    }
}