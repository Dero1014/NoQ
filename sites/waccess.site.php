<!-- Working page if the worker loged in -->

<!-- WELCOME TITLE -->
<?php
echo "<h1>Welcome $wName</h1> <br>";
?>

<!-- SERVICE SELECTION-->
<select id="selService" name="services">
    <option>-----</option>

    <?php
    include '../includes/connect.inc.php';

    // select service
    $sql = "SELECT sName FROM $cDbName";
    $result = mysqli_query($conn, $sql);

    while ($row = mysqli_fetch_array($result)) {
        $sName = $row['sName'];
        echo "<option value='$sName'>$sName</option>";
    }
    ?>
</select>
<br>
<button id="servSelect">SELECT SERVICE</button>
<button id="next">NEXT</button>

<div id="cont">

</div>

<!-- Script to update companies services -->
<script>
    var selComp = document.getElementById("selService");
    var value;
    $(document).ready(function() {
        // SELECT SERVICE
        $("#servSelect").click(function() {
            value = selComp.value;
        });

        // refreshes who is in line every 0.1 seconds
        setInterval(function() {
            $("#cont").load("../includes/workservice.inc.php", {
                servName: value
            });
        }, 100)

        $("#next").click(function() {
            $("#cont").load("../includes/workservice.inc.php", {
                servName: value,
                next:""
            });
        });
    });
</script>