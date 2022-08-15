<?php

class ErrorInfo
{
    private $errorMsg;
    private $errorStatus;

    public function setError($errorMessage, $errorStatus)
    {
        $this->errorMsg = $errorMessage;
        $this->errorStatus = $errorStatus;
    }

    // SPECIFICLY POINTS OUT STATEMENT SQL COMMANDS ERRORS
    /**
     * @brief Points out if a statement didn't work
     * @param bool $result
     * @param mysqli_stmt $stmt
     * @return bool true
     */
    public function tryStmtError($result, $stmt)
    {
        if($result == FALSE)
        {
            die('Stmt error caused by: ' + mysqli_stmt_error($stmt));
        }
        else
        {
            // ERROR HAS PASSED
            return TRUE;
        }
    }

    // SPECIFICLY POINTS OUT IF THERE IS A RETURN VALUE OR NOT
    /**
     * @brief Points out if a return value is null
     * @param bool $result
     * @param mysqli_stmt $stmt
     * @return bool true
     */
    public function tryStmtReturnValue($result, $stmt)
    {
        if($result == NULL)
        {
            die("Returned value was null: " + mysqli_stmt_error($stmt));
        }
        else
        {
            // ERROR HAS PASSED
            return TRUE;
        }
    }

    public function onRegisterError($result, $returnValue)
    {
        if($result == TRUE)
        {
            header("Location: ../sites/signup.site.php?signup=$returnValue");
            exit();
        }
        else
        {
            // ERROR HAS PASSED
            return TRUE;
        }
    }

    public function onLoginError($result, $returnValue)
    {
        if($result == TRUE)
        {
            header("Location: ../sites/login.site.php?signin=$returnValue");
            exit();
        }
        else
        {
            // ERROR HAS PASSED
            return TRUE;
        }
    }

    public function onServiceError($result, $returnValue)
    {
        if($result == TRUE)
        {
            header("Location:  ../sites/company.site.php?page=service&service=$returnValue");
            exit();
        }
        else
        {
            // ERROR HAS PASSED
            return TRUE;
        }
    }
}
