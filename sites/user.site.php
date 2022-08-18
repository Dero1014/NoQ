<!-- COMMON USER PAGE -->

<?php
include '../header/header.php';
include '../includes/user.chk.php';
?>

<!-- WELCOME TITLE-->
<?php
if (isset($user)) {
    $name = $user->getUsername();
    echo "<h1>Welcome user " . $user->getUsername() . "</h1>";
} else {
    echo "<h1>No name exists</h1>";
}
?>

<!-- QUEUE CHECKING -->
<?php
// check if user is already in queue
include '../includes/user.fnc.php';
//$queue->inQueue($user->getUId());
?>

<!-- SERVICE SELECT FORM IF USER NOT IN A QUEUE -->
<?php
$uId = $user->getUId();
$result = $queue->inQueue($uId);



if (!$result) {
    include 'uservice.site.php';
} else {
    echo "<p id='id' style='display:none'>$uId</p>";
    echo "<button type='submit' name='drop' form='dropform'>Drop queue</button>";
}

?>

<form id="dropform" action="../includes/dropqueue.inc.php" method="POST"></form>

<!-- USER IN QUEUE -->
<DIV id='queueInfo'></DIV>

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