<?php
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
} else if (strpos($var, 'includes')) {
    $pathUser = "../classes/" . $pathUser;
    $pathSQL = "../classes/" . $pathSQL;
    $pathError = "../classes/" . $pathError;
} else {
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

session_start();
$wComp = $_GET["cn"];
$worker = new Worker(0, "find", "", $wComp);

$worker = $_SESSION['worker'];


$wId = $_SESSION["workerid"];
$wName = $_SESSION["workerName"];
$wComp = $_SESSION["workercompany"];
$xwComp = $_SESSION["workercompanywithoutspaces"];
$cDbName = "COMPANY_" . $xwComp;
