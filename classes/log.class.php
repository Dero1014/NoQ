<?php
//include_once 'user.class.php';

class Log extends SQL
{
    public function loginUser($uName, $uPass)
    {
        $query = new SQL();
        $sql = "SELECT * FROM Users WHERE uName = '$uName';";

        $row = $query->getStmtRow($sql);

        if ($row !== false) {
            $uName = $row['uName'];

            // Verify password
            $pwdHashed = $row['uPassword'];
            $checkPwd = password_verify($uPass, $pwdHashed);

            if ($checkPwd === true) {
                if ($this->sessionSet($row)) {
                    if ($_SESSION["companyTag"] == 1) {
                        $this->headerLocation("company.site.php?signin=success&page=service");
                    } else {
                        $this->headerLocation("user.site.php?signin=success");
                    }
                    echo "Login success \n";
                    exit();
                } else {
                    echo "Session didn't start \n";
                }
            } else {
                $this->headerLocation("login.site.php?signin=wrongpass");
                exit();
            }
        } else {
            $this->headerLocation("login.site.php?signin=fail");
            exit();
        }
    }

    private function sessionSet($row)
    {
        if (session_start()) {
            $_SESSION["user"] = new User($row['uId'], $row['uName'], $row['uEmail'], $row['uCompany']);
            // $_SESSION["username"] = $row['uName'];
            // $_SESSION["companyTag"] = $row['uCompany'];

            return true;
        }
        return false;
    }

    private function headerLocation($value)
    {
        header("Location: ../sites/$value");
    }
}
