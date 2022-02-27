<?php

/*
*   Script to make sure that a user can't move
*   into company.php if it has uCompany set to 0
*/

session_start();
if (!isset($_SESSION["username"])) {
    header("Location: ../index.php?error=youarenotlogedin");
    exit();
}


if ($_SESSION["companyTag"] != 1) {
    header("Location: ../index.php?error=notacompany");
    exit();
}