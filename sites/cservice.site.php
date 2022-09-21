<!-- COMPANY SERVICE REGISTRATION PAGE -->

<!-- SERVICE REGISTRATION FORUM -->
<form action="../includes/company.inc.php" method="POST">
    <input type="text" name="serviceName" placeholder="Service">
    <br>
    <input type="submit" name="addService" value="Add service">
</form>

<!-- ERROR MESSAGES -->
<?php
if (isset($_GET['service'])) {
    switch ($_GET['service']) {
        case 'serviceExists':
            echo "<p>Service already exists</p>";
            break;
        case 'invalid':
            echo "<p>You entered invalid input</p>";
            break;
        case 'empty':
            echo "<p>You didn't fill up the form</p>";
            break;
        case 'wrongPass':
            echo "<p>Username or password was wrong</p>";
            break;
        case 'success':
            echo "<p>Service has been added!</p>";
            break;
        default:
            # code...
            break;
    }
}
?>

<!-- FORM THAT IS CONNECTED WITH THE DELETE BUTTONS -->
<!-- This form is used to activate the action to delete the service -->
<form id="deleteform" action="../includes/servicedelete.inc.php" method="POST"></form>

<!-- SERVICE TABLE TO MANAGE-->
<br>
<div id="cont">
    <table id="myTable">

    </table>
</div>

<!-- SCRIPT TO UPDATE THE SERVICE TABLE -->
<script>
    $(document).ready(function() {
        $("#myTable").load("../includes/servicetable.inc.php");
    });
</script>