<!-- COMPANY WORKER REGISTRATION PAGE -->

<form method="POST">

    <?php
    // Add a worker to company
    include '../includes/common.fnc.php';
    include '../includes/company.fnc.php';

    if (isset($_POST['addWorker'])) {
        $inspector = new Inspector();
        
        $cName = $company->getCompanyName();
        $wName = $_POST['workerName'];

        $inspector->workerReady($wName, $company->getWorkerTableName());

        $rngPass = randomString();
        $hashedPwd = password_hash($rngPass, PASSWORD_DEFAULT);

        $company->setWorker($hashedPwd, $wName);
        echo "<p>https://noq.ddns.net/sites/worker.site.php?cn=$cName&p=$hashedPwd</p>";
        echo "<br>";
        echo "<p>Password is : $rngPass</p>";
    }
    else
    {
        echo "Press generate to generate a worker";
    }

    ?>
    <br>
    <input type="text" name="workerName" placeholder="User Name">

    <input type="submit" name="addWorker" value="Generate link and password">
</form>

<form id="deleteform" action="../includes/workerdelete.inc.php" method="POST"></form>

<!-- WORKER TABLE TO MANAGE-->
<br>
<div id="cont">
    <table id="myTable">
 
    </table>
</div>

<!-- SCRIPT TO UPDATE THE WORKERS TABLE -->
<script>
    $(document).ready(function() {
        $("#myTable").load("../includes/workertable.inc.php");
    });
</script>