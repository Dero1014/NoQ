<?php
// MISC //
function startPrepStmt($conn, $sql)
{
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        die(" $sql ");
        exit();
    }

    return $stmt;
}

function sessionSet($row)
{
    if (session_start()) {
        $_SESSION["workerid"] = $row['wId'];
        $_SESSION["workerName"] = $row['wName'];
        $_SESSION["workercompany"] = $row['wComp']; // company name
        // company name without spaces
        $_SESSION["workercompanywithoutspaces"] = str_replace(' ', '', $row['wComp']);
        return true;
    }
    return false;
}

function checkPwd($resultData, $wPass)
{
    // since there can be multiple workers in one company 
    // we have to check if the password matches any worker
    // account
    while ($row = mysqli_fetch_assoc($resultData)) {
        $pwdHashed = $row['wPass'];
        $checkPwd = password_verify($wPass, $pwdHashed);
        echo "<br>";
        echo "Pass hash was: " . $pwdHashed . " and entered pass was " . $wPass;

        if ($checkPwd == true) {
            // create a worker info session
            if (sessionSet($row)) {
                return true;
            }
        }
    }

    return false;
}


// LOGIN FUNCTION //
function access($conn, $wPass, $wComp)
{
    session_start();
    $p = $_SESSION["p"];
    $sql = "SELECT * FROM Workers WHERE wComp = ? AND wPass = ?;";

    $stmt = startPrepStmt($conn, $sql);

    mysqli_stmt_bind_param($stmt, "ss", $wComp, $p);
    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    if ($resultData !== false) {

        $checkPwd = checkPwd($resultData, $wPass);

        if ($checkPwd === true) {
            // make sure a session has been started
            header("Location: ../sites/worker.site.php?access=granted");
        } else {
            header("Location: ../sites/worker.site.php?access=denied");
            exit();
        }
    } else {
        header("Location: ../login.site.php?signin=fail");
        exit();
    }
}

//  ACCESSED FUNCTIONS //

function nextInQueue($conn, $sName, $wComp)
{
    // name without spaces
    $xsName = str_replace(' ', '', $sName);
    $xcName = str_replace(' ', '', $wComp);

    // QUEUE name db
    $qDbName = "QUEUE_" . $xcName . "_" . "$xsName";

    $sql = "UPDATE $qDbName SET queue = queue - 1";
    $conn->query($sql);

    // remove the user from the process
    if(userInProcess($conn))
        removeUserFromProcess($conn);
    
    // check if there is a queue below 0
    if (removeFromQueue($conn, $qDbName)) {
        
        //update company service values
        updateCompanyValues($conn, $xsName, $xcName);

        // check if database should exist
        databaseDestroy($conn, $qDbName);
    }
}

// checks if there is a user assigned to worker 
function userInProcess($conn)
{
    include '../includes/worker.inf.php';

    $sql = "SELECT userId FROM Workers WHERE wId = $wId";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result);
    
    return is_null($row['userId']) ? false : true;
}

function removeUserFromProcess($conn)
{
    include '../includes/worker.inf.php';

    // get user id from worker
    $sql = "SELECT userId FROM Workers WHERE wId = $wId";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result);
    $uId = $row['userId'];

    // remove from User from Queues
    $sql = "DELETE FROM Queues WHERE userId = $uId";
    $conn->query($sql);

    // remove User from Worker
    $sql = "UPDATE Workers SET userId = NULL WHERE wId = $wId";
    $conn->query($sql);
}

function removeFromQueue($conn, $qDbName)
{
    include '../includes/worker.inf.php';

    // get userId who has queue less then 0
    $sql = "SELECT userId FROM $qDbName WHERE queue < 1";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result);
    if (isset($row['userId'])) {

        $uId = $row['userId'];

        // remove from qs db
        $sql = "DELETE FROM $qDbName WHERE queue < 1";
        $conn->query($sql);

        // remove from Queue db
        $sql = "UPDATE Queues SET inLine = 1 WHERE userId = $uId";
        $conn->query($sql);

        // Add user to the current worker
        $sql = "UPDATE Workers SET userId = $uId WHERE wId = $wId";
        $conn->query($sql);

        return true;
    }

    //else
    return false;
}

function updateCompanyValues($conn, $xsName, $xcName)
{
    // get userId who has queue less then 0
    $cDbName = "COMPANY_" . $xcName;

    $sql = "UPDATE $cDbName SET numberOfUsers = numberOfUsers + 1 
                    WHERE sName = '$xsName'";

    $conn->query($sql);
}

function databaseDestroy($conn, $qDbName)
{
    $sql = "SELECT * FROM $qDbName";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result);
    if (!isset($row["userId"])) {
        $sql = "DROP TABLE $qDbName";
        $conn->query($sql);
        return true;
    }
    return false;
}

function setCurrentAvgTime($conn, $sName, $wComp, $time)
{
    // name without spaces
    $xcName = str_replace(' ', '', $wComp);

    // QUEUE name db
    $cDbName = "COMPANY_" . $xcName ;

    $sql = "UPDATE $cDbName SET timeSum = timeSum + $time 
            WHERE sName = '$sName'";
    $conn->query($sql);

    $sql = "UPDATE $cDbName SET avgTime = timeSum / numberOfUsers 
            WHERE sName = '$sName'";
    $conn->query($sql);
}