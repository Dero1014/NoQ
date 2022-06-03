<?php

// MISC //
function startPrepStmt($conn, $sql)
{
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        die("$sql");
        header("Location: index.php?error=stmtfail");
        exit();
    }

    return $stmt;
}

// QUEUE UP //
function queueUp($conn, $cName, $sName, $uId)
{
    include 'common.fnc.php';

    // spaceless names
    $xcName = str_replace(' ', '', $cName);
    $xsName = str_replace(' ', '', $sName);

    $cDbName = "COMPANY_" . $xcName;
    //check if Service exists
    if (!alreadyExists($conn, $sName, "sName", $cDbName)) {
        echo "got into error service doesn't exists";
        die();
        return;
    }

    //check if user exists
    if (!alreadyExistsInt($conn, $uId, "uId", "Users")) {
        echo "got into error user doesn't exists";
        die();
        return;
    }

    //check if user is in queue
    if (checkQueue($conn, $uId)) {
        echo "got into error user already in queue";
        die();
        return;
    }

    // name the queue database name
    $qDbName = "QUEUE_" . $xcName . "_" . $xsName;

    // check if the queue exists if not create it
    queueExists($conn, $qDbName);

    // set new queue number
    $currentQueue = getQueue($conn, $qDbName);

    // insert the user in
    $sql = "INSERT INTO $qDbName (queue, userId) 
    VALUES (?, ?);";

    $stmt = startPrepStmt($conn, $sql);

    mysqli_stmt_bind_param($stmt, "ii", $currentQueue, $uId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // add user to the queue
    $sql = "INSERT INTO Queues (userId, queueName, cName, sName) 
    VALUES (?, ?, ?, ?);";

    $stmt = startPrepStmt($conn, $sql);

    mysqli_stmt_bind_param($stmt, "isss", $uId, $qDbName, $cName, $sName);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // set session for queue number
    session_start();
    $_SESSION["queue"] = $currentQueue;
}

// check if the queue exists if not create it
function queueExists($conn, $qDbName)
{
    $sql = "SELECT * FROM $qDbName;";

    if ($conn->query($sql)) {
        return true;
    } else {
        // queue is for the queue number that is gonna be updated
        // userId is for the user that is part of that queue
        $sql = "CREATE TABLE $qDbName(
            qId int not null auto_increment,
            queue int not null,
            userId int,
            foreign key (userId) references Users(uId),
            primary key (qId)
            );";

        if ($conn->query($sql)) {
            echo "Table has been created";
        } else {
            die("error creating table: " . $conn->error);
        }
        return false;
    }
}

// get the last queue
function getQueue($conn, $qDbName)
{
    $sql = "SELECT * FROM $qDbName ORDER BY qId DESC LIMIT 1;";

    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result);

    if (!isset($row['queue'])) {
        return 1;
    }

    $queue = $row['queue'] + 1;

    return $queue;
}

// check if the user is already in queue
function checkQueue($conn, $uId)
{
    // fetch data from queues
    $sql = "SELECT * FROM Queues WHERE userId = $uId;";
    $result = mysqli_query($conn, $sql);
    $rowFromQueues = mysqli_fetch_array($result);
    session_start();
    if (isset($rowFromQueues['userId']) ) {
        // fetch users queue
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);
        $qDbName = $row['queueName'];
        $sql = "SELECT * FROM $qDbName WHERE userId = $uId;";

        // set users queue  
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);
        $_SESSION["queue"] = $row['queue'];

        if($rowFromQueues['inLine'] != 0){ // check if $rowFromQueues is 1
            $_SESSION["inLine"] = $rowFromQueues['inLine'];
        }

        if ($_SESSION["gotInQueue"] == 0) {
            $_SESSION["gotInQueue"] = 1;
        }
        
        return true;
    }else{
        session_start();
        unset($_SESSION["queue"]);
        unset($_SESSION["inLine"]);
        session_unset($_SESSION["queue"]);
        session_unset($_SESSION["inLine"]);
        return false;
    }

    
}


function getAvgTime($conn, $uId)
{
    $sql = "SELECT * FROM Queues WHERE userId = $uId";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result);

    $sName = $row['sName'];

    $cDbName = "COMPANY_" . $row['cName'];
    $sql = "SELECT * FROM $cDbName WHERE sName = '$sName'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result);

    return (int)$row['avgTime'];
   
}
