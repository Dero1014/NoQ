<?php
/*
*   Script to make sure that a user can't move
*   into user.php if it has set to 1
*/

session_start();
if (!isset($_SESSION["username"])) {
    header("Location: ../index.php?error=youarenotlogedin");
    exit();
}

if ($_SESSION["companyTag"] == 1) {
    header("Location: ../index.php?error=notauser");
    exit();
}