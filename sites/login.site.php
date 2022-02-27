<!-- Login site -->

<?php
include '../header/header.php';

// can't access if you are already loged in
if (isset($_SESSION["username"])) {
    header("Location: ../index.php?error=youarelogedin");
}
?>


<!-- The login forum  where you enter your login info -->
<div class="loginContainer">
    <p>Login form</p> <br>
    <form action="../includes/login.inc.php" method="post">
        <input type="text" name="logUserName" placeholder="username" class="inputClass"><br>
        <input type="password" name="logUserPass" placeholder="password" class="inputClass"><br>
        <input type="submit" name="submitLog" value="Login" class="loginButton">
    </form>
</div>

<!-- ERROR MESSAGES -->
<?php
// Shows error messages depending on the error submited
if (isset($_GET['signin'])) {
    $signin = $_GET['signin'];
    if ($signin == "empty") {
        echo "<p>Fill in the input fields!</p>";
    } elseif ($signin == "invalidinput") {
        echo "<p>Invalid character found!</p>";
    } elseif ($signin == "wrongpass") {
        echo "<p>Wrong password!</p>";
    } elseif ($signin == "fail") {
        echo "<p>Wrong user!</p>";
    }
}
?>

</body>

</html>