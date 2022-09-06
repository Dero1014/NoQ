<?php

if (!isset($_POST["registerUser"])) {
    echo "Registration rejected";
    exit();
}

include '../includes/autoloader.inc.php';

$inspector = new Inspector();
$query = new SQL();

$uName =  $_POST['uName'];
$uEmail =  $_POST['uEmail'];
$uPass =  $_POST['uPass'];
$json = new stdClass();

$json->register = "failed";

if ($inspector->registerUserReady($uName, $uPass, $uEmail, 0, "", "", true)) {
    echo "\nRegistration started...\n";

    $register = new Register();
    $register->addUser($uName, $uPass, $uEmail, 0, "", "");
    $json->register = "success";
}

echo '"' . json_encode($json) . '"';