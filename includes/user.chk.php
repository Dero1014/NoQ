<?php
/*
*   Script to make sure that a user can't move
*   into user.php if it has set to 1
*/
include '../includes/user.inf.php';

session_start();

if (!isset($user)) {
    header("Location: ../index.php?error=youAreNotLogedIn");
    exit();
}
if ($_SESSION["companyTag"] == 1 || $user->getCompanyTag() == 1) {
    header("Location: ../index.php?error=notAUser");
    exit();
}