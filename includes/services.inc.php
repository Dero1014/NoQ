<?php

include 'connect.inc.php';

$cName = $_POST['compName'];
$xcName = str_replace(' ', '', $cName);
$cDbName = "COMPANY_" . $xcName;

$sql = "SELECT * FROM $cDbName";
$result = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    $servName = $row['sName'];
    $value = str_replace(' ', '', $servName);
    echo "<option value=$value>$servName</option>";
}
