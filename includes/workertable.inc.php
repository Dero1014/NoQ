<?php
include 'user.inf.php';

$cName = $company->getCompanyName();

$sql = "SELECT * FROM Workers WHERE wComp = '$cName'";
$result = mysqli_query($conn, $sql);

echo "<tr>";
echo "<th>WorkerId</th>";
echo "<th>User names</th>";
echo "<th>Number of Users</th>";
echo "<th>Average time per user</th>";
echo "</tr>";

for ($i = 0; $i < $company->getWorkerLength(); $i++) {
    $worker = $company->getWorkerById($i);
    $wId = $worker->getWorkerId();
    $wName = $worker->getWorkerName();
    $numOfUsers = $worker->getWorkerUserNumber();
    $avgTime = $worker->getWorkerAverageTime() / 60;
    $avgTime = (int) $avgTime;

    if ($avgTime < 1 && $numOfUsers != 0) {
        $avgTime = "<1";
    }
    
    echo "<tr>";
    echo "<td>$wId</td>";
    echo "<td>$wName</td>";
    echo "<td>$numOfUsers</td>";
    echo "<td>$avgTime mins</td>";
    echo "<td><button type='submit' name='delete' form='deleteform' value='$wId'> Delete </button>";
    echo "</tr>";
}
