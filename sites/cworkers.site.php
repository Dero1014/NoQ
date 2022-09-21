<!-- COMPANY WORKER REGISTRATION PAGE -->

<form method="POST">

    <?php
    // Add a worker to company
    include '../includes/common.fnc.php';

    if (isset($_POST['addWorker'])) {
        $inspector = new Inspector();

        $cName = $company->getCompanyName();
        $wName = $_POST['workerName'];

        $inspector->workerInsertReady($wName, $company->getWorkerTableName());

        $rngPass = randomString();
        $hashedPwd = password_hash($rngPass, PASSWORD_DEFAULT);

        $company->setWorker($hashedPwd, $wName);
        $url = "https://noq.ddns.net/sites/worker.site.php?cn=$cName&p=$hashedPwd";
        echo "<p>Link: </p>";
        echo "<p id = 'link'>$url</p>";
        echo "<br>";
        echo "<p>Password: </p><p id = 'pass'>$rngPass</p>";
    } else {
        echo "Press generate to generate a worker";
    }

    ?>
    <br>
    <input type="text" name="workerName" placeholder="User Name">
    <br>
    <input type="submit" name="addWorker" value="Generate worker">
</form>

<input type="button" onclick='copyClipboard("link")' value="Copy link">
<input type="button" onclick='copyClipboard("pass")' value="Copy password">

<!-- ERROR MESSAGES -->
<?php
if (isset($_GET['worker'])) {
    switch ($_GET['worker']) {
        case 'workerExists':
            echo "<p>Worker already exists</p>";
            break;
        case 'invalid':
            echo "<p>You entered invalid input</p>";
            break;
        case 'empty':
            echo "<p>You didn't fill up the form</p>";
            break;
        default:
            # code...
            break;
    }
}
?>

<form id="deleteform" action="../includes/workerdelete.inc.php" method="POST"></form>

<!-- WORKER TABLE TO MANAGE-->
<br>
<div id="cont">
    <table id="myTable">

    </table>
</div>

<!-- SCRIPT TO UPDATE THE WORKERS TABLE -->
<script>
    function copyClipboard(tag) {
        /* Get the text field */
        var copyText = document.getElementById(tag);
        
        /* Copy the text inside the text field */
        navigator.clipboard.writeText(copyText.innerHTML);

        /* Alert the copied text */
        alert("Copied the text: " + copyText.innerHTML);
    }

    $(document).ready(function() {
        $("#myTable").load("../includes/workertable.inc.php");
    });
</script>