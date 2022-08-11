<?php

//check if the user hit submit otherwise return them back
if (!isset($_POST["submitReg"])) {
    header("Location: ../site/signup.site.php?signup=youlilshitpt2");
    exit();
}

include 'connect.inc.php';
include 'reglog.fnc.php';
include 'common.fnc.php';


$uName = $_POST['regUserName'];
$uPass = $_POST['regUserPass'];
$uEmail = $_POST['regUserEmail'];
$uCompany = checkSet($_POST['regUserCompany']);

$cName;
$cDesc;
$words;

if ($uCompany === 1) {
    $cName = $_POST['regCompName'];
    $cDesc = $_POST['regCompDesc'];
    $words = array($uName, $uPass, $uEmail, $cName, $cDesc);
    echo "he has a comsany\n";
} else {
    $words = array($uName, $uPass, $uEmail);
}


// error handlers
// check if anything is empty
if (areEmpty($words)) {
    header("Location: ../sites/signup.site.php?signup=empty");
    exit();
}

// check if the inputs are valid
if (invalidInput($words)) {
    header("Location: ../sites/signup.site.php?signup=invalidinput");
    exit();
}

//check if the mail is valid
if (!filter_var($uEmail, FILTER_VALIDATE_EMAIL)) {
    print "invalid email $uEmail" . PHP_EOL;
    die();
    header("Location: ../sites/signup.site.php?signup=invalidemail");
    exit();
}

//check if the user already exists
if (alreadyExists($conn, $uName, "uName", "Users") || alreadyExists($conn, $uEmail, "uEmail", "Users")) {
    header("Location: ../sites/signup.site.php?signup=userexists");
    exit();
}

// Check if company exists
if (companyExists($conn, $cName)) {
    header("Location: ../sites/signup.site.php?signup=companyexists");
    exit();
}
// If no errors are found continue
addUser($conn, $uName, $uPass, $uEmail, $uCompany, $cName, $cDesc);
header("Location: ../sites/signup.site.php?signup=success");
