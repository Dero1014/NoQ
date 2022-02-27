<?php
// File worker.inc.php is a file that gets users login data //
// and checks if the user exists or not by comparing the    //
// random generated password with the company it's ment for //

include 'connect.inc.php';
include 'common.fnc.php';
include 'worker.fnc.php';
include 'worker.inf.php';

if (isset($_POST["login"])) {
    header("Location: ../sites/worker.site.php?error=wrongpass");
    // GET VALUES //
    $wPass = $_POST['wPass'];
    $wComp = $_POST['wComp'];
    $words = array($wPass, $wComp);


    // ERROR HANDLERS //
    // check if empty 
    if (areEmpty($words)) {
        header("Location: ../sites/index.site.php?error=empty");
        exit();
    }

    // check if the inputs are valid
    if (invalidInput($words)) {
        header("Location: ../sites/index.site.php?error=invalidname");
        exit();
    }

    // give access
    access($conn, $wPass, $wComp);
    exit();
}


