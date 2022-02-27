<?php
include 'connect.inc.php';
include 'user.inf.php';

$sql = "SELECT * FROM Workers WHERE wComp = '$cName'";
$result = mysqli_query($conn, $sql);

echo "<tr>";
echo "<th>WorkerId</th>";
echo "<th>User names</th>";
echo "</tr>";

while ($row = mysqli_fetch_assoc($result)) {
    $wId = $row['wId'];
    $wName = $row['wName'];
    echo "<tr>";
    echo "<td>$wId</td>";
    echo "<td>$wName</td>";
    echo "<td><button type='submit' name='delete' form='deleteform' value='$wId'> Delete </button>";
    echo "</tr>";
}
