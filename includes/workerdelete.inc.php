<?php

include 'connect.inc.php';
include 'user.inf.php';
$wId = $_POST['delete'];

// remove worker from company
$sql = "DELETE FROM Workers WHERE wId = $wId";
$result = mysqli_query($conn, $sql);

$page = "page=worker";

header("Location: ../sites/company.site.php?$page");
