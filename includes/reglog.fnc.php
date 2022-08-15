<?php
// OLD 

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

function sessionSet($row)
{
    $query = new SQL();
    $user = new User($row['uId'], $row['uName'], $row['uEmail'], $row['uCompany']);
    if (session_start()) {
        
        $_SESSION['User'] = $user;
        $_SESSION["userid"] = $row['uId'];
        $_SESSION["username"] = $row['uName'];
        $_SESSION["companyTag"] = $row['uCompany'];

        if ($row['uCompany'] == 1) {
            // Get company name and set it in session
            $sql = "SELECT * FROM Companies WHERE userId = " . $row['uId'] . ";";

            $row = $query->getStmtRow($sql);

            $_SESSION["companyname"] = $row['cName'];
            $_SESSION["companynamewithoutspaces"] = $row['xcName'];
        }

        return true;
    }
    return false;
}

// USER FUNCTIONS //

// login user
function loginUser($uName, $uPass)
{
    $query = new SQL();
    $sql = "SELECT * FROM Users WHERE uName = '$uName';";

    $row = $query->getStmtRow($sql);

    if ($row !== false) {
        $uName = $row['uName'];

        // Varify password
        $pwdHashed = $row['uPassword'];
        $checkPwd = password_verify($uPass, $pwdHashed);

        if ($checkPwd === true) {
            if (sessionSet($row)) {
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
