<?php
include 'connect.inc.php';
include 'user.inf.php';

$sql = "SELECT * FROM " . $company->getCompanyTableName() . ";";
$cName = $company->getCompanyName();
$result = mysqli_query($conn, $sql);

echo "<tr>";
echo "<th>Services</th>";
echo "<th>Number of users</th>";
echo "<th>Average time</th>";
echo "<th>QR Code</th>";
echo "</tr>";

for ($i = 0; $i < $company->getServiceLength(); $i++) {
    $service = $company->getService($i);
    $servName = $service->getServiceName();
    $numberOfUsers = $service->getServiceNumber();
    $avgTime = $service->getServiceTime();
    echo "<tr>";
    echo "<td>$servName</td>";
    echo "<td>$numberOfUsers</td>";
    echo "<td>$avgTime</td>";
    echo "<td><a href='https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=Qcn=$cName;s=$servName&choe=UTF-8'>Service Qr</a></td>";
    echo "<td><button type='submit' name='delete' form='deleteform' value='$servName'> Delete </button></td>";
    echo "</tr>";
}