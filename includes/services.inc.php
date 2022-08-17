<?php
// Display services of the selected company

include 'connect.inc.php';
include 'autoloader.inc.php';

$query = new SQL();

$cName = $_POST['cName'];
$xcName = str_replace(' ', '', $cName);
$cDbName = "COMPANY_" . $xcName;

$sql = "SELECT * FROM $cDbName";
$result = $query->getStmtAll($sql);

for ($i=0; $i < sizeof($result); $i++) { 
    $sName = $result[$i][1];
    $xsName = str_replace(' ', '', $sName);
    echo "<option value=$xsName>$sName</option>";
} 