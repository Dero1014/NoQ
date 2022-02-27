<?php
session_start();

$wId = $_SESSION["workerid"];
$wName = $_SESSION["workerName"];
$wComp = $_SESSION["workercompany"];
$xwComp = $_SESSION["workercompanywithoutspaces"];
$cDbName = "COMPANY_".$xwComp;