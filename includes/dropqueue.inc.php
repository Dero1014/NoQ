<?php

include 'autoloader.inc.php';
include 'user.inf.php';

if (!isset($_POST['drop'])) {
    header("Location: ../sites/user.site.php?drop=invalidAccess");
    exit();
}

$queue->dropFromQueue();
header("Location: ../sites/user.site.php?drop=success");