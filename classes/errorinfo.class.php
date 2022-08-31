<?php
/**
 * @brief Handles any errors in a way that it would stop the
 * code or to display it to the user
 */
class ErrorInfo
{

    // Methods:
    //  Public:

    /**
     * @brief Points out if a statement didn't work
     * @param bool $result - result of a statement execution
     * @param mysqli_stmt $stmt - the statement that was used
     * @return bool true if it succeeds or it stops the code
     * and displays the error on why the statement failed
     */
    public function tryStmtError($result, $stmt)
    {
        if ($result == FALSE) {
            die('Stmt error caused by: ' + mysqli_stmt_error($stmt));
        } else {
            // ERROR HAS PASSED
            return TRUE;
        }
    }

    /**
     * @brief Catches an error for registration and returns 
     * it to the site for proper display
     * @param bool $result - result on checking if registration data
     * is valid
     * @param string $returnValue - use this value to send back to site
     * in case of a fail
     * @return bool true if it passes otherwise it returns back to the
     * signup page with the error value
     */
    public function onRegisterError($result, $returnValue)
    {
        if ($result == TRUE) {
            header("Location: ../sites/signup.site.php?signup=$returnValue");
            exit();
        } else {
            // ERROR HAS PASSED
            return TRUE;
        }
    }

    /**
     * @brief Catches an error for login and returns 
     * it to the site for proper display
     * @param bool $result - result on checking if login data is valid
     * @param string $returnValue - use this value to send back to site
     * in case of a fail
     * @param $mobile in case the login is comming from a mobile device
     * @return bool true if it passes otherwise it returns back to the
     * login page with the error value or false if it's being called 
     * from a mobile device
     */
    public function onLoginError($result, $returnValue, $mobile = false)
    {
        if ($result == TRUE) {
            if ($mobile)
                return FALSE;
            header("Location: ../sites/login.site.php?signin=$returnValue");
            exit();
        } else {
            // ERROR HAS PASSED
            return TRUE;
        }
    }

    /**
     * @brief Catches an error for adding a service to the company and
     * returns it to the site for proper display
     * @param bool $result- result on checking if data is valid
     * @param string $returnValue - use this value to send back to site
     * in case of a fail
     * @return bool true if it succeeds otherwise return it to the site
     * with the error value
     */
    public function onServiceError($result, $returnValue)
    {
        if ($result == TRUE) {
            header("Location:  ../sites/company.site.php?page=service&service=$returnValue");
            exit();
        } else {
            // ERROR HAS PASSED
            return TRUE;
        }
    }

    /**
     * @brief Catches an error for addding a worker to the company and 
     * returns it to the site for proper display
     * @param bool $result - result on checking if data is valid
     * @param string $returnValue - use this value to send back to site
     * in case of a fail
     * @return bool true if it succeeds otherwise return it to the site
     * with the error value
     */
    public function onWorkerError($result, $returnValue)
    {
        if ($result == TRUE) {
            header("Location:  ../sites/company.site.php?page=worker&worker=$returnValue");
            exit();
        } else {
            // ERROR HAS PASSED
            return TRUE;
        }
    }

    /**
     * @brief Catches an error for starting a queue and returns it to 
     * the site for proper display
     * @param bool $result - result on checking if data is valid
     * @param string $returnValue - use this value to send back to site
     * in case of a fail
     * @return bool true if it succeeds otherwise return it to the site
     * with the error value
     */
    public function onQueueError($result, $returnValue)
    {
        if ($result == TRUE) {
            header("Location:  ../sites/user.site.php?queue=$returnValue");
            exit();
        } else {
            // ERROR HAS PASSED
            return TRUE;
        }
    }

    /**
     * @brief Catches an error for login of a worker and returns it to
     * the site
     * @param bool $result - result on checking if data is valid
     * @param string $returnValue - use this value to send back to site
     * in case of a fail
     * @param $cn - the company name that is in the link
     * @param $p - the workers password that is in the link
     * @return bool true if it succeeds otherwise return it to the site
     * with the error value
     */
    public function onWorkerLoginError($result, $returnValue, $cn = "none", $p = "none")
    {
        if ($result == TRUE) {
            header("Location:  ../sites/worker.site.php?cn=$cn&p=$p&login=$returnValue");
            exit();
        } else {
            // ERROR HAS PASSED
            return TRUE;
        }
    }
}
