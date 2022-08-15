<?php
include 'connect.inc.php';
include 'user.inf.php';

$sql = "SELECT * FROM " . $user->getCompany()->getCompanyTableName() .";";
$cName = $user->getCompany()->getCompanyName();
$result = mysqli_query($conn, $sql);

echo "<tr>";
echo "<th>Services</th>";
echo "<th>Number of users</th>";
echo "<th>Average time</th>";
echo "<th>QR Code</th>";
echo "</tr>";

while ($row = mysqli_fetch_assoc($result)) {
    $servName = $row['sName'];
    $numberOfUsers = $row['numberOfUsers'];
    $avgTime = $row['avgTime'];
    echo "<tr>";
    echo "<td>$servName</td>";
    echo "<td>$numberOfUsers</td>";
    echo "<td>$avgTime</td>";
    echo "<td><a href='https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=Qcn=$cName;s=$servName&choe=UTF-8'>Service Qr</a></td>";
    echo "<td><button type='submit' name='delete' form='deleteform' value='$servName'> Delete </button></td>";
    echo "</tr>";
}
