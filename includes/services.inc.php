<?php
// Display services of the selected company

include 'autoloader.inc.php';

$query = new SQL();

$cName = $_POST['cName'];
$xcName = str_replace(' ', '', $cName);
$cDbName = "COMPANY_" . $xcName;

$sql = "SELECT * FROM $cDbName";
$result = $query->getStmtAll($sql);

if (!isset($result[0][0])) {
    echo "<option> none </option>";
}

for ($i=0; $i < sizeof($result); $i++) { 
    $sName = $result[$i][1];
    echo "<option value='$sName'>$sName</option>";
} 