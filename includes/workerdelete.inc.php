<?php
include 'user.inf.php';

$wId = $_POST['delete'];

// Remove worker from company
$company->removeWorker($wId);

$page = "page=worker";

header("Location: ../sites/company.site.php?$page");
