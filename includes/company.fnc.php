<?php

// MISC to shorten code length
/*
function startPrepStmt($conn, $sql)
{
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        die("$sql");
        header("Location: ../index.php?error=stmtfail");
        exit();
    }

    return $stmt;
}
*/

// COMPANY FUNCTIONS //

// Add a service
/*
function addService($sName, $conn, $cDbName)
{
    $query = new SQL();
    $sql = "INSERT INTO $cDbName (sName) VALUES (?);";
    
    $query->setStmtValues("s", $sql, array($sName));
}
*/

// Add a worker account
function addWorker($conn, $rngPass, $wName, $cName)
{
    $sql = "INSERT INTO Workers (wPass, wComp, wName) 
    VALUES (?, ?, ?);";

    $stmt = startPrepStmt($conn, $sql);

    mysqli_stmt_bind_param($stmt, "sss", $rngPass, $cName, $wName);
    mysqli_stmt_execute($stmt);
}

// ERROR HANDLERS //

// check if the service already exists
/*
function serviceExists($conn, $service, $cName)
{
    $xcName = str_replace(' ', '', $cName);
    $cDbName = "COMPANY_".$xcName;
    $sql = "SELECT * FROM $cDbName WHERE sName = ?;";
    $stmt = startPrepStmt($conn, $sql);

    mysqli_stmt_bind_param($stmt, "s", $service);
    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($resultData);
    if ($row['sName'] == $service) {
        return true;
    } else {
        return false;
    }

    mysqli_stmt_close($stmt);
}
*/
