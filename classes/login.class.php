<?php

// Class for loging in users to the site
class Login extends SQL
{
    public function __construct()
    {
        parent::__construct("Log");
    }

    // Logs the user
    /**
     * @brief Logs the user into the appropriate site, company or user site
     * @param string $uName
     * @param string $uPass
     * 
     * @return string
     */
    public function loginUser($uName, $uPass, $boolReturn = false)
    {
        // Find user
        $sql = "SELECT * FROM Users WHERE uName = '$uName';";

        $row = $this->getStmtRow($sql);

        if ($row !== false) {
            $uName = $row['uName'];

            // Verify password
            $pwdHashed = $row['uPassword'];
            $checkPwd = password_verify($uPass, $pwdHashed);

            if ($checkPwd === true) {
                // Log to appropriate site
                if ($this->sessionSet($row)) {
                    if ($row['uCompany'] == 1) {
                        return ($boolReturn) ? true : "company.site.php?signin=success&page=service";
                    } else {
                        return ($boolReturn) ? true : "user.site.php?signin=success";
                    }
                    echo "Login success \n";
                    exit();
                } else {
                    echo "Session didn't start \n";
                }
            } else {
                return ($boolReturn) ? false :"login.site.php?signin=wrongpass";
                exit();
            }
        } else {
            return ($boolReturn) ? false : "login.site.php?signin=fail";
            exit();
        }
    }

    // Creates a session
    /**
     * @brief Creates a session where it stores all the important user information
     * @param array $row
     * @return void
     */
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
