<!-- COMPANY SERVICE REGISTRATION PAGE -->

<!-- SERVICE REGISTRATION FORUM -->
<form action="../includes/company.inc.php" method="POST">
    <input type="text" name="serviceName" placeholder="Service">
    <br>
    <input type="submit" name="addService" value="Add service">
</form>

<!-- FORM THAT IS CONNECTED WITH THE DELETE BUTTONS -->
<!-- the form is used to activate the action to delete the service -->
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