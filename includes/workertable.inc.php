<?php
include 'user.inf.php';

$cName = $company->getCompanyName();

$sql = "SELECT * FROM Workers WHERE wComp = '$cName'";
$result = mysqli_query($conn, $sql);

echo "<tr>";
echo "<th>WorkerId</th>";
echo "<th>User names</th>";
echo "</tr>";

for ($i = 0; $i < $company->getWorkerLength(); $i++) {
    $worker = $company->getWorker($i);
    $wId = $worker->getWorkerId();
    $wName = $worker->getWorkerName();
    
    echo "<tr>";
    echo "<td>$wId</td>";
    echo "<td>$wName</td>";
    echo "<td><button type='submit' name='delete' form='deleteform' value='$wId'> Delete </button>";
    echo "</tr>";
}
