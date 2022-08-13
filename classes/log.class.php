<?php
//include_once 'user.class.php';

class Log extends SQL
{
    public function __construct()
    {
        parent::__construct();
    }
    
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
                    if ($row['uCompany'] == 1) {
                        return "company.site.php?signin=success&page=service";
                    } else {
                        return "user.site.php?signin=success";
                    }
                    echo "Login success \n";
                    exit();
                } else {
                    echo "Session didn't start \n";
                }
            } else {
                return "login.site.php?signin=wrongpass";
                exit();
            }
        } else {
            return "login.site.php?signin=fail";
            exit();
        }
    }

    private function sessionSet($row)
    {
        $user = new User($row['uId'], $row['uName'], $row['uEmail'], $row['uCompany']);
        if (session_start()) {
            $_SESSION["User"] = $user;
            return true;
        }
        return false;
    }

}
