<?php
include '../classes/sql.class.php';

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

function sessionSet($row, $conn)
{
    if (session_start()) {
        $_SESSION["userid"] = $row['uId'];
        $_SESSION["username"] = $row['uName'];
        $_SESSION["companyTag"] = $row['uCompany'];

        if ($row['uCompany'] == 1) {
            //get company name
            $sql = "SELECT * FROM Companies WHERE userId = ?;";

            //this part can be functionised
            $stmt = startPrepStmt($conn, $sql);

            mysqli_stmt_bind_param($stmt, "i", $_SESSION["userid"]);
            mysqli_stmt_execute($stmt);

            $resultData = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($resultData);

            $_SESSION["companyname"] = $row['cName'];
            $_SESSION["companynamewithoutspaces"] = $row['xcName'];
        }

        return true;
    }
    return false;
}

// USER FUNCTIONS //

// register user
function addUser($conn, $uName, $uPass, $uEmail, $uCompany, $cName, $cDesc)
{
    $query = new SqlCommands($conn);
    $sql = "INSERT INTO Users (uName, uPassword, uEmail, uCompany) 
                VALUES (?, ?, ?, ?);";

    $hasedPwd = password_hash($uPass, PASSWORD_DEFAULT);
    $result = $query->setStmtValues("sssi", $sql, array($uName, $hasedPwd, $uEmail, $uCompany));

    echo "registration success";

    // If user has a company add it
    if ($uCompany === 1) {

        // Set no space name
        $xcName = str_replace(' ', '', $cName);
        
        // Get user id
        $userId = 0;

        $sql = "SELECT * FROM Users WHERE uName = '$uName';";

        $row = $query->getStmtRow($sql);
        $userId = $row['uId'];

        // Insert company into table
        $sql = "INSERT INTO Companies (cName, xcName, cDecs, userId) 
            VALUES (?, ?, ?, ?);";

        $query->setStmtValues("sssi", $sql, array($cName, $xcName, $cDesc, $userId));

        // Create company table
        $tableName = "COMPANY_" . $xcName;
        
        $result = $query->setStmtCompanyTable($tableName);

        if ($result) {
            echo "Table has been created";
        } else {
            die("error creating table");
        }
    }
}

// login user
function loginUser($conn, $uName, $uPass)
{
    
    $sql = "SELECT * FROM Users WHERE uName = ?;";

    $stmt = startPrepStmt($conn, $sql);

    mysqli_stmt_bind_param($stmt, "s", $uName);
    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    if ($resultData !== false) {
        $row = mysqli_fetch_assoc($resultData);
        $uName = $row['uName'];

        $pwdHashed = $row['uPassword'];
        $checkPwd = password_verify($uPass, $pwdHashed);

        if ($checkPwd === true) {
            if (sessionSet($row, $conn)) {
                if ($_SESSION["companyTag"] == 1) {
                    header("Location: ../sites/company.site.php?signin=success&page=service");
                } else {
                    header("Location: ../sites/user.site.php?signin=success");
                }
                echo "login success";
                exit();
            } else {
                echo "sesion didn't start";
            }
        } else {
            header("Location: ../sites/login.site.php?signin=wrongpass");
            exit();
        }
    } else {
        header("Location: ../sites/login.site.php?signin=fail");
        exit();
    }
}

// ERROR HANDLERS //
function companyExists($conn, $cName)
{
    //set no space name
    $xcName = str_replace(' ', '', $cName);
    $cDbName = "COMPANY_" . $xcName;

    $sql = "SELECT * FROM $cDbName;";

    if ($conn->query($sql)) {
        return true;
    } else {
        return false;
    }
}
