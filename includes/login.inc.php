<?php
header('Content-type: text/plain');
if (!isset($_POST["submitLog"])) {
    header("Location: ../sites/login.site.php?signin=youLilShit");
    exit();
}

//include 'connect.inc.php';
include 'reglog.fnc.php';
include 'common.fnc.php';
include 'autoloader.inc.php';

$inspector = new Inspector();
$uName =  $_POST['logUserName'];
$uPass =  $_POST['logUserPass'];

if ($inspector->loginUserReady($uName, $uPass)) {
    $login = new Log();
    $result = $login->loginUser($uName, $uPass);
    header("Location: ../sites/$result");
    //loginUser($uName, $uPass);
}
