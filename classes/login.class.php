<?php

/**
 * @brief Contains functions for logging in a user
 */
class Login extends SQL
{
    /**
     * @brief Calls SQL to get a connection
     */
    public function __construct()
    {
        parent::__construct("Log");
    }

    // Methods:
    //  Public:

    /**
     * @brief Logs the user into the appropriate site (company or user site)
     * @param string $uName - username
     * @param string $uPass - password
     * @param bool $mobile - if being used for mobile devices
     * 
     * @return string or bool depending if it is for mobile use or not
     */
    public function loginUser($uName, $uPass, $mobile = false)
    {
        // Find user
        $sql = "SELECT * FROM Users WHERE uName = '$uName';";

        $row = $this->getStmtRow($sql);

        $uName = $row['uName'];

        // Verify password
        $pwdHashed = $row['uPassword'];
        $checkPwd = password_verify($uPass, $pwdHashed);

        if ($checkPwd === true) {

            // Start session and log to the appropriate site (user or company)
            if ($this->sessionSet($row)) {
                if ($row['uCompany'] == 1) {
                    return ($mobile) ? true : "company.site.php?signin=success&page=service";
                } else {
                    return ($mobile) ? true : "user.site.php?signin=success";
                }
                echo "Login success \n";
                exit();
            } else {
                echo "Session didn't start \n";
            }
        } else {
            return ($mobile) ? false :"login.site.php?signin=wrongPass";
            exit();
        }
    }

    //  Private:
    /**
     * @brief Creates a session where it stores all the important user information
     * @param array $row - array of data of a sql row
     * @return bool success if a session has been started successfully 
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
