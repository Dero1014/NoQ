<?php
// THIS PAGE IS USED TO ADD QUEUE UP TO SERVICES MADE BY A COMPANY //
// A DB WILL BE CREATED FOR THE SERVICE WITH THE FORMAT OF         //
// QUEUE_[companyname]_[service] AND IT WILL BE DELETED IF THERE   //
// IS NO ONE IN THE QUEUE                                          //

include 'connect.inc.php';
include 'user.fnc.php';

echo("Start \n\r");
//_POST
$cName = $_POST['companies'];
$sName = $_POST['services'];
$uId = (int)$_POST['uId'];

$json = new stdClass();

$json->companies = $cName;
$json->services = $sName;
$json->uid = $uId;
echo json_encode($json);

queueUp($conn, $cName, $sName, $uId);
header("Location: ../sites/user.site.php?queueup=success");