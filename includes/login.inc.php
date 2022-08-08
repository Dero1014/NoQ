<?php

if (!isset($_POST["submitLog"])) {
    header("Location: ../sites/login.site.php?signin=youLilShit");
    exit();
}

include 'connect.inc.php';
include 'reglog.fnc.php';
include 'common.fnc.php';

$uName =  $_POST['logUserName'];
$uPass =  $_POST['logUserPass'];
echo $uName."\n";
echo $uPass."\n";
//check if input is empty
$words = array($uName, $uPass);
if (areEmpty($words)) {
    header("Location: ../sites/login.site.php?signin=empty");
    exit();
}

//check if the input is alright
if (invalidInput($words)) {
    header("Location: ../sites/login.site.php?signin=invalidinput");
    exit();
}

if (alreadyExists($conn, $uName, "uName", "Users") != true){
    header("Location: ../sites/user.site.php?signin=fail");
    exit();
}

loginUser($conn, $uName, $uPass);