<?php

// MISCS //
function checkSet($set)
{
    if (isset($set)) {
        $set = 1;
    } else {
        $set = 0;
    }
    return $set;
}

function randomString()
{
    $ranString = "";
    $string = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    
    for ($i = 0; $i < 10; $i++) {
        $index = rand(0, strlen($string) - 1);
        $ranString .= $string[$index];
    }

    return $ranString;
}

// ERROR HANDLERS //

// check if any input is empty
function areEmpty($words)
{
    for ($i = 0; $i < count($words); $i++) {
        if (empty($words[$i])) {
            print "Yes it is empty ".$i.PHP_EOL ;
            return true;
        }
    }
    print "No it isn't empty" . PHP_EOL;
}

// check if the input is invalid
function invalidInput($words)
{
    for ($i = 0; $i < count($words); $i++) {
        if (preg_match('/[\^£$%&*()}{#~?><>|=_+¬-]/', $words[$i])) {
            echo "it's invalid" . PHP_EOL;
            return true;
        }
    }
    print "it's not invalid" . PHP_EOL;
}

// check if A STRING already exists
function alreadyExists($conn, $string, $dbData, $db)
{
    $sql = "SELECT * FROM $db WHERE $dbData = ?;";
    $stmt = startPrepStmt($conn, $sql);

    mysqli_stmt_bind_param($stmt, "s", $string);
    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($resultData);
    if ($row[$dbData] == $string) {
        print "It exists" . PHP_EOL;
        return true;
    } else {
        print "It doesn't exist" . PHP_EOL;
        return false;
    }

    mysqli_stmt_close($stmt);
}

function alreadyExistsInt($conn, $int, $dbData, $db)
{
    $sql = "SELECT * FROM $db WHERE $dbData = ?;";
    $stmt = startPrepStmt($conn, $sql);

    mysqli_stmt_bind_param($stmt, "i", $int);
    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($resultData);
    if ($row[$dbData] == $int) {
        print "It exists" . PHP_EOL;
        return true;
    } else {
        print "It doesn't exist" . PHP_EOL;
        return false;
    }

    mysqli_stmt_close($stmt);
}