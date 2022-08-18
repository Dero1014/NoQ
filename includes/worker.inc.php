<?php
// File worker.inc.php is a file that gets users login data //
// and checks if the user exists or not by comparing the    //
// random generated password with the company it's ment for //

include 'connect.inc.php';
include 'common.fnc.php';
include 'autoloader.inc.php';
include 'worker.fnc.php';
include 'worker.inf.php';

if (isset($_POST["login"])) {
    $inspector = new Inspector();
    
    // GET VALUES //
    $wPass = $_POST['wPass'];
    $wComp = $_POST['wComp'];

    $inspector->workerLoginReady($wPass, $wComp);

    // give access
    access($conn, $wPass, $wComp);
    exit();
}


