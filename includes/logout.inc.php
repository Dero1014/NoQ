<?php

include_once 'worker.inf.php';

if ($worker != NULL) {
    $worker->dropOut($worker->getMyUser()->getUId());
}

session_start();
session_unset();
session_destroy();

header("Location: ../index.php?logout=success");
exit();