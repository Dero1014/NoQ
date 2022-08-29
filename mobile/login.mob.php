<?php

if (!isset($_POST["mobileLogin"])) {
    echo "Login rejected";
    exit();
}

include '../includes/autoloader.inc.php';

$inspector = new Inspector();

$uName =  $_POST['username'];
$uPass =  $_POST['password'];
$json = new stdClass();

$json->username = $uName;
$json->password = $uPass;
$json->login = "failed";

if ($inspector->loginUserReady($uName, $uPass, true)) {
    echo "\nLogin started...\n";

    $user = new User(-1, $uName, "", 0, true);

    if ($user->getCompanyTag() === 1) {
        $json->login = "company can't login ... yet ;)";
        echo json_encode($json);
        exit();
    }

    $login = new Login();
    $result = $login->loginUser($uName, $uPass, true);
    if ($result === true) {
        $json->login = "success";
    }
}

echo '"' . json_encode($json) . '"';
