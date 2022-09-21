<?php

if (!isset($_POST["mobileLogin"])) {
    echo "Login rejected";
    exit();
}

include '../includes/autoloader.inc.php';

$inspector = new Inspector();
$query = new SQL();

$uName =  $_POST['username'];
$uPass =  $_POST['password'];
$json = new stdClass();

$json->uName = $uName;
$json->uPass = $uPass;
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
        $sql = "SELECT uId FROM Users WHERE uName = '$uName'";
        $row = $query->getStmtRow($sql);
        $json->uId = $row['uId'];
    }
}

echo '"' . json_encode($json) . '"';