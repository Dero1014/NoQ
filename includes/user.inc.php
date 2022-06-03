<?php
// THIS PAGE IS USED TO ADD QUEUE UP TO SERVICES MADE BY A COMPANY //
// A DB WILL BE CREATED FOR THE SERVICE WITH THE FORMAT OF         //
// QUEUE_[companyname]_[service] AND IT WILL BE DELETED IF THERE   //
// IS NO ONE IN THE QUEUE                                          //

include 'connect.inc.php';
include 'user.inf.php';
include 'user.fnc.php';

if (!isset($_POST["queueUp"])) {
    header("Location: ../user.site.php?error=hacktry");
    exit();
}
$cName = $_POST['companies'];
$sName = $_POST['services'];
echo("whoops");
queueUp($conn, $cName, $sName, $uId);
header("Location: ../sites/user.site.php?queueup=success");