<!-- COMPANY USER PAGE -->

<?php
include '../header/header.php';
// inaccessible if the user isn't from a company or loged in
include '../includes/company.chk.php';
?>

<!-- COMPANY WELCOME TITLE -->
<?php
$info ="Info";
echo "<h1>Welcome to your company $cName user $uName <h1>";

// SELECT WHICH PAGE IS BEING USED
if (isset($_GET['page'])) {
    $page = $_GET['page'];
    if ($page === "service") {
        include 'cservice.site.php';
    }else if ($page === "worker") {
        include 'cworkers.site.php';
    }
}
?>

</body>
</html>