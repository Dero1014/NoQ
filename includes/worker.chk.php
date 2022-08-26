<?php
// Added a safety feature to not allow to go into the page 
include '../includes/worker.inf.php';

if ($worker == NULL) {
    if (!isset($_GET['cn'])) {
        header("Location: ../index.php?error=wrongInfo");
    }
    
    if (!isset($_GET['p'])) {
        header("Location: ../index.php?error=wrongInfo");
    }
}