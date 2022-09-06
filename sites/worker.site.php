<!-- WORKER PAGE -->

<?php
include '../header/header.php';

// Added a safety feature to not allow to go into the page 
// without loging in first
include '../includes/worker.chk.php';

// if not loged in show login page otherwise give access
if ($worker == NULL)
    include 'wlogin.site.php';
else
    include 'waccess.site.php';

?>

</body>

</html>