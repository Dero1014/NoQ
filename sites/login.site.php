<!-- Login site -->

<?php
include '../header/header.php';

// Can't access if you are already loged in
if (isset($_SESSION["User"])) {
    header("Location: ../index.php?error=youAreLogedIn");
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

if (isset($_GET['signin'])) {
    switch ($_GET['signin']) {
        case 'userNotExist':
            echo "<p>Username or password was wrong</p>";
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
            echo "<p>You have loged in!</p>";
            break;
        default:
            # code...
            break;
    }
}
?>

</body>

</html>