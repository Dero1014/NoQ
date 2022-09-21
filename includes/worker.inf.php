<?php
// Everything commented is what we are trying to replace
$pathAuto = "autoloader.inc.php";
$var = getcwd();

if (strpos($var, 'sites')) 
    $pathAuto = "../includes/" . $pathAuto;

include_once $pathAuto;

session_start();
$wComp = $_GET["cn"];
$worker;

if (isset($_SESSION['worker'])) {
    $worker = $_SESSION['worker'];
}