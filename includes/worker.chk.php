<?php
// add a safety feature to not allow to go into the page 

session_start();
if (!isset($_SESSION["workerid"])) {
    if (!isset($_GET['cn'])) {
        header("Location: ../index.php?error=wronginfo");
    }
    
    if (!isset($_GET['p'])) {
        header("Location: ../index.php?error=wronginfo");
    }
}