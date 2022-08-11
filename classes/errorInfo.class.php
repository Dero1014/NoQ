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

    public function tryStmtError($result, $stmt)
    {
        if(!$result)
        {
            die("Stmt error caused by: " + mysqli_stmt_error($stmt));
        }
        else
        {
            // ERROR HAS PASSED
            return TRUE;
        }
    }
}
