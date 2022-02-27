<!-- SERVICE SELECT FORM -->
<form action="../includes/user.inc.php" method="POST">
    <select id="selCompanies" name="companies">
        <option>-----</option>
        <?php
        include '../includes/connect.inc.php';

        $sql = "SELECT cName FROM Companies";
        $result = mysqli_query($conn, $sql);

        while ($row = mysqli_fetch_array($result)) {
            $compName = $row['cName'];
            $value = str_replace(' ', '', $compName);
            echo "<option value=$value>$compName</option>";
        }

        ?>
    </select>
    <br>
    <select id="selServices" name="services">
        <option>-----</option>
    </select>
    <br>
    <input type="submit" name="queueUp" value="QUEUE UP!">
</form>
