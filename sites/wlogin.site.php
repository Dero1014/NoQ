<!-- Login page if the worker hasn't loged in -->
<?php 
include '../includes/worker.inf.php';

session_start();

$_SESSION["cn"] = $_GET['cn'];
$_SESSION["p"] = $_GET['p'];

?>
<form action="../includes/worker.inc.php" method="POST">

<input type="text" name="wComp" placeholder="Please enter company name"> 
<input type="text" name="wPass" placeholder="Please enter company password"> 
<br>
<input type="submit" name="login" value="Login">
</form>
