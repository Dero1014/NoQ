<!-- SERVICE SELECT FORM -->
<form action="../includes/user.inc.php" method="POST">
    <select id="selCompanies" name="companies">
        <option>-----</option>
        <?php
        $query = new SQL();
        $sql = "SELECT cName FROM Companies;";
        $result = $query->getStmtAll($sql);
        for ($i=0; $i < sizeof($result); $i++) { 
            $cName = $result[$i][0];
            $xcName = str_replace(' ', '', $cName);
            echo "<option value=$xcName>$cName</option>";
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
