<!-- COMMON USER PAGE -->

<?php
include '../header/header.php';
include '../includes/user.chk.php';
?>

<!-- WELCOME TITLE-->
<?php
if (isset($user)) {
    $name = $user->getUsername();
    echo "<h1>Welcome user " . $user->getUsername() ."</h1>";
} else {
    echo "<h1>No name exists</h1>";
}
?>

<!-- QUEUE CHECKING -->
<?php
// check if user is already in queue
include '../includes/user.fnc.php';
include '../includes/connect.inc.php';
checkQueue($conn, $uId);
?>

<!-- SERVICE SELECT FORM IF USER NOT IN A QUEUE -->
<?php
$uId = $user->getUId();
if (!$queue->inQueue($uId)) {
    include 'uservice.site.php';
}
?>

<!-- USER IN QUEUE -->
<?php
    echo"<p id='id' style='display:none'>$uId</p>";
?>
<DIV id ='queueInfo' ></DIV>

</body>

</html>

<!-- Script to update companies services -->
<script>
    var selComp = document.getElementById("selCompanies");
    var div = document.getElementById("id");
    var cNameValue;
    var uId;
    $(document).ready(function() {
        setInterval(function() {
            uId = div.innerText;
            $("#queueInfo").load("../includes/userservice.inc.php", {
                userId: uId
            });
        }, 100)

        $("#selCompanies").change(function() {
            cNameValue = selComp.value;
            $("#selServices").load("../includes/services.inc.php", {
                cName: cNameValue
            });
        });
    });
</script>