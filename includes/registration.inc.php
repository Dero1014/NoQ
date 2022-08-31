<?php
header('Content-type: text/plain');

// Check if the user hit submit otherwise return them back
if (!isset($_POST["submitReg"])) {
    header("Location: ../site/signup.site.php?signup=invalidAccess");
    exit();
}

include 'common.fnc.php';
include 'autoloader.inc.php';

$inspector = new Inspector();
$uName = $_POST['regUserName'];
$uPass = $_POST['regUserPass'];
$uEmail = $_POST['regUserEmail'];
$uCompany = checkSet($_POST['regUserCompany']);

// Undefined until proccessed
$cName;
$cDesc;

if ($uCompany === 1) {
    $cName = $_POST['regCompName'];
    $cDesc = $_POST['regCompDesc'];
} 

if ($inspector->registerUserReady($uName, $uPass, $uEmail, $uCompany, $cName, $cDesc)) {
    $register = new Register();
    $register->addUser($uName, $uPass, $uEmail, $uCompany, $cName, $cDesc);
    header("Location: ../sites/signup.site.php?signup=success");
}
