<?php

/*
*   Script to make sure that a user can't move
*   into company.php if it has uCompany set to 0
*/
include '../includes/user.inf.php';

if (!isset($_SESSION['User'])) {
    header("Location: ../index.php?error=youAreNotLogedIn");
    exit();
}


if ($user->getCompanyTag() != 1) {
    header("Location: ../index.php?error=notACompany");
    exit();
}