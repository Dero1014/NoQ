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
            echo "Yes it is empty ".$i."\n" ;
            return true;
        }
    }
    echo "No it isn't empty\n";
}

// check if the input is invalid
function invalidInput($words)
{
    for ($i = 0; $i < count($words); $i++) {
        if (preg_match('/[\^£$%&*()}{#~?><>|=_+¬-]/', $words[$i])) {
            echo "it's invalid\n";
            return true;
        }
    }
    echo "it's not invalid\n";
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
        echo "It exists\n";
        return true;
    } else {
        echo "It doesn't exist\n";
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
        echo "It exists\n";
        return true;
    } else {
        echo "It doesn't exist\n";
        return false;
    }

    mysqli_stmt_close($stmt);
}