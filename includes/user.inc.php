<?php
// THIS PAGE IS USED TO ADD QUEUE UP TO SERVICES MADE BY A COMPANY //
// A DB WILL BE CREATED FOR THE SERVICE WITH THE FORMAT OF         //
// QUEUE_[companyname]_[service] AND IT WILL BE DELETED IF THERE   //
// IS NO ONE IN THE QUEUE                                          //
header('Content-type: text/plain');

include 'user.inf.php';
include 'autoloader.inc.php';

if (!isset($_POST["queueUp"])) {
    header("Location: ../user.site.php?error=invalidAccess");
    exit();
}
$cName = $_POST['companies'];
$sName = $_POST['services'];
$uId = $user->getUId();

$inspector = new Inspector();

$queue = new Queue();

$inspector->queueReady($sName, $cName, $uId);
$queue->queueUp($cName, $sName, $uId);
header("Location: ../sites/user.site.php?queueup=success");