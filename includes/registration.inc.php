<?php
header('Content-type: text/plain');

//check if the user hit submit otherwise return them back
if (!isset($_POST["submitReg"])) {
    header("Location: ../site/signup.site.php?signup=youlilshitpt2");
    exit();
}

include_once 'reglog.fnc.php';
include 'common.fnc.php';
include 'autoloader.inc.php';
$inspector = new Inspector();
$uName = $_POST['regUserName'];
$uPass = $_POST['regUserPass'];
$uEmail = $_POST['regUserEmail'];
$uCompany = checkSet($_POST['regUserCompany']);

$cName;
$cDesc;

if ($uCompany === 1) {
    $cName = $_POST['regCompName'];
    $cDesc = $_POST['regCompDesc'];
    echo "He has a company \n";
} 

// error handlers
if ($inspector->registerUserReady($uName, $uPass, $uEmail, $uCompany, $cName, $cDesc)) {
    // If no errors are found continue
    $register = new Register();
    $register->addUser($uName, $uPass, $uEmail, $uCompany, $cName, $cDesc);
    header("Location: ../sites/signup.site.php?signup=success");
}
